<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: TravelManagerPlanTranspClass_class.php
 * Created:        @rasha.aboushakra    Apr 8, 2015 | 1:46:58 PM
 * Last Update:    @rasha.aboushakra    Apr 8, 2015 | 1:46:58 PM
 */

class TravelManagerPlanTranspClass extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'tmptc';
    const TABLE_NAME = 'travelmanager_plan_transpclass';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = 'tmptc,name,title';
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

    public function get_displayname() {
        return $this->data[self::DISPLAY_NAME];
    }

}