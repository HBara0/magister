<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: GenericProducts_class.php
 * Created:        @zaher.reda    Sep 30, 2014 | 4:32:26 PM
 * Last Update:    @zaher.reda    Sep 30, 2014 | 4:32:26 PM
 */

/**
 * Description of GenericProducts_class
 *
 * @author zaher.reda
 */
class GenericProducts extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'gpid';
    const TABLE_NAME = 'genericproducts';
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

    public function get_segment() {
        return new ProductsSegments($this->data['psid']);
    }

}