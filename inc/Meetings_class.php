<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Meetings.php
 * Created:        @tony.assaad    Nov 7, 2013 | 3:09:17 PM
 * Last Update:    @tony.assaad    Nov 7, 2013 | 3:09:17 PM
 */

/**
 * Description of Meetings
 *
 * @author tony.assaad
 */
class Meetings {
	private $meeting = array();
	private $errorcode = 0;

	public function __construct($id = '', $simple = false) {
		if(isset($id) && !empty($id)) {
			$this->meeting = $this->read($id, $simple);
		}
	}

	private function read($id, $simple = false) {
		global $db;
		$query_select = '*';
		if($simple == true) {
			$query_select = 'mtid, title,identifier ,description';
		}

		return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."meetings WHERE mtid=".$db->escape_string($id)));
	}

	public function create($meeting_data = array()) {
		global $db, $core, $log;
		if(is_array($meeting_data)) {
			$this->meeting = $meeting_data;
			if(empty($this->meeting['title'])) {
				$this->errorcode = 1;
				return false;
			}

			if(value_exists('meetings', 'title', $this->meeting['title'], ' createdBy='.$core->user['uid'].'')) {
				$this->errorcode = 4;
				return false;
			}
			if(!empty($meeting_data['altfromDate'])) {
				$fromdate = explode('-', $meeting_data['altfromDate']);

				if(checkdate($fromdate[1], $fromdate[0], $fromdate[2])) {
					$this->meeting['fromDate'] = strtotime($this->meeting['altfromDate'].' '.$this->meeting['fromTime']);
					$this->meeting['toDate'] = strtotime($this->meeting['alttoDate'].' '.$this->meeting['toTime']);
				}
			}
			if($meeting_data['fromDate'] > $meeting_data['toDate']) {
				$this->errorcode = 3;
				return false;
			}


			if(is_empty($this->meeting['title'], $this->meeting['fromDate'], $this->meeting['toDate'], $this->meeting['fromTime'], $this->meeting['toTime'])) {
				$this->errorcode = 1;
				return false;
			}

			if(value_exists('meetings', 'title', $this->meeting['title'], 'createdBy='.$core->user['uid'])) { /* ADD TIME CHECK, OTHERWISE OKAY */
				$this->errorcode = 2;
				return false;
			}

			/* Check if meeting intersects with another for the same user - START */
			/* Check if meeting intersects with another for the same user - END */

			$this->meeting['title'] = ucwords(strtolower($this->meeting['title']));

			$sanitize_fields = array('title', 'fromDate', 'toDate', 'description');
			foreach($sanitize_fields as $val) {
				$this->meeting[$val] = $core->sanitize_inputs($this->meeting[$val], array('removetags' => true));
			}

			$meeting_data = array(
					'title' => $this->meeting['title'],
					'identifier' => substr(md5(uniqid(microtime())), 1, 10),
					'fromDate' => $this->meeting['fromDate'],
					'toDate' => $this->meeting['toDate'],
					'description' => $this->meeting['description'],
					'location' => $this->meeting['location'],
					'createdBy' => $core->user['uid'],
					'createdOn' => TIME_NOW
			);
			$insertquery = $db->insert_query('meetings', $meeting_data);
			if($insertquery) {
				$this->errorcode = 0;
				$this->meeting['mtid'] = $mtid = $db->last_id();

				$log->record('addedmeeting', $mtid);
				//$this->get_meetingassociations($this->meeting['mtid'])->set_associations($this->meeting['associations']);
				$this->set_associations($this->meeting['associations']);
				/* insert meetings Attendees */
				$this->set_attendees($this->meeting['attendees']);
				if(isset($this->meeting['attendees']['notifyuser']) && ($this->meeting['attendees']['notifyuser'] == 1)) {
					$this->meeting['attendees']['mtid'] = $this->meeting['mtid'];
					$this->notify_usersattendee($this->meeting['attendees']);
				}
				if(isset($this->meeting['attendees']['notifyrep']) && ($this->meeting['attendees']['notifyrep'] == 1)) {
					$this->meeting['attendees']['mtid'] = $this->meeting['mtid'];
					$this->notify_repattendee($this->meeting['attendees']);
				}
				return true;
			}
		}
	}

	private function notify_usersattendee($notificationsdata) {
		MeetingsAttendees::notify_attendees($notificationsdata);
	}

	private function notify_repattendee($notificationsdata) {
		//unset($notificationsdata[notifyuser], $notificationsdata[notifyrep]);
		MeetingsAttendees::notify_attendees($notificationsdata);
	}

	private function set_attendees(array $attendees) {
		global $core;
		unset($attendees['notifyuser'], $attendees['notifyrep']);
		if(empty($attendees)) {
			$attendees = $this->meeting['attendees'];
		}
		if(!isset($attendees)) {
			$attendees = array(array('idAttr' => 'uid', 'mtid' => $this->meeting['mtid'], 'attendee' => $core->user['uid']));
		}

		if(!empty($attendees)) {
			foreach($attendees as $attendee) {
				foreach($attendee as $key => $val) {
					if(empty($val)) {
						continue;
					}
					$new_attendee['mtid'] = $this->meeting['mtid'];
					$new_attendee['idAttr'] = $key;
					$new_attendee['attendee'] = $core->sanitize_inputs($val);
					MeetingsAttendees::set_attendee($new_attendee);
				}
			}
		}
	}

	private function set_associations($associations = '') {
		if(empty($associations)) {
			$associations = $this->meeting['associations'];
		}
		if(is_array($associations)) {
			foreach($associations as $key => $val) {
				if(empty($val)) {
					continue;
				}
				$new_association['mtid'] = $this->meeting['mtid'];
				$new_association['idAttr'] = $key;
				$new_association['id'] = $val;

				MeetingsAssociations::set_association($new_association);
			}
		}
	}

	public function update($meeting_data = array()) {
		global $db, $log, $core;
		$associations = $meeting_data['associations'];
		$attendees = $meeting_data['attendees'];
		$notifyuser = $attendees['notifyuser'];
		$notifyrep = $attendees['notifyrep'];
		unset($meeting_data['attendees'], $meeting_data['associations'], $attendees['notifyuser'], $attendees['notifyrep']);
		if(value_exists('meetings', 'title', $meeting_data['title'], 'mtid!='.intval($this->meeting['mtid']).' AND createdBy='.$core->user['uid'].'')) {
			$this->errorcode = 3;
			return false;
		}
		/* Needs validation for time */
		$meeting_data['fromDate'] = strtotime($meeting_data['fromDate'].' '.$meeting_data['fromTime']);
		$meeting_data['toDate'] = strtotime($meeting_data['toDate'].' '.$meeting_data['toTime']);
		unset($meeting_data['fromTime'], $meeting_data['toTime'], $meeting_data['altfromDate'], $meeting_data['alttoDate']);
		$query = $db->update_query('meetings', $meeting_data, 'mtid='.$db->escape_string($this->meeting['mtid']));
		if($query) {
			$this->errorcode = 2;
			$log->record('updatedmeeting', $this->meeting['mtid']);
			if(is_array($attendees)) {
				foreach($attendees as $meeetingattendees) {
					$meetingatt_obj = new MeetingsAttendees($meeetingattendees['matid']);
					$meetingatt_obj->update_attendees($meeetingattendees);
				}
			}
			$db->delete_query('meetings_associations', 'mtid='.intval($this->meeting['mtid']));
			$this->set_associations($associations);
		}

		if(isset($notifyuser) && $notifyuser == 1) {
			$attendees['notifyuser'] = 1;
			$attendees['mtid'] = $this->meeting['mtid'];
			$this->notify_usersattendee($attendees);
		}
		if(isset($notifyrep) && ($notifyrep == 1)) {
			$attendees['notifyrep'] = 1;
			$this->notify_repattendee($attendees);
		}
	}

	public static function get_multiplemeetings(array $options = array()) {
		global $db, $core;

		$sort_query = 'fromDate DESC';
		if(isset($options['order']['sortby'], $options['order']['order']) && !is_empty($options['order']['sortby'], $options['order']['order'])) {
			$sort_query = $options['order']['sortby'].' '.$options['order']['order'];
		}

		$query_where_and = ' AND ';
		if(isset($options['hasmom'])) {
			$query_where = ' WHERE hasMOM='.intval($options['hasmom']);
		}
		else {
			$query_where_and = ' WHERE ';
		}

		if($options['filter_where']) {
			$query_where .= $query_where_and.$options['filter_where'];
		}

		if($core->usergroup['meetings_canViewAllMeetings'] == 0) {
			$query_where .= $query_where_and.'(createdBy='.$core->user['uid'].' OR isPublic=1';
			$meetings_sharedwith = Meetings::get_meetingsshares_byuser();
			if(is_array($meetings_sharedwith)) {
				$query_where .= ' OR mtid IN ('.implode(', ', array_keys($meetings_sharedwith)).')';
			}
			$query_where .= ')';
		}

		$meetingsquery = $db->query("SELECT * FROM ".Tprefix."meetings{$query_where} ORDER BY {$sort_query}");

		if($db->num_rows($meetingsquery) > 0) {
			while($rowmeetings = $db->fetch_assoc($meetingsquery)) {
				$meeting[$rowmeetings['mtid']] = $rowmeetings;
			}
		}
		return $meeting;
	}

	public static function get_meetingsshares_byuser($uid = '') {
		global $core, $db;
		if(empty($uid)) {
			$uid = $core->user['uid'];
		}

		$query = $db->query('SELECT mtid FROM '.Tprefix.'meetings_sharedwith WHERE uid='.intval($uid));
		if($db->num_rows($query) > 0) {
			while($share = $db->fetch_assoc($query)) {
				$shares[$share['mtid']] = new Meetings($share['mtid']);
			}
			return $shares;
		}
		return false;
	}

	public function get_attendees($filters = array()) {
		global $db;

		if(is_array($filters['atttypes'])) {
			$filter_where = ' WHERE idAttr IN("'.implode('","', $filters[atttypes]).'") AND mtid='.intval($this->meeting['mtid']).'';
		}
		else {
			$filter_where = ' WHERE mtid='.intval($this->meeting['mtid']).'';
		}
		$query = $db->query('SELECT matid FROM '.Tprefix.'meetings_attendees '.$filter_where.'');
		if($db->num_rows($query)) {
			while($rowattendee = $db->fetch_assoc($query)) {
				$attendees[$rowattendee['matid']] = new MeetingsAttendees($rowattendee['matid']);
			}
			return $attendees;
		}
		return false;
	}

	public function parse_attendees($displayas = 'line') {
		$attendees_objs = $this->get_attendees();
		if(is_array($attendees_objs)) {
			foreach($attendees_objs as $id => $attendee) {
				$attendees[] = $attendee->get()['displayName'];
			}

			if($displayas == 'list') {
				return '<ul><li>'.implode('</li><li>', $attendees).'</li></ul>';
			}
			else {
				return implode(', ', $attendees);
			}
		}
		return false;
	}

	public function can_viewmeeting() {
		global $core;
		if($core->usergroup['meetings_canViewAllMeetings'] == 0) {
			if($this->meeting['isPublic'] == 0) {
				if($this->meeting['createdBy'] != $core->user['uid']) {
					if(!value_exists('meetings_sharedwith', 'mtid', $this->meeting['mtid'], 'uid='.$core->user['uid'])) {
						return false;
					}
					else {
						return true;
					}
				}
				else {
					return true;
				}
			}
			else {
				return true;
			}
		}
		else {
			return true;
		}
	}

	public function share($meeting_data = array()) {
		global $db, $core;
		if(is_array($meeting_data)) {
			foreach($meeting_data as $key => $val) {
				if(empty($val)) {
					continue;
				}
				/* get exist users for the current meeting */
				$existing_users = $this->get_shared_users();
				/* get the difference between the exist users and the slected users */
				if(is_array($existing_users)) {
					$existing_users = array_keys($existing_users);
					$users_toremove = array_diff($existing_users, $meeting_data);
					if(!empty($users_toremove)) {
						$db->delete_query('meetings_sharedwith', 'uid IN ('.$db->escape_string(implode(',', $users_toremove)).') AND mtid='.$this->meeting['mtid']);
					}
				}
				$meeting_shares['mtid'] = $this->meeting['mtid'];
				$meeting_shares['createdBy'] = $core->user['uid'];
				$meeting_shares['createdOn'] = TIME_NOW;
				$meeting_shares['uid'] = $core->sanitize_inputs($val);
				if(!value_exists('meetings_sharedwith', 'uid', $val, ' mtid='.$this->meeting['mtid'])) {
					$db->insert_query(' meetings_sharedwith', $meeting_shares);
					$this->errorcode = 0;
				}
			}
		}
	}

	public function get_shared_users() {
		global $db;

		$query = $db->query('SELECT uid FROM '.Tprefix.'meetings_sharedwith WHERE mtid='.$db->escape_string($this->meeting['mtid'].''));
		if($db->num_rows($query)) {
			while($user = $db->fetch_assoc($query)) {
				$users[$user['uid']] = new Users($user['uid']);
			}
			return $users;
		}
		return false;
	}

	public function get_createdby() {
		return new Users($this->meeting['createdBy']);
	}

	public function get_modifiedby() {
		return new Users($this->meeting['modifiedBy']);
	}

	public function get_mom() {
		return MeetingsMOM::get_mom_bymeeting($this->meeting['mtid']);
	}

	public function get_errorcode() {
		return $this->errorcode;
	}

	public function get_meetingassociations() {
		global $db;
		/* Get all associatiosn related to this meeting */
		$query = $db->query('SELECT * FROM '.Tprefix.'meetings_associations WHERE mtid = '.$db->escape_string($this->meeting['mtid'].''));
		if($db->num_rows($query)) {
			while($meeting_assoc = $db->fetch_assoc($query)) {
				$meeting_associations[$meeting_assoc['mtaid']] = new MeetingsAssociations($meeting_assoc['mtaid']);
			}
			return $meeting_associations;
		}
		return false;
	}

	public function get() {
		return $this->meeting;
	}

}

