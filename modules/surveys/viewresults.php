<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Survey List
 * $module: attendance
 * $id: surveysList.php
 * Created: 		@tony.assaad	May 8, 2012 | 12:00 PM
 * Last updated:	@zaher.reda		July 17, 2012 | 10:17 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    if(!empty($core->input['identifier'])) {

        $sort_url = sort_url();
        $newsurvey = new Surveys($core->input['identifier']);
        $survey = $newsurvey->get_survey();

        if(isset($core->input['referrer']) && $core->input['referrer'] == 'sharedlist') {
            $sharedsurvey = SurveyShares::get_data(array('sid' => $survey['sid'], 'uid' => $core->user['uid']));
            if(!is_object($sharedsurvey)) {
                redirect('index.php?module=surveys/list');
            }
            else {
                $survey['sharedwithstatus'] = true;
            }
        }

        $survey['invitations'] = $newsurvey->get_invitations();

        if($survey['sharedwithstatus'] == true || ($survey['isPublicResults'] == 1 || $survey['createdBy'] == $core->user['uid']) || ($survey['isPublicResults'] == 1 && $survey['isPublicFill'] == 0 && $newsurvey->check_invitation()) || value_exists('surveys_associations', 'id', $core->user['uid'], 'attr="uid" AND sid='.$survey['sid'])) {
            $responses_stats = $newsurvey->get_responses_stats();

            if(is_array($responses_stats)) {
                $questionsstats = '<h2><small>Graphical Statistics</small></h2>';
                foreach($responses_stats as $stqid => $question) {
                    if(isset($question['choices']['choicesvalues']) && is_array($question['choices']['choicesvalues'])) {
                        foreach($question['choices']['choicesvalues'] as $choicekyey => $choicesvalues) {
                            $choicetitle = $question['choices']['choice'][$choicekyey];
                            $pie_data['titles'] = $choicesvalues['choice'];
                            $pie_data['values'] = $choicesvalues['values'];
                            $pie = new Charts(array('x' => $pie_data['titles'], 'y' => $pie_data['values']), 'bar', array('scale' => SCALE_START0, 'noLegend' => true));
                            $chart = '<img src='.$pie->get_chart().' />';
                            eval("\$matrix_charts .= \"".$template->get('surveys_results_questionstat_matrix_singlechoice')."\";");
                        }

                        eval("\$questionsstats .= \"".$template->get('surveys_results_questionstat_matrix')."\";");
                        unset($matrixchat);
                    }
                    else {
                        $pie_data['titles'] = $question['choices']['choice'];
                        $pie_data['values'] = $question['choices']['stats'];

                        foreach($question['choices']['choice'] as $stqcid => $choice) {
                            /* Exit the loop if stats value = 0 */
                            if($question['choices']['stats'][$stqcid] == 0 || empty($question['choices']['value'][$stqcid])) {
                                continue;
                            }
                            /* Count of answers for a choice in the question Array */
                            $count[$stqid] += $question['choices']['stats'][$stqcid];
                            /* Weight is  value of the choice for each question */
                            $weighted_sum[$stqid] += ($question['choices']['stats'][$stqcid] * $question['choices']['value'][$stqcid]);
                        }

                        if(!empty($count[$stqid])) {
                            $question['average'] = round($weighted_sum[$stqid] / $count[$stqid], 2);
                            $lang->questionaverage_output = '('.$lang->questionaverage.' '.$question['average'].')';
                        }
                        else {
                            $lang->questionaverage_output = $question['average'] = '';
                        }

                        $pie = new Charts(array('x' => $pie_data['titles'], 'y' => $pie_data['values']), 'bar', array('scale' => SCALE_START0, 'noLegend' => true));
                        //$pie = new Charts(array('titles' => $pie_data['titles'], 'values' => $pie_data['values']), 'pie');
                        //$chart = '<img src='.$pie->get_chart().' />';
                        if($survey['createdBy'] == $core->user['uid']) {
                            $chart = '<a href="#question_'.$stqid.'" id="getquestionresponses_'.$stqid.'_'.$survey['identifier'].'"><img src='.$pie->get_chart().' border="0" /></a>';
                        }
                        else {
                            $chart = '<img src='.$pie->get_chart().' />';
                        }
                        eval("\$questionsstats .= \"".$template->get('surveys_results_questionstat')."\";");
                    }
                }

                $questionsstats .= '<hr /><input type="button" id="crosstabulation" class="button" value="Click to Generate Cross Tabulation"><div id="crosstabulation_results"></div>';
            }
        }

        $display['sendreminders'] = "style='display:none';";
        if($survey['createdBy'] == $core->user['uid']) {
            $display['sendreminders'] = "style='display:block';";
        }
        if($survey['createdBy'] == $core->user['uid'] || ($survey['sharedwithstatus'] == true)) {
            /* Show resposne list - START */
            $surveys_responses = $newsurvey->get_survey_distinct_responses('', array('sortby' => $core->input['sorbtby'], 'order' => $core->input['order']));

            if(is_array($surveys_responses)) {
                if($survey['isQuiz'] == 1) {
                    $passedheader = '<th>'.$lang->result.'<a href="'.$sort_url.'&amp;sortby=isQuiz&amp;order=ASC"><img src="./images/sort_asc.gif" border="0" alt="'.$lang->sortasc.'"/></a><a href="'.$sort_url.'&amp;sortby=isQuiz&amp;order=DESC"><img src="./images/sort_desc.gif" border="0" alt="'.$lang->sortdesc.'"/></a></th>';
                    $passedheader.= '<th>'.$lang->score.'</th>';
                }
                foreach($surveys_responses as $response) {
                    $rowclass = alt_row($rowclass);
                    $response['time_output'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $response['time']);
                    if($survey['anonymousFilling'] == 1) {
                        $response['respondant'] = ' - ';
                        $response['uid'] = '';
                    }
                    if($survey['isQuiz'] == 1) {
                        $textcolor = 'red';
                        $qresult = $lang->failed;
                        if($response['passed'] == 1) {
                            $qresult = $lang->passed;
                            $textcolor = 'green';
                        }
                        $passedcolumns = '<td><span style="color:'.$textcolor.'">'.$qresult.'</span></td>';
                        $passedcolumns .= '<td>'.$response['score'].'/'.$response['total'].'</td>';
                    }

                    eval("\$responses_rows .= \"".$template->get('surveys_results_responses_row')."\";");
                }
                eval("\$responses = \"".$template->get('surveys_results_responses')."\";");
            }
            else {
                $responses = ' <div class="ui-state-highlight ui-corner-all" style="padding-left: 5px; margin-bottom: 10px;"><p>'.$lang->noresponses.'</p></div>';
            }
            $surveys_pendingresponses = $newsurvey->get_pendingresposes();
            if(is_array($surveys_pendingresponses)) {
                foreach($surveys_pendingresponses as $pendingresponse) {
                    $rowclass = alt_row($rowclass);
                    if($survey['anonymousFilling'] != 1) {
                        if($survey['isExternal']) {
                            $pendingresponse['inviteeemail'] = $pendingresponse['invitee'];
                        }
                        else {
                            $user = new Users($pendingresponse['invitee']);
                            if(is_object($user)) {
                                $pendingresponse['inviteedisplayname'] = $user->parse_link();
                            }
                        }
                    }
                    $pendingresponsesrows .='<tr><td>'.$pendingresponse['identifier'].'</td><td>'.$pendingresponse['inviteedisplayname'].$pendingresponse['inviteeemail'].'</td></tr>';
                    unset($pendingresponse);
                }
                eval("\$pendingresponses = \"".$template->get('surveys_results_pendingresponses')."\";");
            }
            /* END resposne list - START */

            /* Parse Invitations Section - START */
            if($survey['createdBy'] == $core->user['uid']) {
                $display['sendreminders'] = "style='display:block';";
                $query = $db->query("SELECT DISTINCT(u.uid), u.*, aff.*, displayName, aff.name AS mainaffiliate, aff.affid
							FROM ".Tprefix."users u JOIN ".Tprefix."affiliatedemployees ae ON (u.uid=ae.uid) JOIN ".Tprefix."affiliates aff ON (aff.affid=ae.affid)
							WHERE gid!='7' AND isMain='1'
							ORDER BY displayName ASC");

                if($db->num_rows($query) > 0) {
                    while($user = $db->fetch_assoc($query)) {
                        $rowclass = alt_row($rowclass);

                        $userpositions = $hiddenpositions = $break = '';

                        $user_positions = $db->query("SELECT p.* FROM ".Tprefix."positions p LEFT JOIN ".Tprefix."userspositions up ON (up.posid=p.posid) WHERE up.uid='{$user[uid]}' ORDER BY p.name ASC");
                        $positions_counter = 0;

                        while($position = $db->fetch_assoc($user_positions)) {
                            if(!empty($lang->{$position['name']})) {
                                $position['title'] = $lang->{$position['name']};
                            }

                            if(++$positions_counter > 2) {
                                $hidden_positions .= $break.$position['title'];
                            }
                            else {
                                $userpositions .= $break.$position['title'];
                            }
                            $break = '<br />';
                        }

                        if($positions_counter > 2) {
                            $userpositions = $userpositions.", <a href='#' id='showmore_positions_{$user[uid]}'>...</a> <span style='display:none;' id='positions_{$user[uid]}'>{$hidden_positions}</span>";
                        }

                        /* Get User Segments - START */
                        $user_segments_query = $db->query("SELECT es.*, u.uid ,u.username, ps.title, ps.psid
												FROM ".Tprefix."employeessegments es
												JOIN ".Tprefix."users u ON (es.uid=u.uid)
												JOIN ".Tprefix."productsegments ps ON (ps.psid=es.psid)
												WHERE es.uid='{$user[uid]}'
												ORDER BY title ASC");

                        while($segment = $db->fetch_assoc($user_segments_query)) {
                            $segment_counter = 0;
                            $usersegments = $break = '';
                            if(++$segment_counter > 2) {
                                $hidden_segments .= $break.$segment['title'];
                            }
                            else {
                                $usersegments = $break.$segment['title'];
                            }
                            $break = '<br />';
                        }

                        if($segment_counter > 2) {
                            $usersegments .= ", <a href='#' id='showmore_segments_{$user[uid]}'>...</a> <span style='display:none;' id='segments_{$user[uid]}'>{$hidden_segments}</span>";
                        }
                        $checked = '';

                        if($newsurvey->check_invitation($user['uid'])) {
                            $rowclass = 'greenbackground';
                            //   $checked = ' checked="checked"';
                            $display['invitationcheckbox'] = "style='display:none';";
                        }
                        eval("\$invitations_row .= \"".$template->get('surveys_createsurvey_invitationrows')."\";");
                        unset($display['invitationcheckbox']);
                    }

                    if($survey['isExternal'] == 1) {
                        $display['internalinvitations'] = "style='display:none;'";
                    }
                    else {
                        $display['externalinvitations'] = "style='display:none;'";
                    }

                    eval("\$invitations .= \"".$template->get('surveys_results_invitations')."\";");
                }
            }
            /* Parse Invitations Section - END */
        }

        eval("\$surveys_viewresults = \"".$template->get('surveys_results')."\";");
        output_page($surveys_viewresults);
    }
    else {
        redirect($_SERVER['HTTP_REFERER']);
    }
}
else {
    if($core->input['action'] == 'get_questionresponses') {
        $newsurvey = new Surveys($core->input['identifier']);
        $survey = $newsurvey->get_survey();

        if($survey['createdBy'] != $core->user['uid']) {
            exit;
        }

        $questions_responses = $newsurvey->get_question_responses($core->input['question']);
        if(is_array($questions_responses)) {
            $responses_details_output = '<table class="datatable"><tr class="altrow"><th>#</th><th>'.$lang->choices.'</th><th>&nbsp;</th></tr>';
            foreach($questions_responses as $responses) {
                $responses_details_output .= '<tr><td style="width:10%;"><a href="index.php?module=surveys/viewresponse&amp;identifier='.$responses['identifier'].'" target="_blank" >'.$responses['identifier'].'</a></td><td style="width:40%;">'.implode(', ', $responses['choices']).'</td><td>'.$responses['comments'].'</td></tr>';
            }
            $responses_details_output .= '</table>';
        }
        else {
            $responses_details_output = $lang->na;
        }
        echo $responses_details_output;
    }
    elseif($core->input['action'] == 'sendreminders') {
        $survey_identifier = $db->escape_string($core->input['identifier']);
        $newsurvey = new Surveys($survey_identifier, false);
        $survey = $newsurvey->get_survey();

        if($survey['createdBy'] != $core->user['uid']) {
            exit;
        }

        $survey['invitations'] = $newsurvey->get_invitations();

        foreach($survey['invitations'] as $invitee) {
            if(($invitee['isDone']) != 1) {
                /* preparing reminder email */
                $surveylink = DOMAIN.'/index.php?module=surveys/fill&amp;identifier='.$survey_identifier;
                if($survey['isExternal'] == 1) {
                    $surveylink = 'http://www.orkila.com/surveys/'.$survey_identifier.'/'.$invitee['identifier'];
                    $invitee['displayName'] = split('@', $invitee['invitee'])[0];

                    $email_data = array(
                            'to' => $invitee['invitee'],
                            'from_email' => $core->user['email'],
                            'from' => $core->user['displayName'],
                            'subject' => $lang->survey_reminder_subject
                    );
                }
                else {
                    $email_data = array(
                            'to' => $invitee['email'],
                            'from_email' => $core->settings['maileremail'],
                            'from' => 'OCOS Mailer',
                            'subject' => $lang->survey_reminder_subject
                    );
                }

                $email_data['message'] = $lang->sprint($lang->survey_reminder_message, $invitee['displayName'], $survey['subject'], $surveylink);
                $mail = new Mailer($email_data, 'php');
            }
        }

        if($mail->get_status() === true) {
            output_xml("<status>true</status><message>{$lang->remindersent}</message>");
            exit;
        }
    }
    elseif($core->input['action'] == 'get_crosstabulation') {
        $survey = new Surveys($core->input['identifier'], false);

        $sharedsurvey = SurveyShares::get_data(array('sid' => $survey->sid, 'uid' => $core->user['uid']));

        if($survey->createdBy != $core->user['uid'] && !is_object($sharedsurvey)) {
            exit;
        }
        $sql = 'SELECT '.SurveysTplQuestions::PRIMARY_KEY.' FROM '.SurveysTplQuestions::TABLE_NAME.' WHERE type IN (SELECT '.SurveysQuestionTypes::PRIMARY_KEY.' FROM '.SurveysQuestionTypes::TABLE_NAME.' WHERE isQuantitative=1 AND hasChoices=1)';
        $sections_filters = SurveysTplSections::get_data(array(SurveysTemplates::PRIMARY_KEY => $survey->{SurveysTemplates::PRIMARY_KEY}), array('returnarray' => true, 'operators' => array(SurveysTemplates::PRIMARY_KEY => 'IN', 'type' => 'IN')));

        $questions = SurveysTplQuestions::get_data(array('type' => 'SELECT '.SurveysQuestionTypes::PRIMARY_KEY.' FROM '.SurveysQuestionTypes::TABLE_NAME.' WHERE isQuantitative=1 AND hasChoices=1', SurveysTplSections::PRIMARY_KEY => array_keys($sections_filters)), array('returnarray' => true, 'operators' => array('type' => 'IN')));
        $responses = SurveysResponses::get_data(array('sid' => $survey->sid, SurveysTplQuestions::PRIMARY_KEY => $sql), array('returnarray' => true, 'operators' => array(SurveysTplQuestions::PRIMARY_KEY => 'IN')));
        if(is_array($responses)) {
            foreach($responses as $id => $value) {
                $respcombinations[$value->stqid][$value->invitee] = $value->response;
            }
        }
        $questions2 = $questions;
        foreach($questions as $id => $question) {
            foreach($questions2 as $id2 => $question2) {
                if($question->get_id() == $question2->get_id()) {
                    continue;
                }
                $answers = $respcombinations[$id];
                $answers2 = $respcombinations[$id2];
                $output .= parse_crosstabulation($question, $question2, $answers, $answers2);
            }
        }
        output($output);
    }
}
function parse_crosstabulation($question, $question2, $answers, $answers2) {
    global $lang;
    $rows = $question->get_choices();
    $cols = $question2->get_choices();

    $output = '';
    foreach($rows as $rowid => $row) {
        foreach($cols as $colid => $col) {
            foreach($answers as $case => $ar1) {
                if($ar1 == $rowid && $answers2[$case] == $colid) {
                    $vals[$rowid][$colid] ++;
                }
            }
        }
    }

    $output .= '<table class="datatable" width="100%">';
    $output .= '<tr><td></td><td colspan="'.(count($cols) + 2).'">'.$question2->get_displayname().'</td></tr>';
    $output .= '<tr><td rowspan="'.(count($rows) + 1).'" style="width: 20%;">'.$question->get_displayname().'</td><td></td>';
    $colswidth = 60 / count($cols);
    foreach($cols as $col) {
        $output .= '<th style="width:'.$colswidth.'%;">'.$col.'</th>';
    }
    $output .= '<th>'.$lang->total.'Total</th>';
    $output .= '</tr>';
    foreach($rows as $rowid => $row) {
        $output .= '<tr><th>'.$row.'</th>';
        foreach($cols as $colid => $col) {
            if(!isset($vals[$rowid][$colid])) {
                $vals[$rowid][$colid] = 0;
            }
            $output .= '<td>'.$vals[$rowid][$colid].'</td>';
        }
        $output .= '<th>'.array_sum($vals[$rowid]).'</th>';
        $output .= '</tr>';
    }
    $output .= '</table><br />';

    return $output;
}

?>