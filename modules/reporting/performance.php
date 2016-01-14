<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: performance.php
 * Created:        @rasha.aboushakra    Jul 6, 2015 | 1:39:21 PM
 * Last Update:    @rasha.aboushakra    Jul 6, 2015 | 1:39:21 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canAdminCP'] == 0) {
    // error($lang->sectionnopermission);
    // exit;
}
if(!$core->input['action']) {
    $report_data['year'] = intval($core->input['year']);
    $report_data['quarter'] = intval($core->input['quarter']);

    $fields = array('daysfromqstart', 'daysfromreportcreation', 'daystoimportfromqstart', 'daystoimportfromcreation');

    if(!isset($core->input['year']) || empty($core->input['year'])) {
        $report_data_temp = currentquarter_info();
        $report_data['year'] = $report_data_temp['year'];
    }
    if(!isset($core->input['quarter']) || empty($core->input['quarter'])) {
        $report_data_temp = currentquarter_info();
        $report_data['quarter'] = $report_data_temp['quarter'];
    }

    $affiliates = Affiliates::get_affiliates('name IS NOT NULL', array('returnarray' => true));
    if(isset($core->input['affid'])) {
        $affiliates = Affiliates::get_affiliates(array('affid' => intval($core->input['affid'])), array('returnarray' => true));
    }
    if(isset($core->input['spid']) && !empty($core->input['spid'])) {
        $extra_where = ' AND spid='.intval($core->input['spid']);
    }
    if(is_array($affiliates)) {
        $aff_count = 0;
        foreach($affiliates as $affiliate) {
            $query = $db->query("SELECT * FROM ".Tprefix."reports WHERE affid = ".$affiliate->affid." AND quarter = ".$report_data['quarter']." AND year = ".$report_data['year'].$extra_where);
            $numrows = $db->num_rows($query);
            if($numrows > 0) {
                $unfinished_reports = 0;
                while($report = $db->fetch_assoc($query)) {
                    $quarterstart = strtotime($report_data['year'].'-'.$core->settings['q'.($report_data['quarter'] + 1).'start']);
                    if($report['status'] == 1) {
                        if($report['finishDate'] != 0) {
                            $report['daysfromqstart'] = max(0, floor(($report['finishDate'] - $quarterstart) / (60 * 60 * 24)));
                        }
                        if($report['finishDate'] != 0 && $report['initDate'] != 0) {
                            $report['daysfromreportcreation'] = max(0, floor(($report['finishDate'] - $report['initDate']) / (60 * 60 * 24)));
                        }
                        $icon_locked = '';
                        if($report['isLocked'] == 1) {
                            $icon_locked = '_locked';
                        }
                        $icon[$report['rid']] = "<a href='index.php?module=reporting/preview&referrer=list&amp;affid={$report[affid]}&amp;spid={$report[spid]}&amp;quarter={$report[quarter]}&amp;year={$report[year]}'><img src='images/icons/report{$icon_locked}.gif' alt='{$report[status]}' border='0' title='prevew report'/></a>";
                    }
                    else {
                        $report['status_output'] = 'not finished yet';
                        $unfinished_reports++;
                    }
                    if($report['dataIsImported'] == 1) {
                        if($report['dataImportedOn'] != 0) {
                            $report['daystoimportfromqstart'] = max(0, floor(($report['dataImportedOn'] - $quarterstart) / (60 * 60 * 24)));
                        }
                        if($report['dataImportedOn'] != 0 && $report['initDate'] != 0) {
                            $report['daystoimportfromcreation'] = max(0, floor(($report['dataImportedOn'] - $report['initDate']) / (60 * 60 * 24)));
                        }
                    }

                    foreach($fields as $field) {
                        $totalperaff[$field] += $report[$field];
                    }
                    $supplier = new Entities($report['spid']);
                    $report['supplier'] = $supplier->get_displayname();

                    $report_obj = new Reporting(array('rid' => $report['rid']));
                    $audits = $report_obj->get_report_supplier_audits();
                    if(is_array($audits)) {
                        foreach($audits as $audit) {
                            $reportaudits[] = $audit->parse_link();
                        }
                        $reportaudits_str = implode(', ', $reportaudits);
                    }
                    $marketreports = MarketReport::get_data(array('rid' => $report['rid']), array('returnarray' => true));
                    if(is_array($marketreports)) {
                        $mkrwithrating = 0;
                        $mkr_rating = '<tr><td class="subtitle" colspan="2">'.$lang->mkrrating.'</td></tr><tr><td colspan="2">';
                        foreach($marketreports as $marketreport) {
                            $reportauthors = MarketReportAuthors::get_data(array('mrid' => $marketreport->mrid), array('returnarray' => true));
                            if(is_array($reportauthors)) {
                                foreach($reportauthors as $reportauthor_obj) {
                                    if(!empty($authors)) {
                                        $authors .= ', ';
                                    }
                                    $reportauthor = new Users($reportauthor_obj->uid);
                                    $authors .= $reportauthor->parse_link();
                                }
                            }
                            $marketreport = $marketreport->get();
                            if(empty($marketreport['rating'])) {
                                $rating_status = ' - (not rated)';
                            }
                            else {
                                $ratingval = $marketreport['rating'];
                                $totalrating['supplier'] += $marketreport['rating'];
                                $mkrwithrating++;
                            }

                            $marketreport['segment'] = ProductsSegments::get_data(array('psid' => $marketreport['psid']));
                            if(is_object($marketreport['segment'])) {
                                $marketreport['segmenttitle'] = $marketreport['segment']->parse_link();
                            }
                            else {
                                $marketreport['segmenttitle'] = $lang->unspecified;
                            }
                            eval("\$mkr_rating .= \"".$template->get('reporting_mkr_rating')."\";");
                            unset($ratingval, $reportauthor_obj, $reportauthors, $authors, $rating_status);
                        }
                        $mkr_rating .= '</td></tr>';
                    }

                    $totalrating['affiliate'] += $totalrating['supplier'];
                    $totals['affmkrwithrating'][$affiliate->affid] += $mkrwithrating;

                    $avgrating['supplier'] = 0;
                    if($mkrwithrating != 0) {
                        $avgrating['supplier'] = $totalrating['supplier'] / $mkrwithrating;
                    }
                    eval("\$supplier_reportperformance .= \"".$template->get('reporting_supplier_reportperformance')."\";");
                    unset($mkr_rating, $avgrating['supplier'], $totalrating['supplier'], $reportaudits, $mkrwithrating);
                }
                foreach($fields as $field) {
                    $mkrreports_count = $numrows;
                    if($field == 'daysfromqstart' || $field == 'daysfromreportcreation') {
                        $mkrreports_count = $numrows - $unfinished_reports;
                    }
                    if($mkrreports_count != 0) {
                        $avgperaff[$field][$affiliate->get_displayname()] = ceil($totalperaff[$field] / $mkrreports_count);
                    }
                }
                unset($totalperaff);
                $totals['allmkrwithrating'] += $totals['affmkrwithrating'][$affiliate->affid];

                $totalrating['allaffiliates'] += $totalrating['affiliate'];
                $avgmkrrating[$affiliate->get_displayname()] = 0;
                if(!empty($totals['affmkrwithrating'][$affiliate->affid])) {
                    $avgmkrrating[$affiliate->get_displayname()] = number_format($totalrating['affiliate'] / $totals['affmkrwithrating'][$affiliate->affid], 2);
                }
                eval("\$aff_rating = \"".$template->get('reporting_mkr_rating')."\";");
                eval("\$affiliate_report .= \"".$template->get('reporting_affiliate_reportperformance')."\";");

                foreach($fields as $field) {
                    $all_aff_total[$field] += $avgperaff[$field][$affiliate->get_displayname()];
                }
                $aff_count++;
                unset($avgrating, $totalrating['affiliate']);
            }
            unset($supplier_reportperformance, $supplier);
        }
        if($aff_count != 0) {
            $avgrating['allaffiliates'] = 0;
            if($totals['allmkrwithrating'] && $totals['allmkrwithrating'] != 0) {
                $avgrating['allaffiliates'] = number_format($totalrating['allaffiliates'] / $totals['allmkrwithrating'], 2);
            }
        }
        foreach($fields as $field) {
            $all_aff_avg[$field] = ceil($all_aff_total[$field] / $aff_count);
        }
        if(is_array($avgmkrrating)) {
            if(!(count(array_unique($avgmkrrating)) === 1 && end($avgmkrrating) === '0.00')) {
                $mkrrating_barchart = new Charts(array('x' => array_keys($avgmkrrating), 'y' => array_values($avgmkrrating)), 'bar', array('yaxisname' => 'MKR Rating', 'xaxisname' => $lang->affiliate, 'title' => $lang->barchartrating, 'scale' => 'SCALE_START0', 'nosort' => true, 'width' => 800, 'height' => 300, 'noLegend' => true, 'labelrotationangle' => 90));
                $mkrratingbarchart = $mkrrating_barchart->get_chart();
            }
        }
        $charts = array('daysfromqstart', 'daysfromreportcreation', 'daystoimportfromqstart', 'daystoimportfromcreation');
        foreach($charts as $chart) {
            if(is_array($avgperaff[$chart])) {
                $avgperaff[$chart] = array_filter($avgperaff[$chart]);
                if(!empty($avgperaff[$chart])) {
                    $daystocompletion_bchart[$chart] = new Charts(array('x' => array_keys($avgperaff[$chart]), 'y' => array_values($avgperaff[$chart])), 'bar', array('yaxisname' => $lang->$chart, 'xaxisname' => $lang->affiliates, 'title' => $lang->$chart, 'scale' => 'SCALE_START0', 'nosort' => true, 'width' => 800, 'height' => 300, 'noLegend' => true, 'labelrotationangle' => 90));
                    $daystocompletion_bchart[$chart.'chart'] = $daystocompletion_bchart[$chart]->get_chart();
                }
            }
        }
    }
    if(isset($core->input['excludecharts']) && $core->input['excludecharts'] == 1) {
        $display['charts'] = 'style = "display:none;"';
        $display['allaffiliates'] = 'style = "display:none;"';
    }
    eval("\$reportperformance .= \"".$template->get('reporting_performance')."\";");
    output_page($reportperformance);
}
