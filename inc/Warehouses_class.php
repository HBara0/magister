<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Warehouses_class.php
 * Created:        @rasha.aboushakra    Feb 3, 2015 | 10:58:35 AM
 * Last Update:    @rasha.aboushakra    Feb 3, 2015 | 10:58:35 AM
 */

class Warehouses extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'wid';
    const TABLE_NAME = 'warehouses';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'affid,name';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(!$this->validate_requiredfields($data)) {
            $city = new Cities($data['ciid']);
            $data['coid'] = $city->get_country()->coid;
            $data['createdOn'] = TIME_NOW;
            $data['createdBy'] = $core->user['uid'];
            $geolocation = $data['geoLocation'];
            unset($data['geoLocation']);

            if(!empty($geolocation)) {
                if(strstr($geolocation, ',')) {
                    $geolocation = str_replace(', ', ' ', $geolocation);
                }
            }
            $query = $db->insert_query(self::TABLE_NAME, $data);
            $id = $db->last_id();
            $db->query('UPDATE warehouses SET geoLocation=geomFromText("POINT('.$db->escape_string($geolocation).')") WHERE wid='.$id);
        }
    }

    protected function update(array $data) {
        global $db, $core;
        if(!$this->validate_requiredfields($data)) {

            $city = new Cities($data['ciid']);
            $data['coid'] = $city->get_country()->coid;
            $data['modifiedOn'] = TIME_NOW;
            $data['modifiedBy'] = $core->user['uid'];

            $geolocation = $data['geoLocation'];
            unset($data['geoLocation']);
            if(!empty($geolocation)) {
                if(strstr($geolocation, ',')) {
                    $geolocation = str_replace(', ', ' ', $geolocation);
                }
            }
            $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
            $id = $db->last_id();
            $db->query('UPDATE warehouses SET geoLocation=geomFromText("POINT('.$db->escape_string($geolocation).')") WHERE wid='.$id);
        }
    }

    private function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('affid', 'name', 'ciid');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

}