<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Sourcing Class
 * $id: Sourcing_class.php
 * Created:			@tony.assaad	October 15, 2012 | 10:53 PM
 * Last Update: 	@zaher.reda		October 31, 2012 | 17:06 PM
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
		$this->representative = $this->supplier['representative']['id'];
		$this->activityarea = $this->supplier['activityarea'];
		$this->supplier['ssid'] = intval($this->supplier['ssid']);

		unset($this->supplier['ssid'], $this->supplier['chemicalproducts'], $this->supplier['productsegment'], $this->supplier['representative'], $this->supplier['activityarea']);
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

		if($options['operationtype'] == 'update') {
			$this->supplier['dateModified'] = TIME_NOW;
			$this->supplier['modifiedBy'] = $core->user['uid'];

			$query = $db->update_query('sourcing_suppliers', $this->supplier, 'ssid='.$this->supplier['ssid'].'');
		}
		else {
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
				foreach($this->activityarea as $activityarea) {
					$activity_area = array(
							'ssid' => $this->supplier['ssid'],
							'coid' => $activityarea,
					);
					$db->insert_query('sourcing_suppliers_activityareas', $activity_area);
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
				foreach($this->representative as $representative) {
					$suppliers_contactpersons = array(
							'ssid' => $this->supplier['ssid'],
							'rpid' => $representative
					);
					$querycontactpersons = $db->insert_query('sourcing_suppliers_contactpersons', $suppliers_contactpersons);
				}
			}

			if($options['operationtype'] == 'update') {
				$db->delete_query('sourcing_suppliers_chemicals', 'ssid='.$this->supplier['ssid']);
			}

			if(is_array($this->chemicals['csid'])) {
				foreach($this->chemicals['csid'] as $chemical) {
					$new_chemicals = array(
							'ssid' => $this->supplier['ssid'],
							'csid' => $chemical
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

	public function contact_supplier($id = '') {
		global $core, $db;

		if(empty($id)) {
			$id = $this->supplier['ssid'];
		}
		$db->insert_query('sourcing_suppliers_contacthist', array('ssid' => $id, 'uid' => $core->user['uid']));
	}

	public function save_communication_report($data, $supplier_id = '') {
		global $core, $db;

		if(is_empty($data['chemical'], $data['application'], $data['affid'], $data['origin'])) {
			$this->status = 1;
			return false;
		}

		if(empty($supplier_id)) {
			$supplier_id = $this->supplier['ssid'];
		}

		$this->communication_entriesexist = 'false';
		$data['date'] = strtotime($data['date']);
		$this->communication_report = $data;
		//$supplier_id = $data['supplier_id'];
		$this->communication_report['uid'] = $core->user['uid'];
		$this->communication_report['description'] = $core->sanitize_inputs($this->communication_report['description'], array('removetags' => true));
		if(!empty($this->communication_report['description']) && !empty($this->communication_report['chemical']) && !empty($this->communication_report['appplication']) && !empty($this->communication_report['date']) && !empty($this->communication_report['market'])) {
			$this->communication_entriesexist = 'true';
		}
		$communication_report_query = $db->query("SELECT * FROM ".Tprefix."  sourcing_suppliers_contacthist  
												 WHERE ssid = ".$supplier_id." and uid = ".$core->user['uid']."");
		if($db->num_rows($communication_report_query) == 1 && value_exists('sourcing_suppliers_contacthist', 'ssid', $supplier_id, 'uid='.$core->user['uid'].' AND chemical="" AND application="" AND market="" AND competitors="" AND description=""')) {
			$db->update_query('sourcing_suppliers_contacthist', $this->communication_report, 'uid='.$core->user['uid'].' AND ssid='.$supplier_id);
			$this->status = 2;
			return true;
		}
		else {
			$db->insert_query('sourcing_suppliers_contacthist', $this->communication_report);
			$this->status = 3;
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
			$query_select = 'ssid, companyName';
		}

		return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."sourcing_suppliers WHERE ssid=".$db->escape_string($id)));
	}

	public function get_all_potential_suppliers($filter_where = '') {
		global $db, $core;

		//$sort_query = 'ORDER BY ss.ssid ASC';
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
										JOIN ".Tprefix."employeessegments es ON (es.psid = ssp.psid) WHERE  es.uid=".$core->user['uid'];
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

		$suppliers_query = $db->query("SELECT ss.ssid, ss.companyName, ss.type, ss.isBlacklisted, ss.businessPotential 
											FROM ".Tprefix."sourcing_suppliers ss							
											{$join_employeessegments}
											{$filter_where}
											{$sort_query} 
											LIMIT {$limit_start}, {$core->settings[itemsperlist]}");
		if($db->num_rows($suppliers_query) > 0) {
			while($supplier = $db->fetch_assoc($suppliers_query)) {
				$potential_suppliers[$supplier['ssid']]['segments'] = $this->get_supplier_segments($supplier['ssid']);
				$potential_suppliers[$supplier['ssid']]['activityarea'] = $this->get_supplier_activity_area($supplier['ssid']);
				$potential_suppliers[$supplier['ssid']]['chemicalsubstance'] = $this->get_chemicalsubstances($supplier['ssid']);
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
			;
		}
		else {
			if(!$this->validate_segment_permission($supplier_id)) {
				return false;
			}
		}

		$segments_query = $db->query("SELECT ssp.psid, ps.title AS segment 
									FROM ".Tprefix."sourcing_suppliers_productsegments ssp
									JOIN ".Tprefix."productsegments ps ON(ps.psid=ssp.psid)
									WHERE ssp.ssid= ".$db->escape_string($supplier_id)."");
		if($db->num_rows($segments_query) > 0) {
			while($segment = $db->fetch_assoc($segments_query)) {
				$segments[$segment['psid']] = $segment['segment'];
			}
			return $segments;
		}
		return false;
	}

	public function get_supplier_contactdetails($supplier_id = '') {
		global $db;

		if(empty($supplier_id)) {
			$supplier_id = $this->supplier['ssid'];
			;
		}
		else {
			if(!$this->validate_segment_permission($supplier_id)) {
				return false;
			}
		}

		return $db->fetch_assoc($db->query("SELECT c.name AS country, ct.name AS city, ss.addressLine1, ss.addressLine2, ss.building, ss.floor, ss.postCode, ss.poBox, ss.phone1, ss.phone2, ss.fax, ss.mainEmail, ss.website
											FROM  ".Tprefix."sourcing_suppliers ss
											JOIN ".Tprefix."countries c ON (ss.country=c.coid)
											JOIN ".Tprefix."cities ct ON (ct.ciid=ss.city)
											WHERE ss.ssid= ".$db->escape_string($supplier_id).""));
	}

	public function get_supplier_contact_persons($supplier_id = '') {
		global $db;

		if(empty($supplier_id)) {
			$supplier_id = $this->supplier['ssid'];
			;
		}
		else {
			if(!$this->validate_segment_permission($supplier_id)) {
				return false;
			}
		}

		$contact_query = $db->query("SELECT rp.* 
									FROM ".Tprefix." sourcing_suppliers_contactpersons sscp
									JOIN ".Tprefix." representatives rp ON(sscp.rpid=rp.rpid)
									WHERE sscp.ssid= ".$db->escape_string($supplier_id)."");
		if($db->num_rows($contact_query) > 0) {
			while($contact_person = $db->fetch_assoc($contact_query)) {
				$contact_persons[$contact_person['rpid']] = $contact_person;
			}
			return $contact_persons;
		}
		return false;
	}

	public function get_supplier_activity_area($supplier_id = '') {
		global $db;

		if(empty($supplier_id)) {
			$supplier_id = $this->supplier['ssid'];
			;
		}
		else {
			if(!$this->validate_segment_permission($supplier_id)) {
				return false;
			}
		}

		$activity_area_query = $db->query("SELECT ss.ssid,ssaa.ssaid ,co.name as country,aff.name as affiliate from ".Tprefix."sourcing_suppliers ss
									JOIN ".Tprefix."sourcing_suppliers_activityareas ssaa ON (ss.ssid= ssaa.ssid)
									JOIN ".Tprefix."countries co ON (co.coid = ssaa.coid)
									JOIN ".Tprefix."affiliates aff ON (aff.affid = co.affid)
									WHERE ss.ssid= ".$db->escape_string($supplier_id)."");

		if($db->num_rows($activity_area_query) > 0) {
			while($activity_areas = $db->fetch_assoc($activity_area_query)) {
				$supplier_activity_area[$activity_areas['ssaid']]['affiliate'] = $activity_areas['affiliate'];
				$supplier_activity_area[$activity_areas['ssaid']] ['country'] = $activity_areas['country'];
			}
			return $supplier_activity_area;
		}
		return false;
	}

	public function get_single_supplier_contact_person($id) {
		global $db;

		return $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."representatives WHERE rpid='".$db->escape_string($id)."'"));
	}

	public function get_chemicalsubstances($supplier_id = '') {
		global $db;

		if(empty($supplier_id)) {
			$supplier_id = $this->supplier['ssid'];
			;
		}

		$chemicalsubstances_query = $db->query("SELECT * FROM ".Tprefix."chemicalsubstances chs
												JOIN ".Tprefix."sourcing_suppliers_chemicals ssc ON (ssc.csid= chs.csid)
												WHERE ssc.ssid= ".$db->escape_string($supplier_id)."");
		if($db->num_rows($chemicalsubstances_query) > 0) {
			while($chemicalsubstance = $db->fetch_assoc($chemicalsubstances_query)) {
				$chemicalsubstances[$chemicalsubstance['csid']] = $chemicalsubstance;
			}
			return $chemicalsubstances;
		}
		return false;
	}

	private function read_contact_history($supplier_id = '') {
		global $db, $core;

		if(empty($supplier_id)) {
			$supplier_id = $this->supplier['ssid'];
			;
		}

		$contact_query = $db->query("SELECT aff.name AS affiliate, aff.affid, co.name AS origincountry, ssch.*, u.displayName, u.uid 
										FROM ".Tprefix."sourcing_suppliers_contacthist  ssch
										JOIN ".Tprefix."countries co ON (co.coid = ssch.origin)
										JOIN ".Tprefix."affiliates aff ON (aff.affid = ssch.affid)
										JOIN ".Tprefix."users u ON (u.uid = ssch.uid)
										WHERE ssch.ssid = ".$db->escape_string($supplier_id)."
										ORDER BY ssch.date DESC");
		if($db->num_rows($contact_query) > 0) {
			while($contact_history = $db->fetch_assoc($contact_query)) {
				$sourcing_contact_history[$contact_history['sschid']] = $contact_history;
			}

			$this->contact_history = $sourcing_contact_history;
			return $this->contact_history;
		}
		return false;
	}

	private function validate_segment_permission($supplier_id = '') {
		global $db, $core;

		if(empty($supplier_id)) {
			$supplier_id = $this->supplier['ssid'];
			;
		}

		/* If user is not a sourcing agent, check his/her segements - START */
		if($core->usergroup['sourcing_canManageEntries'] == 0) {
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
		/* If user is not a sourcing agent, check his/her segements - END */
		return true;
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
			$chemicalrequest_data = array('csid' => $data['product'],
					'uid' => $core->user['uid'],
					'timeRequested' => TIME_NOW,
					'requestDescription' => $data['requestDescription']
			);
			$query = $db->insert_query('sourcing_chemicalrequests', $chemicalrequest_data);
		}
		if($query) {
			return true;
		}
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