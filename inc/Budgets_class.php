<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Budgets_class.php
 * Created:        @tony.assaad    Aug 12, 2013 | 2:10:18 PM
 * Last Update:    @tony.assaad    Aug 13, 2013 | 4:15:18 PM
 */

class Budgets {
	public function __construct($id = '', $simple = false, $budgetdata = '', $isallbudget = false) {
		if(isset($id) && !empty($id) || !empty($isallbudget)) {
			$this->budget = $this->read($id, $simple, $isallbudget);
		}
	}

	private function read($id, $simple = false, $isallbudget = false) {
		global $db;
		if(empty($id) && empty($isallbudget)) {
			return false;
		}

		$query_select = '*';
		if($simple == true) {
			$query_select = 'year, description';
		}
		if($isallbudget == true) {
			$queryall = $db->query("SELECT DISTINCT(year) ,bid,identifier,description,affid,spid,currency,isLocked,isFinalized,finalizedBy,status,createdOn,createdBy,modifiedBy 
									FROM ".Tprefix."budgeting_budgets GROUP BY year ORDER BY year DESC  ");
			if($db->num_rows($queryall) > 0) {
				while($budget = $db->fetch_assoc($queryall)) {
					$allbudgets[] = $budget;
				}
			}
			return $allbudgets;
		}
		else {
			return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."budgeting_budgets WHERE bid=".$db->escape_string($id)));
		}
	}

	private function budget_exist($data) {
		global $db;
		if(value_exists('budgeting_budgets', 'bid', $data['bid'], 'affid='.$data['affid'].' AND spid='.$data['spid'])) {
			return true;
		}
		return false;
	}

	private function available_budget($data) {
		global $db;
		if(isset($data['affid'], $data['spid'], $data['year']) && !is_empty($data['affid'], $data['spid'], $data['year'])) {
			$budget_existquery = $db->query('SELECT bid FROM '.Tprefix.'budgeting_budgets WHERE affid='.$data['affid'].' AND spid='.$data['spid']);
			if($db->num_rows($budget_existquery) > 0) {
				return true;
			}
			else {
				return false;
			}
		}
	}

	public function populate_budgetyears($data = array()) {
		global $db;
		$budget_yearquery = $db->query('SELECT bid,year FROM '.Tprefix.'budgeting_budgets WHERE spid='.$data['spid'].' AND affid='.$data['affid'].' AND isLocked=0 ORDER BY year DESC');
		if($db->num_rows($budget_yearquery) > 0) {
			while($budget_year = $db->fetch_assoc($budget_yearquery)) {
				$budget_years[] = $budget_year['year'];
			}
		}
		//get next year and return budget
		$next_budgetyear = date('Y', strtotime('+1 year'));
		$budget_nextyearquery = $db->query('SELECT bid,year,isLocked FROM '.Tprefix.'budgeting_budgets WHERE spid='.$data['spid'].' 
												AND affid='.$data['affid'].' AND year='.$next_budgetyear.' ORDER BY year DESC');
		if($db->num_rows($budget_nextyearquery) > 0) {
			while($budget_nextyear = $db->fetch_assoc($budget_nextyearquery)) {
				if($budget_nextyear['isLocked'] == 0) {
					$budget_years[] = $budget_nextyear['year'];
				}
			}
		}
		else {
			$budget_years[] = $next_budgetyear;
		}

		if(is_array($budget_years)) {
			$budget_years = array_unique($budget_years);
		}
		return $budget_years;
	}

//	private function populate_nextyear($data = array()) {
//		global $db;
//		$budget_nextyear = $db->query('SELECT year FROM '.Tprefix.'budgeting_budgets WHERE spid='.$data['spid'].' AND affid='.$data['affid'].' AND year = year(CURDATE()) + 1');
//		if($db->num_rows($budget_nextyear) < 0) {
//			return true;
//		}
//		else {
//			return false;
//		}
//	}

	public function save_budget($budgetline_data = array(), $budgetdata = array()) {
		global $db, $core;
		/* check available budget */
		if(is_array($budgetdata)) {
			$this->budget['bid'] = $budgetdata['bid'];
			if(!$this->budget_exist($budgetdata)) {
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
					$this->save_budgetlines($budgetline_data, $this->budget['bid']);
				}

				//$this->errorcode = 2;
				//return false;
			}
			else {
				$this->save_budgetlines($budgetline_data, $this->budget['bid']);
			}
		}
	}

	private function save_budgetlines($budgetline_data = array(), $bid) {
		unset($budgetline_data['customerName']);	
			echo '<hr>';
		foreach($budgetline_data as $blid => $data) {

			if(isset($data['blid']) && !empty($data['blid'])) {
				$budgetline = new BudgetLines($data['blid']);
			}
			else {
				$budgetline = new BudgetLines();
			}

			if(empty($data['pid']) || empty($data['cid'])) {
				//$this->errorcode = 1;
				continue;
			}
//	
//			elseif(empty($data['pid']) && empty($data['cid'])) {		
//				$budgetline->delete();
//				continue;
//			}
			if(isset($data['blid']) && !empty($data['blid'])) {
				$budgetline->update($data);
				$this->errorcode = 3;
			}
			else {
				$budgetline->create($data, $bid);
			}
		}
	}

