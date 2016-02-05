<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AffiliatedEntities.php
 * Created:        @hussein.barakat    Apr 15, 2015 | 10:59:07 AM
 * Last Update:    @hussein.barakat    Apr 15, 2015 | 10:59:07 AM
 */

class AffiliatedEntities extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'aeid';
    const TABLE_NAME = 'affiliatedentities';
    const DISPLAY_NAME = '';
    const UNIQUE_ATTRS = 'eid,affid';
    const SIMPLEQ_ATTRS = '*';
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

    public function get_affiliate() {
        return new Affiliates($this->data['affid']);
    }

    public function get_entity() {
        return new Entities($this->data['eid'], '', false);
    }

}