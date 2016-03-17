<?php
require '../inc/init.php';

$core->settings['dateformat'] = 'd M, Y';
$query2 = $db->query("SELECT affid, name FROM ".Tprefix."affiliates WHERE supervisor='3'");

$excluded_types = (10);
if($db->num_rows($query2) > 0) {
    while($affiliatew = $db->fetch_assoc($query2)) {
        $expected_firsttime_in = true;

        $affiliate_users = get_specificdata('affiliatedemployees', 'uid', 'uid', 'uid', '', 0, "affid='{$affiliate[affid]}' AND isMain='1'");
        if(empty($affiliate_users)) {
            continue;
        }

        $approved_lids = $unapproved_lids = array();
        $query3 = $db->query("SELECT l.lid, la.isApproved FROM ".Tprefix."leaves l JOIN ".Tprefix."leavesapproval la ON (l.lid=la.lid) WHERE ((".TIME_NOW.". BETWEEN l.fromDate AND l.toDate) OR (l.fromDate > ".TIME_NOW.")) AND l.uid IN (".implode(', ', $affiliate_users).") AND l.ltid NOT IN (".implode(', ', $excluded_types).")");
        while($leave = $db->fetch_assoc($query3)) {
            if($leave['isApproved'] == 0) {
                $unapproved_lids[$leave['lid']] = $leave['lid'];
                if(in_array($leave['lid'], $approved_lids)) {
                    unset($approved_lids[$leave['lid']]);
                }
            }
            if(!in_array($leave['lid'], $unapproved_lids) && $leave['isApproved'] == 1) {
                $approved_lids[$leave['lid']] = $leave['lid'];
            }
        }

        if(!empty($approved_lids)) {
            $query4 = $db->query("SELECT l.*, l.uid AS requester, Concat(u.firstName, ' ', u.lastName) AS employeename
					FROM ".Tprefix."leaves l JOIN ".Tprefix."users u ON (l.uid=u.uid)
					WHERE l.lid IN (".implode(',', $approved_lids).") ORDER BY l.fromDate ASC");
            if($db->num_rows($query4) > 0) {
                $message .= '<br /><strong>'.$affiliate['name'].'</strong>';
                $message .= '<ul>';
                while($more_leaves = $db->fetch_assoc($query4)) {
                    if(date($core->settings['dateformat'], $more_leaves['fromDate']) != date($core->settings['dateformat'], $more_leaves['toDate'])) {
                        $todate_format = $core->settings['dateformat'].' '.$core->settings['timeformat'];
                    }
                    else {
                        $todate_format = $core->settings['timeformat'];
                    }

                    if($more_leaves['fromDate'] > TIME_NOW && $expected_firsttime_in == true) {
                        $message .= '</ul><em>Expected leaves:</em><ul>';
                        $expected_firsttime_in = false;
                    }

                    $leave_info = parse_type($more_leaves['type']);

                    if(empty($positions[$more_leaves['requester']])) {
                        $query = $db->query("SELECT p.* FROM ".Tprefix."positions p LEFT JOIN ".Tprefix."userspositions up ON (up.posid=p.posid) WHERE up.uid='{$more_leaves[requester]}' ORDER BY p.name ASC");
                        $comma = '';
                        while($position = $db->fetch_assoc($query)) {
                            if(!empty($lang->{$position['name']})) {
                                $position['title'] = $lang->{$position['name']};
                            }
                            $positions[$more_leaves['requester']] .= $comma.$position['title'];
                            $comma = ', ';
                        }
                    }
                    $message .= '<li>'.$more_leaves['employeename'].' ('.$positions[$more_leaves['requester']].'): '.$leave_info['title'].' between <strong>'.date($core->settings['dateformat'].' '.$core->settings['timeformat'], $more_leaves['fromDate']).'</strong> and <strong>'.date($todate_format, $more_leaves['toDate']).'</strong></li>';
                }
                $message .= '</ul>';
            }
        }
        /* Get Holidays */
        $time_details = getdate(TIME_NOW);
        $query = $db->query("SELECT *
							FROM ".Tprefix."holidays
							WHERE affid='{$affiliate[affid]}' AND day='{$time_details[mday]}' AND month='{$time_details[mon]}' AND (year=0 OR year='{$time_details[year]}')");
        if($db->num_rows($query) > 0) {
            if(empty($approved_lids)) {
                $message .= '<br /><strong>'.$affiliate['name'].'</strong>';
            }
            $message .= '<br />Official Holidays today: <ul>';
            while($holiday = $db->fetch_assoc($query)) {
                $message .= '<li>'.$holiday['title'].' - '.$holiday['numDays'].' day(s)</li>';
            }
            $message .= '</ul>';
        }
    }

    if(!empty($message)) {
        $email_message = "Following is an overview of the leaves related to your affiliates:<br />";
        //$email_message .= implode(' ', $message);
        $email_message .= $message;

        $email_data = array(
                'to' => 'christophe.sacy@orkila.com',
                'cc' => array('nicole.sacy@orkila.com'),
                'from_email' => $core->settings['maileremail'],
                'from' => 'OCOS Mailer',
                'subject' => 'Expected leaves in your affiliates',
                'message' => $email_message
        );

        //echo $email_message.'<hr />';
        print_r($email_data);

        $mail = new Mailer($email_data, 'php');

        $message = '';
        $log->record(3);
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