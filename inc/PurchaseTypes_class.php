<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: PurchaseTypes_class.php
 * Created:        @rasha.aboushakra    Feb 4, 2015 | 11:25:57 AM
 * Last Update:    @rasha.aboushakra    Feb 4, 2015 | 11:25:57 AM
 */

class PurchaseTypes extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'ptid';
    const TABLE_NAME = 'purchasetypes';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {

    }

    protected function update(array $data) {

    }

    public function save(array $data = array()) {

    }

}