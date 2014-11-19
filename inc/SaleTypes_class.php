<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: SaleTypes_class.php
 * Created:        @zaher.reda    Oct 1, 2014 | 4:30:52 PM
 * Last Update:    @zaher.reda    Oct 1, 2014 | 4:30:52 PM
 */

/**
 * Description of SaleTypes_class
 *
 * @author zaher.reda
 */
class SaleTypes extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'stid';
    const TABLE_NAME = 'saletypes';
    const DISPLAY_NAME = 'title';
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

    public function get_invoicesaletype() {
        return new SaleTypes($this->data['invoiceAffStid']);
    }

//put your code here
}