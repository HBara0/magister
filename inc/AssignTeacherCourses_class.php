<?php

/**
 * Description of AssignTeacherCourses
 *
 * @author H.B
 */
class AssignTeacherCourses extends AbstractClass {

    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'atcid';
    const TABLE_NAME = 'assign_teachercourse';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const REQUIRED_ATTRS = 'cid,uid';
    const UNIQUE_ATTRS = 'cid,uid';

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
        if (is_array($data)) {
            $query = $db->insert_query(self::TABLE_NAME, $data);
            $this->{static::PRIMARY_KEY} = $db->last_id();
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
        if (is_array($data)) {
            $query = $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY . '=' . intval($this->data[self::PRIMARY_KEY]));
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
        }
        return $this;
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
     * @return \Courses|boolean
     */
    public function get_course() {
        if (!intval($this->data['cid'])) {
            return false;
        }
        return new Courses(intval($this->data['cid']));
    }

    /**
     *
     * @param type $courseid
     * @return boolean
     */
    public function removeassignment($courseid) {
        $assignedcourse_objs = self::get_data(array('cid' => intval($courseid)), array('returnarray' => true));
        if (!is_array($assignedcourse_objs)) {
            return true;
        }
        foreach ($assignedcourse_objs as $assignedcourse_obj) {
            $assignedcourse_obj->deactivate();
        }
        return true;
    }

    /**
     * 
     */
    public function deactivate() {
        $currentdata = $this->get();
        $currentdata['isActive'] = 0;
        $newobject = new self();
        $newobject->set($currentdata);
        $newobject->save();
    }

}
