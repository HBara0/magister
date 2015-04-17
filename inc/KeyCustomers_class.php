<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: KeyCustomers.php
 * Created:        @hussein.barakat    Apr 15, 2015 | 11:25:01 AM
 * Last Update:    @hussein.barakat    Apr 15, 2015 | 11:25:01 AM
 */

/**
 * Description of KeyCustomers
 * Basic Class for table keycustomers
 * @author hussein.barakat
 */
class KeyCustomers extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'kcid';
    const TABLE_NAME = 'keycustomers';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'cid,rid';
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

}