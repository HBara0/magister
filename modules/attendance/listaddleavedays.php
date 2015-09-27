<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * List additional leaves
 * $module: attendance
 * $id: listaddleaves.php
 * Created:	   	@najwa.kassem	Jan 19, 2011 | 9:37 AM
 * Last Update: 	@zaher.reda		Jan 20, 2011 | 9:37 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $sort_query = 'date DESC';
    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query = $core->input['sortby'].' '.$core->input['order'];
    }

    $sort_url = sort_url();
    $limit_start = 0;

    $multipage_where = " a.affid={$core->user[mainaffiliate]} AND isMain=1 AND u.gid!=7";
    if(isset($core->input['start'])) {
        $limit_start = $db->escape_string($core->input['start']);
    }

    if(isset($core->input['filtervalue']) && !empty($core->input['filtervalue'])) {
        $filter_where = ' AND u.'.$db->escape_string($core->input['filterby']).' LIKE "%'.$db->escape_string($core->input['filtervalue']).'%"';
        $multipage_where .= ' AND u.'.$db->escape_string($core->input['filterby']).' LIKE "%'.$db->escape_string($core->input['filtervalue']).'%"';
    }

    if($core->usergroup['attendance_canViewAffAllLeaves'] != 1) {
        if($core->input['filterby'] != 'uid') {
            $multipage_where .= $filter_where .= ' AND (u.uid = '.$core->user['uid'].' OR u.reportsTo='.$core->user['uid'].')';
        }
        else {
            if($core->input['filterby'] == 'uid' && $core->input['filtervalue'] != $core->user['uid']) {
                $multipage_where .= $filter_where .= ' AND u.reportsTo='.$core->user['uid'];
            }
        }
    }

    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
    }

    $query = $db->query("SELECT *, CONCAT(firstName, ' ', lastName) AS fullname, numDays AS days
						FROM ".Tprefix."attendance_additionalleaves l
						JOIN ".Tprefix."users u ON (l.uid=u.uid)
						JOIN ".Tprefix."affiliatedemployees a ON (a.uid=u.uid)
						WHERE isMain=1 AND u.gid!=7
						{$filter_where}
						ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");

    if($db->num_rows($query) > 0) {
        while($leave = $db->fetch_assoc($query)) {
            $class = alt_row($class);
            $unapproved = '';
            if($leave['isApproved'] == 0) {
                $unapproved = 'unapproved ';
            }
            $leave['approvedOn_output'] = '-';
            if(!empty($leave['approvedOn'])) {
                $leave['approvedOn_output'] = date($core->settings['dateformat'], $leave['approvedOn']);
            }
            else {
                $attendanceadddays = new AttendanceAddDays(array('identifier' => $leave['identifier']));
                if($attendanceadddays->can_approve_user($core->user['uid'])) {
                    $requestKey_encoded = base64_encode($leave['identifier']);
                    $approvelink = "<a href='#{$leave['adid']}' id='approveleave_{$requestKey_encoded}_attendance/listaddleavedays_icon'><img src='{$core->settings[rootdir]}/images/valid.gif' border='0' alt='{$lang->approveadditionaldays}' id='approveimg_".$leave['adid']."' /></a>";
                }
            }
            $addleaves_list .= '<tr id="leaveadd_'.$leave['adid'].'} class="'.$unapproved.$class.'"><td>'.$leave['fullname'].'</td><td>'.$leave['numDays'].'</td><td>'.date($core->settings['dateformat'], $leave['date']).'</td><td>'.$leave['remark'].'</td><td>'.date($core->settings['dateformat'], $leave['requestedOn']).'</td><td>'.$leave['approvedOn_output'].'</td><td>'.$approvelink.'</td><tr>';
            $requestKey_encoded = $approvelink = '';
        }

        $multipages = new Multipages('attendance_additionalleaves l JOIN '.Tprefix.'users u ON (l.uid=u.uid) JOIN '.Tprefix.'affiliatedemployees a ON (u.uid=a.uid)', $core->settings['itemsperlist'], $multipage_where);
        $addleaves_list .= '<tr><td colspan="4">'.$multipages->parse_multipages().'</td></tr>';
    }
    else {
        $addleaves_list = '<tr><td colspan="4">'.$lang->nomatchfound.'</td></tr>';
    }

    eval("\$listaddleaves = \"".$template->get('attendance_listaddleaves')."\";");
    output_page($listaddleaves);
}
else {
    if($core->input['action'] == 'get_approveleave') {

        $attendanceadddays = new AttendanceAddDays(array('identifier' => base64_decode($core->input['id'])));

        $attendanceadddays->approve_user($core->user['uid']);

        //notify approve user
        $attendanceadddays->update_leavestats();
        $attendanceadddays->notifyapprove();
        ?>
        <script language="javascript" type="text/javascript">
            location.reload();
        </script>
        <?php
    }
}
?>