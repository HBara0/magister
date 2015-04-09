<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: MarketReportCompetition_class.php
 * Created:        @rasha.aboushakra    Apr 9, 2015 | 10:09:13 AM
 * Last Update:    @rasha.aboushakra    Apr 9, 2015 | 10:09:13 AM
 */

class MarketReportCompetition extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'mrcid';
    const TABLE_NAME = 'marketreport_competition';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'mrid,sid,pid,csid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        //Add chemical substance saving //Record log
        global $db;
        $db->insert_query(self::TABLE_NAME, $data);
        return $this;
    }

    protected function update(array $data) {
        global $db;
        $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

}