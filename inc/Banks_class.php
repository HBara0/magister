<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Banks_class.php
 * Created:        @rasha.aboushakra    Nov 5, 2014 | 9:29:06 AM
 * Last Update:    @rasha.aboushakra    Nov 5, 2014 | 9:29:06 AM
 */

class Banks extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'bnkid';
    const TABLE_NAME = 'banks';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = 'bnkid,name,affid';
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