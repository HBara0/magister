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
    const DISPLAY_NAME = 'columnDbName';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'stid,columnDbName';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db;
        $table_array = array(
                'stid' => $data['stid'],
                'columnTitle' => $data['columnTitle'],
                'columnDefault' => $data['columnDefault'],
                'dataType' => $data['dataType'],
                'length' => $data['length'],
                'isUnique' => $data['isUnique'],
                'isRequired' => $data['isRequired'],
                'isPrimaryKey' => $data['isPrimaryKey'],
                'extra' => $data['extra'],
                'isDisplayName' => $data['isDisplayName'],
                'isNull' => $data['isNull'],
                'isSimple' => $data['isSimple'],
                'columnSystemName' => $data['columnSystemName'],
                'columnDbName' => $data['columnDbName'],
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
            $table_array['stid'] = $data['stid'];
            $table_array['columnDbName'] = $data['columnDbName'];
            $table_array['columnDefault'] = $data['columnDefault'];
            $table_array['dataType'] = $data['dataType'];
            $table_array['length'] = $data['length'];
            $table_array['isUnique'] = $data['isUnique'];
            $table_array['isRequired'] = $data['isRequired'];
            $table_array['isPrimaryKey'] = $data['isPrimaryKey'];
            $table_array['extra'] = $data['extra'];
            $table_array['isDisplayName'] = $data['isDisplayName'];
            $table_array['isNull'] = $data['isNull'];
            $table_array['isSimple'] = $data['isSimple'];
            $table_array['columnTitle'] = $data['columnTitle'];
            $table_array['columnSystemName'] = $data['columnSystemName'];
        }
        $db->update_query(self::TABLE_NAME, $table_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    public function get_displayname() {
        return self::TABLE_NAME.' - '.$this->data[self::DISPLAY_NAME];
    }

}