<?php

class SystemWindowsSection extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'swsid';
    const TABLE_NAME = 'system_windows_sections';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'swid,name';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'name';

    /* Definition-Start */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $table_array = array(
                'swid' => $data['swid'],
                'name' => $data['name'],
                'dbTable' => $data['dbTable'],
                'type' => $data['type'],
                'sequence' => $data['sequence'],
                'displayType' => $data['displayType'],
                'description' => $data['description'],
                'comment' => $data['comment'],
                'defaultOrderBy' => $data['defaultOrderBy'],
                'displayLogic' => $data['displayLogic'],
                'saveModuleName' => $data['saveModuleName'],
                'saveActionName' => $data['saveActionName'],
                'sqlWhereClause' => $data['sqlWhereClause'],
                'isActive' => $data['isActive'],
                'isMain' => $data['isMain'],
                'inputChecksum' => $data['inputChecksum'],
                'swstid' => $data['swstid'],
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
            $update_array['swid'] = $data['swid'];
            $update_array['name'] = $data['name'];
            $update_array['dbTable'] = $data['dbTable'];
            $update_array['type'] = $data['type'];
            $update_array['sequence'] = $data['sequence'];
            $update_array['displayType'] = $data['displayType'];
            $update_array['description'] = $data['description'];
            $update_array['comment'] = $data['comment'];
            $update_array['defaultOrderBy'] = $data['defaultOrderBy'];
            $update_array['displayLogic'] = $data['displayLogic'];
            $update_array['saveModuleName'] = $data['saveModuleName'];
            $update_array['saveActionName'] = $data['saveActionName'];
            $update_array['sqlWhereClause'] = $data['sqlWhereClause'];
            $update_array['isActive'] = $data['isActive'];
            $update_array['isMain'] = $data['isMain'];
            $update_array['inputChecksum'] = $data['inputChecksum'];
            $update_array['swstid'] = $data['swstid'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

////////Definition End
    /* Getter functions-Start */

    public function get_Window() {
        return new SystemWindows($this->data['swid']);
    }

}