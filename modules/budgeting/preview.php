<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: preview.php
 * Created:        @tony.assaad    Aug 22, 2013 | 4:17:09 PM
 * Last Update:    @tony.assaad    Aug 22, 2013 | 4:17:09 PM
 */


$session->start_phpsession();
if(!($core->input['action'])) {

	if($core->input['referrer'] == 'generate') {
		$identifier = base64_decode($core->input['identifier']);
		$generate_budget_data = unserialize($session->get_phpsession('generatebudgetdata_'.$identifier));
		$budgetsdata = ($core->input['budget']);
		$aggregate_types = array('affilliates', 'suppliers', 'managers', 'segments', 'years');

		$tools_finalize = "<script language='javascript' type='text/javascript'>$(function(){ $('#approvereport').click(function() { sharedFunctions.requestAjax('post', 'index.php?module=budgeting/preview', 'action=approve&identifier={$session_identifier}', 'approvereport_span', 'approvereport_span');}) });</script>";
		$tools = $tools_finalize.$tool_print;
		eval("\$budgetreport_coverpage = \"".$template->get('budgetreport_coverpage')."\";");


		//foreach($budgetsdata as $budget) {				


		$budget = new Budgets();
		//foreach($budgetsdata[$aggregate_type] as $budgetitem) {
		$budgetreport = $budget->get_budgets_byinfo($budgetsdata);
		if(is_array($budgetreport)) {
			foreach($budgetreport as $budgetid) {
				$budget = new Budgets($budgetid);
				$country = $budget->get_affiliate()->get()['name'];

				$supplier = $budget->get_supplier()->get()['companyName'];
				$manager = $budget->get_CreateUser()->get()['displayName'];

				$budgetitems = $budget->get_budgetLines();
				foreach($budgetitems as $budgetitem) {
					$budget_line = new BudgetLines($budgetitem['blid']);
					$cusomtercountry_id = $budget_line->get_customer($budgetitem['cid'])->get()[country];
					$countries = new Countries($cusomtercountry_id);

					$cusomtercountry = $countries->get()['name'];
					$genericproduct = $budget_line->get_product()->get_generic_product();
					$segment = $budget_line->get_product()->get_segment()['title'];
					$budgetitem['customer'] = $budget_line->get_customer($budgetitem['cid'])->get()['companyName'];
					$budgetitem['product'] = $budget_line->get_product($budgetitem['pid'])->get()['name'];
					eval("\$userbudget_report_row .= \"".$template->get('budgetreport_usersreport_row')."\";");
				}
			}
		}
		else {
			$userbudget_report_row = '<tr><td>'.$lang->na.'</td></tr>';
		}





		eval("\$userbudget_report = \"".$template->get('budgetreport_usersreport')."\";");

		eval("\$budgetingreport = \"".$template->get('budgetreport_report')."\";");
	}




	eval("\$budgetingpreview = \"".$template->get('budgetreport_preview')."\";");
	output_page($budgetingpreview);
}
?>
