<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: Entitiesbrands_class.php
 * Created:        @tony.assaad    Dec 4, 2013 | 1:09:49 PM
 * Last Update:    @tony.assaad    Dec 4, 2013 | 1:09:49 PM
 */

/**
 * Description of Entitiesbrands_class
 *
 * @author tony.assaad
 */
class Entbrands {
	private $entitiesbrands = array();

	public function __construct($id, $simple = true) {
		if(isset($id)) {
			$this->read($id, $simple);
		}
	}

	private function read($id, $simple) {
		global $db;
		$query_select = '*';
		if($simple == true) {
			$query_select = 'abid,name';
		}
		$this->entitiesbrands = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'entitiesbrands WHERE abid='.intval($id)));
	}

	public function get_entity() {
		return new Entities($this->entitiesbrands['eid']);
	}

	public function get_createdby() {
		return new Users($this->entitiesbrands['createdBy']);
	}

	public function get_modifiedby() {
		return new Users($this->entitiesbrands['modifiedBy']);
	}

	public static function get_entity_byname($name) {
		global $db;

		if(!empty($name)) {
			$id = $db->fetch_field($db->query('SELECT abid FROM '.Tprefix.'entitiesbrands WHERE name="'.$db->escape_string($name).'"'), 'abid');
			if(!empty($id)) {
				return new Entbrands($id);
			}
		}
		return false;
	}

	public function get() {
		return $this->endproduct;
	}

}
?>
