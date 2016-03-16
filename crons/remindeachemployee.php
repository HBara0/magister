<?php
require '../inc/init.php';

$users = array();

$quarter = currentquarter_info();
$quarter2 = currentquarter_info(true);

$quarter_start = strtotime($quarter2['year'].'-'.$core->settings['q'.$quarter2['quarter'].'start']) - (60 * 60 * 24 * 16);
$quarter_end = strtotime($quarter2['year'].'-'.$core->settings['q'.$quarter2['quarter'].'end']) - (60 * 60 * 24 * 16);

$time_now = TIME_NOW;

$time_difference = abs(((((($quarter_start + (60 * 60 * 24 * 20)) - $time_now) / 24) / 60) / 60));

if($time_now >= $quarter_end) {
    $time_difference = abs(((((($quarter_end + (60 * 60 * 24 * 20)) - $time_now) / 24) / 60) / 60));
    $quarter = $quarter2;
}

$query = $db->query("SELECT r.*, aff.name AS affiliatename, s.companyName AS suppliername
					FROM ".Tprefix."reports r JOIN ".Tprefix."affiliates aff ON (r.affid=aff.affid) JOIN ".Tprefix."entities s ON (r.spid=s.eid)
					WHERE year='{$quarter[year]}' AND quarter = '{$quarter[quarter]}' AND status!=1 AND isLocked!=1 AND r.type='q' AND s.noQReportSend=0");

$audits_query = $db->query("SELECT u.email, sa.eid FROM ".Tprefix."users u JOIN ".Tprefix."suppliersaudits sa ON (u.uid=sa.uid) WHERE u.gid IN ('5', '13', '2')"); //Suppliers Audit
while($audit = $db->fetch_assoc($audits_query)) {
    $audits[$audit['eid']][] = $audit['email'];
}

$supervisors_query = $db->query("SELECT u.email, aff.affid, aff.qrAlwaysCopy FROM ".Tprefix."users u JOIN ".Tprefix."affiliates aff ON (u.uid=aff.supervisor)");
while($supervisor = $db->fetch_assoc($supervisors_query)) {
    $supervisors[$supervisor['affid']] = $supervisor['email'];
    $additional_cc[$supervisor['affid']] = unserialize($supervisor['qrAlwaysCopy']);
}

while($report = $db->fetch_array($query)) {
    $query2 = $db->query("SELECT u.uid, u.firstName, u.lastName, u.email, ase.affid
						  FROM ".Tprefix."users u JOIN ".Tprefix."assignedemployees ase ON (u.uid=ase.uid)
						  WHERE ase.eid='{$report[spid]}' AND ase.affid='{$report[affid]}' AND u.uid NOT IN (SELECT uid FROM ".Tprefix."reportcontributors WHERE rid='{$report[rid]}' AND isDone='1') AND u.gid IN (SELECT gid FROM usergroups WHERE canUseReporting=1 AND canFillReports=1)");
    while($user = $db->fetch_assoc($query2)) {
        if(is_array($user)) {
            $user_obj = new Users($user['uid']);
            if(!array_key_exists($user['uid'], $users)) {
                foreach($user as $key => $val) {
                    if($key == 'isMain' || $key == 'affid') {
                        continue;
                    }
                    $users[$user['uid']][$key] = $val;
                }
            }

            //if($user['isMain'] == 1) {
            $users[$user['uid']]['mainAffiliate'] = $user_obj->get_mainaffiliate()->get()['affid'];
            ;
            //}

            $report_status = $report_status_comma = '';
            if($report['prActivityAvailable'] == 0) {
                $report_status = 'No products activity';
                $report_status_comma = ', ';
            }

//            if($report['keyCustAvailable'] == 0) {
//                $report_status .= $report_status_comma.'No key customers';
//                $report_status_comma = ', ';
//            }

            if($report['mktReportAvailable'] == 0) {
                $report_status .= $report_status_comma.'No market report';
            }

            if(!empty($report_status)) {
                $report_status = ' ('.$report_status.')';
            }
            if(!is_array($users[$user['uid']]['cc'])) {
                $users[$user['uid']]['cc'] = array();
            }

            if(!empty($audits[$report['spid']])) {
                $users[$user['uid']]['cc'] = array_merge($users[$user['uid']]['cc'], $audits[$report['spid']]);
            }
            $users[$user['uid']]['reports'] .= "<li>Q{$quarter[quarter]} {$quarter[year]} - {$report[suppliername]}/{$report[affiliatename]}.{$report_status}</li>";
        }
    }
}

//if($core->input['type'] == 1) {
//if($time_now >= $quarter_start && $time_now <= $quarter_end) {
if((((($time_now - $quarter_start) / 24) / 60) / 60) > 20 && $time_now <= $quarter_end) {
    $message_sentence = ' day(s) have passed since due date of the following reports:';
    $time_difference += 1;
    $is_thedate = true;
    $send_cc = true;
}
else {
    $message_sentence = ' day(s) remaining to finalize the following reports:';
    $is_thedate = check_if_isthedate();
}
//}

$message_sentence .= '<br /><em>This is to notify you to start filling the qualitative part of your quarterly reports. You will get a seperate notification once products activity data has been imported into the quarterly reports in order to proceed by verifications and finalization.</em><br />';
if($is_thedate == true) {
    foreach($users as $key => $val) { //$time_difference days to finalize these reports
        $email_message = "<strong>Hello {$val[firstName]} {$val[lastName]}</strong> <br /> ".floor($time_difference)." {$message_sentence} <ul>{$val[reports]}</ul>";

        $email_data = array(
                'to' => $val['email'],
                'from_email' => $core->settings['maileremail'],
                'from' => 'OCOS Mailer',
                'subject' => 'Some reports have not been finalized yet',
                'message' => $email_message
        );
        if($send_cc == true) {
            if(is_array($val['cc'])) {
                if(in_array($val['email'], $val['cc'])) {
                    unset($val['cc'][array_search($val['email'], $val['cc'])]);
                }
                $email_data['cc'] = $val['cc'];
            }
            else {
                $email_data['cc'][] = $val['cc'];
            }

            $email_data['cc'][] = $supervisors[$val['mainAffiliate']];
            //$email_data['cc'][] = $core->settings['sendreportsto'];

            /* if(is_array($additional_cc[$val['mainAffiliate']])) {
              $email_data['cc'] = array_merge($email_data['cc'], $additional_cc[$val['mainAffiliate']]);
              } */

            //$email_data['cc'] = array_unique($email_data['cc']);

            if(is_array($additional_cc[$val['mainAffiliate']])) {
                $email_data['cc'] = array_merge($email_data['cc'], $additional_cc[$val['mainAffiliate']]);
            }
        }

        if(!is_array($email_data['cc']) || !isset($email_data['cc'])) {
            $email_data['cc'] = array();
            ;
        }

        if(!empty($email_data['cc'])) {
            $email_data['cc'] = array_unique($email_data['cc']);
        }
        else {
            unset($email_data['cc']);
        }

        //echo $email_message.'<hr />';
        $mail = new Mailer($email_data, 'php');
    }
    $core->input['action'] = 'autosendreportsreminders';
    $log->record(count($users));
}
function check_if_isthedate() {
    global $time_difference;

    if($time_difference >= 14 && $time_difference <= 14.9) {
        $is_thedate = true;
    }
    elseif($time_difference >= 10 && $time_difference <= 10.9) {
        $is_thedate = true;
    }
    elseif($time_difference >= 8 && $time_difference <= 8.9) {
        $is_thedate = true;
    }
    elseif($time_difference >= 5 && $time_difference <= 5.9) {
        $is_thedate = true;
    }
    elseif($time_difference >= 3 && $time_difference <= 3.9) {
        $is_thedate = true;
    }
    elseif($time_difference >= 2 && $time_difference <= 2.9) {
        $is_thedate = true;
    }
    elseif($time_difference >= 1 && $time_difference <= 1.9) {
        $is_thedate = true;
    }
    elseif($time_difference >= '0.0' && $time_difference <= '0.9') {
        $is_thedate = true;
    }
    else {
        $is_thedate = false;
    }
    return $is_thedate;
}

/* }
  elseif($core->input['type'] == 2) {
  if(is_array($users) && !empty($users)) {
  if((((($time_now - $quarter_start)/24)/60)/60) >= 15) {
  $email_message = '<p>List of unfinalized report per employee</p>';

  foreach($users as $key => $val) {
  $email_message .="<p><strong>{$val[firstName]} {$val[lastName]}</strong><ul>{$val[reports]}</ul></p>";
  }

  $email_message .= "<p><em>[This is an automated message, please do not reply]</em></p>";

  $email_data = array(
  'to'		 =>  "zaher.reda@orkila.com", //$core->settings['sendreportsto'],
  'from_email'  => $core->settings['maileremail'],
  'from'	   => 'OCOS Mailer',
  'subject'	=> 'List of Q'.$quarter[quarter].' '.$quarter[year].' unfinalized reports',
  'message'   => $email_message
  );

  echo $email_message;
  $mail = new Mailer($email_data, 'php');

  if($mail->get_status() === true) {
  $core->input['action'] = 'autosendreportslist';
  log_action();
  echo 'Done.';
  }
  else
  {
  echo 'Failed';
  }
  }
  }
  }
  else
  {
  exit;
  }
 */
?>