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
    protected $erro_code = 0;

    public function __construct($uid) {
        $this->user['uid'] = $uid;
        $this->user = $this->get_user()->get();
    }

    public function get_attendance_bydate($date) {
        global $db;
        if(!empty($date)) {
            $this->attendance = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."attendance WHERE date=".$db->escape_string($date)));
            if(is_array($this->attendance)) {
                return true;
            }
            return false;
        }

        return false;
    }

    public function get_user() {
        return new Users($this->user['uid']);
    }

    public function get_attendance() {
        return $this->attendance;
    }

}
?>
