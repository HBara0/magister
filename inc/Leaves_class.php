<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: Leave.php
 * Created:        @tony.assaad    May 29, 2013 | 2:17:27 PM
 * Last Update:    @tony.assaad    May 29, 2013 | 2:17:27 PM
 */

/**
 * Description of Leave
 *
 * @author tony.assaad
 */
class Leaves {
//put your code here
	private $status = 0; //0=No errors;1=Subject missing;2=Entry exists;3=Error saving;4=validation violation
	private $leave = array();

	public function __construct($leavedata = array()) {
		global $db; 
		if(isset($leavedata['lid']) && !empty($leavedata['lid'])) {
			$this->leave = $this->read($leavedata['lid'], $simple);
		}
	}

	public function has_expenses($id = '') {
		global $db;
		if(!empty($this->leave['lid'])) {
			$id = $this->leave['lid'];
		}
		else {
			$id = $id;
		}
		if(value_exists('attendance_leaves_expenses', 'lid', $db->escape_string($id))) {
			return true;
		}
		else {
			return false;
		}
	}

	public function get_expenses($id = '') {
		global $db;
		if(!empty($this->leave['lid'])) {
			$id = $this->leave['lid'];
		}
		else {
			$id = $id;
		}
		$leaveexptype_query = $db->query("SELECT * FROM ".Tprefix."attendance_leaves_expenses where lid=".$db->escape_string($id));

		while($leaveexpenses = $db->fetch_assoc($leaveexptype_query)) {
			$leaveexpense[$leaveexpenses['aleid']] = $leaveexpenses;
		}
		if(is_array($leaveexpense)) {
			return $leaveexpense;
		}
	}

	public function get_expensesdetails($id = '') {
		global $db;
		if(!empty($this->leave['lid'])) {
			$id = $this->leave['lid'];
		}
		else {
			$id = $id;
		}
		$leaveexpdetails_query = $db->query("SELECT alex.alteid,alex.title,ale.expectedAmt,ale.currency, ale.lid AS total
				FROM ".Tprefix."attendance_leaves_expenses ale JOIN ".Tprefix."attendance_leavetypes_exptypes alex ON(alex.alteid=ale.alteid)
				WHERE ale.lid=".$db->escape_string($id));

		while($expensesdetails = $db->fetch_assoc($leaveexpdetails_query)) {
			$expensesdetail[$expensesdetails['alteid']] = $expensesdetails;
		}
		if(is_array($expensesdetail)) {
			// $this->expensesdetails=$expensesdetail;
			return $expensesdetail;
		}
	}

	public function create_expenses($expenses = array()) {
		global $db, $log;

		if(is_array($expenses)) {


			foreach($expenses as $alteid => $expense) {

				if(is_empty($expense['expectedAmt'], $expense['currency'])) {
					$this->status = 1;
					return false;
				}
				$expenses_data = array('alteid' => $alteid,
						'lid' => $this->leave['lid'],
						'expectedAmt' => $expense['expectedAmt'],
						'currency' => $expense['currency'],
						'usdFxrate' => ''
				);
				$query = $db->insert_query('attendance_leaves_expenses', $expenses_data);
				if($query) {
					$log->record($expenses_data);
					$this->status = 0;
				}
			}
		}
	}

	public function update_leaveexpences(array $leaveexpenses_data) {
		global $db;
		if(is_array($leaveexpenses_data)) {
			foreach($leaveexpenses_data as $alteid => $val) {
				$db->update_query('attendance_leaves_expenses', $val, 'alteid='.$db->escape_string($alteid));
			}
		}
	}

	private function read($id, $simple = false) {
		global $db;
		if(empty($id)) {
			return false;
		}
		$query_select = '*';
		if($simple == true) {
			$query_select = 'lid, uid,type, fromDate,toDate';
		}

		return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."leaves WHERE lid=".$db->escape_string($id)));
	}

	public function get_leavetype() {
		return new Leavetypes($this->leave['type']);
	}

	public function get() {
		return $this->leave;
	}


	public function get_status() {
		$this->status;
	}

}
?>
