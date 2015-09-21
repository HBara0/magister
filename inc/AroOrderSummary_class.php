<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroOrderSummary_class.php
 * Created:        @rasha.aboushakra    Sep 21, 2015 | 11:27:22 AM
 * Last Update:    @rasha.aboushakra    Sep 21, 2015 | 11:27:22 AM
 */

/**
 * Description of AroOrderSummary_class
 *
 * @author rasha.aboushakra
 */
class AroOrderSummary extends AbstractClass {
    //put your code here
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'aorsid';
    const TABLE_NAME = 'aro_ordersummary';
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
            return $this;
        }
    }

    protected function update(array $data) {
        global $db, $log;
        $query = $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        if($query) {
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            return $this;
        }
    }

}