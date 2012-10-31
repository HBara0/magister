<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Sourcing Class
 * $id: Sourcing_class.php
 * Created:			@tony.assaad	October 15, 2012 | 10:53 PM
 * Last Update: 	@tony.assaad	october 25, 2012 | 3:59  AM
 */
 
class Sourcing {
	protected $status = 0;
	private $supplier = array();

	public function __construct($id='', $simple=false) {
		if(isset($id) && !empty($id)){
			$this->supplier = $this->read_supplier($id, $simple);
			$this->supplier_id = $this->supplier['ssid'];
			$supplier_id = $this->supplier_id;
		}
	}

	public function add($data, array $options = array()) {
		global $db, $log, $core, $errorhandler,$lang;
		$this->supplier = $data;	
		$this->chemical = $this->supplier['chemicalproducts'];
		$this->productsegment = $this->supplier['productsegment'];
		$this->representative = $this->supplier['representative']['id'];
		$this->activityarea = $this->supplier['activityarea'];
		$current_supplier_id = $this->supplier['ssid'];
		/* if the action is edit here we call the add and pass the [options type] function if the action type is do_editsupplier */
		if($options['operationtype'] != 'update'){
			if(is_empty($this->supplier['companyName'] && $this->supplier['productsegment']) ) {
				$this->status = 1;
				return false;	
			}
		}
	unset($this->supplier['ssid'],$this->supplier['chemicalproducts'],$this->supplier['productsegment'],$this->supplier['representative'],$this->supplier['activityarea']);
	if($options['operationtype'] != 'update'){
		if(value_exists('sourcing_suppliers', 'companyName', $this->supplier['companyName'])) {
			$this->status = 2;
			return false;	
		}
	}
  
		$sanitize_fields = array('companyName','companyNameAbbr','country','city','addressLine1','addressLine2','building','postCode','poBox','commentsToShare','marketingRecords','coBriefing','historical','sourcingRecords','productFunction');
			foreach($sanitize_fields as $val) {
			$this->supplier[$val] = $core->sanitize_inputs($this->supplier[$val], array('removetags'=> true));	
		}
		$this->supplier['mainEmail'] = $core->sanitize_email($this->supplier['mainEmail']);
		$this->supplier['mainEmail']= $core->validate_email($this->supplier['mainEmail']);
		$this->supplier['website'] = $core->validtate_URL($this->supplier['website']);
		$this->supplier['createdBy'] = $core->user['uid'];
		$this->supplier['dateCreated'] = TIME_NOW;
		
		if($options['operationtype'] == 'update') { 
			$update_supplier_data = $this->supplier;
			$update_supplier_data['dateModified'] =  TIME_NOW;
			$update_supplier_data['modifiedBy'] = $core->user['uid'];
			unset($update_supplier_data['createdBy'],$update_supplier_data['dateCreated']); print_R($update_supplier_data);
			$query = $db->update_query('sourcing_suppliers',$update_supplier_data,'ssid='.$current_supplier_id.'');
		}
		/* Insert supplier - START */
		if(is_array($this->supplier)) {
			$query = $db->insert_query('sourcing_suppliers', $this->supplier);
			if($query) {
				$this->status = 0;
				$ssid = $db->last_id();
		
			if($options['operationtype'] == 'update') {
					$db->delete_query('sourcing_suppliers_activityareas',"ssid='{$current_supplier_id}'");
				/*update suppliers_activityareas - START */  
				if(is_array($this->activityarea)) {
					foreach($this->activityarea  as  $activityarea) { 
					$activity_area = array('ssid'=>$current_supplier_id,
											'coid'=>$activityarea,	
										);
						$db->insert_query('sourcing_suppliers_activityareas', $activity_area);
					}
				}
				/*update suppliers_activityareas - END */		
			}
			else
			{
				/*Insert new suppliers_activityareas - START */  
				if(is_array($this->activityarea)) {
					foreach($this->activityarea  as  $activityarea) {
					$activity_area = array('ssid'=>$ssid,
											'coid'=>$activityarea,	
											);
						$db->insert_query('sourcing_suppliers_activityareas', $activity_area);
					}
				}		
			} /*Insert suppliers_activityareas - END */
				if($options['operationtype'] == 'update') {
					$db->delete_query('sourcing_suppliers_productsegments',"ssid='{$current_supplier_id}'");
					$ssid = $current_supplier_id;	/* new id for update */
				}
				
			/*Insert suppliers_productsegments - START */
			if(is_array($this->productsegment)) {
				foreach($this->productsegment  as  $productsegment) {
				$suppliers_productsegments = array('ssid'=>$ssid,
														'psid'=>$productsegment,	
														);
				$db->insert_query('sourcing_suppliers_productsegments', $suppliers_productsegments);
				}
			}	
			/*Insert suppliers_productsegments - END */
					
				$log->record($this->supplier['ssid']);
			}
			if($options['operationtype'] == 'update') {
				$db->delete_query('sourcing_suppliers_contactpersons',"ssid='{$current_supplier_id}'");
				$ssid = $current_supplier_id;	//new id to update
			}
			if(is_array($this->representative)) {
				foreach($this->representative as  $representative) {	
					$suppliers_contactpersons = array('ssid'=>$ssid,
															'rpid'=>$representative
															);
					$querycontactpersons = $db->insert_query('sourcing_suppliers_contactpersons', $suppliers_contactpersons);
				}
			}
			if($options['operationtype'] == 'update') {
				$db->delete_query('sourcing_suppliers_chemicals',"ssid='{$current_supplier_id}'");
				$ssid = $current_supplier_id;	//new id to update
			}
			if(is_array($this->chemical['csid'])) {
				foreach($this->chemical['csid'] as  $chemical) { // if edit empty table  and add 
					$casid = array('ssid'=>$ssid,
									'csid'=>$chemical
									);
					$db->insert_query('sourcing_suppliers_chemicals', $casid);
				}	
			}
			return true;						
		}
		/* Insert supplier - END */
	}
	
