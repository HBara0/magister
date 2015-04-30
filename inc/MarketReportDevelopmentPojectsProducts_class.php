<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: MarketReportDevelopmentPojectsProducts_class.php
 * Created:        @rasha.aboushakra    Apr 29, 2015 | 2:28:12 PM
 * Last Update:    @rasha.aboushakra    Apr 29, 2015 | 2:28:12 PM
 */

/**
 * Description of MarketReportDevelopmentPojectsProducts_class
 *
 * @author rasha.aboushakra
 */
class MarketReportDevelopmentPojectsProducts extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'mrdppid';
    const TABLE_NAME = 'marketreport_developmentpojects_products';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'mrdpid,pid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data = array()) {
        global $db;
        if(empty($data)) {
            $data = $this->data;
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
        $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

}