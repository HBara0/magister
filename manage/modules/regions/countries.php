<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 *
 * Manage Countries
 * $module: admin/regions
 * $id: countries.php
 * Last Update: @zaher.reda 	Mar 18, 2009 | 03:45 PM
 */
if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canManageCountries'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $query = $db->query("SELECT c.*, a.name AS affname FROM ".Tprefix."countries c LEFT JOIN ".Tprefix."affiliates a ON (c.affid=a.affid) ORDER BY c.name ASC");
    if($db->num_rows($query) > 0) {
        while($country = $db->fetch_array($query)) {
            $class = alt_row($class);

            extract($country);
            if(empty($affname)) {
                $affname = "N/A";
            }
            $countries_list .= "<tr class='{$class}'><td>{$coid}</td><td>{$name} ({$acronym})</td><td>{$affname}</td>";
            $countries_list .='<td><a style="cursor: pointer;" title="'.$lang->update.'" id="updatecountrydtls_'.$coid.'_'.$core->input['module'].'_loadpopupbyid" rel="countrydetail_'.$coid.'"><img src="'.$core->settings[rootdir].'/images/icons/update.png"/></a></td></tr>';

            $affiliates_attributes = array("affid", "name");
            $countries_order = array(
                    "by" => "name",
                    "sort" => "ASC"
            );

            $affiliates = get_specificdata("affiliates", $affiliates_attributes, "affid", "name", $countries_order, 1);
            if(!empty($affiliates)) {
                $affiliates_list = parse_selectlist("affid", 2, $affiliates, $coid);
            }
            else {
                $affiliates_list = $lang->noaffiliatesavailable;
            }
        }
    }
    else {
        $countries_list = "<tr><td colspan='4' style='text-align: center;
            '>{$lang->nocountriesavailable}</td></tr>";
    }
    eval("\$addcountry = \"".$template->get("popup_addcountry")."\";");
    eval("\$countriespage = \"".$template->get("admin_regions_countries")."\";");
    output_page($countriespage);
}
else {
    if($core->input['action'] == "do_add_countries") {
        $country = $core->input['country'];
        $country['affid'] = $core->input['affid'];
        $country['name'] = ucfirst($country['name']);
        $country['acronym'] = substr(strtoupper($country['acronym']), 0, 2);
        $country_obj = new Countries();
        $country_obj->set($country);
        $country_obj->save();
        switch($country_obj->get_errorcode()) {
            case 0:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 1:
                output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
                break;
            case 2:
            default:
                output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
                break;
        }
        exit;

//        if(empty($country['name']) || empty($country['acronym'])) {
//            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
//            exit;
//        }
//
//        if(value_exists("countries", "name", $country['name'])) {
//            output_xml("<status>false</status><message>{$lang->countryalreadyexists}</message>");
//            exit;
//        }
//
//        log_action($country['name']);
//        unset($core->input['module'], $core->input['action']);
//
//        $country['name'] = ucfirst($country['name']);
//        $country['acronym'] = strtoupper($country['acronym']);
//        $query = $db->insert_query("countries", $country);
//        if($query) {
//            $lang->countryadded = $lang->sprint($lang->countryadded, "<strong>".$country['name']."</strong>");
//            output_xml("<status>true</status><message>{$lang->countryadded}</message>");
//        }
//        else {
//            output_xml("<status>false</status><message>{$lang->erroraddingcountry}</message>");
//        }
    }
    elseif($core->input['action'] == 'get_updatecountrydtls') {
        $country_obj = new Countries($core->input['id']);
        $country['coid'] = $country_obj->coid;
        $country['name'] = $country_obj->name;
        $country['acronym'] = $country_obj->acronym;

        $affiliates[0] = '';
        $affiliates += Affiliates::get_affiliates(null, array('returnarray' => true, 'order' => array('by' => 'name', 'sort' => 'ASC')));
        if(!empty($affiliates)) {
            $affiliates_list = parse_selectlist("affid", 2, $affiliates, $country_obj->affid);
        }
        else {
            $affiliates_list = $lang->noaffiliatesavailable;
        }
        eval("\$addcountry = \"".$template->get('popup_addcountry')."\";");
        output($addcountry);
    }
    elseif($core->input['action'] == 'update_countrydetails') {
        $data = json_decode(Countries::get_livedata());
        if(is_array($data)) {
            foreach($data as $datarray) {
                $city_obj = Cities::get_data(array('name' => $datarray->capital));
                $country_obj = Countries::get_data(array('acronym' => $datarray->alpha2Code), array('returnarray' => false));
                $currency = Currencies::get_data(array('alphaCode' => $datarray->currencies[0]));
                if(is_object($country_obj)) {
                    $country = $country_obj->get();
                    $country['phoneCode'] = $datarray->callingCodes[0];
                    if(is_object($city_obj) && $country['capitalCity'] == '0') {
                        $country['capitalCity'] = $city_obj->ciid;
                    }
                    elseif(is_array($city_obj)) {
                        foreach($city_obj as $city) {
                            if(is_object($city_obj) && $country['coid'] == $city['coid'] && $country['capitalCity'] == '0') {
                                $country['capitalCity'] = $city_obj->ciid;
                            }
                        }
                    }
                    if(is_object($currency) && (is_null($country['mainCurrency']) || $country['mainCurrency'] == '0')) {
                        $country['mainCurrency'] = $currency->numCode;
                    }
                    if(!isset($country['timeZone']) || empty($country['timeZone']) || is_null($country['timeZone'])) {
                        $country['timeZone'] = $datarray->timezones[0];
                    }
                    if($country['acronym'] == 'CY') {
                        $country['continent'] = 'Asia';
                        $country['region'] = 'Western Asia';
                    }
                    else {
                        if(!isset($country['continent']) || empty($country['continent']) || is_null($country['continent'])) {
                            $country['continent'] = $datarray->region;
                        }
                        if(!isset($country['region']) || empty($country['region']) || is_null($country['region'])) {
                            $country['region'] = $datarray->subregion;
                        }
                    }
                    $country_obj->set($country);
                    $country_obj->save();
                    $errorcodes[$country_obj->coid] = $country_obj->get_errorcode();
                }
            }
            if(is_array($errorcodes)) {
                $errorcodes = array_unique($errorcodes);
                foreach($errorcodes as $coid => $errorcode) {
                    if($errorcode != 0) {
                        $problemcoids[] = $coid;
                    }
                }
                if(is_array($problemcoids)) {
                    echo('Errors while saving these country data: '.implode(',', $problemcoids));
                }
                else {
                    echo('Data Saved Succesfully! Nicely Done!!');
                }
            }
            else {
                echo('No Countries Saved');
            }
        }
    }
}
?>