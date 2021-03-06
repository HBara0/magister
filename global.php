<?php

$dir = dirname(__FILE__);
if (!$dir) {
    $dir = '.';
}

require $dir . '/inc/init.php';
set_headers();

define('SYSTEMVERSION', '1.0.0');


if (strpos(strtolower($_SERVER['PHP_SELF']), ADMIN_DIR) !== false) {
    define('IN_AREA', 'admin');
}
else {
    define('IN_AREA', 'user');
}
$lang = new Language('english', IN_AREA);
$charset = $lang->settings['charset'];
$htmllang = $lang->settings['htmllang'];
$db->set_charset($lang->settings['charset_db']);

$lang->load('global');
if (!empty($core->user['language'])) {
    date_default_timezone_set($core->user['language']);
}
eval("\$headerinc = \"" . $template->get('headerinc') . "\";");
if ($session->uid > 0) {
    /* Check if passwors has expired */

    if ($core->usergroup['canAccessSystem'] == 0) {
        error($lang->accountsuspended);
    }

    if ($core->settings['onmaintenance'] == 1) {
        if ($core->user['gid'] != 1) {
            error($core->settings['maintenancemessage']);
        }
        else {
            $maintenancenotice = '<p class="notice">System is set on maintenance.</p>';
        }
    }

    if ($core->usergroup['canAdminCP'] == 1 && IN_AREA != 'admin') {
        eval("\$admincplink = \"" . $template->get('header_admincplink') . "\";");
    }
    elseif (IN_AREA == 'admin') {
        eval("\$mainpageslink = \"" . $template->get('header_mainpageslink') . "\";");
    }
    $lang->welcomeuser = $lang->sprint($lang->welcomeuser, '<strong>' . $core->user['displayName'] . '</strong>');
    if ($core->user['lastVisit'] == 0) {
        $lang->lastvisit = $lang->firstvisit;
    }
    else {
        $lang->lastvisit = $lang->sprint($lang->lastvisit, date($core->settings['dateformat'], $core->user['lastVisit']), date($core->settings['timeformat'], $core->user['lastVisit'])) . ' UTC';
    }

    $modules_list = parse_moduleslist($run_module);
    eval("\$header = \"" . $template->get('navbar') . "\";");
    eval("\$footer = \"" . $template->get('footer2') . "\";");

//    if($core->user['lastVisit'] == 0) {
//        eval("\$footer .= \"".$template->get('global_quickintrovideo')."\";");
//    }
}
else {
    if (strpos(strtolower($_SERVER['PHP_SELF']), 'users.php') === false) {
        redirect(DOMAIN . '/users.php?action=login&amp;referer=' . base64_encode($_SERVER['REQUEST_URI']));
    }
}
?>