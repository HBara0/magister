<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: taskboard.php
 * Created:        @tony.assaad    Jul 17, 2014 | 10:29:25 AM
 * Last Update:    @tony.assaad    Jul 17, 2014 | 10:29:25 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if(!$core->input['action']) {   // improve DAL to have multiple orders
    $task_objs = Tasks::get_tasks(null, array('order' => array('by' => 'isDone', 'SORT' => 'ASC', 'by' => 'dueDate', 'SORT' => 'DESC')));

    if(is_array($task_objs)) {
        foreach($task_objs as $task) {
            $task->dueDate = date($core->settings['dateformat'].' H: i ', $task->dueDate);
            $task_iconstats = $task->parsestatus($task->percCompleted);
            if($task_iconstats == 'inprogress') {
                $task_percentage = $task->percCompleted.'%';
            }
            $task_icon[$task_iconstats] = '<img src="./images/icons/'.$task_iconstats.'.png" border="0"  />';
            eval("\$calendar_taskboard_rows .= \"".$template->get('calendar_taskboard_rows')."\";");
            unset($task_icon[$task_iconstats], $task_percentage);
        }
    }
    eval("\$calendar_taskboard = \"".$template->get('calendar_taskboard')."\";");
    output($calendar_taskboard);
}
elseif($core->input['action'] == 'get_taskdetails') {

    if(!empty($core->input['id'])) {
        $task = new Tasks($core->input['id']);
        $task_details = $task->get_task();
        if($core->user['uid'] != $task_details['uid'] && $core->user['uid'] != $task_details['createdBy']) {
            exit;
        }
        if(isset($task_details['timeDone'])) {
            $task_details['timeDone_output'] = $lang->datecompleted.': '.date($core->settings['dateformat'].' H: i ', $task_details['timeDone']).'<br />';
        }
        $task_details['priority_output'] = $task->parse_status();
        eval("\$taskdetailsbox = \"".$template->get('popup_calendar_taskdetails')."\";");
        output($taskdetailsbox);
    }
}