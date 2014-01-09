<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: Productsegcoord_class.php
 * Created:        @tony.assaad    Dec 4, 2013 | 11:53:25 AM
 * Last Update:    @tony.assaad    Dec 4, 2013 | 11:53:25 AM
 */

/**
 * Description of Productsegcoord_class
 *
 * @author tony.assaad
 */
class ProdSegCoordinators {
	private $prodsegcoordinator = array();

	public function __construct($id, $simple = true) {
		if(isset($id)) {
			$this->read($id, $simple);
		}
	}

	private function read($id, $simple) {
		global $db;
		$query_select = '*';
		if($simple == true) {
			$query_select = 'pscid, psid, uid';
		}
		$this->prodsegcoordinator = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'productsegmentcoordinators WHERE pscid='.intval($id)));
	}
	
	public function get_segment(){
		return new ProductsSegments($this->prodsegcoordinator['psid']);
	}
	
	public function get_coordinator() {
		return new Users($this->prodsegcoordinator['uid']);
	}

	public function get_createdby() {
		return new Users($this->prodsegcoordinator['createdBy']);
	}

	public function get_modifiedby() {
		return new Users($this->prodsegcoordinator['modifiedBy']);
	}

	public function get() {
		return $this->prodsegcoordinator;
	}

}
?>
