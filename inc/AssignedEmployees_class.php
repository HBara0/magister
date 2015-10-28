<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AssignedEmployees_class.php
 * Created:        @hussein.barakat    Mar 27, 2015 | 12:28:53 PM
 * Last Update:    @hussein.barakat    Mar 27, 2015 | 12:28:53 PM
 */

class AssignedEmployees extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'aseid';
    const TABLE_NAME = 'assignedemployees';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'eid,uid,affid';
    const REQUIRED_ATTRS = 'eid,uid,affid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function get_user() {
        return new Users($this->data['uid']);
    }

    public function get_entity() {
        return new Entities($this->data['eid']);
    }

    protected function create(array $data) {
        global $db, $log;
        if(!$this->validate_requiredfields($data)) {
            $this->errorcode = 2;
            return $this;
        }
        $query = $db->insert_query(self::TABLE_NAME, $data);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
            $log->record('assignedemployees', $this->data[self::PRIMARY_KEY]);
            $this->errorcode = 0;
            return $this;
        }
    }

    protected function update(array $data) {
        global $db;
        if(!$this->validate_requiredfields($data)) {
            $this->errorcode = 2;
            return $this;
        }
        $query = $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        if($query) {
            $this->errorcode = 0;
            return $this;
        }
    }

    public function get_supplier_auditor($spid) {
        $suppauditor = self::get_data(array('eid' => $spid, 'isValidator' => 1));
        if(is_object($suppauditor)) {
            $suppauditor = new Users($suppauditor->uid);
            if(is_object($suppauditor)) {
                return $suppauditor;
            }
        }
        return false;
    }

}