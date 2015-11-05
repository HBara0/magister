<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: HrJobApplicants_class.php
 * Created:        @rasha.aboushakra    Nov 3, 2015 | 3:59:36 PM
 * Last Update:    @rasha.aboushakra    Nov 3, 2015 | 3:59:36 PM
 */

/**
 * Description of HrJobApplicants_class
 *
 * @author rasha.aboushakra
 */
class HrJobApplicants extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'jaid';
    const TABLE_NAME = 'hr_jobapplicants';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const REQUIRED_ATTRS = '';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {

    }

    protected function update(array $data) {

    }

    public function save(array $data = array()) {

    }

}