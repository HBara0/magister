<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Events & Tasks
 * $module: calendar
 * $id: evenstasks.php
 * Created: 	@zaher.reda 	April 26, 2011 | 11:52 AM
 * Last Update: @zaher.reda 	May 18, 2012 | 09:00 PM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

/* if($core->usergroup['filesharing_canViewSharedfiles'] == 0) {
  error($lang->sectionnopermission);
  exit;
  } */

if(!$core->input['action']) {
	redirect($_SERVER['HTTP_REFERER']);
}
else {
	if($core->input['action'] == 'do_createeventtask') {
		if($core->input['type'] == 'task') {

			$task = new Tasks();
			$task->create_task($core->input['task']);

			switch($task->get_status()) {
				case 0:
					header('Content-type: text/xml+javascript');
					output_xml('<![CDATA[<script>$("#popup_createeventtask").dialog("close");</script>]]>');
//	output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
					break;
				case 1:
					output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
					exit;
				case 2:
					output_xml("<status>false</status><message>{$lang->taskexists}</message>");
					exit;
				case 3:
					output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
					exit;
			}

			if($core->input['task']['notify']) {
				$task->notify_task();
			}
		}
		elseif($core->input['type'] == 'event') {
			if(is_empty($core->input['event']['title'])) {
				output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
				exit;
			}
			$new_event = array(
					'title' => ucwords(strtolower($core->input['event']['title'])),
					'description' => ucfirst(strtolower($core->input['event']['description'])),
					'uid' => $core->user['uid'],
					'isPublic' => $core->input['event']['isPublic'],
					'place' => $core->input['event']['place'],
					'type' => $core->input['event']['type']
			);

			//$fromdate_details = explode('-', $core->input['event']['fromDate']);
			//$todate_details = explode('-', $core->input['event']['toDate']);

			$new_event['fromDate'] = strtotime($core->input['event']['fromDate'].' '.$core->input['event']['fromTime']);
			$new_event['toDate'] = strtotime($core->input['event']['toDate'].' '.$core->input['event']['toTime']);
			//$new_event['toDate'] = strtotime($core->input['event']['toDate'].' +1 day -1 hour'); //mktime(23, 59, 0, $todate_details[1], $todate_details[0], $todate_details[2]);
			//$new_event['fromDate'] = strtotime($core->input['event']['fromDate'].' midnight'); //mktime(0, 0, 0, $fromdate_details[1], $fromdate_details[0], $fromdate_details[2]);
			
			
			echo $core->input['event']['fromDate'].' '.$core->input['event']['fromTime']. " ".$new_event['fromDate'];
			echo'<br>'.$core->input['event']['toDate'].' '.$core->input['event']['toTime']. ' '.$new_event['toDate'];
			
			
		
			
			if(value_exists('calendar_events', 'title', $core->input['event']['title'], 'type='.$db->escape_string($core->input['event']['type']).' AND (toDate='.$new_event['toDate'].' OR fromDate='.$new_event['fromDate'].')')) {
				output_xml("<status>false</status><message>{$lang->eventexists}</message>");
				exit;
			}

			$query = $db->insert_query('calendar_events', $new_event);
			if($query) {
				$log->record($core->input['type'], $last_id);
				header('Content-type: text/xml+javascript');
				output_xml('<status>true</status><message>'.$lang->successfullysaved.'<![CDATA[<script>$("#popup_createeventtask").dialog("close");</script>]]></message>>');
				exit;
			}
			else {
				output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
				exit;
			}
			$last_id = $db->last_id();

			if($core->input['event']['isPublic'] == 1 && $core->usergroup['calendar_canAddPublicEvents'] == 1) {
				if(isset($core->input['event']['restrictto'])) {
					if(is_array($core->input['event']['restrictto'])) {
						foreach($core->input['event']['restrictto'] as $affid) {
							$db->insert_query('calendar_events_restrictions', array('affid' => $affid, 'ceid' => $last_id));
						}

						if(isset($core->input['event']['notify']) && $core->input['event']['notify'] == 1) {
							/* Send the event notification - START */
							$notification_mails = get_specificdata('affiliates', array('affid', 'mailingList'), 'affid', 'mailingList', '', 0, 'mailingList != "" AND affid IN('.implode(',', $core->input['event']['restrictto']).')');

							$email_data = array(
									'from_email' => 'events@orkila.com',
									'from' => 'Orkila Events Notifier',
									'to' => $notification_mails,
									'subject' => $core->input['event']['title']
							);
							$email_data['message'] = '<strong>'.$core->input['event']['title'].'</strong> (';

							$email_data['message'] .= date($core->settings['dateformat'], $new_event['fromDate']);
							if($new_event['toDate'] != $new_event['fromDate']) {
								$email_data['message'] .= ' - '.date($core->settings['dateformat'], $new_event['toDate']);
							}
							$email_data['message'] .= ')<br />';
							$email_data['message'] .= $core->input['event']['place'].'<br />';
							$email_data['message'] .= str_replace("\n", '<br />', $core->input['event']['description']);

							$mail = new Mailer($email_data, 'php');

							if($mail->get_status() === true) {
								$log->record($notification_mails, $last_id);
							}
							else {
								$errors['notification'] = false;
							}

							/* Send the event notification - END */
						}
					}
				}
			}
		}
		else {
			output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
			exit;
		}
	}
	elseif($core->input['action'] == 'settaskdone') {
		if(is_empty($core->input['id'])) {
			output_xml("<status>false</status><message></message>");
			exit;
		}

		$task = new Tasks($core->input['id'], true);
		$task->change_status($core->input['value']);

		switch($task->get_status()) {
			case 0:
				header('Content-type: text/xml+javascript');
				if($core->input['value'] == 1) {
					$output_js = "$('#ctid_".$core->input['id']."').css('text-decoration', 'line-through');";
				}
				else {
					$output_js = '$("#ctid_'.$core->input['id'].'").css("text-decoration", "none");';
				}
				output_xml("<status>true</status><message><![CDATA[<script>".$output_js."</script>]]></message>");
				break;
			default: output_xml('<status>false</status><message></message>');
		}
	}
	elseif($core->input['action'] == 'get_eventdetails') {
		if(!empty($core->input['id'])) {
			$event = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."calendar_events WHERE ceid='".$db->escape_string($core->input['id'])."'"));

			$event['dates_output'] = date($core->settings['dateformat'], $event['fromDate']);
			if($event['toDate'] != $event['fromDate']) {
				$event['dates_output'] .= ' - '.date($core->settings['dateformat'], $event['toDate']);
			}

			if(!empty($event['place'])) {
				$event['place_output'] = $event['place'].' <a href="http://maps.google.com/maps?hl=en&q='.$event['place'].'" target="_blank"><img src="./images/icons/map.png" border="0" alt="'.$lang->map.'"></a>';
			}
			eval("\$eventdetailsbox = \"".$template->get("popup_calendar_eventdetails")."\";");
			echo $eventdetailsbox;
		}
	}
	elseif($core->input['action'] == 'get_taskdetails') {
		if(!empty($core->input['id'])) {
			$task = new Tasks($core->input['id']);

			$task_details = $task->get_task();

			if($core->user['uid'] != $task_details['uid'] && $core->user['uid'] != $task_details['createdBy']) {
				exit;
			}
			//$task_details['dueDate_output'] = date($core->settings['dateformat'], $task_details['dueDate']);
			$task_details['priority_output'] = $task->parse_status();

			if(isset($task_details['timeDone_output'])) {
				$task_details['timeDone_output'] = $lang->datecompleted.': '.$task_details['timeDone_output'].'<br />';
			}

			if($task_details['uid'] != $core->user['uid']) {
				$task_details['assignedTo_output'] = $lang->assignedto.': '.$task_details['assignedTo'].'<br />';
			}

			$selected['percCompleted'][$task_details['percCompleted']] = ' selected="selected"';

			/* Get Notes - START */
			$task_notes = $task->get_notes();
			if(is_array($task_notes)) {
				$notes_count = count($task_notes);

				foreach($task_notes as $note) {
					$rowclass = alt_row($rowclass);
					$note_date_diff = (TIME_NOW - $note['dateAdded']);
					if(date('y-m-d', $note['dateAdded']) != date('y-m-d', TIME_NOW)) {
						$note['dateAdded_output'] = date($core->settings['dateformat'].' '.$core->setting['timeformat'], $note['dateAdded']);
					}
					else {
						$note['dateAdded_output'] = date($core->settings['timeformat'], $note['dateAdded']);
					}

					$task_notes_output .= '<div class="'.$rowclass.'" style="padding: 5px 0px 5px 10px;">'.$note['note'].'. <span class="smalltext" style="font-style:italic;">'.$note['dateAdded_output'].' by <a href="users.php?action=profile&uid='.$note['uid'].'" target="_blank">'.$note['displayName'].'</a></span></div>';
				}
			}
			eval("\$eventdetailsbox = \"".$template->get("popup_calendar_taskdetails")."\";");
			echo $eventdetailsbox;
		}
	}
	elseif($core->input['action'] == 'save_tasknote') {
		$lang->load('calendar_messages');
		$task = new Tasks($core->input['id']);
		$task->save_note($core->input['note']);
		$task_details = $task->get_task();

		switch($task->get_status()) {
			case 0:
				header('Content-type: text/xml+javascript');
				output_xml("<status>true</status><message><![CDATA[<script>$('#note').val(''); $('#calendar_task_notes').prepend('<div id=\'note_1\' style=\'padding: 5px 0px 5px 10px;\' class=\'altrow2\'>".$db->escape_string($core->input['note']).". <span class=\'smalltext\' style=\'font-style:italic;\'>".date($core->settings['dateformat'], TIME_NOW)." by <a href=\'users.php?action=profile&uid=".$core->user['uid']."\' target=\'_blank\'>".$core->user['displayName']."</a></span></div>');</script>]]></message>");

				if($core->user['uid'] == $task_details['uid']) {
					if($task_details['createdby'] != $core->user['uid']) {
						$to = $db->fetch_field($db->query('SELECT email FROM '.Tprefix.'users WHERE uid='.$task_details['createdby']), 'email');
					}
				}
				else {
					$to = $db->fetch_field($db->query('SELECT email FROM '.Tprefix.'users WHERE uid='.$task_details['uid']), 'email');
				}
				if(!empty($to)) {
					$notification = array(
							'to' => $to,
							'from_email' => $core->settings['maileremail'],
							'from' => 'OCOS Mailer',
							'subject' => $lang->sprint($lang->newnotemessage_subject, $task_details['subject']),
							'message' => $lang->sprint($lang->newnotemessage_body, $core->user['displayName'], $db->escape_string($core->input['note']), date($core->settings['dateformat'].' '.$core->settings['timeformat'], TIME_NOW))
					);

					$mail = new Mailer($notification, 'php');
				}
				break;
			case 1:
				output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
				break;
			case 2:
			case 3:
				output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
				exit;
		}
	}
	elseif($core->input['action'] == 'update_task') {
		$task = new Tasks($core->input['ctid']);
		$task->update_task($core->input['percCompleted']);

		switch($task->get_status()) {
			case 0:
				header('Content-type: text/javascript');

				if($core->input['percCompleted'] == 100) {
					$output_js = '$("#ctid_'.$core->input['ctid'].'").css("text-decoration", "line-through");';
				}
				else {
					$output_js = '$("#ctid_'.$core->input['ctid'].'").css("text-decoration", "none");';
				}
				echo $output_js;
				/* output_xml("<status>true</status><message><![CDATA[<script>".$output_js."</script>]]></message>"); */
				break;
			case 1:
			case 2:
			case 3:
				output_xml("<status>false</status><message></message>");
				break;
		}
	}
}
?>