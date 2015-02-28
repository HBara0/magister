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
            'parse' => array('filters' => array('uid', 'fromDate', 'toDate', 'operation'),
                    'overwriteField' => array('uid' => parse_selectlist('filters[uid][]', 1, $userobjs, $core->input['filters']['uid'], 1, '', array('blankstart' => true, 'width' => 250, 'multiplesize' => 3)),
                            'operation' => parse_selectlist('filters[operation]', 4, array('' => '', 'checkin' => $lang->checkin, 'checkout' => $lang->checkout), $core->input['filters']['operation']),
                    ),
                    'fieldsSequence' => array('uid' => 1, 'fromDate' => 2, 'toDate' => 3, 'operation' => 4)
            ),
            'process' => array(
                    'filterKey' => 'uid',
                    'mainTable' => array(
                            'name' => 'attendance_attrecords',
                            'filters' => array('uid' => array('operatorType' => 'multiple', 'name' => 'uid'), 'fromDate' => array('operatorType' => 'date', 'name' => 'time'), 'toDate', 'operation' => array('operatorType' => 'equal', 'name' => 'operation')),
                    )
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();

    $filters_row_display = 'hide';
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        // $filter_where = ' AND ';
        if(empty($uid_where)) {
            //  $filter_where = ' WHERE ';
        }
        print_r($filter_where_values); // intersect with filteruserwehre
        $filter_where .= $filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_filter_where = ' '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
 
    $configs['order'] = array('by' => array('time', 'uid'), 'sort' => 'DESC');
    if(isset($core->input['sortby'], $core->input['order'])) {
        $configs['order'] = array('by' => $db->escape_string($core->input['sortby']), 'sort' => $db->escape_string($core->input['order']));
    }
    $sort_url = sort_url();

    $configs['limit'] = array('offset' => $limit_start, 'row_count' => $core->settings['itemsperlist']);
    $configs['operators']['uid'] = CUSTOMSQL;
    if(empty($filter_where)) {
        $filter_where = $usersfilter_where;
    }

    $configs['returnarray'] = true;
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