	public function contact_supplier($id='') {
		global $core,$db;
		echo 'conttt';
		$db->insert_query('sourcing_suppliers_contacthist', array('ssid'=>$id,'uid'=>$core->user['uid']));
		}

	public function save_communication_report($data,$id='') {
		global $core,$db;
		if(is_empty($data['chemical'], $data['application'],$data['affid'],$data['origin'])) {
			$this->status = 1;
			return false;	
		}
			if(empty($id)){
				$supplier_id = $this->supplier_id;
			}
			else
			{
				$supplier_id = $id;
			}
	
		$this->communication_entriesexist  = 'false';
		$data['date'] = strtotime($data['date']);
		$this->communication_report = $data;
		//$supplier_id = $data['supplier_id'];
		$this->communication_report['uid'] = $core->user['uid'];
		$this->communication_report['description'] = $core->sanitize_inputs($this->communication_report['description'],array('removetags' => true));
		if(!empty($this->communication_report['description']) && !empty($this->communication_report['chemical']) && !empty($this->communication_report['appplication'])&& !empty($this->communication_report['date'])&& !empty($this->communication_report['market'])) {
			$this->communication_entriesexist = 'true';
		}
		$communication_report_query = $db->query("SELECT * FROM ".Tprefix."  sourcing_suppliers_contacthist  
												 WHERE ssid = ".$supplier_id." and uid = ".$core->user['uid']."");
				if($db->num_rows($communication_report_query) == 1 && value_exists('sourcing_suppliers_contacthist', 'ssid', $supplier_id, 'uid='.$core->user['uid'].' AND chemical="" AND application="" AND market="" AND competitors="" AND description=""')) {
					$db->update_query('sourcing_suppliers_contacthist',$this->communication_report, 'uid='.$core->user['uid'].' AND ssid='.$supplier_id);
					$this->status = 2;
					return true;
				}
				else
				{  
					$db->insert_query('sourcing_suppliers_contacthist', $this->communication_report);
						$this->status = 3;
						return true;
				}
	
		}
			
	public function edit($data) {
		global $core,$db;
		unset($data['representative'],$data['chemicalproducts'],$data['productsegment']);
		print_r($data);
		$db->update_query('sourcing_suppliers',$data);			
	}
	
	private function read($id, $simple=false) {
		global $db;
	
		if(empty($id)) {
			return false;	
		}
	
		$query_select = '*';
		if($simple == true) {
			$query_select = '*';	
		}

		return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."sourcing_suppliers WHERE ssid=".$db->escape_string($id)));		
	}
	
	public function get_all_potential_supplier($filter_where = '') {
		global $db,$core;
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
		
		if(isset($core->input['filterby'], $core->input['filtervalue'])) {
			$attributes_filter_options['companyName'] = array('companyName' => 'ss.');
			
			if($attributes_filter_options['companyName'][$core->input['filterby']] == 'int') {
				$filter_value = ' = "'.$db->escape_string($core->input['filtervalue']).'"';
			}
			else
			{
				$filter_value = ' LIKE "%'.$db->escape_string($core->input['filtervalue']).'%"';
			}

			$multipage_where = ' WHERE '.$db->escape_string($attributes_filter_options['companyName'][$core->input['filterby']].$core->input['filterby']).$filter_value;
		}	
			/* if no permission person should only see suppliers who work in the same segements he/she is working in --START*/		
			if($core->usergroup['sourcing_canManageEntries'] == 0) { 
				$join_employeessegments = "JOIN ".Tprefix."employeessegments es on es.psid = ssp.psid and es.uid=".$core->user['uid']."";
					}
			/* person should only see suppliers who work in the same segements he/she is working in --END*/
				else
				{		
					/*Return All  potentials suppliers--START*/
					$join_employeessegments = '';
					/*Return All  potentials suppliers --END*/
				}				

			$suppliers_query = $db->query("SELECT ss.ssid,ss.companyName,ss.type,ss.isBlacklisted,ss.businessPotential FROM ".Tprefix."sourcing_suppliers ss							
											{$join_employeessegments}
											{$multipage_where}
											{$filter_where}
											{$sort_query} 
											LIMIT {$limit_start}, {$core->settings[itemsperlist]}");									
										
				if($db->num_rows($suppliers_query) > 0) {
					while($suppliers = $db->fetch_assoc($suppliers_query)) {
						$current_supplier_id = $suppliers['ssid'];					
						$potential_suppliers[$suppliers['ssid']]['segments'] = $this->get_supplier_segments($suppliers['ssid']);
						$potential_suppliers[$suppliers['ssid']]['activityarea'] = $this->get_supplier_activity_area($suppliers['ssid']);
						$potential_suppliers[$suppliers['ssid']]['supplier'] = $suppliers ;
					}
					return $potential_suppliers;
				}		
			return false;
			}

		public function get_supplier_segments ($supplier_id='') {
			global $db;
			if(empty($supplier_id)){
				$supplier_id = $this->supplier_id;
			}
			$segments_query = $db->query("SELECT ssp.sspsid, ssp.psid,ps.title AS segment FROM  ".Tprefix."sourcing_suppliers ss
									JOIN ".Tprefix."sourcing_suppliers_productsegments ssp ON (ss.ssid= ssp.ssid)
									JOIN ".Tprefix."productsegments ps ON(ps.psid=ssp.psid)
									WHERE ss.ssid= ".$db->escape_string($supplier_id)."");
				if($db->num_rows($segments_query) > 0) {
					while($segments = $db->fetch_assoc($segments_query)) {
						$segments_suppliers[$segments['psid']]= $segments['segment'];
					}
					return $segments_suppliers;
				}					
				return false;		
			} 
	
	public function get_supplier_contact($id='') {
		global $db;  
		if(empty($id)){
			$supplier_id = $this->supplier_id;
		}
		else
		{
			$supplier_id = $id;
		}
		
		//if($this->validate_segment_permission($supplier_id)) {  //change remove the not
				return $db->fetch_assoc($db->query("SELECT ss.*, ps.title as segment from  ".Tprefix."sourcing_suppliers ss
									JOIN ".Tprefix."sourcing_suppliers_productsegments ssp ON (ss.ssid= ssp.ssid)
									JOIN ".Tprefix."productsegments ps ON(ps.psid=ssp.psid)
									JOIN ".Tprefix."employeessegments es on (es.psid = ssp.psid)
									WHERE ss.ssid= ".$db->escape_string($supplier_id).""));		
				//}
			}
			
	public function get_supplier_contact_persons($supplier_id=''){
		global $db;
		if(empty($supplier_id)){
			$supplier_id = $this->supplier_id;
			}
		//if(!$this->validate_segment_permission($this->supplier_id)) {  //change remove the not
			$contact_query = $db->query("SELECT ss.ssid ,rp.* from ".Tprefix."sourcing_suppliers ss
									JOIN ".Tprefix." sourcing_suppliers_contactpersons sscp ON(sscp.ssid=ss.ssid)
									JOIN ".Tprefix." representatives rp ON(sscp.rpid=rp.rpid)
									WHERE ss.ssid= ".$db->escape_string($supplier_id)."");	
				if($db->num_rows($contact_query) > 0) {
					while($contact_person = $db->fetch_assoc($contact_query)) {
						$contact_persons[$contact_person['rpid']]= $contact_person;
					}
					return $contact_persons;
				}					
				return false;
			//}
		}
	public function get_supplier_activity_area($supplier_id=''){
		global $db; 
		if(empty($supplier_id)){
				$supplier_id = $this->supplier_id;
			}
			$activity_area_query = $db->query("SELECT ss.ssid,ssaa.ssaid ,co.name as country,aff.name as affiliate from ".Tprefix."sourcing_suppliers ss
									JOIN ".Tprefix."sourcing_suppliers_activityareas ssaa ON (ss.ssid= ssaa.ssid)
									JOIN ".Tprefix."countries co ON (co.coid = ssaa.coid)
									JOIN ".Tprefix."affiliates aff ON (aff.affid = co.affid)
									WHERE ss.ssid= ".$db->escape_string($supplier_id)."");
			
				if($db->num_rows($activity_area_query) > 0) {
					while($activity_areas = $db->fetch_assoc($activity_area_query)) {
						$supplier_activity_area[$activity_areas['ssaid']]['affiliate'] = $activity_areas['affiliate'];
						$supplier_activity_area[$activity_areas['ssaid']] ['country']= $activity_areas['country'];
					}
					return $supplier_activity_area;
				}					
				return false;
		}
	
 public function get_single_supplier_contact_persons($id) {
	 global $db; 
	 return $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."representatives WHERE rpid='".$db->escape_string($id)."'"));
	}

	 public function get_chemicalsubstances () {
		global $db;
		$chemicalsubstances_query = $db->query("SELECT * from ".Tprefix."chemicalsubstances chs
												JOIN ".Tprefix."sourcing_suppliers_chemicals ssc ON (ssc.csid= chs.csid)");
		if($db->num_rows($chemicalsubstances_query) > 0) {
					while($chemicalsubstances = $db->fetch_assoc($chemicalsubstances_query)) {
						$chemical_substances[$chemicalsubstances['csid']]= $chemicalsubstances;
					}
					return $chemical_substances;
				}					
				return false;
		
		 } 
		 
		 private function read_contact_history($id='') {
			 global $db,$core;
			  
			 $contact_query = $db->query("SELECT aff.name as affiliate, aff.affid ,co.name as origincountry, ssch.*,u.displayName,u.uid FROM ".Tprefix."sourcing_suppliers_contacthist  ssch
										JOIN ".Tprefix."countries co ON (co.coid = ssch.origin)
										JOIN ".Tprefix."affiliates aff ON (aff.affid = ssch.affid)
										JOIN ".Tprefix."users u ON (u.uid = ssch.uid)
										WHERE ssch.ssid = ".$db->escape_string($this->supplier_id)." 
										AND ssch.uid =".$db->escape_string($core->user['uid'])."
										Order by ssch.date DESC"); 	
			 if($db->num_rows($contact_query) > 0) {
					while($contact_history = $db->fetch_assoc($contact_query)) {
						$sourcing_contact_history[$contact_history['sschid']]= $contact_history;
					
					}
					
					 $this->contact_history = $sourcing_contact_history;
					return $this->contact_history ;	
				}					
				return false;
		
			 }
		 
	private function validate_segment_permission($id='') {
		global $db,$core;
		/* if no permission person should only see suppliers who work in the same segements he/she is working in --START*/		
		if($core->usergroup['sourcing_canManageEntries'] == 0) { 
			$suppliers_query = $db->query("SELECT ssp.psid FROM ".Tprefix."sourcing_suppliers_productsegments ssp 
			JOIN ".Tprefix."employeessegments es on (es.psid = ssp.psid)
			JOIN ".Tprefix."sourcing_suppliers ss on ss.ssid= ssp.ssid
			WHERE ss.ssid= ".$db->escape_string($id)."
			AND es.uid=".$db->escape_string($core->user['uid'])."");
			
			if($db->num_rows($suppliers_query) > 0) {
				return true;
			}
			else
			{
				return false;
			}
		}
		/* person should only see suppliers who work in the same segements he/she is working in --END*/
	}
			
		private function read_supplier($id, $simple=false) {
		global $db;
	
		$query_select = '*';
		if($simple == true) {
			$query_select = 'ssid';	
		}
		
		return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."sourcing_suppliers WHERE ssid='".$db->escape_string($id)."'"));
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