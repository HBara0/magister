<?php
require '../inc/init.php';

$lid_cache = array();

$query = $db->query("SELECT l.*, l.uid AS requester, la.lid, la.uid as approver, Concat(u.firstName, ' ', u.lastName) AS employeename
					FROM ".Tprefix."leavesapproval la JOIN ".Tprefix."leaves l ON (l.lid=la.lid) JOIN ".Tprefix."users u ON (l.uid=u.uid)
					WHERE la.isApproved=0 AND l.lid NOT IN (SELECT tmp.lid FROM ".Tprefix."travelmanager_plan tmp WHERE isFinalized = 0) ORDER BY la.sequence ASC");

while($leave = $db->fetch_assoc($query)) {
    if(in_array($leave['lid'], $lid_cache)) {
        continue;
    }
    $waiting_approval[$leave['approver']][$leave['requester']][$leave['lid']] = $leave;
    $lid_cache[] = $leave['lid'];
}

if(is_array($waiting_approval)) {
    foreach($waiting_approval as $key => $pending_users) {
        $approver_info = $db->fetch_assoc($db->query("SELECT uid, firstName, lastName, email FROM ".Tprefix."users WHERE uid='{$key}'"));
        if(empty($approver_info) || !is_array($approver_info)) {
            $approver_info['email'] = $core->settings['adminemail'];
        }

        foreach($pending_users as $k => $pending_lids) {
            foreach($pending_lids as $lid => $leave) {
                if(empty($message[$k])) {
                    $message[$k] = '<br /><strong>'.$leave['employeename'].'</strong><ul>';
                }

                if(date($core->settings['dateformat'], $leave['fromDate']) != date($core->settings['dateformat'], $leave['toDate'])) {
                    $todate_format = $core->settings['dateformat'].' '.$core->settings['timeformat'];
                }
                else {
                    $todate_format = $core->settings['timeformat'];
                }

                $leave_info = parse_type($leave['type']);

                $approve_link = DOMAIN.'/index.php?module=attendance/listleaves&action=takeactionpage&requestKey='.base64_encode($leave['requestKey']).'&id='.base64_encode($leave['lid']);

                $message[$k] .= '<li><a href="'.$approve_link.'">'.$leave_info['title'].' between '.date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave['fromDate']).' and '.date($todate_format, $leave['toDate']).'</a></li>';
            }
            $message[$k] .= '</ul>';
        }

        $email_message = "<strong>Hello {$approver_info[firstName]} {$approver_info[lastName]}</strong> <br /> No action has been taken yet regarding the following leave requests:<br />";
        $email_message .= implode(' ', $message);

        $email_data = array(
                'to' => $approver_info['email'],
                'from_email' => $core->settings['adminemail'],
                'from' => 'OCOS Mailer',
                'subject' => 'Some leave requests are still pending',
                'message' => $email_message
        );

        //echo $email_message.'<hr />';
        $mail = new Mailer($email_data, 'php');

        $message = array();
        $log->record($approver_info['uid']);
    }
}
function parse_type($type) {
    global $db, $lang;

    $leavetype_details = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."leavetypes WHERE ltid='".$db->escape_string($type)."'"));
    if(!empty($lang->{$leavetype_details['name']})) {
        $leavetype_details['title'] = $lang->{$leavetype_details['name']};
    }
    return $leavetype_details;
}

?>