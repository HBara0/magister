<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: MarketReportCompetitionProducts_class.php
 * Created:        @rasha.aboushakra    Apr 10, 2015 | 11:04:03 AM
 * Last Update:    @rasha.aboushakra    Apr 10, 2015 | 11:04:03 AM
 */

class MarketReportCompetitionProducts extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'mrcpid';
    const TABLE_NAME = 'marketreport_competition_products';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'mrcid,pid,csid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data = array()) {
        global $db;
        if(empty($data)) {
            $data = $this->data;
        }
        if((empty($data['pid']) && empty($data['csid'])) || empty($data['mrcid'])) {
            return;
        }
        $db->insert_query(self::TABLE_NAME, $data);
        $this->data[self::PRIMARY_KEY] = $db->last_id();
        return $this;
    }

    public function update(array $data = array()) {
        global $db;
        if(empty($data)) {
            $data = $this->data;
        }
        if((empty($data['pid']) && empty($data['csid'])) || empty($data['mrcid'])) {
            return;
        }
        $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

}