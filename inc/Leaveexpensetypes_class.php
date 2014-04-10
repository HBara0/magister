<?php
/*
 * Copyright � 2014 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: Leaves_expenses.php
 * Created:        @tony.assaad    Apr 9, 2014 | 2:38:39 PM
 * Last Update:    @tony.assaad    Apr 9, 2014 | 2:38:39 PM
 */

/**
 * Description of Leaves_expenses
 *
 * @author tony.assaad
 */
class Leaveexpensetypes {
	private $expencetypes = array();

	public function __construct($id = '', $simple = true) {
		if(isset($id) && !empty($id)) {
			$this->expencetypes = $this->read($id, $simple);
		}
	}

	private function read($id, $simple = true) {
		global $db;
		if(empty($id)) {
			return false;
		}
		$query_select = '*';
		if($simple == true) {
			$query_select = 'aletid,name,title, title AS name';
		}
		return $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'attendance_leaveexptypes WHERE aletid='.$db->escape_string($id)));
	}

	public function get() {
		return $this->expencetypes;
	}

//	public function parse_link($attributes_param = array('target' => '_blank'), $options = array()) {
//		if(is_array($attributes_param)) {
//			foreach($attributes_param as $attr => $val) {
//				$attributes .= $attr.' "'.$val.'"';
//			}
//		}
//
//		if(!isset($options['outputvar'])) {
//			$options['outputvar'] = 'name';
//		}
//
//		return '<a href="index.php?module=attendance/listleaves" '.$attributes.'>'.$this->expencetypes[$options['outputvar']].'</a>';
//	}

}