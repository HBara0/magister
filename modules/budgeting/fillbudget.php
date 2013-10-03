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
		$session->set_phpsession(array('budgetdata_'.$sessionidentifier => serialize($core->input['budget'])));
		$budget_data = $core->input['budget'];
	}

	$affiliate = new Affiliates($budget_data['affid']);
	$affiliate_name = $affiliate->get()['name'];
	$supplier = new Entities($budget_data['spid']);
	$supplier_name = $supplier->get()['companyName'];


	$currentbudget = Budgets::get_budget_bydata($budget_data);
	if($currentbudget != false) {
		$budgetobj = new Budgets($currentbudget['bid']);
		$budgetlinesdata = $budgetobj->get_budgetLines();
		if(!is_array($budgetlinesdata) || empty($budgetlinesdata)) {
			$budgetlinesdata = $budgetobj->read_prev_budgetbydata();
			$is_prevonly = true;
		}
		$session->set_phpsession(array('budgetmetadata_'.$currentbudget['identifier'] => serialize($currentbudget)));
	}
	else {
		$budgetobj = new Budgets();
		$budgetlinesdata = $budgetobj->read_prev_budgetbydata($budget_data);
		$is_prevonly = true;
		$session->set_phpsession(array('budgetmetadata_'.$currentbudget['identifier'] => serialize($core->input)));
	}

//	$allsaletypes = explode(';', $core->settings['saletypes']);
//
//	foreach($allsaletypes as $key => $val) {
//		$crumb = explode(':', $val);
//		$saletypes[$crumb[0]] = ucfirst($crumb[1]);
//	}
	$saletypes = get_specificdata('saletypes', array('stid', 'title'), 'stid', 'title', array('by' => 'stid', 'sort' => 'ASC'));
	$saletypesmorefields = array(5);
	$saletypesmorefields = implode(', ', $saletypesmorefields);
	$invoice_types = array('supplier', 'other');
	foreach($invoice_types as $key => $val) {
		$invoice_types[$val] = ucfirst($val);
		unset($invoice_types[$key]);
	}

	//$currencies = get_specificdata('currencies', array('numCode', 'alphaCode'), 'numCode', 'alphaCode', array('by' => 'alphaCode', 'sort' => 'ASC'), 1, 'numCode='.$affiliate_currency);
	$affiliate_currency = new Currencies($affiliate->get_country()->get()['mainCurrency']);
	$currencies = array('USD', 'EUR', $affiliate_currency->get()['alphaCode']);
	foreach($currencies as $currency) {
		$budget_currencylist.= '<option value="'.$currency.'">'.$currency.'</option>';
	}


	/* check whether to display existing budget Form or display new one  */
	$unsetable_fields = array('quantity', 'amount', 'incomePerc', 'income');

	if(is_array($budgetlinesdata)) {
		$core->input['identifier'] = base64_encode($currentbudget['identifier']);
		$rowid = 1;

		foreach($budgetlinesdata as $cid => $customersdata) {
			/* Get Customer name from object */
			$customer = new Entities($cid);

			foreach($customersdata as $pid => $productsdata) {
				/* Get Products name from object */
				$product = new Products($pid);

//				if(isset($budgetline[$rowid]['cid']) && !empty($budgetline[$rowid]['cid'])) {
//					$required = ' required="required"';
//				}
				foreach($productsdata as $saleid => $budgetline) {
					$previous_yearsqty = $previous_yearsamount = $previous_yearsincome = '';
					if($is_prevonly === true || isset($budgetline['prevbudget'])) {
						if($is_prevonly == true) {
							$prev_budgetlines = $budgetline;
						}
						elseif(isset($budgetline['prevbudget'])) {
							$prev_budgetlines = $budgetline['prevbudget'];
						}

						foreach($prev_budgetlines as $prev_budgetline) {

							if($is_prevonly == true) {
								foreach($unsetable_fields as $field) {
									unset($budgetline[$field]);
								}
							}
							$previous_yearsqty .= '<span style="display:block;"> '.$prev_budgetline['year'].': '.$prev_budgetline['quantity'].'</span>';
							$previous_yearsamount .= '<span style="display:block;"> '.$prev_budgetline['year'].': '.$prev_budgetline['amount'].'</span>';
							$previous_yearsincome .= '<span style="display:block;"> '.$prev_budgetline['year'].': '.$prev_budgetline['income'].'</span>';
						}
					}
					$budgetline['cid'] = $cid;
					$budgetline['customerName'] = $customer->get()['companyName'];
					$budgetline['pid'] = $pid;
					$budgetline['productName'] = $product->get()['name'];
					foreach($saletypes as $stid => $saletype) {
						$selected = '';
						if($saleid == $stid) {
							$selected = 'selected="selected"';
						}
						$saletype_selectlist.= '<option value="'.$stid.'" '.$selected.'>'.$saletype.'</option>';
					}
					//$saletype_selectlist = parse_selectlist('budgetline['.$rowid.'][saleType]', 0, $saletypes, $saleid, '', '', array('id' => 'salestype_'.$rowid));
					$invoice_selectlist = parse_selectlist('budgetline['.$rowid.'][invoice]', 0, $invoice_types, $budgetline['invoice'], '', '', array('id' => 'invoice_'.$rowid));
					eval("\$budgetlinesrows .= \"".$template->get('budgeting_fill_lines')."\";");
					$rowid++;
				}
			}
		}
	}
	else {
		$rowid = 1;
		foreach($saletypes as $stid => $saletype) {
			$saletype_selectlist.= '<option value="'.$stid.'">'.$saletype.'</option>';
		}
		$invoice_selectlist = parse_selectlist('budgetline['.$rowid.'][invoice]', 0, $invoice_types, '', '', '', array('id' => 'invoice_'.$rowid));
		eval("\$budgetlinesrows .= \"".$template->get('budgeting_fill_lines')."\";");
	}

	eval("\$fillbudget = \"".$template->get('budgeting_fill')."\";");
	output_page($fillbudget);
}

