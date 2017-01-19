<?php

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