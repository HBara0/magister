<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: FacilityMgmtFactypes.php
 * Created:        @rasha.aboushakra    Sep 23, 2015 | 9:53:28 AM
 * Last Update:    @rasha.aboushakra    Sep 23, 2015 | 9:53:28 AM
 */

/**
 * Description of FacilityMgmtFactypes
 *
 * @author rasha.aboushakra
 */
class FacilityMgmtFactypes extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'fmftid';
    const TABLE_NAME = 'facilitymgmt_factypes';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'name';
    const REQUIRED_ATTRS = 'title';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        if(!$this->validate_requiredfields($data)) {
            $this->errorcode = 2;
            return $this;
        }
        $table_array = array(
                'title' => $data['title'],
                'isRoom' => $data['isRoom'],
                'isCoWorkingSpace' => $data['isCoWorkingSpace'],
                'isMainLocation' => $data['isMainLocation'],
                'isActive' => $data['isActive'],
                'description' => $data['description'],
                'createdOn' => TIME_NOW,
                'createdBy' => $core->user['uid'],
        );
        $table_array['name'] = generate_alias($table_array['title']);
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $core;
        if(!$this->validate_requiredfields($data)) {
            $this->errorcode = 2;
            return $this;
        }
        if(is_array($data)) {
            $update_array['title'] = $data['title'];
            $update_array['isRoom'] = $data['isRoom'];
            $update_array['isCoWorkingSpace'] = $data['isCoWorkingSpace'];
            $update_array['isActive'] = $data['isActive'];
            $update_array['isMainLocation'] = $data['isMainLocation'];
            $update_array['description'] = $data['description'];
            $update_array['modifiedOn'] = TIME_NOW;
            $update_array['modifiedBy'] = $core->user['uid'];
        }
        $update_array['name'] = generate_alias($update_array['title']);
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    public function get_roomtypes() {
        $roomtypes = FacilityMgmtFactypes::get_data(array('isRoom' => 1, 'isActive' => 1), array('returnarray' => true));
        return $roomtypes;
    }

    public function get_roomtypesids() {
        $roomtypes = FacilityMgmtFactypes::get_column('fmftid', array('isRoom' => 1, 'isActive' => 1), array('returnarray' => true));
        return $roomtypes;
    }

    public function get_maintypes() {
        $maintype = FacilityMgmtFactypes::get_data(array('isMainLocation' => 1, 'isActive' => 1), array('returnarray' => true));
        return $maintype;
    }

    public function get_maintypesids() {
        $maintype = FacilityMgmtFactypes::get_column('fmftid', array('isMainLocation' => 1, 'isActive' => 1), array('returnarray' => true));
        return $maintype;
    }

}