<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Fill Survey
 * $module: Surveys
 * $id: fillsurvey.php
 * Created: 	@zaher.reda 	May 03, 2012 | 04:45:00 PM
 * Last Update: @zaher.reda 	July 25, 2012 | 11:15:00 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    if(!empty($core->input['identifier'])) {
        $survey = new Surveys($core->input['identifier']);

        if(is_object($survey) && $survey->sid != null && !$survey->check_respondant()) {
            $survey_details = $survey->get_survey();

            /* Prevent accessing from OCOS if survey is external */
            if($survey_details['isExternal'] == 1) {
                redirect('index.php?module=surveys/list');
            }

            if(TIME_NOW > $survey_details['closingDate'] && !empty($survey_details['closingDate'])) {
                error($lang->surveyexpired, 'index.php?module=surveys/list');
            }

            $survey_details['dateCreated_output'] = date($core->settings['dateformat'], $survey_details['dateCreated']);
            $associations = $survey->get_associations();
            if(is_array($associations)) {
                $associations_list = $lang->surveysassociations.':<ul><li>'.implode('</li><li>', $associations).'</li></ul>';
            }

            $questions = $survey->get_questions();
            if(!is_array($questions)) {
                redirect('index.php?module=surveys/list');
            }
            foreach($questions as $section) {
                if(!is_empty($section['section_title'], $section['section_description'])) {
                    $questions_list .= '<div class="subtitle" style="margin-top:20px; border-top: thin solid #E8E8E8;">-'.$section['section_title'].'<br/>'.$section['section_description'].'</div>';
                }
                foreach($section['questions'] as $question) {
                    $questions_list .= $survey->parse_question($question);
                }
            }
            fix_newline($survey_details['description']);

            //Record survey start time//
            $duration = 25;
            $db->update_query('surveys_invitations', array('startTime' => TIME_NOW), 'invitee='.$core->user['uid'].' AND sid='.$survey_details['sid'].' AND ( (('.TIME_NOW.' -startTime)/60) > '.$duration.')');

            eval("\$fillreportpage = \"".$template->get('surveys_fillsurvey')."\";");
            output_page($fillreportpage);
        }
        else {
            redirect('index.php?module=surveys/list');
        }
    }
    else {
        redirect('index.php?module=surveys/list');
    }
}
else {
    if($core->input['action'] == 'do_perform_fill') {
        $survey = new Surveys($core->input['identifier']);
        if(is_array($core->input['answer'])) {
            $survey->save_responses($core->input['answer']);
        }
        switch($survey->get_status()) {
            case 0:
                header('Content-type: text/xml+javascript');
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'<![CDATA[<script>goToURL("'.DOMAIN.'/index.php?module=surveys/list");</script>]]></message>');
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                break;
            case 2:
                output_xml("<status>false</status><message>{$lang->alreadyfilled}</message>");
                break;
            case 3:
                output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                break;
            case 4:
                output_xml("<status>false</status><message>{$lang->violation}</message>");
                break;
        }
    }
}
?>