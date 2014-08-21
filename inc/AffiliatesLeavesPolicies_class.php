<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AffiliatesLeavesPolicies_class.php
 * Created:        @zaher.reda    Aug 4, 2014 | 3:47:52 PM
 * Last Update:    @zaher.reda    Aug 4, 2014 | 3:47:52 PM
 */

/**
 * Description of AffiliatesLeavesPolicies_class
 *
 * @author zaher.reda
 */
class AffiliatesLeavesPolicies extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'alpid';
    const TABLE_NAME = 'affiliatesleavespolicies';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {

    }

    public function save(array $data = array()) {

    }

    protected function update(array $data) {

    }

    protected function delete() {

    }

}