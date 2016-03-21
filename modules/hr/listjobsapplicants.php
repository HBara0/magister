<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * List Job Applicants
 * $module: hr
 * $id: listjob.php
 * Created By: 		@tony.assaad		September 24, 2012 | 5:30 PM
 * Last Update: 	@tony.assaad		September 24, 2012 | 2:13 PM
 */


if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['hr_canCreateJobOpport'] == 0) {
    error($lang->sectionnopermission);
    exit;
}
$lang->load('hr_jobopportunities');

if(!$core->input['action']) {
    //filter by hr permission on affiliate

    if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
        $filters = array('affid' => $core->user['hraffids']);
    }
    //get vacancies sorted by affiliate
    $vacancies_byaffid = HrJobOpportunities::get_data($filters, array('returnarray' => true, 'order' => 'affid'));
    if(is_array($vacancies_byaffid)) {
        foreach($vacancies_byaffid as $vacancy_obj) {
            //get applicants for each vacancy
            $job_applicants = $vacancy_obj->get_active_applicants();
            if(is_array($job_applicants)) {
                $vacancy = $vacancy_obj->get();
                $vacancy['displayname'] = $vacancy_obj->title;
                $vacancy['affiliate'] = $vacancy_obj->get_affiliate()->get_displayname();
                foreach($job_applicants as $job_applicant_obj) {
                    $job_applicant = $job_applicant_obj->get();
                    $job_applicant['displayname'] = $job_applicant_obj->get_displayname();
                    $job_applicant['submissiondate'] = date($core->settings['dateformat'], $job_applicant['createdOn']);
//                    if($jobapplicant['isFlagged'] == 1) {
//                        $jobapplicant['flagicon'] = '<img  id="'.$jobapplicant['hrvaid'].'" src="././images/icons/red_flag.gif" border="0"/>';
//                    }
                    //$td = '<td id="flagg"></td>';
                    eval("\$hr_listjobsapplicants_rows.= \"".$template->get('hr_listjobsapplicants_rows')."\";");
                    unset($job_applicant);
                }
            }
        }
    }
    if(empty($hr_listjobsapplicants_rows)) {
        $hr_listjobsapplicants_rows = '<tr><td colspan="8" style="text-align:center">'.$lang->na.'</td></tr>';
    }
    eval("\$jobslist = \"".$template->get('hr_listjobsapplicants')."\";");
    output_page($jobslist);
}
elseif($core->input['action'] == 'do_moderation') {
    if(count($core->input['listCheckbox']) > 0) {
        $vacancy = new HrVancancies();
        $vacancy_id = $db->escape_string($core->input['vacancyid']);
        $inapplicant = implode(',', $core->input['listCheckbox']);
        if($core->input['moderationtool'] == 'flag') {
            $action = 'flag';
            $vacancy->moderate($action, $vacancy_id, $inapplicant);
            header('Content-type: text/xml+javascript');  /* hide each selected <tr> has applicant id  after successfull deletion */
            output_xml("<status>true</status><message>{$lang->flagged}</message>");
            exit;
        }
        elseif($core->input['moderationtool'] == 'unflag') {
            $action = 'unflag';
            $vacancy->moderate($action, $vacancy_id, $inapplicant);
            header('Content-type: text/xml+javascript');  /* hide each image of selected <td>  */
            output_xml("<status>true</status><message>{$lang->unflagged}</message>");
            exit;
        }
        elseif($core->input['moderationtool'] == 'delete') {
            $action = 'delete';
            $deleted = $vacancy->moderate($action, $vacancy_id, $inapplicant);
            if($deleted) {
                header('Content-type: text/xml+javascript');  /* hide each selected <tr> has applicant id  after successfull deletion */
                output_xml('<status>true</status><message>Applicatns deleted<![CDATA[<script> $("tr[id^='.$inapplicant.']").each(function() {$(this).remove();});</script>]]></message>');
                exit;
            }
        }
        $vacancy->moderate($action, $vacancy_id, $inapplicant);
    }
    else {
        output_xml("<status>false</status><message>{$lang->selectatleastoneapplicant}</message>");
    }
}
?>