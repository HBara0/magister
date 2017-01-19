<?php

define("DIRECT_ACCESS", 1);

require_once "../global.php";

if ($core->usergroup['canAdminCP'] == 1) {
    $modules_dir = ROOT . "/" . ADMIN_DIR . "/modules";

    $current_module = explode('/', $core->input['module'], 2);
    if ($core->input['module'] && $current_module[0]) {
        $run_module = $current_module[0];
    }
    else {
        $defaultmodule = 'home';
        $run_module = $defaultmodule;
        if (!isset($defaultmodule) || empty($defaultmodule)) {
            $run_module = $core->usergroup['defaultModule'];
            if (!isset($core->usergroup['defaultModule']) || empty($core->usergroup['defaultModule'])) {
                $run_module = 'portal';
            }
        }
        $current_module[1] = false;
    }
    $lang->load($run_module . '_meta');

    $modules_list = parse_moduleslist($run_module, ADMIN_DIR . "/modules");
    $display['frequentlyused'] = 'style="display:none;"';
    unset($modules_list_freqmdls);
    eval("\$header = \"" . $template->get("navbar") . "\";");
    eval("\$footer = \"" . $template->get("footer2") . "\";");
    // eval("\$menu = \"".$template->get("admin_mainmenu")."\";");
    $menu_items = parse_menuitems($run_module, ADMIN_DIR . "/modules");

    eval("\$rightsidemenu = \"" . $template->get('rightside_menu') . "\";");


    $current_module = explode("/", $core->input['module'], 2);
    if ($core->input['module'] && $current_module[0]) {
        $run_module = $current_module[0];
    }
    else {
        $run_module = "home";
        $current_module[1] = "index";
    }

    $action_file = $current_module[1] . ".php";

    if ($lang->language_file_exists($run_module . "_meta")) {
        $lang->load($run_module . "_meta");
    }
    require $modules_dir . "/" . $run_module . "/" . $action_file;
}
else {
    error($lang->admincpnopermission);
}
?>