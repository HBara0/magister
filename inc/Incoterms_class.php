<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Incoterms_class.php
 * Created:        @rasha.aboushakra    Dec 10, 2014 | 12:13:35 PM
 * Last Update:    @rasha.aboushakra    Dec 10, 2014 | 12:13:35 PM
 */

class Incoterms extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'iid';
    const TABLE_NAME = 'incoterms';
    const DISPLAY_NAME = 'titleAbbr';
    const SIMPLEQ_ATTRS = 'iid,titleAbbr,name,title,carriageOnBuyer';
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