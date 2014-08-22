<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: viewleave.php
 * Created:        @rasha.aboushakra    Aug 20, 2014 | 10:30:34 AM
 * Last Update:    @rasha.aboushakra    Aug 20, 2014 | 10:30:34 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $leave_obj = new Leaves($core->input['id'], FALSE);
    if($leave_obj->uid != $core->user['uid']) {
        if($core->usergroup['attendance_canViewAffAllLeaves'] == 0) {
            if(!value_exists('users', 'reportsTo', $core->user['uid'], "uid='{$leave_obj->uid}'") && $core->usergroup['attenance_canApproveAllLeaves'] == 0) {
                //if($core->usergroup['attendance_canViewAllAttendnace'] == 0) { //TO REVISE
                error($message, 'index.php?module=attendance/listleaves');
                //}
            }
        }
    }


    $leave_obj->fromDate_output = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave_obj->fromDate);
    $leave_obj->toDate_output = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave_obj->toDate);
    $limitedemail = 'No';
    if(($leave_obj->limitedEmail) == 1) {
        $limitedemail = 'Yes';
    }


    $workingdays = $leave_obj->count_workingdays();
    if($leave_obj->contactPerson != 0) {
        $contactperson = $leave_obj->get_contactperson(true)->parse_link();
    }
    $leavetype = $leave_obj->get_type(false);
    $leave_obj->details_crumb = parse_additionaldata($leave_obj->get(), $leavetype->additionalFields);
    $additionalfield_output = '';

    if(is_array($leave_obj->details_crumb)) {
        $additionalfield_output = implode('<br/>', $leave_obj->details_crumb);
    }
//    foreach($leave_obj->details_crumb as $key => $val) {
//        //echo "$key; $val <br/>\n";
//        $additionalfield_output .= '<div class="lefttext">'.''.'</div><div class="righttext">'.$val.'</div>';
//    }

    $approvers_objs = $leave_obj->get_approvers();
    foreach($approvers_objs as $approver) {
        if($approver->is_apporved()) {
            $approved .=$approver->get_user()->get_displayname().'; ';
        }
        else {
            $toapprove .= $approver->get_user()->get_displayname().'; ';
        }
    }

    $takeactionpage_conversation = $leave_obj->parse_messages(array('uid' => $core->user['uid'], 'viewsource' => 'viewleave'));
    if(empty($takeactionpage_conversation)) {
        $conversation = '';
    }
    else {
        $conversation = '<div class="thead" style="margin-top:15px;">'.$lang->conversation.'</div>';
    }


    eval("\$attendance_viewleave = \"".$template->get('attendance_viewleave')."\";");
    output_page($attendance_viewleave);
}