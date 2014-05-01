<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * List Jobs
 * $module: hr
 * $id: listjob.php	
 * Created By: 		@tony.assaad		September 27, 2012 | 5:30 PM
 * Last Update: 	@tony.assaad		September 27, 2012 | 3:13 PM
 */


if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['hr_canCreateJobOpport'] == 10) {
    error($lang->sectionnopermission);
    exit;
}
if(!$core->input['action']) {
    $sort_url = sort_url();
    $vacancy = new HrVancancies();
    $jobs_details = $vacancy->get_jobs();
    if(is_array($jobs_details)) {
        foreach($jobs_details as $jobid => $job) {
            $rowclass = alt_row($rowclass);
            if(($job['views']) == 0) {
                $job['hits'] = 0;
            }
            $job['dateCreated_output'] = date($core->settings['dateformat'], $job['dateCreated']);
            /* if we are in */
            if(TIME_NOW >= $job['publishOn'] && TIME_NOW <= $job['unpublishOn']) {
                $published = '<img src="./images/valid.gif" border="0" />';
            }
            else {
                $published = '<img src="./images/false.gif" border="0" />';
            }
            if(($job['countapplicants']) != 0) {
                $link = '<a style="cursor:pointer;"href="index.php?module=hr/listjobsapplicants&id='.$job['hrvid'].'" target="_blank"  title="'.$job['countapplicants'].'">'.$job['countapplicants'].'</a>';
            }
            else {
                $link = $job['countapplicants'];
            }

            eval("\$hr_listjobs_row.= \"".$template->get('hr_listjobs_row')."\";");
        }
        $multipage_where .= $db->escape_string($attributes_filter_options['prefixes'][$core->input['filterby']].$core->input['filterby']).$filter_value;
        $multipages = new Multipages('hr_vacancies vr', $core->settings['itemsperlist'], $multipage_where);
        $hr_listjobs_row .= "<tr><td colspan='6'>".$multipages->parse_multipages()."</td></tr>";
    }
    else {
        $hr_listjobs_row .= '<tr><td colspan="5">'.$lang->na.'</td></tr>';
    }
    eval("\$jobslist = \"".$template->get('hr_listjobs')."\";");
    output_page($jobslist);
}
?>