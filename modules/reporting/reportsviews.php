<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Lists views of QR
 * $id: reportsviews.php
 * Created:        @zaher.reda    Aug 28, 2013 | 4:01:18 PM
 * Last Update:    @zaher.reda    Aug 28, 2013 | 4:01:18 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    if(!empty($core->input['quarter']) && !empty($core->input['year'])) {
        $quarter['quarter'] = $core->input['quarter'];
        $quarter['year'] = $core->input['year'];
    }
    else {
        $quarter = currentquarter_info();
    }
//getting last two quarters
    $last_twoqs = get_lastquarters($quarter);

    $query = $db->query('SELECT DISTINCT(identifier), spid
						FROM '.Tprefix.'reports r
							JOIN entities e ON (e.eid=r.spid)
							WHERE quarter='.intval($quarter['quarter']).' AND year='.intval($quarter['year']).'
							ORDER BY companyName ASC');
    $views_outputs .= '<table class="datatable">';
    while($report = $db->fetch_assoc($query)) {
        $supplier = new Entities($report['spid'], '', false);

        if($supplier->get()['noQReportSend'] == 0) {
            $views_outputs .= '<tr><td colspan="3" style="line-height:10px;">&nbsp;</td></tr><tr><td class="thead" colspan="3">'.$supplier->get()['companyName'].'</td></tr>';
            $recepients_query = $db->query('SELECT *
										FROM '.Tprefix.'reporting_qrrecipients rqrr
										JOIN '.Tprefix.'representatives rp ON (rp.rpid=rqrr.rpid)
										WHERE reportIdentifier="'.$report['identifier'].'" AND rqrr.rpid IS NOT NULL');
            if($db->num_rows($recepients_query) > 0) {

                $views_outputs .= '<tr><th class="altrow2">Seen on</th>';
                $views_outputs .= '<th class="altrow2">Sent on</th>';
                $views_outputs .= '<th class="altrow2">Time until seen</th></tr>';
                while($recepient = $db->fetch_assoc($recepients_query)) {
                    $recepient['sentOnObj'] = new DateTime();
                    $recepient['sentOnObj']->setTimestamp($recepient['sentOn']);

                    $recepient['sentByobj'] = new Users($recepient['sentBy']);
                    $recepient['sentBy_output'] = $recepient['sentByobj']->get()['displayName'];
                    $qrrview_query = $db->query('SELECT * FROM '.Tprefix.'reporting_qrrecipients_views WHERE rqrrid='.$recepient['rqrrid']);
                    if($db->num_rows($qrrview_query) > 0) {
                        $views_outputs .= '<tr><th class="subtitle" colspan="3">'.$recepient['name'].'</th></tr>';
                        while($view = $db->fetch_assoc($qrrview_query)) {
                            $rowclass = alt_row($rowclass);
                            $view['timeObj'] = new DateTime();
                            $view['timeObj']->setTimestamp($view['time']);
                            $diff = $view['timeObj']->diff($recepient['sentOnObj']);
                            $recepient['viewsvssent'][] = $view['time'] - $recepient['sentOn'];
                            //$view['location'] = get_meta_tags('http://www.geobytes.com/IpLocator.htm?GetLocation&template=php3.txt&IpAddress='.$view['ipAddress']);
                            $views_outputs .= '<tr class="'.$rowclass.'">';
                            $views_outputs .= '<td>'.date($core->settings['dateformat'].' '.$core->settings['timeformat'], $view['time']).' '.$view['location']['city'].' '.$view['location']['country'].'</td>';
                            $views_outputs .= '<td>'.date($core->settings['dateformat'].' '.$core->settings['timeformat'], $recepient['sentOn']).' by '.$recepient['sentBy_output'].'</td>';
                            $views_outputs .= '<td>'.$diff->format('%a days, %H hours, %I minutes').'</td>';
                            $views_outputs .= '</tr>';
                        }
                    }
                    else {
                        $viewed = 0;
                        $notviewed = '';
                        //check if recipient did not read the report since last two quarters
                        foreach($last_twoqs as $lastq_quarter => $lastq_year) {
                            $lastsingle_q_reports = ReportingQReports::get_column('DISTINCT(identifier)', array('isSent' => 1, 'spid' => $supplier->eid, 'type' => 'q', 'year' => $lastq_year, 'quarter' => $lastq_quarter));
                            if(is_array($lastsingle_q_reports)) {
                                if(is_array($lastqs_reports)) {
                                    $lastqs_reports = array_merge($lastqs_reports, $lastsingle_q_reports);
                                }
                                else {
                                    $lastqs_reports = $lastsingle_q_reports;
                                }
                            }
                        }
                        if(is_array($lastqs_reports)) {
                            $pastqs_reportrecipients = ReportingQrRecipient::get_column(ReportingQrRecipient::PRIMARY_KEY, array('reportIdentifier' => $lastqs_reports));
                            if(is_array($pastqs_reportrecipients)) {
                                $viewsofpast_qs = ReportingQrRecipientViews::get_data(array(ReportingQrRecipient::PRIMARY_KEY => $pastqs_reportrecipients), array('returnarray' => true));
                                if(is_array($viewsofpast_qs)) {
                                    $viewed = 1;
                                }
                            }
                        }
                        if($viewed == 0) {
                            $notviewed = '<span data-toggle="tooltip" data-placement="right" title="'.$lang->notviewedinlasttwoq.'" class="glyphicon glyphicon-info-sign" style="color:red"></span>';
                        }
                        $views_outputs .= '<tr><th class = "subtitle" colspan = "3">'.$recepient['name'].' has not seen it (Sent on '.date($core->settings['dateformat'].' '.$core->settings['timeformat'], $recepient['sentOn']).')'.$notviewed.'</th></tr>';
                    }
                    //				if(is_array($recepient['viewsvssent'])) {
                    //					$average_diff = array_sum($recepient['viewsvssent'])/count($recepient['viewsvssent']);
                    //					$days = floor($average_diff/(24*60*60));
                    //					$hours = floor($average_diff % (24*60*60)/(60*60));
                    //					$minutes = ($average_diff % 60) / 60;
                    //
                    //					$views_outputs .= '<tr><td class = "altrow2" colspan = "2">Average</t><td class = "altrow2">'.$days.' days '.$hours.' hours '.round($minutes).' minutes</td></tr>';
                    //				}
                }
            }
            else {
                $views_outputs .= '<tr><td class = "unapproved" colspan = "3">No Sent</td></tr>';
            }
        }
        else {
            $info['qrnosend'][] = $supplier->get()['companyName'];
        }
    }

    if(is_array($info['qrnosend'])) {
        $views_outputs .= '<tr><td class = "thead" colspan = "3">Filling but not sending to:</td></tr>';
        $views_outputs .= '<tr><td colspan = "3"><ul><li>'.implode('</li><li>', $info['qrnosend']).'</li></ul></td></tr>';
    }
    $views_outputs .= '</table>';
    eval("\$reportviewspage = \"".$template->get('reporting_reportsviews')."\";");
    output_page($reportviewspage);
}
?>
