<?php

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
