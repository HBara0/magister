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
			$query_select = 'ebpid,abid,eptid';
		}
		$this->entbrandproducts = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'entitiesbrandsproducts WHERE ebpid='.intval($id)));
	}

	public function get_entitybrand() {
		return new Entbrands($this->entbrandproducts['abid']);
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
