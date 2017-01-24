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
        if (!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        $data['createdOn'] = TIME_NOW;
        $data['createdBy'] = $core->user['uid'];
        if (!$data['cid']) {
            $data['uid'] = $core->user['uid'];
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

        $data['modifiedOn'] = TIME_NOW;
        $data['modifiedBy'] = $core->user['uid'];
        if (!$data['cid']) {
            $data['uid'] = $core->user['uid'];
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

}
