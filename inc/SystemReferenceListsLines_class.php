<?php

class SystemReferenceListsLines extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'srllid';
    const TABLE_NAME = 'system_referencelists_lines';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'srllid,type';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'name';

    /* Definition-Start */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $table_array = array(
                'name' => $data['name'],
                'title' => $data['title'],
                'value' => $data['value'],
                'sequence' => $data['sequence'],
                'description' => $data['description'],
                'isActive' => $data['isActive'],
                'tableName' => $data['tableName'],
                'keyColumn' => $data['keyColumn'],
                'displayedColumn' => $data['displayedColumn'],
                'whereClause' => $data['whereClause'],
                'type' => $data['type'],
                'srlid' => $data['srlid'],
                'inputChecksum' => $data['inputChecksum'],
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
            $update_array['name'] = $data['name'];
            $update_array['title'] = $data['title'];
            $update_array['value'] = $data['value'];
            $update_array['sequence'] = $data['sequence'];
            $update_array['description'] = $data['description'];
            $update_array['isActive'] = $data['isActive'];
            $update_array['tableName'] = $data['tableName'];
            $update_array['keyColumn'] = $data['keyColumn'];
            $update_array['displayedColumn'] = $data['displayedColumn'];
            $update_array['whereClause'] = $data['whereClause'];
            $update_array['type'] = $data['type'];
            $update_array['srlid'] = $data['srlid'];
            $update_array['inputChecksum'] = $data['inputChecksum'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

////////Definition End
    /* Getter functions-Start */

    public function get_referencelist() {
        return new SystemReferenceLists($this->data['srlid']);
    }

    /* other functions */}