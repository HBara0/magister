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
	public function __construct($id = '', $simple = false) {
		if(isset($id) && !empty($id)) {
			$this->meeting = $this->read($id, $simple);
		}
	}

	private function read($id, $simple = false) {
		global $db;
		$query_select = '*';
		if($simple == true) {
			$query_select = 'mtid, title, description';
		}

		return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."meetings WHERE mtid=".$db->escape_string($id)));
	}

	public function create($metting_data = array()) {
		global $db, $core, $log;
		if(is_array($metting_data)) {
			$this->metting = $metting_data;
			if(!empty($metting_data['altfromDate'])) {
				$fromdate = explode('-', $metting_data['altfromDate']);

				if(checkdate($fromdate[1], $fromdate[0], $fromdate[2])) {
					$this->metting['fromDate'] = strtotime($this->metting['altfromDate'].' '.$this->metting['fromTime']);
					$this->metting['toDate'] = strtotime($this->metting['alttoDate'].' '.$this->metting['toTime']);
				}
			}
			if($metting_data['fromDate'] > $metting_data['toDate']) {
				$this->errorcode = 3;
				return false;
			}

			if(empty($meeting_data['title']) || empty($meeting_data['fromDate']) || empty($meeting_data['toDate']) || empty($meeting_data['fromTime']) || empty($meeting_data['toTime'])) {
				$this->errorcode = 1;
				//return false;
			}


			if(value_exists('meetings', 'title', $this->metting['title'])) {
				$this->errorcode = 2;
				return false;
			}

			$this->metting['title'] = ucwords(strtolower($this->metting['title']));

			$sanitize_fields = array('title', 'fromDate', 'toDate', 'description');
			foreach($sanitize_fields as $val) {
				$this->metting[$val] = $core->sanitize_inputs($this->metting[$val], array('removetags' => true));
			}

			$meeting_data = array(
					'title' => $this->metting['title'],
					'identifier' => substr(md5(uniqid(microtime())), 1, 10),
					'fromDate' => $this->metting['fromDate'],
					'toDate' => $this->metting['toDate'],
					'description' => $this->metting['description'],
					'location' => $this->metting['location'],
					'createdBy' => $core->user['uid'],
					'createdOn' => TIME_NOW
			);
			$insertquery = $db->insert_query('meetings', $meeting_data);
			if($insertquery) {
				$this->errorcode = 0;
				$mtid = $db->last_id();

				$log->record($mtid);
				/* insert meetings Attendees */
				if(isset($this->metting['attendees'])) {
					$this->set_attendees($mtid);
				}
				return true;
			}
		}
	}

	public function set_attendees($mtid) {
		global $db, $core;

		if(!empty($this->metting['attendees'])) {
			foreach($this->metting['attendees'] as $key => $val) {
				if(empty($val)) {
					continue;
				}
				$new_association['mtid'] = $mtid;
				$new_association['idAttr'] = $key;
				$new_association['attendees'] = $core->sanitize_inputs($val);
				$db->insert_query('meetings_attendees', $new_association);
			}
		}
	}

	public function update($meeting_data = array()) {
		global $db, $core, $log;
		unset($meeting_data['attendees']);
		$meeting_data['fromDate'] = strtotime($meeting_data['fromDate'].' '.$meeting_data['fromTime']);
		$meeting_data['toDate'] = strtotime($meeting_data['toDate'].' '.$meeting_data['toTime']);
		unset($meeting_data['fromTime'], $meeting_data['toTime'], $meeting_data['altfromDate'], $meeting_data['alttoDate']);
		$query = $db->update_query('meetings', $meeting_data, ' mtid='.$db->escape_string($this->meeting['mtid']));
		if($query) {
			$this->errorcode = 2;
		}
	}

	public static function get_multiplemeetings($id = '', array $order = array(), array $option = array()) {
		global $db, $core;

		$sort_query = ' createdOn';
		if(isset($order['sortby'], $order['order']) && !is_empty($order['sortby'], $order['order'])) {
			$sort_query = $order['sortby'].' '.$order['order'];
		}
		if($option['hasmom'] == 1) {
			$where_hasMOM = ' WHERE title IS NOT NULL ';
		}
		else {
			$where_hasMOM = ' WHERE hasMOM <>1 AND title IS NOT NULL';
		}

		$meetingsquery = $db->query("SELECT * FROM ".Tprefix."meetings {$where_hasMOM} AND createdBy= {$core->user['uid']} ORDER BY {$sort_query} ");

		if($db->num_rows($meetingsquery) > 0) {
			while($rowmeetings = $db->fetch_assoc($meetingsquery)) {
				$meeting[$rowmeetings['mtid']] = $rowmeetings;
			}
		}
		return $meeting;
	}

	public function get_attendees(
	$mtid = '') {
		global $db;

		return $attendee = $db->fetch_assoc($db->query("SELECT ma.mtid, ma.attendees AS attr, ma.idAttr FROM ".Tprefix."meetings me JOIN ".Tprefix."meetings_attendees ma ON (me.mtid = ma.mtid) WHERE ma.mtid = ".$db->escape_string($this->meeting['mtid'])), 'attendee');
	}

	public static function get_affiliateemployees() {
		global
		$db, $core;
		$query = $db->query("SELECT u.uid, u.displayName
				FROM ".Tprefix."affiliatedemployees ae
				JOIN ".Tprefix."affiliates aff ON (aff.affid = ae.affid)
				JOIN ".Tprefix."users u ON (u.uid = ae.uid)
				WHERE u.gid!=7 AND (u.uid IN (SELECT uid FROM ".Tprefix."users WHERE reportsTo = {$core->user[uid]})
				OR ae.affid IN (SELECT affid FROM ".Tprefix."affiliatedemployees WHERE (canAudit = 1 OR canHR = 1) AND uid = {$core->user[uid]}))
				ORDER BY displayName ASC");
		$employees_affiliate[0] = '';
		while($employee_affiliate = $db->fetch_assoc($query)) {
			$employees_affiliate[$employee_affiliate['uid']] = $employee_affiliate['displayName'];
		}
		return $employees_affiliate;
	}

	public function get_errorcode() {
		return
				$this->errorcode;
	}

	public function get() {
		return
				$this->meeting

		;
	}

}

