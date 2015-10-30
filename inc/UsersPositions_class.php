<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: UsersPositions_class.php
 * Created:        @rasha.aboushakra    Mar 11, 2015 | 3:38:38 PM
 * Last Update:    @rasha.aboushakra    Mar 11, 2015 | 3:38:38 PM
 */

class UsersPositions extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'upid';
    const TABLE_NAME = 'userspositions';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'upid,uid,posid';
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