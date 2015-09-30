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
        if(is_array($users)) {
            $filter_where = 'uid IN ('.implode(',', $users).')';
        }
    }

    $users = Users::get_data($filter_where, array('order' => array('by' => 'displayname', 'sort' => 'ASC'), 'returnarray' => true));
    if(is_array($users)) {
        foreach($users as $user) {
            $aff_output = $aff_output_name = '';
            $affiliate = $user->get_mainaffiliate();
            if(is_object($affiliate) && !empty($affiliate->affid)) {
                $aff_output_name = $affiliate->get_displayname();
            }
            $aff_output = '<td width:="40%">'.$aff_output_name.'</td>';
            $users_list .= ' <tr class="'.$rowclass.'">';
            $users_list .= '<td width="50%"><input style="width:5%;" id="usersfilter_check_'.$user->uid.'" type="checkbox" value="'.$user->uid.'" name="uid[]"><div style="width:90%;display:inline-block;margin-left:5px;">'.$user->get_displayname().'</div></td>'
                    .$aff_output.'</tr>';
        }
    }

    eval("\$generatepage = \"".$template->get('attendance_attendancelog')."\";");
    output_page($generatepage);
}
