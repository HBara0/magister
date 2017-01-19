<?php

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