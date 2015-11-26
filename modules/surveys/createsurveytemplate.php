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

    $radiobuttons['isPublic'] = parse_yesno('isPublic', 1, $survey_template['isPublic']);
    $radiobuttons['forceAnonymousFilling'] = parse_yesno('forceAnonymousFilling', 1, $lang->forceanonymousfilling_tip);
    $radiobuttons['isQuiz'] = parse_yesno('isQuiz', 1, $survey_template['isQuiz']);

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

    $surveycategories = get_specificdata('surveys_categories', array('scid', 'title'), 'scid', 'title', 'title');
    $surveycategories_list = parse_selectlist('category', 5, $surveycategories, $survey_template['category']);

    $altrow_class = alt_row($altrow_class);
    $choicesrowid_rowid = 1;
    $showanswer = 'display:none;';
    if($survey_template['isQuiz'] == 1) {
        $showanswer = '';
    }
    eval("\$choices = \"".$template->get('surveys_createtemplate_sectionrow_questionrow_choicerow')."\";");
    eval("\$newquestions = \"".$template->get('surveys_createtemplate_sectionrow_questionrow')."\";");
    eval("\$newsection = \"".$template->get('surveys_createtemplate_sectionrow')."\";");
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
        exit;
    }
    elseif($core->input['action'] == 'ajaxaddmore_section') {
        $question_rowid = 1;
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
        $choicesrowid_rowid = 1;
        $showanswer = 'display:none;';
        if($core->input['ajaxaddmoredata']['type'] > 0) {
            $showanswer = '';
            $type = $core->input['ajaxaddmoredata']['type'];
        };
        $radiobuttons['isRequired'] = parse_yesno('section['.$section_rowid.'][questions]['.$question_rowid.'][isRequired]', 1, $survey_template['isRequired']);
        eval("\$newquestions = \"".$template->get('surveys_createtemplate_sectionrow_questionrow')."\";");
        eval("\$newsection = \"".$template->get('surveys_createtemplate_sectionrow')."\";");
        echo $newsection;
    }
    elseif($core->input['action'] == 'ajaxaddmore_questions') {
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
        $choicesrowid_rowid = 1;
        $showanswer = 'display:none;';
        if($core->input['ajaxaddmoredata']['type'] > 0) {
            $showanswer = '';
            $type = $core->input['ajaxaddmoredata']['type'];
        };
        eval("\$choices = \"".$template->get('surveys_createtemplate_sectionrow_questionrow_choicerow')."\";");
        eval("\$newquestion = \"".$template->get('surveys_createtemplate_sectionrow_questionrow')."\";");
        echo $newquestion;
    }
    elseif($core->input['action'] == 'ajaxaddmore_questionschoices') {
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
}
?>