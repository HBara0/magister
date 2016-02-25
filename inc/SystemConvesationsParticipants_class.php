<?php
/* -------Definiton-START-------- */

class SystemConvesationsParticipants extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'scpid';
    const TABLE_NAME = 'system_conversations_participants';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'scid,uid';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = '';
    const REQUIRED_ATTRS = 'scid';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $fields = array('scid', 'uid');
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(!is_null($data[$field])) {
                    $table_array[$field] = $data[$field];
                }
            }
        }
        if($this->validate_requiredfields($table_array)) {
            if(is_array($table_array)) {
                $table_array['createdOn'] = TIME_NOW;
                $table_array['createdBy'] = $core->user['uid'];

                $query = $db->insert_query(self::TABLE_NAME, $table_array);
                if($query) {
                    $this->errorcode = 0;
                    $this->data[self::PRIMARY_KEY] = $db->last_id();
                }
            }
        }
        else {
            $this->errorcode = 3;
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $core;
        $fields = array('scid', 'uid');
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(!is_null($data[$field])) {
                    $table_array[$field] = $data[$field];
                }
            }
        }
        if($this->validate_requiredfields($table_array)) {
            if(is_array($table_array)) {
                $table_array['modifiedOn'] = TIME_NOW;
                $table_array['modifiedBy'] = $core->user['uid'];
                $db->update_query(self::TABLE_NAME, $table_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
                $this->errorcode = 0;
            }
        }
        else {
            $this->errorcode = 3;
        }
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    /* -------GETTER FUNCTIONS-START-------- */
    public
            function get_scid() {
        return new SystemConversations($this->data['scid']);
    }

    /* -------GETTER FUNCTIONS-END-------- */
}