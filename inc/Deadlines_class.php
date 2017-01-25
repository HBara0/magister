<?php

/**
 * Description of Deadlines
 *
 * @author H.B
 */
class Deadlines extends AbstractClass {

    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'did';
    const TABLE_NAME = 'deadlines';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const REQUIRED_ATTRS = 'time,title';
    const UNIQUE_ATTRS = 'time,title,uid,cid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $log, $core, $errorhandler, $lang;

        $data['createdOn'] = TIME_NOW;
        $data['createdBy'] = $core->user['uid'];
        if (!$data['cid']) {
            $data['uid'] = $core->user['uid'];
        }
        if (!$data['time']) {
            $data['time'] = strtotime($data['fromDate'] . ' ' . $data['fromTime']);
            unset($data['fromDate'], $data['fromTime']);
        }
        if (!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        if (!$deadline['inputChecksum']) {
            $deadline['inputChecksum'] = generate_checksum();
        }
        if (is_array($data)) {
            $query = $db->insert_query(self::TABLE_NAME, $data);
            $this->{static::PRIMARY_KEY} = $db->last_id();
            if ($query) {
                //assign user to event
                $assigndata = array('uid' => $core->user['uid'], 'did' => $this->get_id());
                $assignobj = new CalendarAssignments();
                $assignobj->set($assigndata);
                $assignobj->save();
            }
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $log, $core, $errorhandler, $lang;
        $data['modifiedOn'] = TIME_NOW;
        $data['modifiedBy'] = $core->user['uid'];
        if (!$data['cid']) {
            $data['uid'] = $core->user['uid'];
        }
        if (!$data['time']) {
            $data['time'] = strtotime($data['fromDate'] . ' ' . $data['fromTime']);
            unset($data['fromDate'], $data['fromTime']);
        }
        if (!$deadline['inputChecksum']) {
            $deadline['inputChecksum'] = generate_checksum();
        }
        if (!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        if (is_array($data)) {
            $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY . '=' . intval($this->data[self::PRIMARY_KEY]));
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
        }
        return $this;
    }

    /**
     *
     * @return \Courses|boolean
     */
    public function get_course() {
        if (!$this->data['cid']) {
            return false;
        }
        return new Courses(intval($this->data['cid']));
    }

    /**
     *
     * @return boolean|\Users
     */
    public function get_user() {
        if (!$this->data['uid']) {
            return false;
        }
        return new Users(intval($this->data['uid']));
    }

    /**
     *
     * @global type $core
     * @return string
     */
    public function get_displayname() {
        global $core;
        $time_output = date($core->settings['dateformat'] . ' ' . $core->settings['timeformat'], $this->data['time']);
        $displayname = $time_output;
        if ($this->data['title']) {
            $displayname = $this->data['title'] . ' ' . $time_output;
        }
        elseif ($this->data['cid']) {
            $course_obj = $this->get_course();
            if (is_object($course_obj)) {
                $displayname = "Deadline: " . $course_obj->get_displayname() . ' ' . $time_output;
            }
        }
        return $displayname;
    }

    /**
     *
     * @return timestamp
     */
    public function get_totime() {
        return ($this->data['time'] + 10);
    }

    /**
     *
     * @return timestamp
     */
    public function get_fromtime() {
        return $this->data['time'];
    }

    public function get_fromdateoutput($format = 'd-m-Y') {
        return date($format, $this->get_fromtime());
    }

    public function get_fromtimeoutput($format = 'h:i A') {
        return date($format, $this->get_fromtime());
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
        $assignmentdata = array('uid' => intval($uid), 'rid' => $this->get_id(), 'isActive' => 1);
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
        //get previous assignments
        $calendarassignments_objs = CalendarAssignments::get_data(array('uid' => intval($uid), 'rid' => $this->get_id(), 'isActive' => 1), array('returnarray' => true));
        if (!is_array($calendarassignments_objs)) {
            return true;
        }
        foreach ($calendarassignments_objs as $calendarassignments_obj) {
            $result = $calendarassignments_obj->do_deactivate();
            if ($result == false) {
                return false;
            }
        }
        return true;
    }

    /**
     *
     * @global type $lang
     * @global type $core
     * @global type $template
     * @return type
     */
    public function parse_popup($div) {
        global $lang, $core, $template;
        $deadline = $this->get();
        $deadline['time_output'] = parse_compared_date($this->data['time']);
        $course_obj = $this->get_course();
        if (is_object($course_obj)) {
            $course_output = $course_obj->get_displayname();
        }
        if (!$deadline['description']) {
            $display_description = 'style="display:none"';
        }
        //parse course take/remove button
        $manageevent_button = $this->parse_manage_button($div);

        eval("\$modal = \"" . $template->get('modal_deadline') . "\";");
        return $modal;
    }

    /**
     * 
     * @global type $core
     * @return boolean
     */
    public function canManageDeadline() {
        global $core;
        //user can manage deadline if he can manage the course of the deadline OR if he created the deadline
        if ($this->data['cid']) {
            $course_obj = new Courses(intval($this->data['cid']));
            return $course_obj->canManageCourse();
        }
        elseif ($this->data['createdBy'] == $core->user['uid']) {
            return true;
        }
        else {
            return false;
        }
    }

    /**
     *
     * @global type $core
     * @return type
     */
    public function get_editlink() {
        global $core;
        return $core->settings['rootdir'] . '/index.php?module=portal/calendar&action=loadpopup_managedeadline&amp;id=' . $this->data[self::PRIMARY_KEY];
    }

    /**
     *
     * @global type $lang
     * @param type $div
     * @return type
     */
    public function parse_manage_button($div) {
        global $lang;
        if (!$this->canManageDeadline()) {
            return;
        }
        return ' <button type="button" class="btn btn-primary"  id="openmodal_' . $this->get_id() . '" data-targetdiv="' . $div . '" data-url="' . $this->get_editlink() . '">' . $lang->manage . '</button>';
    }

}
