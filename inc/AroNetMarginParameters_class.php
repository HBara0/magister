<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroNetMarginParameters_class.php
 * Created:        @rasha.aboushakra    Feb 26, 2015 | 1:23:59 PM
 * Last Update:    @rasha.aboushakra    Feb 26, 2015 | 1:23:59 PM
 */

class AroNetMarginParameters extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'anpid';
    const TABLE_NAME = 'aro_netmargin_parameters';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'aorid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $log;
        $query = $db->insert_query(self::TABLE_NAME, $data);
        if($query) {
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
        }
    }

    protected function update(array $data) {
        global $db, $log;
        $query = $db->update_query(self::TABLE_NAME, $data, ''.self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        if($query) {
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
        }
    }

}