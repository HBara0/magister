<?php

/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Add users
 * $module: admin/users
 * $id: add.php	
 * Created: 	@zaher.reda 		Februar, 2009
 * Last Update: @zaher.reda 		March 21, 2012 | 05:23 PM
 */
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
    $usergroups_list = parse_selectlist('maingid', 5, $usergroups, 3);
    $addusergroups_list = parse_selectlist('addgids[]', 5, $usergroups, '', 1);


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
        }
        else {
            output_xml("<status>false</status><message>{$lang->passwordsnomatch}</message>");
        }
    }
}
?>