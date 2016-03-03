<?php
require '../inc/init.php';

$users = array();

$quarter = currentquarter_info();
$query = $db->query("SELECT r.*, aff.name AS affiliatename, s.companyName AS suppliername
					FROM ".Tprefix."reports r JOIN ".Tprefix."affiliates aff ON (r.affid=aff.affid) JOIN ".Tprefix."entities s ON (r.spid=s.eid)
					WHERE r.type='q' AND year='{$quarter[year]}' AND quarter = '{$quarter[quarter]}' AND r.isSent=0 AND s.noQReportSend=0");
$audits = $passed_over_rids = $not_to_include = array();
while($report = $db->fetch_array($query)) {
    if(!in_array($report['spid'], $not_to_include)) {
        if($report['status'] != 1 || $report['dataIsImported'] != 1) {
            unset($reports[$report['spid']]);
            $not_to_include[] = $report['spid'];
            continue;
        }

        $reports[$report['spid']]['spid'] = $report['spid'];
        $reports[$report['spid']]['suppliername'] = $report['suppliername'];
        $reports[$report['spid']]['affid'][] = $report['affid'];
        $reports[$report['spid']]['rid'][] = $report['rid'];
    }
}

if(!is_array($reports)) {
    exit;
}

foreach($reports as $key => $val) {
    $query2 = $db->query("SELECT u.uid, u.firstName, u.lastName, u.email
						  FROM ".Tprefix."users u JOIN ".Tprefix."suppliersaudits sa ON (u.uid=sa.uid)
						  WHERE u.gid IN ('5', '1', '13', '2') AND sa.eid='{$val[spid]}'");

    $default_audit = $db->fetch_assoc($db->query("SELECT uid, firstName, lastName, email FROM ".Tprefix."users WHERE email='".$core->settings['sendreportsto']."' LIMIT 0,1"));

    if($db->num_rows($query2) > 0) {
        while($audit = $db->fetch_assoc($query2)) {
            if(is_array($audit)) {
                if(!array_key_exists($audit['uid'], $audits)) {
                    foreach($audit as $k => $v) {
                        $audits[$audit['uid']][$k] = $v;
                    }
                }

                //foreach($val['rid'] as $idskey => $ids) {
                //if(!in_array($ids['rid'], $pass_over_rids[$audit['uid']])) {
                //$passed_over_rids[$audit['uid']][] = $report['rid'];
                $report_link = $core->settings['rootdir'].'/index.php?module=reporting/preview&referrer=direct&identifier='.base64_encode(serialize(array('year' => $quarter['year'], 'quarter' => $quarter['quarter'], 'spid' => $val['spid'], 'affid' => $val['affid'])));
                $audits[$audit['uid']]['reports'] .= "<li><a href='{$report_link}'>Q{$quarter[quarter]} {$quarter[year]} - {$val[suppliername]}<a/></li>";
                //}
                //}
            }
        }
    }
    else {
        if(!array_key_exists($default_audit['uid'], $audits)) {
            foreach($default_audit as $k => $v) {
                $audits[$default_audit['uid']][$k] = $v;
            }
        }
        $report_link = $core->settings['rootdir'].'/index.php?module=reporting/preview&referrer=direct&identifier='.base64_encode(serialize(array('year' => $quarter['year'], 'quarter' => $quarter['quarter'], 'spid' => $val['spid'], 'affid' => $val['affid'])));
        $audits[$default_audit['uid']]['reports'] .= "<li><a href='{$report_link}'>Q{$quarter[quarter]} {$quarter[year]} - {$val[suppliername]}<a/></li>";
    }
}

$quarter2 = currentquarter_info(true);

$quarter_start = strtotime($quarter2['year'].'-'.$core->settings['q'.$quarter2['quarter'].'start']);
$quarter_end = strtotime($quarter2['year'].'-'.$core->settings['q'.$quarter2['quarter'].'end']);
$time_now = TIME_NOW;

if($time_now >= $quarter_start && $time_now <= $quarter_end) {
    if(is_array($audits)) {
        foreach($audits as $key => $val) {
            $email_message = "<strong>Hello {$val[firstName]} {$val[lastName]}</strong> <br /> You have been assigned auditor of the following reports, they are completed but not yet sent, please check them and click on the envelope to send it to the suppliers.<br /> <br /> <ul>{$val[reports]}</ul>";

            $email_data = array(
                    'to' => $val['email'],
                    'from_email' => $core->settings['adminemail'],
                    'from' => 'OCOS Mailer',
                    'subject' => 'Some reports have not been sent yet',
                    'message' => $email_message
            );


            $mail = new Mailer($email_data, 'php');

            $mailer = new Mailer();
            $mailer = $mailer->get_mailerobj();
            $mailer->set_layouttype('standard');
            $mailer->set_from(array('name' => 'OCOS Mailer', 'email' => $core->settings['maileremail']));
            $mailer->set_subject('Some reports have not been sent yet');
            $mailer->set_message($email_message);
            $mailer->set_to($val['email']);

            if($val['email'] != $core->settings['sendreportsto']) {
                $mailer->set_cc($core->settings['sendreportsto']);
            }

            $mailer->send();
        }
        $core->input['action'] = 'autosendreportsnotsentreminders';
        $log->record(count($audits));
    }
}
?>