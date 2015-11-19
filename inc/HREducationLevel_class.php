<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: HREducationLevel.php
 * Created:        @rasha.aboushakra    Nov 17, 2015 | 9:09:08 PM
 * Last Update:    @rasha.aboushakra    Nov 17, 2015 | 9:09:08 PM
 */

/**
 * Description of HREducationLevel
 *
 * @author rasha.aboushakra
 */
class HREducationLevel extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'joelid';
    const TABLE_NAME = 'hr_jobopportunities_educationlevel';
    const DISPLAY_NAME = 'title';
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

    public function get_displayname() {
        return $this->data[self::DISPLAY_NAME];
    }

}