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
    $event_obj = new Events(intval($core->input['id']));
    $event = $event_obj->get();
    $id = $event_obj->get_id();
    $hide_manageeventbutton = ' style="display:none"';
    //show manage course button depending on user permission
    if ($event_obj->canManageEvent()) {
        $editlink = $event_obj->get_editlink();
        $hide_manageeventbutton = '';
    }
    $event_displayname = $event_obj->get_displayname();

    //parse date range output0
    $daterangeoutput = $event_obj->parse_daterangeoutput();
    if (!$event['description']) {
        $hide_eventdescription = 'style="display:none"';
    }
    //parse course take/remove button
    if ($core->usergroup['canTakeLessons'] == 1) {
        if ($event_obj->is_subscribed($core->user['uid'])) {
            $addorremovecourse_button = '<div id="subscribedive_' . $id . '" ><button type="button" class="btn btn-danger" id="subscribebutton_' . $id . '_remove"><span class="glyphicon glyphicon-minus"></span>' . $lang->removecourse . '</button>';
        }
        else {
            $addorremovecourse_button = '<div id="subscribedive_' . $id . '"><button type="button" class="btn btn-primary" id="subscribebutton_' . $id . '_subscribe"><span class="glyphicon glyphicon-plus"></span>' . $lang->addcourse . '</button>';
        }
    }

    eval("\$page= \"" . $template->get('events_eventprofile') . "\";");
    output_page($page, array('pagetitledirect' => $event_displayname));
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
            $output = '<div id="subscribedive_' . $id . '"><button type="button" class="btn btn-primary" id="subscribebutton_' . $id . '_subscribe"><span class="glyphicon glyphicon-plus"></span>' . $lang->addcourse . '</button>';
        }
        else {
            $output = '<div id="subscribedive_' . $id . '"><button type="button" class="btn btn-danger" id="subscribebutton_' . $id . '_remove"><span class="glyphicon glyphicon-minus"></span>' . $lang->removecourse . '</button>';
        }
        echo($output);
        exit;
    }
}