<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: FacilityMgmtFeatures_class.php
 * Created:        @rasha.aboushakra    Sep 23, 2015 | 9:53:52 AM
 * Last Update:    @rasha.aboushakra    Sep 23, 2015 | 9:53:52 AM
 */

/**
 * Description of FacilityMgmtFeatures_class
 *
 * @author rasha.aboushakra
 */
class FacilityMgmtFeatures extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'fmftid';
    const TABLE_NAME = 'facilitymgmt_features';
    const DISPLAY_NAME = 'name';
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