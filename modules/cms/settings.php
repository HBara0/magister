<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Manage Site Settings
 * $module: CMS
 * $id: settings.php
 * Created: 	@zaher.reda
 * Last Update: @zaher.reda 	August 09, 2012 | 04:PM
 */
if(!defined("DIRECT_ACCESS")) {
    die('Direct initialization of this file is not allowed.');
}

/* if($core->usergroup['cms_canChangeSettings'] == 0) {
  error($lang->sectionnopermission);
  exit;
  } */

if(!$core->input['action']) {
    $cms = new Cms('db', false);
    $settings = $cms->get_settings();

    foreach($settings as $name => $setting) {
        $rowclass = alt_row($rowclass);

        if($setting['optionscode'] == 'text') {
            $option = "<input type='text' value='".$setting['value']."' id='".$setting['name']."' name='".$setting['name']."' title='".$setting['description']."' />";
        }
        elseif($setting['optionscode'] == 'password') {
            $option = "<input type='password' value='".$setting['value']."' id='".$setting['name']."' name='".$setting['name']."' title='".$setting['description']."' />";
        }
        elseif($setting['optionscode'] == 'yesno') {
            $option = parse_yesno($setting['name'], $counter, $setting['value']);
        }
        elseif($setting['optionscode'] == 'textarea') {
            $option = "<textarea name='".$setting['name']."' id='".$setting['name']."' cols='30' rows='5' title='".$setting['description']."'>".$setting['value']."</textarea>";
        }

        $settingslist .= '<tr class="'.$rowclass.'"><td width="35%"><strong>'.$setting['title'].'</strong></td><td>'.$option.'</td></tr>';
        unset($option);
    }

    eval("\$settingspage = \"".$template->get('cms_sitesettings')."\";");
    output_page($settingspage, array('pagetitle' => 'cmssettings'));
}
else {
    if($core->input['action'] == 'do_change_settings') {
        unset($core->input['module'], $core->input['action']);
        $settings = new Cms();
        $settings->update_settings($core->input);
        $settings->rebuild_settings();
        switch($settings->get_status()) {
            case 0:
                output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                break;
            default:
                output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                break;
        }
    }
}
?>