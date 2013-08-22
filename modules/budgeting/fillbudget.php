<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: create.php
 * Created:        @tony.assaad    Aug 12, 2013 | 3:28:10 PM
 * Last Update:    @tony.assaad    Aug 12, 2013 | 3:28:10 PM
 * 
 */
if(!defined("DIRECT_ACCESS")) {
	die("Direct initialization of this file is not allowed.");
}
if($core->usergroup['canUseBudgeting'] == 0) {
	error($lang->sectionnopermission);
}
$session->start_phpsession();

if(!$core->input['action']) {

	if($core->input['stage'] == 'fillbudgetline') {
		$budget_data = unserialize($session->get_phpsession('budgetdata'));
		$affiliate = new Affiliates($budget_data['affid']);
		$affiliate_name = $affiliate->get()['name'];

		$supplier = new Entities($budget_data['spid']);
		$supplier_name = $supplier->get()['companyName'];

		$saletypes = explode(';', $core->settings['saletypes']);
		foreach($saletypes as $key => $val) {
			$saletypes[$val] = ucfirst($val);
			unset($saletypes[$key]);
		}

		for($rowid = 1; $rowid <= 4; $rowid++) {
			$saletype_selectlist = parse_selectlist('budgetline['.$rowid.'][saleType]', 0, $saletypes, $budgetline['saleType']);
			eval("\$budgetlinesrows .= \"".$template->get('budgeting_fill_lines')."\";");
		}

		$addmore_customers = '';
		$addmore_customers = '<img src="images/add.gif" id="addmore_budgetlines" alt="'.$lang->add.'">';

		eval("\$fillbudget = \"".$template->get('budgeting_fill')."\";");
		output_page($fillbudget);
	}
}
elseif($core->input['action'] == 'do_perform_fillbudget') {
	$budget_data = unserialize($session->get_phpsession('budgetdata'));

	if(is_array($core->input['budgetline'])) {
		$budget = new Budgets();
		$budget->save_budget($core->input['budgetline'], $budget_data);
	}
	switch($budget->get_errorcode()) {
		case 0:
			output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
			break;
	}
}
?>
