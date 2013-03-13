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

if(!$core->input['action']) {
	if($core->input['referrer'] == 'generate' || $core->input['referrer'] == 'list') {
		if(!isset($core->input['year'], $core->input['quarter'], $core->input['spid'], $core->input['affid'])) {
			redirect('index.php?module=reporting/generatereport');
		}


		if($core->input['generateType'] == 1) {
			$foreach = $core->input['affid'];
		}
		else {
			if($core->input['referrer'] == 'list') {
				$core->input['incMarketReport'] = $core->input['incKeyCustomers'] = $core->input['incKeyProducts'] = $core->input['genByProduct'] = 1;
				$core->input['spid'] = array($core->input['spid']);
			}
			$foreach = $core->input['spid'];
		}
	}
	elseif($core->input['referrer'] == 'direct') {
		if(isset($core->input['identifier'])) {
			$identifier = unserialize(base64_decode($core->input['identifier']));
			foreach($identifier as $key => $val) {
				$core->input[$key] = $val;
			}
			$core->input['incMarketReport'] = $core->input['incKeyCustomers'] = $core->input['incKeyProducts'] = $core->input['genByProduct'] = 1;
			$core->input['generateType'] = 1;
			$foreach = $core->input['affid'];
		}
		else {
			redirect('index.php?module=reporting/generatereport');
		}
	}
	else {
		$foreach = array(''); //Dummy array
	}

	$no_send_icon = true;
	$session_identifier = md5(uniqid(microtime()));

	foreach($foreach as $index => $entity) {
		if($core->input['referrer'] == 'generate' || $core->input['referrer'] == 'list' || $core->input['referrer'] == 'direct') {
			if($core->input['generateType'] == 1) {
				$report_param['affid'] = $entity;
				$report_param['spid'] = $db->escape_string($core->input['spid']);
			}
			else {
				$report_param['affid'] = $db->escape_string($core->input['affid']);
				$report_param['spid'] = $entity;
			}
		}
		else {
			//Preview while filling
		}
		$newreport = new ReportingQr(array('year' => $core->input['year'], 'spid' => $report_param['spid'], 'affid' => $report_param['affid'], 'quarter' => $core->input['quarter']));
		$report = $newreport->get();

		$newreport->read_products_activity(true);
		$report['items'] = $newreport->get_classified_productsactivity();
		$report['productsactivity'] = $newreport->get_products_activity();
		$report['keycustomers'] = $newreport->get_key_customers();
		$report['contributors'] = $newreport->get_report_contributors();
		$report['marketreports'] = $newreport->get_market_reports();
		$report['auditors'] = $newreport->get_report_supplier_audits();
		$report['reportstats'] = $newreport->get_report_status();
		$report['finializer'] = $newreport->get_report_finalizer();
		$report['affiliates'] = $newreport->get_report_affiliate();
		$report['supplier'] = $newreport->get_report_supplier();
		$report['representatives'] = $newreport->get_supplier_representatives();
		$report['summary'] = $newreport->get_report_summary();

		$aggregate_types = array('affiliates', 'segments', 'products');
		$report_years = array('current_year' => $report['year'], 'before_1year' => $report['year'] - 1, 'before_2years' => $report['year'] - 2);
		$report['displayyear'] = $report['year'];
		/**/

		$report['quartername'] = 'Q'.$report['quarter'].' '.$report['year'];
		foreach($aggregate_types as $aggregate_type) {
			$item['name'] = '';
			if(is_array($report['items'])) {
				foreach($report['items'] as $category => $catitem) {/* amount or  quantity */
					foreach($catitem as $type => $typeitem) { /* actual or forecast */
						foreach($report_years as $yearef => $year) {

							if($type == 'forecast' && $year != $report['year']) {
								continue;
							}

							$report['year'] = $report_years[$yearef][$year];
							for($quarter = 1; $quarter <= 4; $quarter++) {
								switch($aggregate_type) {
									case 'affiliates':
										if(is_array($report['items'][$category][$type][$year][$quarter])) {
											foreach($report['items'][$category][$type][$year][$quarter] as $affid => $affiliatedata) {
												$item['name'] = $newreport->get_report_affiliate($affid)['name'];

												$item[$aggregate_type][$category][$type][$year][$quarter] = array_sum_recursive($report['items'][$category][$type][$year][$quarter][$affid]);
												$total_year[$aggregate_type][$affid]['data'][$year]+=$item[$aggregate_type][$category][$type][$year][$quarter];
												$total_year[$aggregate_type][$affid]['name'] = $item['name'];
												if($item[$aggregate_type][$category][$type][$year][$quarter] > 1) {
													$item[$aggregate_type][$category][$type][$year][$quarter] = round($item[$aggregate_type][$category][$type][$year][$quarter]);
												}

												eval("\$reporting_report_newoverviewbox_row[$aggregate_type][$category][$affid] = \"".$template->get('new_reporting_report_overviewbox_row')."\";");
											}
										}
										break;
									case 'segments':
										if(is_array($report['items'][$category][$type][$year][$quarter])) {
											$item['name'] = '';
											//$item['name'] = $newreport->get_report_productsegment($report['productsactivity'] ['spid'])['segment'];
											foreach($report['items'][$category][$type][$year][$quarter] as $affid => $affiliatedata) {
												foreach($affiliatedata as $spid => $segmentdata) {
													$item['name'] = $newreport->get_productssegments()[$spid];

													$item[$aggregate_type][$category][$type][$year][$quarter] = round(array_sum($report['items'][$category][$type][$year][$quarter][$affid][$spid]));
													$item[$aggregate_type][$category][$type][$year][$quarter] = array_sum($report['items'][$category][$type][$year][$quarter][$affid][$spid]);
													$total_year[$aggregate_type][$spid]['data'][$year]+=$item[$aggregate_type][$category][$type][$year][$quarter];
													$total_year[$aggregate_type][$spid]['name'] = $item['name'];
													if($item[$aggregate_type][$category][$type][$year][$quarter] > 1) {
														$item[$aggregate_type][$category][$type][$year][$quarter] = round($item[$aggregate_type][$category][$type][$year][$quarter]);
													}
													eval("\$reporting_report_newoverviewbox_row[$aggregate_type][$category][$spid] = \"".$template->get('new_reporting_report_overviewbox_row')."\";");
												}
											}
										}

										break;
									case 'products':
										if(is_array($report['items'][$category][$type][$year][$quarter])) {
											foreach($report['items'][$category][$type][$year][$quarter] as $affid => $affiliatedata) {
												foreach($affiliatedata as $spid => $segmentdata) {
													foreach($segmentdata as $pid => $productdata) {
														$item['name'] = $newreport->get_products()[$pid];
														$item[$aggregate_type][$category][$type][$year][$quarter] = $report['items'][$category][$type][$year][$quarter][$affid][$spid][$pid];
														$total_year[$aggregate_type][$spid][$pid]['data'][$year]+=$item[$aggregate_type][$category][$type][$year][$quarter];
														$total_year[$aggregate_type][$spid][$pid]['name'] = $item['name'];

														if($type == 'forecast' && $quarter > $report['quarter'] && $year == $report['year']) {
															$item[$aggregate_type][$category][$type][$year][$quarter] = ($item[$aggregate_type][$category][$type][$report_years['current_year']][$quarter]);
															if($quarter != $report['quarter']) {
																$item[$aggregate_type][$category][$type][$year][$quarter] /= (4 - $report['quarter']);
															}
														}
														eval("\$reporting_report_newoverviewbox_row[$aggregate_type][$category][$pid] = \"".$template->get('new_reporting_report_overviewbox_row')."\";");
													}
												}
											}
										}

										break;
								}
							}
						}
					}

					if(is_array($reporting_report_newoverviewbox_row[$aggregate_type][$category])) {
						$reporting_report_newoverviewbox_row[$aggregate_type][$category] = implode('', $reporting_report_newoverviewbox_row[$aggregate_type][$category]);
					}
				}
			}
		}

		foreach($total_year as $aggregate_type => $aggdata) {
			foreach($aggdata as $item) {
				$item['data']['percentage'] = 100;
				eval("\$reporting_report_newtotaloverviewbox_row[$aggregate_type]= \"".$template->get('new_reporting_report_totaloverviewbox_row')."\";");
			}
			eval("\$reporting_report_newtotaloverviewbox[$aggregate_type] = \"".$template->get('new_reporting_report_totaloverviewbox')."\";");
		}



		if(is_array($report['items'])) {
			foreach($report['items'] as $category => $catitem) {
				foreach($aggregate_types as $aggregate_type) {
					eval("\$reporting_report_newoverviewbox[$aggregate_type][$category] = \"".$template->get('new_reporting_report_overviewbox')."\";");
				}
			}
		}


		if(is_array($report['keycustomers']) && ($report['keyCustAvailable'] == 1)) {
			foreach($report['keycustomers'] as $keycust => $customer) {
				eval("\$keycustomers .= \"".$template->get('new_reporting_report_keycustomersbox_customerrow')."\";");
			}
			eval("\$keycustomersbox = \"".$template->get('new_reporting_report_keycustomersbox')."\";");
		}

		if(is_array($report['marketreports']['market']) && ($report['mktReportAvailable'] == 1)) {
			foreach($report['marketreports']['market'] as $marketkey => $marketreport) {
				$marketreport['authors'] = $report['marketreports']['marketauthors'][$marketkey]['displayName'];
				eval("\$marketreportbox .= \"".$template->get('new_reporting_report_marketreportbox')."\";");
				eval("\$marketauthors .= \"".$template->get('new_reporting_report_marketreporauthorstbox_row')."\";");
			}
		}


		eval("\$reports .= \"".$template->get('new_reporting_report')."\";");
	}
	/* loop throw new */





	if(!empty($report['supplier']['logo'])) {
		$report['supplierlogo'] = '<img src="./uploads/entitieslogos/'.$report['supplier']['logo'].'" alt="'.$report['supplier']['companyName'].'" width="200px"/><br /><span style="font-size:12px; font-weight:100;font-style:italic;">'.$report['supplier']['companyName'].'</span>';
	}

	if(is_array($report['representatives'])) {
		foreach($report['representatives'] as $representative) {
			$representatives_list .= "<tr><td style='width: 25%; text-align: left;'>{$representative[name]}</td><td style='text-align: left;'>{$representative[email]}</td></tr>";
		}
	}


	if(is_array($report['contributors'])) {
		$contributors_overview_entries = '';

		foreach($report['affiliates'] as $affid => $contributions) {
			$contributors_overview_entries = '';
			$contributors_overview_entries .= '<tr><td colspan="2" class="thead">'.$report['affiliates']['name'].'</td></tr>';
			if(is_array($report['items'])) {
				$contributors_overview_entries = '';

				foreach($report['marketreports']['market'] as $marketkey => $marketreport) {
					$auditors[$uid] = '<a href="mailto:'.$report['marketreports']['marketauthors'][$marketkey]['email'].'">'.$report['marketreports']['marketauthors'][$marketkey]['displayName'].'</a> (<a href="mailto:'.$report['marketreports']['marketauthors'][$marketkey]['email'].'">'.$report['marketreports']['marketauthors'][$marketkey]['email'].'</a>)';
					$contributors_overview_entries .= '<tr><td class="lightdatacell_freewidth" style="text-align:left;">'.$marketreport['segmenttitle'].'</td><td style="width:70%; border-bottom: 1px dashed #CCCCCC;">'.$auditors[$uid].'</td></tr>';
				}
			}
		}
		eval("\$contributorspage = \"".$template->get('new_reporting_report_contributionoverview')."\";");
	}

	eval("\$coverpage = \"".$template->get('new_reporting_report_coverpage')."\";");
	eval("\$closingpage = \"".$template->get('reporting_report_closingpage')."\";");
	eval("\$highlightbox = \"".$template->get('new_reporting_report_highlightbox')."\";");

	if($core->usergroup['reporting_canApproveReports'] == 1 || $core->usergroup['canViewAllSupp'] == 1) {
		$tools_approve = "<script language='javascript' type='text/javascript'>$(function(){ $('#approvereport').click(function() {
			sharedFunctions.requestAjax('post', 'index.php?module=reporting/newpreview', 'action=approve&identifier={$session_identifier}', 'approvereport_span', 'approvereport_span');}) });</script>";
		$tools_approve .= "<span id='approvereport_span'><a href='#approvereport' id='approvereport'><img src='images/valid.gif' alt='{$lang->approve}' border='0' /></a></span> | ";
	}


	$tool_print = "<span id='printreport_span'><a href='index.php?module=reporting/newpreview&amp;action=print&amp;identifier={$session_identifier}' target='_blank'><img src='images/icons/print.gif' border='0' alt='{$lang->printreport}'/></a></span>";


	$tools = $tools_approve.$tools_send."<a href='index.php?module=reporting/newpreview&amp;action=exportpdf&amp;identifier={$session_identifier}' target='_blank'><img src='images/icons/pdf.gif' border='0' alt='{$lang->downloadpdf}'/></a>&nbsp;".$tool_print;

	eval("\$marketreporauthorstbox = \"".$template->get('new_reporting_report_marketreporauthorstbox')."\";");
	/* Output summary table - START */

	if(!empty($report['summary']['summary'])) {
		eval("\$summarypage = \"".$template->get('new_reporting_report_summary')."\";");
	}
	/* Output summary table  - END */


