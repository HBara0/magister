<?php

if (!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if ($core->usergroup['canAccessSystem'] == 0) {
    error($lang->sectionnopermission);
}
if (!isset($core->input['action'])) {

    //show create course button depending on user permission
    if ($core->usergroup['can_CreateCourse'] == 0) {
        $hide_createcoursebutton = ' style="display:none"';
    }
//get active courses list
    $courses_objs = Courses::get_data(array('isActive' => 1), array('returnarray' => true, 'simple' => false));
    if (is_array($courses_objs)) {
        foreach ($courses_objs as $course_obj) {
            $course = $course_obj->get();
            $course_link = $course_obj->parse_link();
            $teacher_obj = $course_obj->get_teacher();
            if (is_object($teacher_obj)) {
                $teachername = $teacher_obj->get_displayname();
            }
            else {
                $teachername = 'N/A';
            }
            if ($course_obj->description) {
                $description = $course_obj->description;
            }
            else {
                $description = 'N/A';
            }
            if ($course_obj->is_subscribed($core->user['uid'])) {
                $subscribe_cell = 'data-sort="1"';
                $subscribed = '<span style="color:green;font-weight:bold">Yes <span class="glyphicon glyphicon-ok"></span></span>';
            }
            else {
                $subscribe_cell = 'data-sort="0"';
                $subscribed = '<span style="color:red;font-weight:bold"">No <span class="glyphicon glyphicon-remove"></span></span>';
            }

            $tool_items = ' <li><a target="_blank" href="' . $course_obj->get_link() . '"><span class="glyphicon glyphicon-eye-open"></span>&nbsp' . $lang->viewcourse . '</a></li>';
            if ($course_obj->canManageCourse()) {
                $tool_items .= ' <li><a target="_blank" href="' . $course_obj->get_editlink() . '"><span class="glyphicon glyphicon-pencil"></span>&nbsp' . $lang->managecourse . '</a></li>';
            }
            eval("\$tools = \"" . $template->get('tools_buttonselectlist') . "\";");
            eval("\$courses_list .= \"" . $template->get('courses_courselist_courserow') . "\";");
            unset($tool_items, $subscribe_cell);
        }
    }
    eval("\$page= \"" . $template->get('courses_courselist') . "\";");
    output_page($page);
}