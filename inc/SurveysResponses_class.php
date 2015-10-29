<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: SurveysResponses_class.php
 * Created:        @zaher.reda    Oct 27, 2015 | 10:06:29 PM
 * Last Update:    @zaher.reda    Oct 27, 2015 | 10:06:29 PM
 */

/**
 * Description of SurveysResponses_class
 *
 * @author zaher.reda
 */
class SurveysResponses extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'srid';
    const TABLE_NAME = 'surveys_responses';
    const DISPLAY_NAME = 'identifier';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'sid,stqid,invitee';
    const REQUIRED_ATTRS = '';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function get_survey() {
        return new Surveys($this->sid);
    }

    public function get_question() {
        return new SurveysTplQuestions($this->{SurveysTplQuestions::PRIMARY_KEY});
    }

    public function get_invitee() {

    }

    public function get_response() {

    }

    protected function update(array $data) {

    }

}