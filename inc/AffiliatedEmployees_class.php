<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AffiliatedEmployess_class.php
 * Created:        @tony.assaad    Jan 21, 2015 | 4:02:31 PM
 * Last Update:    @tony.assaad    Jan 21, 2015 | 4:02:31 PM
 */

/**
 * Description of AffiliatedEmployess_class
 *
 * @author tony.assaad
 */
class AffiliatedEmployees extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'aeid';
    const TABLE_NAME = 'affiliatedemployees';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'aeid, affid, uid,canAudit';
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

}