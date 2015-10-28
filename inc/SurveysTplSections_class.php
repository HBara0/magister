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

}