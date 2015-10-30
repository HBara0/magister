<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: SurveysQuestionTypes_class.php
 * Created:        @zaher.reda    Oct 27, 2015 | 10:22:44 PM
 * Last Update:    @zaher.reda    Oct 27, 2015 | 10:22:44 PM
 */

/**
 * Description of SurveysQuestionTypes_class
 *
 * @author zaher.reda
 */
class SurveysQuestionTypes extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'sqtid';
    const TABLE_NAME = 'surveys_questiontypes';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = null;
    const REQUIRED_ATTRS = '';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function update(array $data) {

    }

}