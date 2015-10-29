<?php
/* -------Definiton-START-------- */

class UsersTransferedAssignments extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'utaid';
    const TABLE_NAME = 'users_transferedassignments';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'fromUser,toUser,eid,affid';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = '';
    const REQUIRED_ATTRS = 'fromUser,toUser,eid,affid';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        if(!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        $fields = array('fromUser', 'toUser', 'affid', 'eid');
        foreach($fields as $field) {
            $table_array[$field] = $data[$field];
        }
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        $fields = array('fromUser', 'toUser', 'affid', 'eid');
        foreach($fields as $field) {
            $update_array[$field] = $data[$field];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    public function get_fromuser() {
        return new Users($this->data['fromUser']);
    }

    public function get_tomuser() {
        return new Users($this->data['toUser']);
    }

    public function get_affid() {
        return new Affiliates($this->data['affid']);
    }

    public function get_eid() {
        return new Entities($this->data['eid']);
    }

    /* -------FUNCTIONS-END-------- */
}