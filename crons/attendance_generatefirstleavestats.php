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
$allactiveusers = Users::get_data('gid !=7', array('returnarray' => true));
if(is_array($allactiveusers)) {
    $from = strtotime("2016-01-01 08:00:00");
    $to = strtotime("2016-01-01 17:00:00");
    foreach($allactiveusers as $user) {
        $data = array('uid' => $user->uid, 'fromDate' => $from, 'toDate' => $to, 'skipWorkingDays' => True, 'type' => 1);
        $query = $db->query("SELECT ls.*, Concat(u.firstName, ' ', u.lastName) AS employeename
                FROM ".Tprefix."leavesstats ls JOIN ".Tprefix."users u ON (ls.uid = u.uid)
                WHERE ltid = '1' AND u.uid = {$user->uid}
                SORT BY periodStart ASC  Limit 0,1
                ");
        $number_stats = $db->num_rows($query);
        if($number_stats > 0) {
            while($stats = $db->fetch_assoc($query)) {
                $stats['balance'] = $stats['canTake'] - $stats['daysTaken'];
                $stats['finalBalance'] = $stats['balance'] + $stats['additionalDays'];
                reinitialize_balance($user, 3, $stats['finalBalance']);
            }
        }

        $stat = new LeavesStats();
        $stat->generate_periodbased($data);
    }
}
