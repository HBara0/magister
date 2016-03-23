<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Create Survey
 * $module: createsurveytemplate
 * $id: createsurvey_template.php
 * Created By: 		@tony.assaad		June 11, 2012 | 1:00 PM
 * Last Update: 	@tony.assaad		July 01, 2012, 2012 | 12:55 PM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['surveys_canCreateSurvey'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$lang->load('surveys_createtemplate');
if(!$core->input['action']) {
    $action = 'createtemplate';
    $section_rowid = 1;
    $question_rowid = 1;
    $sequence = 1;
    if(isset($core->input['bstid']) && !empty($core->input['bstid'])) {
        $stid = $core->input['bstid'];
        $activate_questionsordering = '$("tbody[id^=\'questions\'][id$=\'_tbody\']").sortable({placeholder: "ui-state-highlight", forcePlaceholderSize: true, delay: 300, opacity: 0.5, containment: "parent", handle: \'.questions-sort-icon\'});';
        $template_obj = SurveysTemplates::get_data(array('stid' => $stid));
        $title = $template_obj->title;
        $survey_template = $template_obj->get();
        $questions = $template_obj->get_questions();
        if(is_array($questions)) {
            foreach($questions as $section) {
                foreach($section['questions'] as $question) {
                    $showanswer = 'display:none;';
                    if($core->input['ajaxaddmoredata']['type'] > 0) {
                        $showanswer = '';
                    };
                    if(is_array($question)) {
                        $style['choicesdisplay'] = $style['matrixchoicesdisplay'] = 'style="display:none;"';
                        unset($question['choices'], $question['choicevalues']);
                        $quest_choices = $template_obj->get_questionchoices($question['stqid']);
                        $question['choices'] = $quest_choices['choices'];
                        $question['choicevalues'] = $quest_choices['choicevalues'];
                        $question['answers'] = $quest_choices['answers'];
                        foreach($question as $questionfield => $value) {
                            $section[$section_rowid]['questions'][$question_rowid][$questionfield] = $value;
                            if($questionfield == 'choices' && is_array($value)) {
                                $choicesrowid_rowid = 1;
                                $style['choicesdisplay'] = 'style="display:block;"';
                                foreach($value as $choicevalue) {
                                    $section[$section_rowid]['questions'][$question_rowid]['choices'][$choicesrowid_rowid]['choice'] = $choicevalue['choice'];
                                    $section[$section_rowid]['questions'][$question_rowid]['choices'][$choicesrowid_rowid]['value'] = $choicevalue['value'];
                                    $choice_selected = "";
                                    if(isset($choicevalue['isAnswer']) && $choicevalue['isAnswer'] == 1) {
                                        $choice_selected = 'checked="checked"';
                                    }
                                    eval("\$choices .= \"".$template->get('surveys_createtemplate_sectionrow_questionrow_choicerow')."\";");
                                    $choicesrowid_rowid++;
                                }
                            }
                            if($question['isMatrix'] == 1 && $questionfield == 'choicevalues' && is_array($value)) {
                                $matrixchoicesrowid_rowid = 1;
                                foreach($value as $matrixchoicevalue) {
                                    $section[$section_rowid]['questions'][$question_rowid]['matrixchoices'][$matrixchoicesrowid_rowid]['choice'] = $matrixchoicevalue['choice'];
                                    $section[$section_rowid]['questions'][$question_rowid]['matrixchoices'][$matrixchoicesrowid_rowid]['value'] = $matrixchoicevalue['value'];

                                    $style['matrixchoicesdisplay'] = 'style="display:block;"';
                                    eval("\$matrixchoices .= \"".$template->get('surveys_createtemplate_sectionrow_questionrow_matrixchoicerow')."\";");
                                    $matrixchoicesrowid_rowid++;
                                }
                            }
                        }
                    }
                    $style['hasvlidationdisplay'] = $style['validationcriteriadisplay'] = 'style="display:none;"';
                    if($question['hasValidation'] == 1) {
                        $style['hasvlidationdisplay'] = 'style="display:block;"';
                        if(!empty($question['validationType'])) {
                            $selectedvtype[$question['validationType']] = 'selected="selected"';
                        }
                        if(!empty($question['validationCriterion'])) {
                            $section[$section_rowid][questions][$question_rowid][validationCriterion] = $question['validationCriterion'];
                            $style['validationcriteriadisplay'] = 'style="display:block;"';
                        }
                    }
                    if($question['isSizable'] == 1) {
                        $style['fieldsizedisplay'] = 'style="display:block;"';
                        $section[$section_rowid][questions][$question_rowid]['fieldSize'] = $question['fieldSize'];
                    }
                    $radiobuttons['isRequired'] = parse_yesno("section[$section_rowid][questions][$question_rowid][isRequired]", 1, $question['isRequired']);

                    $fieldtype = get_specificdata('surveys_questiontypes', array('name', 'sqtid'), 'sqtid', 'name', '', 0);
                    $desctype = get_specificdata('surveys_questiontypes', array('sqtid', 'description'), 'sqtid', 'description', '', 0);
                    $question_types_options = "<option value=''></option>";
                    foreach($fieldtype as $key => $formatvalue) {
                        $fieldtype[$key] = $formatvalue;
                        if(!empty($desctype[$key])) {
                            $fieldtype[$key] .= '  ('.$desctype[$key].')';
                        }
                        $selected = "";
                        if($key == $question['type']) {
                            $selected = "selected='selected'";
                        }
                        $question_types_options .= "<option value='{$key}' {$selected}>{$fieldtype[$key]}</option>";
                    }

                    $altrow_class = alt_row($altrow_class);
                    $showanswer = 'display:none;';
                    if($survey_template['isQuiz'] == 1) {
                        $showanswer = '';
                    }
                    $sepertorslist = array('space' => 'Space', 'newline' => 'New-Line');
                    $seperatorselectlist = parse_selectlist("section[{$section_rowid}][questions][{$question_rowid}][choicesSeperator]", '', $sepertorslist, '');

                    eval("\$newquestions .= \"".$template->get('surveys_createtemplate_sectionrow_questionrow')."\";");
                    unset($matrixchoices, $choices, $selectedvtype);
                    $question_rowid++;
                }
                $section['section_inputChecksum'] = generate_checksum();
                eval("\$newsection .= \"".$template->get('surveys_createtemplate_sectionrow')."\";");
                unset($newquestions);
                $section_rowid++;
            }
        }
    }
    else {
        $style['validationcriteriadisplay'] = $style['hasvlidationdisplay'] = 'style="display:none;"';
        $style['choicesdisplay'] = $style['matrixchoicesdisplay'] = 'style="display:none;"';
        $style['fieldsizedisplay'] = 'style="display:none;"';


        $radiobuttons['isRequired'] = parse_yesno("section[$section_rowid][questions][$question_rowid][isRequired]", 1, '');
        $fieldtype = get_specificdata('surveys_questiontypes', array('name', 'sqtid'), 'sqtid', 'name', '', 0);
        $desctype = get_specificdata('surveys_questiontypes', array('sqtid', 'description'), 'sqtid', 'description', '', 0);
        $question_types_options = "<option value=''></option>";
        foreach($fieldtype as $key => $formatvalue) {
            $fieldtype[$key] = $formatvalue;
            if(!empty($desctype[$key])) {
                $fieldtype[$key] .= '  ('.$desctype[$key].')';
            }

            $question_types_options .= "<option value='{$key}'{$selected}>{$fieldtype[$key]}</option>";
        }


        $altrow_class = alt_row($altrow_class);
        $choicesrowid_rowid = $matrixchoicesrowid_rowid = 1;
        $showanswer = 'display:none;';
        if($survey_template['isQuiz'] == 1) {
            $showanswer = '';
        }
        $sepertorslist = array('space' => 'Space', 'newline' => 'New-Line');
        $seperatorselectlist = parse_selectlist("section[{$section_rowid}][questions][{$question_rowid}][choicesSeperator]", '', $sepertorslist, '');
        $section['section_inputChecksum'] = generate_checksum();
        eval("\$matrixchoices = \"".$template->get('surveys_createtemplate_sectionrow_questionrow_matrixchoicerow')."\";");
        eval("\$choices = \"".$template->get('surveys_createtemplate_sectionrow_questionrow_choicerow')."\";");
        eval("\$newquestions = \"".$template->get('surveys_createtemplate_sectionrow_questionrow')."\";");
        eval("\$newsection = \"".$template->get('surveys_createtemplate_sectionrow')."\";");
    }

    $radiobuttons['isPublic'] = parse_yesno('isPublic', 1, $survey_template['isPublic']);
    $radiobuttons['forceAnonymousFilling'] = parse_yesno('forceAnonymousFilling', 1, $survey_template['forceAnonymousFilling']); //$lang->forceanonymousfilling_tip);
    $radiobuttons['isQuiz'] = parse_yesno('isQuiz', 1, $survey_template['isQuiz']);
    $surveycategories = get_specificdata('surveys_categories', array('scid', 'title'), 'scid', 'title', 'title');
    $surveycategories_list = parse_selectlist('category', 5, $surveycategories, $survey_template['category']);


    $sequence_id += 1;
    eval("\$surveys_createtemplate = \"".$template->get('surveys_createtemplate')."\";");
    output_page($surveys_createtemplate);
}
else {

    if($core->input['action'] == 'createtemplate') {
        $survey = new Surveys();
        $survey->create_survey_template($core->input);

        switch($survey->get_status()) {
            case 0:
                if($core->input['preview'] == 1) {
                    output_xml('<status>true</status><message>'.$lang->successfullysaved.'<![CDATA[<script>$(\'#preview\').val(\'0\');window.open(\''.$core->settings['rootdir'].'/index.php?module=surveys/preview&stid='.$survey->stid.'\')</script>]]></message>');
                }
                else {
                    output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                }
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                break;
            case 2:
                output_xml("<status>false</status><message>{$lang->surveytemplateexists}</message>");
                break;
            case 3:
                output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                break;
            case 4:
                output_xml("<status>false</status><message>{$lang->duplicationsectionname}</message>");
                break;
            case 5:
                output_xml("<status>false</status><message>{$lang->duplicationquestionname}</message>");
                break;
            case 6:
                output_xml("<status>false</status><message>{$lang->incorrechoices}</message>");
                break;
        }
    }
    elseif($core->input['action'] == 'parsetype') {
        /* Get validation of the question - START */
        $style['choicesdisplay'] = $style['matrixchoicesdisplay'] = $style['fieldsizedisplay'] = 'style="display:none;"';

        $section_id = $core->input['sectionid'];
        $question_id = $core->input['questionid'];
        $query = $db->query("SELECT * FROM ".Tprefix."surveys_questiontypes sqt
						WHERE sqt.sqtid = ".$db->escape_string($core->input['questiontype'])."");

        $questiontypes = $db->fetch_assoc($query);

        header('Content-type: text/javascript');
        if($questiontypes['isSizable'] == 1) {
            echo '$("tr[id=\'section'.$section_id.'[questions]'.$question_id.'[fieldSize_container]\']").css("display","table-row");';
        }
        else {
            echo '$("tr[id=\'section'.$section_id.'[questions]'.$question_id.'[fieldSize_container]\']").css("display","none");';
        }
        if($questiontypes['hasChoices'] == 1) {
            echo '$("tr[id=\'section'.$section_id.'[questions]'.$question_id.'[choices_container]\']").css("display","table-row");';
        }
        else {
            echo '$("tr[id=\'section'.$section_id.'[questions]'.$question_id.'[choices_container]\']").css("display","none");';
        }
        if($questiontypes['hasValidation'] == 1) {
            echo '$("tr[id=\'section'.$section_id.'[questions]'.$question_id.'[validationType_container]\']").css("display","table-row");';
        }
        else {
            echo '$("tr[id=\'section'.$section_id.'[questions]'.$question_id.'[validationType_container]\']").css("display","none");';
        }
        if($questiontypes['isMatrix'] == 1) {
            echo '$("tr[id=\'section'.$section_id.'[questions]'.$question_id.'[matrixchoices_container]\']").css("display","table-row");';
        }
        else {
            echo '$("tr[id=\'section'.$section_id.'[questions]'.$question_id.'[matrixchoices_container]\']").css("display","none");';
        }
        exit;
    }
    elseif($core->input['action'] == 'ajaxaddmore_section') {
        $question_rowid = 1;
        $style['choicesdisplay'] = $style['matrixchoicesdisplay'] = $style['validationcriteriadisplay'] = $style['hasvlidationdisplay'] = 'style="display:none;"';
        $section_rowid = $db->escape_string($core->input['value']) + 1;
        $sequence = $db->escape_string($core->input['sequence']) + 1;
        $fieldtype = get_specificdata('surveys_questiontypes', array('name', 'sqtid'), 'sqtid', 'name', '', 0);
        $desctype = get_specificdata('surveys_questiontypes', array('sqtid', 'description'), 'sqtid', 'description', '', 0);
        $question_types_options = "<option value=''></option>";
        foreach($fieldtype as $key => $formatvalue) {
            $fieldtype[$key] = $formatvalue;
            if(!empty($desctype[$key])) {
                $fieldtype[$key] = $formatvalue.'  ('.$desctype[$key].')';
            }
            $question_types_options .= "<option value='{$key}'{$selected}>{$fieldtype[$key]}</option>";
        }
        $choicesrowid_rowid = $matrixchoicesrowid_rowid = 1;
        $showanswer = 'display:none;';
        if($core->input['ajaxaddmoredata']['type'] > 0) {
            $showanswer = '';
            $type = $core->input['ajaxaddmoredata']['type'];
        };
        $section['section_inputChecksum'] = generate_checksum();
        $sepertorslist = array('space' => 'Space', 'newline' => 'New-Line', 'tab' => 'Tab');
        $seperatorselectlist = parse_selectlist("section[{$section_rowid}][questions][{$question_rowid}][choicesSeperator]", '', $sepertorslist, '');
        $radiobuttons['isRequired'] = parse_yesno('section['.$section_rowid.'][questions]['.$question_rowid.'][isRequired]', 1, $survey_template['isRequired']);
        eval("\$matrixchoices = \"".$template->get('surveys_createtemplate_sectionrow_questionrow_matrixchoicerow')."\";");
        eval("\$choices = \"".$template->get('surveys_createtemplate_sectionrow_questionrow_choicerow')."\";");
        $style['fieldsizedisplay'] = 'style="display:none;"';
        eval("\$newquestions = \"".$template->get('surveys_createtemplate_sectionrow_questionrow')."\";");
        eval("\$newsection = \"".$template->get('surveys_createtemplate_sectionrow')."\";");
        echo $newsection;
    }
    elseif($core->input['action'] == 'ajaxaddmore_questions') {
        $style['hasvlidationdisplay'] = $style['validationcriteriadisplay'] = 'style="display:none;"';
        $style['choicesdisplay'] = $style['matrixchoicesdisplay'] = 'style="display:none;"';
        $question_rowid = $db->escape_string($core->input['value']) + 1;
        $section_rowid = $db->escape_string($core->input['id']);
        $sequence = $db->escape_string($core->input['value']) + 1;
        $fieldtype = get_specificdata('surveys_questiontypes', array('name', 'sqtid'), 'sqtid', 'name', '', 0);
        $desctype = get_specificdata('surveys_questiontypes', array('sqtid', 'description'), 'sqtid', 'description', '', 0);
        $question_types_options = "<option value=''></option>";
        foreach($fieldtype as $key => $formatvalue) {
            $fieldtype[$key] = $formatvalue;
            if(!empty($desctype[$key])) {
                $fieldtype[$key] = $formatvalue.'  ('.$desctype[$key].')';
            }
            $question_types_options .= "<option value='{$key}'{$selected}>{$fieldtype[$key]}</option>";
        }
        $radiobuttons['isRequired'] = parse_yesno('section['.$section_rowid.'][questions]['.$question_rowid.'][isRequired]', 1, $survey_template['isRequired']);
        $choicesrowid_rowid = $matrixchoicesrowid_rowid = 1;
        $showanswer = 'display:none;';
        if($core->input['ajaxaddmoredata']['type'] > 0) {
            $showanswer = '';
            $type = $core->input['ajaxaddmoredata']['type'];
        };
        $sepertorslist = array('space' => 'Space', 'newline' => 'New-Line', 'tab' => 'Tab');
        $seperatorselectlist = parse_selectlist("section[{$section_rowid}][questions][{$question_rowid}][choicesSeperator]", '', $sepertorslist, '');
        eval("\$matrixchoices = \"".$template->get('surveys_createtemplate_sectionrow_questionrow_matrixchoicerow')."\";");
        eval("\$choices = \"".$template->get('surveys_createtemplate_sectionrow_questionrow_choicerow')."\";");
        $style['fieldsizedisplay'] = 'style="display:none;"';
        eval("\$newquestion = \"".$template->get('surveys_createtemplate_sectionrow_questionrow')."\";");
        echo $newquestion;
    }
    elseif($core->input['action'] == 'ajaxaddmore_questionschoices') {
        $style['matrixchoicesdisplay'] = 'style="display:none;"';
        $choicesrowid_rowid = $db->escape_string($core->input['value']) + 1;
        $section_rowid = intval($core->input['ajaxaddmoredata']['sectionrowid']);
        $question_rowid = intval($core->input['ajaxaddmoredata']['questionrowid']);
        $showanswer = 'display:none;';
        if($core->input['ajaxaddmoredata']['type'] > 0) {
            $showanswer = '';
        };
        eval("\$choices = \"".$template->get('surveys_createtemplate_sectionrow_questionrow_choicerow')."\";");
        echo $choices;
    }
    elseif($core->input['action'] == 'ajaxaddmore_matrixquestionschoices') {
        // $style['matrixchoicesdisplay'] = 'style="display:none;"';

        $matrixchoicesrowid_rowid = $db->escape_string($core->input['value']) + 1;
        $section_rowid = intval($core->input['ajaxaddmoredata']['sectionrowid']);
        $question_rowid = intval($core->input['ajaxaddmoredata']['questionrowid']);
        $showanswer = 'display:none;';
        if($core->input['ajaxaddmoredata']['type'] > 0) {
            $showanswer = '';
        };
        eval("\$matrixchoices = \"".$template->get('surveys_createtemplate_sectionrow_questionrow_matrixchoicerow')."\";");
        echo $matrixchoices;
    }
    elseif($core->input['action'] == 'get_createbasedonanother') {
        $surveystemplates = SurveysTemplates::get_data('isActive = 1 AND (isPublic=1 OR createdBy='.$core->user['uid'].')', array('order' => array('by' => array('isQuiz', 'title'), 'sort' => array('sort' => array('isQuiz' => 'DESC', 'title' => 'ASC'))), 'returnarray' => true));
        if(is_array($surveystemplates)) {
            $surveytemplates_list = parse_selectlist('stid', 5, $surveystemplates, $survey['stid'], '', $onchange, array('id' => 'stid'));
        }
        eval("\$createbasedonanothertpl = \"".$template->get('popup_createbasedonanothertpl')."\";");
        output($createbasedonanothertpl);
    }
    elseif($core->input['action'] == 'createbasedonanother') {
        $stid = $core->input['stid'];
        $action = 'createtemplate';
        $surveytemplate = SurveysTemplates::get_data(array('stid' => $stid));
        $titlee = $surveytemplate->title;
//        eval("\$surveys_createtemplate = \"".$template->get('surveys_createtemplate')."\";");
//        output($surveys_createtemplate);
        redirect('index.php?module=surveys/createsurveytemplate&stid='.$stid);
    }
    elseif($core->input['action'] == 'delete_section') {
        if(isset($core->input['sectionid']) && !empty($core->input['sectionid'])) {
            $section = new SurveysTplSections(intval($core->input['sectionid']));
        }
        elseif(isset($core->input['checksum']) && !empty($core->input['checksum'])) {
            $section = SurveysTplSections::get_data(array('inputChecksum' => $db->escape_string($core->input['checksum'])), array('returnarray' => false));
        }
        if(is_object($section) && !empty($section->{SurveysTplSections::PRIMARY_KEY})) {
            if($section->section_used()) {
                echo('<span style="color:red">'.$lang->sectiontemplatealreadyused.'</span>');
                exit;
            }
            $section->delete();
        }

        $result = '<script> $(function() { $(\'tr[id="section_'.$core->input['rowid'].'"]\').remove();});</script>';
        echo($result);
    }
}
?>