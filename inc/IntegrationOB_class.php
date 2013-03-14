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

	public function __construct(array $database_info, $client_id, $affiliates_ids, $foreign_system = 3, array $sync_period = array()) {
		if(parent::__construct($foreign_system, $database_info)) {
			parent::set_sync_interval($sync_period);
			parent::match_affiliates_ids($affiliates_ids);
			$this->set_client_id($client_id);
		}
		else {
			$this->status = 701;
			return false;
		}
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
			//print_r($product);
			//echo '<hr />';
			$newdata = array(
					'foreignSystem' => $this->foreign_system,
					'foreignId' => $product['m_product_id'],
					'foreignName' => $product['name'],
					'foreignNameAbbr' => $product['value'],
					'affid' => $this->affiliates_index[$product['ad_org_id']]
			);

			$newdata['localId'] = $db->fetch_field($db->query("SELECT pid FROM ".Tprefix."products WHERE name='".$db->escape_string($product['name'])."'"), 'pid');

			if(value_exists('integration_mediation_products', 'foreignId', $product['m_product_id'])) {
				$db->update_query('integration_mediation_products', $newdata, 'foreignId="'.$product['m_product_id'].'"');
			}
			else {
				$db->insert_query('integration_mediation_products', $newdata);
			}

			$items_count++;
		}
		$log->record($items_count);
		return true;
	}

	public function sync_businesspartners() {
		global $db, $log;

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
		$log->record($items_count);
		return true;
	}

	public function sync_sales(array $organisations, array $exclude = array(), $doc_type = 'invoice') {
		global $db, $log;

		if(!is_array($organisations) || empty($organisations)) {
			return false;
		}

		if($doc_type == 'order') {
			return false;
//			$query = $this->f_db->query("SELECT o.ad_org_id, o.c_order_id, o.dateordered, o.documentNo, bp.name AS bpname, bp.c_bpartner_id AS bpid, bp.value AS bpname_abv, c.iso_code AS currency, o.salesrep_id, u.username, u.name AS salesrep, pt.netdays AS paymenttermsdays
//						FROM c_order o JOIN c_bpartner bp ON (bp.c_bpartner_id=o.c_bpartner_id) 
//						JOIN c_currency c ON (c.c_currency_id=o.c_currency_id)
//						JOIN ad_user u ON (u.ad_user_id=o.salesrep_id)
//						JOIN c_paymentterm pt ON (o.c_paymentterm_id=pt.c_paymentterm_id)
//						WHERE o.ad_org_id IN ('".implode('\',\'', $organisations)."') AND docstatus NOT IN ('VO', 'CL') AND issotrx='Y' AND (dateordered BETWEEN '".date('Y-m-d 00:00:00', strtotime($this->period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($this->period['to']))."')
//						ORDER by dateordered ASC");
		}
		elseif($doc_type == 'invoice') {
			$query = $this->f_db->query("SELECT i.ad_org_id, i.c_invoice_id AS doc_id, i.dateinvoiced AS doc_date, i.documentno, bp.name AS bpname, bp.c_bpartner_id AS bpid, bp.value AS bpname_abv, c.iso_code AS currency, i.salesrep_id, u.username, u.name AS salesrep, pt.netdays AS paymenttermsdays
					FROM c_invoice i JOIN c_bpartner bp ON (bp.c_bpartner_id=i.c_bpartner_id)
					JOIN c_currency c ON (c.c_currency_id=i.c_currency_id)
					JOIN ad_user u ON (u.ad_user_id=i.salesrep_id)
					JOIN c_paymentterm pt ON (i.c_paymentterm_id=pt.c_paymentterm_id)
					WHERE i.ad_org_id IN ('".implode('","', $organisations)."') AND docstatus NOT IN ('VO', 'CL') AND issotrx='Y' AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime($this->period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($this->period['to']))."')
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

			$document_newdata['salesRepLocalId'] = $db->fetch_field($db->query("SELECT uid FROM ".Tprefix."users WHERE username='".$db->escape_string($document['username'])."'"), 'uid');

			if(value_exists('integration_mediation_salesorders', 'foreignId', $document['doc_id'])) {
				$query2 = $db->update_query('integration_mediation_salesorders', $document_newdata, 'foreignId="'.$document['doc_id'].'"');
			}
			else {
				$query2 = $db->insert_query('integration_mediation_salesorders', $document_newdata);
			}
			$query2 = true;
			if($query2) {
				if(value_exists('integration_mediation_salesorderlines', 'foreignOrderId', $document['doc_id'])) {
					$db->delete_query('integration_mediation_salesorderlines', 'foreignOrderId="'.$document['doc_id'].'"');
				}

				if($doc_type == 'order') {
//					$documentline_query = $this->f_db->query("SELECT ol.*, ct.cost, ppo.c_bpartner_id, u.x12de355 AS uom, c.iso_code AS costcurrency 
//							FROM c_orderline ol 
//							JOIN m_product p ON (p.m_product_id=ol.m_product_id) 
//							JOIN c_uom u ON (u.c_uom_id=ol.c_uom_id)
//							LEFT JOIN m_costing ct ON (ct.m_product_id=p.m_product_id)
//							LEFT JOIN c_currency c ON (c.c_currency_id=ct.c_currency_id)
//							LEFT JOIN m_product_po ppo ON (p.m_product_id=ppo.m_product_id)
//							LEFT JOIN c_bpartner bp ON (bp.c_bpartner_id=ppo.c_bpartner_id) 
//							WHERE c_order_id='{$document[c_order_id]}' AND ('".$document['dateordered']."' BETWEEN ct.datefrom AND ct.dateto) AND ol.m_product_id NOT IN ('".implode('\',\'', $exclude['products'])."')");
				}
				else {
					$documentline_query = $this->f_db->query('SELECT m_transaction_id, il.*, il.c_invoiceline_id AS docline_id, il.qtyinvoiced AS quantity, ppo.c_bpartner_id, u.x12de355 AS uom, tr.transactioncost AS cost, c.iso_code AS costcurrency, m_costing_algorithm_id
													FROM c_invoiceline il JOIN m_product p ON (p.m_product_id=il.m_product_id)
													JOIN c_uom u ON (u.c_uom_id=il.c_uom_id)
													JOIN m_inoutline iol ON (iol.m_inoutline_id=il.m_inoutline_id)
													JOIN m_transaction tr ON (iol.m_inoutline_id=tr.m_inoutline_id)
													JOIN c_currency c ON (c.c_currency_id=tr.c_currency_id)
													LEFT JOIN m_product_po ppo ON (p.m_product_id=ppo.m_product_id)
													WHERE c_invoice_id=\''.$document['doc_id'].'\' AND iscostcalculated=\'Y\' AND il.m_product_id NOT IN (\''.implode('\',\'', $exclude['products']).'\')
												');
				}

				$documentline_newdata = array();
				while($documentline = $this->f_db->fetch_assoc($documentline_query)) {
					$purchaseprice_data = $this->get_purchaseprice($documentline['m_transaction_id'], $documentline['m_costing_algorithm_id'], 'sale');

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
		$log->record();

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
		$log->record();
		return true;
	}

	public function sync_purchases(array $organisations, array $exclude = array(), $doc_type = 'invoice') {
		global $db, $log;

		if(!is_array($organisations) || empty($organisations)) {
			return false;
		}

		$currency_obj = new Currencies('USD');

		if($doc_type == 'order') {
			return false;
//			$query = $this->f_db->query("SELECT o.c_order_id AS documentid, o.ad_org_id, o.dateordered AS documentdate, bp.name AS bpname, bp.c_bpartner_id, c.iso_code AS currency
//							FROM c_order o JOIN c_bpartner bp ON (bp.c_bpartner_id=o.c_bpartner_id) 
//							JOIN c_currency c ON (c.c_currency_id=o.c_currency_id)
//							WHERE o.ad_org_id IN ('".implode('","', $organisations)."') AND issotrx='N' AND docstatus = 'CO' AND ((dateordered BETWEEN '".date('Y-m-d 00:00:00', strtotime($this->period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($this->period['to']))."') OR (o.updated BETWEEN '".date('Y-m-d 00:00:00', strtotime($this->period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($this->period['to']))."'))");}
		}
		else {
			$query = $this->f_db->query("SELECT i.c_invoice_id AS documentid, i.ad_org_id, i.documentno, bp.name AS bpname, bp.c_bpartner_id AS bpid, c.iso_code AS currency, dateinvoiced AS documentdate, pt.netdays AS paymenttermsdays
							FROM c_invoice i JOIN c_bpartner bp ON (bp.c_bpartner_id=i.c_bpartner_id)
							JOIN c_currency c ON (c.c_currency_id=i.c_currency_id)
							JOIN c_paymentterm pt ON (i.c_paymentterm_id=pt.c_paymentterm_id)
							WHERE  i.ad_org_id IN ('".implode('\',\'', $organisations)."') AND docstatus NOT IN ('VO', 'CL') AND issotrx='N' AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime($this->period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($this->period['to']))."')");
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
					//				$documentline_query = $this->f_db->query("SELECT ol.*, c_orderline_id AS documentlineid, ol.qtyordered AS quantity, p.name AS productname, u.x12de355 AS uom
					//									FROM c_orderline ol JOIN m_product p ON (p.m_product_id=ol.m_product_id) 
					//									JOIN c_uom u ON (u.c_uom_id=p.c_uom_id) 
					//									WHERE c_order_id='{$document[documentid]}'");

					return false;
				}
				else {
					$documentline_query = $this->f_db->query('SELECT il.*, c_invoiceline_id AS documentlineid, il.qtyinvoiced AS quantity, p.name AS productnamem, u.x12de355 AS uom
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
							'quantity' => $documentline['quantity'],
							'quantityUnit' => $documentline['uom']
					);

					$db->insert_query('integration_mediation_purchaseorderlines', $documentline_newdata);
				}
			}
		}
		$log->record();
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
		$log->record();
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

	public function get_status() {
		return $this->status;
	}

}

class IntegrationOBTransaction {
	private $transaction;
	private $f_db;

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

	public function get() {
		return $this->transaction;
	}

}

class IntegrationOBInOutLine {
	private $inoutline;
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
		$this->inoutline = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM m_inoutline 
						WHERE m_inoutline_id='".$this->f_db->escape_string($id)."'"));
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

	public function get_id() {
		return $this->inoutline['m_inoutline_id'];
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

	public function get_id() {
		return $this->inout['m_inout_id'];
	}

	public function get() {
		return $this->inout;
	}

}

class IntegrationOBInvoice {
	private $invoice;
	private $f_db;

	public function __construct($id, $f_db = NULL) {
		if(empty($id)) {
			return false;
		}

		if(!empty($f_db)) {
			$this->f_db = $f_db;
		}
		else {
			//Open connections
		}
		$this->read($id);
	}

	private function read($id) {
		$this->invoice = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM c_invoice
						WHERE c_invoice_id='".$this->f_db->escape_string($id)."'"));
	}

	public function get_currency() {
		return new IntegrationOBCurrency($this->invoice['c_currency_id'], $this->f_db);
	}

	public function get_id() {
		return $this->invoice['c_invoice_id'];
	}

	public function get() {
		return $this->invoice;
	}

}

class IntegrationOBInvoiceLine {
	private $invoiceline;
	private $f_db;

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
		$this->invoiceline = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM c_invoiceline
						WHERE c_invoiceline_id='".$this->f_db->escape_string($id)."'"));
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
		return new IntegrationOBInOutLine($this->invoiceline['m_inoutline_id'], $this->f_db);
	}

	public function get_invoice() {
		return new IntegrationOBInvoice($this->invoiceline['c_invoice_id'], $this->f_db);
	}

	public function get_id() {
		return $this->invoiceline['c_invoiceline_id'];
	}

	public function get() {
		return $this->invoiceline;
	}

}

class IntegrationOBOrder {
	private $order;
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
		$this->order = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM c_order
						WHERE c_order_id='".$this->f_db->escape_string($id)."'"));
	}

	public function get_currency() {
		return new IntegrationOBCurrency($this->order['c_currency_id'], $this->f_db);
	}

	public function get_id() {
		return $this->order['c_order_id'];
	}

	public function get() {
		return $this->order;
	}

}

class IntegrationOBOrderLine {
	private $orderline;
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
		$this->orderline = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM c_orderline
						WHERE c_orderline_id='".$this->f_db->escape_string($id)."'"));
	}

	public function get_inoutline() {
		return new IntegrationOBInOutLine($this->orderline['m_inoutline_id'], $this->f_db);
	}

	public function get_order() {
		return new IntegrationOBOrder($this->orderline['c_order_id'], $this->f_db);
	}

	public function get_id() {
		return $this->orderline['c_orderline_id'];
	}

	public function get() {
		return $this->orderline;
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

	public function get_id() {
		return $this->inputstack['obwfa_input_stack_id'];
	}

	public function get() {
		return $this->inputstack;
	}

}

class IntegrationOBCurrency {
	private $currency;
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
						FROM c_currency
						WHERE c_currency_id='".$this->f_db->escape_string($id)."'"));
	}

	public function get_id() {
		return $this->currency['c_currency_id'];
	}

	public function get() {
		return $this->currency;
	}

}
?>
