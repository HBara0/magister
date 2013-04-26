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

		if(!empty($attedadddays_data['adid'])) {
			$this->additionaldays = $this->read($attedadddays_data['adid']);
		}
	}

	public function aprrove() {
		global $db;
	}

	public function request($data = array()) {
		global $db, $core, $log;

		$this->usersdata['users'] = $data['uid'];
		unset($data['module'], $data['action'], $data['uid']);  //'uid in', implode(',', $data['uid'])
		$this->data = $data;

		$this->data['uid'] = $this->usersdata['users'];
		$this->data['identifier'] = substr(md5(uniqid(microtime())), 1, 10);
		if(is_empty($this->data['date'], $this->data['numDays'])) {
			$this->status = 1;
			return false;
		}
		foreach($this->data['uid'] as $userid) {
			$additional_leavesdata[] = array(
					'uid' => $userid,
					'identifier' => $this->data['identifier'],
					'numDays' => $core->sanitize_inputs($this->data['numDays']),
					'date' => $core->sanitize_inputs(strtotime($this->data['date'])),
					'addedBy' => $core->user['uid'],
					'isApproved' => $this->data['isApproved'],
					'remark' => $core->sanitize_inputs($this->data['remark'])
			);
		}
		if(is_array($additional_leavesdata)) {
			foreach($additional_leavesdata as $additional_leaves) {
				if(!$this->check_existingrequest($additional_leaves['uid'], $additional_leaves['date'])) { /* check if users have exisintg additionalleaves in the same date they are requesting */
					$query = $db->insert_query('attendance_additionalleaves', $additional_leaves);
					if($query) {
						$this->status = 0;
						$log->record($db->last_id());
					}
				}
//				else {
//					$this->status = 2;
//					return false;
//				}
			}
		}
	}

	public function check_existingrequest($uids, $date) {
		global $db;
		$query = $db->query("SELECT uid FROM ".Tprefix."attendance_additionalleaves WHERE uid in('".($uids)."')
							AND date='".$db->escape_string($date)."' ");
		if($db->num_rows($query) > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	public function can_Apporve($id = '') {
		
	}

	public function notify_Request($reportsto = array(), $requester, $additionaldaysdata = array()) {
		global $log, $core, $lang;
		/* notify reports to */
		$body_message = '';
		print_r($additionaldaysdata);
		if(is_array($reportsto)) {
			$additionaldaysdata['dateoutput'] = date($core->settings['dateformat'], $additionaldaysdata['date']);
			$body_message = $requester['displayName'].$lang->adddaysrequestaproval.'<br/>'.$lang->additionaldays.':'.$additionaldaysdata[numDays].' '.$lang->days.'<br/>'.$lang->correspondtoperiod.': '.$additionaldaysdata['dateoutput']
					.'<br/>'.$lang->justification.': '.$additionaldaysdata['remark'];

			$email_data = array(
					'from_email' => 'approve_requestadddays@ocos.orkila.com',
					'from' => 'Orkila Attendance System',
					'to' => $reportsto['email'],
					'subject' => $requester['displayName'].$lang->adddaysnotificationsubject,
					'message' => $body_message
			);

			$mail = new Mailer($email_data, 'php');
			if($mail->get_status() === true) {
				$log->record('notifysupervisors', $reportsto);
			}
		}
	}

	private function read($id = '', $simple = true) {
		global $db;
		if(empty($id)) {
			$id = $this->additionaldays['adid'];
		}

		$this->additionaldays = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."attendance_additionalleaves 
															WHERE adid=".$db->escape_string($id).""));
		if(is_array($this->additionaldays) && !empty($this->additionaldays)) {
			return true;
		}
		return false;
	}

	public function get($id = '') {
		return $this->additionaldays;
	}

	protected function get_affilisateduser() {
		$user_attendance = new Users($id);
		return $this->userattendance = $user_attendance->get();
	}

	public function notify_Approval($id = '') {
		
	}

	public function get_status() {
		return $this->status;
	}

}
?>
