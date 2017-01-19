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
    $id = $course_obj->get_id();
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
    //parse course take/remove button
    if ($core->usergroup['canTakeLessons'] == 1) {
        if ($course_obj->is_subscribed($core->user['uid'])) {
            $addorremovecourse_button = '<div id="subscribedive_' . $id . '" ><button type="button" class="btn btn-danger" id="subscribebutton_' . $id . '_remove"><span class="glyphicon glyphicon-minus"></span>' . $lang->removecourse . '</button></div>';
        }
        else {
            $addorremovecourse_button = '<div id="subscribedive_' . $id . '"><button type="button" class="btn btn-primary" id="subscribebutton_' . $id . '_subscribe"><span class="glyphicon glyphicon-plus"></span>' . $lang->addcourse . '</button></div>';
        }
    }

    //parse course lectures based on user permission
    $lecture_section = $course_obj->get_lectureoutput();
    //parse folder link
    if ($course_obj->folderUrl) {
        $course_folder = '<button type="button" class="btn btn-warning"  onclick="window.open(\'' . $course_obj->folderUrl . '\', \'_blank\')">' . $lang->coursefiles . '</button>';
    }
    eval("\$page= \"" . $template->get('courses_courseprofile') . "\";");
    output_page($page, array('pagetitledirect' => $course_displayname));
}
else {
    if ($core->input['action'] == 'course_subscribe') {
        if (!$core->input['id']) {
            echo('<span style="color:red">' . $lang->error . '</span>');
            exit;
        }
        $id = intval($core->input['id']);
        $course_obj = new Courses($id);
        $assignedcourse_data = array('cid' => $id, 'uid' => $core->user['uid']);
        $assignedcourse_obj = new AssignedCourses();
        $assignedcourse_obj->set($assignedcourse_data);
        $assignedcourse_obj->save();
        if ($assignedcourse_obj->get_errorcode() == 0) {
            $output = '<div id="subscribedive_' . $id . '"><button type="button" class="btn btn-danger" id="subscribebutton_' . $id . '_remove"><span class="glyphicon glyphicon-minus"></span>' . $lang->removecourse . '</button>';
        }
        else {
            $output = '<div id="subscribedive_' . $id . '"><button type="button" class="btn btn-primary" id="subscribebutton_' . $id . '_subscribe"><span class="glyphicon glyphicon-plus"></span>' . $lang->addcourse . '</button>';
        }
        echo($output);
        exit;
    }
    elseif ($core->input['action'] == 'course_remove') {
        if (!$core->input['id']) {
            echo('<span style="color:red">' . $lang->error . '</span>');
            exit;
        }
        $id = intval($core->input['id']);
        $course_obj = new Courses($id);
        $assignedcourses = AssignedCourses::get_data(array('uid' => $core->user['uid'], 'cid' => $id), array('returnarray' => true));
        if (is_array($assignedcourses)) {
            foreach ($assignedcourses as $assignedcourse) {
                $assignedcourse->delete();
            }
            $output = '<div id="subscribedive_' . $id . '"><button type="button" class="btn btn-primary" id="subscribebutton_' . $id . '_subscribe"><span class="glyphicon glyphicon-plus"></span>' . $lang->addcourse . '</button></div>';
        }
        else {
            $output = '<div id="subscribedive_' . $id . '"><button type="button" class="btn btn-danger" id="subscribebutton_' . $id . '_remove"><span class="glyphicon glyphicon-minus"></span>' . $lang->removecourse . '</button></div>';
        }
        echo($output);
        exit;
    }
}