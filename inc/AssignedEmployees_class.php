<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AssignedEmployees_class.php
 * Created:        @hussein.barakat    Mar 27, 2015 | 12:28:53 PM
 * Last Update:    @hussein.barakat    Mar 27, 2015 | 12:28:53 PM
 */

class AssignedEmployees extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'aseid';
    const TABLE_NAME = 'assignedemployees';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function get_user() {
        return new Users($this->data['uid']);
    }

    public function get_entity() {
        return new Entities($this->data['eid']);
    }

    protected function create(array $data) {

    }

    protected function update(array $data) {

    }

}