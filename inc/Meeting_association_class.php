<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: Meeting_association.php
 * Created:        @tony.assaad    Nov 19, 2013 | 12:03:27 PM
 * Last Update:    @tony.assaad    Nov 19, 2013 | 12:03:27 PM
 */

/**
 * Description of Meeting_association
 *
 * @author tony.assaad
 */
class Meeting_association {
	private $meeting = array();

	public function __construct($id = '', $simple = false) {
		if(isset($id) && !empty($id)) {
			$this->meeting = $id;
			$this->meetingassoc = $this->read($id, $simple);
			print_r($this->meetingassoc);
		}
	}

	private function read($id, $simple = false) {
		global $db;

		return $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."meeting_associations WHERE mtid=".$db->escape_string($id)));
	}

	public function set_attendees($attendees = array()) {
		global $db, $core;
		if(is_array($attendees)) {
			foreach($attendees as $key => $val) {
				if(empty($val)) {
					continue;
				}
				$new_attendee['mtid'] = $this->meeting;
				$new_attendee['idAttr'] = $key;
				$new_attendee['attendees'] = $core->sanitize_inputs($val);
				$db->insert_query('meeting_associations', $new_attendee);
			}
		}
	}

	public function get_meeting() {
		return new Meetings($this->meeting);
	}

	public function get() {
		return $this->meetingassoc;
	}

}
?>
