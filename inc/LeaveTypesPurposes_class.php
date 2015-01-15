<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: leave_purpose.php
 * Created:        @tony.assaad    May 13, 2014 | 1:19:31 PM
 * Last Update:    @tony.assaad    May 13, 2014 | 1:19:31 PM
 */

/**
 * Description of leave_purpose
 *
 * @author tony.assaad
 */
class LeaveTypesPurposes extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'ltpid';
    const TABLE_NAME = 'leavetypes_purposes';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function get() {
        return $this->data;
    }

    public function get_createdby() {
        return new Users($this->data['createdBy']);
    }

    protected function create(array $data) {

    }

    protected function update(array $data) {

    }

}