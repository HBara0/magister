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

if(isset($core->input['identifier']) && !empty($core->input['identifier'])) {
	$sessionidentifier = $core->input['identifier'];
}
else {
	$sessionidentifier = md5(uniqid(microtime()));
}

$session->name_phpsession(COOKIE_PREFIX.'fillbudget'.$sessionidentifier);
$session->start_phpsession();

if(!$core->input['action']) {
	if($core->input['stage'] == 'fillbudgetline') {
		if(isset($core->input['identifier']) && !empty($core->input['identifier'])) {
			$budget_data = unserialize($session->get_phpsession('budgetdata_'.$sessionidentifier));
		}
		else {
			$session->set_phpsession(array('budgetdata_'.$sessionidentifier => serialize($core->input['budget'])));
			$budget_data = $core->input['budget'];
		}

		$affiliate = new Affiliates($budget_data['affid']);
		$budget_data['affiliateName'] = $affiliate->get()['name'];
		$supplier = new Entities($budget_data['spid']);
		$budget_data['supplierName'] = $supplier->get()['companyName'];

		$currentbudget = Budgets::get_budget_bydata($budget_data);
		if($currentbudget != false) {
			$budgetobj = new Budgets($currentbudget['bid']);
			$budget_data['bid'] = $currentbudget['bid'];
			$budgetlinesdata = $budgetobj->get_budgetLines();
			if(!is_array($budgetlinesdata) || empty($budgetlinesdata)) {
				$budgetlinesdata = $budgetobj->read_prev_budgetbydata();
				$is_prevonly = true;
			}
			$session->set_phpsession(array('budgetmetadata_'.$sessionidentifier => serialize($currentbudget)));
		}
		else {
			$budgetobj = new Budgets();
			$budgetlinesdata = $budgetobj->read_prev_budgetbydata($budget_data);
			$is_prevonly = true;
			$session->set_phpsession(array('budgetmetadata_'.$sessionidentifier => serialize($core->input)));
		}

		$saletypes_query = $db->query('SELECT * FROM '.Tprefix.'saletypes');
		while($saletype = $db->fetch_assoc($saletypes_query)) {
			$saletype_selectlistdata[$saletype['stid']] = $saletype['title'];
			$saletypes[$saletype['stid']] = $saletype;
		}

		/* Get Invoice Types - START */
		$saleinvoice_query = $db->query('SELECT * FROM '.Tprefix.'saletypes_invoicing WHERE isActive=1 AND affid='.intval($budget_data['affid']));
		if($db->num_rows($saleinvoice_query) > 0) {
			while($saleinvoice = $db->fetch_assoc($saleinvoice_query)) {
				$invoice_selectlistdata[$saleinvoice['invoicingEntity']] = ucfirst($saleinvoice['invoicingEntity']);
				$saletypes_invoicing[$saleinvoice['stid']] = $saleinvoice['invoicingEntity'];
				if($saleinvoice['isAffiliate'] == 1 && !empty($saleinvoice['invoiceAffid'])) {
					$saleinvoice['invoiceAffiliate'] = new Affiliates($saleinvoice['invoiceAffid']);
					$invoice_selectlistdata[$saleinvoice['invoicingEntity']] = $saleinvoice['invoiceAffiliate']->get()['name'];
				}
			}
		}
		else {
			$invoice_selectlistdata['other'] = $lang->other;
		}

		/* Get Invoice Types - ENDs */
		//$currencies = get_specificdata('currencies', array('numCode', 'alphaCode'), 'numCode', 'alphaCode', array('by' => 'alphaCode', 'sort' => 'ASC'), 1, 'numCode='.$affiliate_currency);
		$affiliate_currency = new Currencies($affiliate->get_country()->get()['mainCurrency']);
		$currencies = array('USD', 'EUR', $affiliate_currency->get()['alphaCode']);
		foreach($currencies as $currency) {
			$budget_currencylist.= '<option value="'.$currency.'">'.$currency.'</option>';
		}

		/* check whether to display existing budget Form or display new one  */
		$unsetable_fields = array('quantity', 'amount', 'incomePerc', 'income');
		if(is_array($budgetlinesdata)) {
			$rowid = 1;

			foreach($budgetlinesdata as $cid => $customersdata) {
				/* Get Customer name from object */
				if(!is_int($cid)) {
					$cid = 0;
				}
				$customer = new Entities($cid);

				foreach($customersdata as $pid => $productsdata) {
					/* Get Products name from object */
					$product = new Products($pid);

//				if(isset($budgetline[$rowid]['cid']) && !empty($budgetline[$rowid]['cid'])) {
//					$required = ' required="required"';
//				}
					foreach($productsdata as $saleid => $budgetline) {
						$previous_yearsqty = $previous_yearsamount = $previous_yearsincome = $prevyear_incomeperc = $prevyear_unitprice = $previous_actualqty = $previous_actualamount = $previous_actualincome = '';
						if($is_prevonly === true || isset($budgetline['prevbudget'])) {
							if($is_prevonly == true) {
								$prev_budgetlines = $budgetline;
							}
							elseif(isset($budgetline['prevbudget'])) {
								$prev_budgetlines = $budgetline['prevbudget'];
							}

							foreach($prev_budgetlines as $prev_budgetline) {
								if(!isset($budgetline['invoice'])) {
									$budgetline['invoice'] = $prev_budgetline['invoice'];
								}
								if($is_prevonly == true) {
									foreach($unsetable_fields as $field) {
										unset($budgetline[$field]);
									}
								}

								/* Get Actual data from mediation tables --START */

								if(empty($budgetline['actualQty']) || empty($budgetline['actualincome']) || empty($budgetline['actualamount'])) {
									$mediation_actual = $budgetobj->get_actual_meditaiondata(array('pid' => $prev_budgetline['pid'], 'cid' => $prev_budgetline['cid'], 'saleType' => $prev_budgetline['saleType']));
									print_r($mediation_actual);

									$budgetLines['actualQty'] = $mediation_actual['quantity'];
									$actualqty = '<input type="hidden" name='.$budgetline['quantity'].' value='.$budgetLines['actualQty'].' />';
									$budgetLines['actualamount'] = $mediation_actual['cost'];
									$budgetLines['actualincome'] = $mediation_actual['price'];
								}
								$budgetline['alternativecustomer'] .= '<span style="display:block;">'.ucfirst($prev_budgetline['altCid']).'</span>';
								$previous_yearsqty .= '<span class="altrow smalltext" style="display:block;"><strong>'.$prev_budgetline['year'].'</strong><br />'.$lang->budgetabbr.': '.$prev_budgetline['quantity'].' | '.$lang->actualabbr.': '.$prev_budgetline['actualQty'].'</span>';
								$previous_yearsamount .= '<span class="altrow smalltext" style="display:block;"><strong>'.$prev_budgetline['year'].'</strong><br />'.$lang->budgetabbr.': '.$prev_budgetline['amount'].' | '.$lang->actualabbr.': '.$prev_budgetline['actualAmount'].'</span>';
								$previous_yearsincome .= '<span class="altrow smalltext" style="display:block;"><strong>'.$prev_budgetline['year'].'</strong><br />'.$lang->budgetabbr.': '.$prev_budgetline['income'].' | '.$lang->actualabbr.': '.$prev_budgetline['actualIncome'].'</span>';

								$prev_budgetline['actualIncomePerc'] = 0;
								if(!empty($prev_budgetline['actualAmount'])) {
									$prev_budgetline['actualIncomePerc'] = round(($prev_budgetline['actualIncome'] * 100) / $prev_budgetline['actualAmount'], 2);
								}
								$prevyear_incomeperc .= '<span class="altrow smalltext" style="display:block;"><strong>'.$prev_budgetline['year'].'</strong><br />'.$lang->budgetabbr.': '.$prev_budgetline['incomePerc'].' | '.$lang->actualabbr.': '.$prev_budgetline['actualIncomePerc'].'</span>';

								$prev_budgetline['actualUnitPrice'] = 0;
								if(!empty($prev_budgetline['actualQty'])) {
									$prev_budgetline['actualUnitPrice'] = round($prev_budgetline['actualAmount'] / $prev_budgetline['actualQty'], 2);
								}
								$prevyear_unitprice .= '<span class="altrow smalltext" style="display:block;"><strong>'.$prev_budgetline['year'].'</strong><br />'.$lang->budgetabbr.': '.$prev_budgetline['unitPrice'].' | '.$lang->actualabbr.': '.$prev_budgetline['actualUnitPrice'].'</span>';
							}
						}

						$budgetline['cid'] = $cid;
						$budgetline['customerName'] = $customer->get()['companyName'];
						$budgetline['pid'] = $pid;
						$budgetline['productName'] = $product->get()['name'];
						$saletype_selectlist = parse_selectlist('budgetline['.$rowid.'][saleType]', 0, $saletype_selectlistdata, $saleid, '', '', array('id' => 'salestype_'.$rowid));
						$invoice_selectlist = parse_selectlist('budgetline['.$rowid.'][invoice]', 0, $invoice_selectlistdata, $budgetline['invoice'], '', '', array('id' => 'invoice_'.$rowid));


						/* Get Actual data from mediation tables --END */
						eval("\$budgetlinesrows .= \"".$template->get('budgeting_fill_lines')."\";");
						$rowid++;
					}
				}
			}
		}
		else {
			$rowid = 1;
			$saletype_selectlist = parse_selectlist('budgetline['.$rowid.'][saleType]', 0, $saletype_selectlistdata, '', '', '', array('id' => 'salestype_'.$rowid));
			$invoice_selectlist = parse_selectlist('budgetline['.$rowid.'][invoice]', 0, $invoice_selectlistdata, '', '', '', array('id' => 'invoice_'.$rowid));
			eval("\$budgetlinesrows .= \"".$template->get('budgeting_fill_lines')."\";");
		}
		unset($saletype_selectlistdata);

		/* Parse values for JS - START */
		foreach($saletypes as $stid => $saletype) {
			if($saletype['useLocalCurrency'] == 1) {
				$saltypes_currencies[$stid] = $affiliate_currency->get()['alphaCode'];
			}
			else {
				$saltypes_currencies[$stid] = 'USD';
			}
		}

		$js_currencies = json_encode($saltypes_currencies);
		$js_saletypesinvoice = json_encode($saletypes_invoicing);

		/* Parse values for JS - END */

		eval("\$fillbudget = \"".$template->get('budgeting_fill')."\";");
		output_page($fillbudget);
	}
}
else {
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

			$budget->save_budget($budget_data, $core->input['budgetline']);
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
		$budget_data = $core->input['ajaxaddmoredata'];

		$saletypes_query = $db->query('SELECT * FROM '.Tprefix.'saletypes');
		while($saletype = $db->fetch_assoc($saletypes_query)) {
			$saletype_selectlistdata[$saletype['stid']] = $saletype['title'];
			$saletypes[$saletype['stid']] = $saletype;
		}

		/* Get Invoice Types - START */
		$saleinvoice_query = $db->query('SELECT * FROM '.Tprefix.'saletypes_invoicing WHERE isActive=1 AND affid='.intval($budget_data['affid']));
		if($db->num_rows($saleinvoice_query) > 0) {
			while($saleinvoice = $db->fetch_assoc($saleinvoice_query)) {
				$invoice_selectlistdata[$saleinvoice['invoicingEntity']] = ucfirst($saleinvoice['invoicingEntity']);
				$saletypes_invoicing[$saleinvoice['stid']] = $saleinvoice['invoicingEntity'];
				if($saleinvoice['isAffiliate'] == 1 && !empty($saleinvoice['invoiceAffid'])) {
					$saleinvoice['invoiceAffiliate'] = new Affiliates($saleinvoice['invoiceAffid']);
					$invoice_selectlistdata[$saleinvoice['invoicingEntity']] = $saleinvoice['invoiceAffiliate']->get()['name'];
				}
			}
		}
		else {
			$invoice_selectlistdata['other'] = $lang->other;
		}
		$saletype_selectlist = parse_selectlist('budgetline['.$rowid.'][saleType]', 0, $saletype_selectlistdata, $budgetline['saleType'], '', '', array('id' => 'salestype_'.$rowid));
		$invoice_selectlist = parse_selectlist('budgetline['.$rowid.'][invoice]', 0, $invoice_selectlistdata, $budgetline['invoice'], '', '', array('blankstart' => 0, 'id' => 'invoice_'.$rowid));

		/* Get budget data */
		$affiliate = new Affiliates($budget_data['affid']);
		$affiliate_currency = new Currencies($affiliate->get_country()->get()['mainCurrency']);
		$currencies = array('USD', 'EUR', $affiliate_currency->get()['alphaCode']);
		foreach($currencies as $currency) {
			$budget_currencylist.= '<option value="'.$currency.'">'.$currency.'</option>';
		}

		eval("\$budgetlinesrows = \"".$template->get('budgeting_fill_lines')."\";");
		output($budgetlinesrows);
	}
}
?>
