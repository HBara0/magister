<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: Endproducttpes_class.php
 * Created:        @tony.assaad    Dec 4, 2013 | 12:29:46 PM
 * Last Update:    @tony.assaad    Dec 4, 2013 | 12:29:46 PM
 */

/**
 * Description of Endproducttpes_class
 *
 * @author tony.assaad
 */
class Endproductypes {
	private $endproduct = array();

	public function __construct($id, $simple = true) {
		if(isset($id)) {
			$this->read($id, $simple);
		}
	}

	private function read($id, $simple) {
		global $db;
		$query_select = '*';
		if($simple == true) {
			$query_select = 'eptid,name,title';
		}
		$this->endproduct = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'endproducttypes WHERE eptid='.intval($id)));
	}

	public function get_application() {
		return new Segapplicationfunctions($this->endproduct['psaid']);
	}

	public function get_createdby() {
		return new Users($this->endproduct['createdBy']);
	}

	public function get_modifiedby() {
		return new Users($this->endproduct['modifiedBy']);
	}

	public static function get_producttype_byname($name) {
		global $db;
		if(!empty($name)) {
			$id = $db->fetch_field($db->query('SELECT eptid FROM '.Tprefix.'endproducttypes WHERE name="'.$db->escape_string($name).'"'), 'eptid');
			if(!empty($id)) {
				return new Endproductypes($id);
			}
		}
		return false;
	}

	public function get() {
		return $this->endproduct;
	}

}
?>
