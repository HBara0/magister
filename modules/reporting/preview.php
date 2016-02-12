<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: preview.php
 * Created:        @tony.assaad            |
 * Last Update:    @tony.assaad    March 26, 2013 | 3:24:11 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['canFillReports'] == 0 && $core->usergroup['reporting_canViewComptInfo'] == 0 && $core->usergroup['canGenerateReports'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$session->start_phpsession();
if(!$core->input['action']) {
    $default_rounding = 2; //Later a setting
    $reportcache = new Cache();
    $categories_uom = array('amount' => 'K. USD', 'purchasedQty' => 'MT/Units', 'soldQty' => 'MT/Units');
    $aggregate_types = array('affiliates', 'segments', 'products');
    $reporting_quarter = currentquarter_info(false);
    $report_currencies = array();
    $toc_sequence = 5;
    $reportsinconsistency = false;
    if($core->input['referrer'] == 'generate' || $core->input['referrer'] == 'list') {
        if(!isset($core->input['year'], $core->input['quarter'], $core->input['spid'], $core->input['affid'])) {
            redirect('index.php?module=reporting/generatereport');
        }

        if($core->input['generateType'] == 1) {
            $generate_by = $core->input['affid'];
        }
        else {
            $generate_by = $core->input['spid'];
            if($core->input['referrer'] == 'list') {
                $core->input['incMarketReport'] = $core->input['incKeyCustomers'] = $core->input['incKeyProducts'] = $core->input['genByProduct'] = 1;
                $generate_by = array($core->input['spid']);
            }
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
            $generate_by = $core->input['affid'];
        }
        else {
            redirect('index.php?module=reporting/generatereport');
        }
    }
    else {
        $generate_by = array(''); //Dummy array
    }

    /* Transperent fill */
    if(isset($core->input['transFill']) && !empty($core->input['transFill'])) {
        $transfill = $core->input['transFill'];
    }

    foreach($generate_by as $index => $entity) {
        if($core->input['referrer'] == 'generate' || $core->input['referrer'] == 'list' || $core->input['referrer'] == 'direct') {
            if($core->input['referrer'] != 'generate') {
                $core->input['incKeyCustomers'] = $core->input['incMarketReport'] = 1;
            }

            if($core->input['generateType'] == 1) {
                $report_param['affid'] = $entity;
                $report_param['spid'] = $db->escape_string($core->input['spid']);
            }
            else {
                $report_param['affid'] = $db->escape_string($core->input['affid']);
                $report_param['spid'] = $entity;
            }
            $session_identifier = md5(uniqid(microtime()));
            $newreport = new ReportingQr(array('year' => $core->input['year'], 'spid' => $report_param['spid'], 'affid' => $report_param['affid'], 'quarter' => $core->input['quarter']));
            if(!is_object($newreport) || empty($newreport->rid)) {
                error($lang->reportnotfound, $core->settings['rootdir']);
            }
            $report = $newreport->get();
            $auditor = $newreport->user_isaudit();
            $session->set_phpsession(array('reportmeta_'.$session_identifier => serialize($report)));
            $report['affiliates'] = $newreport->get_report_affiliate();
            if($core->usergroup['canGenerateReports'] == 1 || $core->usergroup['canFillReports'] == 1) {
                $newreport->read_products_activity(true);
                $report['forecasteditems'] = $newreport->get_forecasted_items();
                $report['items'] = $newreport->get_classified_productsactivity();
                $report['itemsclasses'] = $newreport->get_classified_classes();
                unset($report['items']['amount']['forecast']);
                $report['productsactivity'] = $newreport->get_products_activity();
                $report['currencies'] = $newreport->get_currencies();
                if(is_array($report['currencies'])) {
                    $report_currencies += $report['currencies'];
                }

                if($core->input['incKeyCustomers'] == 1) {
                    $report['keycustomers'] = $newreport->get_key_customers();
                }

                $report['auditors'] = $newreport->get_report_supplier_audits();
                $report['reportstats'] = $newreport->get_report_status();
                $report['representatives'] = $newreport->get_supplier_representatives();
                $report['summary'] = $newreport->get_report_summary();

                $report['outliers'] = $newreport->check_outliers();
                if(is_array($report['outliers'])) {
                    $report['hasinconsistency'] = true;

                    $reportsissues['inconsistent'][$report['affid']] = '<a href="#qr-'.$report['affid'].'-'.$report['spid'].'">'.$report['affiliates']['name'].'</a>';
                    $reportsissues['inconsistent'][$report['affid']] .= '<ul>';
                    foreach($report['outliers'] as $pid => $outlier) {
                        $product = new Products($pid);
                        $reportsissues['inconsistent'][$report['affid']] .= '<li>'.$product->get()['name'].' | '.$lang->qty.': '.$outlier['quantity'].' | '.$lang->turnover.': '.$outlier['turnOver'].'</li>';
                    }
                    $reportsissues['inconsistent'][$report['affid']] .= '</ul>';

                    if($reportsinconsistency == false) {
                        $reportsinconsistency = true;
                    }
                }
            }

            $report['contributors'] = $newreport->get_report_contributors();
            $report['finializer'] = $newreport->get_report_finalizer();
            $report['supplier'] = $newreport->get_report_supplier();
            if($core->input['incMarketReport'] == 1) {
                $report['marketreports'] = $newreport->get_market_reports();
            }
            $no_send_icon = true;

            if(!$reportcache->iscached('affiliatesmarketreport', $report['affiliates']['affid'])) {
                $reportcache->add('affiliatesmarketreport', $report['affiliates']['name'], $report['affid']);
            }
        }
        else { /* if Referrrer fill  */
            $newreport = new ReportingQr(array('rid' => $core->input['rid']));
            $report = $newreport->get();
            $auditor = $newreport->user_isaudit();
            $report['contributors'] = $newreport->get_report_contributors();
            $report['affiliates'] = $newreport->get_report_affiliate();
            $report['supplier'] = $newreport->get_report_supplier();
            $identifier = $db->escape_string($core->input['identifier']);
            $session_identifier = $identifier;
            /** CHECK
              //
              //            $report_meta = unserialize($session->get_phpsession('reportmeta_'.$identifier));
              //            if(isset($report_meta['auditor']) && !empty($report_meta['auditor'])) {
              //                $options['isauditor'] = $report_meta['auditor'];
              //                $options['transFill'] = $report_meta['transFill'];
              //            }
              /* read productsactivity from fill  data session */
            //  if($session->isset_phpsession('productsactivitydata_'.$identifier)) {
            // $productsactivity = unserialize($session->get_phpsession('productsactivitydata_'.$identifier));
            $productsactivity = ProductsActivity::get_data(array('rid' => $core->input['rid']), array('returmarray' => true));
            //  $report_meta['excludeProductsActivity'] = $productsactivity['excludeProductsActivity'];
            //  unset($productsactivity['module']);
            $report['productsactivity'] = $reportdata['productactivitydata'] = $productsactivity; // ['productactivity'];

            /* Insert produt data coming from the session those are not saved yet --START */
            if(is_array($productsactivity)) {
                $newreport->save_productactivity($productsactivity['productactivity'], unserialize($session->get_phpsession('reportcurrencies_'.$identifier)), $options);
            }
            /* Insert produt data coming from the session those are not saved yet --END */
            $newreport->read_products_activity(true);
            $report['items'] = $newreport->get_classified_productsactivity();
            $report['itemsclasses'] = $newreport->get_classified_classes();
            //  }
            /* read keycustomersdata from fill  data session */
//            if($session->isset_phpsession('keycustomersdata_'.$identifier)) {
//                $keycustomersdata = unserialize($session->get_phpsession('keycustomersdata_'.$identifier));
//                $report_meta['excludeKeyCustomers'] = $core->input['incKeyCustomers'] = $keycustomersdata['excludeKeyCustomers'];
//
//                if(empty($report_meta['excludeKeyCustomers'])) {
//                    $report['keycustomers'] = $reportdata['keycustomersdata'] = $keycustomersdata['keycustomers'];
//                }
//                unset($keycustomersdata['module']);
//            }

            /* Set the marketrport data by serializing the inputs in the stage market report */
            $marketreports_objs = MarketReport::get_data(array('rid' => $db->escape_string($core->input['rid'])), array('returnarray' => true));
            if(is_array($marketreports_objs)) {
                foreach($marketreports_objs as $marketreports_obj) {
                    $report['marketreports'][$marketreports_obj->mrid] = $report['marketreports'][$marketreports_obj->mrid] = $marketreports_obj->get();
                }
//                $marketreportdata = serialize($core->input);
//                $report['marketreports'] = $core->input['marketreport'];
//                $reportdata['marketreportdata'] = $report['marketreports'];
                // $session->set_phpsession(array('marketreport_'.$identifier => $marketreportdata));
            }

            //    $session->set_phpsession(array('reportmeta_'.$session_identifier => serialize($report_meta)));
            $session->set_phpsession(array('reportrawdata_'.$session_identifier => serialize($reportdata)));
        }

        $reports_meta_data['rid'][] = $report['rid'];
        $reports_meta_data['spid'][] = $report['spid'];

        /* Get affiliate currency */
        //$report['affiliate'] = new Affiliates($report['affid']);
        //$report['affiliate']->get_country()->get_currency()->get()['alphaCode'];
        $affiliate_currency = $db->fetch_field($db->query('SELECT alphaCode FROM affiliates a JOIN countries c ON (c.coid=a.country) JOIN currencies cr ON (c.mainCurrency=cr.numCode) WHERE a.affid='.$report['affid']), 'alphaCode');
        if(!empty($affiliate_currency)) {
            $report_currencies[$affiliate_currency] = $affiliate_currency;
        }
        /* Get affiliate currency */
        $report_years = array('current_year' => $report['year'], 'before_1year' => $report['year'] - 1, 'before_2years' => $report['year'] - 2);
        asort($report_years);
        $report['quartername'] = 'Q'.$report['quarter'].' '.$report['year'];
        $item = array();
        if($core->usergroup['canGenerateReports'] == 1 || $core->usergroup['canFillReports'] == 1) {
            if(is_array($report['items'])) {
                foreach($aggregate_types as $aggregate_type) {
                    foreach($report['items'] as $category => $catitem) {/* amount or  quantity */
                        foreach($catitem as $type => $typeitem) { /* actual or forecast */
                            foreach($report_years as $yearef => $year) {
                                if($type == 'forecast' && $year != $report['year']) {
                                    continue;
                                }
                                for($quarter = 1; $quarter <= 4; $quarter++) {
                                    switch($aggregate_type) {
                                        case 'affiliates':
                                            if(is_array($report['items'][$category][$type][$year][$quarter])) {
                                                foreach($report['items'][$category][$type][$year][$quarter] as $affid => $affiliatedata) {
                                                    $item[$aggregate_type][$category][$affid]['name'] = $total_year[$aggregate_type][$category][$type][$affid]['name'] = $newreport->get_report_affiliate($affid)['name'];
                                                    $item[$aggregate_type][$category][$affid][$type][$year][$quarter] = array_sum_recursive($report['items'][$category][$type][$year][$quarter][$affid]);

//												if($year == $reporting_quarter['year'] && $quarter > $reporting_quarter['quarter']) {
//													$item_class[$aggregate_type][$category][$affid][$type][$year][$quarter] = 'mainbox_forecast';
//												}
                                                    $total_year[$aggregate_type][$category][$type][$affid][$year] += $item[$aggregate_type][$category][$affid][$type][$year][$quarter];

                                                    $boxes_totals['mainbox'][$aggregate_type][$category][$type][$year][$quarter] += $item[$aggregate_type][$category][$affid][$type][$year][$quarter];
                                                    if($item[$aggregate_type][$category][$affid][$type][$year][$quarter] < 0) {
                                                        $item[$aggregate_type][$category][$affid][$type][$year][$quarter] = 0;
                                                        $warnnegative = '<br><div><span style="color:red">'.$lang->warningnegativeandzeronumbers.'</span></div>';
                                                    }
//												$item_rounding = 0;
//												if($item[$aggregate_type][$category][$affid][$type][$year][$quarter] < 1) {
//													$item_rounding = $default_rounding;
//												}
//												$item[$aggregate_type][$category][$affid][$type][$year][$quarter] = round($item[$aggregate_type][$category][$affid][$type][$year][$quarter], $item_rounding);
                                                }
                                            }
                                            break;
                                        case 'segments':
                                            if(is_array($report['items'][$category][$type][$year][$quarter])) {
                                                //$item['name'] = '';
                                                //$item['name'] = $newreport->get_report_productsegment($report['productsactivity'] ['spid'])['segment'];
                                                foreach($report['items'][$category][$type][$year][$quarter] as $affid => $affiliatedata) {
                                                    foreach($affiliatedata as $spid => $segmentdata) {
                                                        $item[$aggregate_type][$category][$spid]['name'] = $total_year[$aggregate_type][$category][$type][$spid]['name'] = $newreport->get_productssegments()[$spid];

                                                        $total_year[$aggregate_type][$category][$type][$spid][$year] += $item[$aggregate_type][$category][$spid][$type][$year][$quarter];

//													if($year == $reporting_quarter['year'] && $quarter > $reporting_quarter['quarter']) {
//														$item_class[$aggregate_type][$category][$spid][$type][$year][$quarter] = 'mainbox_forecast';
//													}
                                                        $boxes_totals['mainbox'][$aggregate_type][$category][$type][$year][$quarter] += $item[$aggregate_type][$category][$spid][$type][$year][$quarter];
                                                        $item[$aggregate_type][$category][$spid][$type][$year][$quarter] = array_sum($report['items'][$category][$type][$year][$quarter][$affid][$spid]);
                                                        if($item[$aggregate_type][$category][$affid][$type][$year][$quarter] < 0) {
                                                            $item[$aggregate_type][$category][$affid][$type][$year][$quarter] = 0;
                                                            $warnnegative = '<br><div><span style="color:red">'.$lang->warningnegativeandzeronumbers.'</span></div>';
                                                        }
//													$item_rounding = 0;
//													if($item[$aggregate_type][$category][$spid][$type][$year][$quarter] < 1) {
//														$item_rounding = $default_rounding;
//													}
//													$item[$aggregate_type][$category][$spid][$type][$year][$quarter] = round($item[$aggregate_type][$category][$spid][$type][$year][$quarter], $item_rounding);
                                                    }
                                                }
                                            }

                                            break;

                                        case 'products':
                                            if(is_array($report['items'][$category][$type][$year][$quarter])) {
                                                foreach($report['items'][$category][$type][$year][$quarter] as $affid => $affiliatedata) {
                                                    foreach($affiliatedata as $spid => $segmentdata) {
                                                        foreach($segmentdata as $pid => $productdata) {
                                                            $item[$aggregate_type][$category][$pid]['name'] = $total_year[$aggregate_type][$category][$type][$spid]['name'] = $newreport->get_products()[$pid];
                                                            $item[$aggregate_type][$category][$pid][$type][$year][$quarter] = $report['items'][$category][$type][$year][$quarter][$affid][$spid][$pid];

                                                            $item_class[$aggregate_type][$category][$pid][$type][$year][$quarter] = $report['itemsclasses'][$category][$type][$year][$quarter][$affid][$spid][$pid];

                                                            $total_year[$aggregate_type][$category][$type][$pid][$year] += $item[$aggregate_type][$category][$pid][$type][$year][$quarter];
                                                            $boxes_totals['mainbox'][$aggregate_type][$category][$type][$year][$quarter] += $item[$aggregate_type][$category][$pid][$type][$year][$quarter];
                                                            if($item[$aggregate_type][$category][$affid][$type][$year][$quarter] < 0) {
                                                                $item[$aggregate_type][$category][$affid][$type][$year][$quarter] = 0;
                                                                $warnnegative = '<br><div><span style="color:red">'.$lang->warningnegativeandzeronumbers.'</span></div>';
                                                            }
                                                            //$item_rounding = 0;
//														if($item[$aggregate_type][$category][$pid][$type][$year][$quarter] < 1) {
//															$item_rounding = $default_rounding;
//														}
//														$item[$aggregate_type][$category][$pid][$type][$year][$quarter] = round($item[$aggregate_type][$category][$pid][$type][$year][$quarter], $item_rounding);
                                                        }
                                                    }
                                                }
                                            }
                                            break;
                                    }
                                }
                            }
                        }
                    }
                }

                $temp_item = $item;
                $item = array();
                foreach($temp_item as $aggregate_type => $aggregate_data) {
                    if($aggregate_type != 'affiliates') {
                        $reporting_report_newoverviewbox[$aggregate_type] = $reporting_report_newoverviewbox_row[$aggregate_type] = array();
                    }
                    foreach($aggregate_data as $category => $cat_data) { /* amount or  quantity */
                        foreach($cat_data as $iid => $item) {
                            $item[$aggregate_type][$category] = $item;
                            foreach($report_years as $yearef => $year) {
                                $colspan = 0;
                                for($quarter = 1; $quarter <= 4; $quarter++) {
                                    if(!isset($boxes_totals['mainbox'][$aggregate_type][$category]['actual'][$year][$quarter])) {
                                        $boxes_totals['mainbox'][$aggregate_type][$category]['actual'][$year][$quarter] = 0;
                                    }

                                    if(!isset($item[$aggregate_type][$category]['actual'][$year][$quarter])) {
                                        $item[$aggregate_type][$category]['actual'][$year][$quarter] = 0;
                                    }

                                    $item_rounding = 0;
                                    if($item[$aggregate_type][$category]['actual'][$year][$quarter] < 1 && $item[$aggregate_type][$category]['actual'][$year][$quarter] != 0) {
                                        $item_rounding = $default_rounding;
                                    }

                                    /* Format numbers for output if we have forecast for the coming quarters */
                                    if($year == $report['year'] && isset($report['forecasteditems'][$category]['actual'][$year][$quarter])) {
                                        $item_outputmerged += $item[$aggregate_type][$category]['actual'][$year][$quarter];
                                        $colspan++;
                                    }
                                    elseif($year == $report['year'] && $quarter != 1) {
                                        $mergeditem_output['forecastmergedcell'] .= '<td class="altrow2 mainbox_datacell">'.number_format($item[$aggregate_type][$category]['actual'][$year][$quarter], $item_rounding, '.', ' ').'</td>';
                                    }

                                    $item_output[$aggregate_type][$category]['actual'][$year][$quarter] = number_format($item[$aggregate_type][$category]['actual'][$year][$quarter], $item_rounding, '.', ' ');
                                    //$item_output[$aggregate_type][$category]['actual'][$year][$quarter]+=$item_output[$aggregate_type][$category]['actual'][$year][$quarter];

                                    $item_rounding = 0;
                                    if($boxes_totals['mainbox'][$aggregate_type][$category]['actual'][$year][$quarter] < 1 && $boxes_totals['mainbox'][$aggregate_type][$category]['actual'][$year][$quarter] != 0) {
                                        $item_rounding = $default_rounding;
                                    }

                                    if($boxes_totals['mainbox'][$aggregate_type][$category]['actual'][$year][$quarter] < 0) {
                                        $boxes_totals['mainbox'][$aggregate_type][$category]['actual'][$year][$quarter] = 0;
                                        $warnnegative = '<br><div><span style="color:red">'.$lang->warningnegativeandzeronumbers.'</span></div>';
                                    }

                                    $boxes_totals_output['mainbox'][$aggregate_type][$category]['actual'][$year][$quarter] = number_format($boxes_totals['mainbox'][$aggregate_type][$category]['actual'][$year][$quarter], $item_rounding, '.', ' ');

//								if($year == $reporting_quarter['year'] && $quarter > $reporting_quarter['quarter']) {
//									$boxes_totals_merged = $boxes_totals['mainbox'][$aggregate_type][$category]['actual'][$year][$quarter];
//								}
//								elseif($year == $report['year'] && $quarter > 1) {
//
//								}
                                    /* Store stacked bar chart data */
                                    $report_charts_data[$aggregate_type][$category]['actual']['y']['Q'.$quarter][$year] = $boxes_totals['mainbox'][$aggregate_type][$category]['actual'][$year][$quarter];
                                    $report_segment_charts_data[$aggregate_type][$category]['actual']['y']['Q'.$quarter][$year] = $boxes_totals['mainbox'][$aggregate_type][$category]['actual'][$year][$quarter];
                                }
                            }

                            if($colspan > 0) {
                                $mergeditem_output['forecastmergedcell'] .= '<td colspan="'.$colspan.'"  class="altrow2 mainbox_forecast">'.$item_outputmerged.'</td>';
                            }
                            //$item[$aggregate_type][$category]['actual'][$year][$quarter] = msort($item[$aggregate_type][$category]['actual'], array('quarter'));
                            eval("\$reporting_report_newoverviewbox_row[$aggregate_type][$category] .= \"".$template->get('new_reporting_report_overviewbox_row')."\";");
                            $mergeditem_output['forecastmergedcell'] = '';
                            $item_outputmerged = 0;
                        }
                        if(is_array($reporting_report_newoverviewbox_row[$aggregate_type][$category])) {
                            $reporting_report_newoverviewbox_row[$aggregate_type][$category] = implode('', $reporting_report_newoverviewbox_row[$aggregate_type][$category]);
                        }

                        $lang->$category = $lang->{(strtolower($category))};

                        /* Loop totals to parse forecasts - START */
                        foreach($report_years as $yearef => $year) {
                            $colspan = 0;
                            $item_rounding = 0;
                            for($quarter = 1; $quarter <= 4; $quarter++) {
                                if($year == $report['year'] && isset($report['forecasteditems'][$category]['actual'][$year][$quarter])) {
                                    if($item[$aggregate_type][$category]['actual'][$year][$quarter] < 1 && $item[$aggregate_type][$category]['actual'][$year][$quarter] != 0) {
                                        $item_rounding = $default_rounding;
                                    }
                                    if($item[$aggregate_type][$category]['actual'][$year][$quarter] == 0) {
                                        $item_outputmerged_total = 0;
                                    }
                                    else {
                                        $item_outputmerged_total+=$item[$aggregate_type][$category]['actual'][$year][$quarter];
                                        $colspan++;
                                    }
                                }
                                elseif($year == $report['year'] && $quarter != 1) {
                                    if(!isset($boxes_totals['mainbox'][$aggregate_type][$category]['actual'][$year][$quarter])) {
                                        $boxes_totals['mainbox'][$aggregate_type][$category]['actual'][$year][$quarter] = 0;
                                    }
                                    if($boxes_totals['mainbox'][$aggregate_type][$category]['actual'][$year][$quarter] < 1 && $boxes_totals['mainbox'][$aggregate_type][$category]['actual'][$year][$quarter] != 0) {
                                        $item_rounding = $default_rounding;
                                    }
                                    $boxes_totals_mergedoutput['mergedmainbox'] .= '<td class="altrow2 mainbox_totalcell">'.number_format($boxes_totals['mainbox'][$aggregate_type][$category]['actual'][$year][$quarter], $item_rounding, '.', ' ').'</td>';
                                }
                            }
                        }
                        if($colspan > 0) {
                            $boxes_totals_mergedoutput['mergedmainbox'] .='<td colspan="'.$colspan.'" class="altrow2 mainbox_totalcell">'.number_format($item_outputmerged_total, $item_rounding, '.', ' ').'</td>';
                        }
                        /* Loop totals to parse forecasts - END */

                        /* Generate Chart */
                        if($aggregate_type == 'affiliates') {
                            $overviewbox_chart = new Charts(array('x' => $report_years, 'y' => $report_charts_data[$aggregate_type][$category]['actual']['y']), 'stackedbar');
                            $reporting_report_newoverviewbox_chart = '<img src="'.$overviewbox_chart->get_chart().'" />';
                        }

//					if($aggregate_type == 'segments') {
//						$overviewboxsegment_chart = new Charts(array('x' => $report_years, 'y' => $report_segment_charts_data[$aggregate_type][$category]['actual']['y']), 'linebar');
//						$reporting_report_newoverviewbox_chart = '<img src="'.$overviewboxsegment_chart->get_chart().'" />';
//					}
                        $toc_data[5]['affiliatesoverview'] = array('title' => $lang->activityby.' '.$lang->affiliate);
                        eval("\$reporting_report_newoverviewbox[$aggregate_type][$category] = \"".$template->get('new_reporting_report_overviewbox')."\";");
                        $boxes_totals_mergedoutput['mergedmainbox'] = '';
                        $reporting_report_newoverviewbox_chart = '';
                        $item_outputmerged_total = 0;
                    }
                }

                $report_charts_data['segments'] = $report_charts_data['products'] = array();
                $item = $boxes_totals['mainbox']['segments'] = $boxes_totals['mainbox']['products'] = array();
            }
            $item = array();

            $keycustomersbox = $keycustomers = '';
//            if(is_array($report['keycustomers'])) {
//                $keycust_count = 0;
//                foreach($report['keycustomers'] as $keycust => $customer) {
//                    /* Limit to 5 customers */
//                    if($keycust_count == 5) {
//                        break;
//                    }
//
//                    if(empty($customer['cid'])) {
//                        continue;
//                    }
//                    $customer['companyName'] = ucwords(strtolower($customer['companyName']));
//                    eval("\$keycustomers .= \"".$template->get('new_reporting_report_keycustomersbox_customerrow')."\";");
//                    $keycust_count++;
//                }
//                eval("\$keycustomersbox = \"".$template->get('new_reporting_report_keycustomersbox')."\";");
//            }
        }

        $marketreportbox = '';
        if(is_array($report['marketreports'])) {
            foreach($report['marketreports'] as $mrid => $marketreport) {
                if(!$reportcache->iscached('marketsegments', $marketreport['psid'])) {
                    $reportcache->add('marketsegments', $marketreport['segmenttitle'], $marketreport['psid']);
                }
                $segment = ProductsSegments::get_data(array('psid' => $marketreport['psid']));
                if(is_object($segment)) {
                    $marketreport['segmenttitle'] = $segment->get_displayname();
                }
                if(isset($marketreport['exclude']) && $marketreport['exclude'] == 1) {
                    continue;
                }

                if(!empty($marketreport['authors'])) {
                    $mkauthors_overview[$report['affid']][$marketreport['psid']] = $marketreport['authors'];

                    $marketreport['authors_output'] = $lang->authors.': ';
                    $marketreportbox_comma = '';
                    foreach($marketreport['authors'] as $author) {
                        $marketreport['authors_output'] .= $marketreportbox_comma.$author['displayName'];
                        $marketreportbox_comma = ', ';
                    }
                }

                foreach($marketreport as $key => $val) {
                    if(!is_numeric($val) && !is_array($val)) {
                        //  $marketreport[$key] = nl2br($val);
                        $marketreport[$key] = str_replace(array('&lt;', '&gt;'), array('<', '>'), $val);
                    }
                }

                array_walk($marketreport, 'parse_ocode');
                if(($core->usergroup['reporting_canViewComptInfo'] == 1) || ($core->usergroup['canGenerateReports'] == 1 || $core->usergroup['canFillReports'] == 1)) {
                    eval("\$marketreportbox_competition = \"".$template->get('new_reporting_report_marketreportbox_competition')."\";");
                }
                /* Parse Mrket Competition - Start */
                $marketcompetition = MarketReportCompetition::get_data(array('mrid' => $mrid), array('returnarray' => true));
                if(is_array($marketcompetition)) {
                    foreach($marketcompetition as $mrcompetition) {
                        $altrow = alt_row($altrow);
                        if($mrcompetition->sid == 0 && $mrcompetition->coid == 0) {
                            $unspecified_competitor = $mrcompetition;
                            // continue;
                            $competitior_label = $lang->unspecifiedsupplier;
                        }
                        if($mrcompetition->sid != 0) {
                            $supplier = Entities::get_data(array('eid' => $mrcompetition->sid, 'type' => 'cs'));
                            if(is_object($supplier)) {
                                $supplier_name = $supplier->get_displayname();
                                $competitior_label = $lang->competitorsupplier;
                            }
                        }
                        if($mrcompetition->coid != 0) {
                            $country = Countries::get_data(array('coid' => $mrcompetition->coid));
                            if(is_object($country)) {
                                $country_name = $country->get_displayname();
                                $competitior_country = $lang->competitororigin;
                            }
                        }
                        $competitionproducts = MarketReportCompetitionProducts::get_data(array('mrcid' => $mrcompetition->mrcid), array('returnarray' => true));
                        if(is_array($competitionproducts)) {
                            foreach($competitionproducts as $mrproduct_obj) {
                                $mrproduct = $mrproduct_obj->get();
                                if($mrproduct['csid'] != 0) {
                                    $chemicalsubs = new Chemicalsubstances($mrproduct['csid']);
                                }
                                if($mrproduct['pid'] != 0) {
                                    $product = new Products($mrproduct['csid']);
                                }
                                if(is_object($chemicalsubs)) {
                                    $chemicalsubstance_name = $chemicalsubs->get_displayname();
                                    $label = $lang->chemicalsubstance;
                                }
                                if(is_object($products)) {
                                    $product_name = $product->get_displayname();
                                    $label = $lang->product;
                                }
                                eval("\$product_row .= \"".$template->get('reporting_previewreport_marketreport_suppproducts')."\";");
                                unset($chemicalsubstance_name, $chemicalsubs, $product, $product_name);
                            }
                        }

                        eval("\$markerreport_segment_suppliers_row .= \"".$template->get('reporting_previewreport_marketreport_suppliers_rows')."\";");
                        unset($product_row, $supplier, $country, $supplier_name, $country_name, $competitior_label, $competitior_country);
                    }
                    eval("\$markerreport_segment_suppliers = \"".$template->get('reporting_previewreport_marketreport_suppliers')."\";");
                    unset($markerreport_segment_suppliers_row);
                }
                /* Parse Mrket Competition - End */

                if($core->usergroup['canGenerateReports'] == 1 || $core->usergroup['canFillReports'] == 1) {
                    eval("\$marketreportbox_other = \"".$template->get('new_reporting_report_marketreportbox_other')."\";");
                }
                $criteriaandstars .= '<div class="evaluation_criterium" name="'.$marketreport['psid'].'_'.$mrid.'"><div class="criterium_name" style="display:inline-block; width:15%;">'.$lang->ratecontentquality.'</div>';
                $criteriaandstars .= '<div class="ratebar" style="width:40%; display:inline-block; background-color:#FFF;">';
                if(!isset($marketreport['rating']) || empty($marketreport['rating'])) {
                    $ratingval = 0;
                }
                else {
                    $ratingval = $marketreport['rating'];
                }

                if($auditor == false) {
                    $criteriaandstars .= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-value="'.$ratingval.'"></div>';
                }
                else {
                    $header_ratingjs = '$(".rateit").click(function() {
					if(sharedFunctions.checkSession() == false) {
						return;
					}
					var targetid = $(this).parent().parent().attr("name");
					var returndiv = "";
                                        var val=$("#rating_"+targetid).val();
                                        var ids=targetid.split("_");
                                        if(ids[1].length < 1 || ids[0].length < 1 ){
                                        return;
                                        }
                                        if(val.length >0){
					sharedFunctions.requestAjax("post", "index.php?module=reporting/preview&action=do_ratesegment", "target="+ids[0]+"&value="+val+"&repid="+ids[1], returndiv, returndiv, "html");
                                        }
				});';
                    $criteriaandstars .= '<input type="range" min="0" max="5" value="'.$ratingval.'" step="1" id="rating_'.$marketreport['psid'].'_'.$mrid.'" class="ratingscale">';
                    $criteriaandstars .= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-resetable="false" data-rateit-backingfld="#rating_'.$marketreport['psid'].'_'.$mrid.'" data-rateit-value="'.$marketreport['rating'].'"></div>';
                }
                eval("\$marketreportbox .= \"".$template->get('new_reporting_report_marketreportbox')."\";");
                unset($mom_followupactions, $criteriaandstars, $markerreport_segment_suppliers);
            }
        }

        /* Parse MOM Specific Follow Up Actions - START */
        $quarter_start = strtotime($report['year'].'-'.$core->settings['q'.$report['quarter'].'start']);
        $quarter_end = strtotime($report['year'].'-'.$core->settings['q'.$report['quarter'].'end']);
        $momactions_where = '(date BETWEEN '.$quarter_start.' AND '.$quarter_end.') AND momid IN (select momid from meetings_minsofmeeting where mtid IN '
                .'(select mtid from meetings_associations where idAttr="spid" AND id='.$report['spid'].'))';
        $momactions = MeetingsMOMActions::get_data(array('filter' => $momactions_where), array('returnarray' => true, 'operators' => array('filter' => CUSTOMSQLSECURE)));
        if(is_array($momactions)) {
            foreach($momactions as $key => $actions) {
                /* The actions are associated to the QR affiliate (primarily) or its employees are assigned to the actions (secondary) */
                $meetings_affassociations = MeetingsAssociations::get_data(array('id' => $report[affid], 'idAttr' => 'affid', 'mtid' => 'mtid=(select mtid from meetings_minsofmeeting where momid='.$actions->momid.')'), array('returnarray' => true, 'operators' => array('mtid' => 'CUSTOMSQL')));
                //If actions are associated to the QR affiliate -> continue
                if(is_array($meetings_affassociations)) {
                    continue;
                }
                //Else check if employees of the QR aff are assigned to the actions
                $employeesassigned = false;
                $momactionsassignees = MeetingsMOMActionAssignees::get_data(array('momaid' => $actions->momaid), array('returnarray' => true));
                if(is_array($momactionsassignees)) {
                    foreach($momactionsassignees as $assignee) {
                        if(isset($assignee->uid) && !empty($assignee->uid)) {
                            $user = new Users($assignee->uid);
                            if(is_object($user) && $user->get_mainaffiliate()->affid == $reportmeta['affid']) {
                                $employeesassigned = true;
                            }
                        }
                    }
                }
                if(!$employeesassigned) {
                    unset($momactions[$key]); // if no aff or employees associations do not parse actions
                }
            }
            $mom_obj = new MeetingsMOM();
            $mom_followupactions .= $mom_obj->parse_actions('QR', $momactions);
            $marketreportbox .= '<table class="reportbox">
    <tr>
        <td class="thead">'.$lang->specificfollowactions.'</td>
    </tr>
    <tr><td>'.$mom_followupactions.'</td></tr>
</table>';
        }

        /* Parse MOM Specific Follow Up Actions - end */
        /* Show QR contributors */
        $lang->reportpreparedby_text = $lang->reportpreparedby;
        $lang->email_text = $lang->email;
        if(is_array($report['contributors']) && !empty($report['contributors'])) {
            $contributors = '';
            foreach($report['contributors'] as $contributor) {
                eval("\$contributors .= \"".$template->get('new_reporting_report_contributorrow')."\";");
                $lang->reportpreparedby_text = $lang->email_text = '';
            }
        }
        else {
            $contributor['email'] = $core->user['email'];
            $contributor['displayName'] = $core->user['displayName'];
            eval("\$contributors = \"".$template->get('new_reporting_report_contributorrow')."\";");
        }
        if($core->usergroup['canGenerateReports'] == 1 || $core->usergroup['canFillReports'] == 1) {
            /* record report anchor - START */
            $toc_data[++$toc_sequence]['qr-'.$report['affid'].'-'.$report['spid']] = array('title' => $report['affiliates']['name']);
            /* record report anchor - END */
        }
        eval("\$highlightbox = \"".$template->get('new_reporting_report_highlightbox')."\";");

        eval("\$reports .= \"".$template->get('new_reporting_report')."\";");
        $reporting_report_newoverviewbox['segments'] = $reporting_report_newoverviewbox['products'] = array();
    }
    if($core->usergroup['canGenerateReports'] == 1 || $core->usergroup['canFillReports'] == 1) {
        if(is_array($total_year) && !empty($total_year)) {
            foreach($total_year as $aggregate_type => $aggdata) {
                //$reporting_report_newtotaloverviewbox[$aggregate_type] = $reporting_report_newtotaloverviewbox_row[$aggregate_type] = array();
                foreach($aggdata as $category => $catdata) {
                    foreach($catdata['actual'] as $itemkey => $item) {
                        foreach($report_years as $yearkey => $yearval) {
                            $item['data'][$yearval] = $item[$yearval];
                            $progression_totals['data'][$yearval] += $item['data'][$yearval];

                            if(empty($item['data'][$yearval])) {
                                $item['data'][$yearval] = 0;
                                $progression_totals['perc'][$yearval] = 0;
                            }

                            if($yearval != $report['year']) {
                                if(empty($item['data'][$yearval]) && empty($item[$yearval + 1])) {
                                    $item['perc'][$yearval] = 0;
                                }
                                else {
                                    if(empty($item['data'][$yearval])) {
                                        $item['perc'][$yearval] = 100;
                                    }
                                    else {
                                        $item['perc'][$yearval] = round((($item[$yearval + 1] / $item['data'][$yearval] ) * 100 ) - 100);  /* Divide the next year total ammount with the ammount of previous year */
                                    }
                                }

                                $newtotaloverviewbox_row_percclass[$yearval] = ' totalsbox_perccellpositive';
                                if(($yearval + 1) == $reporting_quarter['year'] && $reporting_quarter['quarter'] < 4) {
                                    $newtotaloverviewbox_row_class[$yearval] = ' mainbox_forecast';
                                }
                                if($item['perc'][$yearval] == 0) {
                                    $newtotaloverviewbox_row_percclass[$yearval] = ' totalsbox_perccellzero';
                                }
                                elseif($item['perc'][$yearval] < 0) {
                                    $newtotaloverviewbox_row_percclass[$yearval] = ' totalsbox_perccellnegative';
                                }
                            }

                            $item_rounding = 0;
                            if($item[$yearval] < 1 && $item[$yearval] != 0) {
                                $item_rounding = $default_rounding;
                            }
                            $item['data'][$yearval] = number_format($item[$yearval], $item_rounding, '.', ' ');
//$item['data'][$yearval] = round($item[$yearval]);
                            /* Store stacked bar chart data */

                            $report_charts_data[$aggregate_type][$category]['actual']['y'][1][$yearval] = $progression_totals['data'][$yearval];
                            $report_affiliate_charts_data[$aggregate_type][$category]['actual']['y'][$item['name']][$yearval] = $progression_totals['data'][$yearval];
                        }

                        eval("\$reporting_report_newtotaloverviewbox_row[$aggregate_type][$category] .= \"".$template->get('new_reporting_report_totaloverviewbox_row')."\";");
                    }

                    foreach($progression_totals['data'] as $year => $total_amount) {
                        if(empty($progression_totals['data'][$year]) && empty($progression_totals['data'][$year + 1])) {
                            $progression_totals['perc'][$year] = 0;
                        }
                        else {
                            if(empty($progression_totals['data'][$year])) {
                                $progression_totals['perc'][$year] = 100;
                            }
                            else {
                                if(!empty($progression_totals['data'][$year]) && !empty($progression_totals['data'][$year + 1])) {
                                    $next_progression_totals = $progression_totals['data'][$year + 1];
                                    $prev_progression_totals = $progression_totals['data'][$year];
                                    $progression_totals['perc'][$year] = round((($next_progression_totals / $prev_progression_totals ) * 100 ) - 100);
                                }
                            }
                        }
                        if(($yearval + 1) == $reporting_quarter['year'] && $reporting_quarter['quarter'] < 4) { /* if the year val equal to  the previous  quarter year */
                            $newtotaloverviewbox_row_class[$yearval] = ' mainbox_forecast';
                        }
                        $newtotaloverviewbox_row_percclass[$year] = ' totalsbox_perccellpositive';
                        if($progression_totals['perc'][$year] == 0) {
                            $newtotaloverviewbox_row_percclass[$yearval] = ' totalsbox_perccellzero';
                        }
                        elseif($progression_totals['perc'][$year] < 0) {
                            $newtotaloverviewbox_row_percclass[$year] = ' totalsbox_perccellnegative';
                        }
                        $item_rounding = 0;
                        if($progression_totals['data'][$year] < 1 && $progression_totals['data'][$year] != 0) {
                            $item_rounding = $default_rounding;
                        }
                        $progression_totals['data'][$year] = number_format($progression_totals['data'][$year], $item_rounding, '.', ' ');
                    }

                    if(is_array($reporting_report_newtotaloverviewbox_row[$aggregate_type][$category])) {
                        $reporting_report_newtotaloverviewbox_row[$aggregate_type][$category] = implode('', $reporting_report_newtotaloverviewbox_row[$aggregate_type][$category]);
                    }
                    /* Generate Chart */
                    if($aggregate_type == 'segments') {
                        $progressionbox_chart = new Charts(array('x' => $report_years, 'y' => $report_charts_data[$aggregate_type][$category]['actual']['y']), 'stackedbar', array('seriesnames' => array(1 => $item['name'])));
                        $reporting_report_newtotaloverviewbox_chart = '<img src="'.$progressionbox_chart->get_chart().'" />';
                    }
                    if($aggregate_type == 'affiliates') {
                        $progressionbox_chart = new Charts(array('x' => $report_years, 'y' => $report_affiliate_charts_data[$aggregate_type][$category]['actual']['y']), 'linebar', array('seriesnames' => array(1 => $item['name'])));
                        $reporting_report_newtotaloverviewbox_chart = '<img src="'.$progressionbox_chart->get_chart().'" />';
                    }

                    eval("\$reporting_report_newtotaloverviewbox[$aggregate_type][$category] = \"".$template->get('new_reporting_report_totaloverviewbox')."\";");
                    $reporting_report_newtotaloverviewbox_row[$aggregate_type][$category] = array();
                    $reporting_report_newtotaloverviewbox_chart = '';
                    $progression_totals['data'] = array();
                    unset($newtotaloverviewbox_row_percclass);
                }
            };
            $toc_data[3]['progressionbyaffiliates'] = array('title' => $lang->progressionyearsby.' '.$lang->affiliates);
            $toc_data[4]['progressionbysegments'] = array('title' => $lang->progressionyearsby.' '.$lang->segments);
        }
    }

    if($core->input['referrer'] == 'generate' || $core->input['referrer'] == 'direct' || $core->input['referrer'] == 'list') {
        if($core->usergroup['canGenerateReports'] == 1 || $core->usergroup['canFillReports'] == 1) {
            if($core->input['referrer'] != 'list') {
                $report['supplierlogo'] = $report['supplier']['companyName'];
                if(!empty($report['supplier']['logo'])) {
                    $report['supplierlogo'] = '<img src="./uploads/entitieslogos/'.$report['supplier']['logo'].'" alt="'.$report['supplier']['companyName'].'" width="200px"/><br /><span style="font-size:12px; font-weight:100;font-style:italic;">'.$report['supplier']['companyName'].'</span>';
                }

                if(is_array($report['representatives'])) {
                    foreach($report['representatives'] as $representative) {
//$representatives_list .= "<div style='width: 35%; text-align: left; display: inline-block;margin: 0px auto;'>{$representative[name]}</div><div style='width: 35%; text-align: left; display: inline-block;margin: 0px auto;'>{$representative[email]}</div>";
                        $representatives_list .= $representative['name'].' - '.$representative['email'].'<br />';
                    }
                }

//Use Cache class where appropriate below
                if(is_array($mkauthors_overview)) {
                    $authors_overview_entries = '';
                    foreach($mkauthors_overview as $affid => $mkauthors) {
                        if(is_array($mkauthors) && !empty($mkauthors)) {
                            $authors_overview_entries .= '<tr><td colspan="2" class="thead">'.$reportcache->data['affiliatesmarketreport'][$affid].'</td></tr>';
                            foreach($mkauthors as $psid => $authors) {
                                $parsed_authors = array();
                                if(is_array($authors)) {
                                    foreach($authors as $uid => $author) {
                                        $parsed_authors[$uid] = '<a href="mailto:'.$author['email'].'">'.$author['displayName'].'</a> (<a href="mailto:'.$author['email'].'">'.$author['email'].'</a>)';
                                    }

                                    if(empty($reportcache->data['marketsegments'][$psid])) {
                                        $reportcache->data['marketsegments'][$psid] = $lang->others;
                                    }
                                    $authors_overview_entries .= '<tr><td class="mainbox_itemnamecell">'.$reportcache->data['marketsegments'][$psid].'</td><td style="width:70%; border-bottom: 1px dotted #CCCCCC;">'.implode('<br />', $parsed_authors).'</td></tr>';
                                }
                            }
                        }
                    }
                    eval("\$contributorspage = \"".$template->get('new_reporting_report_contributionoverview')."\";");
                    $toc_data[2]['contributors'] = array('title' => $lang->reportcontributorsoverview);
                }

                eval("\$coverpage = \"".$template->get('new_reporting_report_coverpage')."\";");
                /* Output summary table - START */
                if(!empty($report['summary']['summary'])) {
                    $toc_data[1]['summary'] = array('title' => $lang->reportsummary);
                    eval("\$summarypage = \"".$template->get('new_reporting_report_summary')."\";");
                }
                /* Output summary table  - END */

                $toc_data[$toc_sequence + 2]['closingpage'] = array('title' => 'Closing Page');
                eval("\$closingpage = \"".$template->get('reporting_report_closingpage')."\";");


                eval("\$marketreporauthorstbox = \"".$template->get('new_reporting_report_marketreporauthorstbox')."\";");

                /* Parse Currencies Table - START */
                if(!isset($report_currencies['USD'])) {
                    $report_currencies['USD'] = 'USD';
                }

                if(is_array($report_currencies) && !empty($report_currencies)) {
                    $fxratespage_tablecolspan = count($report_currencies) + 1;
                    $fxratespage_tablehead .= '<tr><td>&nbsp;</td>';
                    $currencies_from = strtotime($report['year'].'-'.$core->settings['q'.$report['quarter'].'start']);
                    $currencies_to = strtotime($report['year'].'-'.$core->settings['q'.$report['quarter'].'end']);
                    if($report['quarter'] == 1) {
                        $prev_currencies_from = strtotime(($report['year'] - 1).'-'.$core->settings['q4start']);
                        $prev_currencies_to = strtotime(($report['year'] - 1).'-'.$core->settings['q4end']);
                    }
                    else {
                        $prev_currencies_from = strtotime($report['year'].'-'.$core->settings['q'.($report['quarter'] - 1).'start']);
                        $prev_currencies_to = strtotime($report['year'].'-'.$core->settings['q'.($report['quarter'] - 1).'end']);
                    }

                    foreach($report_currencies as $cur) {
                        $fxratespage_tablehead .= '<td style="text-align:center;">'.$cur.'</td>';
                        $currency = new Currencies($cur); //$reports_meta_data['baseCurrency']);

                        $currencies_fx = $currency->get_average_fxrates($report_currencies, array('from' => $currencies_from, 'to' => $currencies_to), array('distinct_by' => 'alphaCode', 'precision' => 4));
                        if(is_array($currencies_fx)) {
                            $fx_rates_entries .= '<tr><td class="namescell" style="text-align:left; width: 2%;">'.$cur.'</td>';
                            foreach($report_currencies as $currkey => $fx_currency) {
                                $trend_symbol = '';
                                if(empty($currencies_fx[$fx_currency])) {
                                    $currencies_fx[$fx_currency] = ' - ';
                                }
                                else {
                                    $currencies_fx[$fx_currency] = round($currencies_fx[$fx_currency], 4);
                                    $prev_rate = $currency->get_average_fxrate($fx_currency, array('from' => $prev_currencies_from, 'to' => $prev_currencies_to), array('distinct_by' => 'alphaCode', 'precision' => 4), $cur);
                                    $trend_symbol = '<div class="arrow-down"></div> ';
                                    if($currencies_fx[$fx_currency] - $prev_rate > 0) {
                                        $trend_symbol = '<div class="arrow-up"></div> ';
                                    }
                                }

                                $fx_rates_entries .= '<td style="width:5%; text-align: center;" class="currenciesbox_datacell">'.$trend_symbol.' '.round($currencies_fx[$fx_currency], 2).'</td>';
                            }
                            $fx_rates_entries .= '</tr>';
                        }
                    }

                    $fxratespage_tablehead .= '</tr>';

                    $currency_rates_year = $currency->get_yearaverage_fxrate_monthbased('USD', $report['year'], array('distinct_by' => 'alphaCode', 'precision' => 4, 'monthasname' => true), 'EUR'); /* GET the fxrate of previous quarter year */
                    if($report['year'] == $reporting_quarter['year']) {
                        $currency_rates_year = array_slice($currency_rates_year, 0, date('n', TIME_NOW));
                    }

                    $overyears_rates = $currency->get_yearaverage_fxrate_yearbased('USD', 2005, $report['year'] - 1, array('distinct_by' => 'alphaCode', 'precision' => 4), 'EUR');
                    $overyears_rates = $overyears_rates + $currency_rates_year;
                    $fxrates_linechart = new Charts(array('x' => array_keys($overyears_rates), 'y' => array('1 EUR' => $overyears_rates)), 'line', array('xaxisname' => 'Months ('.$report['year'].')', 'yaxisname' => 'USD Rate', 'yaxisunit' => '', 'width' => 700, 'height' => 200, 'writelabel' => true));
                    $fx_rates_chart .= '<tr><td style="border-bottom: 1px dashed #CCCCCC; text-align: center;" colspan="'.$fxratespage_tablecolspan.'"><img src="'.$fxrates_linechart->get_chart().'" /></td></tr>';

                    if(!empty($fx_rates_entries)) {
                        $toc_data[$toc_sequence + 1]['currenciesoverview'] = array('title' => $lang->currenciesfxrate);
                        eval("\$fxratespage = \"".$template->get('reporting_report_fxrates')."\";");
                    }
                }
                /* Parse Currencies Table - END */

//if(is_array($report['productsactivity'])){
                eval("\$overviewpage .= \"".$template->get('new_reporting_report_overviewpage')."\";");
//}
            }

            if($core->input['referrer'] == 'direct') {
                if($report['isSent'] == 0) {
                    if($core->usergroup['reporting_canSendReportsEmail'] == 1) {
                        $unique_array = $report['spid'];
                        if(count($report['spid']) == 1 || $core->usergroup['canViewAllSupp'] == 1) {
                            if(in_array($report['spid'], $core->user['auditfor']) || $core->usergroup['canViewAllSupp'] == 1) {
                                $tools_send = "<a href='index.php?module=reporting/sendbymail&amp;identifier={$session_identifier}'><img src='images/icons/send.gif' border='0' alt='{$lang->sendbyemail}' /></a> ";
                                $fillsummary_msg = $core->input['message'];
                                eval("\$reportingeditsummary = \"".$template->get('reporting_report_editsummary')."\";");
                            }
                        }
                    }
                }
            }

            if($report['isApproved'] == 0) {
                if($core->usergroup['reporting_canApproveReports'] == 1) {
                    $can_approve = true;
                    foreach(array_unique($reports_meta_data['spid']) as $key => $val) {
                        if(!in_array($val, $core->user['auditfor'])) {
                            $can_approve = false;
                            break;
                        }
                    }
                    if($can_approve == true || $core->usergroup['canViewAllSupp'] == 1) {
                        $tools_approve = "<script language='javascript' type='text/javascript'>$(function(){ $('#approvereport').click(function() { sharedFunctions.requestAjax('post', 'index.php?module=reporting/preview', 'action=approve&identifier={$session_identifier}', 'approvereport_span', 'approvereport_span');}) });</script>";
                        $tools_approve .= "<span id='approvereport_span'><a href='#approvereport' id='approvereport'><img src='images/valid.gif' alt='{$lang->approve}' border='0' /></a></span> | ";
                    }
                }
            }

            $tool_print = "<span id='printreport_span'><a href='index.php?".http_build_query($core->input, '', '&amp;')."&amp;media=print' target='_blank'><img src='images/icons/print.gif' border='0' alt='{$lang->printreport}'/></a></span>";
//$tools = $tools_approve.$tools_send."<a href='index.php?module=reporting/preview&amp;action=exportpdf&amp;identifier={$session_identifier}' target='_blank'><img src='images/icons/pdf.gif' border='0' alt='{$lang->downloadpdf}'/></a>&nbsp;".$tool_print;
            $tools = $tools_approve.$tools_send.$tool_print;
            ksort($toc_data);
            foreach($toc_data as $sequence => $entry) {
                $toc_entries .= '<div><a class="scrolldown" href=#'.key($entry).'>'.$entry[key($entry)]['title'].'</a></div>';
            }

            eval("\$tablecontent = \"".$template->get('new_reporting_report_tableofcontents')."\";");

            /* Display Warining Notifications - START */
            if($reportsinconsistency == true) {
                $warnings = '<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom:10px; text-align: left;">';
                $warnings .= '<p>'.$lang->reportsinconsistent.'<ul><li>'.implode('</li><li>', $reportsissues['inconsistent']).'</li></ul></p>';
                $warnings .= '</div>';
            }
            /* Display Warining Notifications - END */
        }
        $reports = $warnings.$coverpage.$tablecontent.$contributorspage.$summarypage.$overviewpage.$reports.$fxratespage.$closingpage;

        $session->set_phpsession(array('reports_'.$session_identifier => $reports));
    }
    else {
// Add below to class
        $missing_employees_query1 = $db->query("SELECT DISTINCT(u.uid), displayName
												FROM ".Tprefix."users u JOIN ".Tprefix."assignedemployees ae ON (u.uid=ae.uid)
												WHERE ae.affid='{$report[affid]}' AND ae.eid='{$report[spid]}' AND u.gid IN (SELECT gid FROM usergroups WHERE canUseReporting=1 AND canFillReports=1) AND u.uid NOT IN (SELECT uid FROM ".Tprefix."reportcontributors WHERE rid='{$report[rid]}' AND isDone=1) AND u.uid!={$core->user[uid]}"); // AND rc.rid='{$report[rid]}'

        while($assigned_employee = $db->fetch_assoc($missing_employees_query1)) {
            $missing_employees['name'][] = $assigned_employee['displayName'];
            $missing_employees['uid'][] = $assigned_employee['uid'];
        }

        if(is_array($missing_employees)) {
            $missing_employees_notification = '<div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; font-weight:bold;">'.$lang->employeesnotfillpart.' <ul><li>'.implode('</li><li>', $missing_employees['name']).'</li></ul></div><br />';
        }

        if(($auditor == true && is_array($missing_employees) ) || !is_array($missing_employees)) {
            $reporting_preview_tools_finalize_button = $lang->suretofinalizebody.' <p align="center"><input type="button" id="save_report_reporting/fillreport_Button" value="'.$lang->yes.'" class="button" onclick="$(\'#popup_finalizereportconfirmation\').dialog(\'close\')"/></p>';
            $reporting_preview_tools_finalize_type = 'finalize';
        }
        else {
            $reporting_preview_tools_finalize_button = $lang->cannotfinalizereport.' <p align="center"><input type="button" id="save_report_reporting/fillreport_Button" value="'.$lang->yes.'" class="button" onclick="$(\'#popup_finalizereportconfirmation\').dialog(\'close\')"/></p>';
            $reporting_preview_tools_finalize_type = 'saveonly';
        }
        /* Check who hasn't yet filled in the report - End */
        eval("\$tools .= \"".$template->get('reporting_preview_tools_finalize')."\";");
    }

    $session->set_phpsession(array('reportsmetadata_'.$session_identifier => serialize($reports_meta_data)));
    $session->set_phpsession(array('sessionid' => base64_encode(serialize($session_identifier))));
    if($core->input['media'] == 'print') {
        $headerinc .= '<script language="javascript" type="text/javascript" >window.print();</script>';
        eval("\$reportspage = \"".$template->get('website_reporting_preview')."\";");
    }
    else {
        if($core->input['reportmode'] == 'reportonly') {

            eval("\$header = \"".$template->get('new_reporting_preview_header')."\";");

            $reportspage = $header.$reports.$tools;
        }
        else {
            eval("\$reportspage = \"".$template->get('new_reporting_preview')."\";");
        }
    }
    output_page($reportspage);
}
else {
    if($core->input['action'] == 'do_savesummary') {
        $reportsids = unserialize($session->get_phpsession('reportsmetadata_'.$core->input['identifier']))['rid'];

        if(empty($core->input['summary'])) {
            error($lang->fillrequiredfields);
        }
        else {
            $summary_report = array(
                    'uid' => $core->user['uid'],
                    'summary' => $core->sanitize_inputs($core->input['summary'], array('method' => 'striponly', 'allowable_tags' => '<span><div><a><br><p><b><i><del><strike><img><video><audio><embed><param><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><figure><figcaption><strong><em><table><tr><td><th><tbody><thead><tfoot><h1><h2><h3><h4><h5><h6>', 'removetags' => true))
            );

            if(!empty($core->input['rpsid'])) {
                $query = $db->update_query('reporting_report_summary', $summary_report, 'rpsid='.intval($core->input['rpsid']));
            }
            else {
                $query = $db->insert_query('reporting_report_summary', $summary_report);
                if($query) {
                    if(is_array($reportsids)) {
                        $db->update_query('reports', array('summary' => $db->last_id()), 'rid IN ('.$db->escape_string(implode(',', $reportsids)).')');
                    }
                }
            }
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    elseif($core->input['action'] == 'exportpdf' || $core->input['action'] == 'print' || $core->input['action'] == 'saveandsend' || $core->input['action'] == 'approve') {
        if($core->input['action'] == 'print') {
            $show_html = 1;
            $content = "<link href='{$core->settings[rootdir]}/report_printable.css' rel='stylesheet' type='text/css' />";
            $content .= "<script language='javascript' type='text/javascript'>window.print();</script>";
        }
        else {
            $content = "<link href='styles.css' rel='stylesheet' type='text/css' />";
            $content .= "<link href='./css/report.css' rel='stylesheet' type='text/css' />";
        }
        $content .= $session->get_phpsession('reports_'.$core->input['identifier']);

        $meta_data = unserialize($session->get_phpsession('reportsmetadata_'.$core->input['identifier']));
        $newreport = new ReportingQr(array('rid' => $meta_data['rid'][0]));
        $report = $newreport->get();
        $report['reqQRSummary'] = $newreport->get_report_supplier()['reqQRSummary'];
        $report['affiliates'] = $newreport->get_report_affiliate();

        $suppliername = $newreport->get_report_supplier()['companyName'];
        ob_end_clean();

        require_once ROOT.'/'.INC_ROOT.'html2pdf/html2pdf.class.php';
        $html2pdf = new HTML2PDF('P', 'B4', 'en', TRUE, 'UTF-8');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->pdf->SetTitle($suppliername, true);
//$content = html_entity_decode($content, ENT_XHTML, 'ISO-8859-1');
//$content = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $content);
        $content = str_replace(array('&uarr;', '&darr;'), array('^', '<sub>v</sub>'), $content);

        if($core->input['action'] == 'saveandsend') {
            set_time_limit(0);
            ini_set('memory_limit', '200M');
            if(empty($report['summary']) && $report['reqQRSummary'] == 1) {
                error($lang->summarymissing, $_SERVER['HTTP_REFERER']);
            }
            else {
                $html2pdf->WriteHTML($content, $show_html);
                $html2pdf->Output($core->settings['exportdirectory'].'quarterlyreports_'.$core->input['identifier'].'.pdf', 'F');
                redirect('index.php?module=reporting/sendbymail&amp;identifier='.$core->input['identifier']);
            }
        }
        elseif($core->input['action'] == 'approve') {
            $reportsids = unserialize($session->get_phpsession('reportsmetadata_'.$core->input['identifier']))['rid'];
            if($core->usergroup['reporting_canApproveReports'] == 1) {
                foreach($reportsids as $key => $val) {
                    $newreport->approve_report($val);
                }

                switch($newreport->get_status()) {
                    case 0:
                        output_xml("<status>true</status><message>{$lang->approved}</message>");
                        $log->record($meta_data['rid'], 'approve');
                        break;
                }

                if($core->settings['sendreportsonapprove'] == 1) {
                    $html2pdf->WriteHTML($content, $show_html);
                    $html2pdf->Output($core->settings['exportdirectory'].'quarterlyreports_'.$core->input['identifier'].'.pdf', 'F');

                    if(empty($core->settings['sendreportsto'])) {
                        $core->settings['sendreportsto'] = $core->settings['adminemail'];
                    }

                    $query = $db->query("SELECT r.quarter, r.year, a.name, s.companyName
										FROM ".Tprefix."reports r, ".Tprefix."entities s, ".Tprefix."affiliates a
										WHERE r.spid=s.eid AND a.affid=r.affid AND r.rid='{$identifier[1]}'");

                    $quarter = $report['quarter'];

//list($quarter, $year, $affiliate_name, $supplier_name) = $db->fetch_array($query);
                    $email_data = array(
                            'from_email' => 'no-reply@ocos.orkila.com',
                            'from' => 'OCOS Mailer',
                            'to' => $core->settings['sendreportsto'],
                            'subject' => 'Just approved: Q'.$report['quarter'].' '.$report['year'].' '.$suppliername.'/'.$report['affiliates']['name'],
                            'message' => 'Q'.$report['quarter'].' '.$report['year'].' '.$supplier_name.'/'.$report['affiliates']['name'].' was just approved. ('.date($core->settings['dateformat'].' '.$core->settings['timeformat'], TIME_NOW).')',
                            'attachments' => array($core->settings['exportdirectory'].'quarterlyreports_'.$core->input['identifier'].'.pdf')
                    );

                    $mail = new Mailer($email_data, 'php');
                    @unlink($core->settings['exportdirectory'].'quarterlyreports_'.$core->input['identifier'].'.pdf');
                }
            }
        }
        else {
            set_time_limit(0);
            ini_set('memory_limit', '200M');
            $html2pdf->WriteHTML(trim($content), $show_html);
            $html2pdf->Output($suppliername.'_'.date($core->settings['dateformat'], TIME_NOW).'.pdf');
        }
    }
    elseif($core->input['action'] == 'do_ratesegment') {
        $mrid = $db->escape_string($core->input['repid']);
        $psid = $db->escape_string($core->input['target']);
        $marketreport_obj = MarketReport::get_data(array('mrid' => $mrid));
        if(is_object($marketreport_obj)) {
            $marketreport_obj->rating = $core->input['value'];
            $marketreport_obj->save();
        }
    }
}
function msort($array, $key, $sort_flags = SORT_REGULAR) {
    if(is_array($array) && count($array) > 0) {
        if(!empty($key)) {
            $mapping = array();
            foreach($array as $k => $v) {
                $sort_key = '';
                if(!is_array($key)) {
                    $sort_key = $v[$key];
                }
                else {
// @TODO This should be fixed, now it will be sorted as string
                    foreach($key as $key_key) {
                        $sort_key .= $v[$key_key];
                    }
                    $sort_flags = SORT_STRING;
                }
                $mapping[$k] = $sort_key;
            }
            asort($mapping, $sort_flags);
            $sorted = array();
            foreach($mapping as $k => $v) {
                $sorted[] = $array[$k];
            }
            return $sorted;
        }
    }
    return $array;
}

//function aasort (&$array, $key) {
//
//    $sorter=array();
//    $ret=array();
//    reset($array);
//    foreach ($array as $i => $va) {
//        $sorter[$i]=$va[$key];
//    }
//    asort($sorter);
//    foreach ($sorter as $i => $va) {
//        $ret[$i]=$array[$i];
//    }
//    $array=$ret;
//}
//function subval_sort($a,$subkey) {
//	foreach($a as $k=>$v) {
//		$b[$k] = strtolower($v[$subkey]);
//	}
//	asort($b);
//	foreach($b as $key=>$val) {
//		$c[] = $a[$key];
//		asort($b);
//	}
//
//	return $c;
//}
?>