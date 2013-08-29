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
		eval("\$budgetreport_coverpage = \"".$template->get('budgeting_budgetreport_coverpage')."\";");

		//foreach($budgetsdata as $budget) {				

		$budget_genobj = new Budgets();
		//foreach($budgetsdata[$aggregate_type] as $budgetitem) {
		$budgets = $budget_genobj->get_budgets_byinfo($budgetsdata);
		if(is_array($budgets)) {
			foreach($budgets as $budgetid) {
				$budget_obj = new Budgets($budgetid);
				$budget['country'] = $budget_obj->get_affiliate()->get()['name'];

				$budget['supplier'] = $budget_obj->get_supplier()->get()['companyName'];
				$budget['manager'] = $budget_obj->get_CreateUser()->get()['displayName'];

				$budgetlines = $budget_obj->get_budgetLines();
				foreach($budgetlines as $budgetline) {
					$budgetline_obj = new BudgetLines($budgetline['blid']);
					$countries = new Countries($budgetline_obj->get_customer($budgetline['cid'])->get()['country']);

					$budgetline['uom'] = 'Kg';
					$budgetline['cusomtercountry'] = $countries->get()['name'];
					$budgetline['genericproduct'] = $budgetline_obj->get_product()->get_generic_product();
					$budgetline['segment'] = $budgetline_obj->get_product()->get_segment()['title'];
					$budgetline['customer'] = $budgetline_obj->get_customer($budgetline['cid'])->get()['companyName'];
					$budgetline['product'] = $budgetline_obj->get_product($budgetline['pid'])->get()['name'];
					eval("\$budget_report_row .= \"".$template->get('budgeting_budgetrawreport_row')."\";");
				}
			}
		}
		else {
			$budget_report_row = '<tr><td>'.$lang->na.'</td></tr>';
		}
		eval("\$budgeting_budgetrawreport = \"".$template->get('budgeting_budgetrawreport')."\";");
	}

	eval("\$budgetingpreview = \"".$template->get('budgeting_budgetreport_preview')."\";");
	output_page($budgetingpreview);
}
?>
