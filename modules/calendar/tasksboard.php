<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: tasksboard.php
 * Created:        @tony.assaad    Jul 17, 2014 | 10:29:25 AM
 * Last Update:    @tony.assaad    Jul 17, 2014 | 10:29:25 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
//    $taskdata['filter']['ctid'] = 'SELECT ctid FROM calendar_tasks_shares WHERE uid='.$core->user['uid'].') OR (uid='.$core->user['uid'].' OR createdBy='.$core->user['uid'];
//    $tasks = Tasks::get_tasks($taskdata['filter'], array('simple' => false, 'order' => 'dueDate DESC, isDone', 'operators' => array('ctid' => 'IN')));

    $alltask['createdby'] = Tasks::get_tasks(array('createdBy' => $core->user['uid']), array('order' => array('sort' => array('DESC', 'ASC'), 'by' => array('dueDate', 'isDone')), 'returnarray' => true, 'simple' => false));
    $alltask['assigned'] = Tasks::get_tasks(array('uid' => $core->user['uid']), array('order' => array('sort' => array('DESC', 'ASC'), 'by' => array('dueDate', 'isDone')), 'returnarray' => true, 'simple' => false));
    $alltask['shared'] = CalendarTaskShares::get_tasks_byuser($core->user['uid']);

    if(is_array($alltask)) {
        $createdby_ids = array();
        foreach($alltask as $type => $tasks) {
            if(!is_array($tasks)) {
                continue;
            }
            switch($type) {
                case 'createdby':
                    foreach($tasks as $task) {
                        $createdby_ids[] = $task->ctid;
                        $task->dueDate = date($core->settings['dateformat'], $task->dueDate);
                        $task_iconstats = $task->parsestatus();
                        $task_barid = $task->ctid.'c';
                        if($task_iconstats == 'inprogress') {
//                $task->percCompleted_output = numfmt_format(numfmt_create('en_EN', NumberFormatter::PERCENT), $task->percCompleted / 100);
                            $value = $task->percCompleted;
                            eval("\$progressbar = \"".$template->get('progressbar')."\";");
                        }
                        $task_icon[$task_iconstats] = '<img src="./images/icons/'.$task_iconstats.'.png" border="0" />';
                        eval("\$calendar_taskboard_rows .= \"".$template->get('calendar_tasksboard_rows')."\";");
                        unset($task_icon[$task_iconstats], $task_barid, $value, $progressbar);
                    }
                    eval("\$calendar_taskboard_createdby = \"".$template->get('calendar_tasksboard')."\";");
                    unset($calendar_taskboard_rows);
                    break;
                case 'assigned':
                    foreach($tasks as $task) {
                        if(in_array($task->ctid, $createdby_ids)) {
                            continue;
                        }
                        $task->dueDate = date($core->settings['dateformat'], $task->dueDate);
                        $task_iconstats = $task->parsestatus();
                        $task->percCompleted_output = '';
                        $task_barid = $task->ctid.'a';

                        if($task_iconstats == 'inprogress') {
//                $task->percCompleted_output = numfmt_format(numfmt_create('en_EN', NumberFormatter::PERCENT), $task->percCompleted / 100);
                            $value = $task->percCompleted;
                            eval("\$progressbar = \"".$template->get('progressbar')."\";");
                        }
                        $task_icon[$task_iconstats] = '<img src="./images/icons/'.$task_iconstats.'.png" border="0" />';
                        eval("\$calendar_taskboard_rows .= \"".$template->get('calendar_tasksboard_rows')."\";");
                        unset($task_icon[$task_iconstats], $task_barid, $value, $progressbar);
                    }
                    eval("\$calendar_taskboard_assigned = \"".$template->get('calendar_tasksboard')."\";");
                    unset($calendar_taskboard_rows);

                    break;
                case 'shared':
                    foreach($tasks as $task) {
                        $task->dueDate = date($core->settings['dateformat'], $task->dueDate);
                        $task_iconstats = $task->parsestatus();
                        $task->percCompleted_output = '';
                        $task_barid = $task->ctid.'s';
                        if($task_iconstats == 'inprogress') {
//                $task->percCompleted_output = numfmt_format(numfmt_create('en_EN', NumberFormatter::PERCENT), $task->percCompleted / 100);
                            $value = $task->percCompleted;
                            eval("\$progressbar = \"".$template->get('progressbar')."\";");
                        }
                        $task_icon[$task_iconstats] = '<img src="./images/icons/'.$task_iconstats.'.png" border="0" />';
                        eval("\$calendar_taskboard_rows .= \"".$template->get('calendar_tasksboard_rows')."\";");
                        unset($task_icon[$task_iconstats], $task_barid, $value, $progressbar);
                    }
                    eval("\$calendar_taskboard_shared = \"".$template->get('calendar_tasksboard')."\";");
                    unset($calendar_taskboard_rows);
                    break;
            }
        }
    }

    $helptour = new HelpTour();
    $helptour->set_id('tasksboard_helptour');
    $helptour->set_cookiename('tasksboard_helptour');

    $touritems = array(
            'taskstabs-1_btn' => array('text' => 'Here you can see all tasks assigned to you. This also includes the tasks you created for yourself.'),
            'taskstabs_2_btn' => array('text' => 'ny task that you created shows up here, regardless if assigned to you or to someone else.'),
            'taskstabs-3_btn' => array('text' => 'Any task that is shared with you shows up here. It is usually created by other users.')
    );
    $helptour->set_items($touritems);
    $helptour = $helptour->parse();
    eval("\$calendar_taskboard = \"".$template->get('calendar_tasks_tabs')."\";");

    output_page($calendar_taskboard);
}
elseif($core->input['action'] == 'get_taskdetails') {
    if(!empty($core->input['id'])) {
        $task = new Tasks($core->input['id'], false);
        $task_details = $task->get_task();
        if(!$task->is_sharedwithuser() && $core->user['uid'] != $task_details['uid'] && $core->user['uid'] != $task_details['createdBy']) {
            exit;
        }
        if(isset($task_details['timeStarted'])) {
            $task_details['timeStarted_output'] = $lang->datestarted.': '.date($core->settings['dateformat'].' H: i ', $task_details['timeStarted']).'<br />';
        }
        if(isset($task_details['timeDone'])) {
            $task_details['timeDone_output'] = $lang->datecompleted.': '.date($core->settings['dateformat'].' H: i ', $task_details['timeDone']).'<br />';
        }
        $task_details['priority_output'] = $task->parse_status();
        $selected['percCompleted'][$task_details['percCompleted']] = ' selected="selected"';
        if(isset($task->prerequisitTask) && !empty($task->prerequisitTask) && $task->prerequisitTask != 0) {
            $prereqtask_obj = new Tasks($task->prerequisitTask);
            if(is_object($prereqtask_obj)) {
                $pre_requisit = $lang->prerequisittask.': ';
                $pre_requisit.=$prereqtask_obj->subject.'<br>';
            }
        }
        /* Get Notes - START */
        $task_notes = $task->get_notes();
        if(is_array($task_notes)) {

            $notes_count = count($task_notes);

            foreach($task_notes as $note) {
                $rowclass = alt_row($rowclass);
                $note_date_diff = ( TIME_NOW - $note->dateAdded);
                if(date('y-m-d', $note->dateAdded) != date('y-m-d', TIME_NOW)) {
                    $note->dateAdded_output = date($core->settings['dateformat'].' '.$core->setting['timeformat'], $note->dateAdded);
                }
                else {
                    $note->dateAdded_output = date($core->settings['timeformat'], $note->dateAdded);
                }

                fix_newline($note->note);
                $task_notes_output .= '<div class="'.$rowclass.'" style="padding: 5px 0px 5px 10px;">'.$note->note.'. <span class="smalltext" style="font-style:italic;">'.$note->dateAdded_output.' by <a href="users.php?action=profile&uid='.$note->uid.'" target="_blank">'.$note->get_user()->displayName.'</a></span></div>';
            }
        }

        /* Parse share with users */
        if($core->user['uid'] == $task_details['uid'] || $core->user['uid'] == $task_details['createdBy']) {
            $shared_users = $task->get_shared_users();
            $users_order = '0';
            if(is_array($shared_users)) {
                $shared_users_uids = array_keys($shared_users);
                $users_order = implode(',', $shared_users_uids);
            }

            $users = Users::get_data('gid!=7', array('order' => 'CASE WHEN uid IN ('.$users_order.') THEN -1 ELSE displayName END, displayName'));
            foreach($users as $uid => $user) {
                $checked = $rowclass = '';
                if($uid == $core->user['uid']) {
                    continue;
                }

                if(is_array($shared_users_uids)) {
                    if(in_array($uid, $shared_users_uids)) {
                        $checked = ' checked="checked"';
                        $rowclass = 'selected';
                    }
                }
                eval("\$sharewith_rows .= \"".$template->get('calendar_createeventtask_sharewithrows')."\";");
            }

            eval("\$sharewith_section = \"".$template->get('calendar_createeventtask_sharewithsection')."\";");
            unset($sharewith_rows);
            eval("\$task_sharewith = \"".$template->get('calendar_createeventtask_sharewithform')."\";");
        }
        eval("\$taskdetailsbox = \"".$template->get('popup_calendar_taskdetails')."\";");
        output($taskdetailsbox);
    }
}
?>