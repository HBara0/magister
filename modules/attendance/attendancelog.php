<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: attendancelog.php
 * Created:        @hussein.barakat    Sep 15, 2015 | 2:12:38 PM
 * Last Update:    @hussein.barakat    Sep 15, 2015 | 2:12:38 PM
 */

if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['attendance_canGenerateReport'] == 0) {
    error($lang->sectionnopermission);
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

    $users = get_specificdata('users', array('uid', 'displayname'), 'uid', 'displayname', array('by' => 'displayname', 'sort' => 'ASC'), 0, $filter_where);
    $users_list = parse_selectlist('uid[]', 0, $users, $core->user['uid'], 1);

    eval("\$generatepage = \"".$template->get('attendance_attendancelog')."\";");
    output_page($generatepage);
}
