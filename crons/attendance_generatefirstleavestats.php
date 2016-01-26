<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: attendance_generatefirstleavestats.php
 * Created:        @hussein.barakat    04-Dec-2015 | 11:37:42
 * Last Update:    @hussein.barakat    04-Dec-2015 | 11:37:42
 */
require '../inc/init.php';
require '../inc/attendance_functions.php';

$lang = new Language('english', 'user');
$allactiveusers = Users::get_data('gid !=7 AND uid>389', array('returnarray' => true));
$leavetype = 1;
if(is_array($allactiveusers)) {
    $from = strtotime((date('Y') + 1).'-01-01 08:00:00');
    $to = strtotime((date('Y') + 1).'-01-01 17:00:00');
    foreach($allactiveusers as $user) {
        $data = array('uid' => $user->uid, 'fromDate' => $from, 'toDate' => $to, 'skipWorkingDays' => true, 'type' => $leavetype);
        $query = $db->query("SELECT remainPrevYear
                FROM ".Tprefix."leavesstats
                WHERE ltid = '{$leavetype}' AND uid = {$user->uid}
                ORDER BY periodStart ASC LIMIT 0,1");
        $prevbalance = $db->fetch_field($query, 'remainPrevYear');

        //reinitialize_balance($user, $leavetype, $prevbalance);
        echo $user->uid;
        echo '<hr />';
        $stat = new LeavesStats();
        $stat->generate_periodbased($data);
    }
}
