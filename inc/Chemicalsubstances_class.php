<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: Chemicalsubstances_class.php
 * Created:        @tony.assaad    Dec 4, 2013 | 11:39:58 AM
 * Last Update:    @tony.assaad    Dec 4, 2013 | 11:39:58 AM
 */

/**
 * Description of Chemicalsubstances_class
 *
 * @author tony.assaad
 */
class Chemicalsubstances {
	private $chemicalsubstances = array();

	public function __construct($id, $simple = false) {
		if(isset($id)) {
			$this->read($id, $simple);
		}
	}

	private function read($id, $simple) {
		global $db;
		$query_select = '*';
		if($simple == true) {
			$query_select = 'csid,casNum';
		}
		$this->chemicalsubstances = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'chemicalsubstances WHERE csid='.intval($id)));
	}

	public static function get_chemical_byname($chemname) { /* return object of chemi */
		global $db;
		if(!empty($chemname)) {
			$id = $db->fetch_field($db->query('SELECT csid FROM '.Tprefix.'chemicalsubstances WHERE name="'.$db->escape_string($name).'"'), 'csid');
			if(!empty($id)) {
				return new Chemicalsubstances($id);
			}
		}
	}

	public static function get_chemicalsubstances() {
		global $db;
		$query = $db->query("SELECT csid  FROM ".Tprefix."chemicalsubstances LIMIT 0,5");
		if($db->num_rows($query) > 0) {
			while($rowschcmicals = $db->fetch_assoc($query)) {
				$chemical_substances[$rowschcmicals['csid']] = new Chemicalsubstances($rowschcmicals['csid']);
			}
			return $chemical_substances;
		}
		return false;
	}

	public function get() {
		return $this->chemicalsubstances;
	}

}
?>
