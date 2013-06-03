<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Leavetypes_class.php
 * Created:        @tony.assaad    May 29, 2013 | 3:39:18 PM
 * Last Update:    @tony.assaad    May 29, 2013 | 3:39:18 PM
 */

/**
 * Description of Leavetypes_class
 *
 * @author tony.assaad
 */
class Leavetypes {
	public function __construct($ltid = 0, $simple = true) {
		global $db;
		if(isset($ltid) && !empty($ltid)) {
			$this->leavetype = $this->read($ltid, $simple);
		}
	}

	public function has_expenses($id = '') {
		global $db;
		if(!empty($this->leavetype['ltid'])) {
			$id = $this->leavetype['ltid'];
		}
		else {
			$id = $id;
		}
		if(value_exists('attendance_leavetypes_exptypes', 'ltid', $db->escape_string($id))) {
			return true;
		}
		else {
			return false;
		}
	}

	public function get_expenses($id = '') {
		global $db;
		if(!empty($this->leavetype['ltid'])) {
			$id = $this->leavetype['ltid'];
		}
		else {
			$id = $id;
		}
		$leavetypeexp_query = $db->query("SELECT * FROM ".Tprefix."attendance_leavetypes_exptypes where ltid=".$db->escape_string($id));

		while($leavetype_expenses = $db->fetch_assoc($leavetypeexp_query)) {
			$leavetypeexpense[$leavetype_expenses['alteid']] = $leavetype_expenses;
		}
		if(is_array($leavetypeexpense)) {
			return $leavetypeexpense;
		}
	}

	public function parse_expensesfields(array $expensestype, array $leaveexpences, $attribute) {
		global $db, $template;
		if($expensestype['isRequired'] == 1) {
			$expenses_output_required = '<span class="red_text">*</span>';
			$expenses_output_requiredattr = ' required="required"';
		}

		if(isset($expensestype['title'])) {

			//$expensestitle_output = '<div style="display:inline-block;width:25%;">'.$expensestype['title'].'</div>';
			//$expenses_fields = '<div style="display:inline-block; padding:5px; text-align:left; width:30%;"> <input type="text"  tabindex="'.$tabindex.'" accept="numeric" size="7"  value="'.$leaveexpences[$expensestype['alteid']]['expectedAmt'].'" id="expenses_'.$expensestype['title'].'['.$expensestype['alteid'].']" name="leaveexpenses['.$expensestype['alteid'].'][expectedAmt]" '.$expenses_output_requiredattr.' /> <select name="leaveexpenses['.$expensestype['alteid'].'][currency]"><option value="USD">USD</option></select>'.$expenses_output_required.'</div>';
		}
		//$expenses_output = $expensestitle_output.$expenses_fields.$expenses_fields_currency;
		eval("\$requestleaveexpenses = \"".$template->get('attendance_requestleave_expenses')."\";");
		//return '<div id="attendancecontainer_'.$expensestype['alteid'].'" style="display:inline-block; width:45%;" >'.$expenses_output.'</div>';
		return $requestleaveexpenses;
	}

	private function read($id, $simple = true) {
		global $db;
		if(empty($id)) {
			return false;
		}
		$query_select = '*';
		if($simple == true) {
			$query_select = 'ltid, name,title,description,toApprove';
		}
		return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."leavetypes WHERE ltid=".$db->escape_string($id)));
	}

}
?>
