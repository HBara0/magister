<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroRequestsLinesSupervision_class.php
 * Created:        @rasha.aboushakra    Feb 13, 2015 | 1:32:20 PM
 * Last Update:    @rasha.aboushakra    Feb 13, 2015 | 1:32:20 PM
 */

class AroRequestLines extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'arlsid';
    const TABLE_NAME = 'aro_requests_linessupervision';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

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