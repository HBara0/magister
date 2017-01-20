<?php

/**
 * Description of Events
 *
 * @author H.B
 */
class Events extends AbstractClass {

    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'eid';
    const TABLE_NAME = 'events';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const REQUIRED_ATTRS = 'title,fromDate,toDate';
    const UNIQUE_ATTRS = 'alias,fromDate,toDate';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $log, $core, $errorhandler, $lang;
        if (!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        $data['createdOn'] = TIME_NOW;
        $data['createdBy'] = $core->user['uid'];
        $data['alias'] = generate_alias($data['title']);
        if (!is_empty($data['fromTime'], $data['fromDate'])) {
            $data['fromDate'] = strtotime($data['fromDate'] . ' ' . $data['fromTime']);
        }
        else {
            $this->errorcode = 2;
            return $this;
        }
        if (!is_empty($data['toTime'], $data['toDate'])) {
            $data['toDate'] = strtotime($data['toDate'] . ' ' . $data['toTime']);
        }
        else {
            $this->errorcode = 3;
            return $this;
        }
        unset($data['fromTime'], $data['toTime']);
        if (!$data['inputChecksum']) {
            $data['inputChecksum'] = generate_checksum();
        }
        if (is_array($data)) {
            $query = $db->insert_query(self::TABLE_NAME, $data);
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $log, $core, $errorhandler, $lang;
        if (!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        if (!is_empty($data['fromTime'], $data['fromDate'])) {
            $data['fromDate'] = strtotime($data['fromDate'] . ' ' . $data['fromTime']);
        }
        if (empty($data['fromDate'])) {
            $this->errorcode = 2;
            return $this;
        }
        if (!is_empty($data['toTime'], $data['toDate'])) {
            $data['toDate'] = strtotime($data['toDate'] . ' ' . $data['toTime']);
        }
        if (empty($data['toDate'])) {
            $this->errorcode = 3;
            return $this;
        }
        unset($data['fromTime'], $data['toTime']);
        $data['modifiedOn'] = TIME_NOW;
        $data['modifiedBy'] = $core->user['uid'];
        $data['alias'] = generate_alias($data['title']);

        if (is_array($data)) {
            $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY . '=' . intval($this->data[self::PRIMARY_KEY]));
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
        }
        return $this;
    }

    /**
     *
     * @return \Users|boolean
     */
    public function get_createdBy() {
        return new Users(intval($this->data['createdBy']));
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        if (is_array($attributes_param)) {
            foreach ($attributes_param as $attr => $val) {
                $attributes .= $attr . '="' . $val . '"';
            }
        }
        return ' <button type="button" class="btn btn-primary" ' . $attributes . ' id="openmodal_' . $this->get_id() . '" data-targetdiv="events_modal" data-url="' . $this->get_link() . '">' . $this->get_displayname() . '</button>';
    }

    public function get_link() {
        global $core;
        return $core->settings['rootdir'] . '/index.php?module=events/eventslist&action=loadevents_popup&id=' . $this->get_id();
    }

    /**
     *
     * @param type $uid
     * @return boolean
     */
    public function is_subscribed($uid) {
        if ($this->data['createdBy'] == $uid && $this->data['isPublic'] == 0 && $this->data['isActive'] == 1) {
            return true;
        }

        $assignedevents = CalendarAssignments::get_data(array('uid' => intval($uid), 'eid' => $this->get_id(), 'isActive' => 1), array('returnarray' => true));
        if (is_array($assignedevents)) {
            return true;
        }
        return false;
    }

    public function canManageEvent() {
        global $core;
        if ($core->usergroup['canManageAllEvents']) {
            return true;
        }
        elseif ($this->data['createdBy'] == $core->user['uid']) {
            return true;
        }
        return false;
    }

    public function get_editlink() {
        global $core;
        return $core->settings['rootdir'] . '/index.php?module=events/manageevent&amp;id=' . $this->data[self::PRIMARY_KEY];
    }

    public function is_past() {
        if ($this->data['toDate'] > TIME_NOW) {
            return true;
        }
        return false;
    }

    public function get_fromdate() {
        return $this->data['fromDate'];
    }

    /**
     *
     * @global type $core
     * @return type
     */
    public function get_todate() {
        global $core;
        if ($this->data['toDate']) {
            return $this->data['toDate'];
        }

        return $this->data['fromDate'] + $core->settings['lecturelength'];
    }

    public function get_subsribers() {
        $assignedevents = CalendarAssignments::get_data(array('eid' => $this->get_id(), 'isActive' => 1), array('returnarray' => true));
        if (is_array($assignedevents)) {
            foreach ($assignedevents as $assignedevent) {
                $subscribers[$assignedevent->uid] = $assignedevent->get_user();
            }
            return $subscribers;
        }
        return false;
    }

    public function get_fromdateoutput($format = 'd-m-Y') {
        return date($format, $this->get_fromdate());
    }

    public function get_fromtimeoutput($format = 'h:i A') {
        return date($format, $this->get_fromdate());
    }

    public function get_todateoutput($format = 'd-m-Y') {
        return date($format, $this->get_todate());
    }

    public function get_totimeoutput($format = 'h:i A') {
        return date($format, $this->get_todate());
    }

    /**
     *
     * @return string
     */
    public function parse_daterangeoutput() {
        $fromdate = $this->parse_fromdate();
        $todate = $this->parse_todate();
        return $fromdate . ' TO ' . $todate;
    }

    /**
     *
     * @return boolean|\Recommendations
     */
    public function get_recommendation() {
        if (!intval($this->data['rid'])) {
            return false;
        }
        $recommednation_obj = new Recommendations(intval($this->data['rid']));
        if (!is_object($recommednation_obj)) {
            return false;
        }
        return $recommednation_obj;
    }

    /**
     *
     * @return String
     */
    public function parse_fromdate() {
        $fromdate = $this->get_fromdateoutput('D, j M Y') . ' ' . $this->get_fromtimeoutput();
        $fromdate_class = 'success';
        if ($this->data['fromDate'] < TIME_NOW) {
            $fromdate_class = 'danger';
        }
        return '<span class="label label-' . $fromdate_class . '">' . $fromdate . '</span>';
    }

    /**
     *
     * @return String
     */
    public function parse_todate() {
        $todate = $this->get_todateoutput('D, j M Y') . '  ' . $this->get_totimeoutput();
        $todate_class = 'success';
        if ($this->data['toDate'] < TIME_NOW) {
            $todate_class = 'danger';
        }
        return '<span class="label label-' . $todate_class . '">' . $todate . '</span>';
    }

    /**
     *
     * @return boolean
     */
    public function get_calendarassignments() {
        $assignedstudents = CalendarAssignments::get_data(array('eid' => $this->get_id(), 'isActive' => 1), array('returnarray' => true));
        if (!is_array($assignedstudents)) {
            return false;
        }
        return $assignedstudents;
    }

    /**
     *
     * @return \Users
     */
    public function get_attendees() {
        //if event is public then get all assignemnts to this event
        if ($this->data['isPublic'] == 1) {
            $assignments = $this->get_calendarassignments();
            if (!is_array($assignments)) {
                return false;
            }
            foreach ($assignments as $assignment) {
                $user_obj = $assignment->get_user();
                $user_obj->assignmentCreatedOn = $assignment->createdOn;
                $attendees[$assignment->uid] = $user_obj;
            }
            return $attendees;
        }
        //else it is private, then only the creator is linked to the event
        else {
            $user_obj = new Users(intval($this->data['createdBy']));
            $user_obj->assignmentCreatedOn = $this->createdOn;
            return array(intval($this->data['createdBy']) => $user_obj);
        }
    }

    /**
     *
     * @global type $template
     * @global type $lang
     * @global type $core
     * @return type
     */
    public function parse_attendeessection() {
        global $template, $lang, $core;
        $attendees_objs = $this->get_attendees();
        if (!is_array($attendees_objs)) {
            return;
        }
        foreach ($attendees_objs as $attendees_obj) {
            $displayname = $attendees_obj->get_displayname();
            $assignedon = date($core->settings['dateformat'], $attendees_obj->assignmentCreatedOn);
            eval("\$attendees_rows.= \"" . $template->get('events_attendeeslist_row') . "\";");
        }
        eval("\$attendees_list= \"" . $template->get('events_attendeeslist') . "\";");
        return $attendees_list;
    }

    /**
     *
     * @global type $lang
     * @return type
     */
    public function parse_addremove_button() {
        global $lang, $core;
        if ($this->is_subscribed($core->user['uid'])) {
            return'<div id="subscribedive_' . $this->get_id() . '" ><button type="button" class="btn btn-danger" id="subscribebutton_' . $this->get_id() . '_remove"><span class="glyphicon glyphicon-minus"></span>' . $lang->removeevent . '</button>';
        }
        else {
            return '<div id="subscribedive_' . $this->get_id() . '"><button type="button" class="btn btn-primary" id="subscribebutton_' . $this->get_id() . '_subscribe"><span class="glyphicon glyphicon-plus"></span>' . $lang->addevent . '</button>';
        }
    }

    /**
     * Assign user to event
     * @global type $core
     * @return boolean
     */
    public function do_assignuser($uid = '') {
        global $core;
        if (!$uid) {
            $uid = $core->user['uid'];
        }
        $assignmentdata = array('uid' => intval($uid), 'eid' => $this->get_id(), 'isActive' => 1);
        $assignment_obj = new CalendarAssignments();
        $assignment_obj->set($assignmentdata);
        $assignment_obj->save();
        if ($assignment_obj->get_errorcode() == 0) {
            return true;
        }
        return false;
    }

    /**
     * Remove user from current event assignments
     * @param type $uid
     * @return boolean
     */
    public function do_removeuser($uid = '') {
        global $core;
        if (!$uid) {
            $uid = $core->user['uid'];
        }
        if ($this->data['createdBy'] == 1 && $this->data['isPublic'] == 0) {
            $result = $this->do_deactivate();
            return $result;
        }
        else {
            //get previous assignments
            $calendarassignments_objs = CalendarAssignments::get_data(array('uid' => intval($uid), 'eid' => $this->get_id(), 'isActive' => 1), array('returnarray' => true));
            if (!is_array($calendarassignments_objs)) {
                return true;
            }
            foreach ($calendarassignments_objs as $calendarassignments_obj) {
                $result = $calendarassignments_obj->do_deactivate();
                if ($result == false) {
                    return false;
                }
            }
        }
        return true;
    }

}
