<?php

if ($core->usergroup['can_CreateCourse'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if (!$core->input['action']) {
    if ($core->usergroup['can_CreateCourse'] == 0) {
        $hide_createcoursebutton = ' style="display:none"';
    }
    $course_obj = new Courses();
    if (isset($core->input['id']) && !empty($core->input['id'])) {
        $course_obj = new Courses(intval($core->input['id']));
        $course = $course_obj->get();
        $teacherid = $course['teacherId'];
        $hide_createcoursebutton = '';
        $isActive = $course['isActive'];

        //get all subscribed students
        $subscribedstudent_objs = AssignedCourses::get_data(array('isActive' => 1, 'cid' => $core->input['id']), array('returnarray' => true));
        if (is_array($subscribedstudent_objs)) {
            foreach ($subscribedstudent_objs as $subscribedstudent_obj) {
                $subscribedstudents_ids[] = $subscribedstudent_obj->uid;
            }
        }
    }
    $isactive_list = parse_selectlist2('course[isActive]', 1, array('1' => 'Yes', '0' => 'No'), $isActive);
    $teacher_objs = Users::get_teachers();
    if (is_array($teacher_objs)) {
        $teacher_list = parse_selectlist2('course[teacherId][]', 1, $teacher_objs, $teacherid, 1, '', array('id' => 'teacher', 'blankstart' => true));
    }
    //parse student subscription section
    $student_objs = Users::get_students();
    if (is_array($student_objs)) {
        foreach ($student_objs as $student_obj) {
            if (!$student_obj->isActive()) {
                continue;
            }
            $check_assign = '';
            if (is_array($subscribedstudents_ids) && in_array($student_obj->get_id(), $subscribedstudents_ids)) {
                $check_assign = 'checked';
            }
            $studentid = $student_obj->get_id();
            $studentoutput = $student_obj->get_displayname();
            eval("\$studentsection_lines.= \"" . $template->get('courses_managecourse_studentsubscription_line') . "\";");
        }
        eval("\$studentsubscription_section= \"" . $template->get('courses_managecourse_studentsubscription') . "\";");
    }

    //parse lecture and deadline section
    $lecture_section = $course_obj->get_lectureoutput();

    eval("\$managecourse= \"" . $template->get('courses_managecourse') . "\";");
    output_page($managecourse);
}
elseif ($core->input['action'] == 'do_perform_managecourse') {
    $course_obj = new Courses();
    $core->input['course']['alias'] = generate_alias($core->input['course']['title']);
    $course_obj->set($core->input['course']);
    $course_obj = $course_obj->save();
    switch ($course_obj->get_errorcode()) {
        case 0:
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
            break;
        case 1:
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            break;
        default:
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
            break;
    }
}
?>