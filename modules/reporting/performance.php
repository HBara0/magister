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

    if(!isset($core->input['year']) || empty($core->input['year'])) {
        $report_data['year'] = date('Y', TIME_NOW);
    }
    if(!isset($core->input['quarter']) || empty($core->input['quarter'])) {
        $report_data['quarter'] = ceil(date('n', time()) / 4);
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
                while($report = $db->fetch_assoc($query)) {
                    $quarterstart = strtotime($report_data['year'].'-'.$core->settings['q'.$report_data['quarter'].'start']);

                    $report['daysfromqstart'] = $report['daysfromreportcreation'] = $report['daystoimportfromcreation'] = $report['daystoimportfromqstart'] = '-';

                    if($report['finishDate'] != 0) {
                        $report['daysfromqstart'] = max(0, floor(($report['finishDate'] - $quarterstart) / (60 * 60 * 24)));
                    }
                    if($report['finishDate'] != 0 && $report['initDate'] != 0) {
                        $report['daysfromreportcreation'] = max(0, floor(($report['finishDate'] - $report['initDate']) / (60 * 60 * 24)));
                    }
                    if($report['dataImportedOn'] != 0) {
                        $report['daystoimportfromqstart'] = max(0, floor(($report['dataImportedOn'] - $quarterstart) / (60 * 60 * 24)));
                    }
                    if($report['dataImportedOn'] != 0 && $report['initDate'] != 0) {
                        $report['daystoimportfromcreation'] = max(0, floor(($report['dataImportedOn'] - $report['initDate']) / (60 * 60 * 24)));
                    }

                    $fields = array('daysfromqstart', 'daysfromreportcreation', 'daystoimportfromqstart', 'daystoimportfromcreation');
                    foreach($fields as $field) {
                        $totalperaff[$field] += $report[$field];
                    }

                    $supplier = new Entities($report['spid']);
                    $report['supplier'] = $supplier->get_displayname();
                    $marketreports = MarketReport::get_data(array('rid' => $report['rid']), array('returnarray' => true));
                    if(is_array($marketreports)) {
                        $mkr_rating = '<tr><td class="subtitle" colspan="2">'.$lang->mkrrating.'</td></tr><tr><td colspan = "2">';
                        foreach($marketreports as $marketreport) {
                            $reportauthor_obj = MarketReportAuthors::get_data(array('mrid' => $marketreport->mrid));
                            if(is_object($reportauthor_obj)) {
                                $reportauthor = new Users($reportauthor_obj->uid);
                                $author = $reportauthor->get_displayname();
                            }
                            $marketreport = $marketreport->get();
                            $ratingval = $marketreport['rating'];
                            $totalrating['supplier'] +=$marketreport['rating'];
                            $marketreport['segment'] = new ProductsSegments($marketreport['psid']);
                            eval("\$mkr_rating .= \"".$template->get('reporting_mkr_rating')."\";");
                            unset($ratingval, $reportauthor_obj, $reportauthor, $author);
                        }
                        $mkr_rating .= '</td></tr>';
                    }
                    $avgrating['supplier'] = $totalrating['supplier'] / count($marketreports);
                    eval("\$supplier_reportperformance .= \"".$template->get('reporting_supplier_reportperformance')."\";");
                    $totalrating['affiliate'] +=$avgrating['supplier'];
                    unset($mkr_rating, $avgrating['supplier'], $totalrating['supplier']);
                }
                foreach($fields as $field) {
                    $avgperaff[$field][$affiliate->get_displayname()] = ceil($totalperaff[$field] / $numrows);
                }
                unset($totalperaff);
                $avgmkrrating[$affiliate->get_displayname()] = $totalrating['affiliate'] / $numrows;

                eval("\$aff_rating = \"".$template->get('reporting_mkr_rating')."\";");
                eval("\$affiliate_report .= \"".$template->get('reporting_affiliate_reportperformance')."\";");

                $totalrating['allaffiliates']+=$avgmkrrating[$affiliate->get_displayname()];
                foreach($fields as $field) {
                    $all_aff_total[$field] += $avgperaff[$field][$affiliate->get_displayname()];
                }
                $aff_count++;
                unset($avgrating, $totalrating['affiliate']);
            }

            unset($supplier_reportperformance, $supplier);
        }
        $avgrating['allaffiliates'] = $totalrating['allaffiliates'] / $aff_count;
        foreach($fields as $field) {
            $all_aff_avg[$field] = ceil($all_aff_total[$field] / $aff_count);
        }
        $mkrrating_barchart = new Charts(array('x' => array_keys($avgmkrrating), 'y' => array_values($avgmkrrating)), 'bar', array('yaxisname' => 'MKR Rating', 'xaxisname' => $lang->affiliate, 'title' => $lang->barchartrating, 'scale' => 'SCALE_START0', 'nosort' => true, 'width' => 1000));
        $daystocompletion_bchart['fromqstart'] = new Charts(array('x' => array_keys($avgperaff['daysfromqstart']), 'y' => array_values($avgperaff['daysfromqstart'])), 'bar', array('yaxisname' => 'Days to Completition (From Q Start)', 'xaxisname' => $lang->affiliates, 'title' => $lang->barchartdayscompletion, 'scale' => 'SCALE_START0', 'nosort' => true, 'width' => 1000));
        $daystocompletion_bchart['fromcreationdate'] = new Charts(array('x' => array_keys($avgperaff['daysfromreportcreation']), 'y' => array_values($avgperaff['daysfromreportcreation'])), 'bar', array('yaxisname' => 'Days to Completition (From Report Creation)', 'xaxisname' => $lang->affiliates, 'title' => $lang->barchartdayscompletion, 'scale' => 'SCALE_START0', 'nosort' => true, 'width' => 1000));
        $daystocompletion_bchart['daystoimportfromqstart'] = new Charts(array('x' => array_keys($avgperaff['daystoimportfromqstart']), 'y' => array_values($avgperaff['daystoimportfromqstart'])), 'bar', array('yaxisname' => 'Days to Completition (From Q Start)', 'xaxisname' => $lang->affiliates, 'title' => $lang->barchartdaystoimport, 'scale' => 'SCALE_START0', 'nosort' => true, 'width' => 1000));
        $daystocompletion_bchart['daystoimportfromcreation'] = new Charts(array('x' => array_keys($avgperaff['daystoimportfromcreation']), 'y' => array_values($avgperaff['daystoimportfromcreation'])), 'bar', array('yaxisname' => 'Days to Completition (From Report Creation)', 'xaxisname' => $lang->affiliates, 'title' => $lang->barchartdaystoimport, 'scale' => 'SCALE_START0', 'nosort' => true, 'width' => 1000));
    }
    if(isset($core->input['excludecharts']) && $core->input['excludecharts'] == 1) {
        $display['charts'] = 'style="display:none;"';
        $display['allaffiliates'] = 'style="display:none;"';
    }
    eval("\$reportperformance .= \"".$template->get('reporting_performance')."\";");
    output_page($reportperformance);
}
