<?php

class CalendarEventTypes extends AbstractClass {

    protected $data = array();

    const PRIMARY_KEY = 'cetid';
    const TABLE_NAME = 'calendar_eventtypes';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = 'cetid,name,title';
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

    public function get_displayname() {
        return $this->data[self::DISPLAY_NAME];
    }

}
