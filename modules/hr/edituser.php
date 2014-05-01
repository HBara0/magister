<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Edit user
 * $module: hr
 * $id: edituser.php	
 * Last Update: @zaher.reda 	October 18, 2010 | 4:23 PM
 * Last Update: @zaher.reda 	September 7, 2012 | 04:32 PM
 */
if(!defined("DIRECT_ACCESS")) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['hr_canEditEmployee'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {
    if(!isset($core->input['uid']) || empty($core->input['uid'])) {
        redirect('index.php?module=hr/employeeslist');
    }

    $uid = $db->escape_string($core->input['uid']);
    if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
        if(!value_exists('affiliatedemployees', 'uid', $uid, 'affid IN ('.implode(',', $core->user['hraffids']).')')) {
            redirect('index.php?module=hr/employeeslist');
        }
    }

    $user = $db->fetch_assoc($db->query("SELECT u.*, uhrid.*, u.uid AS uid, AES_DECRYPT(salary, `salarykey`) as salary 
										FROM ".Tprefix."users u LEFT JOIN ".Tprefix."userhrinformation uhrid ON (u.uid=uhrid.uid)
										WHERE u.uid='{$uid}'"));
    if(!is_array($user)) {
        redirect('index.php?module=hr/employeeslist');
    }

    $dates_index = array('birthDate', 'joinDate', 'leaveDate', 'firstJobDate');
    foreach($dates_index as $val) {
        if(!empty($user[$val])) {
            $user[$val.'_output'] = date($core->settings['dateformat'], $user[$val]);
            $user[$val.'_formatted'] = date('d-m-Y', $user[$val]);
        }
    }

    $selectlist_index = array('gender', 'maritalStatus', 'empClassification', 'paymentMethod');
    foreach($selectlist_index as $val) {
        $selectedoptions[$val][$user[$val]] = ' selected="selected"';
    }

    if($user['hasChildren'] == 1) {
        $checkedboxes['hasChildren'] = ' checked';
    }

    $user['reportsToName'] = $db->fetch_field($db->query("SELECT username FROM ".Tprefix."users WHERE uid='{$user[reportsTo]}'"), 'username');
    $user['assistantName'] = $db->fetch_field($db->query("SELECT username FROM ".Tprefix."users WHERE uid='{$user[assistant]}'"), 'username');

    $countries = get_specificdata('countries', array('coid', 'name'), 'coid', 'name', $countries_order);
    $nationality_list = parse_selectlist('nationality', 10, $countries, $user['nationality']);
    $countries_list = parse_selectlist('country', 10, $countries, $user['country']);

    $userpositions = get_specificdata('userspositions', '*', 'upid', 'posid', '', 0, "uid='{$uid}'");
    $positions_list = parse_selectlist('posid[]', 24, get_specificdata('positions', '*', 'posid', 'title', array('by' => 'name', 'sort' => 'ASC')), $userpositions, 1);

    $employeessegments = get_specificdata('employeessegments', '*', 'emsid', 'psid', '', 0, "uid='{$uid}'");
    $segments_list = parse_selectlist('psid[]', 3, get_specificdata('productsegments', array('psid', 'title'), 'psid', 'title', 'title'), $employeessegments, 1);

    $phones_index = array('mobile', 'mobile2', 'telephone', 'telephone2');
    foreach($phones_index as $val) {
        $phone[$val] = explode('-', $user[$val]);

        $phones[$val]['intcode'] = $phone[$val][0];
        $phones[$val]['areacode'] = $phone[$val][1];
        $phones[$val]['number'] = $phone[$val][2];
    }

    /* $telephone[1] = explode('-', $user['phone1']);
      $telephone[2] = explode('-', $user['phone2']);

      $telephone[1]['intcode'] = &$telephone[1][0];
      $telephone[1]['areacode'] = &$telephone[1][1];
      $telephone[1]['number'] = &$telephone[1][2];

      $telephone[2]['intcode'] = &$telephone[2][0];
      $telephone[2]['areacode'] = &$telephone[2][1];
      $telephone[2]['number'] = &$telephone[2][2];
     */
    //$uidfield = "<input type='hidden' value='{$uid}' name='uid'>";

    for($i = 1; $i <= 12; $i++) {
        $months[$i] = $lang->{strtolower(date('F', mktime(0, 0, 0, $i, 1, 0)))};
    }
    $experience_query = $db->query("SELECT fromMonth, toMonth, fromYear, toYear, company, position
									FROM ".Tprefix."employeesexperience
									WHERE uid='{$uid}'");
    $experience_rowid = 1;
    if($db->num_rows($experience_query) > 0) {
        while($experience = $db->fetch_assoc($experience_query)) {
            $experience_frommonth_selectlist = parse_selectlist("experience[{$experience_rowid}][fromMonth]", 1, $months, $experience['fromMonth'], 0);
            $experience_tomonth_selectlist = parse_selectlist("experience[{$experience_rowid}][toMonth]", 1, $months, $experience['toMonth'], 0);
            eval("\$experience_rows .= \"".$template->get('hr_edituser_experienceentry')."\";");
            $experience_rowid++;
        }
    }
    else {
        $experience_frommonth_selectlist = parse_selectlist("experience[{$experience_rowid}][fromMonth]", 1, $months, '', 0);
        $experience_tomonth_selectlist = parse_selectlist("experience[{$experience_rowid}][toMonth]", 1, $months, '', 0);

        eval("\$experience_rows = \"".$template->get('hr_edituser_experienceentry')."\";");
    }

    $certificate_rowid = 1;
    $certificates_query = $db->query("SELECT name, year, schoolName
									FROM ".Tprefix."employeeseducationcert
									WHERE uid='{$uid}'");
    if($db->num_rows($certificates_query) > 0) {
        while($certificate = $db->fetch_assoc($certificates_query)) {
            eval("\$education_rows .= \"".$template->get('hr_edituser_certificateentry')."\";");
            $certificate_rowid++;
        }
    }
    else {
        eval("\$education_rows = \"".$template->get('hr_edituser_certificateentry')."\";");
    }

    eval("\$editpage = \"".$template->get('hr_edituser')."\";");
    output_page($editpage);
}
else {
    if($core->input['action'] == 'do_perform_edituser') {
        if(empty($core->input['firstName']) || empty($core->input['lastName'])) {
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            exit;
        }

        /* 		if(!value_exists('affiliatedemployees', 'uid', $core->input['uid'], 'affid IN ('.implode(',', $core->user['hraffids']).')')) {
          output_xml("<status>false</status><message>{$lang->errorupdatingprofile}</message>");
          exit;
          } */

        $log->record($core->input['username']);
        unset($core->input['action'], $core->input['module']);

        $core->input['referrer'] = 'hr';

        $modify = new ModifyAccount($core->input);
        if($modify->get_status() === true) {
            output_xml("<status>true</status><message>{$lang->profilesuccessfullyupdated}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorupdatingprofile}</message>");
        }
    }
}
?>