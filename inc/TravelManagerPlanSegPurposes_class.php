<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: TravelManagerSegPurposes_class.php
 * Created:        @rasha.aboushakra    Apr 8, 2015 | 12:20:55 PM
 * Last Update:    @rasha.aboushakra    Apr 8, 2015 | 12:20:55 PM
 */

class TravelManagerPlanSegPurposes extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'tmpspid';
    const TABLE_NAME = 'travelmanager_plan_segpurposes';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'tmpspid,tmpsid,purpose';
    const UNIQUE_ATTRS = 'tmpsid,purpose';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db;
        if(is_array($data)) {
            $db->insert_query(self::TABLE_NAME, $data);
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(is_array($data)) {
            $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        }
        return $this;
    }

}