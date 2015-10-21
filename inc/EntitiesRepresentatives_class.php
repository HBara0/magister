<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: EntitiesRepresentatives.php
 * Created:        @hussein.barakat    Apr 15, 2015 | 11:04:48 AM
 * Last Update:    @hussein.barakat    Apr 15, 2015 | 11:04:48 AM
 */

class EntitiesRepresentatives extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'erpid';
    const TABLE_NAME = 'entitiesrepresentatives';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'rpid,eid';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function save(array $data = array()) {
        parent::save($data);
    }

    public function create(array $data) {
        global $db, $core;
        if(!$this->validate_requiredfields($data)) {
            return false;
        }
        $fields = array('rpid', 'eid');
        foreach($fields as $field) {
            $table_array[$field] = $data[$field];
        }

        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data = $table_array;
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    public function update(array $data) {
        global $db, $core;
        if(!$this->validate_requiredfields($data)) {
            return false;
        }
        $fields = array('eid', 'rpid', 'erpid');
        foreach($fields as $field) {
            $update_array[$field] = $data[$field];
        }

        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    public function get_entity() {
        return new Entities($this->data['eid'], '', false);
    }

    public function get_representative() {
        return new Representatives($this->data['rpid']);
    }

}