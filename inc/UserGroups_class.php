<?php

/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: UserGroups_class.php
 * Created:        @hussein.barakat    Mar 24, 2015 | 11:48:02 AM
 * Last Update:    @hussein.barakat    Mar 24, 2015 | 11:48:02 AM
 */

class UserGroups extends AbstractClass {

    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'gid';
    const TABLE_NAME = 'usergroups';
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

?>