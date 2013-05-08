<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: AttendanceAddDays_class.php
 * Created:        @tony.assaad    Apr 23, 2013 | 2:18:23 PM
 * Last Update:    @tony.assaad    Apr 24, 2013 | 2:18:23 PM
 */

/**
 * Description of AttendanceAddDays_class
 *
 * @author tony.assaad
 */
class AttendanceAddDays Extends Attendance {
	private $status = 0; //0=No errors;1=Subject missing;2=Entry exists;3=Error saving;4=validation violation
	private $additionaldays = array();

	public function __construct($attedadddays_data = array()) {
		parent::__construct($attedadddays_data);

		if(!empty($attedadddays_data['adid']) && isset($attedadddays_data['adid'])) {
			$this->read($attedadddays_data['adid'], '');
		}
		elseif(!empty($attedadddays_data['identifier']) && isset($attedadddays_data['identifier'])) {
			$this->read('', $attedadddays_data['identifier']);
		}
	}

	public function approve($id, $uid, $fromemail) {
		global $db;
		$id = $db->escape_string($id);
		if($this->can_apporve($uid, $fromemail)) {
			$db->update_query("attendance_additionalleaves", array('isApproved' => 1), "identifier='$id' AND isApproved='0'");
		}
		else {
			return false;
		}
	}

	public function request($uid, $data = array()) {
		global $db, $core, $log;

		unset($data['module'], $data['action'], $data['uid']);
		$this->data = $data;

		if(is_empty($this->data['date'], $this->data['numDays'])) {
			$this->status = 1;
			return false;
		}
		$additional_leavesdata = array(
				'identifier' => $identifier = substr(md5(uniqid(microtime())), 1, 10),
				'numDays' => $core->sanitize_inputs($this->data['numDays']),
				'date' => $core->sanitize_inputs(strtotime($this->data['date'])),
				'addedBy' => $core->user['uid'],
				'isApproved' => $this->data['isApproved'],
				'remark' => $core->sanitize_inputs($this->data['remark'])
		);
		$additional_leavesdata['uid'] = $uid;
		if(is_array($additional_leavesdata)) {
			if(!$this->check_existingrequest($uid, $additional_leavesdata['date'])) { /* check if users have exisintg additionalleaves in the same date they are requesting */
				$query = $db->insert_query('attendance_additionalleaves', $additional_leavesdata);
				if($query) {
					$this->status = 0;
					$log->record($db->last_id());
				}
			}
			else {
				$this->status = 2;
				return false;
			}
		}
	}

	public function check_existingrequest($uids, $date) {
		global $db;
		$query = $db->query("SELECT uid FROM ".Tprefix."attendance_additionalleaves WHERE uid =('".( $uids)."')
							AND date='".$db->escape_string($date)."' ");
		if($db->num_rows($query) > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	public function can_apporve($uid, $reporttofromemail) {
		global $core;
		/* /* if  from email= email of reportto to this user */
		$user = new Users($uid);
		$reporttsto = $user->get_reportsto()->get();
		if($reporttsto['email'] == $reporttofromemail) {
			return true;
		}
		else {
			return false;
		}
	}

	public function notify_request($reportsto = array(), $requester, $additionaldaysdata = array()) {
		global $log, $core, $lang;
		/* notify reports to */
		$body_message = '';
		if(is_array($reportsto)) {
			$additionaldaysdata['dateoutput'] = date($core->settings['dateformat'], $additionaldaysdata['date']);
			$body_message = $requester['displayName'].$lang->adddaysrequestaproval.'<br/>'.$lang->additionaldays.':'.$additionaldaysdata[numDays].' '.$lang->days.'<br/>'.$lang->correspondtoperiod.': '.$additionaldaysdata['dateoutput']
					.'<br/>'.$lang->justification.': '.$additionaldaysdata['remark'];
			$email_data = array(
					'from_email' => 'approve_requestadddays@sandbox.ocos.orkila.com',
					'from' => 'Orkila Attendance System',
					'to' => $reportsto['email'],
					'subject' => $requester['displayName'].$lang->adddaysnotificationsubject.'['.$additionaldaysdata['identifier'].']',
					'message' => $body_message
			);

			$mail = new Mailer($email_data, 'php');
			if($mail->get_status() === true) {
				$log->record('notifysupervisors', $reportsto);
			}
		}
	}

	public function notifyapprove($request_key, $uid) {
		global $db, $lang, $log;
		if(value_exists('attendance_additionalleaves', 'isApproved', 1, 'identifier="'.$request_key.'"')) {
			$user = new Users($uid);
			$requester_details = $user->get();
			$lang->adddaysrequestaproval = $lang->sprint($lang->adddaysapprovedmessage, $requester_details['displayName'], $this->additionaldays['numDays']);
			$email_data = array(
					'from_email' => 'attendance@ocos.orkila.com',
					'from' => 'Orkila Attendance System',
					'to' => $requester_details['email'],
					'subject' => $lang->additionadaysapprovedsubject,
					'message' => $lang->adddaysrequestaproval
			);
			$mail = new Mailer($email_data, 'php');
			if($mail->get_status() === true) {
				$log->record('notifyrequester', $reportsto);
			}
		}
	}

	private function read($id = '', $identifier = '', $simple = true) {
		global $db;
		if(empty($id) && !empty($identifier)) {
			$where_statement = ' WHERE identifier="'.$db->escape_string($identifier).'"';
		}
		elseif(!empty($id)) {
			$where_statement = ' WHERE adid='.$db->escape_string($id);
		}
		$this->additionaldays = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."attendance_additionalleaves 
															{$where_statement}"));

		if(is_array($this->additionaldays) && !empty($this->additionaldays)) {
			return true;
		}
		return false;
	}

	public function get() {
		return $this->additionaldays;
	}

	protected function get_affilisateduser() {
		$user_attendance = new Users($id);
		return $this->userattendance = $user_attendance->get();
	}

	public function get_status() {
		return $this->status;
	}

}
?>
