<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: preview.php
 * Created:        @tony.assaad    Aug 22, 2013 | 4:17:09 PM
 * Last Update:    @tony.assaad    Aug 22, 2013 | 4:17:09 PM
 */

if(!($core->input['action'])) {
	if($core->input['referrer'] == 'generate') {
		$budgetcache = new Cache();
//		$identifier = base64_decode($core->input['identifier']);
//		$generate_budget_data = unserialize($session->get_phpsession('generatebudgetdata_'.$identifier));
		$budgetsdata = ($core->input['budget']);
		$aggregate_types = array('affilliates', 'suppliers', 'managers', 'segments', 'years');

		eval("\$budgetreport_coverpage = \"".$template->get('budgeting_budgetreport_coverpage')."\";");

		$export_identifier = base64_encode(serialize($budgetsdata));
		$budgets = Budgets::get_budgets_bydata($budgetsdata);
		if(is_array($budgets)) {
			foreach($budgets as $budgetid) {
				$budget_obj = new Budgets($budgetid);
				$budget['country'] = $budget_obj->get_affiliate()->get()['name'];
				$budget['affiliate'] = $budget_obj->get_affiliate()->get()['name'];

//				$filter_auditor = parse_userentities_data($core->user['uid']);
//				if(!in_array(implode(',', $budgetsdata['suppliers']), $filter_auditor['auditfor'])) {
//					$filter = array('filters' => array('businessMgr' => array($core->user['uid'])));
//				}
				/* Validate Permissions - START */
				if($core->usergroup['canViewAllSupp'] == 0 && $core->usergroup['canViewAllAff'] == 0) {
					if(is_array($core->user['auditfor'])) {
						if(!in_array($budget_data['spid'], $core->user['auditfor'])) {
							if(is_array($core->user['auditedaffids'])) {
								if(!in_array($budget_data['affid'], $core->user['auditedaffids'])) {
									if(is_array($core->user['suppliers']['affid'][$budget_data['spid']])) {
										if(in_array($budget_data['affid'], $core->user['suppliers']['affid'][$budget_data['spid']])) {
											$filter = array('filters' => array('businessMgr' => array($core->user['uid'])));
										}
										else {
											redirect('index.php?module=budgeting/create');
										}
									}
									else {
										$filter = array('filters' => array('businessMgr' => array($core->user['uid'])));
									}
								}
							}
							else {
								$filter = array('filters' => array('businessMgr' => array($core->user['uid'])));
							}
						}
					}
					else {
						$filter = array('filters' => array('businessMgr' => array($core->user['uid'])));
					}
				}
				/* Validate Permissions - END */
				$firstbudgetline = $budget_obj->get_budgetLines(0, $filter);
				if(is_array($firstbudgetline)) {
					foreach($firstbudgetline as $cid => $customersdata) {
						foreach($customersdata as $pid => $productsdata) {
							foreach($productsdata as $saleid => $budgetline) {
								$rowclass = alt_row($rowclass);
								$budgetline_obj = new BudgetLines($budgetline['blid']);

								$budget['manager'] = $budgetline_obj->get_businessMgr()->get();
								$budget['managerid'] = $budgetline_obj->get_businessMgr()->get()['uid'];

								if(!$budgetcache->iscached('managercache', $budget['manager']['uid'])) {
									$budgetcache->add('managercache', $budget['manager']['displayName'], $budget['manager']['uid']);
								}
								$budget['supplier'] = $budget_obj->get_supplier()->get()['companyNameShort'];
								if(empty($budget['supplier'])) {
									$budget['supplier'] = $budget_obj->get_supplier()->get()['companyName'];
								}
								$budget['manager'] = $budgetcache->data['managercache'][$budget['manager']['uid']];

								$budgetline['customerCountry'] = $budgetline_obj->parse_country();

								$budgetline['uom'] = 'Kg';
								$budgetline['saleType'] = Budgets::get_saletype_byid($saleid);

//								if(isset($budgetline['genericproduct']) && !empty($budgetline['genericproduct'])) {
//									$budgetline['genericproduct'] = $budgetline_obj->get_product()->get_generic_product();
//								}
								if(isset($budgetline['pid']) && !empty($budgetline['pid'])) {
									$budgetline['segment'] = $budgetline_obj->get_product()->get_segment()['titleAbbr'];
								}
								if((empty($budgetline['cid']) && !empty($budgetline['altCid']))) {
									$customername = $budgetline['altCid'];
								}
								else {
									$budget['customerid'] = $budgetline_obj->get_customer()->get()['eid'];
									$budgetline['customer'] = $budgetline_obj->get_customer()->get()['companyName'];
									$customername = '<a href="index.php?module=profiles/entityprofile&eid='.$budget['customerid'].'" target="_blank">'.$budgetline['customer'].'</a>';
								}

								$budgetline['product'] = $budgetline_obj->get_product($budgetline['pid'])->get()['name'];
								eval("\$budget_report_row .= \"".$template->get('budgeting_budgetrawreport_row')."\";");
							}
						}
					}
				}
			}
			$toolgenerate = '<div align="right" title="'.$lang->generate.'" style="float:right;padding:10px;width:10px;"><a href="index.php?module=budgeting/preview&identifier='.$export_identifier.'&action=exportexcel" target="_blank"><img src="./images/icons/xls.gif"/>'.$lang->generateexcel.'</a></div>';
		}
		else {
			$budgeting_budgetrawreport = '<tr><td>'.$lang->na.'</td></tr>';
		}
		eval("\$budgeting_budgetrawreport = \"".$template->get('budgeting_budgetrawreport')."\";");
	}

	eval("\$budgetingpreview = \"".$template->get('budgeting_budgetreport_preview')."\";");
	output_page($budgetingpreview);
}
elseif($core->input['action'] == 'exportexcel') {
	if(!isset($core->input['identifier'])) {
		error($lang->fillallrequirefields);
	}
	$budgetsdata = unserialize(base64_decode($core->input['identifier']));
	$budgets = Budgets::get_budgets_bydata($budgetsdata);

	$headers_data = array('manager', 'customer', 'customerCountry', 'affiliate', 'supplier', 'segment', 'product', 'quantity', 'uom', 'unitPrice', 'saleType', 'amount', 'income', 's1Perc', 's2Perc');
	$counter = 1;
	if(is_array($budgets)) {
		foreach($budgets as $budgetid) {
			$budget_obj = new Budgets($budgetid);
			$budget['affiliate'] = $budget_obj->get_affiliate()->get()['name'];
			$budget['supplier'] = $budget_obj->get_supplier()->get()['companyNameShort'];
			if(empty($budget['supplier'])) {
				$budget['supplier'] = $budget_obj->get_supplier()->get()['companyName'];
			}

			$budget['year'] = $budget_obj->get()['year'];

			$firstbudgetline = $budget_obj->get_budgetLines(0, $filter);
			foreach($firstbudgetline as $cid => $customersdata) {
				foreach($customersdata as $pid => $productsdata) {
					foreach($productsdata as $saleid => $budgetline[$counter]) {
						$budgetline_obj = new BudgetLines($budgetline[$counter]['blid']);
						$countries = new Countries($budgetline_obj->get_customer($cid)->get()['country']);
						$budgetline[$counter]['manager'] = $budgetline_obj->get_businessMgr()->get()['displayName'];

						$budgetline[$counter]['customerCountry'] = $budgetline_obj->parse_country();

						$budgetline[$counter]['segment'] = $budgetline_obj->get_product($pid)->get_segment()['title'];
						$budgetline[$counter]['product'] = $budgetline_obj->get_product($pid)->get()['name'];
						$budgetline[$counter]['uom'] = 'Kg';
						$budgetline[$counter]['unitPrice'] = $budgetline[$counter]['unitPrice'];
						$budgetline[$counter]['saleType'] = Budgets::get_saletype_byid($saleid);


						if((empty($budgetline[$counter]['cid']) && !empty($budgetline[$counter]['altCid']))) {
							$budgetline[$counter]['customer'] = $budgetline[$counter]['altCid'];
						}
						else {
							$budgetline[$counter]['customer'] = $budgetline_obj->get_customer()->get()['companyName'];
						}

						foreach($budgetline[$counter] as $key => $val) {
							if(!in_array($key, $headers_data)) {
								unset($budgetline[$counter][$key]);
							}
							$budgetline[$counter] += $budget;
							unset($budgetline[$counter]['prevbudget']);
						}
						$counter++;
					}
				}
			}
		}
	}

	$budgetline_temp = $budgetline;
	unset($budgetline);
	foreach($headers_data as $val) {
		$budgetline[0][$val] = $lang->{strtolower($val)};
		foreach($budgetline_temp as $counter => $value) {
			$budgetline[$counter][$val] = $value[$val];
		}
	}
	unset($budgetline_temp);

	//unset($budgetline['bid'], $budgetline['blid'], $budgetline['pid'], $budgetline['cid'], $budgetline['incomePerc'], $budgetline['invoice'], $budgetline['createdBy'], $budgetline['modifiedBy'], $budgetline['originalCurrency'], $budgetline['prevbudget'], $budgetline['cusomtercountry']);
	$excelfile = new Excel('array', $budgetline);
}
?>
