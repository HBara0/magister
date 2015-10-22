<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: YefLinesBackup_class.php
 * Created:        @zaher.reda    Oct 17, 2015 | 3:25:40 PM
 * Last Update:    @zaher.reda    Oct 17, 2015 | 3:25:40 PM
 */

/**
 * Description of YefLinesBackup
 *
 * @author zaher.reda
 */
class YefLinesBackup extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'yeflibkid';
    const TABLE_NAME = 'budgeting_yeflbackup';
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