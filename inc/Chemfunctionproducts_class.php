<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: Chemfunctionproducts_class.php
 * Created:        @tony.assaad    Dec 3, 2013 | 5:09:33 PM
 * Last Update:    @tony.assaad    Dec 3, 2013 | 5:09:33 PM
 */

/**
 * Description of Chemfunctionproducts_class
 *
 * @author tony.assaad
 */
class Chemfunctionproducts {
	private $chemfuntionproducts = array();
	private $segmentapplicationfunction = null;

	public function __construct($id, $simple = true) {
		if(isset($id)) {
			$this->read($id, $simple);
		}
	}

	private function read($id, $simple) {
		global $db;
		$query_select = '*';
		if($simple == true) {
			$query_select = 'cfpid,pid,safid';
		}
		$this->chemfuntionproducts = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'chemfunctionproducts WHERE cfpid='.intval($id)));
	}

	public function get_segapplicationfunction() {
		$this->segmentapplicationfunction = new Segapplicationfunctions($this->chemfuntionproducts['safid']);  /* we store object in the var to avoid multiple instantiation thus will avoid multiple queries */
		return $this->segmentapplicationfunction;
	}

	public function get_segmentapplication() {
		if(is_object($this->segmentapplicationfunction)) {
			return $this->segmentapplicationfunction->get_application();
		}
		else {
			return $this->get_segapplicationfunction()->get_application();
		}
	}

	public function get_chemicalfunction() {
		if(is_object($this->segmentapplicationfunction)) {
			return $this->segmentapplicationfunction->get_function();
		}
		else {
			return $this->get_segapplicationfunction()->get_function();
		}
	}


	public function get_segment() {
		return $this->get_segmentapplication()->get_segment();
	}

	public function get_produt() {
		return new Products($this->chemfuntionproducts['pid']);
	}

	public function get_createdby() {
		return new Users($this->chemfuntionproducts['createdBy']);
	}

	public function get_modifiedby() {
		return new Users($this->chemfuntionproducts['modifiedBy']);
	}

	public function get() {
		return $this->chemfuntionproducts;
	}

}
?>
