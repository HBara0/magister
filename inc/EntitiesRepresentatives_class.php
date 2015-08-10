<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: EntitiesRepresentatives.php
 * Created:        @hussein.barakat    Apr 15, 2015 | 11:04:48 AM
 * Last Update:    @hussein.barakat    Apr 15, 2015 | 11:04:48 AM
 */

class EntitiesRepresentatives extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'erpid';
    const TABLE_NAME = 'entitiesrepresentatives';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'rpid,eid';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function save(array $data = array()) {
        parent::save($data);
    }

    public function create(array $data) {
        ;
    }

    public function update(array $data) {

    }

    public function get_entity() {
        return new Entities($this->data['eid']);
    }

    public function get_representative() {
        return new Representatives($this->data['rpid']);
    }

}