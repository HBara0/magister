<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: attendancerecords.php
 * Created:        @zaher.reda    Jan 15, 2015 | 5:14:11 PM
 * Last Update:    @zaher.reda    Jan 15, 2015 | 5:14:11 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['attendance_canListAttendance'] == 0) {
    error($lang->sectionnopermission);
}
/* Temporary specific fix for time zone */
date_default_timezone_set($core->user_obj->get_mainaffiliate()->get_country()->defaultTimeZone);

if(!$core->input['action']) {
    $limit_start = 0;
    if(isset($core->input['start'])) {
        $limit_start = intval($core->input['start']);
    }
    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = intval($core->input['perpage']);
    }

    if($core->usergroup['attendance_canViewAllAttendance'] != 1) {
        $filter_where = '(uid = '.$core->user['uid'].' OR uid IN (SELECT uid FROM users WHERE reportsTo='.$core->user['uid'].')) ';
        $and = ' AND ';
    }
    else {
        if(is_array($core->user['affiliates'])) {
            $users_where = 'affid IN ('.implode(',', $core->user['affiliates']).')';
        }
        else {
            $users_where = 'isMain=1 AND affid='.$core->user['mainaffiliate'];
        }

        $users = get_specificdata('affiliatedemployees', array('uid'), 'uid', 'uid', '', 0, $users_where);
        $filter_where = 'uid IN ('.implode(',', $users).') ';
        $and = ' AND ';
    }

    $configs['order'] = array('by' => array('time', 'uid'), 'sort' => 'DESC');
    if(isset($core->input['sortby'], $core->input['order'])) {
        $configs['order'] = array('by' => $db->escape_string($core->input['sortby']), 'sort' => $db->escape_string($core->input['order']));
    }
    $sort_url = sort_url();

    $configs['limit'] = array('offset' => $limit_start, 'row_count' => $core->settings['itemsperlist']);
    $configs['operators']['uid'] = CUSTOMSQL;

    $records = AttendanceAttRecords::get_data(array('uid' => $filter_where), $configs);
    if(is_array($records)) {
        foreach($records as $record) {
            $user = $record->get_user();
            $record->timeOutput = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $record->time);
            eval("\$attendancelist .= \"".$template->get('attendance_attrecords_entry')."\";");
        }


        $multipages = new Multipages(AttendanceAttRecords::TABLE_NAME, $core->settings['itemsperlist'], $filter_where);
        $attendancelist .= '<tr><td colspan="5">'.$multipages->parse_multipages().'</td></tr>';
    }
    else {
        $attendancelist .= '<tr><td colspan="5">'.$lang->na.'</td></tr>';
    }
    eval("\$listpage = \"".$template->get('attendance_attrecords')."\";");
    output_page($listpage);
}