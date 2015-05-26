<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: windowslist.php
 * Created:        @hussein.barakat    May 22, 2015 | 12:13:35 PM
 * Last Update:    @hussein.barakat    May 22, 2015 | 12:13:35 PM
 */

if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}
if($core->usergroup['admin_canManageSystemDef'] == 0) {
    error($lang->sectionnopermission);
    exit;
}
$lang = new Language('english', 'admin');
$lang->load('managesystem');
$lang->load('global');

$window_objs = SystemWindows::get_data('', array('returnarray' => true));
if(is_array($window_objs)) {
    foreach($window_objs as $window_obj) {
        $window = $window_obj->get();
        $wid = $window['swid'];
        $is_active = $core->settings['rootdir'].'/images/false.gif';
        if($window['isActive'] == 1) {
            $is_active = $core->settings['rootdir'].'/images/true.gif';
        }

        eval("\$window_list_row .= \"".$template->get('admin_windows_list_rows')."\";");
        unset($wid, $is_active);
    }
}
eval("\$window_list = \"".$template->get('admin_windows_list')."\";");
output_page($window_list);
