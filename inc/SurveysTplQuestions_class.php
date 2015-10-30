<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: SurveysTemplatesQuestions_class.php
 * Created:        @zaher.reda    Oct 27, 2015 | 10:10:55 PM
 * Last Update:    @zaher.reda    Oct 27, 2015 | 10:10:55 PM
 */

/**
 * Description of SurveysTemplatesQuestions_class
 *
 * @author zaher.reda
 */
class SurveysTplQuestions extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'stqid';
    const TABLE_NAME = 'surveys_templates_questions';
    const DISPLAY_NAME = 'question';
    const SIMPLEQ_ATTRS = 'stqid, stsid, question, type';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = '';
    const REQUIRED_ATTRS = '';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function get_section() {

    }

    public function get_choices() {
        return SurveysTplQChoices::get_data(array(SurveysTplQuestions::PRIMARY_KEY => $this->{SurveysTplQuestions::PRIMARY_KEY}), array('returnarray' => true));
    }

    public function get_type() {
        return new SurveysQuestionTypes($this->type);
    }

    protected function update(array $data) {

    }

}