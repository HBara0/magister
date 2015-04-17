<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: VisitReports.php
 * Created:        @hussein.barakat    Apr 15, 2015 | 11:21:34 AM
 * Last Update:    @hussein.barakat    Apr 15, 2015 | 11:21:34 AM
 */

class VisitReports extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'vrid';
    const TABLE_NAME = 'visitreports';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'vrid,identifier,uid,cid,rpid,affid,date';
    const UNIQUE_ATTRS = 'identifier,uid,cid';
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