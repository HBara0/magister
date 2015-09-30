<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: FacilityMgmtFactypes.php
 * Created:        @rasha.aboushakra    Sep 23, 2015 | 9:53:28 AM
 * Last Update:    @rasha.aboushakra    Sep 23, 2015 | 9:53:28 AM
 */

/**
 * Description of FacilityMgmtFactypes
 *
 * @author rasha.aboushakra
 */
class FacilityMgmtFactypes extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'fmftid';
    const TABLE_NAME = 'facilitymgmt_factypes';
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

    public function get_roomtypes() {
        $roomtypes = FacilityMgmtFactypes::get_data(array('isRoom' => 1, 'isActive' => 1), array('returnarray' => true));
        return $roomtypes;
    }

    public function get_roomtypesids() {
        $roomtypes = FacilityMgmtFactypes::get_column('fmftid', array('isRoom' => 1, 'isActive' => 1), array('returnarray' => true));
        return $roomtypes;
    }

    public function get_maintypes() {
        $maintype = FacilityMgmtFactypes::get_data(array('isMainLocation' => 1, 'isActive' => 1), array('returnarray' => true));
        return $maintype;
    }

    public function get_maintypesids() {
        $maintype = FacilityMgmtFactypes::get_column('fmftid', array('isMainLocation' => 1, 'isActive' => 1), array('returnarray' => true));
        return $maintype;
    }

}