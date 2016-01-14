<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: preview.php
 * Created:        @hussein.barakat    Jul 14, 2015 | 4:15:32 PM
 * Last Update:    @hussein.barakat    Jul 14, 2015 | 4:15:32 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {

    if($core->input['identifier']) {
        $surveys = new Surveys($db->escape_string($core->input['identifier']));
        if(!is_object($surveys)) {
            redirect('index.php?module=surveys/list');
        }
        $survey = $surveys->get();
        $stid = $survey['stid'];
    }
    else {
        if($core->input['stid']) {
            $stid = intval($core->input['stid']);
        }
    }
    if(isset($stid) && !empty($stid)) {
        $template_obj = new SurveysTemplates($stid);
        if(!is_object($template_obj)) {
            redirect('index.php?module=surveys/list');
        }
        $templates = $template_obj->get();
        $questions = $template_obj->get_questions();
        if(!is_array($questions)) {
            redirect('index.php?module=surveys/list');
        }
        foreach($questions as $section) {
            if(!is_empty($section['section_title'], $section['section_description'])) {
                $questions_list .= '<div class="subtitle" style="margin-top:20px; border-top: thin solid #E8E8E8;">-'.$section['section_title'].'<br/>'.$section['section_description'].'</div>';
            }
            foreach($section['questions'] as $question) {
                $questions_list .= $template_obj->parse_question($question);
            }
        }
        eval("\$previewtemp = \"".$template->get('surveys_previewtemplate')."\";");
        output_page($previewtemp);
    }
    else {
        redirect('index.php?module=surveys/list');
    }
}