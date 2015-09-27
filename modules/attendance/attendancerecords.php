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
        $usersfilter_where = '(uid = '.$core->user['uid'].' OR uid IN (SELECT uid FROM users WHERE reportsTo='.$core->user['uid'].')) ';
        $users = get_specificdata('users', array('uid'), 'uid', 'uid', '', 0, $usersfilter_where);
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
        $usersfilter_where = 'uid IN ('.implode(',', $users).') ';
        $and = ' AND ';
    }

    /* Perform inline filtering - START */
    $userobjs = Users::get_data(array('uid' => $users), array('operators' => array('uid' => 'IN')));
    $filters_config = array(
            'parse' => array('filters' => array('uid', 'time', 'operation'),
                    'overwriteField' => array('uid' => parse_selectlist('filters[uid][]', 1, $userobjs, $core->input['filters']['uid'], 1, '', array('blankstart' => true, 'width' => 250, 'multiplesize' => 3)),
                            'operation' => parse_selectlist('filters[operation]', 4, array('' => '', 'checkin' => $lang->checkin, 'checkout' => $lang->checkout), $core->input['filters']['operation']),
                    ),
                    'fieldsSequence' => array('uid' => 1, 'time' => 2, 'operation' => 3)
            ),
            'process' => array(
                    'filterKey' => 'aarid',
                    'mainTable' => array(
                            'name' => 'attendance_attrecords',
                            'filters' => array('uid' => array('operatorType' => 'multiple', 'name' => 'uid'), 'time' => array('operatorType' => 'date', 'name' => 'time'), 'operation' => array('operatorType' => 'equal', 'name' => 'operation')),
                    )
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();

    if(is_array($filter_where_values)) {
        // $filter_where = ' AND ';
        if(empty($uid_where)) {
            //  $filter_where = ' WHERE ';
        }
        // $filter_where .= $filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $filters[$filters_config['process']['filterKey']] = $filter_where_values;
        $configs['operators'][$filters_config['process']['filterKey']] = 'IN';
        $multipage_filter_where = ' '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table'));

    $configs['order'] = array('by' => array('time', 'uid'), 'sort' => 'DESC');
    if(isset($core->input['sortby'], $core->input['order'])) {
        $configs['order'] = array('by' => $db->escape_string($core->input['sortby']), 'sort' => $db->escape_string($core->input['order']));
    }
    $sort_url = sort_url();

    $configs['limit'] = array('offset' => $limit_start, 'row_count' => $core->settings['itemsperlist']);
    $configs['operators']['uid'] = CUSTOMSQL;
    if(empty($filter_where)) {
        $filter_where = $usersfilter_where;
        $filters['uid'] = $usersfilter_where;
    }

    $configs['returnarray'] = true;
    $records = AttendanceAttRecords::get_data($filters, $configs);
    if(is_array($records)) {
        foreach($records as $record) {
            $user = $record->get_user();
            $affiliate = $user->get_mainaffiliate();
            if(in_array($affiliate->affid, $core->user['hraffids'])) {
                $hr_section = '<a href="#"  id="updateattrecords_'.$record->aarid.'_attendance/attendancerecords_loadpopupbyid"><img src="'.$core->settings['rootdir'].'/images/edit.gif"></a>';
            }
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
else {
    if($core->input['action'] == 'get_updateattrecords') {
        $record = new AttendanceAttRecords(intval($core->input['id']));
        $user = $record->get_user();
        $time['date'] = date($core->settings['dateformat'], $record->time);
        $time['hours'] = trim(preg_replace('/(AM|PM)/', '', date('H:i', $record->time)));
        $type = parse_selectlist('record[operation]', 4, array('check-in' => $lang->checkin, 'check-out' => $lang->checkout), $record->operation);
        $show_lastupdated = 'style="display:none"';
        if(!empty($record->lastupdateTime)) {
            $show_lastupdated = '';
            $lastupdated_time = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $record->lastupdateTime);
        }
        eval("\$attrecord_details= \"".$template->get('popup_atteendance_records')."\";");
        output($attrecord_details);
    }
    else if($core->input['action'] == 'do_editattendancerecord') {
        $record = $core->input['record'];
        $record['time'] = strtotime($core->input['time']['date'].' '.$core->input['time']['time']);
        $record_obj = new AttendanceAttRecords($record['aarid']);
        $record_obj = $record_obj->update($record);
        switch($record_obj->get_errorcode()) {
            case 0:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 2:
                output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
                break;
            default:
                output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
                break;
        }
    }
}