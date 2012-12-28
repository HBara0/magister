<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Sourcing Class
 * $id: Sourcing_class.php
 * Created:			@tony.assaad	October 15, 2012 |  10:53 PM
 * Last Update:     @tony.assaad	December 26 2012 | 4:06 PM
 */

class Sourcing {
	protected $status = 0;
	private $supplier = array();

	public function __construct($id = '', $simple = false) {
		if(isset($id) && !empty($id)) {
			$this->supplier = $this->read($id, $simple);
		}
	}

	public function add($data, array $options = array()) {
		global $db, $log, $core, $errorhandler, $lang;
		if(is_empty($data['companyName'], $data['productsegment']) || (empty($data['ssid']) && $options['operationtype'] == 'update')) {
			$this->status = 1;
			return false;
		}

		$this->supplier = $data;
		$this->chemicals = $this->supplier['chemicalproducts'];
		$this->productsegments = $this->supplier['productsegment'];
		$this->representative = $this->supplier['representative'];
		$this->activityarea = $this->supplier['activityarea'];
		$this->supplier['ssid'] = intval($this->supplier['ssid']);
		$this->supplier['type'] = $this->determine_supplyiertype($this->chemicals);

		unset($this->supplier['chemicalproducts'], $this->supplier['productsegment'], $this->supplier['representative'], $this->supplier['activityarea']);
		/* If action is edit, don't check if supplier already exists */
		if($options['operationtype'] != 'update') {
			if(value_exists('sourcing_suppliers', 'companyName', $this->supplier['companyName'])) {
				$this->status = 2;
				return false;
			}
		}

		/* Santize inputs - START */
		$sanitize_fields = array('companyName', 'companyNameAbbr', 'country', 'city', 'addressLine1', 'addressLine2', 'building', 'postCode', 'poBox', 'commentsToShare', 'marketingRecords', 'coBriefing', 'historical', 'sourcingRecords', 'productFunction');
		foreach($sanitize_fields as $val) {
			$this->supplier[$val] = $core->sanitize_inputs($this->supplier[$val], array('removetags' => true));
		}

		$this->supplier['mainEmail'] = $core->validate_email($core->sanitize_email($this->supplier['mainEmail']));
		$this->supplier['website'] = $core->validtate_URL($this->supplier['website']);
		/* Santize inputs - END  */

		if(!is_empty($this->supplier['phone1']['intcode'], $this->supplier['phone1']['areacode'], $this->supplier['phone1']['number'])) {
			$this->supplier['phone1'] = implode('-', $this->supplier['phone1']);
		}
		else {
			unset($this->supplier['phone1']);
		}

		if(!is_empty($this->supplier['phone2']['intcode'], $this->supplier['phone2']['areacode'], $this->supplier['phone2']['number'])) {
			$this->supplier['phone2'] = implode('-', $this->supplier['phone2']);
		}
		else {
			unset($this->supplier['phone2']);
		}

		if(!is_empty($this->supplier['fax']['intcode'], $this->supplier['fax']['areacode'], $this->supplier['fax']['number'])) {
			$this->supplier['fax'] = implode('-', $this->supplier['fax']);
		}
		else {
			unset($this->supplier['fax']);
		}

		if($options['operationtype'] == 'update') {
			$this->supplier['dateModified'] = TIME_NOW;
			$this->supplier['modifiedBy'] = $core->user['uid'];
			$query = $db->update_query('sourcing_suppliers', $this->supplier, 'ssid='.$this->supplier['ssid'].'');
		}
		else {
			unset($this->supplier['ssid']);
			$this->supplier['createdBy'] = $core->user['uid'];
			$this->supplier['dateCreated'] = TIME_NOW;
			$query = $db->insert_query('sourcing_suppliers', $this->supplier);
			$this->supplier['ssid'] = $db->last_id();
		}

		/* Insert/Update Related Tables - START */
		if($query) {
			$this->status = 0;

			if($options['operationtype'] == 'update') {
				$db->delete_query('sourcing_suppliers_activityareas', 'ssid='.$this->supplier['ssid']);
			}

			/* Insert  suppliers_activityareas - START */
			if(is_array($this->activityarea)) {
				foreach($this->activityarea as $coid => $activityarea) { /* If supplier not available in the country move to the next availablity country */
					if(empty($activityarea['availability'])) {
						continue;
					}
					if(isset($activityarea['availability']) && !empty($activityarea['availability'])) {
						$activity_area = array(
								'ssid' => $this->supplier['ssid'],
								'coid' => $activityarea['coid'],
								'availability' => $activityarea['availability']
						);

						$db->insert_query('sourcing_suppliers_activityareas', $activity_area);
					}
				}
			}
			/* Insert suppliers_activityareas - END */

			if($options['operationtype'] == 'update') {
				$db->delete_query('sourcing_suppliers_productsegments', 'ssid='.$this->supplier['ssid']);
			}

			/* Insert suppliers_productsegments - START */
			if(is_array($this->productsegments)) {
				foreach($this->productsegments as $productsegment) {
					$suppliers_productsegments = array(
							'ssid' => $this->supplier['ssid'],
							'psid' => $productsegment,
					);
					$db->insert_query('sourcing_suppliers_productsegments', $suppliers_productsegments);
				}
			}
			/* Insert suppliers_productsegments - END */


			if($options['operationtype'] == 'update') {
				$db->delete_query('sourcing_suppliers_contactpersons', 'ssid='.$this->supplier['ssid']);
			}

			if(is_array($this->representative)) {
				foreach($this->representative as $key => $representative) {
					$suppliers_contactpersons = array(
							'ssid' => $this->supplier['ssid'],
							'rpid' => $representative['id'],
							'notes' => $representative['notes']
					);
					$querycontactpersons = $db->insert_query('sourcing_suppliers_contactpersons', $suppliers_contactpersons);
				}
			}

			if($options['operationtype'] == 'update') {
				$db->delete_query('sourcing_suppliers_chemicals', 'ssid='.$this->supplier['ssid']);
			}

			if(is_array($this->chemicals)) {
				foreach($this->chemicals as $chemical) {
					$new_chemicals = array(
							'ssid' => $this->supplier['ssid'],
							'csid' => $chemical['csid'],
							'supplyType' => $chemical['supplyType']
					);
					$db->insert_query('sourcing_suppliers_chemicals', $new_chemicals);
				}
			}
			$log->record($this->supplier['ssid']);
			return true;
		}
		/* Insert/Update Related Tables - END */
		$this->status = 4;
		return false;
	}

