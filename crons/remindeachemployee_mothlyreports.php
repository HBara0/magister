<?php
require '../inc/init.php';

$users = array();

$time = time();
$current_month = date('n', $time);
$year = date('Y', $time);
if($current_month == 1) {
    $year = $year - 1;
}
$month = date('n', strtotime("last month"));

$month_name = date('F', strtotime("last month"));

$suppliers = array(1);
$suppliers_names = array(1 => 'Adisseo');
$affiliates_names = array();

foreach($suppliers as $key => $val) {
    $query = $db->query("SELECT ae.uid, ae.affid, u.firstName, u.lastName, u.email, aff.name AS affiliatename FROM ".Tprefix."assignedemployees ae JOIN ".Tprefix."users u ON (ae.uid=u.uid) JOIN ".Tprefix."affiliates aff ON (aff.affid=ae.affid)
						WHERE ae.eid='{$val}' AND u.gid NOT IN (7, 11)");
    while($user = $db->fetch_assoc($query)) {
        if(!array_key_exists($user['uid'], $users)) {
            $users[$user['uid']] = $user;
            $users[$user['uid']]['eid'][$val] = $val;
        }
        else {
            $users[$user['uid']]['eid'][$val] = $val;
        }

        if(value_exists('affiliatedentities', 'affid', $user['affid'], "eid='{$val}'")) {
            $users[$user['uid']]['affids'][$val][$user['affid']] = $user['affid'];
            $affiliates_names[$user['affid']] = $user['affiliatename'];
        }

        /* $query2 = $db->query("SELECT ae.*, aff.name FROM ".Tprefix."affiliatedemployees ae LEFT JOIN ".Tprefix."affiliates aff ON (aff.affid=ae.affid) WHERE ae.uid='{$user[uid]}'");
          while($affiliate = $db->fetch_assoc($query2)) {
          if(value_exists('affiliatedentities', 'affid', $affiliate['affid'], "eid='{$val}'")) {
          $users[$user['uid']]['affid'][$val][$affiliate['affid']] = $affiliate['affid'];
          $affiliates_names[$affiliate['affid']] = $affiliate['name'];
          }
          } */
    }
}

foreach($users as $key => $val) {
    if(!isset($val['affids'])) {
        continue;
    }
    foreach($val['eid'] as $k => $v) {
        foreach($val['affids'][$v] as $z => $x) {
            $query = $db->query("SELECT * FROM ".Tprefix."reports WHERE year='{$year}' AND month='{$month}' AND affid='{$x}' AND spid='{$v}' AND type='m'");
            if($db->num_rows($query) > 0) {
                unset($users[$key]['affids'][$v][$x]);
            }
        }
        if(count($users[$key]['affids'][$v]) == 0) {
            unset($users[$key]['eid'][$v]);
        }

        if(count($users[$key]['eid']) == 0) {
            unset($users[$key]);
        }
    }
}

foreach($users as $key => $val) {
    if(!isset($val['affids'])) {
        continue;
    }
    foreach($val['eid'] as $k => $v) {
        foreach($val['affids'][$v] as $z => $x) {
            if(array_key_exists($x, $affiliates_names)) {
                $val['affiliatename'] = $affiliates_names[$x];
            }
            else {
                $affiliates_names[$x] = $db->fetch_field($db->query("SELECT name FROM ".Tprefix."affiliates WHERE affid='{$x}'"), 'name');
                $val['affiliatename'] = $affiliates_names[$x];
            }
            $val['reports'] .= '<li>'.$month_name.' '.$year.' - '.$val['affiliatename'].'/'.$suppliers_names[$v].'</li>';
        }
    }
    $email_message = "<strong>Hello {$val[firstName]} {$val[lastName]}</strong> <br /> ".date('j', $time)." have passed since begining of the month. The following monthly reports have not be filled yet:<ul>{$val[reports]}</ul>";

    $email_data = array(
            'to' => $val['email'],
            'from_email' => $core->settings['adminemail'],
            'from' => 'OCOS Mailer',
            'subject' => 'Some monthly reports have not been finalized yet',
            'message' => $email_message
    );
    $email_data['cc'] = 'ayman.khoury@orkila.com'; //$email_data['cc'] = 'mohamed.hosny@orkila.com.eg';
    echo $email_message.'<hr />';
    //$mail = new Mailer($email_data, 'php');
}
?>