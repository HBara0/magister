<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: ReportsSendLog_class.php
 * Created:        @zaher.reda    Jul 1, 2015 | 12:54:14 PM
 * Last Update:    @zaher.reda    Jul 1, 2015 | 12:54:14 PM
 */

/**
 * Description of ReportsSendLog_class
 *
 * @author zaher.reda
 */
class ReportsSendLog extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'rslid';
    const TABLE_NAME = 'reportssendlog';
    const DISPLAY_NAME = 'report';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = null;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function update(array $data) {

    }

}