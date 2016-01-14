<?php
/* -------Definiton-START-------- */

class SystemReferenceListsLines extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'srllid';
    const TABLE_NAME = 'system_referencelists_lines';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'srllid,type';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'name';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $fields = array('name', 'title', 'value', 'sequence', 'description', 'isActive', 'table', 'keyColumn', 'displayedColumn', 'whereClause', 'type', 'srlid', 'inputChecksum', 'tableName');
        foreach($fields as $field) {
            if(!is_null($data[$field])) {
                $table_array[$field] = $data[$field];
            }
        }
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        $update_array['isActive'] = 0;
        $fields = array('name', 'title', 'value', 'sequence', 'description', 'isActive', 'table', 'keyColumn', 'displayedColumn', 'whereClause', 'type', 'srlid', 'inputChecksum', 'tableName');
        foreach($fields as $field) {
            if(!is_null($data[$field])) {
                $update_array[$field] = $data[$field];
            }
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-START-------- */
    /* -------GETTER FUNCTIONS-START-------- */
    public function get_referencelist() {
        return new SystemReferenceLists($this->data['srlid']);
    }

    /* -------GETTER FUNCTIONS-END-------- */
}