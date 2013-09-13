<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: create.php
 * Created:        @tony.assaad    Aug 12, 2013 | 3:28:10 PM
 * Last Update:    @tony.assaad    Aug 22, 2013 | 3:28:10 PM
 * 
 */
if(!defined("DIRECT_ACCESS")) {
	die("Direct initialization of this file is not allowed.");
}
if($core->usergroup['canUseBudgeting'] == 0) {
	error($lang->sectionnopermission);
}
$session->start_phpsession();

//if($core->input['action']) { 

if($core->input['stage'] == 'fillbudgetline') {
	$session_identifier = $db->escape_string(base64_decode($core->input['budget']['sessionidentifier']));
	$session->set_phpsession(array('budgetdata_'.$sessionidentifier => serialize($core->input['budget'])));
	$budget_data = unserialize($session->get_phpsession('budgetdata_'.$session_identifier));

	$affiliate = new Affiliates($budget_data['affid']);
	$affiliate_name = $affiliate->get()['name'];
	$supplier = new Entities($budget_data['spid']);
	$supplier_name = $supplier->get()['companyName'];
	$budget = new Budgets();
	$currentbudget = $budget->get_budgetbydata($budget_data);
	$budgetlines = $budget->get_budgetLines($currentbudget['bid']);
	$session->set_phpsession(array('budgetmetadata_'.$currentbudget['identifier'] => serialize($currentbudget)));

	$saletypes = explode(';', $core->settings['saletypes']);
	foreach($saletypes as $key => $val) {
		$saletypes[$val] = ucfirst($val);
		unset($saletypes[$key]);
	}
		$affiliate_currency = $affiliate->get_country()->get()['mainCurrency'];
		$currencies = get_specificdata('currencies', array('numCode', 'alphaCode'), 'numCode', 'alphaCode', array('by' => 'alphaCode', 'sort' => 'ASC'), 1, 'numCode='.$affiliate_currency);
		if(is_array($currencies)) {
			 $budget_currencylist.= "<option value=''></option> <option value='{$currencies[$affiliate_currency]}'>{$currencies[$affiliate_currency]}</option>";
		}
	/* check whether to display existing budget Form or display new one  */
	if(is_array($budgetlines)) {
		$core->input['identifier'] = base64_encode($currentbudget['identifier']);
		$core->input['bid'] = $currentbudget['bid'];
		$rowid = 0;
		foreach($budgetlines as $blid => $budgetline) {
			/* Get Products name from object */
			$rowid++;
			$product = new Products($budgetline['pid']);
			$budgetline['productname'] = $product->get()['name'];
			if(isset($budgetline['cid']) && !empty($budgetline['cid'])) {
				$required = 'required="required"';
			}
			/* Get Customer name from object */
			$customer = new Entities($budgetline['cid']);
			$budgetline['customerName'] = $customer->get()['companyName'];

			$saletype_selectlist = parse_selectlist('budgetline['.$rowid.'][saleType]', 0, $saletypes, $budgetline['saleType']);
			eval("\$budgetlinesrows .= \"".$template->get('budgeting_fill_lines')."\";");
		}
	}
	else {
		$previous_budget = $budget->read_prev_budgetbydata($budget_data);

		$rowid = 0;
		if(is_array($previous_budget)) {
			foreach($previous_budget as $bid => $previous_budgetdetials) {
				$core->input['bid'] = $bid;
				$budgetlines_data = $budget->get_budgetLines($previous_budgetdetials[bid]);
				$previous_year = $previous_budgetdetials[year];

				foreach($budgetlines_data as $blid => $budgetline) {
					$previous_yearsqty = '';
					$rowid++;
					$budgetline_output['Quantity'] = $budgetline['Quantity'];
					$budgetline_output['ammount'] = $budgetline['ammount'];
					$budgetline_output['income'] = $budgetline['income'];
					unset($budgetline['Quantity'], $budgetline['income'], $budgetline['ammount']);
					/* Get Products name from object */
					$product = new Products($budgetline['pid']);
					$budgetline['productname'] = $product->get()['name'];
					if(isset($budgetline[$rowid]['cid']) && !empty($budgetline[$rowid]['cid'])) {
						$required = 'required="required"';
					}
					/* Get Customer name from object */
					$customer = new Entities($budgetline['cid']);
					$budgetline['customerName'] = $customer->get()['companyName'];
					$previous_yearsqty = '<span style="display:block;"> '.$previous_year.' : '.$budgetline_output['Quantity'].'</span>';
					$previous_yearsamount = '<span style="display:block;"> '.$previous_year.' : '.$budgetline_output['ammount'].'</span>';
					$previous_yearsincome = '<span style="display:block;"> '.$previous_year.' : '.$budgetline_output['income'].'</span>';

					$saletype_selectlist = parse_selectlist('budgetline['.$rowid.'][saleType]', 0, $saletypes, $budgetline['saleType']);
					eval("\$budgetlinesrows .= \"".$template->get('budgeting_fill_lines')."\";");
					//}
				}
			}
			//for($rowid = 1; $rowid <= 4; $rowid++) {
			//}
		}
		eval("\$fillbudget = \"".$template->get('budgeting_fill')."\";");
		output_page($fillbudget);
	}
}
elseif($core->input['action'] == 'ajaxaddmore_budgetlines') {
	$rowid = $db->escape_string($core->input['value']) + 1;
	$saletypes = explode(';', $core->settings['saletypes']);
	foreach($saletypes as $key => $val) {
		$saletypes[$val] = ucfirst($val);
		unset($saletypes[$key]);
	}
	$saletype_selectlist = parse_selectlist('budgetline['.$rowid.'][saleType]', 0, $saletypes, $budgetline['saleType']);
	eval("\$budgetlinesrows = \"".$template->get('budgeting_fill_lines')."\";");
	echo $budgetlinesrows;
}

if($core->input['action'] == 'do_perform_fillbudget') {
	$budget_data = unserialize($session->get_phpsession('budgetdata'));
	if(is_array($core->input['budgetline'])) {
		$budget = new Budgets();
		$currentbudget = $budget->get_budgetbydata($budget_data);
		$budget_data['bid'] = $currentbudget['bid'];
		$budget->save_budget($core->input['budgetline'], $budget_data);
	}
	switch($budget->get_errorcode()) {
		case 0:
			output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
			break;
		case 1:
			output_xml('<status>false</status><message>'.$lang->fillrequiredfield.'</message>');
			break;
		case 2:
			output_xml('<status>false</status><message>'.$lang->budgetexist.'</message>');
			break;
		case 3:
			output_xml('<status>true</status><message>'.$lang->successfullyupdate.'</message>');
			break;
	}
}
?>
