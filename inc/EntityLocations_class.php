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
    const DISPLAY_NAME = 'location';
    const SIMPLEQ_ATTRS = 'eloid,eid,location';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'eid,location';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $required_fields = array('location', 'ciid', 'addressLine1');
            foreach($required_fields as $field) {
                if(empty($data[$field])) {
                    $this->errorcode = 2;
                    return false;
                }
            }
            if(!empty($data['sid']) && empty($data['cid'])) {
                $data['eid'] = $data['sid'];
            }
            else {
                $data['eid'] = $data['cid'];
            }
            $data['address'] = $data['addressLine1'].' - '.$data['addressLine1'];
            $data['buildingName'] = $data['building'].' - '.$data['floor'];
            unset($data['cid'], $data['sid'], $data['building'], $data['addressLine1'], $data['floor'], $data['addressLine2']);

            $data['createdOn'] = TIME_NOW;
            $data['createdBy'] = $core->user['uid'];
            $query = $db->insert_query(self::TABLE_NAME, $data);
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

    //put your code here
}