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
if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['canUseBudgeting'] == 0) {
	error($lang->sectionnopermission);
}

$session->start_phpsession();

if($core->input['stage'] == 'fillbudgetline') {
	if(isset($core->input['identifier']) && !empty($core->input['identifier'])) {
		$sessionidentifier = $core->input['identifier'];
		$budget_data = unserialize($session->get_phpsession('budgetdata_'.$sessionidentifier));
	}
	else {
		$sessionidentifier = md5(uniqid(microtime()));
		print_r($core->input['budget']);
		$session->set_phpsession(array('budgetdata_'.$sessionidentifier => serialize($core->input['budget'])));
		$budget_data = $core->input['budget'];
	}

	$affiliate = new Affiliates($budget_data['affid']);
	$affiliate_name = $affiliate->get()['name'];
	$supplier = new Entities($budget_data['spid']);
	$supplier_name = $supplier->get()['companyName'];
	$budget = new Budgets();
	$currentbudget = $budget->get_budgetbydata($budget_data);
	if($currentbudget != false) {
		$budgetlines = $budget->get_budgetLines($currentbudget['bid']);
		$session->set_phpsession(array('budgetmetadata_'.$currentbudget['identifier'] => serialize($currentbudget)));
	}
	else {
		$budgetlines = null;
		$session->set_phpsession(array('budgetmetadata_'.$currentbudget['identifier'] => serialize($core->input)));
	}

	$allsaletypes = explode(';', $core->settings['saletypes']);

	foreach($allsaletypes as $key => $val) {
		$saletypes[$key] = ucfirst($val);
	}
	$invoice_types = array('supplier', 'other');
	foreach($invoice_types as $key => $val) {
		$invoice_types[$val] = ucfirst($val);
		unset($invoice_types[$key]);
	}
	$affiliate_currency = $affiliate->get_country()->get()['mainCurrency'];
	$currencies = get_specificdata('currencies', array('numCode', 'alphaCode'), 'numCode', 'alphaCode', array('by' => 'alphaCode', 'sort' => 'ASC'), 1, 'numCode='.$affiliate_currency);
	if(is_array($currencies)) {
		array_push($currencies, 'USD', 'EURO');
		foreach($currencies as $currency) $budget_currencylist.= "<option value='{$currency}'>{$currency}</option>";
	}
	/* check whether to display existing budget Form or display new one  */
	if(is_array($budgetlines)) {
		$core->input['identifier'] = base64_encode($currentbudget['identifier']);
		$core->input['bid'] = $currentbudget['bid'];
		$rowid = 1;
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

			$saletype_selectlist = parse_selectlist('budgetline['.$rowid.'][saleType]', 0, $saletypes, $budgetline['saleType'], '', '', array('id' => 'salestype_'.$rowid));
			$invoice_selectlist = parse_selectlist('budgetline['.$rowid.'][invoice]', 0, $invoice_types, $budgetline['invoice'], '', '', array('id' => 'invoice_'.$rowid));
			eval("\$budgetlinesrows .= \"".$template->get('budgeting_fill_lines')."\";");
		}
	}
	else {
		$previous_budget = $budget->read_prev_budgetbydata($budget_data);
		$rowid = 1;

		if(is_array($previous_budget)) {
			foreach($previous_budget as $cid => $previous_budgetdetials) {
				/* Get Customer name from object */
				$customer = new Entities($cid);
				$prevbudgetline['customerName'] = $customer->get()['companyName'];
				foreach($previous_budgetdetials as $pid => $budgetdetials) {
					$previous_yearsqty = $previous_yearsamount = $previous_yearsincome = '';
					/* Get Products name from object */
					$product = new Products($pid);
					$prevbudgetline['productname'] = $product->get()['name'];
					if(isset($budgetline[$rowid]['cid']) && !empty($budgetline[$rowid]['cid'])) {
						$required = ' required="required"';
					}
					foreach($budgetdetials as $bid => $budgetline) {
						$previous_year = $budgetline['year'];
						$core->input['bid'] = $bid;
						$blid = $budgetline['blid'];
						///$rowid++;
						$previous_yearsqty .= '<span style="display:block;"> '.$previous_year.' : '.$budgetline['quantity'].'</span>';
						$previous_yearsamount .= '<span style="display:block;"> '.$previous_year.' : '.$budgetline['amount'].'</span>';
						$previous_yearsincome .= '<span style="display:block;"> '.$previous_year.' : '.$budgetline['income'].'</span>';
					}
					eval("\$budgetlinesrows .= \"".$template->get('budgeting_fill_lines')."\";");
				}
			}
		}
	}
	eval("\$fillbudget = \"".$template->get('budgeting_fill')."\";");
	output_page($fillbudget);
}

if($core->input['action'] == 'do_perform_fillbudget') {
	$budget_data = unserialize($session->get_phpsession('budgetdata_'.$core->input['identifier']));
	if(is_array($core->input['budgetline'])) {
		$budget = new Budgets();
		$currentbudget = $budget->get_budgetbydata($budget_data);
		if(!is_array($currentbudget) && empty($currentbudget['bid'])) {
			$budget_data['bid'] = $core->input['budgetline']['bid'];
		}
		$budget_data['bid'] = $currentbudget['bid'];
		$budget->save_budget($core->input['budgetline'], $budget_data);
	}
	switch($budget->get_errorcode()) {
		case 0:
			output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
			break;
		case 2:
			output_xml('<status>false</status><message>'.$lang->fillrequiredfield.'</message>');
			break;
		case 602:
			output_xml('<status>false</status><message>'.$lang->budgetexist.'</message>');
			break;
	}
}
elseif($core->input['action'] == 'ajaxaddmore_budgetlines') {
	$rowid = intval($core->input['value']) + 1;
	$saletypes = explode(';', $core->settings['saletypes']);
	foreach($saletypes as $key => $val) {
		$saletypes[$val] = ucfirst($val);
		unset($saletypes[$key]);
	}
	$invoice_types = array('supplier', 'other');
	foreach($invoice_types as $key => $val) {
		$invoice_types[$val] = ucfirst($val);
		unset($invoice_types[$key]);
	}
	$saletype_selectlist = parse_selectlist('budgetline['.$rowid.'][saleType]', 0, $saletypes, $budgetline['saleType'], '', '', array('id' => 'salestype_'.$rowid));
	$invoice_selectlist = parse_selectlist('budgetline['.$rowid.'][invoice]', 0, $invoice_types, $budgetline['invoice'], '', '', array('blankstart' => 1, 'id' => 'invoice_'.$rowid));
	eval("\$budgetlinesrows = \"".$template->get('budgeting_fill_lines')."\";");
	output($budgetlinesrows);
}
?>
