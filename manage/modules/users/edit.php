<?php

if (!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if ($core->usergroup['canAdminCP'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$lang->load('users_add');

if (!$core->input['action']) {
    if (!isset($core->input['uid']) || empty($core->input['uid'])) {
        redirect('index.php?module=users/view');
    }

    $uid = $db->escape_string($core->input['uid']);
    $userobj = new Users($uid, false);
    $user = $userobj->get();


    $usergroup_attributes = array('gid', 'title');
    $usergroup_order = array(
        'by' => 'title',
        'sort' => 'ASC'
    );

    $usergroups = get_specificdata('usergroups', $usergroup_attributes, 'gid', 'title', $usergroup_order);
    if ($core->user['gid'] != 1) {
        unset($usergroups[1]);
    }
    $usergroups_list = parse_selectlist('gid', 5, $usergroups, $user['gid']);
    //parse programs list
    $activeprograms = Programs::get_data(array('isActive' => 1), array('returnarray' => true));
    //get already assigned programs
    $assignedprograms_objs = $userobj->get_assignedprograms();
    if (is_array($assignedprograms_objs)) {
        foreach ($assignedprograms_objs as $assignedprograms_obj) {
            $assignedprograms[$assignedprograms_obj->progid] = $assignedprograms_obj->progid;
        }
    }
    $programs_list = parse_selectlist2('program[]', 1, $activeprograms, $assignedprograms, 1);


    $actiontype = 'edit';
    $pagetitle = $user['username'];

    $uidfield = "<input type='hidden' value='{$uid}' name='uid'>";
    eval("\$editpage = \"" . $template->get('admin_users_addedit') . "\";");
    output_page($editpage);
}
else {
    if ($core->input['action'] == 'do_perform_edit') {
        if (!empty($core->input['password']) || !empty($core->input['password2'])) {
            if ($core->input['password'] != $core->input['password2']) {
                output_xml("<status>false</status><message>{$lang->passwordsnomatch}</message>");
                exit;
            }
        }

        if (is_empty($core->input['email'], $core->input['firstName'], $core->input['lastName'])) {
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            exit;
        }

        unset($core->input['password2'], $core->input['action'], $core->input['module']);
        $modify = new ModifyAccount($core->input);
        if ($modify->get_status() === true) {
            output_xml("<status>true</status><message>{$lang->profilesuccessfullyupdated}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorupdatingprofile}</message>");
        }
    }
}
?>