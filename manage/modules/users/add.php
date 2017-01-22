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
    $usergroup_attributes = array('gid', 'title');
    $usergroup_order = array(
        'by' => 'title',
        'sort' => 'ASC'
    );

    $usergroups = get_specificdata('usergroups', $usergroup_attributes, 'gid', 'title', $usergroup_order);
    if ($core->user['gid'] != 1) {
        unset($usergroups[1]);
    }
    $usergroups_list = parse_selectlist('gid', 5, $usergroups, 3);

    $activeprograms = Programs::get_data(array('isActive' => 1), array('returnarray' => true));
    $programs_list = parse_selectlist2('program[]', 1, $activeprograms, '', 1);

    $actiontype = 'add';
    $pagetitle = $lang->adduser;
    eval("\$addpage = \"" . $template->get('admin_users_addedit') . "\";");
    output_page($addpage);
}
else {
    if ($core->input['action'] == 'do_perform_add') {
        if (empty($core->input['password'])) {
            output_xml("<status>false</status><message>{$lang->specifypassword}</message>");
            exit;
        }

        if ($core->input['password'] == $core->input['password2']) {
            $log->record($core->input['username']);
            unset($core->input['module'], $core->input['action'], $core->input['password2']);
            $account = new CreateAccount($core->input);
            output_xml("<status>true</status><message>Success</message>");
            exit;
        }
        else {
            output_xml("<status>false</status><message>{$lang->passwordsnomatch}</message>");
        }
    }
}
?>