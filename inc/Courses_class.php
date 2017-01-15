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
        return $core->settings['rootdir'] . '/index.php?module=courses/courseprofile&amp;id=' . $this->data[self::PRIMARY_KEY];
    }

    /**
     * check if current user can manage course
     * @global type $core
     * @return boolean
     */
    public function canManageCourse() {
        global $core;
        if ($core->usergroup['canManageAllCourses']) {
            return true;
        }
        elseif ($this->data['teacherId'] == $core->user['uid']) {
            return true;
        }
        return false;
    }

    /**
     *
     * @global type $core
     * @return type
     */
    public function get_editlink() {
        global $core;
        return $core->settings['rootdir'] . '/index.php?module=courses/managecourse&amp;id=' . $this->data[self::PRIMARY_KEY];
    }

    public function get_teacheroutput() {
        $teacher_obj = $this->get_teacher();
        if (!is_object($teacher_obj)) {
            return;
        }
        return $teacher_obj->get_displayname();
    }

    public function get_lectureoutput() {
        global $template, $lang, $core;
        $course_lectures = $this->get_lectures();
        if (!is_array($course_lectures)) {
            return;
        }
        foreach ($course_lectures as $lecture_obj) {
            $fromtime = $lecture_obj->get_fromdate();
            $totime = $lecture_obj->get_todate();

            $fromdate = date($core->settings['dateformat'] . ' ' . $core->settings[timeformat], $fromtime);
            $todate = date($core->settings['dateformat'] . ' ' . $core->settings[timeformat], $totime);

            $title_output = 'N/A';
            if ($lecture_obj->title) {
                $title_output = $lecture_obj->title;
            }
            $location_output = 'N/A';
            if ($lecture_obj->location) {
                $location_output = $lecture_obj->location;
            }
            $type_output = $lang->lecture;
            //parse tools depending on user permission
            if ($this->canManageCourse()) {
//            $tool_items = ' <li><a target="_blank" href="' . $course_obj->get_link() . '"><span class="glyphicon glyphicon-eye-open"></span>&nbsp' . $lang->viewcourse . '</a></li>';
//            if ($course_obj->canManageCourse()) {
//                $tool_items .= ' <li><a target="_blank" href="' . $course_obj->get_editlink() . '"><span class="glyphicon glyphicon-pencil"></span>&nbsp' . $lang->managecourse . '</a></li>';
//            }
                eval("\$tools = \"" . $template->get('tools_buttonselectlist') . "\";");
            }

            eval("\$lecutre_rows.= \"" . $template->get('lecturesection_table_row') . "\";");
            unset($tools);
        }

        //parse deadlines
        $deadline_objs = Deadlines::get_data(array('cid' => $this->get_id(), 'isActive' => 1), array('returnarray' => true));
        if (is_array($deadline_objs)) {
            foreach ($deadline_objs as $deadline_obj) {
                if ($this->canManageCourse()) {
                    eval("\$tools = \"" . $template->get('tools_buttonselectlist') . "\";");
                }
                $fromtime = $deadline_obj->get_fromdate();
                $totime = $deadline_obj->get_todate();

                $fromdate = date($core->settings['dateformat'] . ' ' . $core->settings[timeformat], $fromtime);
                $todate = date($core->settings['dateformat'] . ' ' . $core->settings[timeformat], $totime);

                $title_output = 'N/A';
                if ($deadline_obj->title) {
                    $title_output = $deadline_obj->title;
                }
                $location_output = 'N/A';
                if ($deadline_obj->location) {
                    $location_output = $deadline_obj->location;
                }
                $type_output = $lang->deadline;

                eval("\$lecutre_rows.= \"" . $template->get('lecturesection_table_row') . "\";");
                unset($tools);
            }
        }
        eval("\$lecutre_section_table= \"" . $template->get('lecturesection_table') . "\";");

        eval("\$lecutre_section= \"" . $template->get('courses_courseprofile_lecturesection') . "\";");
        return $lecutre_section;
    }

    /**
     * return thetotal number of active students
     * @return int
     */
    public function get_totalstudents() {
        $assignedstudents = AssignedCourses::get_data(array('isActive' => 1, 'cid' => $this->get_id()), array('returnarray' => true));
        if (is_array($assignedstudents)) {
            return count($assignedstudents);
        }
        return 0;
    }

}
