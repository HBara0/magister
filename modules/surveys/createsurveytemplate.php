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

/* if($core->usergroup['surveys_canCreateTemplates'] == 0) {
  error($lang->sectionnopermission);
  exit;
  } */

$lang->load('surveys_createtemplate');
if(!$core->input['action']) {
	$action = 'createsurveytemplate';
	$section_rowid = 1;
	$question_rowid = 1;
	$sequence = 1;

	$radiobuttons['isPublic'] = parse_yesno('isPublic', 1, $survey_template['isPublic']);
	$radiobuttons['forceAnonymousFilling'] = parse_yesno('forceAnonymousFilling', 1, $lang->forceAnonymousFilling_tip);

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

	eval("\$newquestions = \"".$template->get('surveys_createtemplate_sectionrow_questionrow')."\";");
	eval("\$newsection = \"".$template->get('surveys_createtemplate_sectionrow')."\";");

	$sequence_id += 1;
	eval("\$surveys_createtemplate = \"".$template->get('surveys_createtemplate')."\";");
	output_page($surveys_createtemplate);
}
else {

	if($core->input['action'] == 'createsurveytemplate') {
		$survey = new Surveys();
		$survey->create_survey_template($core->input);

		switch($survey->get_status()) {
			case 0:
				output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
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
		}
		//redirect("index.php?module=surveys/createsurveytemplate");
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
		$question_types_options="<option value=''></option>";
		foreach($fieldtype as $key => $formatvalue) {
			$fieldtype[$key] = $formatvalue;
			if(!empty($desctype[$key])) {
				$fieldtype[$key] = $formatvalue.'  ('.$desctype[$key].')';
			}
			$question_types_options .= "<option value='{$key}'{$selected}>{$fieldtype[$key]}</option>";
		}
		$radiobuttons['isRequired'] = parse_yesno('section['.$section_rowid.'][questions]['.$question_rowid.'][isRequired]', 1, $survey_template['isRequired']);


		eval("\$newquestion = \"".$template->get('surveys_createtemplate_sectionrow_questionrow')."\";");
		echo $newquestion;
	}
}
?>