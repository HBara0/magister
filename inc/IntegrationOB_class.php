<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Class to Integrate OCOS with Openbravo
 * $id: IntegrationOB_class.php
 * Created:        @zaher.reda    Feb 18, 2013 | 12:45:36 PM
 * Last Update:    @zaher.reda    Feb 18, 2013 | 12:45:36 PM
 */

class IntegrationOB extends Integration {
    private $client;
    private $status = 0;
    private $organisations = array();

    public function __construct(array $database_info, $client_id, $affiliates_ids = null, $foreign_system = 3, array $sync_period = array()) {
        if(parent::__construct($foreign_system, $database_info)) {
            parent::set_sync_interval($sync_period);
            if(!is_null($affiliates_ids)) {
                parent::match_affiliates_ids($affiliates_ids);
            }
            $this->set_client_id($client_id);
        }
        else {
            $this->status = 701;
            return false;
        }
    }

    public function set_organisations($organisations) {
        $this->organisations = $organisations;
    }

    public function sync_products($exclude = array()) {
        global $db, $log;

        $query = $this->f_db->query("SELECT *
					FROM m_product
					WHERE ad_client_id='".$this->client."'
					AND (updated BETWEEN '".date('Y-m-d 00:00:00', strtotime($this->period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($this->period['to']))."')");

        $newdata = array();
        $items_count = 0;
        while($product = $this->f_db->fetch_assoc($query)) {
            $newdata = array(
                    'foreignSystem' => $this->foreign_system,
                    'foreignId' => $product['m_product_id'],
                    'foreignName' => $product['name'],
                    'foreignNameAbbr' => $product['value'],
                    'affid' => $this->affiliates_index[$product['ad_org_id']]
            );

            $newdata['localId'] = $db->fetch_field($db->query("SELECT pid FROM ".Tprefix."products WHERE name='".$db->escape_string($product['name'])."'"), 'pid');

            if(value_exists('integration_mediation_products', 'foreignId', $product['m_product_id'], 'foreignSystem='.$this->foreign_system)) {
                if(empty($newdata['localId'])) {
                    unset($newdata['localId']);
                }
                $db->update_query('integration_mediation_products', $newdata, 'foreignId="'.$product['m_product_id'].'" AND foreignSystem='.$this->foreign_system);
            }
            else {
                $db->insert_query('integration_mediation_products', $newdata);
            }

            $items_count++;
        }
        $log->record('Integration: Synced '.$items_count.' product(s).');
        return true;
    }

    public function sync_businesspartners() {
        global $db, $log, $cache;

        $query = $this->f_db->query("SELECT *
					FROM c_bpartner
					WHERE ad_client_id='".$this->client."' AND (iscustomer='Y' OR isvendor='Y')
					AND (updated BETWEEN '".date('Y-m-d 00:00:00', strtotime($this->period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($this->period['to']))."')");

        $newdata = array();
        $items_count = 0;
        while($bpartner = $this->f_db->fetch_assoc($query)) {
            $newdata = array(
                    'foreignSystem' => $this->foreign_system,
                    'foreignId' => $bpartner['c_bpartner_id'],
                    'foreignName' => $bpartner['name'],
                    'foreignNameAbbr' => $bpartner['value'],
                    'affid' => $this->affiliates_index[$bpartner['ad_org_id']]
            );

            /**
             * Get Address of the BP
             */
            $address = IntegrationOBBusinessPartnerLocation::get_data('c_bpartner_id=\''.$bpartner['c_bpartner_id'].'\'', array('returnarray' => true));
            if(is_array($address)) {
                $address = current($address);
                if(is_object($address)) {
                    $location = $address->get_location()->get_country()->countrycode;

                    if(!$cache->iscached('countrycode', $location)) {
                        $country = Countries::get_data(array('acronym' => $location));
                        $cache->add('countrycode', $country, $location);
                    }
                    else {
                        $country = $cache->get_cachedval('countrycode', $location);
                    }


                    if(is_object($country)) {
                        $newdata['country'] = $country->get_id();
                    }
                }
            }
            $newdata['localId'] = $db->fetch_field($db->query("SELECT eid FROM ".Tprefix."entities WHERE companyName='".$db->escape_string($bpartner['name'])."'"), 'eid');

            if($bpartner['isvendor'] == 'Y') {
                $newdata['entityType'] = 's';
            }
            elseif($bpartner['iscustomer'] == 'Y') {
                $newdata['entityType'] = 'c';
            }

            if(value_exists('integration_mediation_entities', 'foreignId', $bpartner['c_bpartner_id'])) {
                $db->update_query('integration_mediation_entities', $newdata, 'foreignId="'.$bpartner['c_bpartner_id'].'"');
            }
            else {
                $db->insert_query('integration_mediation_entities', $newdata);
            }
            $items_count++;
        }
        $log->record('Integration: Synced '.$items_count.' business partner(s).');
        return true;
    }

    public function sync_sales(array $organisations, array $exclude = array(), $doc_type = 'invoice') {
        global $db, $log;

        if(!is_array($organisations) || empty($organisations)) {
            return false;
        }

        if($doc_type == 'order') {
            $query = $this->f_db->query("SELECT o.ad_org_id, o.c_order_id AS doc_id, o.dateordered AS doc_date, o.documentNo, bp.name AS bpname, bp.c_bpartner_id AS bpid, bp.value AS bpname_abv, c.iso_code AS currency, o.salesrep_id, u.username, u.name AS salesrep, pt.netdays AS paymenttermsdays
						FROM c_order o
                                                JOIN c_bpartner bp ON (bp.c_bpartner_id=o.c_bpartner_id)
						JOIN c_currency c ON (c.c_currency_id=o.c_currency_id)
						LEFT JOIN ad_user u ON (u.ad_user_id=o.salesrep_id)
						JOIN c_paymentterm pt ON (o.c_paymentterm_id=pt.c_paymentterm_id)
						WHERE o.ad_org_id IN ('".implode('\',\'', $organisations)."') AND docstatus NOT IN ('VO', 'CL') AND issotrx='Y' AND ((dateordered BETWEEN '".date('Y-m-d 00:00:00', strtotime($this->period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($this->period['to']))."') OR (o.updated BETWEEN '".date('Y-m-d 00:00:00', strtotime($this->period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($this->period['to']))."'))
						ORDER by dateordered ASC");
        }
        elseif($doc_type == 'invoice') {
            $query = $this->f_db->query("SELECT i.ad_org_id, i.c_invoice_id AS doc_id, i.dateinvoiced AS doc_date, i.documentno, bp.name AS bpname, bp.c_bpartner_id AS bpid, bp.value AS bpname_abv, c.iso_code AS currency, i.salesrep_id, u.username, u.name AS salesrep, pt.netdays AS paymenttermsdays
					FROM c_invoice i
					JOIN c_bpartner bp ON (bp.c_bpartner_id=i.c_bpartner_id)
					JOIN c_currency c ON (c.c_currency_id=i.c_currency_id)
					LEFT JOIN ad_user u ON (u.ad_user_id=i.salesrep_id)
					JOIN c_paymentterm pt ON (i.c_paymentterm_id=pt.c_paymentterm_id)
					WHERE i.ad_org_id IN ('".implode('\',\'', $organisations)."') AND docstatus NOT IN ('VO', 'CL') AND issotrx='Y' AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime($this->period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($this->period['to']))."')
					ORDER by dateinvoiced ASC");
        }

        $document_newdata = array();
        while($document = $this->f_db->fetch_assoc($query)) {
            $document_newdata = array(
                    'foreignSystem' => $this->foreign_system,
                    'foreignId' => $document['doc_id'],
                    'docNum' => $document['documentno'],
                    'date' => strtotime($document['doc_date']),
                    'cid' => $document['bpid'],
                    'affid' => $this->affiliates_index[$document['ad_org_id']],
                    'currency' => $document['currency'],
                    'paymentTerms' => $document['paymenttermsdays'],
                    'salesRep' => $document['salesrep']
            );

            $document_newdata['salesRepLocalId'] = $db->fetch_field($db->query("SELECT uid FROM ".Tprefix."users WHERE displayName='".$db->escape_string($document['salesrep'])."' OR username='".$db->escape_string($document['username'])."'"), 'uid');

            if(value_exists('integration_mediation_salesorders', 'foreignId', $document['doc_id'])) {
                $query2 = $db->update_query('integration_mediation_salesorders', $document_newdata, 'foreignId="'.$document['doc_id'].'"');
            }
            else {
                $query2 = $db->insert_query('integration_mediation_salesorders', $document_newdata);
            }

            if($query2) {
                if(value_exists('integration_mediation_salesorderlines', 'foreignOrderId', $document['doc_id'])) {
                    $db->delete_query('integration_mediation_salesorderlines', 'foreignOrderId="'.$document['doc_id'].'"');
                }

                if($doc_type == 'order') {
                    $documentline_query = $this->f_db->query("SELECT ol.*, c_orderline_id AS documentlineid, ol.qtyordered AS quantity, p.name AS productname, u.x12de355 AS uom
							FROM c_orderline ol
							JOIN m_product p ON (p.m_product_id=ol.m_product_id)
							JOIN c_uom u ON (u.c_uom_id=ol.c_uom_id)
							WHERE c_order_id='{$document[doc_id]}'");
                }
                else {
                    $documentline_query = $this->f_db->query('SELECT m_transaction_id, il.*, il.c_invoiceline_id AS docline_id, il.qtyinvoiced AS quantity, ppo.c_bpartner_id, u.x12de355 AS uom, tr.transactioncost AS cost, c.iso_code AS costcurrency, m_costing_algorithm_id
													FROM c_invoiceline il
                                                                                                        JOIN m_product p ON (p.m_product_id=il.m_product_id)
													JOIN c_uom u ON (u.c_uom_id=il.c_uom_id)
													LEFT JOIN m_inoutline iol ON (iol.m_inoutline_id=il.m_inoutline_id)
													LEFT JOIN m_transaction tr ON (iol.m_inoutline_id=tr.m_inoutline_id)
													LEFT JOIN c_currency c ON (c.c_currency_id=tr.c_currency_id)
													LEFT JOIN m_product_po ppo ON (p.m_product_id=ppo.m_product_id)
													WHERE c_invoice_id=\''.$document['doc_id'].'\' AND il.m_product_id NOT IN (\''.implode('\',\'', $exclude['products']).'\')
												'); // AND iscostcalculated=\'Y\'
                }

                $documentline_newdata = array();
                while($documentline = $this->f_db->fetch_assoc($documentline_query)) {
                    if(isset($documentline['m_transaction_id']) && !empty($documentline['m_transaction_id'])) {
                        $purchaseprice_data = $this->get_purchaseprice($documentline['m_transaction_id'], $documentline['m_costing_algorithm_id'], 'sale');
                    }
                    $documentline_newdata = array(
                            'foreignId' => $documentline['docline_id'],
                            'foreignOrderId' => $document['doc_id'],
                            'pid' => $documentline['m_product_id'],
                            'affid' => $this->affiliates_index[$document['ad_org_id']],
                            'price' => $documentline['priceactual'],
                            'quantity' => $documentline['quantity'],
                            'quantityUnit' => $documentline['uom'],
                            'cost' => $documentline['cost'],
                            'costCurrency' => $documentline['costcurrency'],
                            'purchasePrice' => $purchaseprice_data['price'],
                            'purPriceCurrency' => $purchaseprice_data['currency']
                    );

                    $db->insert_query('integration_mediation_salesorderlines', $documentline_newdata);
                }
            }
        }
        $log->record('Integration: Synced Sales');

        $this->remove_voided_sales($organisations, $doc_type);
    }

    private function remove_voided_sales(array $organisations, $doc_type = 'invoice') {
        global $db, $log;

        if($doc_type == 'order') {
            return false;
        }
        elseif($doc_type == 'invoice') {
            $query = $this->f_db->query("SELECT i.c_invoice_id AS doc_id
					FROM c_invoice i
					WHERE i.ad_org_id IN ('".implode('\',\'', $organisations)."') AND docstatus IN ('VO', 'CL') AND issotrx='Y' AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime($this->period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($this->period['to']))."')
					ORDER by dateinvoiced ASC");
        }

        while($document = $this->f_db->fetch_assoc($query)) {
            if(value_exists('integration_mediation_salesorders', 'foreignId', $document['doc_id'])) {
                $db->delete_query('integration_mediation_salesorders', 'foreignId="'.$document['doc_id'].'"');
                if(value_exists('integration_mediation_salesorderlines', 'foreignOrderId', $document['doc_id'])) {
                    $db->delete_query('integration_mediation_salesorderlines', 'foreignOrderId="'.$document['doc_id'].'"');
                }
            }
        }
        $log->record('Integration: Removed Voided Purchases');
        return true;
    }

    public function sync_purchases(array $organisations, array $exclude = array(), $doc_type = 'invoice') {
        global $db, $log;

        if(!is_array($organisations) || empty($organisations)) {
            return false;
        }

        $currency_obj = new Currencies('USD');

        if($doc_type == 'order') {
            $query = $this->f_db->query("SELECT o.c_order_id AS documentid, o.ad_org_id, o.documentno, o.dateordered AS documentdate, bp.name AS bpname, bp.c_bpartner_id AS bpid, c.iso_code AS currency, pt.netdays AS paymenttermsdays
							FROM c_order o JOIN c_bpartner bp ON (bp.c_bpartner_id=o.c_bpartner_id)
							JOIN c_currency c ON (c.c_currency_id=o.c_currency_id)
							JOIN c_paymentterm pt ON (o.c_paymentterm_id=pt.c_paymentterm_id)
							WHERE o.ad_org_id IN ('".implode('\',\'', $organisations)."') AND issotrx='N' AND docstatus = 'CO' AND ((dateordered BETWEEN '".date('Y-m-d 00:00:00', strtotime($this->period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($this->period['to']))."') OR (o.updated BETWEEN '".date('Y-m-d 00:00:00', strtotime($this->period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($this->period['to']))."'))");
        }
        else {
            $query = $this->f_db->query("SELECT i.c_invoice_id AS documentid, i.ad_org_id, i.documentno, bp.name AS bpname, bp.c_bpartner_id AS bpid, c.iso_code AS currency, dateinvoiced AS documentdate, pt.netdays AS paymenttermsdays
							FROM c_invoice i JOIN c_bpartner bp ON (bp.c_bpartner_id=i.c_bpartner_id)
							JOIN c_currency c ON (c.c_currency_id=i.c_currency_id)
							JOIN c_paymentterm pt ON (i.c_paymentterm_id=pt.c_paymentterm_id)
							WHERE i.ad_org_id IN ('".implode('\',\'', $organisations)."') AND docstatus NOT IN ('VO', 'CL') AND issotrx='N' AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime($this->period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($this->period['to']))."')");
        }

        $document_newdata = array(0);
        while($document = $this->f_db->fetch_assoc($query)) {
            $document_newdata = array(
                    'foreignSystem' => $this->foreign_system,
                    'foreignId' => $document['documentid'],
                    'docNum' => $document['documentno'],
                    'date' => strtotime($document['documentdate']),
                    'spid' => $document['bpid'],
                    'affid' => $this->affiliates_index[$document['ad_org_id']],
                    'currency' => $document['currency'],
                    'paymentTerms' => $document['paymenttermsdays'],
                    'purchaseType' => 'SKI'
            );

            /* Get currencies FX from own system - START */
            $document_newdata['usdFxrate'] = $currency_obj->get_average_fxrate($document['currency'], array('from' => strtotime(date('Y-m-d', $newdata['date']).' 01:00'), 'to' => strtotime(date('Y-m-d', $newdata['date']).' 24:00')));
            if(empty($newdata['usdFxrate'])) {
                $document_newdata['usdFxrate'] = $currency_obj->get_average_fxrate($document['currency'], array('from' => strtotime(date('Y-m-d', $newdata['date']).' 01:00') - (24 * 60 * 60 * 7), 'to' => strtotime(date('Y-m-d', $newdata['date']).' 24:00')));
            }
            /* Get currencies FX from own system - END */

            if(value_exists('integration_mediation_purchaseorders', 'foreignId', $document['documentid'])) {
                $query2 = $db->update_query('integration_mediation_purchaseorders', $document_newdata, 'foreignId="'.$document['documentid'].'"');
            }
            else {
                $query2 = $db->insert_query('integration_mediation_purchaseorders', $document_newdata);
            }

            if($query2) {
                if(value_exists('integration_mediation_purchaseorderlines', 'foreignOrderId', $document['documentid'])) {
                    $db->delete_query('integration_mediation_purchaseorderlines', 'foreignOrderId="'.$document['documentid'].'"');
                }

                if($doc_type == 'order') {
                    $documentline_query = $this->f_db->query('SELECT ol.*, c_orderline_id AS documentlineid, ol.qtyordered AS quantity, p.name AS productname, u.x12de355 AS uom
										FROM c_orderline ol JOIN m_product p ON (p.m_product_id=ol.m_product_id)
										JOIN c_uom u ON (u.c_uom_id=p.c_uom_id)
										WHERE c_order_id=\''.$document['documentid'].'\'');
                }
                else {
                    $documentline_query = $this->f_db->query('SELECT il.*, c_invoiceline_id AS documentlineid, il.qtyinvoiced AS quantity, p.name AS productname, u.x12de355 AS uom
												FROM c_invoiceline il JOIN m_product p ON (p.m_product_id=il.m_product_id)
												JOIN c_uom u ON (u.c_uom_id=p.c_uom_id)
												WHERE c_invoice_id=\''.$document['documentid'].'\'');
                }

                $documentline_newdata = array();
                while($documentline = $this->f_db->fetch_assoc($documentline_query)) {
                    $documentline_newdata = array(
                            'foreignId' => $documentline['documentlineid'],
                            'foreignOrderId' => $document['documentid'],
                            'pid' => $documentline['m_product_id'],
                            'spid' => $document['bpid'],
                            'affid' => $this->affiliates_index[$document['ad_org_id']],
                            'price' => $documentline['priceactual'],
                            'producerPrice' => $documentline['em_ork_producerprice'],
                            'quantity' => $documentline['quantity'],
                            'quantityUnit' => $documentline['uom']
                    );

                    $db->insert_query('integration_mediation_purchaseorderlines', $documentline_newdata);
                }
            }
        }
        $log->record('Integration: Synced Purchases');
        $this->remove_voided_purchases($organisations, $doc_type);
    }

    private function remove_voided_purchases(array $organisations, $doc_type = 'invoice') {
        global $db, $log;

        if($doc_type == 'order') {
            return false;
        }
        else {
            $query = $this->f_db->query("SELECT i.c_invoice_id as documentid
							FROM c_invoice i
							WHERE  i.ad_org_id IN ('".implode('\',\'', $organisations)."') AND docstatus IN ('VO', 'CL') AND issotrx='N' AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime($this->period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($this->period['to']))."')");
        }

        while($document = $this->f_db->fetch_assoc($query)) {
            if(value_exists('integration_mediation_purchaseorders', 'foreignId', $document['documentid'])) {
                $db->delete_query('integration_mediation_purchaseorders', 'foreignId="'.$document['documentid'].'"');
                if(value_exists('integration_mediation_purchaseorderlines', 'foreignOrderId', $document['documentid'])) {
                    $db->delete_query('integration_mediation_purchaseorderlines', 'foreignOrderId="'.$document['documentid'].'"');
                }
            }
        }
        $log->record('Integration: Removed Voided Purchases');
        return true;
    }

    private function get_purchaseprice($transactionid, $costing_engine_id, $transaction_type = 'sale') {
        switch($this->get_costingengine($costing_engine_id)->get_shortname()) {
            case 'fifo':
                if($transaction_type == 'sale') {
                    $input_transactionid = $this->get_fifoinput($this->get_fifoutput_bytransacction($transactionid)['obwfa_input_stack_id'])->get()['m_transaction_id'];
                    $transcation = $this->get_transaction($input_transactionid);
                }
                else {
                    $transcation = $this->get_transaction($transactionid);
                }
                break;
            case 'standard':
            case 'average': // To be improved
                if($transaction_type == 'sale') {
                    $output_transcation_data = $this->get_transaction($transactionid)->get();
                    $input_transcation = new IntegrationOBTransaction('', $this->f_db);
                    $transcation = $input_transcation->get_lasttranscation_bydate($output_transcation_data['m_product_id'], $output_transcation_data['movementdate']);

                    if(is_null($transcation)) {
                        return 0;
                    }
                }
                else {
                    $transcation = $this->get_transaction($transactionid);
                }
                break;
            default: return 0;
                break;
        }

        $inoutline = $transcation->get_inoutline();
        if(!is_null($inoutline)) {
            $invoiceline = $inoutline->get_invoiceline();
            if(!is_null($invoiceline)) {
                $price['price'] = $invoiceline->get()['priceactual'];
                $price['currency'] = $invoiceline->get_invoice()->get_currency()->get()['iso_code'];
            }
        }

        if(empty($price)) {
            if(!is_null($inoutline)) {
                $orderline = $inoutline->get_orderline();
                $price['price'] = $orderline->get()['priceactual'];
                $price['currency'] = $orderline->get_order()->get_currency()->get()['iso_code'];
            }
            else {
                if(empty($transcation->get()['movementqty'])) {
                    return false;
                }
                $price['price'] = $transcation->get()['transactioncost'] / $transcation->get()['movementqty'];
                $price['currency'] = $transcation->get_currency()->get()['iso_code'];
            }
        }

        return $price;
    }

    private function get_transaction($transaction) {
        return new IntegrationOBTransaction($transaction, $this->f_db);
    }

    private function get_costingengine($id) {
        return new IntegrationOBCostingAlgorithm($id, $this->f_db);
    }

    public function get_fifoinputs(array $organisations, array $options) {
        if(isset($options['hasqty']) && $options['hasqty'] == true) {
            $query_extrawhere = ' AND remaining_qty !=0';
        }

        $query = $this->f_db->query("SELECT *
									FROM obwfa_input_stack
									WHERE ad_org_id IN ('".implode('\',\'', $organisations)."')
									AND trxdate BETWEEN '".date('Y-m-d 00:00:00', strtotime($this->period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($this->period['to']))."'{$query_extrawhere}
									ORDER BY trxdate ASC, m_product_id ASC");
        if($this->f_db->num_rows($query) > 0) {
            while($transcation = $this->f_db->fetch_assoc($query)) {
                $stack = new IntegrationOBInputStack($transcation['obwfa_input_stack_id'], $this->f_db);
                $inputs[$transcation['obwfa_input_stack_id']]['stack'] = $stack->get();
                if(is_null($stack->get_transcation()->get_firsttransaction()->get_inoutline())) {
                    $movement = $stack->get_transcation()->get_movementline();
                    if(is_object($movement)) {
                        $inputs[$transcation['obwfa_input_stack_id']]['stack']['daysinstock'] = $movement->get_output_transaction()->get_outputstack()->get_inputstack()->get_daysinstock();
                        $inputs[$transcation['obwfa_input_stack_id']]['movementdate'] = $movement->get_output_transaction()->get_outputstack()->get_inputstack()->get()['trxdate'];
                    }
                    else {
                        $inputs[$transcation['obwfa_input_stack_id']]['stack']['daysinstock'] = $stack->get_daysinstock();
                    }
                }
                else {
                    $inputs[$transcation['obwfa_input_stack_id']]['stack']['daysinstock'] = $stack->get_daysinstock();
                }

                $inputs[$transcation['obwfa_input_stack_id']]['product'] = $stack->get_product()->get();
                $inputs[$transcation['obwfa_input_stack_id']]['product']['category'] = $stack->get_product()->get_category()->get();
                $inputs[$transcation['obwfa_input_stack_id']]['category'] = &$inputs[$transcation['obwfa_input_stack_id']]['product']['category'];
                $inputs[$transcation['obwfa_input_stack_id']]['product']['uom'] = $stack->get_product()->get_uom()->get();
                $supplier = $stack->get_supplier();
                if(!empty($supplier)) {
                    $inputs[$transcation['obwfa_input_stack_id']]['supplier'] = $supplier->get();
                }
                //If movement type=return from customer, replace buisness partner by local product
                if($stack->get_transcation()->movementtype == 'C+') {
                    $localproduct = $stack->get_transcation()->get_firsttransaction()->get_product_local();
                    if(is_object($localproduct)) {
                        $inputs[$transcation['obwfa_input_stack_id']]['supplier'] = $localproduct->get_supplier()->get();
                    }
                    else {
                        $inputs[$transcation['obwfa_input_stack_id']]['supplier'] = 'unspecified';
                    }
                }
                $inputs[$transcation['obwfa_input_stack_id']]['transaction'] = $stack->get_transcation()->get();
                $inputs[$transcation['obwfa_input_stack_id']]['transaction']['attributes'] = $stack->get_transcation()->get_attributesetinstance()->get();
                $inputs[$transcation['obwfa_input_stack_id']]['transaction']['attributes']['daystoexpire'] = $stack->get_transcation()->get_attributesetinstance()->get_daystoexpire();
                if($inputs[$transcation['obwfa_input_stack_id']]['transaction']['attributes']['daystoexpire'] < 0) {
                    $inputs[$transcation['obwfa_input_stack_id']]['transaction']['attributes']['daystoexpire'] = '<span style="color:red;font-weight:bold;">Expired</span>';
                }
                $inputs[$transcation['obwfa_input_stack_id']]['transaction']['attributes']['packaging'] = $stack->get_transcation()->get_packaging();
                $inputs[$transcation['obwfa_input_stack_id']]['warehouse'] = $stack->get_warehouse()->get();

                $outputs = $stack->get_outputstacks();
                if(is_array($outputs)) {
                    foreach($outputs as $output) {
                        $inputs[$transcation['obwfa_input_stack_id']]['stack']['soldqty'] += $output->get()['qty'];
                    }
                }
                else {
                    $inputs[$transcation['obwfa_input_stack_id']]['stack']['soldqty'] = 0;
                }
            }
            return $inputs;
        }
        return false;
    }

    public function get_firsttransaction($organisations) {
        $query = $this->f_db->query("SELECT m_transaction_id
				FROM m_transaction
				WHERE ad_org_id IN ('".implode('\',\'', $organisations)."')
				ORDER BY trxprocessdate ASC");
        if($this->f_db->num_rows($query) > 0) {
            $transcation = $this->f_db->fetch_assoc($query);

            return new IntegrationOBTransaction($transcation['m_transaction_id'], $this->f_db);
        }
        return false;
    }

    public function get_totalvalue_bydate($date, $options = array(), $organisations = null) {
        if(empty($organisations)) {
            $organisations = $this->organisations;
        }

        $aging_scale = array(0, 120, 180, 360);
        if(is_array($options['aging_scale'])) {
            $aging_scale = $options['aging_scale'];
        }

        $aging_scale = array_combine(range(1, count($aging_scale)), $aging_scale);

        if($options['method'] == 'fifo') {
            $query = $this->f_db->query("SELECT *
                                        FROM obwfa_input_stack
                                        WHERE ad_org_id IN ('".implode('\',\'', $organisations)."')
                                        AND trxdate < '".date('Y-m-d 00:00:00', strtotime($date))."'
                                        ORDER BY trxdate ASC, m_product_id ASC");

            if($this->f_db->num_rows($query) > 0) {
                while($transcation = $this->f_db->fetch_assoc($query)) {
                    $stock['value'][$transcation['m_product_id']] += $transcation['cost'];
                    $stock['qty'][$transcation['m_product_id']] += $transcation['qty'];

                    $stack_obj = new IntegrationOBInputStack($transcation['obwfa_input_stack_id'], $this->f_db);
                    $outputs = $stack_obj->get_outputstacks('trxdate < \''.date('Y-m-d 00:00:00', strtotime($date)).'\'');

                    if(is_array($outputs)) {
                        foreach($outputs as $output_obj) {
                            $output = $output_obj->get();
                            $stock['value'][$transcation['m_product_id']] -= $output['cost'];
                            $stock['qty'][$transcation['m_product_id']] -= $output['qty'];

                            //$age = $stack_obj->get_daysinstock($output['trxdate']);
                            $age = $stack_obj->get_daysinstock($date);
                            $this->classify_data_byage($age, $aging_scale, $stock['aging']['value'], 0 - $output['cost']);
                        }
                    }
                    $age = $stack_obj->get_daysinstock($date);
                    $this->classify_data_byage($age, $aging_scale, $stock['aging']['value'], $transcation['cost']);
                }
            }
        }
        else {
            if(isset($options['costingalgorithm'])) {
                $query_where = ' AND trx.m_costing_algorithm_id=\''.$this->f_db->escape_string($options['costingalgorithm']).'\'';
            }

            $query = $this->f_db->query("SELECT trx.M_PRODUCT_ID, trx.MOVEMENTQTY AS QTY, CASE WHEN trx.MOVEMENTQTY < 0 THEN- tc.trxcost ELSE tc.trxcost END AS trxcost,
					                   trx.C_UOM_ID, trx.AD_CLIENT_ID, trx.iscostcalculated, tc.c_currency_id, coalesce(io.dateacct,trx.movementdate) as movementdate, trx.M_TRANSACTION_ID
					                    FROM M_TRANSACTION trx
					                      LEFT JOIN M_INOUTLINE iol ON trx.M_INOUTLINE_ID = iol.M_INOUTLINE_ID
					                      LEFT JOIN M_INOUT io ON iol.M_INOUT_ID = io.M_INOUT_ID
					                      LEFT JOIN (SELECT sum(cost) AS trxcost, m_transaction_id, c_currency_id
					                                 FROM M_TRANSACTION_COST
					                                 WHERE costdate < to_date( '".$date."' , 'yyyy-mm-dd')
					                                 GROUP BY m_transaction_id, c_currency_id) tc ON trx.m_transaction_id = tc.m_transaction_id
					                    WHERE trx.MOVEMENTDATE < to_date(  '".$date."' , 'yyyy-mm-dd')
											{$query_where}
											AND trx.ad_org_id IN ('".implode('\',\'', $organisations)."')");
            if($this->f_db->num_rows($query) > 0) {
                $stock = array();
                while($transcation = $this->f_db->fetch_assoc($query)) {
                    $transaction_obj = new IntegrationOBTransaction($transcation['m_transaction_id'], $this->f_db);
                    $fifo_input = $transaction_obj->get_inputstack();

                    if(is_object($fifo_input)) {
                        if(is_null($fifo_input->get_transcation()->get_inoutline())) {
                            $movement = $fifo_input->get_transcation()->get_movementline();
                            if(is_object($movement)) {
                                $transcation['daysinstock'] = $movement->get_output_transaction()->get_outputstack()->get_inputstack()->get_daysinstock();
                            }
                            else {
                                $transcation['daysinstock'] = $fifo_input->get_daysinstock();
                            }
                        }
                        else {
                            $transcation['daysinstock'] = $fifo_input->get_daysinstock();
                        }
                    }
                    $stock['info'][$transcation['m_product_id']] = $transcation;
                    $stock['value'][$transcation['m_product_id']] += $transcation['trxcost'];
                    $stock['qty'][$transcation['c_uom_id']][$transcation['m_product_id']] += $transcation['qty'];

                    end($aging_scale);
                    $last_aging_key = key($aging_scale);
                    reset($aging_scale);
                    foreach($aging_scale as $key => $age) {
                        if($transcation['daysinstock'] < $age || $key == $last_aging_key) {
                            $stock['aging'][$key]['value'][$transcation['m_product_id']] += $transcation['trxcost'];
                            $stock['aging'][$key]['qty'][$transcation['c_uom_id']][$transcation['m_product_id']] += $transcation['qty'];
                            break;
                        }
                        else {
                            continue;
                        }
                    }
                }

                $this->f_db->free_result($query);
            }
        }
        return $stock;
    }

    private function classify_data_byage($check_age, $aging_scale, &$variable, $value) {
        end($aging_scale);
        $last_aging_key = key($aging_scale);
        reset($aging_scale);
        foreach($aging_scale as $key => $age) {
            if($check_age == $age || ($check_age > $age && $check_age < $aging_scale[$key + 1]) || $key == $last_aging_key) {
                $variable[$key] += $value;
                break;
            }
            else {
                continue;
            }
        }
    }

    public function get_currcostingrule($organisation = '') {
        if(empty($organisation)) {
            $organisation = $this->organisations;
        }
        $query = $this->f_db->query("SELECT m_costing_rule_id, ad_org_id
				FROM m_costing_rule
				WHERE ad_org_id='".$this->f_db->escape_string(implode('\',\'', $organisation))."'
				AND isvalidated='Y' AND isvalidated='Y'
				ORDER BY datefrom DESC
				LIMIT 1 OFFSET 0");

        $rows_count = $this->f_db->num_rows($query);
        if($rows_count == 0) {
            return false;
        }

        if($rows_count > 1) {
            while($rule = $this->f_db->fetch_assoc($query)) {
                $rules[$rule['ad_org_id']] = new IntegrationCostingRule($rule['m_costing_rule_id'], $this->f_db);
            }
            return $rules;
        }
        else {
            $rule = $this->f_db->fetch_assoc($query);
            return new IntegrationCostingRule($rule['m_costing_rule_id'], $this->f_db);
        }
    }

    public function get_productsstock() {
        $query = $this->f_db->query("SELECT *
									FROM m_transcations
									WHERE movementdate < ");
    }

    private function get_fifoinput($id) {
        return new IntegrationOBInputStack($id, $this->f_db);
    }

    private function get_fifoinput_bytransacction($transaction) {
        return $this->f_db->fetch_assoc($this->f_db->query("SELECT *
			FROM obwfa_input_stack
			WHERE m_transaction_id='".$this->f_db->escape_string($transaction)."'"));
    }

    private function get_fifoutput_bytransacction($transaction) {
        return $this->f_db->fetch_assoc($this->f_db->query("SELECT *
			FROM obwfa_output_stack
			WHERE m_transaction_id='".$this->f_db->escape_string($transaction)."'"));
    }

    private function set_client_id($id) {
        $this->client = $this->f_db->escape_string($id);
    }

    public function get_saleinvoices($filters = null) {
        $invoices = new IntegrationOBInvoice(null, $this->f_db);
        return $invoices->get_saleinvoices($filters);
    }

    public function get_sales_byyearmonth($filters = null) {
        $sales = new IntegrationOBInvoiceLine(null, $this->f_db);

        if(!empty($filters)) {
            $filters .= ' AND ';
        }
        $filters .= "c_invoice.issotrx='Y'";
        return $sales->get_data_byyearmonth($filters);
    }

    public function get_status() {
        return $this->status;
    }

    public function get_dbconn() {
        return $this->f_db;
    }

    public function close_dbconn() {
        $this->f_db->close();
    }

}

class IntegrationOBTransaction extends IntegrationAbstractClass {
    protected $transaction;
    protected $f_db;

    const PRIMARY_KEY = 'm_transaction_id';
    const TABLE_NAME = 'm_transaction';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
            //Open connections
        }

        if(!empty($id)) {
            $this->read($id);
        }
    }

    private function read($id) {
        $this->transaction = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_transaction
						WHERE m_transaction_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_lasttranscation_bydate($product, $date) {
        $query = $this->f_db->query("SELECT m_transaction_id
						FROM m_transaction
						WHERE movementtype IN ('I+', 'V+') AND movementdate < '".$this->f_db->escape_string($date)."'
						AND m_product_id = '".$this->f_db->escape_string($product)."'
						ORDER BY movementdate DESC
						LIMIT 1 OFFSET 0");
        if($this->f_db->num_rows($query) > 0) {
            return new IntegrationOBTransaction($this->f_db->fetch_field($query, 'm_transaction_id'), $this->f_db);
        }
        else {
            return null;
        }
    }

    public function get_currency() {
        return new IntegrationOBCurrency($this->transaction['c_currency_id'], $this->f_db);
    }

    public function get_id() {
        return $this->transcation['m_transaction_id'];
    }

    public function get_inoutline() {
        if(empty($this->transaction['m_inoutline_id'])) {
            return null;
        }
        return new IntegrationOBInOutLine($this->transaction['m_inoutline_id'], $this->f_db);
    }

    public function get_movementline() {
        if(empty($this->transaction['m_movementline_id'])) {
            return null;
        }
        return new IntegrationOBMovementLine($this->transaction['m_movementline_id'], $this->f_db);
    }

    public function get_outputstack() {
        $query = $this->f_db->query('SELECT obwfa_output_stack_id
									FROM obwfa_output_stack
									WHERE m_transaction_id=\''.$this->transaction['m_transaction_id'].'\'');
        if($this->f_db->num_rows($query) > 0) {
            $stack = $this->f_db->fetch_assoc($query);
            return new IntegrationOBOutputStack($stack['obwfa_output_stack_id'], $this->f_db);
        }
    }

    public function get_inputstack() {
        $query = $this->f_db->query('SELECT obwfa_input_stack_id
									FROM obwfa_input_stack
									WHERE m_transaction_id=\''.$this->transaction['m_transaction_id'].'\'');
        if($this->f_db->num_rows($query) > 0) {
            $stack = $this->f_db->fetch_assoc($query);
            return new IntegrationOBInputStack($stack['obwfa_input_stack_id'], $this->f_db);
        }
        return false;
    }

    public function get_attributesetinstance() {
        return new IntegrationOBAttributeSetInstance($this->transaction['m_attributesetinstance_id'], $this->f_db);
    }

    public function get_packaging() {
        $filters = array('attributename' => array('id' => 'm_attribute_id', 'table' => 'm_attribute', 'attribute' => 'name', 'value' => 'Packaging'));

        $instance = $this->get_attributesetinstance()->get_attributeinstances($filters);
        if(is_array($instance)) {
            $instance = current($instance);
        }

        if(!empty($instance)) {
            return $instance->get_attributevalue($this->f_db)->get()['value'];
        }
        else {
            return false;
        }
    }

    public function get_transaction_byattr($attr, $value, $f_db = null) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        $query = $this->f_db->query('SELECT '.self::PRIMARY_KEY.' FROM '.self::TABLE_NAME.' WHERE '.$this->f_db->escape_string($attr).'=\''.$this->f_db->escape_string($value).'\'');
        if($this->f_db->num_rows($query) == 1) {
            $id = $this->f_db->fetch_field($query, self::PRIMARY_KEY);
            return new self($id, $this->f_db);
        }
        else {
            // to do
            return new self(null, $this->f_db);
        }
        return false;
    }

    public function get_product() {
        return new IntegrationOBProduct($this->transaction['m_product_id']);
    }

    public function get_product_local() {
        $product = new Products($db->fetch_field($db->query('SELECT localId FROM integration_mediation_products WHERE foreignSystem=3 AND foreignName="'.$this->get_product()->name.'"'), 'localId'));
        if(!is_object($product)) {
            $product = new Products($db->fetch_field($db->query('SELECT pid FROM products WHERE name="'.$this->get_product()->name.'"'), 'pid'));
        }
        return $product;
    }

    public function get_supplier() {
        return $this->get_inoutline()->get_inout()->get_bpartner();
    }

    public function __get($name) {
        if(isset($this->transaction[$name])) {
            return $this->transaction[$name];
        }
        return false;
    }

    public function get() {
        return $this->transaction;
    }

    public function get_firsttransaction() { //($inputstack = NULL)
        switch($this->movementtype) {
            case 'V+':
            case 'I+':
                $transaction = $this;
                break;
            case 'C+':
            case 'C-':
            case 'M+':
                if($this->transaction != NULL) {
                    $transaction = new IntegrationOBTransaction($this->f_db->fetch_field($this->f_db->query("SELECT * FROM m_transaction WHERE m_attributesetinstance_id='".$this->m_attributesetinstance_id."' ORDER BY movementdate ASC LIMIT 1"), 'm_transaction_id'), $this->f_db);
                }
                break;
            default:
                break;
        }
        return $transaction;

//        $inputstack = $this->get_inputstack();
//        if(!is_object($inputstack)) {
//            $outputstack = $this->get_outputstack();
//            if(is_object($outputstack)) {
//                $inputstack = $outputstack->get_inputstack(); //$this->get_input($outputstack);
//            }
//        }
//        $transaction = $this;
//        if(!is_object($transaction)) {
//            $transaction = $inputstack->get_transcation();
//        }
//        switch($transaction->movementtype) {
//            case 'V+':
//            case 'I+':
//                break;
//            case 'C+':
//                $inoutline = $transaction->get_inoutline();
//                if(!is_null($inoutline)) {
//                    $reverse_inoutline = $inoutline->get_orderline()->get_inoutline();
//                    $reverse_transaction = IntegrationOBTransaction::get_data(array(IntegrationOBInOutLine::PRIMARY_KEY => $reverse_inoutline->get_id()));
//                    $inputstack = $reverse_transaction->get_inputstack();
//                    $this->get_firstinputstack($inputstack);
//                }
//                break;
//            case 'M+':
//                $movement = $transaction->get_movementline();
//                $inputstack = $movement->get_output_transaction()->get_outputstack()->get_inputstack();
//                $this->get_firstinputstack($inputstack);
//                break;
//            default:
//                break;
//        }
//        return $inputstack;
    }

}

class IntegrationOBMovement {
    private $movement;
    private $f_db;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }

        if(empty($id)) {
            return null;
        }

        $this->read($id);
    }

    private function read($id) {
        $this->movement = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_movement
						WHERE m_movement_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_id() {
        return $this->movement['m_movement_id'];
    }

    public function get() {
        return $this->movement;
    }

}

class IntegrationOBMovementLine {
    private $movementline;
    private $f_db;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }

        if(empty($id)) {
            return null;
        }

        $this->read($id);
    }

    private function read($id) {
        $this->movementline = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_movementline
						WHERE m_movementline_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_attributesetinstance() {
        return new IntegrationOBAttributeSetInstance($this->movementline['m_attributesetinstance_id'], $this->f_db);
    }

    public function get_movement() {
        return new IntegrationOBMovement($this->movementline['m_movement_id']);
    }

    public function get_product() {
        return new IntegrationOBProduct($this->movementline['m_product_id']);
    }

    public function get_fromlocator() {
        return new IntegrationOBLocator($this->movementline['m_locator_id']);
    }

    public function get_tolocator() {
        return new IntegrationOBLocator($this->movementline['m_locatorto_id']);
    }

    public function get_output_transaction() {
        $query = $this->f_db->query('SELECT t.m_transaction_id
								FROM m_transaction t
								JOIN obwfa_output_stack os ON (os.m_transaction_id=t.m_transaction_id)
								WHERE t.m_movementline_id=\''.$this->movementline['m_movementline_id'].'\'');
        if($this->f_db->num_rows($query) > 0) {
            $transaction = $this->f_db->fetch_assoc($query);
            return new IntegrationOBTransaction($transaction['m_transaction_id'], $this->f_db);
        }

        return false;
    }

    public function get_input_transcation() {

    }

    public function get_id() {
        return $this->movementline['m_movementline_id'];
    }

    public function get() {
        return $this->movementline;
    }

}

class IntegrationOBInOutLine extends IntegrationAbstractClass {
    protected $inoutline;
    protected $f_db;

    const PRIMARY_KEY = 'm_inoutline_id';
    const TABLE_NAME = 'm_inoutline';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }

        if(empty($id)) {
            return null;
        }

        $this->read($id);
    }

    private function read($id) {
        $this->inoutline = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_inoutline
						WHERE m_inoutline_id='".$this->f_db->escape_string($id)."'"));
        if(empty($this->inoutline)) {
            return null;
        }
    }

    public function get_inout() {
        return new IntegrationOBInOut($this->inoutline['m_inout_id'], $this->f_db);
    }

    public function get_orderline() {
        return new IntegrationOBOrderLine($this->inoutline['c_orderline_id'], $this->f_db);
    }

    public function get_invoiceline() {
        $invoiceline = new IntegrationOBInvoiceLine('', $this->f_db);
        return $invoiceline->get_byinoutline($this->inoutline['m_inoutline_id']);
    }

    public function get_attributesetinstance() {
        return new IntegrationOBAttributeSetInstance($this->inoutline['m_attributesetinstance_id'], $this->f_db);
    }

    public function get_inoutline_byattr($attr, $value, $f_db = null) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        $sql = 'SELECT '.self::PRIMARY_KEY.' FROM '.self::TABLE_NAME.' WHERE '.$this->f_db->escape_string($attr).'=\''.$this->f_db->escape_string($value).'\'';
        $query = $this->f_db->query($sql);
        if($this->f_db->num_rows($query) == 1) {
            $id = $this->f_db->fetch_field($query, self::PRIMARY_KEY);
            return new self($id, $this->f_db);
        }
        else {
            if($this->f_db->num_rows($query) > 1) {
                while($item = $this->f_db->fetch_assoc($query)) {
                    $items[$item[self::PRIMARY_KEY]] = new self($item[self::PRIMARY_KEY], $this->f_db);
                }
                return $items;
            }
            return new self(null, $this->f_db);
        }
        return false;
    }

    public function get_id() {
        return $this->inoutline['m_inoutline_id'];
    }

    public function __get($name) {
        if(isset($this->inoutline[$name])) {
            return $this->inoutline[$name];
        }
        return false;
    }

    public function __isset($name) {
        return isset($this->inoutline[$name]);
    }

    public function get() {
        return $this->inoutline;
    }

}

class IntegrationOBInOut {
    private $inout;
    private $f_db;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }
        $this->read($id);
    }

    private function read($id) {
        $this->inout = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_inout
						WHERE m_inout_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_invoice() {
        return new IntegrationOBInvoice($this->inout['c_invoice_id'], $this->f_db);
    }

    public function get_bpartner() {
        return new IntegrationOBBPartner($this->inout['c_bpartner_id'], $this->f_db);
    }

    public function get_id() {
        return $this->inout['m_inout_id'];
    }

    public function __get($name) {
        if(isset($this->inout[$name])) {
            return $this->inout[$name];
        }
        return false;
    }

    public function get() {
        return $this->inout;
    }

}

class IntegrationOBInvoice {
    private $invoice;
    private $f_db;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {

        }

        if(empty($id)) {
            return false;
        }

        $this->read($id);
    }

    private function read($id) {
        $this->invoice = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM c_invoice
						WHERE c_invoice_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_currency($currency = '') {
        if(!empty($currency)) {
            return new IntegrationOBCurrency($currency, $this->f_db);
        }
        else {
            return new IntegrationOBCurrency($this->invoice['c_currency_id'], $this->f_db);
        }
    }

    public function get_id() {
        return $this->invoice['c_invoice_id'];
    }

    public function get_salesrep() {
        return new IntegrationOBUser($this->invoice['salesrep_id'], $this->f_db);
    }

    public function get_customer() {
        return new IntegrationOBBPartner($this->invoice['c_bpartner_id'], $this->f_db);
    }

    public function get_saleorder() {
        return new IntegrationOBOrder($this->invoice['c_order_id'], $this->f_db);
    }

    public function get_paymentterm() {
        return new IntegrationOBPaymentTerm($this->invoice['c_paymentterm_id'], $this->f_db);
    }

    public function get_organisation() {
        return new IntegrationOBOrg($this->invoice['ad_org_id'], $this->f_db);
    }

    public function get_invoicelines() {
        $lines = new IntegrationOBInvoiceLine(null, $this->f_db);
        return $lines->get_invoicelines('c_invoice_id=\''.$this->invoice['c_invoice_id'].'\'');
    }

    public function is_saletrx() {
        if($this->invoice['issotrx'] == 'Y') {
            return true;
        }
        return false;
    }

    public function get_saleinvoices($filters = null) {
        if(!empty($filters)) {
            $query_where = ' AND '.$filters; //' AND '.$this->f_db->escape_string($filters);
        }
        $query = $this->f_db->query("SELECT c_invoice_id FROM c_invoice WHERE issotrx='Y'".$query_where." ORDER by dateinvoiced ASC, created ASC");
        if($this->f_db->num_rows($query) > 0) {
            while($invoice = $this->f_db->fetch_assoc($query)) {
                $invoices[$invoice['c_invoice_id']] = new self($invoice['c_invoice_id'], $this->f_db);
            }
            return $invoices;
        }
        return false;
    }

    public static function get_aggregates(array $sums, array $groupby, $filters) {
        if(!empty($filters)) {
            $query_where = ' AND '.$filters; //' AND '.$this->f_db->escape_string($filters);
        }

        foreach($sums as $attr) {
            $attr = $this->f_db->escape_string($attr);
            $sum_select .= $comma.'SUM('.$attr.') AS '.$attr;
            $comma = ', ';
        }

        $groupby = array_map('$this->f_db->escape_string', $groupby);
        $groupby_querystring = ' GROUP BY '.implode(', ', $groupby);
        $query = $this->f_db->query("SELECT c_invoice_id, ".$sum_select." FROM c_invoice WHERE issotrx='Y'".$query_where.$groupby_querystring);
        if($this->f_db->num_rows($query) > 0) {
            while($invoice = $this->f_db->fetch_assoc($query)) {
                $invoices[] = $invoice;
            }
            return $invoices;
        }
        return false;
    }

    public function get_paymentplan() {
        return IntegrationOBFinPaymentSchedule::get_data("c_invoice_id='".$this->invoice['c_invoice_id']."'");
    }

    public function __get($name) {
        if(isset($this->invoice[$name])) {
            return $this->invoice[$name];
        }
        return false;
    }

    public function get() {
        return $this->invoice;
    }

}

class IntegrationOBInvoiceLine extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'c_invoiceline_id';
    const TABLE_NAME = 'c_invoiceline';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

    public function get_byinoutline($id) {
        $query = $this->f_db->query("SELECT c_invoiceline_id
						FROM c_invoiceline
						WHERE m_inoutline_id='".$this->f_db->escape_string($id)."'");
        if($this->f_db->num_rows($query) > 0) {
            return new IntegrationOBInvoiceLine($this->f_db->fetch_field($query, 'c_invoiceline_id'), $this->f_db);
        }
        return null;
    }

    public function get_inoutline() {
        return new IntegrationOBInOutLine($this->data['m_inoutline_id'], $this->f_db);
    }

    public function get_orderline() {
        return new IntegrationOBOrderLine($this->data['c_orderline_id'], $this->f_db);
    }

    public function get_invoice() {
        return new IntegrationOBInvoice($this->data['c_invoice_id'], $this->f_db);
    }

    public function get_product() {
        return new IntegrationOBProduct($this->data['m_product_id'], $this->f_db);
    }

    public function get_product_local() {
        global $db;
        $product = new Products($db->fetch_field($db->query('SELECT localId FROM integration_mediation_products WHERE foreignSystem=3 AND foreignName="'.$this->get_product()->name.'"'), 'localId'));
        if(!is_object($product)) {
            $product = new Products($db->fetch_field($db->query('SELECT pid FROM products WHERE name="'.$this->get_product()->name.'"'), 'pid'));
        }
        return $product;
    }

    public function get_uom() {
        return new IntegrationOBUom($this->data['c_uom_id'], $this->f_db);
    }

    public function get_cost($referer = null) {
        global $core;
        //$transaction = $this->get_transaction();
//        if(is_array($transaction)) {
//            $cost = 0;
//            foreach($transaction as $trx) {
//                $cost += $trx->transactioncost;
//            }
//            return $cost;
//        }
        return $this->get_transaction()->transactioncost;
    }

    public function get_transaction() {
        $inoutline = $this->get_inoutline();
        if(!$inoutline->get_id()) {
            $inoutline = $this->get_orderline()->get_inoutline();
            if(!$inoutline->get_id()) {
                $iol = new IntegrationOBInOutLine(null, $this->f_db);
                $inoutline = $iol->get_inoutline_byattr('c_orderline_id', $this->get_orderline()->get_id());
                unset($iol);
            }
        }
        $transaction = new IntegrationOBTransaction(null, $this->f_db);
        if(is_array($inoutline)) {
            //$transactions = array();
            foreach($inoutline as $iol) {
                if($iol->get_inout()->docstatus == 'CO') {
                    return $transaction->get_transaction_byattr('m_inoutline_id', $iol->m_inoutline_id);
                }
                //$transactions[] = $transaction->get_transaction_byattr('m_inoutline_id', $iol->m_inoutline_id);
            }
            return null;
            //return $transactions;
        }
        return $transaction->get_transaction_byattr('m_inoutline_id', $inoutline->m_inoutline_id);
    }

    public function get_invoicelines($filters) {
        if(!empty($filters)) {
            $query_where = ' WHERE '.$filters; //' AND '.$this->f_db->escape_string($filters);
        }
        $query = $this->f_db->query("SELECT ".self::PRIMARY_KEY." FROM ".self::TABLE_NAME.$query_where);
        if($this->f_db->num_rows($query) > 0) {
            while($invoiceline = $this->f_db->fetch_assoc($query)) {
                $invoicelines[$invoiceline[self::PRIMARY_KEY]] = new self($invoiceline[self::PRIMARY_KEY], $this->f_db);
            }
            return $invoicelines;
        }
        return false;
    }

    public function get_data_byyearmonth($filters = '', $options = array()) {
        //$rawdata = $this->get_aggreateddata_byyearmonth('salesrep_id, c_currency_id', $filters);
        $lines = IntegrationOBInvoiceLine::get_data($filters); // array('order' => array('sort' => 'DESC', 'by' => 'qtyinvoiced')));
        if(is_array($lines)) {
            foreach($lines as $line) {
                $invoice = $line->get_invoice();
                $invoice->dateinvoiceduts = strtotime($invoice->dateinvoiced);
                $invoice->dateparts = getdate($invoice->dateinvoiceduts);
                $currency = $invoice->get_currency();
                $data['salerep']['qty'][$invoice->salesrep_id][$invoice->dateparts['year']][$invoice->dateparts['mon']] += $line->qtyinvoiced;


                if(is_empty($line->m_product_id)) {
                    $line->m_product_name = 'Unspecified';
                }
                else {
                    $product = $line->get_product();
                    if(is_object($product) && !is_empty($product->name)) {
                        $line->m_product_name = $product->name;
                    }
                    else {
                        $line->m_product_name = $line->get_product_local()->name;
                    }
                }
                if(is_empty($invoice->salesrep_id)) {
                    $invoice->salesrep_id = 0;
                }
                $iltrx = $line->get_transaction();
                if(is_object($iltrx)) {
                    $outputstack = $iltrx->get_outputstack();
                }
                if(is_object($outputstack)) {
                    $inputstack = $outputstack->get_inputstack();
                }
                $product = $line->get_product_local();
                if(is_object($product)) {
                    $invoice->bpartner_name = $product->get_supplier()->name;
                }
                if(empty($invoice->bpartner_name) || strstr($invoice->bpartner_name, 'Orkila')) {
                    if(is_object($inputstack)) {
                        $invoice->bpartner_name = $inputstack->get_supplier()->name;
                    }
                    if(empty($invoice->bpartner_name) || strstr($invoice->bpartner_name, 'Orkila')) {
                        $invoice->bpartner_name = $line->get_product_local()->get_supplier()->name;
                    }
                    if(empty($invoice->bpartner_name)) {
                        $invoice->bpartner_name = 'Unspecified';
                    }
                }

                if(!empty($options['reportcurrency'])) {
                    $reportcurrency = new Currencies($options['reportcurrency']);
                    $fxrate = $reportcurrency->get_fxrate_bytype($options['fxtype'], $currency->iso_code, array('from' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice->dateinvoiceduts).' 24:00'), 'year' => date('Y', $invoice->dateinvoiceduts), 'month' => date('m', $invoice->dateinvoiceduts)), array('precision' => 4));
                    if(!empty($fxrate)) {
                        $data['salerep']['linenetamt'][$invoice->salesrep_id][$invoice->dateparts['year']][$invoice->dateparts['mon']] += $line->linenetamt / $fxrate;
                        //   $data['products']['linenetamt'][$line->m_product_name][$invoice->dateparts['year']][$invoice->dateparts['mon']] += $line->linenetamt / $fxrate;
                        //   $data['suppliers']['linenetamt'][$invoice->bpartner_name][$invoice->dateparts['year']][$invoice->dateparts['mon']] += $line->linenetamt / $fxrate;
                        $dataperday['salerep']['linenetamt'][$invoice->salesrep_id][$invoice->dateparts['year']][$invoice->dateparts['mon']][$invoice->dateparts['mday']] += $line->linenetamt / $fxrate;
                        $dataperday['products']['linenetamt'][$line->m_product_name][$invoice->dateparts['year']][$invoice->dateparts['mon']][$invoice->dateparts['mday']] += $line->linenetamt / $fxrate;
                        $dataperday['suppliers']['linenetamt'][$invoice->bpartner_name][$invoice->dateparts['year']][$invoice->dateparts['mon']][$invoice->dateparts['mday']] += $line->linenetamt / $fxrate;
                    }
                }
                else {
                    $data['salerep']['linenetamt'][$invoice->salesrep_id][$invoice->dateparts['year']][$invoice->dateparts['mon']] += $line->linenetamt;
                    $dataperday['salerep']['linenetamt'][$invoice->salesrep_id][$invoice->dateparts['year']][$invoice->dateparts['mon']][$invoice->dateparts['mday']] += $line->linenetamt;
                    $dataperday['products']['linenetamt'][$line->m_product_name][$invoice->dateparts['year']][$invoice->dateparts['mon']][$invoice->dateparts['mday']]+= $line->linenetamt;
                    $dataperday['suppliers']['linenetamt'][$invoice->bpartner_name][$invoice->dateparts['year']][$invoice->dateparts['mon']][$invoice->dateparts['mday']] += $line->linenetamt;
                }
            }
            $data['dataperday'] = $dataperday;
            return $data;
        }
        return false;
    }

    public function get_classification($data, $period, $options = array()) {
        global $core;
        $tableindexes = array('salerep', 'products', 'suppliers');
        $classificationtypes = array('bymonth', 'byytd', 'byquarter');
        $TIME_NOW = TIME_NOW;
//        if($core->user['uid'] == 362) {
//            $TIME_NOW = '1451460679';
//        }
        $current_year = date('Y', $TIME_NOW);
        foreach($tableindexes as $tableindex) {
            if(is_array($data[$tableindex])) {
                if(($TIME_NOW > $period['from']) && ($TIME_NOW < $period['to'])) {
                    foreach($data[$tableindex]['linenetamt'] as $id => $salerepdata) {//rename salerepdata
                        $currentquarter = ceil(date('n', $TIME_NOW) / 3);
                        $current_month = date("m");
                        $currentyeardata = $salerepdata[$current_year];
                        if(isset($currentyeardata[$current_month]) && !empty($currentyeardata[$current_month])) {
                            $classification[$tableindex]['bymonth'][$tableindex][$id]['currentdata'] = array_sum($currentyeardata[$current_month]) / 1000;
                            $classification[$tableindex]['bymonth'][$tableindex][$id]['currentmonthdata'] = array_sum($currentyeardata[$current_month]) / 1000;
                            $classification_data[$id] = array_sum($currentyeardata[$current_month]) / 1000;
                        }
                        else {
                            $classification[$tableindex]['bymonth'][$tableindex][$id]['currentdata'] = 0;
                            $classification_data[$id] = 0;
                        }
                        if(is_array($currentyeardata)) {
                            foreach($currentyeardata as $cydata_array) {
                                $cydata = array_sum($cydata_array);
                                if(!empty($cydata)) {
                                    $classification[$tableindex]['byytd'][$tableindex][$id]['currentdata'] +=$cydata / 1000;
                                    $ytdclassification_data[$id] +=$cydata / 1000;
                                }
                                else {
                                    $classification[$tableindex]['byytd'][$tableindex][$id]['currentdata'] += 0;
                                    $ytdclassification_data[$id] +=0;
                                }
                            }
                            //Change variable names from months to ytd
                            $classification[$tableindex]['byytd'][$tableindex][$id]['currentmonthdata'] = $classification[$tableindex]['byytd'][$tableindex][$id]['currentdata'];
                            //Get Last year total data to be compared with current year data
                            $lastyeardata = $salerepdata[($current_year - 1)];
                            if(is_array($lastyeardata)) {
                                foreach($lastyeardata as $lydata_array) {
                                    if(is_array($lydata_array)) {
                                        $lydata = array_sum($lydata_array);
                                    }
                                    if(!empty($lydata)) {
                                        $classification[$tableindex]['byytd'][$tableindex][$id]['prevmonthdata'] +=$lydata / 1000;
                                    }
                                    else {
                                        $classification[$tableindex]['byytd'][$tableindex][$id]['prevmonthdata'] += 0;
                                    }
                                }
                            }
//////////////////////////////////////////////////////////////////
                        }
//Get Last Month data to be compared with current month data
                        if(is_array($currentyeardata[($current_month - 1)])) {
                            $classification[$tableindex]['bymonth'][$tableindex][$id]['prevmonthdata'] = array_sum($currentyeardata[$current_month - 1]) / 1000;
                        }
                        else {
                            $classification[$tableindex]['bymonth'][$tableindex][$id]['prevmonthdata'] = 0;
                        }
///////////////////////////////////////////////////////////////////
//classify data by quarter//
                        $qmonths = $this->get_quartermonths($currentquarter);
                        foreach($qmonths as $month) {
                            if(is_array($currentyeardata[$month])) {
                                $classification[$tableindex]['byquarter'][$tableindex][$id]['currentdata'] += array_sum($currentyeardata[$month]) / 1000;
                                $qclassification_data[$id] +=array_sum($currentyeardata[$month]) / 1000;
                            }
                        }
                    }
///Sort Data descending to classify top supp/products or BM //
                    if(isset($classification_data) && isset($classification[$tableindex]['bymonth'][$tableindex])) {
                        array_multisort($classification_data, SORT_DESC, $classification[$tableindex]['bymonth'][$tableindex]);
                    }
                    if(isset($ytdclassification_data) && isset($classification[$tableindex]['byytd'][$tableindex])) {
                        array_multisort($ytdclassification_data, SORT_DESC, $classification[$tableindex]['byytd'][$tableindex]);
                    }
                    if(isset($qclassification_data) && isset($classification[$tableindex]['byquarter'][$tableindex])) {
                        array_multisort($qclassification_data, SORT_DESC, $classification[$tableindex]['byquarter'][$tableindex]);
                    }
                    unset($classification_data, $ytdclassification_data, $qclassification_data);
                }
                else {
                    $from = getdate($period['from']);
                    $to = getdate($period['to']);
                    foreach($data[$tableindex]['linenetamt'] as $id => $salerepdata) {
                        if(is_array($salerepdata)) {
                            foreach($salerepdata as $year => $year_data) {
                                foreach($year_data as $month => $month_data) {
                                    if(is_array($month_data)) {
                                        foreach($month_data as $day => $day_data) {
                                            if(($year == $from['year'] && $month >= $from['mon'] && $day >= $from['mday']) || ($year == $to['year'] && $month <= $to['mon'] && $day <= $to['mday'])) {
                                                if($from['mon'] == $to['mon']) {
                                                    if(!($day >= $from['mday'] && $day <= $to['mday'])) {
                                                        continue;
                                                    }
                                                }
                                                $classification[$tableindex]['wholeperiod'][$tableindex][$id]['currentdata'] +=$day_data / 1000;
                                                $periodclassification[$id] +=$day_data / 1000;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if(isset($periodclassification) && isset($classification[$tableindex]['wholeperiod'][$tableindex])) {
                        array_multisort($periodclassification, SORT_DESC, $classification[$tableindex]['wholeperiod'][$tableindex]);
                    }
                    unset($periodclassification);
                }
            }
            //  }
            if($options['reporttype'] == 'endofmonth') {
                $classification[$tableindex]['byytd'][$tableindex] = $this->get_ytdsales($data, $period, $tableindex);
            }
        }
//Select only top 10 out of the array//
        foreach($tableindexes as $tableindex) {
            foreach($classificationtypes as $type) {
                if(is_array($classification[$tableindex][$type][$tableindex])) {
                    $classification[$tableindex][$type][$tableindex] = array_slice($classification[$tableindex][$type][$tableindex], 0, 10);
                }
            }
            if(is_array($classification[$tableindex]['wholeperiod'][$tableindex])) {
                $classification[$tableindex]['wholeperiod'][$tableindex] = array_slice($classification[$tableindex]['wholeperiod'][$tableindex], 0, 10);
            }
        }

        return $classification;
    }

    public function get_ytdsales($data, $period, $tableindex) {
        global $core;
        $TIME_NOW = TIME_NOW;
        $classificationtypes = array('byytd');
        $current_year = date('Y', $TIME_NOW);
        if(is_array($data[$tableindex])) {
            foreach($data[$tableindex]['linenetamt'] as $id => $salerepdata) {//rename salerepdata
                $currentquarter = ceil(date('n', $TIME_NOW) / 3);
                $current_month = date("m");
                $currentyeardata = $salerepdata[$current_year];
                if(is_array($currentyeardata)) {
                    foreach($currentyeardata as $cydata_array) {
                        $cydata = array_sum($cydata_array);
                        if(!empty($cydata)) {
                            $classification[$tableindex]['byytd'][$tableindex][$id]['currentdata'] +=$cydata / 1000;
                            $ytdclassification_data[$id] +=$cydata / 1000;
                        }
                        else {
                            $classification[$tableindex]['byytd'][$tableindex][$id]['currentdata'] += 0;
                            $ytdclassification_data[$id] +=0;
                        }
                    }
                    //Change variable names from months to ytd
                    $classification[$tableindex]['byytd'][$tableindex][$id]['currentmonthdata'] = $classification[$tableindex]['byytd'][$tableindex][$id]['currentdata'];
                    //Get Last year total data to be compared with current year data
                    $lastyeardata = $salerepdata[($current_year - 1)];
                    if(is_array($lastyeardata)) {
                        $to = getdate($period['to']);
                        foreach($lastyeardata as $lydata_array) {
                            if(is_array($lydata_array)) {
                                foreach($lydata_array as $year => $year_data) {
                                    if(is_array($year_data)) {
                                        foreach($year_data as $month => $month_data) {
                                            if(is_array($month_data)) {
                                                foreach($month_data as $day => $day_data) {
                                                    if(!($month <= $to['mon'] && $day <= $to['mday'])) {
                                                        continue;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                                $lydata = array_sum($lydata_array);
                            }
                            if(!empty($lydata)) {
                                $classification[$tableindex]['byytd'][$tableindex][$id]['prevmonthdata'] +=$lydata / 1000;
                            }
                            else {
                                $classification[$tableindex]['byytd'][$tableindex][$id]['prevmonthdata'] += 0;
                            }
                        }
                    }
//////////////////////////////////////////////////////////////////
                }
            }

            ///Sort Data descending to classify top supp/products or BM //
            if(isset($ytdclassification_data) && isset($classification[$tableindex]['byytd'][$tableindex])) {
                array_multisort($ytdclassification_data, SORT_DESC, $classification[$tableindex]['byytd'][$tableindex]);
            }

            unset($classification_data, $ytdclassification_data, $qclassification_data);
            //   }
        }
        return $classification[$tableindex]['byytd'][$tableindex];
    }

    public function parse_classificaton_tables($classification) {
        global $lang, $core;
        $TIME_NOW = TIME_NOW;
        $css_styles['header'] = 'background-color: #F1F1F1;';
        $css_styles['altrow'] = 'background-color:#D0F6AA;'; #f7fafd;;';

        $tableindexes = array('products', 'suppliers', 'salerep');
        foreach($tableindexes as $tableindex) {
            switch($tableindex) {
                case 'salerep':
                    $classname = 'IntegrationOBUser';
                    break;
                case 'suppliers':
                    $classname = 'IntegrationOBBPartner';
                    break;
                case 'products':
                    $classname = 'IntegrationOBProduct';
                    break;
                default:
                    break;
            }
            $rowstyle = $css_styles['altrow'];
            if(is_array($classification[$tableindex])) {
                foreach($classification[$tableindex] as $classificationtype => $classificationdata) {
                    if(is_array($classificationdata)) {
                        $colspan = 5;
                        if($tableindex == 'products') {
                            $colspan = 6;
                        }
                        $output .= '<table class="datatable"><tr><td style="background-color:#92D050;font-weight:bold;" colspan="'.$colspan.'">'.$lang->topten.' '.$lang->$tableindex.' '.$lang->$classificationtype.'</td></tr>';
                        $output .= '<tr style="'.$css_styles['header'].'"><th>'.$lang->rank.'</th><th>'.$lang->$tableindex.'</th>';
                        if($tableindex == 'products') {
                            $output .='<th>'.$lang->supplier.'</th>';
                        }
                        if($classificationtype != 'wholeperiod' && $classificationtype != 'byquarter') {
                            switch($classificationtype) {
                                case 'bymonth':
                                    $prevperiod = getdate($TIME_NOW);
                                    $currperiod = ' ('.$prevperiod['month'].')';
                                    $dateObj = DateTime::createFromFormat('!m', $prevperiod['mon'] - 1);
                                    $prevperiod = $dateObj->format('F');
                                    unset($dateObj);
                                    break;
                                case 'byytd':
                                    $currperiod = ' ('.(date('Y', $TIME_NOW)).')';
                                    $prevperiod = (date('Y', $TIME_NOW) - 1);
                                    break;
                                default:
                                    break;
                            }
                        }
                        $output .='<th>'.$lang->currentdata.$currperiod.'</th>';
                        unset($currperiod);
                        if($classificationtype != 'wholeperiod' && $classificationtype != 'byquarter') {
                            $output .='<th>'.$lang->prevdata.'('.$prevperiod.')</th><th>'.$lang->position.'</th>';
                        }
                        $output .= '</tr>';
                        if(is_array($classificationdata[$tableindex])) {
                            reset($classificationdata[$tableindex]);
                            $topofthemonthid = key($classificationdata[$tableindex]);
                            // }
                            $rank = 1;
                            foreach($classificationdata[$tableindex] as $id => $cdata) {
                                if(is_array($cdata)) {
                                    if($classificationtype != 'wholeperiod') {
                                        if(!isset($cdata['prevmonthdata']) && $classificationtype != 'byquarter') {
                                            $cdata['prevmonthdata'] = 0;
                                        }
                                        $position = '<img src="'.$core->settings['rootdir'].'/images/icons/red_down_arrow.gif" alt="&darr;"/>';
                                        if($cdata['currentmonthdata'] > $cdata['prevmonthdata']) {
                                            $position = '<img src="'.$core->settings['rootdir'].'/images/icons/green_up_arrow.gif" alt="&uarr;"/>';
                                        }
                                    }
                                    unset($cdata['currentmonthdata']);


                                    if($classname == 'IntegrationOBUser') {
                                        $object = new $classname($id);
                                        if(!is_object($object) || empty($object->name) || $object->name == 'System') {
                                            $object->name = 'unspecified';
                                            continue;
                                        }
                                    }
                                    $output .= '<tr style="'.$rowstyle.'"><td>#'.$rank.'</td>';
                                    if($classname == 'IntegrationOBUser' && is_object($object)) {
                                        $output .= '<td>'.$object->name.'</td>';
                                    }
                                    else {
                                        $output .= '<td>'.$id.'</td>';
                                    }
                                    if($tableindex == 'products') {
                                        $supplier_output = 'Unspecified';
                                        $product = IntegrationOBProduct::get_data("name='".$this->f_db->escape_string($id)."'");
                                        if(is_object($product)) {
                                            if(is_object($product->get_supplier())) {
                                                $supplier_output = $product->get_supplier()->get_displayname();
                                            }
                                        }
                                        $output .='<td>'.$supplier_output.'</td>';
                                    }
                                    $numfmt = new NumberFormatter($lang->settings['locale'], NumberFormatter::DECIMAL);
                                    foreach($cdata as $data) {
                                        if($data > 10) {
                                            $numfmt->setPattern("#,##0");
                                        }
                                        else {
                                            $numfmt->setPattern("#0.##");
                                        }
                                        $output .= '<td style="text-align:right;">'.$numfmt->format($data).'</td>';
                                    }
                                    if($classificationtype != 'wholeperiod' && $classificationtype != 'byquarter') {
                                        $output .= '<td style="text-align:center;">'.$position.'</td>';
                                    }
                                    $output .='</tr>';
                                }
                                unset($position);
                                $rank++;
                            }
                        }
                        $output .= '</table><br/>';
                        $label = $lang->$tableindex;
                        if($classname == 'IntegrationOBUser') {
                            $topofthemonth_obj = new $classname($topofthemonthid);
                            if(is_object($topofthemonth_obj)) {
                                $topofthemonth_obj_name = $topofthemonth_obj->name;
                            }
                        }
                        else {
                            $topofthemonth_obj_name = $topofthemonthid;
                            $label = substr_replace($lang->$tableindex, '', -1);
                        }
                        $topclassification_summary .='<div>Top '.$label.' '.$lang->$classificationtype.' : <span style="font-weight:bold;">'.$topofthemonth_obj_name.'</span></div><br/>';


                        $output .='<div style="width:100%;"><h2>'.$lang->chart.' <small>(K Table Amounts )</small> </h2>';
                        $chart = $this->parse_classificaton_charts($classificationdata[$tableindex], $tableindex);
                        if(!empty($chart)) {
                            $output .= '<img src="data:image/png;base64,'.base64_encode(file_get_contents($this->parse_classificaton_charts($classificationdata[$tableindex], $tableindex))).'" />';
                        }
                        $output .= '</div><br/>';
                    }
                }
            }
        }
        $classificationoutput['tablesandcharts'] = $output;
        $classificationoutput['summary'] = $topclassification_summary;

        return $classificationoutput;
    }

    public function parse_classificaton_charts($data, $type) {
        global $lang, $core;
        $TIME_NOW = TIME_NOW;
        if(is_array($data)) {
            $data_ids = array_keys($data);
            switch($type) {
                case 'salerep':
                    $classname = 'IntegrationOBUser';
                    break;
                case 'suppliers':
                    $classname = 'IntegrationOBBPartner';
                    break;
                case 'products':
                    $classname = 'IntegrationOBProduct';
                    break;
                default:
                    break;
            }
            foreach($data_ids as $id) {
                if($classname == 'IntegrationOBUser') {
                    $object = new $classname($id);
                    if(!is_object($object) || empty($object->name)) {
                        $yaxixdata[] = 'unspecified';
                    }
                    else {
                        $yaxixdata[] = $object->name;
                    }
                }
                else {
                    $yaxixdata[] = $id;
                }

                $xaxisdata[] = $data[$id]['currentdata'] / 1000;
                if(!empty($data[$id]['prevmonthdata'])) {
                    $xaxisdata[] = array($data[$id]['currentdata'] / 1000, $data[$id]['prevmonthdata'] / 1000);
                }
            }
            $chart = new Charts(array('x' => $yaxixdata, 'y' => $xaxisdata), 'bar', array('yaxisname' => $lang->topten.' '.$lang->$type, 'xaxisname' => '', 'width' => '900', 'height' => 300, 'scale' => 'SCALE_START0', 'nosort' => true, 'scalepos' => SCALE_POS_TOPBOTTOM, 'noLegend' => true, 'labelrotationangle' => 45, 'x1position' => 200));
            return $chart->get_chart();
        }
    }

    public function get_quartermonths($quarter) {
        switch($quarter) {
            case 1: return array('1', '2', '3');
            case 2: return array('4', '5', '6');
            case 3: return array('7', '8', '9');
            case 4: return array('10', '11', '12');
        }
    }

    public function get_aggreateddata_byyearmonth($groupby, $filters = '') {
        if(!empty($filters)) {
            $query_where = ' WHERE '.$filters; //' AND '.$this->f_db->escape_string($filters);
        }

        if(!empty($groupby)) {
            $groupby = ', '.$this->f_db->escape_string($groupby);
        }

        $sql = "SELECT EXTRACT(YEAR FROM dateinvoiced) AS year, EXTRACT(MONTH FROM dateinvoiced) AS month, SUM(qtyinvoiced) as qty, SUM(linenetamt) AS linenetamt ".$groupby."
                        FROM c_invoiceline JOIN c_invoice  ON (c_invoice.c_invoice_id=c_invoiceline.c_invoice_id)".$query_where."
                        GROUP BY year, month ".$groupby;
        $query = $this->f_db->query($sql);
        if($this->f_db->num_rows($query) > 0) {
            while($salesdata = $this->f_db->fetch_assoc($query)) {
                $data[] = $salesdata;
            }
            $this->f_db->free_result($query);
            return $data;
        }
        return false;
    }

    public function get_totallines($where) {
        global $core;
        $sql = "SELECT SUM(totallines) AS totallines, ad_org_id, c_currency_id, date_part('month', dateinvoiced) AS month, date_part('year', dateinvoiced) AS year FROM c_invoice "
                ."WHERE issotrx='Y' AND docstatus='CO' AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime((date('Y', $TIME_NOW)).'-01-01'))."'"
                ." AND '".date('Y-m-d 23:59:59', strtotime((date('Y', $TIME_NOW)).'-12-31'))."' ".$where.") GROUP BY ad_org_id, c_currency_id, year, month";
        $chartcurrency = new Currencies('USD');
        $query = $this->f_db->query($sql);
        if($this->f_db->num_rows($query) > 0) {
            while($invoiceline = $this->f_db->fetch_assoc($query)) {
                $obcurrency_obj = new IntegrationOBCurrency($invoiceline['c_currency_id']);
                $currency_obj = new Currencies($obcurrency_obj->iso_code);

                if($chartcurrency->get_id() != $currency_obj->get_id()) {
                    $invoiceline['usdfxrate'] = $chartcurrency->get_fxrate_bytype('mavg', $currency_obj->alphaCode, array('year' => $invoiceline['year'], 'month' => $invoiceline['month']), array('precision' => 4), 'USD');
                    if(empty($invoiceline['usdfxrate'])) {
                        $invoiceline['usdfxrate'] = $chartcurrency->get_latest_fxrate($currency_obj->alphaCode, array('precision' => 4), 'USD');
                    }
                    if(empty($invoiceline['usdfxrate'])) {
                        $data[$invoiceline['ad_org_id']] = 0;
                        continue;
                    }
                    $data[$invoiceline['ad_org_id']] += $invoiceline['totallines'] / $invoiceline['usdfxrate'];
                }
                else {
                    $data[$invoiceline['ad_org_id']] += $invoiceline['totallines'];
                }
            }
            return $data;
        }
        return false;
    }

}

class IntegrationOBOrder extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'c_order_id';
    const TABLE_NAME = 'c_order';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }
        $this->read($id);
    }

    private function read($id) {
        $this->data = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM c_order
						WHERE c_order_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_paymentplan() {
        return IntegrationOBFinPaymentSchedule::get_data("c_order_id='".$this->invoice['c_order_id']."'");
    }

    public function get_id() {
        return $this->data['c_order_id'];
    }

    public function get() {
        return $this->data;
    }

    public function get_currency() {
        return new IntegrationOBCurrency($this->data['c_currency_id'], $this->f_db);
    }

    public function get_salesrep() {
        return new IntegrationOBUser($this->data['salesrep_id'], $this->f_db);
    }

    public function get_paymentterm() {
        return new IntegrationOBPaymentTerm($this->data['c_paymentterm_id'], $this->f_db);
    }

    public function get_incoterms() {
        return new IntegrationOBIncoterms($this->data['em_ork_incoterms'], $this->f_db);
    }

}

class IntegrationOBOrderLine extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'c_orderline_id';
    const TABLE_NAME = 'c_orderline';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

//    public function __construct($id, $f_db = NULL) {
//        parent::__construct($id, $f_db);
//    }

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }
        $this->read($id);
    }

    private function read($id) {
        $this->data = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM c_orderline
						WHERE c_orderline_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_inoutline() {
        return new IntegrationOBInOutLine($this->data['m_inoutline_id'], $this->f_db);
    }

    public function get_order() {
        return new IntegrationOBOrder($this->data['c_order_id'], $this->f_db);
    }

    public function get_id() {
        return $this->data['c_orderline_id'];
    }

    public function get() {
        return $this->data;
    }

    public function get_attributesetinstance() {
        return new IntegrationOBAttributeSetInstance($this->data['m_attributesetinstance_id'], $this->f_db);
    }

    public function get_packaging() {
        $filters = array('attributename' => array('id' => 'm_attribute_id', 'table' => 'm_attribute', 'attribute' => 'name', 'value' => 'Packaging'));

        $instance = $this->get_attributesetinstance()->get_attributeinstances($filters);
        if(is_array($instance)) {
            $instance = current($instance);
        }

        if(!empty($instance)) {
            return $instance->get_attributevalue($this->f_db)->get()['value'];
        }
        else {
            return false;
        }
    }

}

class IntegrationOBCostingAlgorithm {
    private $algorithm;
    private $f_db;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }
        $this->read($id);
    }

    private function read($id) {
        $this->algorithm = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_costing_algorithm
						WHERE m_costing_algorithm_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_id() {
        return $this->algorithm['m_costing_algorithm_id'];
    }

    public function get_shortname() {
        switch($this->algorithm['name']) {
            case 'FIFO Costing Algorithm':
                return 'fifo';
                break;
            case 'Average Algorithm':
                return 'average';
                break;
            case 'Standard Algorithm':
            default:
                return 'standard';
                break;
        }
    }

    public function get() {
        return $this->algorithm;
    }

}

class IntegrationOBInputStack {
    private $inputstack;
    private $f_db;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }
        $this->read($id);
    }

    private function read($id) {
        $this->inputstack = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM obwfa_input_stack
						WHERE obwfa_input_stack_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_daysinstock($relativeto = 'now') {
        if(strstr($this->inputstack['trxdate'], '.')) {
            $input_date = DateTime::createFromFormat('Y-m-d G:i:s.u', $this->inputstack['trxdate']);
        }
        else {
            $input_date = DateTime::createFromFormat('Y-m-d G:i:s', $this->inputstack['trxdate']);
        }

        $end_date = new DateTime();
        if($relativeto == 'now') {
            $end_date->setTimestamp(TIME_NOW);
        }
        else {
            $end_date->setTimestamp(strtotime($relativeto));
        }

        if($input_date === false) {
            return false;
        }

        $diff = $end_date->diff($input_date);
        $days = $diff->format('%a');

        return $days;
    }

    public function get_transcation() {
        return new IntegrationOBTransaction($this->inputstack['m_transaction_id'], $this->f_db);
    }

    public function get_product() {
        return new IntegrationOBProduct($this->inputstack['m_product_id'], $this->f_db);
    }

    public function get_locator() {
        return new IntegrationOBLocator($this->get_transcation()->get()['m_locator_id'], $this->f_db);
    }

    public function get_warehouse() {
        return new IntegrationOBWarehouse($this->get_locator()->get()['m_warehouse_id'], $this->f_db);
    }

    public function get_currency() {
        return new IntegrationOBCurrency($this->inputstack['c_currency_id'], $this->f_db);
    }

    public function get_outputstacks($filters = '') {
        if(!empty($filters)) {
            $query_where = ' AND '.$filters;
        }

        $query = $this->f_db->query("SELECT *
						FROM obwfa_output_stack
						WHERE obwfa_input_stack_id='".$this->inputstack['obwfa_input_stack_id']."'".$query_where);

        if($this->f_db->num_rows($query) > 0) {
            while($output = $this->f_db->fetch_assoc($query)) {
                $outputs[$output['obwfa_output_stack_id']] = new IntegrationOBOutputStack($output['obwfa_output_stack_id'], $this->f_db);
            }
            return $outputs;
        }
        return false;
    }

    public function get_supplier() {
        $inoutline = $this->get_transcation()->get_inoutline();
        if(!empty($inoutline)) {
            return $inoutline->get_inout()->get_bpartner();
        }
        return false;
    }

    public function get_id() {
        return $this->inputstack['obwfa_input_stack_id'];
    }

    public function get() {
        return $this->inputstack;
    }

}

class IntegrationOBOutputStack {
    private $outputstack;
    private $f_db;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }
        $this->read($id);
    }

    private function read($id) {
        $this->outputstack = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM obwfa_output_stack
						WHERE obwfa_output_stack_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_transcation() {
        return new IntegrationOBTransaction($this->outputstack['m_transaction_id'], $this->f_db);
    }

    public function get_product() {
        return new IntegrationOBProduct($this->outputstack['m_product_id'], $this->f_db);
    }

    public function get_warehouse() {
        return new IntegrationOBWarehouse($this->outputstack['m_warehouse_id'], $this->f_db);
    }

    public function get_currency() {
        return new IntegrationOBCurrency($this->outputstack['c_currency_id'], $this->f_db);
    }

    public function get_customer() {
        $inoutline = $this->get_transcation()->get_inoutline();
        if(!empty($inoutline)) {
            return $inoutline->get_inout()->get_bpartner();
        }
        return false;
    }

    public function get_inputstack() {
        return new IntegrationOBInputStack($this->outputstack['obwfa_input_stack_id'], $this->f_db);
    }

    public function get_id() {
        return $this->outputstack['obwfa_output_stack_id'];
    }

    public function get() {
        return $this->outputstack;
    }

}

class IntegrationOBCurrency extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'c_currency_id';
    const TABLE_NAME = 'c_currency';
    const DISPLAY_NAME = 'description';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

}

class IntegrationOBLandedCosts {
    private $landedcost;
    private $f_db;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }
        $this->read($id);
    }

    private function read($id) {
        $this->currency = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_landedcosts
						WHERE m_landedcosts_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_id() {
        return $this->landedcost['m_landedcosts_id'];
    }

    public function is_intercompany() {
        if($this->landedcost['isinterco']) {
            return true;
        }
        return false;
    }

    public function get() {
        return $this->landedcost;
    }

}

class IntegrationOBProduct extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'm_product_id';
    const TABLE_NAME = 'm_product';
    const DISPLAY_NAME = 'name';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

    private function read($id) {
        $this->data = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_product
						WHERE m_product_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_category() {
        return new IntegrationOBProductCategory($this->data['m_product_category_id'], $this->f_db);
    }

    public function get_uom() {
        return new IntegrationOBUom($this->data['c_uom_id'], $this->f_db);
    }

    public function get_id() {
        return $this->data['m_product_id'];
    }

    public function __get($name) {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }
        return false;
    }

    public function get() {
        return $this->data;
    }

    public function get_product_local() {
        global $db;
        $product = new Products($db->fetch_field($db->query('SELECT localId FROM integration_mediation_products WHERE foreignSystem=3 AND foreignName="'.$this->name.'"'), 'localId'));
        if(!is_object($product)) {
            $product = new Products($db->fetch_field($db->query('SELECT pid FROM products WHERE name="'.$this->name.'"'), 'pid'));
        }
        return $product;
    }

    public function get_supplier() {
        $localproduct = $this->get_product_local();
        if(is_object($localproduct) && !empty($localproduct->pid)) {
            $supplier = $localproduct->get_supplier();
        }
        if(is_object($supplier)) {
            return $supplier;
        }
        return false;
    }

}

class IntegrationOBProductCategory {
    private $productcategory;
    private $f_db;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }
        $this->read($id);
    }

    private function read($id) {
        $this->productcategory = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_product_category
						WHERE m_product_category_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_id() {
        return $this->productcategory['m_product_category_id'];
    }

    public function __get($attr) {
        if(isset($this->productcategory[$attr])) {
            return $this->productcategory[$attr];
        }
        return false;
    }

    public function __isset($name) {
        return isset($this->productcategory[$name]);
    }

    public function get() {
        return $this->productcategory;
    }

}

class IntegrationOBLocator extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'm_locator_id';
    const TABLE_NAME = 'm_locator';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }
        $this->read($id);
    }

    private function read($id) {
        $this->data = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_locator
						WHERE m_locator_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_warehouse() {
        return new IntegrationOBWarehouse($this->m_warehouse_id, $this->f_db);
    }

    public function get_id() {
        return $this->data['m_locator_id'];
    }

    public function get() {
        return $this->data;
    }

}

class IntegrationOBWarehouse extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'm_warehouse_id';
    const TABLE_NAME = 'm_warehouse';
    const DISPLAY_NAME = 'name';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }
        $this->read($id);
    }

    private function read($id) {
        $this->data = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_warehouse
						WHERE m_warehouse_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_id() {
        return $this->data['m_warehouse_id'];
    }

    public function get() {
        return $this->data;
    }

    public function get_location() {
        return new IntegrationOBLocation($this->data['c_location_id'], $this->f_db);
    }

}

class IntegrationOBBPartner extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'c_bpartner_id';
    const TABLE_NAME = 'c_bpartner';
    const DISPLAY_NAME = 'name';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

    private function read($id) {
        $this->data = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM c_bpartner
						WHERE c_bpartner_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_id() {
        return $this->data['c_bpartner_id'];
    }

    public function get_bp_local() {
        return IntegrationMediationEntities::get_entity_byattr('foreignId', $this->data['c_bpartner_id'])->get_localentity();
    }

    public function __get($name) {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }
        return false;
    }

    public function get() {
        return $this->data;
    }

}

class IntegrationOBAttributeSetInstance {
    private $setinstance;
    private $f_db;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }
        $this->read($id);
    }

    private function read($id) {
        $this->setinstance = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_attributesetinstance
						WHERE m_attributesetinstance_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_daystoexpire() {
        if(!empty($this->setinstance['guaranteedate'])) {
            if(strstr($this->setinstance['guaranteedate'], '.')) {
                $expiry_date = DateTime::createFromFormat('Y-m-d G:i:s.u', $this->setinstance['guaranteedate']);
            }
            else {
                $expiry_date = DateTime::createFromFormat('Y-m-d G:i:s', $this->setinstance['guaranteedate']);
            }

            if($expiry_date === false) {
                return false;
            }

            $end_date = new DateTime();
            $end_date->setTimestamp(TIME_NOW);

            $diff = $end_date->diff($expiry_date);
            $days = $diff->format('%r%a');

            return $days;
        }
        return false;
    }

    public function get_attributeinstances($filters = array()) {
        if(!empty($filters) && is_array($filters)) {
            foreach($filters as $filter) {
                $extra_where .= ' AND '.$filter['id'].' IN (SELECT '.$filter['id'].' FROM '.$filter['table'].' WHERE '.$filter['attribute'].'=\''.$filter['value'].'\')';
            }
        }
        $query = $this->f_db->query("SELECT m_attributeinstance_id
						FROM m_attributeinstance
						WHERE m_attributesetinstance_id='".$this->setinstance['m_attributesetinstance_id']."'".$extra_where);
        if($this->f_db->num_rows($query) > 0) {
            while($instance = $this->f_db->fetch_assoc($query)) {
                $instances[$instance['m_attributeinstance_id']] = new IntegrationOBAttributeInstance($instance['m_attributeinstance_id'], $this->f_db);
            }
            return $instances;
        }
        return false;
    }

    public function get_id() {
        return $this->setinstance['m_attributesetinstance_id'];
    }

    public function get() {
        return $this->setinstance;
    }

}

class IntegrationOBAttributeInstance {
    private $instance;
    private $f_db;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }
        $this->read($id);
    }

    private function read($id) {
        $this->instance = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_attributeinstance
						WHERE m_attributeinstance_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_attribute() {
        return new IntegrationOBAttribute($this->instance['m_attribute_id'], $this->f_db);
    }

    public function get_attributevalue($f_db = null) {
        if(is_null($f_db)) {
            $f_db = $this->f_db;
        }
        return new IntegrationOBAttributeValue($this->instance['m_attributevalue_id'], $f_db);
    }

    public function get_id() {
        return $this->instance['m_attributeinstance_id'];
    }

    public function get() {
        return $this->instance;
    }

}

class IntegrationOBAttribute {
    private $attribute;
    private $f_db;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }
        $this->read($id);
    }

    private function read($id) {
        $this->attribute = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_attribute
						WHERE m_attribute_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_id() {
        return $this->attribute['m_attribute_id'];
    }

    public function get() {
        return $this->attribute;
    }

}

class IntegrationOBAttributeValue {
    private $attributevalue;
    private $f_db;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }
        $this->read($id);
    }

    private function read($id) {
        $this->attributevalue = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_attributevalue
						WHERE m_attributevalue_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_id() {
        return $this->attributevalue['m_attributevalue_id'];
    }

    public function get() {
        return $this->attributevalue;
    }

}

class IntegrationOBUom extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'c_uom_id';
    const TABLE_NAME = 'c_uom';
    const DISPLAY_NAME = 'name';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

//    public function __construct($id, $f_db = NULL) {
//        if(!empty($f_db)) {
//            $this->f_db = $f_db;
//        }
//        else {
////Open connections
//        }
//        $this->read($id);
//    }

    private function read($id) {
        $this->data = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM c_uom
						WHERE c_uom_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_id() {
        return $this->data['c_uom_id'];
    }

    public function __get($name) {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }
        return false;
    }

    public function get() {
        return $this->data;
    }

}

class IntegrationCostingRule {
    private $rule;
    private $f_db;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }
        $this->read($id);
    }

    private function read($id) {
        $this->rule = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_costing_rule
						WHERE m_costing_rule_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_costingalgorithm() {
        return new IntegrationCostingAlgorithm($this->rule['m_costing_algorithm_id'], $this->f_db);
    }

    public function get_id() {
        return $this->rule['m_costing_rule_id'];
    }

    public function get() {
        return $this->rule;
    }

}

class IntegrationCostingAlgorithm {
    private $algorithm;
    private $f_db;

    public function __construct($id, $f_db = NULL) {
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
//Open connections
        }
        $this->read($id);
    }

    private function read($id) {
        $this->algorithm = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_costing_algorithm
						WHERE m_costing_algorithm_id='".$this->f_db->escape_string($id)."'"));
    }

    public function get_id() {
        return $this->algorithm['m_costing_algorithm_id'];
    }

    public function get() {
        return $this->algorithm;
    }

}

class IntegrationOBUser extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'ad_user_id';
    const TABLE_NAME = 'ad_user';
    const DISPLAY_NAME = 'name';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

    private function read($id) {
        $this->data = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM ".self::TABLE_NAME."
						WHERE ".self::PRIMARY_KEY."='".$this->f_db->escape_string($id)."'"));
    }

    public function get_id() {
        return $this->data[self::PRIMARY_KEY];
    }

    public function __get($name) {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }
        return false;
    }

    public function __isset($name) {
        return isset($this->data[$name]);
    }

    public function get() {
        return $this->data;
    }

}

class IntegrationOBPaymentTerm extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'c_paymentterm_id';
    const TABLE_NAME = 'c_paymentterm';
    const DISPLAY_NAME = 'name';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

