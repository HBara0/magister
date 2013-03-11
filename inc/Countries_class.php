<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * Countries Class
 * $id: Countries_class.php
 * Created:        @zaher.reda    Mar 8, 2013 | 4:56:25 PM
 * Last Update:    @zaher.reda    Mar 8, 2013 | 4:56:25 PM
 */

class Countries {
	private $country = array();
	
	public function __construct($id) {
		if(empty($id)) {
			return false;
		}
		$this->read($id);
	}
	
	private function read($id) {
		global $db;
		$this->country = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.'countries WHERE coid='.intval($id)));
		print_r($this->country);
	}
	
	public function get_maincurrency() {
		return new Currencies($this->country['mainCurrency']);
	}
	
	public function get_affiliate() {
		return new Affiliates($this->country['affid']);
	}
	
	public function get() {
		return $this->country;
	}
}
?>
