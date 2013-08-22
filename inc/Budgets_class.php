<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: Budgets_class.php
 * Created:        @tony.assaad    Aug 12, 2013 | 2:10:18 PM
 * Last Update:    @tony.assaad    Aug 13, 2013 | 4:15:18 PM
 */

/**
 * Description of Budgets_class
 *
 * @author tony.assaad
 */
class Budgets {
	public function __construct($id = '', $simple = false, $additionaldata = false) {
		if(isset($id) && !empty($id)) {
			$this->budget = $this->read($id, $simple, $additionaldata);
		}
	}

	private function read($id, $simple = false, $additionaldata = false) {
		global $db;

		if(empty($id)) {
			return false;
		}

		$query_select = '*';
		if($simple == true) {
			$query_select = 'year, description';
		}
		if($additionaldata == true) {
			return $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."budgeting_budgets  bd
									JOIN ".Tprefix."budgeting_budgets_lines bdl ON(bd.bid=bdl.bid)
									WHERE bid=".$db->escape_string($id)), 'budgetproducts');
		}
		else {
			return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."budgeting_budgets WHERE bid=".$db->escape_string($id)));
		}
	}

	private function budget_exist($data) {
		global $db;
		if(value_exists('budgeting_budgets', 'bid', $this->budget['bid'], 'affid='.$data['affid'], ' AND spid='.$data['spid'].'AND year='.$data['year'])) {
			return $this->budget['bid'];
		}
		return false;
	}

	private function available_budget() {
		global $db;
		$budget_existquery = $db->query('SELECT * FROM '.Tprefix.'budgeting_budgets');
		if($db->num_rows($budget_existquery) > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	public function populate_years($data = array()) {
		global $db;
		$budget_existquery = $db->query('SELECT year FROM '.Tprefix.'budgeting_budgets WHERE spid='.$data['spid'].' AND affid='.$data['affid'].' AND isLocked=0');
		if($db->num_rows($budget_existquery) > 0) {
			$years = $db->fetch_assoc($budget_existquery);
		}
		else {
			if($this->populate_nextyear($data)) {
				return $years + 1;
			}
		}
		return $years;
	}

	private function populate_nextyear($data = array()) {
		global $db;
		$budget_nextyear = $db->query('SELECT year FROM '.Tprefix.'budgeting_budgets WHERE spid='.$data['spid'].' AND affid='.$data['affid'].' AND year = year(CURDATE()) + 1');
		if($db->num_rows($budget_nextyear) < 0) {
			return true;
		}
		else {
			return false;
		}
	}

	public function save_budget($budgetline_data = array(), $budgetdata = array()) {
		global $db, $core;
		/* check available budget */
		if(is_array($budgetdata)) {
			if(!$this->available_budget()) {
				$budget_data = array('identifier' => substr(uniqid(time()), 0, 10),
						'year' => $budgetdata['year'],
						'affid' => $budgetdata['affid'],
						'spid' => $budgetdata['spid'],
						'currency' => $budgetdata['currency'],
						'createdBy' => $core->user['uid'],
						'createdOn' => TIME_NOW
				);

				$insertquery = $db->insert_query('budgeting_budgets', $budget_data);
				if($insertquery) {
					$this->budget['bid'] = $db->last_id();
					$this->errorcode = 0;
				}
			}
			else {
				echo $this->budget['bid'];
				$budgetline = new BudgetLines();

				foreach($budgetline_data as $data) {
					$budgetline->save_budgetline($data, $this->budget['bid']);
				}
			}
		}
	}

	/* function return object Type --START */
	public function get_supplier() {
		return new Entities($this->budget['spid']);
	}

	public function get_affiliate() {
		return new Affiliates($this->budget['affid']);
	}

	public function get_currency() {
		return new Currencies($this->budget['originalCurrency']);
	}

	public function get_CreateUser() {
		return new Users($this->budget['createdBy']);
	}

	public function get_ModifyUser() {
		return new Users($this->budget['modifiedBy']);
	}

	public function get_FinalizeUser() {
		return new Users($this->budget['finalizedBy']);
	}

	public function get_LockUser() {
		return new Users($this->budget['lockedBy']);
	}

	/* function return object Type --END */
	public function get() {
		return $this->budget;
	}

	public function get_errorcode() {
		return $this->errorcode;
	}

	public function get_budgetLines() {
		global $db;

		if(isset($this->budget['bid']) && !empty($this->budget['bid'])) {
			$budgetline_queryid = $db->query("SELECT blid
												FROM ".Tprefix."budgeting_budgets_line
												WHERE bid=".$this->budget['bid']."");
			if($db->num_rows($budgetline_queryid) > 0) {
				while($budgetline_data = $db->fetch_assoc($budgetline_queryid)) {

					$budgetline = new BudgetLines_class($budgetline_data['blid']);
					$budgetline_details[$budgetline_data['blid']] = $budgetline->get();
				}
				return $budgetline_details;
			}
		}
	}

}
/* Budgeting Line Class --START */

class BudgetLines {
	private $budgetline;

	public function __construct($budgetlineid = '') {
		$this->read($budgetlineid);
		$this->budgetlineid = $budgetlineid;
	}

	private function read($budgetlineid) {
		global $db;
		if(isset($budgetlineid) && !empty($budgetlineid)) {
			$this->budgetline = $db->fetch_assoc($db->query("SELECT bdl.*, bd.bid 
														FROM ".Tprefix."budgeting_budgets bd
														JOIN ".Tprefix."budgeting_budgets_lines bdl ON (bd.bid=bdl.bid)
														WHERE bdl.blid='".$db->escape_string($budgetlineid)."'"));
		}
	}

	public function save_budgetline($budgetline_data = array(), $budget_id) {
		global $db, $core;
		if(is_array($budgetline_data)) {
			$budgetline_data['bid'] = $budget_id;
			unset($budgetline_data['companyName']);
			print_r($budgetline_data);
			$insertquery = $db->insert_query('budgeting_budgets_lines', $budgetline_data);
		}
	}

	public function get_customer() {
		return new Entities($this->budgetline['cid']);
	}

	public function get_product() {
		return new Product($this->budgetline['pid']);
	}

	public function get_saletype() {
		return $this->budgetline['saletype'];
	}

	public function get_CreateUser() {
		return new Users($this->budgetline['createdBy']);
	}

	public function get_ModifyUser() {
		return new Users($this->budgetline['modifiedBy']);
	}

	public function get() {
		return $this->budgetline;
	}

}
/* Budgeting Line Class --END */
?>
