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
            else {
                $link = 'viewresponse';
                $identifier = $newsurvey->get_responseidentifier($survey['sid'], $core->user['uid']);
            }
            $fillsurvey_link = 'index.php?module=surveys/'.$link.'&identifier='.$identifier;

            $surveystats_link = '';
            if(($survey['isPublicResults'] == 1 && $core->user['uid'] != $survey['createdBy']) || $core->user['uid'] == $survey['createdBy']) {
                $surveystats_link = '<a href="index.php?module=surveys/viewresults&identifier='.$survey['identifier'].'"><img src="./images/icons/stats.gif" border="0" alt="{$lang->viewresults}"/></a>';
            }
            $previewlink = '<a href="index.php?module=surveys/preview&identifier='.$survey['identifier'].'" target="_blank"><img src="./images/icons/report.gif" border="0" title="'.$lang->preview.'" alt="{$lang->preview}"/></a>';
            $sharewith = '<a href="#'.$survey['sid'].'" id="sharesurvey_'.$survey['sid'].'_surveys/list_loadpopupbyid" data-id="'.$survey['sid'].'" data-template="sharesurvey" data-module="surveys/list" data-params="'.base64_encode(json_encode(array('identifier' => $survey['identifier']))).'" rel="share_'.$survey['sid'].'" title="'.$lang->sharewith.'"><img src="'.$core->settings['rootdir'].'/images/icons/sharedoc.png" alt="'.$lang->sharewith.'" border="0"></a>';

            eval("\$surveys_rows .= \"".$template->get('surveys_listsurveys_row')."\";");
        }
    }
    else {
        $surveys_rows .= '<tr><td colspan="5">'.$lang->na.'</td></tr>';
    }
    eval("\$surveysList = \"".$template->get('surveys_listsurveys')."\";");
    output_page($surveysList);
}
else {
    if($core->input['action'] == 'get_sharesurvey') {
        $sid = intval($core->input['id']);
        if(isset($core->input['params']) && !empty($core->input['params'])) {
            $core->input['params'] = json_decode(base64_decode($core->input['params']));

            $identifier = $core->input['params']->identifier;
        }
        $affiliates_users = Users::get_allusers();
        $survey_obj = new Surveys($identifier);
        if(!is_object($survey_obj)) {
            return;
        }
        $shared_users = $survey_obj->get_shared_users();
        if(is_array($shared_users)) {
            foreach($shared_users as $user_obj) {
                $user = $user_obj->get();
                $userposition = $user_obj->get_positions();
                if(is_array($userposition)) {
                    $user['position'] = implode(',', $userposition);
                }
                $user['mainAffiliate'] = $user_obj->get_mainaffiliate()->get_displayname();
                $checked = ' checked="checked"';
                $rowclass = 'selected';
                eval("\$sharewith_rows .= \"".$template->get('popup_surveys_sharewith_rows')."\";");
            }
        }

        foreach($affiliates_users as $uid => $user_obj) {
            $user = $user_obj->get();
            $checked = $rowclass = '';
            if($uid == $core->user['uid']) {
                continue;
            }
            if(is_array($shared_users)) {
                if(array_key_exists($uid, $shared_users)) {
                    continue;
                }
            }
            $userposition = $user_obj->get_positions();
            if(is_array($userposition)) {
                $user['position'] = implode(',', $userposition);
            }
            $user['mainAffiliate'] = $user_obj->get_mainaffiliate()->get_displayname();
            eval("\$sharewith_rows .= \"".$template->get('popup_surveys_sharewith_rows')."\";");
        }
        $file = 'list';
        eval("\$share_survey = \"".$template->get('popup_surveys_share')."\";");
        output($share_survey);
    }
    elseif($core->input['action'] == 'do_share') {
        $sid = $db->escape_string($core->input['sid']);
        $identifier = $core->input['identifier'];
        if(is_array($core->input['sharesurvey'])) {
            $survey_obj = new Surveys($identifier);
            $sharesurvey = $survey_obj->share($core->input['sharesurvey']);
            if($sharesurvey == false) {
                output_xml('<status>true</status><message>Error Saving</message>');
                exit;
            }
            else {
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            }
        }
        else {
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
        }
    }
}
?>