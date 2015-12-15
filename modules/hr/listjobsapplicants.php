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

if($core->usergroup['hr_canCreateJobOpport'] == 10) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $vacancy_id = $db->escape_string($core->input['id']);
    if(!$core->input['action']) {
        $sort_url = sort_url();
        $vacancy = new HrVancancies();
        $job_applicant = $vacancy->get_jobapplicants($vacancy_id);
        if(is_array($job_applicant)) {
            foreach($job_applicant as $key => $jobapplicant) {
                $rowclass = alt_row($rowclass);
                if($jobapplicant['isFlagged'] == 1) {
                    $jobapplicant['flagicon'] = '<img  id="'.$jobapplicant['hrvaid'].'" src="././images/icons/red_flag.gif" border="0"/>';
                }
                //$td = '<td id="flagg"></td>';

                $jobapplicant['dateCreated_output'] = date($core->settings['dateformat'], $jobapplicant['dateSubmitted']);
                eval("\$hr_listjobsapplicants_rows.= \"".$template->get('hr_listjobsapplicants_rows')."\";");
            }

            $multipage_where .= $db->escape_string($attributes_filter_options['prefixes'][$core->input['filterby']].$core->input['filterby']).$filter_value;
            $multipages = new Multipages('hr_vacancies_applicants hrvapp', $core->settings['itemsperlist'], $multipage_where);
            $hr_listjobsapplicants_rows .= "<tr><td colspan='6'>".$multipages->parse_multipages()."</td></tr>";
        }
        else {
            $hr_listjobsapplicants_rows .= '<tr><td colspan="5">'.$lang->na.'</td></tr>';
        }
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