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
    $leave_obj->user_output = $leave_obj->get_requester()->displayName;
    $limitedemail = $lang->no;
    if(($leave_obj->limitedEmail) == 1) {
        $limitedemail = $lang->yes;
    }

    $workingdays = $leave_obj->count_workingdays();
    $contactperson = $leave_obj->get_contactperson(true)->parse_link();

    $leavetype = $leave_obj->get_type(false);
    $leave_obj->details_crumb = parse_additionaldata($leave_obj->get(), $leavetype->additionalFields);
    $additionalfield_output = '';

    if(is_array($leave_obj->details_crumb)) {
        $additionalfield_output = implode('<br/>', $leave_obj->details_crumb);
    }

    $seperator = '';
    $approvers_objs = $leave_obj->get_approvers();
    foreach($approvers_objs as $approver) {
        if($approver->is_apporved()) {
            $approved .= $seperator.$approver->get_user()->get_displayname();
            $seperator = ', ';
        }
        else {
            $toapprove .= $seperator.$approver->get_user()->get_displayname();
            $seperator = ', ';
        }
    }

    $seperator = '';
    $affiliates_list = '';
    $informedaffs = unserialize($leave_obj->affToInform);
    if(is_array($informedaffs)) {
        foreach($informedaffs as $affiliate) {
            $aff_object = new affiliates($affiliate);
            $affiliates_list .= $seperator.$aff_object->parse_link();
            $seperator = ', ';
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