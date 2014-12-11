<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Positions_class.php
 * Created:        @rasha.aboushakra    Dec 10, 2014 | 10:01:26 AM
 * Last Update:    @rasha.aboushakra    Dec 10, 2014 | 10:01:26 AM
 */

class Positions extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'posid';
    const TABLE_NAME = 'positions';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = 'posid,name,title';
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