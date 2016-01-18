<?php
/*
 * Copyright � 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: generatexpensesreport.php
 * Created:        @tony.assaad    Apr 7, 2014 | 2:52:35 PM
 * Last Update:    @tony.assaad    Apr 7, 2014 | 2:52:35 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['attendance_canGenerateExpReport'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    if(isset($core->input['messagecode']) && $core->input['messagecode'] == 1) {
        $notification_message = '<div class="ui-state-highlight ui-corner-all" style="padding: 5px; margin-bottom:10px; font-weight: bold;">'.$lang->invaliddaterange.'</div>';
    }

    $identifier = substr(md5(microtime(uniqid())), 0, 10);
    /* Preparing Users section - START */
    $business_managers = LeavesExpenses::get_viewableusers();
    $employees_list = parse_selectlist('expencesreport[filter][uid][]', 1, $business_managers, $core->user['uid'], 1, '', array('width' => '200px'));

    /* Preparing USers section - END */

    /* Here we get affiliate for user assigned to, or he can audit */
    $user_affiliates = Leaves::get_viewableuseraffiliates();
    $affiliates_list = parse_selectlist('expencesreport[filter][useraffids][]', 1, $user_affiliates, $core->user['mainaffiliate'], 1, '', array('width' => '200px'));


    $leavetype_objs = LeaveTypes::get_leavetypes('isBusiness=1');
    foreach($leavetype_objs as $leavetype_obj) {
        $leavetypes = $leavetype_obj->get();
        $leaves_types[$leavetypes['ltid']] = $leavetypes['title'];
    }
    $leavetype_list = parse_selectlist('expencesreport[filter][type][]', 1, $leaves_types, '', 1, '', array('width' => '200px'));

    /* Leave Expences type */
    $leave_expencestypes = LeaveExpenseTypes::get_leaveexpensetypes();

    foreach($leave_expencestypes as $leave_expencestype) {
        $leave_expencestypes[$leave_expencestype['aletid']] = $leave_expencestype['title'];
    }

    $leave_expencestypes_list = parse_selectlist('expencesreport[filter][aletid][]', 1, $leave_expencestypes, '', 1, '', array('width' => '200px'));
    /* 'useraffid' => $lang->affiliate */
    $dimensions = array('uid' => $lang->employee, 'ltid' => $lang->leavetype, 'aletid' => $lang->leaveexptype, 'lid' => $lang->leave);

    foreach($dimensions as $dimensionid => $dimension) {
        $dimension_item .= '<li class="ui-state-default" id='.$dimensionid.' title="Click and Hold to move the '.$dimension.'">'.$dimension.'</li>';
    }
    eval("\$expencesreport_options = \"".$template->get('attendance_expencesreport_options')."\";");
    output($expencesreport_options);
}
else {
    if($core->input['action'] == 'preview') {
        $expencesreport_data = $core->input['expencesreport'];
        if(strtotime($expencesreport_data['filter']['toDate']) < strtotime($expencesreport_data['filter']['fromDate'])) {
            redirect('index.php?module=attendance/generatexpensesreport&messagecode=1');
        }

        $dimensionalize_ob = new DimentionalData();

        /* split the dimension and explode them into chuck of array */
        if(!is_array($expencesreport_data['dimension']) || is_empty($expencesreport_data['dimension'][0])) {
            redirect($core->settings['rootdir'].'/index.php?module=attendance/generatexpensesreport');
        }
        $expencesreport_data['dimension'] = $dimensionalize_ob->construct_dimensions($expencesreport_data['dimension']);

        $expences_indexes = array('expectedAmt', 'actualAmt');
        $leave_expencesdata = Leaves::get_leaves_expencesdata($expencesreport_data['filter'], array('maintablealias' => 'l'));

        if(is_array($leave_expencesdata)) {
            $expencesreport_data['dimension'] = $dimensionalize_ob->set_dimensions($expencesreport_data['dimension']);
            $dimensionalize_ob->set_requiredfields($expences_indexes);
            $dimensionalize_ob->set_data($leave_expencesdata);

            $parsed_dimension = $dimensionalize_ob->get_output(array('outputtype' => 'table', 'noenclosingtags' => true));
            $headers_title = $dimensionalize_ob->get_requiredfields();

            foreach($headers_title as $report_header => $header_data) {
                $header_data = strtolower($header_data);
                $dimension_head.= '<th>'.$lang->{$header_data}.'</th>';
            }
        }

        eval("\$expencesreport_output = \"".$template->get('attendance_expencesreport_output')."\";");
        output($expencesreport_output);
    }
}
?>