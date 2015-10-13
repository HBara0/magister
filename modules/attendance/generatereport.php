<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2012 Orkila International Offshore, All Rights Reserved
 * Generate a report for preview and export
 * $module: attendance
 * Created		@tony.assaad 		April 03, 2012 | 5:00 PM
 * Last Update: 	@zaher.reda			May 09, 2012 | 12:02 AM
 */
$session->start_phpsession();
/* Temporary specific fix for time zone */
date_default_timezone_set($core->user_obj->get_mainaffiliate()->get_country()->defaultTimeZone);
if(!$core->input['output'] != 'email') {
    if(!defined("DIRECT_ACCESS")) {
        die("Direct initialization of this file is not allowed.");
    }
    if($core->usergroup['attendance_canGenerateReport'] == 0) {
        error($lang->sectionnopermission);
    }
}

if(!$core->input['action']) {
    $filter_where = '';
    if($core->usergroup['attendance_canViewAllAttendance'] != 1) {
        $filter_where = '(uid = '.$core->user['uid'].' OR reportsTo = '.$core->user['uid'].')';
    }
    else {
        if(is_array($core->user['affiliates'])) {
            $users_where = 'affid IN ('.implode(',', $core->user['affiliates']).')';
        }
        else {
            $users_where = 'isMain = 1 AND affid = '.$core->user['mainaffiliate'];
        }
        $users = get_specificdata('affiliatedemployees', array('uid'), 'uid', 'uid', '', 0, $users_where);
        $filter_where = 'uid IN ('.implode(',', $users).')';
    }

    $users = Users::get_data($filter_where, array('order' => array('by' => 'displayName', 'sort' => 'ASC'), 'returnarray' => true));
    if(is_array($users)) {
        foreach($users as $user) {
            $aff_output_name = '';
            $affiliate = $user->get_mainaffiliate();
            if(is_object($affiliate) && !empty($affiliate->affid)) {
                $aff_output_name = $affiliate->get_displayname();
            }
            $users_list .= ' <tr '.$rowstyle.'">';
            $users_list .= '<td width="50%"><input id="usersfilter_check_'.$user->uid.'" type="checkbox" value="'.$user->uid.'" name="uid[]"> '.$user->get_displayname().'</td><td width:="40%">'.$aff_output_name.'</td></tr>';
        }
    }
    eval("\$generatepage = \"".$template->get('attendance_generatereport')."\";");
    output_page($generatepage);
}
else {
    if($core->input['action'] == 'do_generatereport') {
        parse_attendance_reports($core, $headerinc, $header, $menu, $footer);
    }
}
?>