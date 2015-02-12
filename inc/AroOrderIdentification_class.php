<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroOrderIdentification_class.php
 * Created:        @tony.assaad    Feb 11, 2015 | 11:40:28 AM
 * Last Update:    @tony.assaad    Feb 11, 2015 | 11:40:28 AM
 */

/**
 * Description of AroOrderIdentification_class
 *
 * @author tony.assaad
 */
class AroOrderIdentification_class extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'adsid';
    const TABLE_NAME = 'aro_documentsequences';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'adsid,affid,ptid,effectiveFrom,effectiveTo';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'affid,ptid,effectiveFrom,effectiveTo';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        ;
    }

    protected function update(array $data) {
        ;
    }

}