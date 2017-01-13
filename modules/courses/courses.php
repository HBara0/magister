<?php

if (!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if ($core->usergroup['canAccessSystem'] == 0) {
    error($lang->sectionnopermission);
}
if (!isset($core->input['action'])) {

    //get active courses list
    $courses_objs = Courses::get_data(array('isActive' => 1), array('returnarray' => true, 'simple' => false));
    if (is_array($courses_objs)) {
        foreach ($courses_objs as $course_obj) {
            $courses_list .= '<tr>';
            $courses_list .= '<td>' . $course_obj->code . '</td>';
            $courses_list .= '<td>' . $course_obj->title . '</td>';
            $teacher_obj = $course_obj->get_teacher();
            if (is_object($teacher_obj)) {
                $courses_list .= '<td>' . $teacher_obj->get_displayname . '</td>';
            }
            else {
                $courses_list .= '<td>N/A</td>';
            }
            if ($course_obj->description) {
                $courses_list .= '<td>' . $course_obj->description . '/td>';
            }
            else {
                $courses_list .= '<td>N/A</td>';
            }
            if ($course_obj->is_subscribed($core->user['uid'])) {
                $courses_list .= '<td><span style="color:green;font-weight:bold">Yes <span class="glyphicon glyphicon-ok"></span></span></td>';
            }
            else {
                $courses_list .= '<td><span style="color:red;font-weight:bold"">No <span class="glyphicon glyphicon-remove"></span></span></td>';
            }
            $courses_list .= '<td></td>';

            $courses_list .= '</tr>';
        }
    }
    eval("\$page= \"" . $template->get('courses_courselist') . "\";");
    output_page($page);
}
else {
    
}