<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AttendanceAttRecords_class.php
 * Created:        @zaher.reda    Jan 15, 2015 | 5:09:44 PM
 * Last Update:    @zaher.reda    Jan 15, 2015 | 5:09:44 PM
 */

/**
 * Description of AttendanceAttRecords_class
 *
 * @author zaher.reda
 */
class AttendanceAttRecords extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'aarid';
    const TABLE_NAME = 'attendance_attrecords';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = null;
    const REQUIRED_ATTRS = 'uid,time,operation';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function get_user() {
        return new Users($this->data['uid']);
    }

    protected function create(array $data) {
        global $db, $core;
        if(!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        $fields = array('aarid', 'uid', 'operation', 'time', 'lastupdateTime', 'lastupdateOperation');
        foreach($fields as $field) {
            $tablearray[$field] = $data[$field];
        }

        $tablearray['createdOn'] = TIME_NOW;
        $tablearray['createdBy'] = $core->user['uid'];
        $insert = $db->insert_query(self::TABLE_NAME, $tablearray);
        if($insert) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    public function update(array $data) {
        global $db, $core;
        if(!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        $fields = array('aarid', 'uid', 'operation', 'time', 'lastupdateTime', 'lastupdateOperation');
        foreach($fields as $field) {
            $update_array[$field] = $data[$field];
        }
        if($this->time != $update_array['time']) {
            $update_array['lastupdateTime'] = $this->time;
        }
        if($this->operation != $update_array['operation']) {
            $update_array['lastupdateOperation'] = $this->operation;
        }
        $update_array['modifiedOn'] = TIME_NOW;
        $update_array['modifiedBy'] = $core->user['uid'];
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

}