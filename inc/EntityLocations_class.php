<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: EntityLocations.php
 * Created:        @tony.assaad    Dec 9, 2014 | 12:16:45 PM
 * Last Update:    @tony.assaad    Dec 9, 2014 | 12:16:45 PM
 */

/**
 * Description of EntityLocations
 *
 * @author tony.assaad
 */
class EntityLocations extends AbstractClass {
    protected $data = array();
    protected $errorcode = null;

    const PRIMARY_KEY = 'eloid';
    const TABLE_NAME = 'entities_locations';
    const DISPLAY_NAME = 'locationType';
    const SIMPLEQ_ATTRS = 'eloid, eid, locationType';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'eid,locationType,coid,ciid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $required_fields = array('locationType', 'eid', 'coid', 'ciid', 'addressLine1');
            foreach($required_fields as $field) {
                if(empty($data[$field])) {
                    $this->errorcode = 2;
                    return false;
                }
            }
            if(!is_empty($data['telephone_intcode'], $data['telephone_areacode'], $data['telephone_number'])) {
                $data['phone'] = $data['telephone_intcode'].'-'.$data['telephone_areacode'].'-'.$data['telephone_number'];
            }
            //  $data['address'] = $data['addressLine1'].' - '.$data['addressLine1'];
            $data['buildingName'] = $data['building'].' - '.$data['floor'];
            unset($data['building'], $data['floor'], $data['telephone_intcode'], $data['telephone_areacode'], $data['telephone_number']);

            $data['createdOn'] = TIME_NOW;
            $data['createdBy'] = $core->user['uid'];
            $query = $db->insert_query(self::TABLE_NAME, $data);
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
    }

    protected function update(array $data) {

    }

    public function get_entity() {
        return new Entities($this->data['eid']);
    }

    public function get_country() {
        return new Countries($this->data['coid']);
    }

    public function get_city() {
        return new Cities($this->data['ciid']);
    }

    public function get_displayname() {
        return ucwords($this->data['locationType']).': '.$this->data['addressLine1'].' - '.$this->get_city()->name.' - '.$this->get_country()->get_displayname();
    }

}