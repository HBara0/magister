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
// improve DAL to have multiple orders
    $tasks = Tasks::get_tasks('(uid='.$core->user['uid'].' OR createdBy='.$core->user['uid'].')', array('simple' => false, 'order' => 'dueDate DESC, isDone'));

    if(is_array($tasks)) {
        foreach($tasks as $task) {
            $task->dueDate = date($core->settings['dateformat'], $task->dueDate);
            $task_iconstats = $task->parsestatus();
            $task->percCompleted_output = '';
            if($task_iconstats == 'inprogress') {
                //$task->percCompleted_output = numfmt_format(numfmt_create('en_EN', NumberFormatter::PERCENT), $task->percCompleted / 100);
            }

            $task_icon[$task_iconstats] = '<img src="./images/icons/'.$task_iconstats.'.png" border="0" />';
            eval("\$calendar_taskboard_rows .= \"".$template->get('calendar_tasksboard_rows')."\";");
            unset($task_icon[$task_iconstats], $task_percentage);
        }
        unset($tasks);
    }
    eval("\$calendar_taskboard = \"".$template->get('calendar_tasksboard')."\";");
    output_page($calendar_taskboard);
}
elseif($core->input['action'] == 'get_taskdetails') {
    if(!empty($core->input['id'])) {
        $task = new Tasks($core->input['id'], false);
        $task_details = $task->get_task();
        if($core->user['uid'] != $task_details['uid'] && $core->user['uid'] != $task_details['createdBy']) {
            exit;
        }
        if(isset($task_details['timeDone'])) {
            $task_details['timeDone_output'] = $lang->datecompleted.': '.date($core->settings['dateformat'].' H: i ', $task_details['timeDone']).'<br />';
        }
        $task_details['priority_output'] = $task->parse_status();
        $selected['percCompleted'][$task_details['percCompleted']] = ' selected="selected"';

        /* Get Notes - START */
        $task_notes = $task->get_notes();
        if(is_array($task_notes)) {
            $notes_count = count($task_notes);

            foreach($task_notes as $note) {
                $rowclass = alt_row($rowclass);
                $note_date_diff = (TIME_NOW - $note->dateAdded);
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
        $affiliates_users = Users::get_allusers();
        $shared_users = $task->get_shared_users();
        if(is_array($shared_users)) {

            foreach($shared_users as $uid => $user) {
                $user = $user->get();
                $user = array_unique($user);
                $checked = ' checked="checked"';
                $rowclass = 'selected';

                eval("\$sharewith_rows .= \"".$template->get('calendar_createeventtask_sharewithrows')."\";");
            }
        }


        foreach($affiliates_users as $uid => $user) {
            $user = $user->get();
            $checked = $rowclass = '';
            if($uid == $core->user['uid']) {
                continue;
            }

            eval("\$sharewith_rows .= \"".$template->get('calendar_createeventtask_sharewithrows')."\";");
        }

        eval("\$task_sharewith = \"".$template->get('calendar_createeventtask_sharewith')."\";");
        eval("\$taskdetailsbox = \"".$template->get('popup_calendar_taskdetails')."\";");
        output($taskdetailsbox);
    }
}
?>