<?php

if (!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if ($core->usergroup['canAdminCP'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if (!$core->input['action']) {

    $user_objs = Users::get_data('', array('returnarray' => true,));
    if (is_array($user_objs)) {
        foreach ($user_objs as $user_obj) {
            $user = $user_obj->get();
            $class = alt_row($class);

            if ($user['lastVisit'] != 0) {
                $lastvisit = date($core->settings['dateformat'] . " " . $core->settings['timeformat'], $user['lastVisit']);
            }
            else {
                $lastvisit = $lang->never;
            }
            $usergroup_output = '';
            $usergroup_obj = $user_obj->get_usergroup();
            if (is_object($usergroup_obj)) {
                $usergroup_output = $usergroup_obj->get_displayname();
            }
            $userslist .= "<tr>";
            $userslist .= "<td>{$user[uid]}</td><td>{$user[email]}&nbsp;</td><td>{$usergroup_output}</td><td>{$lastvisit}&nbsp;</td>";
            $userslist .= "<td><a target='_blank' href='index.php?module=users/edit&amp;uid={$user[uid]}'><img src='{$core->settings[rootdir]}/images/edit.gif' alt='{$lang->edit}' border='0' /></a></td></tr>";
        }
//        $multipages = new Multipages("users", $core->settings['itemsperlist']);
//        $userslist .= "<tr><td colspan='7'>".$multipages->parse_multipages()."</td></tr>";
    }
    else {
        $userslist = "<tr><td colspan='5'>{$lang->nousers}</td></tr>";
    }

    eval("\$viewpage = \"" . $template->get("admin_users_view") . "\";");
    output_page($viewpage);
}
?>