    private function read($id) {
        $this->data = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM ".self::TABLE_NAME."
						WHERE ".self::PRIMARY_KEY."='".$this->f_db->escape_string($id)."'"));
    }

    public function get_id() {
        return $this->data[self::PRIMARY_KEY];
    }

    public function __get($name) {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }
        return false;
    }

    public function get() {
        return $this->data;
    }

}

class IntegrationOBFinPaymentSchedule extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'fin_payment_schedule_id';
    const TABLE_NAME = 'fin_payment_schedule';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

    public function get_invoice() {
        return new IntegrationOBInvoice($this->data['c_invoice_id'], $this->f_db);
    }

    public function get_order() {
        return new IntegrationOBOrder($this->data['c_order_id'], $this->f_db);
    }

    public function get_currency() {
        return new IntegrationOBCurrency($this->data['c_currency_id'], $this->f_db);
    }

    public function get_paymentmethod() {
        return new IntegrationOBFinPaymentMethod($this->data['fin_paymentmethod_id'], $this->f_db);
    }

    public function get_plandetails() {
        return IntegrationOBFinPaymentScheduleDetail::get_data("fin_payment_schedule_invoice='".$this->data[self::PRIMARY_KEY]."' OR fin_payment_schedule_order='".$this->data[self::PRIMARY_KEY]."'", array('returnarray' => true));
    }

}

