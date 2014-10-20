<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetPlItems_class.php
 * Created:        @rasha.aboushakra    Oct 13, 2014 | 2:54:42 PM
 * Last Update:    @rasha.aboushakra    Oct 13, 2014 | 2:54:42 PM
 */

class BudgetPlItems extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'bpliid';
    const TABLE_NAME = 'budgeting_plitems';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = 'bpliid,bplcid,name,title';
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