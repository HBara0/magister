<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved

 * Lists daily attendance
 * $module: attendance
 * Created		@najwa.kassem 		June 2, 2010
 * Last Update: 	@zaher.reda 		October 10, 2010 | 02:59 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['attendance_canListAttendance'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {
    $and = '';
    $sort_query = 'fullname, a.date DESC';

    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query = $db->escape_string($core->input['sortby']).' '.$db->escape_string($core->input['order']);
    }

    $sort_url = sort_url();
    $limit_start = 0;

    if(isset($core->input['start'])) {
        $limit_start = $db->escape_string($core->input['start']);
    }

    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
    }

    if($core->usergroup['attendance_canViewAllAttendance'] != 1) {
        $filter_where = ' WHERE (u.uid = '.$core->user['uid'].' OR u.reportsTo = '.$core->user['uid'].') ';
        $multipage_where = '(u.uid = '.$core->user['uid'].' OR u.reportsTo = '.$core->user['uid'].') ';
        $and = ' AND ';
    }
    else {
        //$users = get_specificdata('affiliatedemployees', array('uid'), 'uid', 'uid', '', 0, 'isMain=1 AND affid='.$core->user['mainaffiliate']);
        if(is_array($core->user['affiliates'])) {
            $users_where = 'affid IN ('.implode(',', $core->user['affiliates']).')';
        }
        else {
            $users_where = 'isMain=1 AND affid='.$core->user['mainaffiliate'];
        }

        $users = get_specificdata('affiliatedemployees', array('uid'), 'uid', 'uid', '', 0, $users_where);
        $filter_where = ' WHERE u.uid IN ('.implode(',', $users).') ';
        $multipage_where = 'u.uid IN ('.implode(',', $users).')';
        $and = ' AND ';
    }

    if(isset($core->input['from'], $core->input['to'])) {
        $filter_where .= $and.'(a.date BETWEEN '.$db->escape_string($core->input['from']).' AND '.$db->escape_string($core->input['to']).') ';
        $multipage_where .= $and.'(a.date BETWEEN '.$db->escape_string($core->input['from']).' AND '.$db->escape_string($core->input['to']).')';
        $and = ' AND ';
    }

    if(isset($core->input['filterby'], $core->input['filtervalue'])) {
        $multipage_where .= $and.'u.'.$db->escape_string($core->input['filterby']).' LIKE "%'.$db->escape_string($core->input['filtervalue']).'%"';
        $filter_where .= $and.'u.'.$db->escape_string($core->input['filterby']).' LIKE "%'.$db->escape_string($core->input['filtervalue']).'%"';
    }

    $query = $db->query("SELECT a.*, a.timeIn AS arrival, a.timeOut AS departure, u.uid, CONCAT(firstName, ' ', lastName) AS fullname
		 FROM ".Tprefix."attendance a JOIN ".Tprefix."users u ON (a.uid=u.uid)
		 {$filter_where}ORDER BY {$sort_query} LIMIT {$limit_start}, {$core->settings[itemsperlist]} ");

    if($db->num_rows($query) > 0) {
        while($attendance = $db->fetch_array($query)) {

            if($attendance['arrival'] == 0) {
                $attendance['arrival'] = ' - ';
            }
            else {
                $attendance['arrival'] = date($core->settings['timeformat'], $attendance['arrival']);
            }

            if($attendance['departure'] == 0) {
                $attendance['departure'] = ' - ';
            }
            else {
                $attendance['departure'] = date($core->settings['timeformat'], $attendance['departure']);
            }

            $attendance_details_output[$attendance['uid']]['userinfo']['fullname'] = $attendance['fullname'];
            $attendance_details_output[$attendance['uid']]['attendance'][$attendance['date']]['in'][] = $attendance['arrival'];
            $attendance_details_output[$attendance['uid']]['attendance'][$attendance['date']]['out'][] = $attendance['departure'];
        }

        foreach($attendance_details_output as $uid => $data) {
            foreach($data['attendance'] as $date => $details) {
                $rowclass = alt_row($rowclass);

                $data['date_output'] = date($core->settings['dateformat'], $date);
                $attendance_ins = implode('<br />', $details['in']);
                $attendance_outs = implode('<br />', $details['out']);
                if($core->usergroup['attendance_canEditAttendance'] == 1) {
                    $hr_section = '<a href="#'.$date.'" id="editlist_'.base64_encode(serialize(array('uid' => $uid, 'date' => $date))).'_attendance/list_loadpopupbyid"><img src="./images/icons/edit.gif" border="0" alt="'.$lang->edit.'"/></a>';
                }

                eval("\$attendancelist .= \"".$template->get('attendance_list_entryrow')."\";");
            }
        }

        $multipages = new Multipages('attendance a JOIN '.Tprefix.'users u ON (u.uid=a.uid)', $core->settings['itemsperlist'], $multipage_where);
        $attendancelist .= '<tr><td colspan="5">'.$multipages->parse_multipages().'</td></tr>';
    }
    else {
        $attendancelist = '<tr><td colspan="5">'.$lang->noattendanceavailable.'</td></tr>';
    }

    eval("\$listpage .= \"".$template->get('attendance_list')."\";");
    output_page($listpage);
}
else {
    if($core->input['action'] == 'do_perform_list') {
        $time_now = time();

        if(isset($core->input['fromDate']) && !empty($core->input['fromDate'])) {
            $fromdate = explode('-', $core->input['fromDate']);

            if(checkdate($fromdate[1], $fromdate[0], $fromdate[2])) {
                $core->input['fromDate'] = mktime(0, 0, 0, $fromdate[1], $fromdate[0], $fromdate[2]);
            }
            else {
                output_xml("<status>false</status><message>{$lang->dateinvalid}</message>");
                exit;
            }
        }
        else {
            output_xml("<status>false</status><message>{$lang->dateinvalid}</message>");
            exit;
        }

        if(isset($core->input['toDate']) && !empty($core->input['toDate'])) {
            $todate = explode('-', $core->input['toDate']);

            if(checkdate($todate[1], $todate[0], $todate[2])) {
                $core->input['toDate'] = mktime(24, 59, 0, $todate[1], $todate[0], $todate[2]);
            }
            else {
                output_xml("<status>false</status><message>{$lang->dateinvalid}</message>");
                exit;
            }
        }
        else {
            output_xml("<status>false</status><message>{$lang->dateinvalid}</message>");
            exit;
        }

        if($core->input['fromDate'] > $time_now) {
            output_xml("<status>false</status><message>{$lang->dateinvalid}</message>");
            exit;
        }

        if($core->input['fromDate'] > $core->input['toDate']) {
            output_xml("<status>false</status><message>{$lang->dateinvalid}</message>");
            exit;
        }

        output_xml("<status>true</status><message><![CDATA[<script type='text/javascript'> goToURL('index.php?module=attendance/list&from=".$core->input['fromDate']."&to=".$core->input['toDate']."');</script>]]></message>");
    }
    elseif($core->input['action'] == 'get_editlist') {
        $parameters = unserialize(base64_decode($core->input['id']));

        for($hour = 1; $hour <= 24; $hour++) {
            $hours[$hour] = $hour;
        }

        for($min = 0; $min < 60; $min++) {
            $mins[$min] = $min;
        }

        $query = $db->query("SELECT * FROM attendance WHERE date=".$db->escape_string($parameters['date'])." AND uid=".$db->escape_string($parameters['uid'])."");
        while($attendance = $db->fetch_assoc($query)) {
            $in = explode(':', date('h:i', $attendance['timeIn']));
            $out = explode(':', date('h:i', $attendance['timeOut']));

            $attendance_rows .= '<tr><td>'.date($core->settings['dateformat'], $attendance['date']).'<input type="hidden" value="'.$attendance['aid'].'" name="aid['.$attendance['aid'].']" id="aid_'.$attendance['aid'].'"  /></td><td>'.parse_selectlist("in_hours[{$attendance[aid]}]", 1, $hours, $in[0]).parse_selectlist("in_mins[{$attendance[aid]}]", 1, $mins, $in[1]).'</td><td>'.parse_selectlist("out_hours[{$attendance[aid]}]", 1, $hours, $out[0]).parse_selectlist("out_mins[{$attendance[aid]}]", 1, $mins, $out[1]).'</td></tr>';
        }

        eval("\$editbox = \"".$template->get('popup_attendance_list_edit')."\";");
        output($editbox);
    }
    elseif($core->input['action'] == 'do_editattendance') {
        foreach($core->input['aid'] as $key => $val) {
            $dates = getdate($core->input['date']);
            $timeIn = mktime($core->input['in_hours'][$key], $core->input['in_mins'][$key], 0, $dates['mon'], $dates['mday'], $dates['year']);
            $timeOut = mktime($core->input['out_hours'][$key], $core->input['out_mins'][$key], 0, $dates['mon'], $dates['mday'], $dates['year']);

            $db->update_query('attendance', array('timeIn' => $timeIn, 'timeOut' => $timeOut), 'aid='.$db->escape_string($key));
        }
        $log->record($core->input['aid']);
        output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
    }
}
function custom_sort($a, $b) {
    if($a == $b) {
        return 0;
    }
    if($a > $b) {
        return 1;
    }
    else {
        return -1;
    }
}

?>