	public function contact_supplier($supplier_id = '') {
		global $core, $db;

		if(empty($supplier_id)) {
			$supplier_id = $this->supplier['ssid'];
		}
		else {
			if(!$this->validate_permission($supplier_id)) {
				return false;
			}
		}
		$db->insert_query('sourcing_suppliers_contacthist', array('ssid' => $supplier_id, 'uid' => $core->user['uid']));
	}

	public function save_communication_report($data, $supplier_id = '', $identifier = '', $options = array()) {
		global $core, $db;
		$data['isCompleted'] = 0;
		if(is_empty($data['chemical'], $data['affid'])) {
			$this->status = 1;
			return false;
		}

		if(empty($supplier_id)) {
			$supplier_id = $this->supplier['ssid'];
		}
		if(empty($identifier)) {
			$identifier = $identifier;
		}

		if($options['operationtype'] == 'add' && (value_exists("sourcing_suppliers_contacthist", "isCompleted", 1, "identifier='".$identifier."'"))) {
			$this->status = 6;
			return false;
		}

		/* if(!value_exists('sourcing_suppliers_contacthist', 'ssid', $supplier_id, 'uid='.$core->user['uid'])) {
		  return false;
		  } */

		/* Check whether the communications section has  been validated  completed */
		if(isset($data['commercialoffer']) && !empty($data['commercialoffer']) || (isset($data['sourcingnotPossibleDesc']) && !empty($data['sourcingnotPossibleDesc']))) {
			$data['isCompleted'] = 1;
		}
		$this->orderpassed = $data['orderpassed'];
		unset($data['orderpassed'], $data['commercialoffer']);

		$this->communication_entriesexist = 'false';
		$data['date'] = strtotime($data['date']);
		$this->communication_report = $data;
		$this->communication_report['uid'] = $core->user['uid'];
		$date_tostrtime = array('customerDocumentDate', 'receivedQuantityDate', 'providedDocumentsDate', 'customerAnswerDate', 'provisionDate', 'offerDate', 'OfferAnswerDate');
		foreach($date_tostrtime as $converteddate) {
			$this->communication_report[$converteddate] = strtotime($this->communication_report[$converteddate]);
		}
		$filter_inputs = array('customerDocument', 'requestedQuantity', 'requestedDocuments', 'receivedQuantity', 'receivedDocuments', 'providedQuantity', 'providedDocuments', 'customerAnswer', 'industrialQuantity', 'trialResult', 'offerMade', 'customerOfferAnswer', 'sourcingnotPossibleDesc', 'description');
		foreach($filter_inputs as $sanitizedinput) {
			$this->communication_report[$sanitizedinput] = $core->sanitize_inputs($this->communication_report[$sanitizedinput], array('removetags' => true));
		}
		if(!empty($this->communication_report['description']) && !empty($this->communication_report['chemical']) && !empty($this->communication_report['appplication']) && !empty($this->communication_report['date']) && !empty($this->communication_report['market'])) {
			$this->communication_entriesexist = 'true';
		}

		if($options['operationtype'] == 'update' && (isset($this->orderpassed) && ($this->orderpassed == 1))) {
			$this->communication_report['isCompleted'] = 1;
			$db->update_query("sourcing_suppliers_contacthist", $this->communication_report, " identifier='".$db->escape_string($identifier)."' AND ssid=".$db->escape_string($supplier_id));
			$this->status = 7;
			return true;
		}
		$communication_report_query = $db->query("SELECT * FROM ".Tprefix."  sourcing_suppliers_contacthist  WHERE ssid = ".$supplier_id." and uid = ".$core->user['uid']."");
		if($db->num_rows($communication_report_query) == 1 && value_exists('sourcing_suppliers_contacthist', 'ssid', $supplier_id, 'uid='.$core->user['uid'].' AND chemical="" AND application="" AND market="" AND competitors="" AND description=""')) {
			$db->update_query('sourcing_suppliers_contacthist', $this->communication_report, 'uid='.$core->user['uid'].' AND ssid='.$supplier_id);
			$this->status = 2;
			return true;
		}
		elseif($options['operationtype'] == 'add') {
			echo 'adding...';
			$db->insert_query('sourcing_suppliers_contacthist', $this->communication_report);
			$this->status = 0;
			return true;
		}
	}

