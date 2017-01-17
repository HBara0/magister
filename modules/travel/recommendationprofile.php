<?php

if (!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if ($core->usergroup['canAccessSystem'] == 0) {
    error($lang->sectionnopermission);
}
if (!isset($core->input['action'])) {

    if (!isset($core->input['id']) || empty($core->input['id'])) {
        redirect(DOMAIN . '/index.php?module=travel/recommendationslist');
    }
    $recommendation_obj = new Recommendations(intval($core->input['id']));
    $recommendation = $recommendation_obj->get();
    $recommendation_displayname = $recommendation_obj->get_compositeDisplayname();

    $id = $recommendation_obj->get_id();
    $hide_managerecommendationbutton = ' style="display:none"';
    //show manage course button depending on user permission
    if ($recommendation_obj->canManageRecommendation()) {
        $editlink = $recommendation_obj->get_editlink();
        $hide_managerecommendationbutton = '';
    }

    if (!$recommendation['description']) {
        $hide_recommendatinodescription = 'style="display:none"';
    }
    //parse subscribe button
    $gotobutton = '<div id="subscribedive_' . $id . '"><button type="button" class="btn btn-primary" id="subscribebutton_' . $id . '_subscribe"><span class="glyphicon glyphicon-plus"></span>' . $lang->addcourse . '</button>';



    eval("\$page= \"" . $template->get('travel_recommendationprofile') . "\";");
    output_page($page, array('pagetitledirect' => $recommendation_displayname));
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
}