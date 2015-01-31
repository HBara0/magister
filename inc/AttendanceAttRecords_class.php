<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AttendanceAttRecords_class.php
 * Created:        @zaher.reda    Jan 15, 2015 | 5:09:44 PM
 * Last Update:    @zaher.reda    Jan 15, 2015 | 5:09:44 PM
 */

/**
 * Description of AttendanceAttRecords_class
 *
 * @author zaher.reda
 */
class AttendanceAttRecords extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'aarid';
    const TABLE_NAME = 'attendance_attrecords';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = null;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function get_user() {
        return new Users($this->data['uid']);
    }

    protected function create(array $data) {

    }

    protected function update(array $data) {

    }

}