class IntegrationOBFinPaymentScheduleDetail extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'fin_payment_scheduledetail_id';
    const TABLE_NAME = 'fin_payment_scheduledetail';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

    public function get_paymentschedule_invoice() {
        return new IntegrationOBFinPaymentSchedule($this->data['fin_payment_schedule_invoice'], $this->f_db);
    }

    public function get_paymentschedule_order() {
        return new IntegrationOBFinPaymentSchedule($this->data['fin_payment_schedule_order'], $this->f_db);
    }

    public function get_paymentdetail() {
        return new IntegrationOBFinPaymentDetail($this->data['fin_payment_detail_id'], $this->f_db);
    }

    public function get_businesspartner() {
        return new IntegrationOBBPartner($this->data['c_bpartner_id'], $this->f_db);
    }

}

class IntegrationOBFinPaymentMethod extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'fin_paymentmethod_id';
    const TABLE_NAME = 'fin_paymentmethod';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

}

class IntegrationOBFinPayment extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'fin_payment_id';
    const TABLE_NAME = 'fin_payment';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

}

class IntegrationOBFinPaymentDetail extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'fin_payment_detail_id';
    const TABLE_NAME = 'fin_payment_detail';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

    public function get_payment() {
        return new IntegrationOBFinPayment($this->data['fin_payment_id'], $this->f_db);
    }

}