class MeetingAttendees {
	public function __construct($meeting_id = '', $simple = false) {
		if(isset($id) && !empty($id)) {
			$this->attenddees = $this->read($id, $simple);
		}
	}

}

class MeetingsMOM {
	public function __construct($mtid = '') {
		if(isset($mtid) && !empty($mtid)) {
			$this->mom = $this->read($mtid);
		}
	}

	private function read($mtid = '') {
		global $db;
		return $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."meetings_minsofmeeting WHERE mtid = ".$db->escape_string($mtid)));
	}

	public function save($mom_data = array()) {
		global $db, $core;

		if(empty($mom_data['mtid']) || empty($mom_data['meetingDetails'])) {
			$this->errorcode = 1;
			return false;
		}
		$mom_data['meetingDetails'] = $core->sanitize_inputs($mom_data['meetingDetails'], array('removetags' => true));
		$mom_data['followup'] = $core->sanitize_inputs($mom_data['followup'], array('removetags' => true));
		$query = $db->insert_query('meetings_minsofmeeting', array('mtid' => $mom_data['mtid'], 'meetingDetails' => $mom_data['meetingDetails'], 'followup' => $mom_data['followup'], 'createdBy' => $core->user['uid'], 'createdOn' => TIME_NOW));
		if($query) {
			$db->update_query('meetings', array('hasMOM' => 1), ' mtid='.$mom_data['mtid']);
			$this->errorcode = 0;
		}
	}

	public function update($mom_data = array()) {
		global $db, $core, $log;
		$mom_data['modifiedBy'] = $core->user['uid'];
		$mom_data['modifiedOn'] = TIME_NOW;

		$query = $db->update_query('meetings_minsofmeeting', array('meetingDetails' => $mom_data['meetingDetails'], 'followup' => $mom_data['followup'], 'modifiedBy' => $mom_data['modifiedBy'], 'modifiedOn' => $mom_data['modifiedOn']), ' mtid='.$db->escape_string($this->mom['mtid']).'');
		if($query) {
			$this->errorcode = 2;
		}
	}

	public function get_errorcode() {
		return $this->errorcode;
	}

	public function get() {
		return $this->mom






		;
	}

}
?> 
