<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Logs Class
 * $id: Tasks_class.php
 * Created:		@zaher.reda		April 20, 2012 | 10:53 AM
 * Last Update: @zaher.reda		May 18, 2012 | 09:53 AM
 */

class Tasks {
	private $task = array();
	private $status = 0; //0=No errors;1=Subject missing;2=Entry exists;3=Error saving
	private $date_vars = array('dueDate', 'timeDone');

	public function __construct($id = '', $simple = false) {
		global $core;

		if(isset($id) && !empty($id)) {
			$this->task = $this->read_task($id, $simple);
			if($simple == false) {
				foreach($this->date_vars as $attr) {
					if(isset($this->task[$attr]) && !empty($this->task[$attr])) {
						$this->task[$attr.'_output'] = date($core->settings['dateformat'], $this->task[$attr]);
					}
				}
			}
		}
	}

	private function read_task($id, $simple = false) {
		global $db;

		if(empty($id)) {
			return false;
		}

		$query_select = 'ct.*, u.displayName AS assignedTo';
		if($simple == true) {
			$query_select = 'ctid, pimAppId';
		}
		return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."calendar_tasks ct JOIN ".Tprefix."users u ON (u.uid=ct.uid) WHERE ctid=".$db->escape_string($id)));
	}

	/* Creates the task in the DB
	 * @param  	Array			$data 		Array containing the input	
	 * @return  Boolean						0=No errors;1=Subject missing;2=Entry exists
	 */
	public function create_task(array $data) {
		global $db, $log, $core;
		if(is_empty($data['subject'])) {
			$this->status = 1;
			return false;
		}

		$data['dueDate'] = strtotime($data['dueDate']);
		if(value_exists('calendar_tasks', 'subject', $data['subject'], 'dueDate='.$data['dueDate'].' AND uid='.$db->escape_string($data['uid']))) {
			$this->status = 2;
			return false;
		}

		$data['description'] = $core->sanitize_inputs($data['description'], array('method' => 'striponly', 'removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));

		$new_task = array(
				'uid' => $data['uid'],
				'subject' => ucwords(strtolower($core->sanitize_inputs($data['subject']))),
				'priority' => $data['priority'],
				'percCompleted' => $data['percCompleted'],
				'description' => ucfirst(strtolower($data['description'])),
				'reminderInterval' => $data['reminderInterval'],
				'reminderStart' => strtotime($data['reminderStart']),
				'createdBy' => $core->user['uid'],
				'dueDate' => $data['dueDate']
		);

		if(empty($new_task['reminderStart'])) {
			unset($new_task['reminderInterval'], $new_task['reminderStart']);
		}

		$this->task = $new_task;

		$query = $db->insert_query('calendar_tasks', $new_task);
		if($query) {
			$this->task['ctid'] = $db->last_id();

			$log->record($this->task['ctid']);
			$this->status = 0;
			return true;
		}
		else {
			$this->status = 3;
			return false;
		}
	}

	public function notify_task() {
		global $core, $db, $lang;

		$lang->load('calendar_messages');
		if($this->task['uid'] != $core->user['uid']) {
			fix_newline($this->task['description']);

			/* prepare send  that in icalender format - START */
			$ical_obj = new iCalendar(array('component' => 'task'));  /* pass identifer to outlook to avoid creation of multiple file with the same date */
			$ical_obj->set_summary($this->task['subject']);
			$ical_obj->set_name();
			$ical_obj->set_description($this->task['description']);
			$ical_obj->set_duedate($this->task['dueDate']);
			$ical_obj->set_priority($this->task['priority']);
			//$ical_obj->set_icalattendees($this->task['uid']);
			$ical_obj->sentby();
			$ical_obj->set_percentcomplete($this->task['percCompleted']);
			//$ical_obj->set_categories('CalendarTask');
			$ical_obj->endical();
			$icaltask = $ical_obj->geticalendar();

			$email_data = array(
					'from_email' => $core->user['email'],
					'from' => $core->user['displayName'],
					'subject' => $lang->task.': '.$this->task['subject'],
					//'message' => $lang->sprint($lang->assigntaskmessage, $this->parse_status(), date($core->settings['dateformat'], $this->task['dueDate']), $this->task['description']),
					'message' => $icaltask,
					//'replyby' => $this->task['dueDate'],
					//'flag' => 'Follow up'
			);

			$email_data['to'] = $db->fetch_field($db->query("SELECT email FROM ".Tprefix."users WHERE uid=".$db->escape_string($this->task['uid']).""), 'email');

			/* prepare send  that in icalender format - END */
			$mail = new Mailer($email_data, 'php', true, array(), array('content-class' => 'task', 'filename' => $this->task['subject'].'.ics'));
			if($mail->get_status() === false) {
				return false; //output_xml("<status>false</status><message>{$lang->errorsendingemail}</message>");
			}
		}
	}

	public function set_pimid($pimid) {
		global $db, $log;

		$db->update_query('calendar_tasks', array('pimAppId' => $pimid), 'ctid='.$this->task['ctid']);
	}

	public function change_status($new_status) {
		global $db, $log;

		$new_perc = 0;
		if($new_status == 1) {
			$new_perc = 100;
		}
		$query = $db->update_query('calendar_tasks', array('isDone' => $new_status, 'timeDone' => TIME_NOW, 'percCompleted' => $new_perc), 'ctid='.$db->escape_string($this->task['ctid']));
		if($db->affected_rows() > 0) {
			try {
				if(isset($this->task['pimAppId'])) {
					$ol = new COM('Outlook.Application');
					$mapi = $ol->GetNamespace('MAPI');
					$pimtask = $mapi->GetItemFromID($this->task['pimAppId']);

					if($new_status == 0) {
						$pimtask->complete = false;
						$pimtask->PercentComplete = 0;
						$pimtask->Status = 0;
						$pimtask->Save();
					}
					else {
						$pimtask->MarkComplete();
					}
				}
			}
			catch(Exception $e) {
				
			}

			$this->status = 0;
			return true;
		}
		else {
			$this->status = 3;
			return false;
		}
		$log->record($this->task['ctid'], $new_status);
	}

	public function update_task($completed) {
		global $db, $core, $log;

		if(!isset($completed)) {
			return false;
		}

		$new_status = 0;
		if($completed == '100') {
			$new_status = 1;
		}

		$query = $db->update_query('calendar_tasks', array('percCompleted' => $completed, 'isDone' => $new_status), 'ctid='.$this->task['ctid']);
		if($query) {
			$log->record($this->task['ctid']);
			$this->status = 0;
			return true;
		}
		else {
			$this->status = 3;
			return true;
		}
	}

	public function save_note($note) {
		global $db, $core, $log;

		if(empty($note)) {
			$this->status = 1;
			return false;
		}
		/* Check if task note with same subject created by the same user exists */
		if(value_exists('calendar_tasks_notes', 'note', $note, 'uid='.$core->user['uid'].' AND ctid='.$this->task['ctid'])) {
			$this->status = 2;
			return false;
		}
		else {
			$task_notes_details = array(
					'ctid' => $this->task['ctid'],
					'uid' => $core->user['uid'],
					'note' => $note,
					'dateAdded' => TIME_NOW
			);

			$query = $db->insert_query('calendar_tasks_notes', $task_notes_details);
			if($query) {
				$log->record($db->last_id(), $this->task['ctid']);
				$this->status = 0;
				return true;
			}
			else {
				$this->status = 3;
				return false;
			}
		}
	}

	public function get_notes() {
		global $db, $core;

		$query = $db->query("SELECT ctn.*, u.displayName 
							FROM ".Tprefix." calendar_tasks_notes ctn
							JOIN ".Tprefix."users u ON (u.uid=ctn.uid) 
							WHERE ctn.ctid=".$this->task['ctid']." 
							ORDER BY dateAdded DESC");
		if($db->num_rows($query) > 0) {
			while($tasks_note = $db->fetch_assoc($query)) {
				fix_newline($tasks_note['note']);
				$tasks_notes[$tasks_note['ctnid']] = $tasks_note;
			}
			return $tasks_notes;
		}
		else {
			return false;
		}
	}

	public function get_status() {
		return $this->status;
	}

	public function get_id() {
		return $this->task['ctid'];
	}

	public function get_task() {
		fix_newline($this->task['description']);
		return $this->task;
	}

	public function parse_status() {
		global $lang;

		switch($this->task['priority']) {
			case '0': return $lang->prioritylow;
				break;
			case '1': return $lang->prioritynormal;
				break;
			case '2': return $lang->priorityhigh;
				break;
			default: return false;
		}
	}

}
?>