<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroRequestsCurStkSupervision.php
 * Created:        @rasha.aboushakra    Feb 13, 2015 | 1:35:30 PM
 * Last Update:    @rasha.aboushakra    Feb 13, 2015 | 1:35:30 PM
 */

class AroRequestsCurStkSupervision extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'arcssid';
    const TABLE_NAME = 'aro_requests_curstksupervision';
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