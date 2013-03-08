<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: newpreview.php
 * Created:        @tony.assaad            |
 * Last Update:    @tony.assaad    March 07, 2013 | 1:24:11 PM
 */


if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

//$newreport = new reportingQr(array('rid'=>14));
$newreport = new reportingQr(array('year' => 2013, 'spid' => 12, 'affid' => 15, 'quarter' => 4));
$report = $newreport->get();
$report['items'] = $newreport->read_products_activity(true);
$report['productsactivity'] = $newreport->get_products_activity();
$report['keycustomers'] = $newreport->get_key_customers();
$report['contributors'] = $newreport->get_report_contributors();
$report['marketreports'] = $newreport->get_market_reports();
$report['auditors'] = $newreport->get_report_supplier_audits();
$report['reportstats'] = $newreport->get_report_stats();
$report['finializer'] = $newreport->get_report_finalizer();
$report['affiliates'] = $newreport->get_report_affiliate();
$report['supplier'] = $newreport->get_report_supplier();
$report['representatives'] = $newreport->get_supplier_representatives();
$report['summary'] = $newreport->get_report_summary();

$aggregate_types = array('affiliates', 'segments', 'products');
$report_years = array('current_year' => $report['year'], 'before_1year' => $report['year'] - 1, 'before_2years' => $report['year'] - 2);
$report['displayyear'] = $report['year'];
/**/


foreach($aggregate_types as $aggregate_type) {
	$item['name'] = '';
	if(is_array($report['items'])) {
		foreach($report['items'] as $category => $catitem) {/* amount or  quantity */
			foreach($catitem as $type => $typeitem) { /* actual or forecast */
				foreach($report_years as $yearef => $year) {
					$report['year'] = $report_years[$yearef][$year];
					for($quarter = 1; $quarter <= 4; $quarter++) {
						switch($aggregate_type) {
							case 'affiliates':
								if(is_array($report['items'][$category][$type][$year][$quarter])) {
									//print_r($report['items'][$category][$type]);
									foreach($report['items'][$category][$type][$year][$quarter] as $affid => $affiliatedata) {
										$item['name'] = $newreport->get_report_affiliate($affid)['name'];
										foreach($affiliatedata as $spid => $segmentdata) {
											$item[$aggregate_type][$category][$type][$year][$quarter] = array_sum($report['items'][$category][$type][$year][$quarter][$affid][$spid]);
										}
									}
								}
								break;
							case 'segments':
								if(is_array($report['items'][$category][$type][$year][$quarter])) {
									$item['name'] = '';
									foreach($report['productsactivity'] as $paid => $sid) {
										$item['name'] .= '<div>'.$sid['segment'].'</div>';
									}
									//$item['name'] = $newreport->get_report_productsegment($report['productsactivity'] ['spid'])['segment'];
									foreach($report['items'][$category][$type][$year][$quarter] as $affid => $affiliatedata) {
										foreach($affiliatedata as $spid => $segmentdata) {
											$item[$aggregate_type][$category][$type][$year][$quarter] = array_sum($report['items'][$category][$type][$year][$quarter][$affid][$spid]);
										}
									}
								}
								break;
							case 'products':
								if(is_array($report['items'][$category][$type][$year][$quarter])) {
									$productsitem = $newreport->get_product_name()['productsname'];
									$item['name'] = '';
									foreach($productsitem as $product) { /* get the product name */
										$item['name'] .='<div>'.$product['name'].'</div>';
									}
									foreach($report['items'][$category][$type][$year][$quarter] as $affid => $affiliatedata) {
										foreach($affiliatedata as $spid => $segmentdata) {
											foreach($segmentdata as $pid => $productdata) {
												$item[$aggregate_type][$category][$type][$year][$quarter] = $report['items'][$category][$type][$year][$quarter][$affid][$spid][$pid];
											}
										}
									}
								}
								break;
						}
						eval("\$reporting_report_newoverviewbox_row[$aggregate_type][$category] = \"".$template->get('new_reporting_report_overviewbox_row')."\";");
					}
				}
			}
		}
	}
}

if(!empty($report['supplier']['logo'])) {
	$report['supplierlogo'] = '<img src="./uploads/entitieslogos/'.$report['supplier']['logo'].'" alt="'.$report['supplier']['companyName'].'" width="200px"/><br /><span style="font-size:12px; font-weight:100;font-style:italic;">'.$report['supplier']['companyName'].'</span>';
}

