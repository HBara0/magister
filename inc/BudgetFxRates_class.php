<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetFxRates_class.php
 * Created:        @tony.assaad    Oct 8, 2014 | 2:10:08 PM
 * Last Update:    @tony.assaad    Oct 8, 2014 | 2:10:08 PM
 */

/**
 * Description of BudgetFxRates_class
 *
 * @author tony.assaad
 */
class BudgetFxRates extends AbstractClass {
    const PRIMARY_KEY = 'bfxid';
    const TABLE_NAME = 'budgeting_fxrates';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'bfxid, affid, year, fromCurrency, toCurrency, rate';
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