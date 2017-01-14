<?php

/**
 * Description of Courses
 *
 * @author H.B
 */
class Courses extends AbstractClass {

    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'cid';
    const TABLE_NAME = 'courses';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const REQUIRED_ATTRS = 'code,title';
    const UNIQUE_ATTRS = 'alias,code';

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
        $subscriptions = $data['assignstudent'];
        unset($data['assignstudent']);
        if (is_array($data)) {
            $query = $db->insert_query(self::TABLE_NAME, $data);
            $this->{static::PRIMARY_KEY} = $db->last_id();

            if ($query) {
                if (is_array($subscriptions)) {
                    $assigncourse['cid'] = $this->get_id();
                    $assigncourse['isActive'] = 1;
                    foreach ($subscriptions as $uid) {
                        $assigncourse['uid'] = intval($uid);
                        $assignecourse_obj = new AssignedCourses();
                        $assignecourse_obj->set($assigncourse);
                        $assignecourse_obj->save();
                    }
                }
            }
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
        $subscriptions = $data['assignstudent'];
        unset($data['assignstudent']);
        if (is_array($data)) {
            $query = $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY . '=' . intval($this->data[self::PRIMARY_KEY]));
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            if ($query) {
                //set all former assignments to disavtive
                $previousassignement = AssignedCourses::removeassignment($this->get_id());
                if (is_array($subscriptions)) {
                    $assigncourse['cid'] = $this->get_id();
                    $assigncourse['isActive'] = 1;
                    foreach ($subscriptions as $uid) {
                        $assigncourse['uid'] = intval($uid);
                        $assignecourse_obj = new AssignedCourses();
                        $assignecourse_obj->set($assigncourse);
                        $assignecourse_obj->save();
                    }
                }
            }
        }
        return $this;
    }

    /**
     * Return course teacher as users object or false if not exist
     * @return boolean|\Users
     */
    public function get_teacher() {
        if (!$this->data['teacherId']) {
            return false;
        }
        return new Users(intval($this->data['teacherId']));
    }

    public function get_displayname() {
        return $this->data['code'] . ' - ' . parent::get_displayname();
    }

    /**
     * Get assigned lectures to course
     * @return boolean/Lectures array
     */
    public function get_lectures() {
        $lectures_objs = Lectures::get_data(array('isActive' => 1, 'cid' => $this->get_id()), array('returnarray' => true, 'simple' => false));
        if (is_array($lectures_objs)) {
            return $lectures_objs;
        }
        return false;
    }

    /**
     * Get assigned deadlines to course
     * @return boolean/Deadlines array
     */
    public function get_deadlines() {
        $deadlines_objs = Deadlines::get_data(array('isActive' => 1, 'cid' => $this->get_id()), array('returnarray' => true, 'simple' => false));
        if (is_array($deadlines_objs)) {
            return $deadlines_objs;
        }
        return false;
    }

    /**
     *
     * @param type $uid
     * @return boolean
     */
    public function is_subscribed($uid) {
        $assignedcourses = AssignedCourses::get_data(array('uid' => $uid, 'cid' => $this->get_id()), array('returnarray' => true));
        if (is_array($assignedcourses)) {
            return true;
        }
        return false;
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {

        if (is_array($attributes_param)) {
            foreach ($attributes_param as $attr => $val) {
                $attributes .= $attr . '="' . $val . '"';
            }
        }
        return '<a href="' . $this->get_link() . '" ' . $attributes . '>' . $this->get_displayname() . '</a>';
    }

    public function get_link() {
        global $core;
        return $core->settings['rootdir'] . '/index.php?module=courses/courseprofile&amp;pid=' . $this->data[self::PRIMARY_KEY];
    }

}
