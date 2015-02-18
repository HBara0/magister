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
    $leave = new Leaves($core->input['id'], FALSE);
    $leavetype = $leave->get_type(false);
    if($leave->uid != $core->user['uid']) {
        if($core->usergroup['attendance_canViewAffAllLeaves'] == 0) {
            if(!value_exists('users', 'reportsTo', $core->user['uid'], "uid='{$leave->uid}'") && $core->usergroup['attenance_canApproveAllLeaves'] == 0) {
                //if($core->usergroup['attendance_canViewAllAttendnace'] == 0) { //TO REVISE
                if($core->usergroup['attendace_canViewAllAffExpenses'] == 1) {
                    if($leavetype->isBusiness == 0) {
                        error($message, 'index.php?module=attendance/listleaves');
                    }
                }
                else {
                    error($message, 'index.php?module=attendance/listleaves');
                }
                //}
            }
        }
    }

    $leave->fromDate_output = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave->fromDate);
    $leave->toDate_output = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave->toDate);
    $leave->user_output = $leave->get_requester()->displayName;
    $limitedemail = $lang->no;
    if(($leave->limitedEmail) == 1) {
        $limitedemail = $lang->yes;
    }

    $workingdays = $leave->count_workingdays();
    $contactperson = $leave->get_contactperson(true)->parse_link();


    $leave->details_crumb = parse_additionaldata($leave->get(), $leavetype->additionalFields);
    $additionalfield_output = '';

    if(is_array($leave->details_crumb)) {
        $additionalfield_output = implode('<br/>', $leave->details_crumb);
    }

    $seperator = '';
    $approvers_objs = $leave->get_approvers(array('returnarray' => true));
    if(is_array($approvers_objs)) {
        foreach($approvers_objs as $approver) {
            if($approver->is_apporved()) {
                if(empty($approved)) {
                    $seperator = '';
                }
                $approved .= $seperator.$approver->get_user()->get_displayname();
                $seperator = ', ';
            }
            else {
                $toapprove .= $seperator.$approver->get_user()->get_displayname();
            }
            $seperator = ', ';
        }
    }

    $seperator = '';
    $affiliates_list = '';
    $informedaffs = unserialize($leave->affToInform);
    if(is_array($informedaffs)) {
        foreach($informedaffs as $affiliate) {
            $affiliate = new Affiliates($affiliate);
            $affiliates_list .= $seperator.$affiliate->parse_link();
            $seperator = ', ';
        }
    }

    $conversation = $leave->parse_messages(array('uid' => $core->user['uid'], 'viewsource' => 'viewleave'));
    if(empty($conversation)) {
        $lang->conversation = '';
    }

    eval("\$attendance_viewleave = \"".$template->get('attendance_viewleave')."\";");
    output_page($attendance_viewleave);
}