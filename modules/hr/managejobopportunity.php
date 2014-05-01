<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Manage Job Opportunity
 * $module: HR
 * $id: managejobopportunity.php	
 * Created By: 		@tony.assaad		September 17, 2012 | 3:30 PM
 * Last Update: 	@tony.assaad		September 21, 2012 | 1:13 PM
 */

if($core->usergroup['hr_canCreateJobOpport'] == 10) {
    error($lang->sectionnopermission);
    exit;
}
if(!$core->input['action']) {
    $question_rowid = 0;
    /* validate and only show the affiliate the user is canHr --START */
    if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
        $canhr_where = "WHERE (aff.canHr=1) AND (aff.uid=".$core->user['uid'].")";
    }
    $queryaffiliates = $db->query("SELECT af.name,aff.* FROM ".Tprefix." affiliatedemployees aff JOIN ".Tprefix." affiliates af 
								  ON (af.affid=aff.affid)
			 					  {$canhr_where}"
    );

    while($affiliates = $db->fetch_assoc($queryaffiliates)) {
        $hraffiliates[$affiliates['affid']] = $affiliates;
    }

    foreach($hraffiliates as $key => $affiliates) {
        $list_affiliates .= "<option value='{$affiliates[affid]}'>{$affiliates[name]}</option>";
    }
    /* validate and only show the affiliate the user is canHr --END */

    $citiesquery = $db->query("SELECT c.ciid,c.coid,c.name as city, co.name".Tprefix." FROM cities c JOIN countries co ON (c.coid = co.coid)");
    while($cities = $db->fetch_assoc($citiesquery)) {
        $worklocation[$cities['ciid']] = $cities;
    }

    foreach($worklocation as $key => $val) {
        $citycountry_list .= "<option value='{$val[ciid]}'>{$val[city]}-{$val[name]}</option>";
        $citylist .= "<option value='{$val[ciid]}'>{$val[city]}-{$val[name]}</option>";
    }
    $radiobuttons['managesOthers'] = parse_yesno('managesOthers', 8, array('title' => 'can manage Others'));

    $countries = get_specificdata('countries', array('coid', 'name'), 'coid', 'name', array('by' => 'name', 'sort' => 'ASC'));
    $nationalitylist = parse_selectlist('vacancies[nationality]', 11, $countries, $coid, 0, '');

    $employmentType_array = array(1 => 'Fulltime', 2 => 'Parttime', 3 => 'casual', 4 => 'fixedterm', 5 => 'commission', 6 => 'trainee', 7 => 'workexperience');
    foreach($employmentType_array as $key => $val) {
        $employmentType.= "<option value='{$key}'>{$val}</option>";
    }
    $careerlevel_array = array(1 => 'student', 2 => 'entrylevel', 3 => 'ms', 4 => 'phd');
    foreach($careerlevel_array as $key => $val) {
        $careerlevel.= "<option value='{$key}'>{$val}</option>";
    }
    $educationLevel_array = array(1 => 'highschool', 2 => 'ba', 3 => 'midCareer', 4 => 'management', 5 => 'executive', 6 => 'seniorexecutive');
    foreach($educationLevel_array as $key => $val) {
        $educationLevel.= "<option value='{$key}'>{$val}</option>";
    }
    $gender = '<label><input type="radio" id="vacancies[gender]" value="1" tabindex="13" name="vacancies[gender]" oldtitle="gender" title=""/>'.$lang->male.'</label>
				<label><input type="radio" checked="checked" id="vacancies[gender]" value="0" tabindex="13" name="vacancies[gender]"   aria-describedby="ui-tooltip-3"/>'.$lang->female.'</label>';

    $drivinglicense = parse_yesno('vacancies[drivingLicReq]', 14, array('title' => 'driving license'));

    $filtergender = '<label><input type="radio" id="vacancies[filterGender]" value="1" tabindex="37" name="vacancies[filterGender]" oldtitle="gender"/>'.$lang->male.'</label>
					<label><input type="radio" checked="checked" id="vacancies[filterGender]" value="0" tabindex="37" name="vacancies[filterGender]" "/>'.$lang->female.'</label>';

    $currencies = get_specificdata('currencies', array('alphaCode', 'name'), 'alphaCode', 'name', array('by' => 'name', 'sort' => 'ASC'));
    $currencies_list = parse_selectlist('vacancies[salaryCurrency]', 11, $currencies, $alphaCode, 0, '');

    $allowsocialsharing = parse_yesno('vacancies[allowSocialSharing]', 33, array('title' => 'Share on Social websites'));

    $industries = get_specificdata('hr_industries', array('hriid', 'title'), 'hriid', 'title', array('by' => 'title', 'sort' => 'ASC'));
    $listindustries = parse_selectlist('vacancies[filterIndustry]', 41, $industries, $hriid, 0, '');
    $time_zones = parse_selectlist('vacancies[publishingTimeZone]', 32, DateTimeZone::listIdentifiers(), 0, ''); /* call the listidentifier from Object DateTimeZone */

    //$reqPortraitPhoto = parse_yesno('vacancies[	reqPortraitPhoto]',14, array('title'=>'require Portrait Photo'));
    $customize_questions = array('reqPortraitPhoto', 'reqIdNumber', 'reqMaritalStatus', 'reqMilitaryStatus', 'reqDiseasesInfo', 'reqEducationDetails', 'reqTrainingDetails', 'reqPrevExperience');
    $tabindex = 21;
    foreach($customize_questions as $reqquestion) {
        $tabindex++;
        if($reqquestion == 'reqTrainingDetails' || $reqquestion == 'reqPrevExperience') {
            $options = array('checked' => 1); // array('checked'=>1);
        }
        ${$reqquestion} = parse_yesno('vacancies['.$reqquestion.']', $tabindex, $options);
        ;
    }

    $actiontype = 'Add';
    eval("\$managejob = \"".$template->get('hr_managejobopportunity')."\";");
    output_page($managejob);
}
/* Add vacancie --START */
elseif($core->input['action'] == 'do_Addjob') {
    $vacancy = new HrVancancies();
    $vacancy->create_vacancy($core->input['vacancies']);

    switch($vacancy->get_status()) {
        case 0:
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
            break;
        case 1:
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            break;
        case 2:
            output_xml("<status>false</status><message>{$lang->jobexists}</message>");
            break;
        case 3:
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
            break;
        case 4:
            output_xml("<status>false</status><message>{$lang->jobexistsameaff}</message>");
            break;
    }
}
/* Add vacancie --END */
?>