<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: create.php
 * Created:        @tony.assaad    Feb 20, 2014 | 3:27:43 PM
 * Last Update:    @tony.assaad    Feb 20, 2014 | 3:27:43 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['development_canCreateReq'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $requirements_obj = new Requirements();
    $user_obj = new Users();
    $allusers_objs = $user_obj->get_allusers();
    $reports_to = $user_obj->get_reportingto();
    foreach($allusers_objs as $allusers_obj) {
        $allusers[] = $allusers_obj->get();
    }
    foreach($allusers as $user) {
        $requestedby_list .='<option value="'.$user['uid'].'"> '.$user['displayName'].' </option>';
    }
    $assignedto_list = '<option value="" selected="selected"> </option>';
    foreach($reports_to as $assignedto) {
        $assignedto_list.='<option value="'.$assignedto['uid'].'"> '.$assignedto['displayName'].' </option>';
    }

    $requirements[0] = array('title' => '', 'refWord' => '');
    $requirements = $requirements + $requirements_obj->read_user_requirements(true);

    if(is_array($requirements)) {
        $parent_list = $requirements_obj->parse_requirements_list($requirements, true, '', 'select', array('id' => 'development[parent]', 'name' => 'development[parent]'));
    }

    eval("\$createrequirment = \"".$template->get('development_createrequirement')."\";");
    output_page($createrequirment);
}
else {
    if($core->input['action'] == 'do_add') {
        if(is_empty($core->input['development']['modulefield'], $core->input['development']['title'])) {
            output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
            exit;
        }


        $refKey = $db->fetch_field($db->query("SELECT (refKey)+1 as refKey FROM ".Tprefix."development_requirements WHERE parent=".intval($core->input['development']['parent'])." AND refWord='".$db->escape_string($core->input['development']['refWord'])."' ORDER BY refKey DESC LIMIT 0, 1"), 'refKey');
        if(empty($refKey)) {
            $refKey = 1;
        }
        $requi_array = Array
                (
                'module' => $core->input['development']['modulefield'],
                'title' => $core->input['development']['title'],
                'refKey' => $refKey,
                'parent' => $core->input['development']['parent'],
                'description' => $core->input['development']['description'],
                'userInterface' => $core->input['development']['userInterface'],
                'security' => $core->input['development']['security'],
                'performance' => $core->input['development']['performance'],
                'requestedby' => $core->input['development']['requestedby'],
                'isApproved' => $core->input['development']['isApproved'],
                'isCompleted' => $core->input['development']['isCompleted'],
                'assignedTo' => $core->input['development']['assignedTo'],
                'refWord' => $core->input['development']['refWord'],
                'createdBy' => $core->user['uid'],
                'dateCreated' => TIME_NOW
        );

        if($db->insert_query('development_requirements', $requi_array)) {
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            exit;
        }
        else {
            output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
            exit;
        }
    }
}
?>
