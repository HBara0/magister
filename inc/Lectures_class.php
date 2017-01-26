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
        if (!$data['toTime']) {
            $data['toTime'] = strtotime($data['toDate'] . ' ' . $data['toTime']);
            unset($data['toDate']);
        }
        if (!$data['fromTime']) {
            $data['fromTime'] = strtotime($data['fromDate'] . ' ' . $data['fromTime']);
            unset($data['fromDate']);
        }
        $data['toTime'] = $data['fromTime'] + $sessiontime;
        $data['createdOn'] = TIME_NOW;
        $data['createdBy'] = $core->user['uid'];
        if (!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        if (is_array($data)) {
            $query = $db->insert_query(self::TABLE_NAME, $data);
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $log, $core, $errorhandler, $lang;
        if (!$data['toTime']) {
            $data['toTime'] = strtotime($data['toDate'] . ' ' . $data['toTime']);
            unset($data['toDate']);
        }

        if (!$data['fromTime']) {
            $data['fromTime'] = strtotime($data['fromDate'] . ' ' . $data['fromTime']);
            unset($data['fromDate']);
        }

        $data['modifiedOn'] = TIME_NOW;
        $data['modifiedBy'] = $core->user['uid'];
        if (!$data['toTime']) {
            $data['toTime'] = $data['fromTime'] + $sessiontime;
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

    public function get_fromtime() {
        return $this->data['fromTime'];
    }

    /**
     *
     * @global type $core
     * @return type
     */
    public function get_totime() {
        global $core;
        if ($this->data['toTime']) {
            return $this->data['toTime'];
        }

        return $this->data['fromTime'] + $core->settings['lecturelength'];
    }

    public function get_fromdateoutput($format = 'd-m-Y') {
        return date($format, $this->get_fromtime());
    }

    public function get_fromtimeoutput($format = 'h:i A') {
        return date($format, $this->get_fromtime());
    }

    public function get_todateoutput($format = 'd-m-Y') {
        return date($format, $this->get_totime());
    }

    public function get_totimeoutput($format = 'h:i A') {
        return date($format, $this->get_totime());
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
        $lecture = $this->get();
        $lecture['fromtime_output'] = parse_compared_date($this->data['fromTime']);
        $lecture['totime_output'] = parse_compared_date($this->data['toTime']);

        $course_obj = $this->get_course();
        if (is_object($course_obj)) {
            $course_output = $course_obj->get_displayname();
        }
        //parse course take/remove button
        $manageevent_button = $this->parse_manage_button($div);

        eval("\$modal = \"" . $template->get('modal_lecture') . "\";");
        return $modal;
    }

    /**
     *
     * @global type $core
     * @return type
     */
    public function get_editlink() {
        global $core;
        return $core->settings['rootdir'] . '/index.php?module=portal/calendar&action=loadpopup_managelecture&amp;id=' . $this->data[self::PRIMARY_KEY];
    }

    /**
     *
     * @global type $lang
     * @param type $div
     * @return type
     */
    public function parse_manage_button($div) {
        global $lang;
        if (!$this->canManageLecture()) {
            return;
        }
        return ' <button type="button" class="btn btn-primary"  id="openmodal_' . $this->get_id() . '" data-targetdiv="' . $div . '" data-url="' . $this->get_editlink() . '">' . $lang->manage . '</button>';
    }

    /**
     *
     * @global type $core
     * @return boolean
     */
    public function canManageLecture() {
        global $core;
        $course_obj = $this->get_course();
        if (is_object($course_obj)) {
            return $course_obj->canManageCourse();
        }
        elseif ($this->data['createdBy'] == $core->user['uid']) {
            return true;
        }
        else {
            return false;
        }
    }

}
