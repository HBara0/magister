<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Sourcing Class
 * $id: Sourcing_class.php
 * Created:			@tony.assaad	October 08, 2012 | 10:53 PM
 * Last Update: 	@tony.assaad	October 09, 2012 | 10:59  AM
 */
 
class Sourcing {
	protected $status = 0;
	private $supplier = array();
	
	public function __construct($id='', $simple=false) {
		if(isset($id) && !empty($id)){
			$this->supplier = $this->read($id, $simple);
		
		}
	}
	
	public function add($data, array $options = array()) {
		global $db, $log, $core, $errorhandler,$lang;
		$this->supplier = $data;
		
		$this->chemical = $this->supplier['chemicalproducts'];
		$this->productsegment = $this->supplier['productsegment'];
		$this->representative = $this->supplier['representative']['id'];
		unset($this->supplier['chemicalproducts'],$this->supplier['productsegment'],$this->supplier['representative']);
		/* if the action is edit here we call the add and pass the [options type] function if the action type is do_editsupplier */
		if(is_empty($this->supplier['companyName'])) {
			$this->status = 1;
			return false;	
		}
	if(value_exists('sourcing_suppliers', 'companyName', $this->supplier['companyName'])) {
			$this->status = 2;
			return false;	
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
	
		/* Insert supplier - START */
		if(is_array($this->supplier)) {
			$query = $db->insert_query('sourcing_suppliers', $this->supplier);
			if($query) {
				$this->status = 0;
				$ssid = $db->last_id();
				/*Insert suppliers_activityareas - START */
				$activity_area = array('ssid'=>$ssid,
										'coid'=>$this->supplier['country'],	
									   );
					$db->insert_query('sourcing_suppliers_activityareas', $activity_area);
				/*Insert suppliers_activityareas - END */
				
				/*Insert suppliers_productsegments - START */
				$suppliers_productsegments = array('ssid'=>$ssid,
													'psid'=>$this->productsegment,	
									 		  		);
					$db->insert_query('sourcing_suppliers_productsegments', $suppliers_productsegments);
				/*Insert suppliers_productsegments - END */
					
				$log->record($this->supplier['ssid']);
			}
			foreach($this->representative as  $representative) {	
					$suppliers_contactpersons = array('ssid'=>$ssid,
													 'rpid'=>$representative
												);
				$querycontactpersons = $db->insert_query('sourcing_suppliers_contactpersons', $suppliers_contactpersons);
			}
		
			foreach($this->chemical['name'] as  $chemical) {print_r($chemical);
				$db->insert_query('sourcing_suppliers_chemicals', $chemical);
			}
			return true;						
		}
		/* Insert supplier - END */
	}
	
	
	private function get_lastversion_supplier($alias, array $options = array()) {
		global $db;								
		if(isset($options['exclude'])) {
			$exclude_querystring = ' AND cmspid NOT IN ('.implode(',', $options['exclude']).')';
		}
		
		return $db->fetch_assoc($db->query("SELECT title, alias, bodyText,version FROM ".Tprefix."cms_page WHERE alias='".$db->escape_string($alias)."'{$exclude_querystring} ORDER BY version DESC"));
	}
	
	public function edit($page='') {
		global $core,$db;
		// check similar string if major or minor update version.				
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
	
	public function get() {
		return $this->supplier;				
	}


	
	public function get_status() {
		return $this->status;
	}
}
?>