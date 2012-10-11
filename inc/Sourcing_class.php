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
	
	public function get_potential_supplier() {
		global $db,$core;
		$sort_query = 'ORDER BY ss.dateCreated ASC';
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
			$attributes_filter_options['title'] = array('title' => 'hv.');
			
			if($attributes_filter_options['title'][$core->input['filterby']] == 'int') {
				$filter_value = ' = "'.$db->escape_string($core->input['filtervalue']).'"';
			}
			else
			{
				$filter_value = ' LIKE "%'.$db->escape_string($core->input['filtervalue']).'%"';
			}

			$filter_where = ' WHERE '.$db->escape_string($attributes_filter_options['title'][$core->input['filterby']].$core->input['filterby']).$filter_value;
		}	
		/* if no permission person should only see suppliers who work in the same segements he/she is working in --START*/		
			if($core->usergroup['sourcing_canManageEntries'] == 0) { 
				$user_suppliers_id = implode(',',$core->user['suppliers']['eid']);
				$join_employeessegments = "JOIN ".Tprefix."employeessegments es on es.psid = ssp.psid and es.uid=".$core->user['uid']."";
					}
			/* person should only see suppliers who work in the same segements he/she is working in --END*/

				else
				{		
					/*Return All  potentials suppliers--START*/
					$join_employeessegments = '';
					/*Return All  potentials suppliers --END*/
				}				
		
				$suppliers_query = $db->query("SELECT ps.title,ssp.psid,s.ssid,s.companyName,s.type,s.businessPotential,co.name as country FROM ".Tprefix."sourcing_suppliers_productsegments  ssp 
												JOIN ".Tprefix."productsegments ps ON(ps.psid=ssp.psid)
												JOIN ".Tprefix."countries co
												JOIN ".Tprefix."sourcing_suppliers_activityareas ssa ON(ssa.coid=co.coid)
												{$join_employeessegments}
												JOIN ".Tprefix."sourcing_suppliers s on s.ssid= ssp.ssid
												{$filter_where}
												");

				if($db->num_rows($suppliers_query) > 0) {
					while($suppliers = $db->fetch_assoc($suppliers_query)) {
						$potential_suppliers[$suppliers['ssid']]= $suppliers;
					}
				}		
		return $potential_suppliers;
		}
	
	public function get() {
		return $this->supplier;				
	}

	
	public function get_status() {
		return $this->status;
	}
}
?>