//if($core->input['referrer'] == 'direct') {
	if($report['isSent'] == 0) {
		if($core->usergroup['reporting_canSendReportsEmail'] == 1) {
			//$unique_array = array_unique($report['spid']);
			//if(count(array_unique($report['spid'])) == 1 || $core->usergroup['canViewAllSupp'] == 1) {
			if(in_array($report['spid'], $core->user['auditfor']) || $core->usergroup['canViewAllSupp'] == 1) {
				$tools_send = "<a href='index.php?module=reporting/preview&amp;action=saveandsend&amp;identifier={$session_identifier}'><img src='images/icons/send.gif' border='0' alt='{$lang->sendbyemail}' /></a> ";
				//eval("\$reportingeditsummary = \"".$template->get('new_reporting_report_editsummary')."\";");
			}
		}
	}
//}
//}




	eval("\$overviewpage .= \"".$template->get('new_reporting_report_overviewpage')."\";");
	$reports = $coverpage.$contributorspage.$summarypage.$overviewpage.$reports.$closingpage;

//$reports = $coverpage.$contributorspage.$summarypage.$reporting_report_newoverviewbox['segments']['amount'].$reporting_report_newoverviewbox['products']['amount'].$keycustomersbox.$marketreportbox.$marketreporauthorstbox.$reportauthors.$closingpage;
	eval("\$reportspage = \"".$template->get('new_reporting_preview')."\";");
	output_page($reportspage);
}
else {
	/* exportpdf,print,saveandsend,approve ---START */
	if($core->input['action'] == 'exportpdf' || $core->input['action'] == 'print' || $core->input['action'] == 'saveandsend' || $core->input['action'] == 'approve') {

		if($core->input['action'] == 'print') {
			$content = "<link href='{$core->settings[rootdir]}/report_printable.css' rel='stylesheet' type='text/css' />";
			$content .= "<script language='javascript' type='text/javascript'>window.print();</script>";
		}
		else {
			$content = "<link href='styles.css' rel='stylesheet' type='text/css' />";
			$content .= "<link href='report.css' rel='stylesheet' type='text/css' />";
		}
		/* pdf  Printing ----START */
		require_once ROOT.'/'.INC_ROOT.'html2pdf/html2pdf.class.php';
		$html2pdf = new HTML2PDF('P', 'A4', 'en');
		$html2pdf->pdf->SetDisplayMode('fullpage');
		$html2pdf->pdf->SetTitle($report['supplier']['companyName'], true);
		$content = iconv("UTF-8", "ISO-8859-1//TRANSLIT", $content);


		if($core->input['action'] == 'saveandsend') {
			set_time_limit(0);
			$html2pdf->WriteHTML($content, $show_html);
			$html2pdf->Output($core->settings['exportdirectory'].'quarterlyreports_'.$core->input['identifier'].'.pdf', 'F');
			redirect('index.php?module=reporting/sendbymail&amp;identifier='.$core->input['identifier']);
		}



		/* pdf  Printing ----END */
	}

	/* exportpdf,print,saveandsend,approve ---END */


	/* 	action Submit ----START */
	if($core->input['action'] == 'do_savesummary') {
		$reportsids = unserialize($session->get_phpsession('reportsmetadata_'.$core->input['identifier']))['rid'];

		if(empty($core->input['summary'])) {
			error($lang->fillrequiredfields
			);
		}
		else {
			$summary_report = array(
					'uid' => $core->user['uid'],
					'summary' => $core->sanitize_inputs($core->input['summary'], array('method' => 'striponly', 'allowable_tags' => '<span><div><a><br><p><b><i><del><strike><img><video><audio><embed><param><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><figure><figcaption><strong><em><table><tr><td><th><tbody><thead><                tfoot><h1><h2><h3><h4><h5><h6>', 'removetags' => true))
			);

			if(!empty($core->input['rpsid'])) {
				$query = $db->update_query('reporting_report_summary', $summary_report, 'rpsid='.intval($core->input['rpsid']));
			}
			else {
				$query = $db->insert_query('reporting_report_summary', $summary_report);
				if($query) {
					$db->update_query('reports', array('summary' => $db->last_id()), 'rid IN ('.$db->escape_string(implode(',', $reportsids)).')');
				}
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
	}
}

/* action Submit ----END */
?>
