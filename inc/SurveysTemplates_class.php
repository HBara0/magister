<?php
/* -------Definiton-START-------- */

class SurveysTemplates extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'stid';
    const TABLE_NAME = 'surveys_templates';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'stid';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'title';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $table_array = array(
                'title' => $data['title'],
                'category' => $data['category'],
                'isPublic' => $data['isPublic'],
                'forceAnonymousFilling' => $data['forceAnonymousFilling'],
                'createdBy' => $core->user['id'],
                'dateCreated' => $data['dateCreated'],
        );
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(is_array($data)) {
            $update_array['title'] = $data['title'];
            $update_array['category'] = $data['category'];
            $update_array['isPublic'] = $data['isPublic'];
            $update_array['forceAnonymousFilling'] = $data['forceAnonymousFilling'];
            $update_array['dateCreated'] = $data['dateCreated'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    public function get_questions($referrer = '') {
        global $db;

        $query = $db->query("SELECT *, sts.title AS section_title,sts.inputChecksum AS section_inputChecksum,sts.stsid AS section_id,sts.description as section_description, stq.description AS description
							FROM ".Tprefix."surveys_templates st
							JOIN ".Tprefix."surveys_templates_sections sts ON (sts.stid=st.stid)
							JOIN ".Tprefix."surveys_templates_questions stq ON (sts.stsid=stq.stsid)
							JOIN ".Tprefix."surveys_questiontypes sqt ON (sqt.sqtid=stq.type)
							WHERE st.stid={$this->data[stid]}
							ORDER BY sequence ASC");

        while($question = $db->fetch_assoc($query)) {
            $choices = array();
            if($question['hasChoices'] == 1) {
                $query2 = $db->query("SELECT * FROM ".Tprefix."surveys_templates_questions_choices WHERE stqid={$question[stqid]} ORDER BY stqcid ASC");
                while($choice = $db->fetch_assoc($query2)) {
                    $question['choices'][$choice['stqcid']] = $choice['choice'];
                    if($choice['hasMultipleValues'] == 1) {
                        $query3 = $db->query("SELECT * FROM ".Tprefix."surveys_templates_questionschoices_choices WHERE stqcid={$choice[stqcid]} ORDER BY stqcid ASC");
                        while($choicevalue = $db->fetch_assoc($query3)) {
                            $question['choicevalues'][$choicevalue['stqccid']] = $choicevalue['choice'];
                        }
                    }
                }
            }
            if(is_array($question['choicevalues'])) {
                $question['choicevalues'] = array_unique($question['choicevalues']);
            }
            $questions[$question['stsid']]['section_description'] = $question['section_description'];
            $questions[$question['stsid']]['section_title'] = $question['section_title'];
            $questions[$question['stsid']]['section_id'] = $question['section_id'];
            $questions[$question['stsid']]['section_inputChecksum'] = $question['section_inputChecksum'];
            $questions[$question['stsid']]['questions'][$question['stqid']] = $question;
        }

        return $questions;
    }

    public function parse_question(array $question, $secondary = false, array $response = array()) {
        $question_output_requiredattr = '';
        if($question['isRequired'] == 1) {
            $question_output_required = '<span class="red_text">*</span>';
            $question_output_requiredattr = ' required="required"';
        }

        if($secondary == true) {
            $question_output = '<div style="margin: 5px 0px 5px 20px; font-style:italic; ">'.$question['question'].'</div>';
        }
        else {
            if(isset($question['description'])) {
                $question_desc_output = '<div class="altrow2" style="margin-left:15px; font-style: italic;font-weight:normal;">'.$question['description'].'</div>';
            }

            $question_output = '<div  class="altrow2" style="padding-bottom:10px; padding-top:10px; font-weight: bold;">'.$question['sequence'].' - '.$question['question'].$question_output_required.$question_desc_output.'</div>';
        }

        switch($question['fieldType']) {
            case 'textbox':
                if($question['fieldSize'] == 0) {
                    $question['fieldSize'] = 50;
                }

                $question_output_inputaccept = '';
                if($question['validationType'] == 'numeric') {
                    $question_output_inputaccept = ' accept="numeric"';
                }

                if(is_array($question['choices']) && !empty($question['choices'])) {
                    if(!empty($response)) {
                        foreach($response as $values) {
                            $question_output .= '<div style="margin-left:20px;">'.$values['choice'].': '.$values['response'].'</div>';
                        }
                    }
                    else {
                        foreach($question['choices'] as $key => $choice) {
                            $question_output .= '<div style="margin-left:20px;">'.$choice.' <input type="text" id="answer_actual_'.$question['stqid'].'_'.$key.'" name="answer[actual]['.$question['stqid'].']['.$key.']" size="'.$question['fieldSize'].'"'.$question_output_inputaccept.$question_output_requiredattr.' /> <input type="hidden" id="answer_comments_'.$question['stqid'].'_'.$key.'" name="answer[comments]['.$question['stqid'].']['.$key.']" value="'.$key.'"/>'.$this->parse_validation($question).'</div>';
                        }
                    }
                }
                else /* Single textbox */ {
                    if(!empty($response)) {
                        $question_output .= '<div style="margin: 5px 20px; 5px; 20px;">'.$response['response'].'</div>';
                    }
                    else {
                        $question_output_idadd = '[actual]';
                        if($secondary == true) {
                            $question_output_idadd = '[comments]';
                        }

                        $question_output .= '<div style="margin: 5px 20px; 5px; 20px;"><input type="text" id="answer_'.$question_output_idadd.'_'.$question['stqid'].'" name="answer'.$question_output_idadd.'['.$question['stqid'].']" size="'.$question['fieldSize'].'"'.$question_output_inputaccept.$question_output_requiredattr.' /> '.$this->parse_validation($question).'</div>';
                    }
                }
                break;
            case 'selectlist':
                if(!empty($response)) {
                    if($question['hasMultiAnswers'] == 0) {
                        $question_output .= '<div style="margin: 5px 20px; 5px; 20px;">'.$response['choice'].'</div>';
                    }
                    else {
                        foreach($response as $attr => $value) {
                            $question_output_response[] .= $value['choice'];
                        }
                        $question_output .= '<div style="margin: 5px 20px; 5px; 20px;">'.implode('<br />', $question_output_response).'</div>';
                    }
                }
                else {
                    $question_output .= '<div style="margin: 5px 20px; 5px; 20px;"> '.parse_selectlist('answer[actual]['.$question['stqid'].'][]', $question['order'], $question['choices'], '', $question['hasMultiAnswers'], '', array('required' => $question['isRequired'])).'</div>';
                }
                break;
            case 'checkbox':
                if(!empty($response)) {
                    foreach($response as $attr => $value) {
                        $question_output_response[] .= $value['choice'];
                    }
                    $question_output .= '<div style="margin: 5px 20px; 5px; 20px;">'.implode(', ', $question_output_response).'</div>';
                }
                else {
                    $seperator = '&nbsp;&nbsp;';
                    if(!empty($question['choicesSeperator'])) {
                        $htmlents = array('space' => '&nbsp;&nbsp;', 'newline' => '<br/>');
                        $seperator = $htmlents[$question['choicesSeperator']];
                    }
                    $question_output .= '<div style="margin: 5px 20px; 5px; 20px;">'.parse_checkboxes('answer[actual]['.$question['stqid'].']', $question['choices'], '', true, '', $seperator).'</div>';
                }
                break;
            case 'radiobutton':
                if(!empty($response)) {
                    $question_output .= '<div style="margin: 5px 20px; 5px; 20px;">'.$response['choice'].'</div>';
                }
                else {
                    $seperator = '&nbsp;&nbsp;';
                    if(!empty($question['choicesSeperator'])) {
                        $htmlents = array('space' => '&nbsp;&nbsp;', 'newline' => '<br/>');
                        $seperator = $htmlents[$question['choicesSeperator']];
                    }
                    $question_output .= '<div style="margin: 5px 20px; 5px; 20px;">'.parse_radiobutton('answer[actual]['.$question['stqid'].']', $question['choices'], '', true, $seperator, array('required' => $question['isRequired'])).'</div>';
                }
                break;
            case 'textarea':
                if(!empty($response)) {
                    $question_output .= '<div style="margin: 5px 20px; 5px; 20px;">'.$response['response'].'</div>';
                }
                else {
                    $question_output_idadd = '[actual]';
                    if($secondary == true) {
                        $question_output_idadd = '[comments]';
                    }
                    $question_output .= '<div style="margin: 5px 20px; 5px; 20px;"><textarea id="answer_'.$question_output_idadd.'_'.$question['stqid'].'" name="answer'.$question_output_idadd.'['.$question['stqid'].']" cols="50" rows="'.$question['fieldSize'].'"'.$question_output_requiredattr.'></textarea> '.$this->parse_validation($question).'</div>';
                }
                break;
            case 'matrix':
                $question_output .= '<div style="margin: 5px 20px; 5px; 20px;"><table class="datatable">';
                $question_output .= '<tr><th><input type="hidden" name="answer[options]['.$question['stqid'].'][isMatrix]" value="1"/></th>';
                foreach($question['choicevalues'] as $choicevalue) {
                    $question_output .= '<th style="text-align:left;">'.$choicevalue.'</th>';
                }
                $question_output .='</tr>';
                foreach($question['choices'] as $choicekey => $choice) {
                    $question_output .='<tr><th>'.$choice.'</th>';
                    foreach($question['choicevalues'] as $valuekey => $choicevalue) {
                        $question_output .='<td style="text-align:left;"><input type="radio" name="answer[actual]['.$question['stqid'].']['.$choicekey.']" value="'.$choicekey.'_'.$valuekey.'" '.$checked.$required.'/></td>';
                    }
                    $question_output .='</tr>';
                }
                $question_output .='</table>';
                break;
            default:
                return false;
        }

        if($question['hasCommentsField'] == 1) {
            if(!empty($response)) {
                if(empty($response['comments'])) {
                    $response['comments'] = '-';
                }
                $question_output .= '<div style="margin: 5px 20px; 5px; 20px;">'.$question['commentsFieldTitle'].': '.$response['comments'].'</div>';
            }
            else {
                $question_output .= $this->parse_question(array('stqid' => $question['stqid'], 'question' => $question['commentsFieldTitle'], 'fieldType' => $question['commentsFieldType'], 'fieldSize' => $question['commentsFieldSize']), true);
            }
        }
        return $question_output;
    }

    private function parse_validation($question) {
        global $lang;
        switch($question['validationType']) {
            case 'numeric':
                $note = $lang->numbersonly;
                break;
            case 'minchars':
            case 'maxchars':
                $note = $lang->sprint($lang->{$question['validationType']}, $question['validationCriterion']);
                break;
            case 'email':
                $note = $lang->emailonly;
                break;
            default: return false;
        }
        return '<span class="smalltext" style="font-style:italic;">('.$note.')</span>';
    }

    public function get_displayname() {
        if($this->data ['isQuiz'] == 1) {
            return 'quiz - '.$this->data[self::DISPLAY_NAME];
        }
        else {
            return $this->data[self::DISPLAY_NAME];
        }
    }

    public function get_questionchoices($stqid) {
        global $db;
        $question = SurveysTplQuestions::get_data(array('stqid' => $stqid), array('simple' => false));
        if(is_object($question)) {
            $question = $question->get();
            $qtype_obj = SurveysQuestionTypes::get_data(array('sqtid' => $question['type']));
            $question['hasChoices'] = $qtype_obj->hasChoices;
            $question['isMatrix'] = $qtype_obj->isMatrix;
            if($question['hasChoices'] == 1) {
                $query2 = $db->query("SELECT * FROM ".Tprefix."surveys_templates_questions_choices WHERE stqid={$question[stqid]} ORDER BY stqcid ASC");
                while($choice = $db->fetch_assoc($query2)) {
                    $question['choices'][$choice['stqcid']]['choice'] = $choice['choice'];
                    if($choice['hasMultipleValues'] == 1) {
                        $query3 = $db->query("SELECT * FROM ".Tprefix."surveys_templates_questionschoices_choices WHERE stqcid={$choice[stqcid]} ORDER BY stqcid ASC");
                        while($choicevalue = $db->fetch_assoc($query3)) {
                            if(is_array($choices)) {
                                if(!in_array($choicevalue['choice'], $choices)) {
                                    $question['choicevalues'][$choicevalue['stqccid']]['choice'] = $choicevalue['choice'];
                                    $choices[] = $choicevalue['choice'];
                                }
                            }
                            else {
                                $question['choicevalues'][$choicevalue['stqccid']]['choice'] = $choicevalue['choice'];
                                $choices[] = $choicevalue['choice'];
                            }
                            if(is_array($values)) {
                                if(!in_array($choicevalue['value'], $values)) {
                                    $question['choicevalues'][$choicevalue['stqccid']]['value'] = $choicevalue['value'];
                                    $values[] = $choicevalue['value'];
                                }
                            }
                            else {
                                $question['choicevalues'][$choicevalue['stqccid']]['value'] = $choicevalue['value'];
                                $values[] = $choicevalue['value'];
                            }
                        }
                    }
                    else {
                        $question['choices'][$choice['stqcid']]['value'] = $choice['value'];
                    }
                    if($choice['isAnswer'] == 1) {
                        $question['choicevalues'][$choicevalue['stqccid']]['isAnswer'] = 1;
                    }
                }
            }
        }
        $data['choices'] = $question['choices'];
        $data['choicevalues'] = $question['choicevalues'];
        $data['answers'] = $question['choicevalues'];
        if($question['isMatrix'] == 1) {
            $data['choices'] = $question['choicevalues'];
            $data['choicevalues'] = $question['choices'];
        }
        return $data;
    }

    public function template_used() {
        global $db;
        $query = $db->query("SELECT sid FROM ".Tprefix."surveys WHERE stid=".$this->data[self::PRIMARY_KEY]);
        if($query && $db->num_rows($query) > 0) {
            return true;
        }
        return false;
    }

}