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
        $dates = array('approxJoinDate', 'publishOn', 'unpublishOn');
        foreach($dates as $date) {
            if($jobopportunity[$date] != 0) {
                $jobopportunity[$date.'_output'] = date($core->settings['dateformat'], $jobopportunity[$date]);
            }
        }

        if($jobopportunity_obj->joinDateImmediate == 1) {
            $checked['joinDateImmediate'] = 'checked="checked"';
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
    if(!empty($jobopportunity['workLocation'])) {
        $city = new Cities(intval($jobopportunity['workLocation']));
        if(is_object($city)) {
            $jobopportunity['workLocation_output'] = $city->get_displayname();
        }
    }
    if(!empty($jobopportunity['residence'])) {
        $rescity = new Cities(intval($jobopportunity['residence']));
        if(is_object($rescity)) {
            $jobopportunity['residence_output'] = $rescity->get_displayname();
        }
    }
    $employmentType_array = array('fulltime' => $lang->fulltime, 'parttime' => $lang->parttime, 'casual' => $lang->casual, 'fixedterm' => $lang->fixedterm, 'commission' => $lang->commission, 'trainee' => $lang->trainee, 'workexperience' => $lang->workexperience);
    $employmenttype_list = parse_selectlist('jobopportunity[employmentType]', $tabindex, $employmentType_array, $jobopportunity['employmentType'], '', '', array('width' => '200px'));
    if(isset($jobopportunity['drivingLicReq'])) {
        if($jobopportunity['drivingLicReq'] == '1') {
            $checked['drivingLicReq']['yes'] = 'checked="checked"';
        }
        elseif($jobopportunity['drivingLicReq'] == '0') {
            $checked['drivingLicReq']['no'] = 'checked="checked"';
        }
    }
    if(isset($jobopportunity['allowSocialSharing'])) {
        if($jobopportunity['allowSocialSharing'] == '1') {
            $checked['allowSocialSharing']['yes'] = 'checked="checked"';
        }
        elseif($jobopportunity['allowSocialSharing'] == '0') {
            $checked['allowSocialSharing']['no'] = 'checked="checked"';
        }
    }
    if(isset($jobopportunity['managesOthers'])) {
        if($jobopportunity['managesOthers'] == '1') {
            $checked['managesOthers']['yes'] = 'checked="checked"';
        }
        elseif($jobopportunity['managesOthers'] == '0') {
            $checked['managesOthers']['no'] = 'checked="checked"';
        }
    }
    if(isset($jobopportunity['gender'])) {
        if($jobopportunity['gender'] == '1') {
            $checked['gender']['male'] = 'checked="checked"';
        }
        elseif($jobopportunity['gender'] == '2') {
            $checked['gender']['female'] = 'checked="checked"';
        }
    }

    $countries = get_specificdata('countries', array('coid', 'name'), 'coid', 'name', array('by' => 'name', 'sort' => 'ASC'));
    $nationality_list = parse_selectlist('jobopportunity[nationality]', '', $countries, $jobopportunity['nationality'], 0, '', array('width' => '200px', 'blankstart' => true));

    $radiobuttons['drivingLicReq'] = parse_yesno('jobopportunity[drivingLicReq]', '', $jobopportunity[drivingLicReq], '', '', array('width' => '200px'));

    $selectedcareers = HrJobOpportunitiesSelectedCareerLevel::get_data(array('joid' => $jobopportunity['joid'], 'type' => 'requirement'), array('returnarray' => true));
    if(is_array($selectedcareers)) {
        foreach($selectedcareers as $selectedcareer) {
            $selectedcareers_options[$selectedcareer->joclid] = $selectedcareer->joclid;
        }
    }
    $careerlevels = HRCareerLevel::get_data(array('name IS NOT NULL'), array('returnarray' => true, 'order' => array('by' => 'sequence', 'sort' => 'ASC')));
    $careerlevel_list = parse_selectlist('jobopportunity[careerLevel][]', '', $careerlevels, $selectedcareers_options, '1', '', array('width' => '200px', 'blankstart' => true));


    $filter_selectedcareerlevels = HrJobOpportunitiesSelectedCareerLevel::get_data(array('joid' => $jobopportunity['joid'], 'type' => 'filter'), array('returnarray' => true));
    if(is_array($filter_selectedcareerlevels)) {
        foreach($filter_selectedcareerlevels as $filter_selectedcareerlevel) {
            $filter_selectedcareerlevels_options[$filter_selectedcareerlevel->joclid] = $filter_selectedcareerlevel->joclid;
        }
    }

    $filter['careerlevel_list'] = parse_selectlist('filter[careerLevel][]', '', $careerlevels, $filter_selectedcareerlevels_options, '1', '', array('width' => '200px', 'blankstart' => true));


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
    $selectededucations = HrJobOpportunitiesSelectedEducationLevel::get_data(array('joid' => $jobopportunity['joid'], 'type' => 'requirement'), array('returnarray' => true));
    if(is_array($selectededucations)) {
        foreach($selectededucations as $selectededucation) {
            $selectededucations_options[$selectededucation->joelid] = $selectededucation->joelid;
        }
    }
    $educationlevels = HREducationLevel::get_data(array('name IS NOT NULL'), array('returnarray' => true, 'order' => array('by' => 'sequence', 'sort' => 'ASC')));
    $educationlevel_list = parse_selectlist('jobopportunity[educationLevel][]', '', $educationlevels, $selectededucations_options, '1', '', array('width' => '200px', 'blankstart' => true));


    $filter_selectededucations = HrJobOpportunitiesSelectedEducationLevel::get_data(array('joid' => $jobopportunity['joid'], 'type' => 'filter'), array('returnarray' => true));
    if(is_array($filter_selectededucations)) {
        foreach($filter_selectededucations as $filter_selectededucation) {
            $filter_selectededucations_options[$filter_selectededucation->joelid] = $filter_selectededucation->joelid;
        }
    }
    $filter['educationlevel_list'] = parse_selectlist('filter[educationLevel][]', '', $educationlevels, $filter_selectededucations_options, '1', '', array('width' => '200px', 'blankstart' => true));

    $languages_array = array('english' => 'English', 'french' => 'French', 'arabic' => 'Arabic');
    $selectedlanguages = HrJobOpportunitiesLanguage::get_data(array('joid' => $jobopportunity['joid'], 'type' => 'requirement'), array('returnarray' => true));
    if(is_array($selectedlanguages)) {
        foreach($selectedlanguages as $selectedlanguage) {
            $langselected_options[$selectedlanguage->language] = $selectedlanguage->language;
        }
    }
    $languages_list = parse_selectlist('jobopportunity[requiredlang][]', '', $languages_array, $langselected_options, '1', '', '', array('width' => '200px', 'blankstart' => true));

    $mainaffiliate_obj = new Affiliates($core->user['mainaffiliate']);
    $mainaffiliate['curr'] = $mainaffiliate_obj->get_currency()->numCode;
    if(isset($jobopportunity['salaryCurrency']) && !empty($jobopportunity['salaryCurrency'])) {
        $mainaffiliate['curr'] = $jobopportunity['salaryCurrency'];
    }
    $currencies = get_specificdata('currencies', array('numCode', 'alphaCode'), 'numCode', 'alphaCode', array('by' => 'name', 'sort' => 'ASC'));
    $currencies_list = parse_selectlist('jobopportunity[salaryCurrency]', 11, $currencies, $mainaffiliate['curr'], 0, '');


    $time_zones = DateTimeZone::listIdentifiers();
    $time_zones = parse_selectlist('jobopportunity[publishingTimeZone]', '', array_combine($time_zones, $time_zones), $jobopportunity['publishingTimeZone'], '', '', array('width' => '200px', 'blankstart' => true)); /* call the listidentifier from Object DateTimeZone */


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



    /* parse existing filter data */
    $filterdata = array();
    $filter_obj = HrJobOpportunitiesFilter::get_data(array('joid' => intval($core->input['id'])), array('returnarray' => false));
    if(is_object($filter_obj)) {
        $filterdata = $filter_obj->get();
        if(!empty($filterdata['residence'])) {
            $resicity = new Cities($filterdata['residence']);
            if(is_object($resicity)) {
                $filterdata['residence_output'] = $resicity->get_displayname();
            }
            if(!empty($filterdata['filterType'])) {
                $checked['filtertype'][$filterdata['filterType']] = 'checked="checked"';
            }
            if(isset($filterdata['gender'])) {
                if($filterdata['gender'] == '1') {
                    $checked['filtergender']['male'] = 'checked="checked"';
                }
                elseif($filterdata['gender'] == '2') {
                    $checked['filtergender']['female'] = 'checked="checked"';
                }
            }
        }
    }
    eval("\$managejob = \"".$template->get('hr_managejobopportunity')."\";");
    output_page($managejob);
}
elseif($core->input['action'] == 'do_perform_managejobopportunity') {
    $jobopportunitiy_obj = new HrJobOpportunities();
    $jobopportunitiy_obj->set($core->input['jobopportunity']);
    $jobopportunitiy_obj = $jobopportunitiy_obj->save();
    switch($jobopportunitiy_obj->get_errorcode()) {
        case 0:
            if(!empty($core->input['filter']) && is_array($core->input['filter'])) {
                $jobopportunitiyfilter_obj = new HrJobOpportunitiesFilter();
                $jobopportunitiyfilter_obj->set($core->input['filter']);
                $jobopportunitiyfilter_obj->joid = $jobopportunitiy_obj->joid;
                $jobopportunitiyfilter_obj = $jobopportunitiyfilter_obj->save();
                switch($jobopportunitiyfilter_obj->get_errorcode()) {
                    case 0:
                        output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                        exit;
                    default:
                        output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                        exit;
                }
            }
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
        case 5:
            output_xml("<status>false</status><message>{$lang->wrongpublishoptions}</message>");
            break;
        default:
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
            break;
    }
}
?>