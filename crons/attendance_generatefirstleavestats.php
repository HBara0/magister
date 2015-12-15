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

$allactiveusers = Users::get_data('gid !=7', array('returnarray' => true));
if(is_array($allactiveusers)) {
    $from = strtotime("2016-01-01 08:00:00");
    $to = strtotime("2016-01-01 17:00:00");
    foreach($allactiveusers as $user) {
        $data = array('uid' => $user->uid, 'fromDate' => $from, 'toDate' => $to, 'skipWorkingDays' => True, 'type' => 1);
        $stat = new LeavesStats();
        $stat->generate_periodbased($data);
        reinitialize_balance($user, 3);
    }
}
