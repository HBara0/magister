<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Global file
 * $id: global.php
 * Created: 	@zaher.reda		Mar 11, 2009 | 01:00 AM
 * Last Update: @zaher.reda 	August 1, 2012 | 03:17 PM
 */

$dir = dirname(__FILE__);
if(!$dir) {
    $dir = '.';
}

require $dir.'/inc/init.php';
set_headers();

define('SYSTEMVERSION', '24.0.0');

if(strpos(strtolower($_SERVER['PHP_SELF']), ADMIN_DIR) !== false) {
    define('IN_AREA', 'admin');
    /* $additional_inc = '<script src="'.$core->settings['rootdir']."/".$config['admindir'].'/jscript/jscript.js" type="text/javascript"></script>';
      $additional_inc .= '<script src="'.$core->settings['rootdir']."/".$config['admindir'].'/jscript/jquery.jeditable.pack.js" type="text/javascript"></script>'; */
}
else {
    define('IN_AREA', 'user');
}

$lang = new Language($core->user['language'], IN_AREA);
$charset = $lang->settings['charset'];
$htmllang = $lang->settings['htmllang'];
$db->set_charset($lang->settings['charset_db']);

$lang->load('global');
if(!empty($core->user['language'])) {
    date_default_timezone_set($core->user['language']);
}
eval("\$headerinc = \"".$template->get('headerinc')."\";");

if($session->uid > 0) {
    /* Check if passwors has expired */
    if(IN_AREA != 'admin') {
        if(((TIME_NOW - $core->user['lastPasswordChange']) / 24 / 60 / 60) > $core->settings['passwordExpiresAfter']) {
            if(!defined('PASSEXPIRE_EXCLUDE') || PASSEXPIRE_EXCLUDE == 0) {
                redirect(DOMAIN.'/users.php?action=profile&amp;do=edit&amp;messagecode=1');
            }
        }
    }

    if($core->usergroup['canAccessSystem'] == 0) {
        error($lang->accountsuspended);
    }

    if($core->settings['onmaintenance'] == 1) {
        if($core->user['gid'] != 1) {
            error($core->settings['maintenancemessage']);
        }
        else {
            $maintenancenotice = '<p class="notice">System is set on maintenance.</p>';
        }
    }

    if($core->usergroup['canAdminCP'] == 1 && IN_AREA != 'admin') {
        eval("\$admincplink = \"".$template->get('header_admincplink')."\";");
    }
    elseif(IN_AREA == 'admin') {
        eval("\$mainpageslink = \"".$template->get('header_mainpageslink')."\";");
    }
    $lang->welcomeuser = $lang->sprint($lang->welcomeuser, '<strong>'.$core->user['displayName'].'</strong>');
    if($core->user['lastVisit'] == 0) {
        $lang->lastvisit = $lang->firstvisit;
    }
    else {
        $lang->lastvisit = $lang->sprint($lang->lastvisit, date($core->settings['dateformat'], $core->user['lastVisit']), date($core->settings['timeformat'], $core->user['lastVisit'])).' UTC';
    }

    eval("\$header = \"".$template->get('header')."\";");
    eval("\$footer = \"".$template->get('footer')."\";");
    if($core->user['lastVisit'] == 0) {
        eval("\$footer .= \"".$template->get('global_quickintrovideo')."\";");
    }
}
else {
    if(strpos(strtolower($_SERVER['PHP_SELF']), 'users.php') === false) {
        redirect(DOMAIN.'/users.php?action=login&amp;referer='.base64_encode($_SERVER['REQUEST_URI']));
    }
}
?>