//	public function get_budgetbyspecificdata($data) {
//		global $db;
//
//		if(is_array($data)) {
//			if(isset($data['affilliates']) && !empty($data['affilliates'])) {
//				$where = ' WHERE affid in('.implode(',', $data['affilliates']).')';
//			}
//
//			if(isset($data['affilliates'], $data['suppliers'], $data['managers'], $data['segments'], $data['years']) && !is_empty($data['affilliates'], $data['suppliers'], $data['managers'], $data['segments'], $data['years'])) {
//				return $this->get_budget_byinfo($data);
//			}
//
//			$budget_bydataquery = $db->query("SELECT bid FROM ".Tprefix."budgeting_budgets ".$where);
//			if($db->num_rows($budget_bydataquery) > 0) {
//				while($budget_bydata = $db->fetch_assoc($budget_bydataquery)) {
//					$this->get_budgetLines($budget_bydata['bid']);
////$budget_report[$budget_bydata['bid']] = $budget_bydata;
//				}
//				//return $budget_report;
//			}
//		}
//	}

	public function get_budgets_byinfo($data = array()) {
		global $db;

		if(isset($data['affilliates'], $data['suppliers'], $data['years']) && !is_empty($data['affilliates'], $data['suppliers'], $data['years'])) {
			$budget_reportquery = $db->query("SELECT bid FROM ".Tprefix."budgeting_budgets 
														  WHERE  year in(".$db->escape_string(implode(',', $data['years'])).") 
														  AND affid in(".$db->escape_string(implode(',', $data['affilliates'])).") 
														  AND spid in(".$db->escape_string(implode(',', $data['suppliers'])).")");
		}

		if(isset($data['affilliates'], $data['suppliers'], $data['managers'], $data['segments'], $data['years']) && !empty($data['affilliates']) && !empty($data['suppliers']) && !empty($data['managers']) && !empty($data['years'])) {
			$budget_reportquery = $db->query("SELECT bid FROM ".Tprefix."budgeting_budgets 
														  WHERE  year in(".$db->escape_string(implode(',', $data['years'])).") 
														  AND affid in(".$db->escape_string(implode(',', $data['affilliates'])).") 
														  AND spid in(".$db->escape_string(implode(',', $data['suppliers'])).") 
														  AND createdBy in(".$db->escape_string(implode(',', $data['managers'])).")");
		}

		while($budget_reportids = $db->fetch_assoc($budget_reportquery)) {
			$budgetreport[$budget_reportids['bid']] = $budget_reportids['bid'];
		}
		return $budgetreport;
	}

	public function get_budgetbydata($data) {
		global $db;
		if(is_array($data)) {
			$budget_bydataquery = $db->query("SELECT * FROM ".Tprefix."budgeting_budgets WHERE affid='".$data['affid']."' AND spid='".$data['spid']."' AND year='".$data['year']."'");
			if($db->num_rows($budget_bydataquery) > 0) {
				while($budget_bydata = $db->fetch_assoc($budget_bydataquery)) {
					$budget_details = $budget_bydata;
				}
				return $budget_details;
			}
		}
	}

	public function read_prev_budgetbydata($data) {
		global $db;
		for($year = $data['year']; $year >= ($data['year'] - 2); $year--) {
			if($year == $data['year']) {
				continue;
			}
			$prev_budget_bydataquery = $db->query("SELECT * FROM ".Tprefix."budgeting_budgets  bd JOIN ".Tprefix."budgeting_budgets_lines bdl ON(bd.bid=bdl.bid) 
													WHERE affid='".$data['affid']."' AND spid='".$data['spid']."' AND year='".$year."'");
			if($db->num_rows($prev_budget_bydataquery) > 0) {
				while($prevbudget_bydata = $db->fetch_assoc($prev_budget_bydataquery)) {
					$prevbudgetline_details[$prevbudget_bydata['cid']][$prevbudget_bydata['pid']][$prevbudget_bydata['bid']] = $prevbudget_bydata;
				}
			}
		}
		return $prevbudgetline_details;
	}

	public function get_budgetLines($bid = '') {
		global $db;
		if(empty($bid)) {
			$bid = $this->budget['bid'];
		}

		if(isset($bid) && !empty($bid)) {

			$budgetline_queryid = $db->query("SELECT *
												FROM ".Tprefix."budgeting_budgets_lines
												WHERE bid in(".$db->escape_string($bid).")");
			if($db->num_rows($budgetline_queryid) > 0) {
				while($budgetline_data = $db->fetch_assoc($budgetline_queryid)) {
					$budgetline = new BudgetLines($budgetline_data['blid']);
					$budgetline_details[$budgetline_data['blid']] = $budgetline->get();
				}
				return $budgetline_details;
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

}
/* Budgeting Line Class --START */

class BudgetLines {
	private $budgetline;

	public function __construct($budgetlineid = '') {
		if(!empty($budgetlineid)) {
			$this->budgetline = $this->read($budgetlineid);
			$this->budgetlineid = $budgetlineid;
		}
	}

	private function read($budgetlineid) {
		global $db;
		if(isset($budgetlineid) && !empty($budgetlineid)) {
			return $db->fetch_assoc($db->query("SELECT bdl.*, bd.bid
														FROM ".Tprefix."budgeting_budgets bd
														JOIN ".Tprefix."budgeting_budgets_lines bdl ON (bd.bid=bdl.bid)
														WHERE bdl.blid='".$db->escape_string($budgetlineid)."'"));
		}
	}

	public function create($budgetline_data = array(), $bid) {
		global $db, $core;
		if(is_array($budgetline_data)) {
			$budgetline_data['bid'] = $bid;
			$budgetline_data['createdBy'] = $core->user['uid'];
			unset($budgetline_data['customerName']);
			$insertquery = $db->insert_query('budgeting_budgets_lines', $budgetline_data);
			if($insertquery) {
				$this->errorcode = 0;
			}
		}
	}

	public function update($budgetline_data = array()) {
		global $db, $core;
		unset($budgetline_data['customerName']);
		$budgetline_data['modifiedBy'] = $core->user['uid'];
		$db->update_query('budgeting_budgets_lines', $budgetline_data, 'blid='.$this->budgetline['blid']);
	}

	public function delete() {
		global $db;
		$db->delete_query('budgeting_budgets_lines', 'blid='.$this->budgetline['blid']);
	}

	public function get_customer() {
		return new Entities($this->budgetline['cid'], '', false);
	}

	public function get_product() {
		return new Products($this->budgetline['pid']);
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
