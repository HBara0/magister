<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: SurveysTplSections_class.php
 * Created:        @zaher.reda    Oct 27, 2015 | 11:11:59 PM
 * Last Update:    @zaher.reda    Oct 27, 2015 | 11:11:59 PM
 */

/**
 * Description of SurveysTplSections_class
 *
 * @author zaher.reda
 */
class SurveysTplSections extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'stsid';
    const TABLE_NAME = 'surveys_templates_sections';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = '';
    const REQUIRED_ATTRS = '';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function get_template($simple = false) {
        return new SurveysTemplates($this->{SurveysTemplates::PRIMARY_KEY}, $simple);
    }

    protected function update(array $data) {

    }

    public function section_used() {
        $template = $this->get_template();
        if(is_object($template)) {
            if($template->template_used()) {
                return true;
            }
        }
        return false;
    }

    public function delete() {
        $sectionquestions = SurveysTplQuestions::get_data(array(self::PRIMARY_KEY => $this->data[self::PRIMARY_KEY]), array('returnarray' => true));
        if(is_array($sectionquestions)) {
            foreach($sectionquestions as $question) {
                $question->delete();
            }
        }
        parent::delete();
    }

}