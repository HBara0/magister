<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * Affiliates Class
 * $id: Affiliates_class.php
 * Created:        @zaher.reda    Mar 8, 2013 | 4:51:09 PM
 * Last Update:    @zaher.reda    Mar 8, 2013 | 4:51:09 PM
 */

class Affiliates {
	private $affiliate = array();
	
	public function __construct($id, $simple = TRUE) {
		if(empty($id)) {
			return false;
		}
		$this->read($id, $simple);
	}
	
	private function read($id, $simple = TRUE) {
		global $db;
		
		$query_select = 'affid, name, legalName, country';
		if($simple == false) {
			$query_select = '*';
		}
		$this->affiliate = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'affiliates WHERE affid='.intval($id)));
	}
	
	public function get_country() {
		return new Countries($this->affiliate['country']);
	}
	
	public function get_supervisor() {
		return new Users($this->affiliate['supervisor']);
	}
	
	public function get_generalmanager() {
		return new Users($this->affiliate['generalManager']);
	}
	
	public function get_hrmanager() {
		return new Users($this->affiliate['supervisor']);
	}
	
	public function get() {
		return $this->affiliate;
	}
}
?>
