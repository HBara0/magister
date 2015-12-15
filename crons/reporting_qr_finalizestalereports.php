<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: reporting_finalizestalereports.php
 * Created:        @hussein.barakat    15-Dec-2015 | 08:47:49
 * Last Update:    @hussein.barakat    15-Dec-2015 | 08:47:49
 */

require_once '../inc/init.php';
if($_REQUEST['authkey'] == 'kia5ravb$op09dj4a!xhegalhj') {
    $log = new Log();
    $quarter = currentquarter_info();
    /* calculate the timestamp 7 days ago before NOW */
    $timelimit = TIME_NOW - (7 * 24 * 60 * 60);
    $unfinalizedreports = ReportingQReports::get_data(array('year' => $quarter, 'modifiedOn' => $timelimit, 'type' => 'q', 'prActivityAvailable' => 1, 'mktReportAvailable' => 1, 'status' => 0, 'isSent' => 0, 'quarter' => $quarter['quarter']), array('operators' => array('modifiedOn' => 'lt'), 'returnarray' => true));
    if(is_array($unfinalizedreports)) {
        foreach($unfinalizedreports as $report) {
            $new_status = array(
                    'uidFinish' => 0,
                    'finishDate' => TIME_NOW,
                    'status' => 1,
                    'prActivityAvailable' => 1,
                    'keyCustAvailable' => 1,
                    'mktReportAvailable' => 1,
                    'isLocked' => 1
            );
            $update_status = $db->update_query('reports', $new_status, "rid='{$report->rid}'");
            $log->record('finalizeqr_bycron', $report->rid);
        }
    }
}
