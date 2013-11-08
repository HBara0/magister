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
$sessionidentifier = md5(uniqid(microtime()));
$session->name_phpsession(COOKIE_PREFIX.'generatebudget'.$sessionidentifier);

if(!($core->input['action'])) {
	if($core->input['referrer'] == 'generate') {
		$budgetcache = new Cache();
//		$identifier = base64_decode($core->input['identifier']);
//		$generate_budget_data = unserialize($session->get_phpsession('generatebudgetdata_'.$identifier));
		$budgetsdata = ($core->input['budget']);
		$aggregate_types = array('affilliates', 'suppliers', 'managers', 'segments', 'years');

		eval("\$budgetreport_coverpage = \"".$template->get('budgeting_budgetreport_coverpage')."\";");

		$budget_genobj = new Budgets();
		$budgets = $budget_genobj->get_budgets_bydata($budgetsdata);
		if(is_array($budgets)) {
			foreach($budgets as $budgetid) {
				$budget_obj = new Budgets($budgetid);
				$budget['country'] = $budget_obj->get_affiliate()->get()['name'];
				$budget['affiliate'] = $budget_obj->get_affiliate()->get()['name'];
				$firstbudgetline = $budget_obj->get_budgetLines(0, array('filters' => array('businessMgr' => $budgetsdata['managers'])));
				if(is_array($firstbudgetline)) {
					$session->set_phpsession(array('budgetmetadata_' => serialize($firstbudgetline)));
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
								$countries = new Countries($budgetline_obj->get_customer($cid)->get()['country']);

								$budgetline['cusomtercountry'] = $countries->get()['name'];
								if(empty($budgetline['cusomtercountry'])) {
									$budgetline['cusomtercountry'] = $lang->na;
								}

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
									$budget['customerid'] = $budgetline_obj->get_customer($budgetline['cid'])->get()['eid'];
									$budgetline['customer'] = $budgetline_obj->get_customer($budgetline['cid'])->get()['companyName'];
									$customername = '<a href="index.php?module=profiles/entityprofile&eid='.$budget['customerid'].'" target="_blank">'.$budgetline['customer'].'</a>';
								}

								$budgetline['product'] = $budgetline_obj->get_product($budgetline['pid'])->get()['name'];
								eval("\$budget_report_row .= \"".$template->get('budgeting_budgetrawreport_row')."\";");
							}
						}
					}
				}
			}
			$toolgenerate = '<div align="right"  title="'.$lang->generate.'" style=" float:right;padding:10px;width:10px;"><a href="index.php?module=budgeting/preview&action=exportexcel&bid='.$budgetid.'" target="_blank"><img src="././images/icons/xls.gif"/>'.$lang->generateexcel.'</a></div>';
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
	$budget_metadata = unserialize($session->get_phpsession('budgetmetadata_'));
	$budget_obj = new Budgets($core->input['bid']);
	$budgetcache = new Cache();
	$firstbudgetline['affiliate'] = $budget_obj->get_affiliate()->get()['name'];
//	$firstbudgetline['manager'] = $budget_obj->get_businessMgr()->get();
//
//	if(!$budgetcache->iscached('managercache', $firstbudgetline['manager']['uid'])) {
//		$budgetcache->add('managercache', $firstbudgetline['manager']['displayName'], $firstbudgetline['manager']['uid']);
//	}
//	$firstbudgetline['manager'] = $budgetcache->data['managercache'][$firstbudgetline['manager']['uid']];
	$firstbudgetline['supplier'] = $budget_obj->get_supplier()->get()['companyName'];
	$counter = 1;

	//$headers_data = array('unitPrice', 'amount', 'income', 'quantity', 'saleType', 's1Perc', 's2Perc', 'uom', 'segment', 'customer', 'product', 'cusomtercountry', 'affiliate', 'supplier', 'manager');
	$headers_data = array('manager', 'customer', 'cusomtercountry', 'affiliate', 'supplier', 'segment', 'product', 'quantity', 'uom', 'unitPrice', 'saleType', 'amount', 'income', 's1Perc', 's2Perc');
	if(is_array($budget_metadata)) {
		foreach($budget_metadata as $cid => $customersdata) {
			foreach($customersdata as $pid => $productsdata) {
				foreach($productsdata as $saleid => $budgetline[$counter]) {

					$budgetline_obj = new BudgetLines($budgetline[$counter]['blid']);
					$countries = new Countries($budgetline_obj->get_customer($cid)->get()['country']);
					$firstbudgetline['manager'] = $budgetline_obj->get_businessMgr()->get()['displayName'];

					$budgetline[$counter]['customer'] = $budgetline_obj->get_customer($cid)->get()['companyName'];
					if(empty($budgetline[$counter]['customer'])) {
						$budgetline[$counter]['customer'] = $budgetline[$counter]['altCid'];
					}
					$budgetline[$counter]['cusomtercountry'] = $countries->get()['name'];
					if(empty($budgetline[$counter]['cusomtercountry'])) {
						$budgetline[$counter]['cusomtercountry'] = $lang->na;
					}
					$budgetline[$counter]['segment'] = $budgetline_obj->get_product($pid)->get_segment()['title'];

					$budgetline[$counter]['product'] = $budgetline_obj->get_product($pid)->get()['name'];
					$budgetline[$counter]['uom'] = 'Kg';

					$budgetline[$counter]['unitPrice'] = $budgetline[$counter]['unitPrice'];
					$budgetline[$counter]['saleType'] = Budgets::get_saletype_byid($saleid);


					foreach($headers_data as $headerkey) {
						foreach($budgetline[$counter] as $key => $val) {
							if(!in_array($key, $headers_data)) {
								unset($budgetline[$counter][$key]);
							}
							$budgetline[$counter] +=$firstbudgetline;
							unset($budgetline[$counter]['prevbudget']);
						}
						//$budgetline[$counter][$val] = $budgetline[$counter][$headerkey];
					}


					//array_multisort($budgetline[$counter], SORT_ASC, $headers_data);
					$counter++;
				}
			}
		}
	}
	$budgetline2 = $budgetline;
	unset($budgetline);
	foreach($headers_data as $val) {
		$budgetline[0][$val] = $lang->$val;
		foreach($budgetline2 as $counter => $value) {
			$budgetline[$counter][$val] = $value[$val];
		}
	}

	//unset($budgetline['bid'], $budgetline['blid'], $budgetline['pid'], $budgetline['cid'], $budgetline['incomePerc'], $budgetline['invoice'], $budgetline['createdBy'], $budgetline['modifiedBy'], $budgetline['originalCurrency'], $budgetline['prevbudget'], $budgetline['cusomtercountry']);
	$excelfile = new Excel('array', $budgetline);
}
?>
