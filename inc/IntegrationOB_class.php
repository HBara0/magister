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
		else
		{
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
			else
			{
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
			else
			{
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
				'foreignSystem' 	=> $this->foreign_system,
				'foreignId' 		=> $document['doc_id'],
				'docNum' 		   => $document['documentno'], 
				'date'			 => strtotime($document['doc_date']),
				'cid'			  => $document['bpid'],
				'affid'			=> $this->affiliates_index[$document['ad_org_id']],
				'currency' 		 => $document['currency'],
				'paymentTerms'	 => $document['paymenttermsdays'],
				'salesRep'		 => $document['salesrep']
			);
			
			$document_newdata['salesRepLocalId'] = $db->fetch_field($db->query("SELECT uid FROM ".Tprefix."users WHERE username='".$db->escape_string($document['username'])."'"), 'uid');

			if(value_exists('integration_mediation_salesorders', 'foreignId', $document['doc_id'])) {
				$query2 = $db->update_query('integration_mediation_salesorders', $document_newdata, 'foreignId="'.$document['doc_id'].'"');
			}
			else
			{
				$query2 = $db->insert_query('integration_mediation_salesorders', $document_newdata);
			}
			
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
				else
				{
					$documentline_query	= $this->f_db->query('SELECT il.*, il.c_invoiceline_id AS docline_id, il.qtyinvoiced AS quantity, ppo.c_bpartner_id, u.x12de355 AS uom, tr.transactioncost AS cost, c.iso_code AS costcurrency
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
						$documentline_newdata = array(
							'foreignId' 		=> $documentline['docline_id'], 
							'foreignOrderId'   => $document['doc_id'],
							'pid'				=> $documentline['m_product_id'],
							'spid'			=> $documentline['c_bpartner_id'],
							'affid'		=> $this->affiliates_index[$document['ad_org_id']],
							'price'		=> $documentline['priceactual'],
							'quantity'	=> $documentline['quantity'],
							'quantityUnit' => $documentline['uom'],
							'cost'		=> $documentline['cost'],
							'costCurrency' => $documentline['costcurrency']
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
		else
		{
			$query = $this->f_db->query("SELECT i.c_invoice_id AS documentid, i.ad_org_id, i.documentno, bp.name AS bpname, bp.c_bpartner_id AS bpid, c.iso_code AS currency, dateinvoiced AS documentdate, pt.netdays AS paymenttermsdays
							FROM c_invoice i JOIN c_bpartner bp ON (bp.c_bpartner_id=i.c_bpartner_id)
							JOIN c_currency c ON (c.c_currency_id=i.c_currency_id)
							JOIN c_paymentterm pt ON (i.c_paymentterm_id=pt.c_paymentterm_id)
							WHERE  i.ad_org_id IN ('".implode('\',\'', $organisations)."') AND docstatus NOT IN ('VO', 'CL') AND issotrx='N' AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime($this->period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($this->period['to']))."')");
		}
		
		$document_newdata = array(0);
		while($document = $this->f_db->fetch_assoc($query)) {
			$document_newdata = array(
				'foreignSystem' 	=> $this->foreign_system,
				'foreignId' 		=> $document['documentid'],
				'docNum' 		   => $document['documentno'], 
				'date'			 => strtotime($document['documentdate']),
				'spid'			  => $document['bpid'],
				'affid'			=> $this->affiliates_index[$document['ad_org_id']],
				'currency' 		 => $document['currency'],
				'paymentTerms'	 => $document['paymenttermsdays'],
				'purchaseType'	=> 'SKI'
			);
			
			/* Get currencies FX from own system - START */
			$document_newdata['usdFxrate'] = $currency_obj->get_average_fxrate($document['currency'], array('from' => strtotime(date('Y-m-d', $newdata['date']).' 01:00'), 'to' => strtotime(date('Y-m-d', $newdata['date']).' 24:00')));
			if(empty($newdata['usdFxrate'])) {
				$document_newdata['usdFxrate'] = $currency_obj->get_average_fxrate($document['currency'], array('from' => strtotime(date('Y-m-d', $newdata['date']).' 01:00')-(24*60*60*7), 'to' => strtotime(date('Y-m-d', $newdata['date']).' 24:00')));
			}
			/* Get currencies FX from own system - END */
			
			if(value_exists('integration_mediation_purchaseorders', 'foreignId', $document['documentid'])) {
				$query2 = $db->update_query('integration_mediation_purchaseorders', $document_newdata, 'foreignId="'.$document['documentid'].'"');
			}
			else
			{
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
				else
				{
					$documentline_query = $this->f_db->query('SELECT il.*, c_invoiceline_id AS documentlineid, il.qtyinvoiced AS quantity, p.name AS productnamem, u.x12de355 AS uom
												FROM c_invoiceline il JOIN m_product p ON (p.m_product_id=il.m_product_id)
												JOIN c_uom u ON (u.c_uom_id=p.c_uom_id)
												WHERE c_invoice_id=\''.$document['documentid'].'\'');
				}

				$documentline_newdata = array();
				while($documentline = $this->f_db->fetch_assoc($documentline_query)) {
					$documentline_newdata = array(
						'foreignId' 		=> $documentline['documentlineid'], 
						'foreignOrderId'   => $document['documentid'],
						'pid'				=> $documentline['m_product_id'],
						'spid'			=> $document['bpid'],
						'affid'		=> $this->affiliates_index[$document['ad_org_id']],
						'price'		=> $documentline['priceactual'],
						'quantity'	=> $documentline['quantity'],
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
		else
		{
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
	
	private function set_client_id($id) {
		$this->client = $this->f_db->escape_string($id);
	}
	
	public function get_status() {
		return $this->status;
	}
}
?>
