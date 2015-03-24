<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: ProductsChemicalSubstances_class.php
 * Created:        @hussein.barakat    Mar 23, 2015 | 3:28:07 PM
 * Last Update:    @hussein.barakat    Mar 23, 2015 | 3:28:07 PM
 */

class ProductsChemicalSubstances extends AbstractClass {
    protected $data = array();
    protected $errorcode = null;

    const PRIMARY_KEY = 'pcsid';
    const TABLE_NAME = 'productschemsubstances';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'pcsid, pid, csid';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function get_product() {
        return new Products($this->pid);
    }

    public function get_chemicalsubstance() {
        return new Chemicalsubstances($this->csid);
    }

    protected function create(array $data) {

    }

    protected function update(array $data) {

    }

    public function save(array $data = array()) {

    }

}