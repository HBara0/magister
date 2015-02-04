<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Uom_class.php
 * Created:        @tony.assaad    Feb 3, 2015 | 12:51:56 PM
 * Last Update:    @tony.assaad    Feb 3, 2015 | 12:51:56 PM
 */

/**
 * Description of Uom_class
 *
 * @author tony.assaad
 */
class Uom extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'uomid';
    const TABLE_NAME = 'uom';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = 'uomid,name,symbol,ediCode';
    const UNIQUE_ATTRS = 'uomid,name';
    const CLASSNAME = __CLASS__;

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