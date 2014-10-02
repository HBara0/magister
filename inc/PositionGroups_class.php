<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: PositionGroups_class.php
 * Created:        @rasha.aboushakra    Sep 30, 2014 | 2:19:00 PM
 * Last Update:    @rasha.aboushakra    Sep 30, 2014 | 2:19:00 PM
 */

Class PositionGroups extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'posgid';
    const TABLE_NAME = 'positiongroups';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'posgid, name, title';
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
?>