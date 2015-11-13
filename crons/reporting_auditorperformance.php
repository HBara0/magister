<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: reporting_auditorperformance.php
 * Created:        @hussein.barakat    11-Nov-2015 | 15:29:46
 * Last Update:    @hussein.barakat    11-Nov-2015 | 15:29:46
 */
require '../inc/init.php';
$lang = new Language('english', 'user');
$lang->load('global');
$lang->load('reporting_meta');
$data = ReportingQr::auditor_ratings();
$quarter = currentquarter_info(true);
if($quarter['quarter'] == 1) {
    $quarter['quarter'] = 4;
    $quarter['year'] = $quarter['year'] - 1;
}
else {
    $quarter['quarter'] --;
}
$outputmessage = '<h2>Quarterly Report Auditors Performance Report : Q'.$quarter['quarter'].'/'.$quarter['year'].'</h2>';
if(is_array($data)) {
    foreach($data as $uid => $coordata) {
        $totalreports = 0;
        $supsids = array();
        $coord_obj = new Users($uid);
        $unfsupsids = array();
        $finsupsids = array();
        $unflate = $finlate = 0;
        if(is_array($coordata)) {
            foreach($coordata as $supplierid => $numbers) {
                if($supplierid == 'count') {
                    continue;
                }
                if(is_array($numbers)) {
                    foreach($numbers as $type => $offsetdata) {
                        $affiliate = new Affiliates($type);

                        $maxfin = $maxdue = 0;
                        if(is_array($offsetdata)) {
                            foreach($offsetdata as $offsettype => $offset) {
                                if($offsettype == 'remaining') {
                                    $color = 'green';
                                    $symbol = '&#x2713;';
                                    $linestatus = $lang->remaining;
                                    if($offset < 0) {
                                        if(round(-$offset / 60 / 60 / 24) > 60) {
                                            continue;
                                        }
                                        if($maxdue > $offset) {
                                            $maxdue = round(-$offset / 60 / 60 / 24);
                                        }
                                        $offset = -$offset;
                                        $linetitle = $lang->notfinalized;
                                        $color = 'red';
                                        $linestatus = $lang->unflateby;
                                        $symbol = '&#x2717;';
                                        if(!in_array($supplierid, $unfsupsids)) {
                                            $unflate ++;
                                            $unfsupsids[] = $supplierid;
                                        }
                                    }
                                    $offset_output = round($offset / 60 / 60 / 24);
                                    $listitems_unfin .= '<li>'.$affiliate->get_displayname().' :  '.$linestatus.' '.$offset_output.' days<span style="font-family:bold;color:'.$color.'">'.$symbol.'</span></li>';
                                }
                                else {
                                    $color = 'green';
                                    $symbol = '&#x2713;';
                                    $linestatus = $lang->finalizedearlyby;
                                    if($offset > 0) {
                                        if($maxfin < $offset) {
                                            $maxfin = round($offset / 60 / 60 / 24);
                                        }
                                        $linetitle = $lang->finalized;
                                        $color = 'red';
                                        $linestatus = $lang->finalizelateby;
                                        $symbol = '&#x2717;';
                                        if(!in_array($supplierid, $finsupsids)) {
                                            $finlate++;
                                            $finsupsids[] = $supplierid;
                                        }
                                    }
                                    else {
                                        $offset = -$offset;
                                    }
                                    $offset_output = round($offset / 60 / 60 / 24);
                                    $listitems_fin .= '<li>'.$affiliate->get_displayname().' :  '.$linestatus.' '.$offset_output.' days<span style="font-family:bold;color:'.$color.'">'.$symbol.'</span></li>';
                                }
                                unset($offset_output, $linestatus);
                            }
                        }
                    }
                }
                if(!in_array($supplierid, $supsids)) {
                    $supplier = new Entities($supplierid);
                    $totalreports ++;
                    $supsids[] = $supplierid;
                    if($maxdue != 0) {
                        $supstatus = ':&nbsp;&nbsp;&nbsp;(Report Still Unfinalized : '.$maxdue.' days remaining)';
                        $suppoutput = '<ul type="circle">'.$listitems_unfin.'</ul>';
                    }
                    elseif($maxfin != 0) {
                        $supstatus = ':&nbsp;&nbsp;&nbsp;(Finalized : '.$maxfin.' days late from the 15th of the month)';
                        $suppoutput = '<ul type="circle">'.$listitems_fin.'</ul>';
                    }
                    else {
                        $supstatus = ':&nbsp;&nbsp;&nbsp;(Finalized And Sent On Time)';
                    }
                    $supplieroutput .= '<ul type="disc"> '.$supplier->get_displayname().' '.$supstatus.$suppoutput.'</ul><br>';
                }
                unset($supplier, $suppoutput, $supstatus, $listitems_unfin, $listitems_fin);
            }
        }
        if($unflate > 0) {
            $auditorstatus_un = 'Unfinalized And Late =>'.$unflate;
        }
        if($finlate > 0) {
            $auditorstatus_fin = '<br>Sent Late =>'.$finlate;
        }
        $outputmessage .= '<div><h3>'.$coord_obj->get_displayname().' : '.($totalreports - ($unflate + $finlate)).'/'.$totalreports.'</h3> '.$lang->totalreports.' => '.$totalreports.' <br>'.$auditorstatus_un.$auditorstatus_fin.'<ul>';
        $outputmessage.=$listitems.$supplieroutput.'</ul></div><hr>   ';
        unset($supplieroutput, $auditorstatus_fin, $auditorstatus_un);
    }
}
print($outputmessage);
exit;
