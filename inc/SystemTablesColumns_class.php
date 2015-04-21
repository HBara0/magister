<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Tables_class.php
 * Created:        @hussein.barakat    Apr 21, 2015 | 1:46:17 PM
 * Last Update:    @hussein.barakat    Apr 21, 2015 | 1:46:17 PM
 */

class SystemTablesColumns extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'stcid';
    const TABLE_NAME = 'system_tables_columns';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'tableName,columnName';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db;
        $table_array = array(
                'tableName' => $data['tableName'],
                'columnName' => $data['columnName'],
                'columnDefault' => $data['columnDefault'],
                'dataType' => $data['dataType'],
                'extra' => $data['extra'],
                'key' => $data['key'],
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
            $table_array['tableName'] = $data['tableName'];
            $table_array['columnName'] = $data['columnName'];
            $table_array['columnDefault'] = $data['columnDefault'];
            $table_array['dataType'] = $data['dataType'];
            $table_array['extra'] = $data['extra'];
            $table_array['key'] = $data['key'];
        }
        $db->update_query(self::TABLE_NAME, $table_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

}