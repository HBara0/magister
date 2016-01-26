<?php

class SystemWindowsSectionFields extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'swsfid';
    const TABLE_NAME = 'system_windows_sectionfields';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'swsfid';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'name';

    /* Definition-Start */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $table_array = array(
                'swsid' => $data['swsid'],
                'name' => $data['name'],
                'dbColumn' => $data['dbColumn'],
                'isDisplayed' => $data['isDisplayed'],
                'isReadOnly' => $data['isReadOnly'],
                'sequence' => $data['sequence'],
                'displayLogic' => $data['displayLogic'],
                'fieldType' => $data['fieldType'],
                'srlid' => $data['srlid'],
                'length' => $data['length'],
                'description' => $data['description'],
                'comment' => $data['comment'],
                'onChangeFunction' => $data['onChangeFunction'],
                'allowedFileTypes' => $data['allowedFileTypes'],
                'swstid' => $data['swstid'],
                'inputChecksum' => $data['inputChecksum'],
        );
        if($table_array['fieldType'] != 'list') {
            $table_array['srlid'] = 0;
        }
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(is_array($data)) {
            $update_array['swsid'] = $data['swsid'];
            $update_array['name'] = $data['name'];
            $update_array['dbColumn'] = $data['dbColumn'];
            $update_array['isDisplayed'] = $data['isDisplayed'];
            $update_array['isReadOnly'] = $data['isReadOnly'];
            $update_array['sequence'] = $data['sequence'];
            $update_array['displayLogic'] = $data['displayLogic'];
            $update_array['fieldType'] = $data['fieldType'];
            $update_array['srlid'] = $data['srlid'];
            $update_array['length'] = $data['length'];
            $update_array['description'] = $data['description'];
            $update_array['comment'] = $data['comment'];
            $update_array['onChangeFunction'] = $data['onChangeFunction'];
            $update_array['allowedFileTypes'] = $data['allowedFileTypes'];
            $update_array['swstid'] = $data['swstid'];
            $update_array['inputChecksum'] = $data['inputChecksum'];
        }
        if($update_array['fieldType'] != 'list') {
            $update_array['srlid'] = 0;
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

////////Definition End
    /* Getter functions-Start */

    public function get_Windowsection() {
        return new SystemWindowsSection($this->data['swsid']);
    }

    public function get_srlid() {
        return new SystemReferenceLists($this->data['srlid']);
    }

}