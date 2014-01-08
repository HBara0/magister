<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Entbrandsproducts_class.php
 * Created:        @tony.assaad    Dec 4, 2013 | 1:19:43 PM
 * Last Update:    @tony.assaad    Dec 4, 2013 | 1:19:43 PM
 */

/**
 * Description of Entbrandsproducts_class
 *
 * @author tony.assaad
 */
class Entbrandsproducts {
	private $entbrandproducts = array();

	public function __construct($id, $simple = true) {
		if(isset($id)) {
			$this->read($id, $simple);
		}
	}

	private function read($id, $simple) {
		global $db;
		$query_select = '*';
		if($simple == true) {
			$query_select = 'ebpid,ebid,eptid';
		}
		$this->entbrandproducts = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'entitiesbrandsproducts WHERE ebpid='.intval($id)));
	}

	public static function get_endproducts($id) {
		global $db;

		if(!empty($id)) {
			$query = $db->query('SELECT eptid  FROM '.Tprefix.'entitiesbrandsproducts  WHERE ebid="'.$db->escape_string($id).'"');
			while($rows = $db->fetch_assoc($query)) {
				$endproducts[$rows['eptid']] = new Endproductypes($rows['eptid']);
			}
			return $endproducts;
		}
		return false;
	}

	public static function get_entitiesbrandsproducts_Bybrand($id) {
		global $db;

		if(!empty($id)) {
			$query = $db->query('SELECT ebpid  FROM '.Tprefix.'entitiesbrandsproducts  WHERE ebid="'.$db->escape_string($id).'"');
			while($rows = $db->fetch_assoc($query)) {
				$entbrandsproducts[$rows['ebpid']] = new Entbrandsproducts($rows['ebpid']);
			}
			return $entbrandsproducts;
		}
		return false;
	}

	public static function get_entbrandsproducts() {
		global $db;
		$query = $db->query('SELECT * FROM '.Tprefix.'entitiesbrandsproducts');
		while($rows = $db->fetch_assoc($query)) {
			$entbrandsproducts[$rows['ebpid']] = new Entbrandsproducts($rows['ebpid']);
		}
		return $entbrandsproducts;
	}

	public function get_entitybrand() {
		return new Entbrands($this->entbrandproducts['ebid']);
	}

	public function get_endproduct() {
		return new Endproductypes($this->entbrandproducts['eptid']);
	}

	public function get_createdby() {
		return new Users($this->entbrandproducts['createdBy']);
	}

	public function get_modifiedby() {
		return new Users($this->entbrandproducts['modifiedBy']);
	}

	public function get() {
		return $this->entbrandproducts;
	}

}
?>
