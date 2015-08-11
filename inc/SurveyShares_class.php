<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: SurveyShares_class.php
 * Created:        @rasha.aboushakra    Aug 10, 2015 | 3:58:30 PM
 * Last Update:    @rasha.aboushakra    Aug 10, 2015 | 3:58:30 PM
 */

class SurveyShares extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'sswid';
    const TABLE_NAME = 'surveys_sharedwith';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {

    }

    public function save(array $data = array()) {

    }

    protected function update(array $data) {

    }

}