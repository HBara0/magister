<?php

if (!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if ($core->usergroup['canAccessSystem'] == 0) {
    error($lang->sectionnopermission);
}
if (!isset($core->input['action'])) {

    if (!isset($core->input['id']) || empty($core->input['id'])) {
        redirect(DOMAIN . '/index.php?module=courses/courses');
    }
    $course_obj = new Courses(intval($core->input['id']));
    $course = $course_obj->get();

    $hide_managecoursebutton = ' style="display:none"';
    $teacheroutput = $course_obj->get_teacheroutput();
    //show manage course button depending on user permission
    if ($course_obj->canManageCourse()) {
        $editlink = $course_obj->get_editlink();
        $hide_managecoursebutton = '';
    }
    $course_displayname = $course_obj->get_displayname();

    if (!$course['description']) {
        $hide_coursedescription = 'style="display:none"';
    }

    //parse course lectures based on user permission
    $lecture_section = $course_obj->get_lectureoutput();

    eval("\$page= \"" . $template->get('courses_courseprofile') . "\";");
    output_page($page, array('pagetitledirect' => $course_displayname));
}