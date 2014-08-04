<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * Tasks Notes Class
 * $id: TasksNotes_class.php
 * Created:        @zaher.reda    Jul 31, 2014 | 11:31:37 AM
 * Last Update:    @zaher.reda    Jul 31, 2014 | 11:31:37 AM
 */

/**
 * Description of TasksNotes_class
 *
 * @author zaher.reda
 */
class TasksNotes extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'ctnid';
    const TABLE_NAME = 'calendar_tasks_notes';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;
    const SIMPLEQ_ATTRS = 'ctnid, ctid, uid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {

    }

    protected function save(array $data = array()) {

    }

    protected function update(array $data) {

    }

    public function get_user() {
        return new Users($this->data['uid']);
    }

    public function get_task() {
        return new Tasks($this->data['ctid']);
    }

}