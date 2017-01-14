<?php

/**
 * Description of Lectures
 *
 * @author H.B
 */
class Lectures extends AbstractClass {

    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'lid';
    const TABLE_NAME = 'lectures';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const REQUIRED_ATTRS = 'cid,fromTime';
    const UNIQUE_ATTRS = 'cid,fromTime';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $log, $core, $errorhandler, $lang;
        //session time
        $sessiontime = 1800;
        if (!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        if (!$data['toTime']) {
            $data['toTime'] = $data['fromTime'] + $sessiontime;
        }
        $data['createdOn'] = TIME_NOW;
        $data['createdBy'] = $core->user['uid'];
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
        if (!$data['toTime']) {
            $data['toTime'] = $data['fromTime'] + $sessiontime;
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
     * Return lecture course teacher as users object or false if not exist
     * @return boolean|\Users
     */
    public function get_teacher() {
        $course_obj = $this->get_course();
        if (!is_object($course_obj)) {
            return false;
        }
        if (!$course_obj['teacherId']) {
            return false;
        }
        return new Users(intval($course_obj['teacherId']));
    }

    /**
     *
     * @return type
     */
    public function get_displayname() {
        $course_name = '';
        //get lecture course name
        $course_obj = $this->get_course();
        if (is_object($course_obj)) {
            $course_name = ' - ' . $course_obj->get_displayname();
        }
        if ($this->data['title']) {
            return $this->data['title'] . $course_name;
        }
        return 'Lecture ' . $course_name;
    }

    /**
     *
     * @global type $errorhandler
     * @param type $data
     * @return boolean
     */
    public function validate_requiredfields($data) {
        global $errorhandler;
        $required_fields = self::REQUIRED_ATTRS;
        if (!empty($required_fields)) {
            $required_fields = explode(',', $required_fields);
            if (is_array($required_fields) && is_array($data)) {
                foreach ($required_fields as $field) {
                    if (!isset($data[$field]) || empty($data[$field])) {
                        $errorhandler->record('Required fields', $field);
                        return false;
                    }
                }
            }
        }
        if ($data['fromTime'] > $data['toTime']) {
            $errorhandler->record('Wrong dates', $field);
            return false;
        }

        return true;
    }

    public function get_fromdate() {
        return $this->data['fromTime'];
    }

    /**
     *
     * @global type $core
     * @return type
     */
    public function get_todate() {
        global $core;
        if ($this->data['toTime']) {
            return $this->data['toTime'];
        }

        return $this->data['fromTime'] + $core->settings['lecturelength'];
    }

}