class MeetingsAttendees {
	private $attendee = array();

	public function __construct($id = '', $simple = true) {
		if(isset($id) && !empty($id)) {
			$this->read($id, $simple);
		}
	}

	private function read($id, $simple = true) {
		global $db;
		$this->attendee = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."meetings_attendees WHERE matid=".$db->escape_string($id)));
	}

	public static function set_attendee($attendee = array()) {
		global $db;
		if(is_array($attendee)) {
			$db->insert_query('meetings_attendees', $attendee);
		}
	}

	public function switch_attendee($idAttr = '') { /* filter the type of ATTRid */
		if(!empty($idAttr)) {
			$this->attendee['idAttr'] = $idAttr;
		}
		switch($this->attendee['idAttr']) {
			case 'uid':
				return new Users($this->attendee['attendee']);
				break;
			case 'repid':
				return new representatives($this->attendee['attendee']);
				break;
		}
	}

	public static function notify_user(array $notificationsdata) {
		global $db;
		if(is_array($notificationsdata)) {
			$meeting_obj = new MeetingsAttendees();
			if(!empty($notificationsdata['notifyuser']) && ($notificationsdata['notifyuser'] == 1)) {
				//unset($notificationsdata['notifyuser'], $notificationsdata['notifyrep']);
				$appointmentdata['meeting'] = $notificationsdata['mtid'];  //add $notificationsdata[attendees]
				foreach($notificationsdata as $tonotify) {
					if(empty($tonotify[uid])) {
						continue;
					}
					unset($tonotify['repid']);
					/* Get User object */
					$user_obj = new Users($tonotify['uid']);
					$attendees_details = $user_obj->get();
					$appointmentdata['to'] = $attendees_details['email'];
					$meeting_obj->send_appointment($notificationsdata);
				}
			}
		}
	}

	public function notify_rep(array $notificationsdata) {
		global $db;
		if(is_array($notificationsdata)) {
			$meeting_obj = new MeetingsAttendees();
			if(!empty($notificationsdata['notifyrep']) && ($notificationsdata['notifyrep'] == 1)) {
				unset($notificationsdata['notifyuser'], $notificationsdata['notifyrep']);
				foreach($notificationsdata as $tonotify) {
					if(empty($tonotify['repid'])) {
						continue;
					}
					unset($tonotify['uid']);
					/* Get User object */
					$attendees_details = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."representatives WHERE rpid='".$tonotify['repid']."'"));
					$appointmentdata_email['to'] = $attendees_details['email'];
					if(empty($appointmentdata_email['to'])) {
						continue;
					}
					$meeting_obj->send_appointment($appointmentdata_email);
				}
			}
		}
	}

	public static function notify_attendees($appointment_data) {
		global $core, $log;
		if(is_array($appointment_data)) {
			/* Get meeting details for these attenddees */
			$meeting_obj = new Meetings($appointment_data['mtid']);
			$appointment_data['meeting'] = $meeting_obj->get();

			/* exclude repre or user if notify <>1 */
			if(!empty($appointment_data['notifyuser']) && ($appointment_data['notifyuser'] == 1)) {
				$filters[] = 'uid';
			}
			if(!empty($appointment_data['notifyrep']) && ($appointment_data['notifyrep'] == 1)) {
				$filters[] = 'repid';
			}
			$attendes_objs = $meeting_obj->get_attendees(array('atttypes' => $filters));

			foreach($attendes_objs as $attendes_obj) {
				if($attendes_obj->is_representative()) {
					$receipient_attendees[] = $attendes_obj->get_rep()->get();
				}
				else {
					$receipient_attendees[] = $attendes_obj->get_user()->get();
				}
			}
			/* Loop over the receients and send them notifications email */
			if(is_array($receipient_attendees)) {
				foreach($receipient_attendees as $receipient_attendee) {
					$receipient_attendee = array_unique($receipient_attendee);
					/* call ics object then write (to disk)  */
					$ical_obj = new Icalendar(array('identifier'=>$appointment_data['meeting']['identifier'], 'uidate' => $appointment_data['meeting']['createdOn'], 'component' => 'event', 'method' => 'REQUEST'));  /* pass identifer to outlook to avoid creation of multiple file with the same date */
					$ical_obj->set_datestart($appointment_data['meeting']['fromDate']);
					$ical_obj->set_datend($appointment_data['meeting']['toDate']);
					$ical_obj->set_location($appointment_data['meeting']['location']);
					$ical_obj->set_summary($appointment_data['meeting']['title']);
					$ical_obj->set_categories('Appointment');
					$ical_obj->set_organizer();
					$ical_obj->set_icalattendees($receipient_attendees);
					$ical_obj->set_description($appointment_data['meeting']['description']);
					$ical_obj->endical();
					/* Prepare the Email data */ 
					$email_data = array(
							'to' => $receipient_attendee['email'],
							'from_email' => $core->settings['maileremail'],
							'from' => 'OCOS Mailer',
							'subject' => $appointment_data['meeting']['title'],
							'message' => $ical_obj->geticalendar()
					);
					//$email_data['attachments'] = array('./tmp/'.$ical_obj->get()['summary'].'.ics');
					$mail = new Mailer($email_data, 'php', true, array(), array('content-class' => 'appointment', 'method' => 'REQUEST'));
				}
			}
		}

		//$mail = new Mailer($email_data, 'php');
		/* Get attendees for this meeting  and set their invitation a Sent upon receiving the appointment */
		//	if($mail->get_status() === true) {
		$log->record('meetings_appointment', array('to' => $email_data['to']));
//		$attendees_objs = $meeting_obj->get_attendees();
//		foreach($attendees_objs as $attendees_obj) {
//			$attendees = $attendees_obj->get();
//			$attendees_obj->setinvitaion_sent();
//		}
		//}
	}

	public function update_attendees($attendees_data) {
		global $db;
		if(isset($attendees_data['uid']) && empty($attendees_data['uid']) || isset($attendees_data['repid']) && empty($attendees_data['repid'])) {
			$db->delete_query('meetings_attendees', 'matid='.intval($this->attendee['matid']));
		}
		else if(isset($attendees_data['uid']) && !empty($attendees_data['uid'])) {
			$meetingattendees_data['attendee'] = $attendees_data['uid'];
		}
		if(isset($attendees_data['repid']) && !empty($attendees_data['repid'])) {
			$meetingattendees_data['attendee'] = $attendees_data['repid'];
		}
		if(!empty($this->attendee['matid'])) {
			$query = $db->update_query('meetings_attendees', $meetingattendees_data, 'matid='.$db->escape_string($this->attendee['matid']));
		}
	}

	public function get_user() {
		if($this->attendee['idAttr'] == 'uid' && !empty($this->attendee['attendee'])) {
			return new Users($this->attendee['attendee']);
		}
		return false;
	}

	public function is_representative() {
		if($this->attendee['idAttr'] == 'repid') {
			return true;
		}
		return false;
	}

	public function get_rep() {
		if($this->attendee['idAttr'] == 'repid' && !empty($this->attendee['attendee'])) {
			return new representatives($this->attendee['attendee']);
		}
		return false;
	}

	private function setinvitaion_sent() {
		global $db;
		$query = $db->update_query('meetings_attendees', array('invitationSent' => 1, 'sentOn' => TIME_NOW), 'matid='.$db->escape_string($this->attendee['matid']));
	}

	public function get_meeting() {
		return new Meetings($this->attendee['mtid']);
	}

	public function get() {
		return $this->attendee;
	}

}
?>