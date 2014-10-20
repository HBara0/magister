<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetExpenseItems_Class.php
 * Created:        @rasha.aboushakra    Sep 23, 2014 | 1:42:21 PM
 * Last Update:    @rasha.aboushakra    Sep 23, 2014 | 1:42:21 PM
 */

Class BudgetExpenseItems extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'beciid';
    const TABLE_NAME = 'budgeting_expense_items';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = 'becid, name, title';
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
?>