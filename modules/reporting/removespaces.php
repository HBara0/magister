<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: removespaces.php
 * Created:        @tony.assaad    Apr 30, 2014 | 10:31:30 AM
 * Last Update:    @tony.assaad    Apr 30, 2014 | 10:31:30 AM
 */

// markTrendCompetition, quarterlyHighlights, devProjectsNewOp,issues, actionPlan, remark
if(!$core->input['action']) {
    $query = ($db->query("SELECT mrid, markTrendCompetition, quarterlyHighlights, devProjectsNewOp,issues, actionPlan, remarks  FROM ".Tprefix."marketreport "));

    if($db->num_rows($query) > 0) {
        while($rows = $db->fetch_assoc($query)) {
            $marketreports[$rows['mrid']] = $rows;
        }
        foreach($marketreports as $rid => $marketreport) {
            $marketreportdata[$rid] = $marketreport;
        }

        foreach($marketreportdata as $fieldtable => $val) {
            print_r($val);
            $mrid = $val['mrid'];
            unset($val['mrid']);

            $db->update_query('marketreport', str_replace(array('\n','\\n'), '<br/>', $val), '  mrid='.$mrid);
            $db->update_query('marketreport', str_replace('\<br/>', '<br/>', $val), '  mrid='.$mrid);
        }

    }
}
