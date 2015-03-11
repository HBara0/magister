<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroRequestsApprovals_class.php
 * Created:        @rasha.aboushakra    Feb 13, 2015 | 2:38:35 PM
 * Last Update:    @rasha.aboushakra    Feb 13, 2015 | 2:38:35 PM
 */

class AroRequestsApprovals extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'araid';
    const TABLE_NAME = 'aro_requests_approvals';
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