<?php
require '../inc/init.php';

$current_date = getdate(TIME_NOW);
$current_date['week'] = date('W', TIME_NOW);
$timeframe['from'] = strtotime('last '.$current_date['weekday'].' 21:00:00');
$timeframe['to'] = TIME_NOW;

$affiliate = 11;

$query = $db->query('SELECT * FROM '.Tprefix.'productsegments ps JOIN productsegments_mailinglists psm ON (ps.psid=psm.psid) WHERE affid='.$affiliate);
while($segment = $db->fetch_assoc($query)) {
    $recipients['bysegment'][$segment['psid']]['email'] = $segment['email'];
}

$recipients['bysegment'][1]['uid'] = 187;
$recipients['bysegment'][6]['uid'] = 27;
$recipients['bysegment'][14]['uid'] = 185;
$recipients['bysegment'][2]['uid'] = 78;
$recipients['bysegment'][4]['uid'] = 21;
$recipients['bysegment'][12]['uid'] = 186;

$recipients['byuser'][6] = array('email' => 'all.pharma.ng@orkila.com', 'uid' => 253);
$recipients['byuser'][4] = array('email' => 'all.paint.ng@orkila.com', 'uid' => 271);
$recipients['byuser'][2] = array('email' => 'all.food.ng@orkila.com', 'uid' => 258);
$recipients['byuser'][3] = array(
        array('email' => 'agro.ng@orkila.com', 'uid' => 185),
        array('email' => 'feed.ng@orkila.com', 'uid' => array(259, 184))
);

$query = $db->query("SELECT vr.*, u.displayName AS employeename, u.reportsTo, e.companyName as customer
					FROM visitreports vr JOIN users u ON (u.uid=vr.uid) JOIN entities e ON (e.eid=vr.cid) JOIN affiliatedemployees ae ON (u.uid=ae.uid)
					WHERE (finishDate BETWEEN {$timeframe[from]} AND {$timeframe[to]}) AND ae.affid={$affiliate} AND isMain=1 ORDER BY uid ASC, cid ASC");

if($db->num_rows($query) > 0) {
    while($report = $db->fetch_assoc($query)) {
        $report['date_output'] = date($core->settings['dateformat'], $report['date']);

        if(!isset($cache['visitreport_type'][$report['type']]) || empty($cache['visitreport_type'][$report['type']])) {
            $cache['visitreport_type'][$report['type']] = parse_calltype($report['type']);
        }
        $report['type_output'] = $cache['visitreport_type'][$report['type']];

        $query2 = $db->query("SELECT productLine AS psid FROM ".Tprefix."visitreports_productlines WHERE vrid={$report[vrid]}");
        while($product_segments = $db->fetch_assoc($query2)) {
            if($recipients['bysegment'][$product_segments['psid']]['uid'] == $report['uid']) {
                $reports[$product_segments['psid']][$report['uid']][$report['vrid']] = $report;
            }
            else {
                if(isset($recipients['byuser'][$product_segments['psid']]['uid'])) {
                    if(is_array($recipients['byuser'][$product_segments['psid']]['uid'])) {
                        foreach($recipients['byuser'][$product_segments['psid']]['uid'] as $subject) {
                            if(is_array($subject['uid'])) {
                                foreach($subject['uid'] as $uid) {
                                    if($uid == $report['uid']) {
                                        $reports[$product_segments['psid']][$report['uid']][$report['vrid']] = $report;
                                    }
                                }
                            }
                            else {
                                if($subject['uid'] == $report['uid']) {
                                    $reports[$product_segments['psid']][$report['uid']][$report['vrid']] = $report;
                                }
                                else {
                                    continue;
                                }
                            }
                        }
                    }
                    else {
                        if($recipients['byuser'][$product_segments['psid']]['uid'] == $report['uid']) {
                            $reports[$product_segments['psid']][$report['uid']][$report['vrid']] = $report;
                        }
                        else {
                            continue;
                        }
                    }
                }
                else {
                    continue;
                }
            }
        }
    }
}

if(is_array($reports)) {
    foreach($reports as $psid => $reports_users) {
        foreach($reports_users as $uid => $reports) {
            //$user_section_parsed = false;
            $message_user_reports = '';
            foreach($reports as $vrid => $report) {
                /* if($user_section_parsed == false) {
                  $user_section_parsed = true;
                  //$message_user_reports .= '<strong>'.$report['employeename'].'</strong><ul>';
                  } */
                $message_user_reports .= '<li><a href="'.DOMAIN.'/index.php?module=crm/previewvisitreport&referrer=list&amp;vrid='.$vrid.'">'.$report['customer'].' - '.$report['type_output'].' ('.$report['date_output'].')</a></li>';
            }
            $message_user_reports .= '</ul>';

            $message_output = '<html><head><title>Visit Reports Overview - Week '.$current_date['week'].'/'.$current_date['year'].'</title></head><body>';
            $message_output .= 'Please find below the visit reports of <strong>'.$report['employeename'].'</strong><ul>'.$message_user_reports.'</body></html>';

            $email_data = array(
                    'from_email' => $core->settings['maileremail'],
                    'from' => 'OCOS Mailer',
                    'subject' => 'Visit Reports Overview - Week '.$current_date['week'].'/'.$current_date['year'],
                    'message' => $message_output
            );

            if($recipients['bysegment'][$psid]['uid'] == $uid) {
                $email_data['to'] = $recipients['bysegment'][$psid]['email'];
            }
            elseif($recipients['byuser'][$psid]['uid'] == $uid) {
                $email_data['to'] = $recipients['byuser'][$psid]['email'];
            }

            $mail = new Mailer($email_data, 'php');

            if($mail->get_status() === true) {
                $log->record('weeklyvisitreportsoverview_nigeria', $email_data['to']);
                $result['successfully'][] = $email_data['to'];
            }
            else {
                $result['error'][] = $email_data['to'];
            }
        }
    }
}
function parse_calltype($value) {
    $lang = new Language('english');
    $lang->load('crm_visitreport');
    switch($value) {
        case '1':
            return $lang->facetoface;
            break;
        case '2':
            return $lang->telephonecall;
            break;
        default: break;
    }
}

?>