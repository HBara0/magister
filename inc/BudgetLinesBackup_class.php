<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetLinesBackup_class.php
 * Created:        @zaher.reda    Oct 17, 2015 | 3:25:40 PM
 * Last Update:    @zaher.reda    Oct 17, 2015 | 3:25:40 PM
 */

/**
 * Description of BudgetLinesBackup_class
 *
 * @author zaher.reda
 */
class BudgetLinesBackup extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'blibkid';
    const TABLE_NAME = 'budgeting_blbackup';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = null;
    const REQUIRED_ATTRS = '';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function update(array $data) {

    }

}