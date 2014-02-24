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
class Representatives {
	private $representative = array();
	private $errorcode = 0;

	public function __construct($id = '', $simple = false) {
		if(isset($id) && !empty($id)) {
			$this->representative = $this->read($id, $simple);
		}
	}

	private function read($id, $simple) {
		global $db;
		
		$query_select = '*';
		if($simple == true) {
			$query_select = 'rpid, name, email';
		}
		if(!empty($id)) {
			return $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix."representatives WHERE rpid='".$db->escape_string($id)."'"));
		}
	}

	public function get() {
		return $this->representative;
	}

}
?>