	private function read($id, $simple = false) {
		global $db;

		if(empty($id)) {
			return false;
		}

		$query_select = '*';
		if($simple == true) {
			$query_select = 'ssid, companyName, isBlacklisted';
		}

		return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."sourcing_suppliers WHERE ssid=".$db->escape_string($id)));
	}

	public function get_all_potential_suppliers($filter_where = '') {
		global $db, $core;

		$sort_query = 'ORDER BY businessPotential DESC, isBlacklisted ASC, relationMaturity DESC';
		if(isset($core->input['sortby'], $core->input['order'])) {
			$sort_query = 'ORDER BY '.$core->input['sortby'].' '.$core->input['order'];
		}

		if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
			$core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
		}

		$limit_start = 0;
		if(isset($core->input['start'])) {
			$limit_start = $db->escape_string($core->input['start']);
		}

		/* if no permission person should only see suppliers who work in the same segements he/she is working in - START */
		$join_employeessegments = '';
		if($core->usergroup['sourcing_canManageEntries'] == 0) {
			$join_employeessegments = "	JOIN ".Tprefix."sourcing_suppliers_productsegments ssp ON (ssp.ssid = ss.ssid)
										JOIN ".Tprefix."employeessegments es ON (es.psid = ssp.psid)
										WHERE es.uid=".$core->user['uid'];
			if(!empty($filter_where) && isset($filter_where)) {
				$filter_where = ' AND '.$filter_where;
			}
		}
		else {
			if(!empty($filter_where) && isset($filter_where)) {
				$filter_where = ' WHERE '.$filter_where;
			}
		}
		/* if no permission person should only see suppliers who work in the same segements he/she is working in - END */

		$suppliers_query = $db->query("SELECT ss.ssid, ss.companyName, ss.companyNameAbbr, ss.type, ss.isBlacklisted, ss.businessPotential
											FROM ".Tprefix."sourcing_suppliers ss
											{$join_employeessegments}
											{$filter_where}
											{$sort_query}
											LIMIT {$limit_start}, {$core->settings[itemsperlist]}");

		if($db->num_rows($suppliers_query) > 0) {
			while($supplier = $db->fetch_assoc($suppliers_query)) {
				$activity_area = $this->get_supplier_activity_area($supplier['ssid']);
				if(!$this->validate_permission($supplier['ssid'])) {
					//	continue;
				}
				$potential_suppliers[$supplier['ssid']]['segments'] = $this->get_supplier_segments($supplier['ssid']);
				$potential_suppliers[$supplier['ssid']]['activityarea'] = $this->get_supplier_activity_area($supplier['ssid']);
				$supplier['type'] = $this->parse_supplytype($supplier['type']);
				$potential_suppliers[$supplier['ssid']]['supplier'] = $supplier;
			}
			return $potential_suppliers;
		}
		return false;
	}

	public function get_supplier_segments($supplier_id = '') {
		global $db;

		if(empty($supplier_id)) {
			$supplier_id = $this->supplier['ssid'];
		}
		else {
			if(!$this->validate_permission($supplier_id)) {
				return false;
			}
		}

		$segments_query = $db->query("SELECT ssp.psid, ps.title AS segment
									FROM ".Tprefix."sourcing_suppliers_productsegments ssp
									JOIN ".Tprefix."productsegments ps ON(ps.psid=ssp.psid)
									WHERE ssp.ssid= ".$db->escape_string($supplier_id));
		if($db->num_rows($segments_query) > 0) {
			while($segment = $db->fetch_assoc($segments_query)) {
				$segments[$segment['psid']] = $segment['segment'];
			}
			return $segments;
		}
		return array();
	}

	public function get_supplier_contactdetails($supplier_id = '') {
		global $db;

		if(empty($supplier_id)) {
			$supplier_id = $this->supplier['ssid'];
		}
		else {
			if(!$this->validate_permission($supplier_id)) {
				return false;
			}
		}

		return $db->fetch_assoc($db->query("SELECT c.name AS country, ss.addressLine1, ss.addressLine2, ss.building, ss.floor, ss.postCode, ss.poBox, ss.city, ss.phone1, ss.phone2, ss.fax, ss.mainEmail, ss.website
											FROM ".Tprefix."sourcing_suppliers ss
											JOIN ".Tprefix."countries c ON (ss.country=c.coid)

											WHERE ss.ssid=".$db->escape_string($supplier_id))); //JOIN ".Tprefix."cities ct ON (ct.ciid=ss.city)
	}

	public function get_supplier_contact_persons($supplier_id = '') {
		global $db;

		if(empty($supplier_id)) {
			$supplier_id = $this->supplier['ssid'];
		}
		else {
			if(!$this->validate_permission($supplier_id)) {
				return false;
			}
		}

		$contact_query = $db->query("SELECT rp.*, sscp.*
									FROM ".Tprefix."sourcing_suppliers_contactpersons sscp
									JOIN ".Tprefix."representatives rp ON(sscp.rpid=rp.rpid)
									WHERE sscp.ssid= ".$db->escape_string($supplier_id));

		if($db->num_rows($contact_query) > 0) {
			while($contact_person = $db->fetch_assoc($contact_query)) {
				$contact_persons[$contact_person['rpid']] = $contact_person;
			}
			return $contact_persons;
		}
		return false;
	}

	public function get_supplier_activity_area($supplier_id = '', $real = false) {
		global $db;

		if(empty($supplier_id)) {
			$supplier_id = $this->supplier['ssid'];
		}
		else {
			if(!$this->validate_permission($supplier_id)) {
				return false;
			}
		}

		if($real == false) {
			$query_whereadd = ' AND availability!=0';
		}

		$activity_area_query = $db->query("SELECT ssaa.*, co.name AS country, aff.name AS affiliate
									FROM ".Tprefix."sourcing_suppliers ss
									JOIN ".Tprefix."sourcing_suppliers_activityareas ssaa ON (ss.ssid=ssaa.ssid)
									JOIN ".Tprefix."countries co ON (co.coid=ssaa.coid)
									JOIN ".Tprefix."affiliates aff ON (aff.affid=co.affid)
									WHERE ss.ssid= ".$db->escape_string($supplier_id).$query_whereadd);


		if($db->num_rows($activity_area_query) > 0) {
			while($activity_area = $db->fetch_assoc($activity_area_query)) {
				$activity_areas[$activity_area['coid']] = $activity_area;
			}

			return $activity_areas;
		}
		//return array();  /* empty array */
	}

	public function get_single_supplier_contact_person($id) {
		global $db;

		//return $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."representatives WHERE rpid='".$db->escape_string($id)."'"));
	}

	public function get_chemicalsubstances($id = '', $contact_hisoryid = '', $options = '') {
		global $db;

		if(empty($id)) {
			$id = $this->supplier['ssid'];
		}
		elseif(!empty($contact_hisoryid)) {
			$contact_hisoryid = $contact_hisoryid;
		}
		if(!empty($options) && $options == 'chemicalhistory') {

			$chemical_name = $db->fetch_assoc($db->query("SELECT chs.csid,chs.casNum,chs.name 
												FROM ".Tprefix."chemicalsubstances chs
												JOIN ".Tprefix."sourcing_suppliers_contacthist ssch ON (ssch.chemical= chs.csid)
												WHERE ssch.sschid= ".$db->escape_string($contact_hisoryid)));
			return $chemical_name;
		}

		$chemicalsubstances_query = $db->query("SELECT *
												FROM ".Tprefix."chemicalsubstances chs
												JOIN ".Tprefix."sourcing_suppliers_chemicals ssc ON (ssc.csid= chs.csid)
												WHERE ssc.ssid= ".$db->escape_string($id));
		if($db->num_rows($chemicalsubstances_query) > 0) {
			while($chemicalsubstance = $db->fetch_assoc($chemicalsubstances_query)) {
				$chemicalsubstance['supplyType_output'] = $this->parse_supplytype($chemicalsubstance['supplyType']);
				$chemicalsubstances[$chemicalsubstance['csid']] = $chemicalsubstance;
			}
			$db->free_result($chemicalsubstances_query);

			return $chemicalsubstances;
		}
		return false;
	}

	private function read_contact_history($supplier_id = '') {
		global $db, $core;

		if(empty($supplier_id)) {
			$supplier_id = $this->supplier['ssid'];
		}

		$contact_query = $db->query("SELECT aff.name AS affiliate, aff.affid, ssch.*, u.displayName, u.uid
										FROM ".Tprefix."sourcing_suppliers_contacthist  ssch
										JOIN ".Tprefix."affiliates aff ON (aff.affid = ssch.affid)
										JOIN ".Tprefix."users u ON (u.uid = ssch.uid)
										WHERE ssch.ssid = ".$db->escape_string($supplier_id)."
										ORDER BY ssch.date DESC"); // There is no user limitation

		if($db->num_rows($contact_query) > 0) {
			while($contact_history = $db->fetch_assoc($contact_query)) {
				$sourcing_contact_history[$contact_history['sschid']] = $contact_history;
			}

			$this->contact_history = $sourcing_contact_history;
			return $this->contact_history;
		}
		return false;
	}

	private function validate_permission($supplier_id = '') {
		global $db, $core;

		if(empty($supplier_id)) {
			$supplier_id = $this->supplier['ssid'];
		}

		/* If user is not a sourcing agent, check his/her segements - START */
		if($core->usergroup['sourcing_canManageEntries'] == 1) {
			/* check country if availabilty is no */
			$activityarea_query = $db->query("SELECT ssaa.ssaid 
												FROM ".Tprefix."sourcing_suppliers_activityareas ssaa
												JOIN ".Tprefix."countries co ON (co.coid=ssaa.coid)
												JOIN ".Tprefix."affiliates aff ON (aff.affid=co.affid)
												WHERE aff.affid IN ('".implode(',', $core->user['affiliates'])."') AND ssaa.ssid= ".$db->escape_string($supplier_id));

			if($db->num_rows($activityarea_query) > 0) {
				$suppliers_query = $db->query("SELECT ssp.psid
										FROM ".Tprefix."sourcing_suppliers_productsegments ssp
										JOIN ".Tprefix."employeessegments es ON (es.psid = ssp.psid)
										WHERE ssp.ssid=".$db->escape_string($supplier_id)."
										AND es.uid=".$db->escape_string($core->user['uid'])."
										LIMIT 0, 1");

				if($db->num_rows($suppliers_query) > 0) {
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return false; //
			}
		}
		else {
			return true;
		}
		/* If user is not a sourcing agent, check his/her segements - END */
		return false;
	}

	public function request_chemical($data) {
		global $db, $core;

		if(is_empty($data['product'], $data['requestDescription'])) {
			$this->status = 1;
			return false;
		}

		if(value_exists('sourcing_chemicalrequests', 'requestDescription', $data['requestDescription'])) {
			$this->status = 2;
			return false;
		}

		if(is_array($data)) {
			$chemicalrequest_data = array(
					'csid' => $data['product'],
					'uid' => $core->user['uid'],
					'timeRequested' => TIME_NOW,
					'requestDescription' => $core->sanitize_inputs($data['requestDescription'], array('removetags' => true))
			);
			$query = $db->insert_query('sourcing_chemicalrequests', $chemicalrequest_data);
			if($query) {
				$this->status = 0;
				return true;
			}
		}
		return false;
	}

	public function get_chemicalrequests() {
		global $db, $core;

		$sort_query = 'ORDER BY cs.name ASC, scr.timeRequested DESC';
		if(isset($core->input['sortby'], $core->input['order'])) {
			$sort_query = 'ORDER BY '.$core->input['sortby'].' '.$core->input['order'];
		}

		if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
			$core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
		}

		$limit_start = 0;
		if(isset($core->input['start'])) {
			$limit_start = $db->escape_string($core->input['start']);
		}

		if($core->usergroup['sourcing_canManageEntries'] == 0) { /* Users shouldn't be able to see requests by other users. Sourcing agents can see all requests. */
			$see_otherusers = '	WHERE scr.uid='.$core->user['uid'];
		}

		$chemicalrequests_query = $db->query("SELECT scr.*, u.displayName, cs.name AS chemicalname
										FROM ".Tprefix."sourcing_chemicalrequests scr
										JOIN ".Tprefix."users u ON (u.uid = scr.uid)
										JOIN ".Tprefix."chemicalsubstances cs ON (cs.csid = scr.csid)
										{$see_otherusers} {$sort_query}");

		if($db->num_rows($chemicalrequests_query) > 0) {
			while($chemicalrequest = $db->fetch_assoc($chemicalrequests_query)) {
				$chemicalrequests[$chemicalrequest['scrid']] = $chemicalrequest;
			}

			return $chemicalrequests;
		}
		return false;
	}

	public function set_feedback($data, $request_id = '') {
		global $db, $core;
		if(is_empty($data['feedback'])) {
			$this->status = 1;
			return false;
		}

		$data['feedback'] = $core->sanitize_inputs($data['feedback'], array('removetags' => true));

		if(value_exists('sourcing_chemicalrequests', 'feedback', $data['feedback'], 'isClosed='.intval($data['isClosed']))) {
			$this->status = 2;
			return false;
		}

		$feedback_data = array('feedback' => trim($data['feedback']),
				'feedbackBy' => $core->user['uid'],
				'feedbackTime' => TIME_NOW,
				'isClosed' => $data['isClosed']
		);

		$update_query = $db->update_query('sourcing_chemicalrequests', $feedback_data, 'scrid='.intval($request_id));
		if($update_query) {
			$this->status = 0;
			return true;
		}
		else {
			return false;
		}
	}

	private function read_feedback($request_id) {
		global $db, $core;
		if($core->usergroup['sourcing_canManageEntries'] == 0) { /* Users shouldn't be able to see requests by other users. Sourcing agents can see all requests. */
			$see_otherusers = '	AND scr.uid='.$core->user['uid'];
		}
		return $db->fetch_assoc($db->query("SELECT scr.feedback, scr.feedbackTime, u.displayName, isClosed
										FROM ".Tprefix."sourcing_chemicalrequests scr
										JOIN ".Tprefix."users u ON (u.uid = scr.feedbackBy)
										WHERE scr.scrid=".intval($request_id)."
										{$see_otherusers}"));
	}

	public function parse_supplytype($supplytype) {
		global $lang;

		if(empty($supplytype)) {
			$supplytype = $this->supplier['type'];
		}

		switch($supplytype) {
			case 't':
				return $lang->trader;
				break;
			case 'p':
				return $lang->producer;
				break;
			case 'b':
				return $lang->both;
				break;
		}
		return false;
	}

	public function is_blacklisted() {
		if($this->supplier['isBlacklisted'] == 1) {
			return true;
		}
		return false;
	}

	public function supplier_exists($supplier_id = '') {
		if(!empty($supplier_id)) {
			if(value_exists('sourcing_suppliers', 'ssid', $supplier_id)) {
				return true;
			}
			return false;
		}
		else {
			if(is_array($this->supplier) && !empty($this->supplier)) {
				return true;
			}
			return false;
		}
		return false;
	}

	public function register_supplier($affid) {
		global $db, $core;
		$registered_supplier_data = array('companyName', 'companyNameAbbr', 'type', 'country', 'city', 'addressLine1', 'addressLine2', 'building', 'floor', 'postCode', 'geoLocation', 'poBox', 'phone1', 'phone2', 'fax1', 'mainEmail', 'website');
		$temp_supplier = $this->supplier;
		unset($temp_supplier['ssid'], $temp_supplier['eid']);
		foreach($registered_supplier_data as $registered_supplier) {
			$this->registered_supplier[$registered_supplier] = $temp_supplier[$registered_supplier];
		}
		$this->registered_supplier['affid'][] = $affid;
		$this->registered_supplier['psid'] = array_keys($this->get_supplier_segments()); /* GET the psid from the array returned from the function */
		$this->registered_supplier['representative'] = $this->get_supplier_contact_persons();
		$registered_supplier = new Entities($this->registered_supplier);
		if($registered_supplier) {
			$this->status = 3;
			return true;
		}
		else {
			$this->status = 1;
			return false;
		}
	}

	public function create_chemical(array $data) {
		global $db, $core;

		if(is_empty($data['casNum'], $data['name'])) {
			$this->status = 4;
			return false;
		}

		if(value_exists('chemicalsubstances', 'casNum', $data['casNum']) || value_exists('chemicalsubstances', 'name', $data['name'])) {
			$this->status = 5;
			return false;
		}

		$chemical_data = array(
				'casNum' => $core->sanitize_inputs($data['casNum'], array('removetags' => true)),
				'name' => $core->sanitize_inputs($data['name'], array('removetags' => true)),
				'synonyms' => $core->sanitize_inputs($data['synonyms'], array('removetags' => true))
		);
		$query = $db->insert_query('chemicalsubstances', $chemical_data);
		if($query) {
			$this->status = 0;
			return true;
		}
		return false;
	}

	private function determine_supplyiertype($supply_types) {
		foreach($supply_types as $cas => $supplytype) {
			$types[] = $supplytype['supplyType'];
		}

		$types = array_unique($types);
		if(count($types) > 1) {
			return 'b';
		}
		else {
			return current($types);
		}
	}

	public function parse_rmlbar($supplier_id = '') {
		global $core, $db;

		if(empty($supplier_id)) {
			$supplier_id = $this->supplier['ssid'];
			$rmlcurrentlevel = $this->supplier['relationMaturity'];
		}

		if(empty($rmlcurrentlevel)) {
			$rmlcurrentlevel = $db->fetch_field($db->query('SELECT relationMaturity FROM '.Tprefix.'sourcing_suppliers WHERE ssid='.intval($supplier_id)), 'relationMaturity');
		}

		$maturity_bars = '';
		$readonlymaturity = true;

		$rmllist = array();

		$rmllevels_query = $db->query('SELECT ermlid, title FROM '.Tprefix.'entities_rmlevels ORDER BY sequence');
		if($db->num_rows($rmllevels_query) > 0) {
			while($maturitylevelrow = $db->fetch_assoc($rmllevels_query)) {
				$rmllist[$maturitylevelrow['ermlid']] = $maturitylevelrow['title'];
			}
		}
		else {
			return false;
		}

		$maturity_bars .= '<div id="rml_bars">';
		$divclassactive = (!$readonlymaturity) ? 'rmlselectable rmlactive' : 'rmlactive';
		$divclassinactive = (!$readonlymaturity) ? 'rmlselectable rmlinactive' : 'rmlinactive';
		$counter = 1;
		$positionclass = ' first';

		$is_coloredlevel = true;
		if(!isset($rmlcurrentlevel)) {
			$is_coloredlevel = false;
		}
		$is_lastactiveitem = false;

		foreach($rmllist as $ermlid => $name) {
			if($is_coloredlevel == true) {
				if($rmlcurrentlevel == $ermlid) {
					$is_lastactiveitem = true;
				}
			}

			if($counter++ == count($rmllist)) {
				if($counter == 2) {
					$positionclass = ' first last';
				}
				else {
					$positionclass = ' last';
				}
			}
			else {
				if($counter != 2) {
					$positionclass = '';
				}
			}

			if($is_coloredlevel) {

				$maturity_bars .= '<div id="'.$ermlid.'" class="'.$divclassactive.$positionclass.'" title="'.($lang->{$name} ? $lang->{$name} : $name).'">&nbsp;</div>';
			}
			else {
				$maturity_bars .= '<div id="'.$ermlid.'" class="'.$divclassinactive.$positionclass.'" title="'.($lang->{$name} ? $lang->{$name} : $name).'">&nbsp;</div>';
			}

			if($is_lastactiveitem) {
				$is_coloredlevel = false;
				$is_lastactiveitem = false;
			}
		}
		$maturity_bars .= '</div>';

		return $maturity_bars;
	}

	public function get_feedback($request_id) {
		return $this->read_feedback($request_id);
	}

	public function get_contact_history() {
		return $this->read_contact_history();
	}

	public function get_supplier() {
		return $this->supplier;
	}

	public function get_status() {
		return $this->status;
	}

}
?>