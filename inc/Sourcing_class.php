<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Sourcing Class
 * $id: Sourcing_class.php
 * Created:			@tony.assaad	October 08, 2012 | 10:53 PM
 * Last Update: 	@tony.assaad	October 08, 2012 | 10:59  AM
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
		unset($this->supplier['chemicalproducts']);
		
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
		$this->supplier['website'] = $core->validtate_URL($this->supplier['website']);
		$this->supplier['createdBy'] = $core->user['uid'];
		$this->supplier['dateCreated'] = TIME_NOW;
	
	print_r($this->chemical['name']);
		/* Insert supplier - START */
		//if(is_array($this->supplier)) {
			$query = $db->insert_query('sourcing_suppliers', $this->supplier);
			if($query) {
				$this->status = 0;
				$ssid = $db->last_id();
				$log->record($this->supplier['ssid']);
			}
			foreach($this->chemical['name'] as  $chemical) {print_r($chemical);
				$db->insert_query('sourcing_suppliers_chemicals', $chemical);
			}
			return true;						
		//}
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
			$query_select = 'cmspid, title, alias';	
		}

		return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."cms_pages WHERE cmspid=".$db->escape_string($id)));		
	}
	
	public function get() {
		return $this->page;				
	}


	
	public function get_status() {
		return $this->status;
	}
}
?>