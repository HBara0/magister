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

	public function get_users($options = array()) {
		global $db;

		if(is_array($options)) {
			if(isset($options['ismain']) && $options['ismain'] === 1) {
				$query_where_add = ' AND isMain=1';
			}
		}
		$query = $db->query("SELECT DISTINCT(u.uid) 
					FROM ".Tprefix."users u 
					JOIN ".Tprefix."affiliatedemployees a ON (a.uid=u.uid) 
					WHERE a.affid={$this->affiliate['affid']}".$query_where_add." AND u.gid!=7");
		while($user = $db->fetch_assoc($query)) {
			$users = new Users($user['uid']);
			$users_affiliates[$user['uid']] = $users->get();
		}
		return $users_affiliates;
	}

	public function get_suppliers() {
		global $db;
		$additional_where = getquery_entities_viewpermissions('suppliersbyaffid', $this->affiliate['affid']);
		$query = $db->query("SELECT DISTINCT(e.eid) 
					FROM ".Tprefix."entities e 
					LEFT JOIN ".Tprefix."affiliatedentities ae ON (ae.eid=e.eid) 
					WHERE ae.affid={$this->affiliate['affid']} AND type='s'".$additional_where[extra]."");
		while($supplier = $db->fetch_assoc($query)) {
			$suppliers = new Entities($supplier['eid']);
			$suppliers_affiliates[$supplier['eid']] = $suppliers->get();
		}
		return $suppliers_affiliates;
	}

	public function get() {
		return $this->affiliate;
	}

}
?>
