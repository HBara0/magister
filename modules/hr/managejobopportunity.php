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

if($core->usergroup['hr_canCreateJobOpport'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$lang->load('hr_jobopportunities');
if(!$core->input['action']) {
    if(isset($core->input['id']) && !empty($core->input['id'])) {
        $jobopportunity_obj = HrJobOpportunities::get_data(array('joid' => intval($core->input['id'])));
        if(!is_object($jobopportunity_obj)) {
            redirect('index.php?module=hr/managejobopportunity');
        }
        $jobopportunity = $jobopportunity_obj->get();
        $dates = array('approxJoinDate', 'publishOn', 'unpublishOn', 'publishingTimeZone');
        foreach($dates as $date) {
            if($jobopportunity[$date] != 0) {
                $jobopportunity[$date.'_output'] = date($core->settings['dateformat'], $jobopportunity[$date]);
            }
        }
    }
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
        $hraffiliates[$affiliates['affid']] = $affiliates['name'];
    }

    $affiliates_list = parse_selectlist('jobopportunity[affid]', $tabindex, $hraffiliates, '', '', '', array('width' => '200px'));
    /* validate and only show the affiliate the user is canHr --END */


//
//    foreach($worklocation as $key => $val) {
//        $citycountry_list .= "<option value='{$val[ciid]}'>{$val[city]}-{$val[name]}</option>";
//        $citylist .= "<option value='{$val[ciid]}'>{$val[city]}-{$val[name]}</option>";
//    }
//    $city['workLocation'] = Cities::get_data(array('eid' => $jobopportunity['workLocation']));
//    if(is_object($city['workLocation'])) {
//        $jobopportunity['workLocation_output'] = $city['workLocation']->get_displyname();
//    }

    $employmentType_array = array('fulltime' => $lang->fulltime, 'parttime' => $lang->parttime, 'casual' => $lang->casual, 'fixedterm' => $lang->fixedterm, 'commission' => $lang->commission, 'trainee' => $lang->trainee, 'workexperience' => $lang->workexperience);
    $employmenttype_list = parse_selectlist('jobopportunity[employmentType]', $tabindex, $employmentType_array, $jobopportunity['employmentType'], '', '', array('width' => '200px'));
    $radiobuttons['managesOthers'] = parse_yesno('jobopportunity[managesOthers]', '', $jobopportunity['managesOthers'], '', '', array('width' => '200px'));

    $checked['gender']['male'] = 'checked="checked"';
    $checked['gender']['female'] = '';
    if(isset($jobopportunity['gender'])) {
        if($jobopportunity['gender'] == 'male') {
            $checked['gender']['male'] = 'checked="checked"';
        }
        else {
            $checked['gender']['female'] = 'checked="checked"';
        }
    }

    $filter['gender'] = '<label><input type = "radio" id = "filter_gender" value = "1" name = "filter[gender]" oldtitle = "gender" title = ""/>'.$lang->male.'</label>
    <label><input type = "radio" checked = "checked" id = "filter[gender]" value = "0" name = "filter[gender]" aria-describedby = "ui-tooltip-3"/>'.$lang->female.'</label>';

    $countries = get_specificdata('countries', array('coid', 'name'), 'coid', 'name', array('by' => 'name', 'sort' => 'ASC'));
    $nationality_list = parse_selectlist('jobopportunity[nationality]', '', $countries, $jobopportunity['nationality'], 0, '', array('width' => '200px', 'blankstart' => true));

    $radiobuttons['drivingLicReq'] = parse_yesno('jobopportunity[drivingLicReq]', '', $jobopportunity[drivingLicReq], '', '', array('width' => '200px'));


    $careerlevels = HRCareerLevel::get_data(array('name IS NOT NULL'), array('returnarray' => true, 'order' => array('by' => 'sequence', 'sort' => 'ASC')));
    $careerlevel_list = parse_selectlist('jobopportunity[careerLevel]', '', $careerlevels, $selected_options, '1', '', array('width' => '200px', 'blankstart' => true));
    $filter['careerlevel_list'] = parse_selectlist('filter[careerLevel]', '', $careerlevels, $selected_options, '1', '', array('width' => '200px', 'blankstart' => true));


//    if(is_array($educationLevels)) {
//        foreach($educationLevels as $key => $value) {
//            if($key == 0) {
//                continue;
//            }
//            $checked = $rowclass = '';
//            $educationLevel_list .='<tr class="'.$rowclass.'">';
//            $educationLevel_list .='<td><input id="educationlevelfilter_check_'.$key.'" name="jobopportunity[educationLevel][]"  type="checkbox" '.$checked.' value="'.$key.'">'.$value.'</td></tr>';
//
//            $filter['educationLevel_list'] .='<tr class="'.$rowclass.'">';
//            $filter['educationLevel_list'] .='<td><input id="educationlevelfilter_check_'.$key.'" name="filter[educationLevel][]"  type="checkbox" '.$checked.' value="'.$key.'">'.$value.'</td></tr>';
//        }
//    }
    $educationlevels = HREducationLevel::get_data(array('name IS NOT NULL'), array('returnarray' => true, 'order' => array('by' => 'sequence', 'sort' => 'ASC')));
    $educationlevel_list = parse_selectlist('jobopportunity[educationLevel]', '', $educationlevels, $selected_options, '1', '', array('width' => '200px', 'blankstart' => true));
    $filter['educationlevel_list'] = parse_selectlist('filter[educationLevel]', '', $educationlevels, $selected_options, '1', '', array('width' => '200px', 'blankstart' => true));

    $languages_array = array('egilsh' => 'English', 'french' => 'French', 'arabic' => 'Arabic');
    $languages_list = parse_selectlist('jobopportunity[requiredlang]', '', $languages_array, $selected_options, '1', '', '', array('width' => '200px', 'blankstart' => true));

    $mainaffiliate_obj = new Affiliates($core->user['mainaffiliate']);
    $mainaffiliate['curr'] = $mainaffiliate_obj->get_currency()->numCode;
    if(isset($jobopportunity['salaryCurrency']) && !empty($jobopportunity['salaryCurrency'])) {
        $mainaffiliate['curr'] = $jobopportunity['salaryCurrency'];
    }
    $currencies = get_specificdata('currencies', array('numCode', 'alphaCode'), 'numCode', 'alphaCode', array('by' => 'name', 'sort' => 'ASC'));
    $currencies_list = parse_selectlist('jobopportunity[salaryCurrency]', 11, $currencies, $mainaffiliate['curr'], 0, '');


    $time_zones = parse_selectlist('jobopportunity[publishingTimeZone]', '', DateTimeZone::listIdentifiers(), 0, '', '', array('width' => '200px', 'blankstart' => true)); /* call the listidentifier from Object DateTimeZone */

    $radiobuttons['allowSocialSharing'] = parse_yesno('jobopportunity[allowSocialSharing]', 33, array('title' => $lang->allowSocialSharing));

    // $industries = get_specificdata('hr_industries', array('hriid', 'title'), 'hriid', 'title', array('by' => 'title', 'sort' => 'ASC'));
    // $listindustries = parse_selectlist('jobopportunity[filterIndustry]', 41, $industries, $hriid, 0, '');
    //$reqPortraitPhoto = parse_yesno('vacancies[reqPortraitPhoto]',14, array('title'=>'require Portrait Photo'));
//    $customize_questions = array('reqPortraitPhoto', 'reqIdNumber', 'reqMaritalStatus', 'reqMilitaryStatus', 'reqDiseasesInfo', 'reqEducationDetails', 'reqTrainingDetails', 'reqPrevExperience');
//    $tabindex = 21;
//    foreach($customize_questions as $reqquestion) {
//        $tabindex++;
//        if($reqquestion == 'reqTrainingDetails' || $reqquestion == 'reqPrevExperience') {
//            $options = array('checked' => 1);
//        }
//        ${$reqquestion} = parse_yesno('vacancies['.$reqquestion.']', $tabindex, $options);
//    }

    $filter['types'] = '<label><input type="radio" id = "filter_type" value="flag" name="filter[filterType]" oldtitle="type" title=""/>'.$lang->flag.'</label>
    <label><input type="radio" checked="checked" id = "filter[type]" value="discard" name="filter[filterType]" aria-describedby="ui-tooltip-3"/>'.$lang->discard.'</label>';

    eval("\$managejob = \"".$template->get('hr_managejobopportunity')."\";");
    output_page($managejob);
}
elseif($core->input['action'] == 'do_perform_managejobopportunity') {
    $jobopportunitiy_obj = new HrJobOpportunities();
    $jobopportunitiy_obj->set($core->input['jobopportunity']);
    $jobopportunitiy_obj->save();
    switch($jobopportunitiy_obj->get_errorcode()) {
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
?>