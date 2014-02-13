<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: representatives_class.php
 * Created:        @tony.assaad    Feb 10, 2014 | 10:49:58 AM
 * Last Update:    @tony.assaad    Feb 10, 2014 | 10:49:58 AM
 */

/**
 * Description of representatives_class
 *
 * @author tony.assaad
 */
class representatives {
	private $representatives = array();
	private $errorcode = 0;

	public function __construct($id = '', $simple = false) {
		if(isset($id) && !empty($id)) {
			$this->representative = $this->read($id, $simple);
		}
	}

	private function read($id, $simple) {
		global $db;
		if(!empty($id)) {
			return $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."representatives WHERE rpid='".$db->escape_string($id)."'"));
		}
	}

	public function get() {
		return $this->representative;
	}

}
?>
