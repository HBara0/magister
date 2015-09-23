<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: FacilityMgmtFacilityFeatures_class.php
 * Created:        @rasha.aboushakra    Sep 23, 2015 | 9:54:26 AM
 * Last Update:    @rasha.aboushakra    Sep 23, 2015 | 9:54:26 AM
 */

/**
 * Description of FacilityMgmtFacilityFeatures_class
 *
 * @author rasha.aboushakra
 */
class FacilityMgmtFacilityFeatures extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'fmffid';
    const TABLE_NAME = 'facilitymgmt_facilityfeatures';
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

}