<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: SupplierAudits_class.php
 * Created:        @hussein.barakat    Apr 17, 2015 | 9:36:51 AM
 * Last Update:    @hussein.barakat    Apr 17, 2015 | 9:36:51 AM
 */

class SupplierAudits extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'said';
    const TABLE_NAME = 'suppliersaudits';
    const DISPLAY_NAME = '';
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

    public function get_entity() {
        return new Entities($this->data['eid']);
    }

}