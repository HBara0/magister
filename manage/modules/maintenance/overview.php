<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * System overview
 * $module: admin/overview
 * $id: overview.php	
 * Created: 	@zaher.reda		September 9, 2009 | 04:31 PM
 * Last Update: @zaher.reda 	September 9, 2009 | 04:31 PM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canPerformMaintenance'] == 0) {
    error($lang->sectionnopermission);
}

$dbsize = format_size($db->table_status(), 2);
$serverload = get_serverload();
$phpversion = phpversion();
if($core->settings['enablecompression'] == 1) {
    $gzip_status = $lang->enabled;
}
else {
    $gzip_status = $lang->disabled;
}

if(is_writable(ROOT.INC_ROOT.'/settings.php')) {
    $chmod['settings'] = "<span style='color: green;'>{$lang->writable}</span>";
}
else {
    $chmod['settings'] = "<strong><span style='color: #C00'>{$lang->notwritable}</span></strong><br />{$lang->chmodto} 777";
}

if(is_writable(ROOT.'/'.$core->settings['exportdirectory'])) {
    $chmod['exportsdir'] = "<span style='color: green;'>{$lang->writable}</span>";
}
else {
    $chmod['exportsdir'] = "<strong><span style='color: #C00'>{$lang->notwritable}</span></strong><br />{$lang->chmodto} 777";
}

if(is_writable(ROOT.'/images/charts')) {
    $chmod['chartsdir'] = "<span style='color: green;'>{$lang->writable}</span>";
}
else {
    $chmod['chartsdir'] = "<strong><span style='color: #C00'>{$lang->notwritable}</span></strong><br />{$lang->chmodto} 777";
}

eval("\$overviewpage = \"".$template->get('admin_maintenance_overview')."\";");
output_page($overviewpage);
function get_serverload() {
    global $lang;

    $serverload = array();

    // Running Windows ??
    if(DIRECTORY_SEPARATOR != '\\') {
        if(@file_exists('/proc/loadavg') && $load = @file_get_contents('/proc/loadavg')) {
            $serverload = explode(' ', $load);
            $serverload[0] = round($serverload[0], 4);
        }

        if(!$serverload) {
            if(@ini_get('safe_mode') == 'On') {
                return $lang->unknown;
            }

            if($func_blacklist = @ini_get('suhosin.executor.func.blacklist')) {
                if(strpos(','.$func_blacklist.',', 'exec') !== false) {
                    return $lang->unknown;
                }
            }
            if($func_blacklist = @ini_get('disable_functions')) {
                if(strpos(','.$func_blacklist.',', 'exec') !== false) {
                    return $lang->unknown;
                }
            }

            $load = @exec('uptime');
            $load = split('load averages?: ', $load);
            $serverload = explode(',', $load[1]);
            if(!is_array($serverload)) {
                return $lang->unknown;
            }
        }
    }
    else {
        return $lang->unknown;
    }

    $returnload = trim($serverload[0]);

    return $returnload;
}

?>