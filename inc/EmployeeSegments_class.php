<?php
/* -------Definiton-START-------- */

class EmployeeSegments extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'emsid';
    const TABLE_NAME = 'employeessegments';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'uid,psid';
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
                'uid' => $data['uid'],
                'psid' => $data['psid'],
        );
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(is_array($data)) {
            $update_array['uid'] = $data['uid'];
            $update_array['psid'] = $data['psid'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    public function get_segment() {
        return new ProductsSegments($this->data['psid']);
    }

    public function get_user() {
        return new Users($this->data['uid']);
    }

}