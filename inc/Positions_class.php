<?php

class Positions extends AbstractClass {

    protected $data = array();

    const PRIMARY_KEY = 'posid';
    const TABLE_NAME = 'positions';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = 'posid,name,title';
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
