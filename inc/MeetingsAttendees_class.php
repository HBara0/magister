<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: MeetingsAttendees_class.php
 * Created:        @hussein.barakat    17-Dec-2015 | 11:11:47
 * Last Update:    @hussein.barakat    17-Dec-2015 | 11:11:47
 */

class MeetingsAttendees {
    public $attendee = array();

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
            if(!value_exists('meetings_attendees', 'attendee', $attendee['attendee'], 'idAttr="'.$db->escape_string($attendee['idAttr']).'" AND mtid='.intval($attendee['mtid']))) {
                $db->insert_query('meetings_attendees', $attendee);
                return true;
            }
            return false;
        }
        return false;
    }

    public function get_attendee() {
        switch($this->attendee['idAttr']) {
            case 'uid':
                return new Users($this->attendee['attendee']);
                break;
            case 'rpid':
                return new Representatives($this->attendee['attendee']);
                break;
        }
    }

    public function update($attendee_data) {
        global $db;

        $attendee_data['attendee'] = intval($attendee_data['id']);
        unset($attendee_data['id']);

        if(!empty($this->attendee['matid'])) {
            $db->update_query('meetings_attendees', $attendee_data, 'matid='.intval($this->attendee['matid']));
        }
    }

    public function delete() {
        global $db;

        $db->delete_query('meetings_attendees', 'matid='.intval($this->attendee['matid']));
    }

    public function get_user() {
        if($this->attendee['idAttr'] == 'uid' && !empty($this->attendee['attendee'])) {
            return new Users($this->attendee['attendee']);
        }
        return false;
    }

    public function is_representative() {
        if($this->attendee['idAttr'] == 'rpid') {
            return true;
        }
        return false;
    }

    public function get_rep() {
        if($this->attendee['idAttr'] == 'rpid' && !empty($this->attendee['attendee'])) {
            return new Representatives($this->attendee['attendee']);
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