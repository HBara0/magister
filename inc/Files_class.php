<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Files_class.php
 * Created:        @rasha.aboushakra    Mar 17, 2016 | 3:58:15 PM
 * Last Update:    @rasha.aboushakra    Mar 17, 2016 | 3:58:15 PM
 */

/**
 * Description of Files_class
 *
 * @author rasha.aboushakra
 */
class Files extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'fid';
    const TABLE_NAME = 'files';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {

    }

    public function save(array $data = array()) {
        global $db, $log;
        $query = $db->insert_query(self::TABLE_NAME, $policies_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            $this->errorcode = 0;
        }
    }

    protected function update(array $data) {
        global $db, $log;
        $query = $db->update_query(self::TABLE_NAME, $data_array, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        if($query) {
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
        }
    }

    public function get_displayname() {
        return $this->data[self::DISPLAY_NAME];
    }

}