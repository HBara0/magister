<?php 
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Survey List
 * $module: survyes
 * $id: list.php	
 * Created: 		@tony.assaad	        May 8, 2012 | 12:00 PM
 * Last Updated: 	@zaher.reda				May 14, 2012 | 12:46 PM
 */
 
if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
	$sort_url = sort_url();
	$newsurvey = new Surveys();
	$survey_details = $newsurvey->get_user_surveys();
	
	if(is_array($survey_details)) {
		foreach($survey_details as $survey) {
			$rowclass = alt_row($rowclass);	
			$survey['dateCreated_output'] = date($core->settings['dateformat'], $survey['dateCreated']);
			if(!$newsurvey->check_respondant($survey['sid'])) {
				$link = 'fill';
				$identifier = $survey['identifier'];
			}	
			else
			{
				$link = 'viewresponse';
				$identifier = $newsurvey->get_responseidentifier($survey['sid'],$core->user['uid']);
			}
			$fillsurvey_link = 'index.php?module=surveys/'.$link.'&identifier='.$identifier;
			
			$surveystats_link = '';
			if(($survey['isPublicResults'] == 1 && $core->user['uid'] != $survey['createdBy']) || $core->user['uid'] == $survey['createdBy']) {
				$surveystats_link = '<a href="index.php?module=surveys/viewresults&identifier='.$survey['identifier'].'"><img src="./images/icons/stats.gif" border="0" alt="{$lang->viewresults}"/></a>';
			}
			
			eval("\$surveys_rows .= \"".$template->get('surveys_listsurveys_row')."\";");
		}
	}
	else
	{
		$surveys_rows .= '<tr><td colspan="5">'.$lang->na.'</td></tr>';
	}
	eval("\$surveysList = \"".$template->get('surveys_listsurveys')."\";");
	output_page($surveysList);
}
?>