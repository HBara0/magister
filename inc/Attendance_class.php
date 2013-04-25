<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: Attendance_class.php
 * Created:        @tony.assaad    Apr 22, 2013 | 3:42:33 PM
 * Last Update:    @tony.assaad    Apr 22, 2013 | 3:42:33 PM
 */

/**
 * Description of Attendance_class
 *
 * @author tony.assaad
 */
class Attendance {
	protected $attendance = array();

	public function __construct($attendence_data = array()) {
		if(isset($attendence_data['uid']) && !empty($attendence_data['uid'])) {
			$this->get_user($attendence_data['uid']);
		}
		elseif(isset($attendence_data[date])) {
			$this->get_attendance_bydate($attendence_data['date']);
		}
	}

	protected function get_attendance_bydate($date) {
		global $db;
		if(!empty($date)) {
			$this->attendance = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."attendance WHERE date=".$date." "));
			if(is_array($this->attendance)) {
				return true;
			}
		}

		return false;
	}

	protected function get_user($id) {
		if(!empty($id)) {
			$user_attendance = new Users($id);
			return $this->userattendance = $user_attendance->get();
		}
		return false;
	}

	public function get_attendance() {
		return $this->attendance;
	}

}
?>
