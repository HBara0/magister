<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: FacilityMgmtFacilities_class.php
 * Created:        @rasha.aboushakra    Sep 23, 2015 | 9:53:03 AM
 * Last Update:    @rasha.aboushakra    Sep 23, 2015 | 9:53:03 AM
 */

/**
 * Description of FacilityMgmtFacilities_class
 *
 * @author rasha.aboushakra
 */
class FacilityMgmtFacilities extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'fmfid';
    const TABLE_NAME = 'facilitymgmt_facilities';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $data['createdOn'] = TIME_NOW;
            $data['createdBy'] = $core->user['uid'];
            if(is_array($data['dimensions'])) {
                $data['dimensions'] = implode("x", $data['dimensions']);
            }
            $db->insert_query(self::TABLE_NAME, $data);
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            if(is_array($data['dimensions'])) {
                $data['dimensions'] = implode("x", $data['dimensions']);
            }
            $data['modifiedOn'] = TIME_NOW;
            $data['modifiedBy'] = $core->user['uid'];
            $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        }
        return $this;
    }

    public function get_displayname() {
        return $this->data[self::DISPLAY_NAME];
    }

}