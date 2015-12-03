<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * View a Response Questionnaire
 * $module: Surveys
 * $id: viewresponse.php
 * Created: 	@zaher.reda 	May 03, 2012 | 04:45:00 PM
 * Last Update: @zaher.reda 	May 22, 2012 | 05:45:00 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    if(!empty($core->input['identifier'])) {
        $survey = new Surveys();

        $responses = $survey->get_single_responses($core->input['identifier']);
        if(!$responses) {
            redirect('index.php?module=surveys/list');
        }
        $survey_details = $survey->get_survey();
        $associations = $survey->get_associations();
        if(is_array($associations)) {
            $associations_list = $lang->surveysassociations.':<ul><li>'.implode('</li><li>', $associations).'</li></ul>';
        }

        $questions = $survey->get_questions();

        foreach($questions as $section) {
            $questions_list .= '<div class="subtitle" style="margin-top:20px; border-top: thin solid #E8E8E8;">'.$section['section_title'].'</div>';
            foreach($section['questions'] as $question) {
                if(isset($responses[$question['stqid']])) {
                    if(!is_object($surveyinvitation)) {
                        $surveyinvitation = new SurveyInvitations(intval($responses[$question['stqid']]['srid']));
                    }
                    $questions_list .= $survey->parse_response($responses[$question['stqid']], $question, $question['isQuiz']);
                }
            }
        }
        if(is_object($surveyinvitation)) {
            if($survey_details['isQuiz'] == 1) {
                $result = $lang->failed;
                if($surveyinvitation->passed == 1) {
                    $result = $lang->passed;
                }
                if($surveyinvitation->score > $surveyinvitation->total) {
                    $surveyinvitation->score = $surveyinvitation->total;
                }
                $totalscore = $surveyinvitation->score.'/'.$surveyinvitation->total;
                $scores = $lang->result.' : '.$result.'<br>'.$lang->totalscore.' : '.$totalscore;
            }
        }
        eval("\$fillreportpage = \"".$template->get('surveys_viewresponse')."\";");
        output_page($fillreportpage);
    }
    else {
        redirect($_SERVER['HTTP_REFERER']);
    }
}
?>