<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: LeavesStats_class.php
 * Created:        @zaher.reda    Aug 2, 2014 | 1:43:04 PM
 * Last Update:    @zaher.reda    Aug 2, 2014 | 1:43:04 PM
 */

/**
 * Description of LeavesStats_class
 *
 * @author zaher.reda
 */
class LeavesStats extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'lsid';
    const TABLE_NAME = 'leavesstats';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function get_user() {
        return new Users($this->data['uid']);
    }

    public function get_leavetype() {
        return new Leavetypes($this->data['ltid']);
    }

    protected function create(array $data) {

    }

    protected function save(array $data = array()) {

    }

    protected function update(array $data) {

    }

}