if(is_array($report['representatives'])) {
	foreach($report['representatives'] as $representative) {
		$representatives_list .= "<tr><td style='width: 25%; text-align: left;'>{$representative[name]}</td><td style='text-align: left;'>{$representative[email]}</td></tr>";
	}
}

if(is_array($report['items'])) {
	foreach($report['items'] as $category => $catitem) {
		foreach($aggregate_types as $aggregate_type) {
			eval("\$reporting_report_newoverviewbox[$aggregate_type][$category] = \"".$template->get('new_reporting_report_overviewbox')."\";");
		}
	}
}

if(is_array($report['contributors'])) {
	$contributors_overview_entries = '';
//	foreach($report['auditors'] as $uid => $auditor) {
//		$auditors[$uid] = '<a href="mailto:'.$report['auditors']['email'].'">'.$report['auditors']['email'].'</a> (<a href="mailto:'.$report['auditors']['email'].'">'.$report['auditors']['email'].'</a>)';
//	}
	foreach($report['affiliates'] as $affid => $contributions) {
		$contributors_overview_entries = '';
		$contributors_overview_entries .= '<tr><td colspan="2" class="thead">'.$report['affiliates']['name'].'</td></tr>';
		if(is_array($report['items'])) {
			$contributors_overview_entries = '';
//			foreach($report['productsactivity'] as $psid => $contributors) {
//				$contributors_overview_entries .= '<tr><td class="lightdatacell_freewidth" style="text-align:left;">'.$contributors['segment'].'</td><td style="width:70%; border-bottom: 1px dashed #CCCCCC;">'.$auditors[$uid].'</td></tr>';
//			}
			foreach($report['marketreports']['market'] as $marketkey => $marketreport) {
				$auditors[$uid] = '<a href="mailto:'.$report['marketreports']['marketauthors'][$marketkey]['email'].'">'.$report['marketreports']['marketauthors'][$marketkey]['displayName'].'</a> (<a href="mailto:'.$report['marketreports']['marketauthors'][$marketkey]['email'].'">'.$report['marketreports']['marketauthors'][$marketkey]['email'].'</a>)';
				$contributors_overview_entries .= '<tr><td class="lightdatacell_freewidth" style="text-align:left;">'.$marketreport['segmenttitle'].'</td><td style="width:70%; border-bottom: 1px dashed #CCCCCC;">'.$auditors[$uid].'</td></tr>';
			}
		}
	}
	eval("\$contributorspage = \"".$template->get('new_reporting_report_contributionoverview')."\";");
}


$report['quartername'] = 'Q'.$report['quarter'].' '.$report['displayyear'];
eval("\$coverpage = \"".$template->get('new_reporting_report_coverpage')."\";");
eval("\$closingpage = \"".$template->get('reporting_report_closingpage')."\";");
eval("\$highlightbox = \"".$template->get('new_reporting_report_highlightbox')."\";");

if(is_array($report['keycustomers']) && ($report['keyCustAvailable'] == 1)) {
	foreach($report['keycustomers'] as $keycust => $customer) {
		eval("\$keycustomers .= \"".$template->get("new_reporting_report_keycustomersbox_customerrow")."\";");
	}
	eval("\$keycustomersbox = \"".$template->get("new_reporting_report_keycustomersbox")."\";");

	if(is_array($report['marketreports']['market']) && ($report['mktReportAvailable'] == 1)) {
		foreach($report['marketreports']['market'] as $marketkey => $marketreport) {
			$marketreport['authors'] = $report['marketreports']['marketauthors'][$marketkey]['displayName'];
			eval("\$marketreportbox .= \"".$template->get('new_reporting_report_marketreportbox')."\";");
			eval("\$marketauthors .= \"".$template->get('new_reporting_report_marketreporauthorstbox_row')."\";");
		}
	}
}
eval("\$marketreporauthorstbox = \"".$template->get('new_reporting_report_marketreporauthorstbox')."\";");
/* Output summary table - START */
if(!empty($report['rid']) && !empty($report['summary'])) {
	eval("\$summarypage = \"".$template->get('reporting_report_summary')."\";");
}
/* Output summary table  - END */

$reports = $coverpage.$contributorspage.$summarypage.$highlightbox.$reporting_report_newoverviewbox['affiliates']['amount'].$reporting_report_newoverviewbox['segments']['amount'].$reporting_report_newoverviewbox['products']['amount'].$keycustomersbox.$marketreportbox.$marketreporauthorstbox.$reportauthors.$closingpage;
eval("\$reportspage = \"".$template->get('new_reporting_preview')."\";");
output_page($reportspage);
?>
