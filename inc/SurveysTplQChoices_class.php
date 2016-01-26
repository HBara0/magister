<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: SurveysTplQChoices_class.php
 * Created:        @zaher.reda    Oct 27, 2015 | 10:19:47 PM
 * Last Update:    @zaher.reda    Oct 27, 2015 | 10:19:47 PM
 */

/**
 * Description of SurveysTplQChoices_class
 *
 * @author zaher.reda
 */
class SurveysTplQChoices extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'stqcid';
    const TABLE_NAME = 'surveys_templates_questions_choices';
    const DISPLAY_NAME = 'choice';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = null;
    const REQUIRED_ATTRS = '';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function get_question() {
        return new SurveysTplQuestions($this->{SurveysTplQuestions::PRIMARY_KEY});
    }

    protected function update(array $data) {

    }

    public function delete() {
        $sectionquestionschoiceschoices = SurveysTplQChoiceChoices::get_data(array(self::PRIMARY_KEY => $this->data[self::PRIMARY_KEY]), array('returnarray' => true));
        if(is_array($sectionquestionschoiceschoices)) {
            foreach($sectionquestionschoiceschoices as $choice) {
                $choice->delete();
            }
        }
        parent::delete();
    }

}