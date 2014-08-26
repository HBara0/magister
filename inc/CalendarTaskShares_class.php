<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: CalendarTaskShare_class.php
 * Created:        @tony.assaad    Aug 20, 2014 | 10:55:49 AM
 * Last Update:    @tony.assaad    Aug 20, 2014 | 10:55:49 AM
 */

/**
 * Description of CalendarTaskShare_class
 *
 * @author tony.assaad
 */
class CalendarTaskShares extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'ctsid';
    const TABLE_NAME = 'calendar_tasks_shares';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'ctsid, ctid, uid';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            if(is_empty($data['ctid'])) {
                return;
            }
            $task_data['ctid'] = $data['ctid'];
            unset($data['ctid']);


            /* get the difference between the exist users and the slected users */
//            if(is_array($existing_users)) {
//                $existing_users = array_keys($existing_users);
//                $users_toremove = array_diff($existing_users, $data);
//                if(!empty($users_toremove)) {
//                    $users_toremove = array_map(intval, $users_toremove);
//                    $db->delete_query(self::TABLE_NAME, 'uid IN ('.implode(',', $users_toremove).') AND ctid='.intval($task_data['ctid']));
//                    $this->errorcode = 0;
//                }
//            }

            $task_data['createdBy'] = $core->user['uid'];
            $task_data['createdOn'] = TIME_NOW;
            $task_data['uid'] = intval($data['uid']);
            if(!value_exists(self::TABLE_NAME, 'uid', $data['uid'], 'ctid='.intval($this->data['ctid']))) {
                $db->insert_query(self::TABLE_NAME, $task_data);
            }
        }
    }

    public function save(array $data = array()) {
//get object of and the id and set data and save
        if(empty($data)) {
            $data = $this->data;
        }
        if(!empty($data['ctid'])) {
            // $latest_taskshared = CalendarTaskShare::get_data('ctid='.intval($data['ctid']));
        }

        if(is_object($latest_taskshared)) {
            $this->update($data);
        }
        else {

            if(!is_object($latest_taskshared)) {
                $this->create($data);
            }
            else {
                $latest_taskshared->update($data);
            }
        }

        $this->errorode = 0;
    }

    protected function update(array $data) {

    }

//    protected function delete() {
//        global $db;
//        $query = $db->delete_query(self::TABLE_NAME, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
//        if($query) {
//            return true;
//        }
//        return false;
//    }

    public function get_user() {
        return new Users($this->data['uid']);
    }

    public function get_task() {
        return new Tasks($this->data['ctid']);
    }

}