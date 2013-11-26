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

	public function create($meeting_data = array()) {
		global $db, $core, $log;
		if(is_array($meeting_data)) {
			$this->meeting = $meeting_data;
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

			if(value_exists('meetings', 'title', $this->meeting['title'])) {
				$this->errorcode = 2;
				return false;
			}

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
				/* insert meetings Attendees */
				if(!isset($this->meeting['attendees'])) {
					$this->meeting['attendees'] = array(array('idAttr' => 'uid', 'mtid' => $mtid, 'attendees' => $core->user['uid']));
				}
				$this->get_meetingassociations($this->meeting['mtid'])->set_associations($this->meeting['attendees']);
				$this->set_attendees();
				return true;
			}
		}
	}

	private function set_attendees() {
		global $db, $core;

		if(!empty($this->meeting['attendees'])) {
			foreach($this->meeting['attendees'] as $key => $val) {
				if(empty($val)) {
					continue;
				}
				$new_attendee['mtid'] = $this->meeting['mtid'];
				$new_attendee['idAttr'] = $key;
				$new_attendee['attendee'] = $core->sanitize_inputs($val);
				$db->insert_query('meetings_attendees', $new_attendee);
			}
		}
	}

	public function update($meeting_data = array()) {
		global $db, $log;
		unset($meeting_data['attendees']);

		/* Needs validation for time */
		/* Needs update for attendees */
		$meeting_data['fromDate'] = strtotime($meeting_data['fromDate'].' '.$meeting_data['fromTime']);
		$meeting_data['toDate'] = strtotime($meeting_data['toDate'].' '.$meeting_data['toTime']);
		unset($meeting_data['fromTime'], $meeting_data['toTime'], $meeting_data['altfromDate'], $meeting_data['alttoDate']);
		$query = $db->update_query('meetings', $meeting_data, 'mtid='.$db->escape_string($this->meeting['mtid']));
		if($query) {
			$this->errorcode = 2;
			$log->record('updatedmeeting', $this->meeting['mtid']);
		}
	}

	public static function get_multiplemeetings($id = '', array $order = array(), array $option = array()) {
		global $db, $core;

		$sort_query = 'fromDate DESC';
		if(isset($order['sortby'], $order['order']) && !is_empty($order['sortby'], $order['order'])) {
			$sort_query = $order['sortby'].' '.$order['order'];
		}
		if($option['hasmom'] == 1) {
			$where_hasMOM = ' WHERE title IS NOT NULL ';
		}
		else {
			$where_hasMOM = ' WHERE hasMOM <>1 AND title IS NOT NULL';
		}

		$meetingsquery = $db->query("SELECT * FROM ".Tprefix."meetings {$where_hasMOM} AND createdBy={$core->user['uid']} ORDER BY {$sort_query}");

		if($db->num_rows($meetingsquery) > 0) {
			while($rowmeetings = $db->fetch_assoc($meetingsquery)) {
				$meeting[$rowmeetings['mtid']] = $rowmeetings;
			}
		}
		return $meeting;
	}

	public function get_attendees() {
		global $db;

		$query = $db->query('SELECT * FROM '.Tprefix.'meetings_attendees WHERE mtid='.intval($this->meeting['mtid']));
		if($db->num_rows($query)) {
			while($attendee = $db->fetch_assoc($query)) {
				if($attendee['idAttr'] == 'uid') {
					$attendees[$attendee['attendee']] = new Users($attendee['attendee']);
				}
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
		$query = $db->query('SELECT * FROM '.Tprefix.'meeting_associations WHERE mtid='.$db->escape_string($this->meeting['mtid'].''));
		if($db->num_rows($query)) {
			while($meeting_assoc = $db->fetch_assoc($query)) {
				$meeting_associsations[$meeting_assoc['matid']] = new Meeting_association($meeting_assoc['matid']);
				$meeting_associsations[$meeting_assoc['matid']] = $meeting_associsations[$meeting_assoc['matid']]->get();
			}
			return $meeting_associsations;
		}
	}

	public function get() {
		return $this->meeting;
	}

}

class MeetingAttendees {
	public function __construct($meeting_id = '', $simple = false) {
		if(isset($id) && !empty($id)) {
			$this->attenddees = $this->read($id, $simple);
		}
	}

}
?> 
