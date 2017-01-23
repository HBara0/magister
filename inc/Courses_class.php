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

        if (is_array($data['teacherId'])) {
            $teachers = $data['teacherId'];
        }
        unset($data['teacherId']);

        if (is_array($data['program'])) {
            $programs = $data['program'];
        }
        unset($data['program']);

        if (is_array($data)) {
            $query = $db->insert_query(self::TABLE_NAME, $data);
            $this->{static::PRIMARY_KEY} = $db->last_id();

            if ($query) {
//add subscribptions
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

//add teachers
                if (is_array($teachers)) {
                    $assigncourses_array = array('cid' => $this->get_id());
                    foreach ($teachers as $teacherid) {
                        $assigncourses_array['uid'] = intval($teacherid);
                        $assignecourse_obj = new AssignTeacherCourses();
                        $assignecourse_obj->set($assigncourses_array);
                        $assignecourse_obj->save();
                    }
                }

//assign course to programs
                if (is_array($programs)) {
                    foreach ($programs as $progid) {
                        $assignprograms_array = array('isActive' => 1, 'cid' => intval($this->get_id()), 'progid' => intval($progid));
                        $assignprogram_obj = new AssignedProgramCourse();
                        $assignprogram_obj->set($assignprograms_array);
                        $assignprogram_obj->save();
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
        if (is_array($data['teacherId'])) {
            $teachers = $data['teacherId'];
        }
        unset($data['teacherId']);

        if (is_array($data['program'])) {
            $programs = $data['program'];
        }
        unset($data['program']);

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

                $previousteacherassignment = AssignTeacherCourses::removeassignment($this->get_id());
//manage teachers
                if (is_array($teachers)) {
                    $assigncourses_array = array('cid' => $this->get_id());
                    foreach ($teachers as $teacherid) {
                        $assigncourses_array['uid'] = intval($teacherid);
                        $assignecourse_obj = new AssignTeacherCourses();
                        $assignecourse_obj->set($assigncourses_array);
                        $assignecourse_obj->save();
                    }
                }

                $this->deactivate_assignedprograms();
//assign course to programs
                if (is_array($programs)) {
                    foreach ($programs as $progid) {
                        $assignprograms_array = array('isActive' => 1, 'cid' => intval($this->get_id()), 'progid' => intval($progid));
                        $assignprogram_obj = new AssignedProgramCourse();
                        $assignprogram_obj->set($assignprograms_array);
                        $assignprogram_obj->save();
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
    public function get_teachers() {
        $courseteachers_assignment = AssignTeacherCourses::get_data(array('cid' => $this->get_id()), array('returnarray' => TRUE));
        if (!is_array($courseteachers_assignment)) {
            return false;
        }
        $teacherobj_arrays = array();
        foreach ($courseteachers_assignment as $assign_teachercourse_obj) {
            $teacherobj_arrays[$assign_teachercourse_obj->uid] = $assign_teachercourse_obj->get_user();
        }
        return $teacherobj_arrays;
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

    /**
     *
     * @global type $core
     * @return type
     */
    public function get_link() {
        global $core;
        return $core->settings['rootdir'] . '/index.php?module=courses/courses&amp;action=loadcourses_popup&amp;id=' . $this->data[self::PRIMARY_KEY];
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

    /**
     *
     * @return type
     */
    public function get_teacheroutput() {
        $teachers_array = $this->get_teachers();
        if (!is_array($teachers_array)) {
            return;
        }
        $teachers_outputs = array();
        foreach ($teachers_array as $teacher_obj) {
            $teachers_outputs[] = $teacher_obj->get_displayname();
        }
        return implode(', ', $teachers_outputs);
    }

    /**
     * Parse lecture panel section
     * @global type $template
     * @global type $lang
     * @global type $core
     * @return type
     */
    public function get_lectureoutput() {
        global $template, $lang, $core;
        $toolsclass = 'never';
        $manage = false;
        if ($this->canManageCourse()) {
            $toolsclass = 'all';
            $manage = true;
            $createlecture_button = '<button type="button" class="btn btn-primary" id="openmodal_courses" data-targetdiv="courses_modal" data-url="' . $core->settings['rootdir'] . '/index.php?module=courses/courses&action=get_managelecturedeadlines&id=new&courseid=' . $this->get_id() . '">' . $lang->create . '</button>';
        }
        $course_lectures = $this->get_lectures();
        if (is_array($course_lectures)) {
            $rowclass = 'lecture_row';
            foreach ($course_lectures as $lecture_obj) {
                $fromtime = $lecture_obj->get_fromdate();
                $totime = $lecture_obj->get_todate();

                $fromdate = date($core->settings['dateformat'] . ' ' . $core->settings[timeformat], $fromtime);
                $todate = date($core->settings['dateformat'] . ' ' . $core->settings[timeformat], $totime);
                $dateoutput = $fromdate . ' ' . $lang->to . ' ' . $todate;
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
                if ($manage) {
                    $tools = '<button type="button" class="btn btn-warning" id="openmodal_courses" data-targetdiv="courses_modal" data-url="' . $core->settings['rootdir'] . '/index.php?module=courses/courses&action=get_managelecturedeadlines&type=lecture&id=' . $lecture_obj->get_id() . '&courseid=' . $this->get_id() . '">' . $lang->manage . '</button>';
                }
                eval("\$lecutre_rows.= \"" . $template->get('lecturesection_table_row') . "\";");
                unset($tools);
            }
        }

//parse deadlines
        $deadline_objs = Deadlines::get_data(array('cid' => $this->get_id(), 'isActive' => 1), array('returnarray' => true));
        if (is_array($deadline_objs)) {
            $rowclass = 'deadline_row';
            foreach ($deadline_objs as $deadline_obj) {
                $fromtime = $deadline_obj->get_fromdate();
                $totime = $deadline_obj->get_todate();

                $fromdate = date($core->settings['dateformat'] . ' ' . $core->settings[timeformat], $fromtime);
                $todate = date($core->settings['dateformat'] . ' ' . $core->settings[timeformat], $totime);
                $dateoutput = $fromdate;

                $title_output = 'N/A';
                if ($deadline_obj->title) {
                    $title_output = $deadline_obj->title;
                }
                $location_output = 'N/A';
                if ($deadline_obj->location) {
                    $location_output = $deadline_obj->location;
                }
                $type_output = $lang->deadline;

                //parse tools depending on user permission
                if ($manage) {
                    $tools = '<button type="button" class="btn btn-warning" id="openmodal_courses" data-targetdiv="courses_modal" data-url="' . $core->settings['rootdir'] . '/index.php?module=courses/courses&action=get_managelecturedeadlines&type=deadline&id=' . $deadline_obj->get_id() . '&courseid=' . $this->get_id() . '">' . $lang->manage . '</button>';
                }
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

    /**
     *
     * @return boolean
     */
    public function get_assignedprograms() {
        $assigneprogs = AssignedProgramCourse::get_data(array('isActive' => 1, 'cid' => $this->get_id()), array('returnarray' => 1));
        if (!is_array($assigneprogs)) {
            return false;
        }
        return $assigneprogs;
    }

    public function deactivate_assignedprograms() {
        $assignedprograms_objs = $this->get_assignedprograms();
        if (!is_array($assignedprograms_objs)) {
            return true;
        }
        foreach ($assignedprograms_objs as $assignedprograms_obj) {
            $assignedprograms_obj->do_deactivate();
        }
    }

}
