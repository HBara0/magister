<?php
require '../inc/init.php';

$users = array();

$quarter = currentquarter_info();
$query = $db->query("SELECT r.*, aff.name AS affiliatename, s.companyName AS suppliername
					FROM ".Tprefix."reports r, ".Tprefix."affiliates aff, ".Tprefix."entities s
					WHERE r.spid=s.eid AND r.affid=aff.affid AND year='{$quarter[year]}' AND quarter = '{$quarter[quarter]}' AND status=1 AND isSent=0");

$audits = array();
while($report = $db->fetch_array($query)) {
    $query2 = $db->query("SELECT u.uid, u.firstName, u.lastName, u.email
						  FROM ".Tprefix."users u LEFT JOIN ".Tprefix."assignedemployees ae ON (u.uid=ae.uid)
						  WHERE (u.gid='5' OR u.gid='1') and ae.isValidator='1' AND ae.eid='{$report[spid]}'");

    $default_audit = $db->fetch_assoc($db->query("SELECT uid, firstName, lastName, email FROM ".Tprefix."users WHERE email='".$core->settings['sendreportsto']."' LIMIT 0,1"));

    if($db->num_rows($query2) > 0) {
        while($audit = $db->fetch_assoc($query2)) {
            if(is_array($audit)) {
                if(!array_key_exists($audit['uid'], $audits)) {
                    foreach($audit as $key => $val) {
                        $audits[$audit['uid']][$key] = $val;
                    }
                }
                $audits[$audit['uid']]['reports'] .= "<li>Q{$quarter[quarter]} {$quarter[year]} - {$report[suppliername]}/{$report[affiliatename]}.".parse_status($report)."</li>";
            }
        }
    }
    else {
        if(!array_key_exists($default_audit['uid'], $audits)) {
            foreach($default_audit as $key => $val) {
                $audits[$default_audit['uid']][$key] = $val;
            }
        }
        $audits[$default_audit['uid']]['reports'] .= "<li>Q{$quarter[quarter]} {$quarter[year]} - {$report[suppliername]}/{$report[affiliatename]}.".parse_status($report)."</li>";
    }
}

$quarter2 = currentquarter_info(true);

$quarter_start = strtotime($quarter2['year'].'-'.$core->settings['q'.$quarter2['quarter'].'start']);
$quarter_end = strtotime($quarter2['year'].'-'.$core->settings['q'.$quarter2['quarter'].'end']);
$time_now = TIME_NOW;

if($time_now >= $quarter_start && $time_now <= $quarter_end) {
    if(is_array($audits)) {
        foreach($audits as $key => $val) {
            $email_message = "<strong>Hello {$val[firstName]} {$val[lastName]}</strong> <br /> The following reports have not been sent yet: <ul>{$val[reports]}</ul>";

            $email_data = array(
                    'to' => $val['email'],
                    'from_email' => $core->settings['maileremail'],
                    'from' => 'OCOS Mailer',
                    'subject' => 'Some reports have not been sent yet',
                    'message' => $email_message
            );

            if($val['email'] != $core->settings['sendreportsto']) {
                $email_data['cc'][] = $core->settings['sendreportsto'];
            }
            //echo $email_message.'<hr />';
            $mail = new Mailer($email_data, 'php');
        }
        $core->input['action'] = 'autosendreportsnotsentreminders';
        log_action(count($audits));
    }
}
function parse_status($report) {
    $report_status = $report_status_comma = '';

    if($report['isApproved'] == 0) {
        $report_status = 'Not approved';
        $report_status_comma = ', ';

        if($report['prActivityAvailable'] == 0) {
            $report_status .= $report_status_comma.'No products activity';
            $report_status_comma = ', ';
        }

        if($report['keyCustAvailable'] == 0) {
            $report_status .= $report_status_comma.'No key customers';
            $report_status_comma = ', ';
        }

        if($report['mktReportAvailable'] == 0) {
            $report_status .= $report_status_comma.'No market report';
        }
    }

    if(!empty($report_status)) {
        $report_status = ' ('.$report_status.')';
    }
    return $report_status;
}

?>