if($core->input['action'] == 'do_perform_fillbudget') {
	$budget_data = unserialize($session->get_phpsession('budgetdata_'.$core->input['identifier']));
	if(is_array($core->input['budgetline'])) {
		if(isset($core->input['budget']['bid'])) {
			$currentbudget = $core->input['budget'];
			$budget = new Budgets($core->input['budget']['bid']);
		}
		else {
			$currentbudget = Budgets::get_budget_bydata($budget_data);
		}
		if(is_array($currentbudget) && !empty($currentbudget['bid'])) {
			$budget_data['bid'] = $currentbudget['bid'];
		}

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
	$affid = intval($core->input['affiliate']);
	$budget_data = unserialize($session->get_phpsession('budgetdata_'.$core->input['identifier']));
	$saletypes = get_specificdata('saletypes', array('stid', 'title'), 'stid', 'title', array('by' => 'stid', 'sort' => 'ASC'));
//	$saletypes_setting = explode(';', $core->settings['saletypes']);
//	foreach($saletypes_setting as $key => $val) {
//		$crumb = explode(':', $val);
//		$saletypes[$crumb[0]] = ucfirst($crumb[1]);
//	}
	foreach($saletypes as $stid => $saletype) {
		$saletype_selectlist.= '<option value="'.$stid.'" '.$selected.'>'.$saletype.'</option>';
	}
	$invoice_types = array('supplier', 'other');
	foreach($invoice_types as $key => $val) {
		$invoice_types[$val] = ucfirst($val);
		unset($invoice_types[$key]);
	}
	$invoice_selectlist = parse_selectlist('budgetline['.$rowid.'][invoice]', 0, $invoice_types, $budgetline['invoice'], '', '', array('blankstart' => 1, 'id' => 'invoice_'.$rowid));

	/* Get budget data */
	$affiliate = new Affiliates($affid);
	$affiliate_currency = new Currencies($affiliate->get_country()->get()['mainCurrency']);
	$currencies = array('USD', 'EUR', $affiliate_currency->get()['alphaCode']);
	foreach($currencies as $currency) {
		$budget_currencylist.= '<option value="'.$currency.'">'.$currency.'</option>';
	}

	eval("\$budgetlinesrows = \"".$template->get('budgeting_fill_lines')."\";");
	output($budgetlinesrows);
}
?>
