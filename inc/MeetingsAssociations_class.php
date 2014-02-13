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
class MeetingsAssociations {
	private $meetingassociation = array();

	public function __construct($id = '', $simple = false) {
		if(isset($id) && !empty($id)) {
			$this->meetingassociation = $this->read($id, $simple);
		}
	}

	private function read($id, $simple = false) {
		global $db;
		return $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."meetings_associations WHERE matid=".$db->escape_string($id)));
	}

	public static function set_association($association = array()) {
		global $db;
		if(is_array($association)) {
			$db->insert_query('meetings_associations', $association);
		}
	}

	public function get_meeting() {
		return new Meetings($this->association['mtid']);
	}

	public function get() {
		return $this->meetingassociation;
	}

}
?>
