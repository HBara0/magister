<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetsExpenseCategories.php
 * Created:        @rasha.aboushakra    Sep 23, 2014 | 1:25:08 PM
 * Last Update:    @rasha.aboushakra    Sep 23, 2014 | 1:25:08 PM
 */

class BudgetExpenseCategories extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'becid';
    const TABLE_NAME = 'budgeting_expense_categories';
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

    public function get_items() {
        return BudgetExpenseItems::get_data(array('becid' => $this->data['becid']), array('sort' => 'title', 'returnarray' => true, 'simple' => false));
    }

}
?>