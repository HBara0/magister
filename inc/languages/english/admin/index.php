<?php

define("DIRECT_ACCESS", 1);

require_once "../global.php";

if ($core->usergroup['canAdminCP'] == 1) {
    eval("\$header = \"" . $template->get("header") . "\";");
    eval("\$footer = \"" . $template->get("footer") . "\";");
    eval("\$menu = \"" . $template->get("admin_mainmenu") . "\";");

    $modules_dir = ROOT . "/" . ADMIN_DIR . "/modules";

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