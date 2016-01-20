<?php
/* -------Definiton-START-------- */

class ReportingQrRecipient extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'rqrrid';
    const TABLE_NAME = 'reporting_qrrecipients';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'reportIdentifier,rpid';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = '';
    const REQUIRED_ATTRS = '';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $fields = array('reportIdentifier', 'rpid', 'uid', 'unregisteredRcpts', 'token', 'password', 'salt', 'loginKey', 'sentOn', 'sentBy');
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(!is_null($data[$field])) {
                    $table_array[$field] = $data[$field];
                }
            }
        }
        $this->errorcode = 3;
        if(is_array($table_array)) {

            $query = $db->insert_query(self::TABLE_NAME, $table_array);
            if($query) {
                $this->errorcode = 0;
                $this->data[self::PRIMARY_KEY] = $db->last_id();
            }
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        $fields = array('reportIdentifier', 'rpid', 'uid', 'unregisteredRcpts', 'token', 'password', 'salt', 'loginKey', 'sentOn', 'sentBy');
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(!is_null($data[$field])) {
                    $table_array[$field] = $data[$field];
                }
            }
        }
        $this->errorcode = 3;
        if(is_array($table_array)) {

            $db->update_query(self::TABLE_NAME, $table_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
            $this->errorcode = 0;
        }
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
}