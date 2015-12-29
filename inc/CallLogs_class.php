<?php
/* -------Definiton-START-------- */

class CallLogs extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'clogid';
    const TABLE_NAME = 'call_log';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = '';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = '';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $table_array = array(
                'uid' => $core->user['uid'],
                'eid' => intval($data['eid']),
                'isPrivate' => intval($data['isPrivate']),
                'createdOn' => TIME_NOW,
                'description' => $db->escape_string($data['description']),
        );
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {

    }

    /* -------FUNCTIONS-END-------- */
}