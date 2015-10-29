<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Packaging_class.php
 * Created:        @rasha.aboushakra    Dec 10, 2014 | 11:59:14 AM
 * Last Update:    @rasha.aboushakra    Dec 10, 2014 | 11:59:14 AM
 */

class Packaging extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'packid';
    const TABLE_NAME = 'packaging';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = 'packid,name,title';
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