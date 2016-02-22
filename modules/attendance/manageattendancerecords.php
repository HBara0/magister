<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: newEmptyPHP.php
 * Created:        @hussein.barakat    02-Feb-2016 | 12:12:51
 * Last Update:    @hussein.barakat    02-Feb-2016 | 12:12:51
 */


if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['attendance_canListAttendance'] == 0) {
    error($lang->sectionnopermission);
}
/* Temporary specific fix for time zone */

if(!$core->input['action']) {
    if($core->usergroup['attendance_canViewAllAttendance'] != 1) {
        $users = get_specificdata('users', array('uid'), 'uid', 'uid', '', 0, $usersfilter_where);
    }
    else {
        $users = get_specificdata('affiliatedemployees', array('uid'), 'uid', 'uid', '', 0, $users_where);
    }
    /* Perform inline filtering - START */
    $userobjs = Users::get_data(array('uid' => $users), array('operators' => array('uid' => 'IN')));
    if(is_array($userobjs)) {
        foreach($userobjs as $user) {
            $employeelist .= ' <tr><td><input id="users_check_'.$user->uid.'" type="checkbox" value="'.$user->uid.'" name="records[uids]['.$user->uid.']">'.$user->get_displayname().'</td></tr>';
        }
    }
    else {
        error($lang->sectionnopermission);
    }
    $newstatus_list = parse_selectlist('records[operation]', 1, array('check-in' => 'In', 'check-out' => 'Out'), '');
    eval("\$manageattendancerecords = \"".$template->get('attendance_manageattendancerecords')."\";");
    output_page($manageattendancerecords);
}
else {
    date_default_timezone_set($core->user_obj->get_mainaffiliate()->get_country()->defaultTimeZone);
    if($core->input['action'] == 'do_perform_manageattendancerecords') {
        if(!isset($core->input['records']) || !is_array($core->input['records']) || is_empty($core->input['records'])) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
        }
        $editrecords = $core->input['records'];
        if(!is_array($editrecords['uids']) || is_empty($editrecords['fromDate'], $editrecords['toDate'])) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
        }
        $existing_records = AttendanceAttRecords::get_data('uid IN ('.implode(',', $editrecords['uids']).') AND time BETWEEN '.strtotime($editrecords['fromDate']).' AND '.strtotime($editrecords['toDate']), array('returnarray' => true));
        if(is_array($existing_records)) {
            foreach($existing_records as $record) {
                if(date('H', $record->time) > $editrecords['fromTime'] && date('H', $record->time) < $editrecords['toTime']) {
                    $newrecords = $record->get();
                    $newrecords['operation'] = $editrecords['operation'];
                    $newrecords['modifiedOn'] = TIME_NOW;
                    $newrecords['modifiedBy'] = $core->user['uid'];
                    $record->update($newrecords);
                }
            }
        }
        output_xml("<status>true</status><message>{$lang->success}</message>");
        exit;
    }
}