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
class CalendarTaskShare extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'ctsid';
    const TABLE_NAME = 'calendar_tasks_sharewith';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'ctsid,ctid,uid';
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

            /* get exist users for the current task */
            $existing_users = $this->get_task()->get_shared_users();

            /* get the difference between the exist users and the slected users */
            if(is_array($existing_users)) {
                $existing_users = array_keys($existing_users);
                $users_toremove = array_diff($existing_users, $data);
                if(!empty($users_toremove)) {
                    $db->delete_query(self::TABLE_NAME, 'uid IN ('.$db->escape_string(implode(',', $users_toremove)).') AND ctid='.$task_data['ctid']);
                    $this->errorcode = 0;
                }
            }
            foreach($data as $uid) {
                $task_data['createdBy'] = $core->user['uid'];
                $task_data['createdOn'] = TIME_NOW;
                $task_data['uid'] = $core->sanitize_inputs($uid);
                if(!value_exists(self::TABLE_NAME, 'uid', $uid, ' ctid='.$this->data['ctid'])) {
                    $db->insert_query(self::TABLE_NAME, $task_data);
                }
            }
            $this->errorcode = 0;
            return true;
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

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function get_user() {
        return new Users($this->data['uid']);
    }

    public function get_task() {
        return new Tasks($this->data['ctid']);
    }

    public function get_errorcode() {
        return parent::get_errorcode();
    }

    public function get() {
        return parent::get();
    }

}