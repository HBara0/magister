<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Tasks Reminders
 * $id: calender_tasks_reminder.php
 * Created:	   	@tony.assaad   	July 18, 2012 | 10:13 PM
 * Last Update: @zaher.reda   	July 24, 2012 | 03:53 PM
 */

require_once '../inc/init.php';
$lang = new Language('english');
$lang->load('messages');

$today_timestamp = strtotime('today');
$tasks_query = $db->query("SELECT ct.*, u.uid, u.displayName, u.email 
							FROM ".Tprefix."calendar_tasks ct
						 	JOIN ".Tprefix."users u ON (ct.uid=u.uid)
						 	WHERE (ct.percCompleted !=100 OR ct.isDone=0) AND reminderStart IS NOT NULL AND dueDate<".strtotime('tomorrow -1 second')."
						 	ORDER BY ct.dueDate ASC, ct.priority DESC");

if($db->num_rows($tasks_query) > 0) {
    while($task = $db->fetch_assoc($tasks_query)) {
        if(empty($task['reminderStart'])) {
            continue;
        }
        /* Check Over Dude Tasks - START */
        if($today_timestamp > $task['dueDate']) {


            if(strtotime('today') <= $task['reminderStart'] && strtotime('tomorrow -1 second') >= $task['reminderStart']) {
                $tasks[$task['uid']]['overdue'][$task['ctid']] = $task;
            }

            /* Remind If due+intrval in on day of TIME_NOW */
            if(strtotime('today') <= ($task['reminderStart'] + $task['reminderInterval']) && strtotime('tomorrow -1 second') >= ($task['reminderStart'] + $task['reminderInterval'])) {
                $tasks[$task['uid']]['overdue'][$task['ctid']] = $task;
            }

            if(!empty($task['reminderInterval'])) {
                if(TIME_NOW > ($task['dueDate'] + $task['reminderInterval'])) {
                    $daynum_interval = ($task['reminderInterval'] / 60 / 60 / 24); //date('j',$task['reminderInterval']);
                    $daynum_duedate = date('j', $task['dueDate']);
                    $daynum_today = date('j', TIME_NOW);

                    $criterion = (((abs($daynum_today - $daynum_duedate) / $daynum_interval) * $daynum_interval) * 60 * 60 * 24);
                    if(strtotime('today') >= ($task['dueDate'] + $criterion) && strtotime('tomorrow -1 second') <= ($task['dueDate'] + $criterion)) {
                        $tasks[$task['uid']]['overdue'][$task['ctid']] = $task;
                    }
                }
            }
        } /* Check Over Dude Tasks - END */
        else {
            $tasks[$task['uid']]['due'][$task['ctid']] = $task;
        }
    }

    if(is_array($tasks)) {
        foreach($tasks as $uid => $data) {
            $body_message = '';
            /* Loop over the available categories of the user's tasks */
            foreach($data as $category => $category_tasks) {
                if($category == 'due') {
                    $body_message .= 'Tasks Due Today';
                }
                else {
                    $body_message .= 'Overdue Tasks';
                }

                $body_message .= '<ul>';
                /* Loops over each individual task in the category */
                foreach($category_tasks as $ctid => $task) {
                    $body_message .= '<li><span style="font-style: italic; font-weight:bold;">'.$task['subject'].'</span><br /><span  style="font-style: italic;">'.$task['description'].'</span></li>';
                }
                $body_message .= '</ul>';
            }

            if(empty($body_message)) {
                continue;
            }

            /* Prepare the Email data */
            $email_data = array(
                    'to' => $task['email'],
                    'from_email' => $core->settings['maileremail'],
                    'from' => 'OCOS Mailer',
                    'subject' => $lang->calendar_taskreminder_subject,
                    'message' => $lang->sprint($lang->calendar_taskreminder_message, $task['displayName'], $body_message)
            );

            $mail = new Mailer($email_data, 'php');
            if($mail->get_status() === true) {
                $log->record('crmvisitreportreminder', array('to' => $taskdetails['email']));
            }
        }
    }
}
?>