<?php

if ($core->usergroup['can_CreateCourse'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if (!$core->input['action']) {
    if ($core->usergroup['can_CreateCourse'] == 0) {
        $hide_createcoursebutton = ' style="display:none"';
    }
    if (isset($core->input['id']) && !empty($core->input['id'])) {
        $course_obj = new Courses(intval($core->input['id']));
        $course = $course_obj->get();
        $teacherid = $course['teacherId'];
        $hide_createcoursebutton = '';
        $isActive = $course['isActive'];
    }
    $isactive_list = parse_selectlist2('course[isActive]', 1, array('1' => 'Yes', '0' => 'No'), $isActive);
    $teacher_objs = Users::get_teachers();
    if (is_array($teacher_objs)) {
        $teacher_list = parse_selectlist2('course[teacherId]', 1, $teacher_objs, $teacherid, '', '', array('id' => 'teacher', 'blankstart' => true));
    }
    eval("\$managejcourse= \"" . $template->get('courses_managecourse') . "\";");
    output_page($managejcourse);
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
        case 2:
            output_xml("<status>false</status><message>{$lang->jobexists}</message>");
            break;
        case 3:
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
            break;
        case 4:
            output_xml("<status>false</status><message>{$lang->jobexistsameaff}</message>");
            break;
        case 5:
            output_xml("<status>false</status><message>{$lang->wrongpublishoptions}</message>");
            break;
        default:
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
            break;
    }
}
?>