class IntegrationOBOrg extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'ad_org_id';
    const TABLE_NAME = 'ad_org';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

    public function get_currency() {
        return new IntegrationOBCurrency($this->data['c_currency_id'], $this->f_db);
    }

}

class IntegrationOBOrgInfo extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'ad_org_id';
    const TABLE_NAME = 'ad_orginfo';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

    public function get_bp() {
        return new IntegrationOBBPartner($this->data['c_bpartner_id'], $this->f_db);
    }

}

class IntegrationOBBusinessPartnerLocation extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'c_bpartner_location_id';
    const TABLE_NAME = 'c_bpartner_location';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

    public function get_location() {
        return new IntegrationOBLocation($this->data['c_location_id'], $this->f_db);
    }

}

class IntegrationOBLocation extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'c_location_id';
    const TABLE_NAME = 'c_location';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

    public function get_country() {
        return new IntegrationOBCountry($this->data['c_country_id'], $this->f_db);
    }

}

class IntegrationOBCountry extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'c_country_id';
    const TABLE_NAME = 'c_country';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

}

class IntegrationOBIncoterms extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'c_incoterms_id';
    const TABLE_NAME = 'c_incoterms';
    const DISPLAY_NAME = 'name';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

}

class IntegrationOBIAttachments extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'c_file_id';
    const TABLE_NAME = 'c_file';
    const DISPLAY_NAME = 'name';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

}

class IntegrationOBBPCustAcct extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'c_bp_customer_acct_id';
    const TABLE_NAME = 'c_bp_customer_acct';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

}

class IntegrationOBValidCombination extends IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = 'c_validcombination_id';
    const TABLE_NAME = 'c_validcombination';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        parent::__construct($id, $f_db);
    }

}
?>
