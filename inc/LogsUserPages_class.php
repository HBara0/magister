<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * This class has been cread to mainly log in users activity by page and module and generate user activity
 * reports based on these logs
 *
 *
 * $id: LogsUserPages_class1.php
 * Created:        @hussein.barakat    16-Feb-2016 | 14:43:58
 * Last Update:    @hussein.barakat    16-Feb-2016 | 14:43:58
 */

class LogsUserPages extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'lupid';
    const TABLE_NAME = 'logs_userpages';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'uid,page,module,time';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = '';
    const REQUIRED_ATTRS = '';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $fields = array('uid', 'page', 'module', 'time');
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(!is_null($data[$field])) {
                    $table_array[$field] = $data[$field];
                }
            }
        }
        $this->errorcode = 3;
        if(is_array($table_array)) {
            $query = $db->insert_query(self::TABLE_NAME, $table_array);
            if($query) {
                $this->errorcode = 0;
                $this->data[self::PRIMARY_KEY] = $db->last_id();
            }
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        $fields = array('uid', 'page', 'module', 'time');
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(!is_null($data[$field])) {
                    $table_array[$field] = $data[$field];
                }
            }
        }
        $this->errorcode = 3;
        if(is_array($table_array)) {

            $db->update_query(self::TABLE_NAME, $table_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
            $this->errorcode = 0;
        }
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    /**
     *
     * @return \Users which has his id logged in the object
     */
    public function get_user() {
        return new Users($this->data['uid']);
    }

    /**
     * this function is responsible for all logging actions , it creates/updates the row and afterwards it removes past rows spanning over a
     * limited time (put in the setting file)
     * @param array $data
     * @return boolean
     */
    public function record_log(array $data) {
        if(is_array($data)) {
            $data['time'] = TIME_NOW;
            $this->set($data);
            $this->save($data);
            if(!$this->errorcode == 0) {
                return false;
            }
            $this->clear_logs();
        }
        return true;
    }

    /**
     *
     * main role for this function is to clear all logs for current object's user spanning before a period of time (period is in the settings file under
     * 'clearlogperiod' variable)
     * @global type $db
     * @global type $core
     * @return boolean
     */
    protected function clear_logs() {
        global $db, $core;
        /**
         * Avoid dead-end by setting default rotation days in case setting is missing
         */
        if(empty($core->settings['clearlogperiod'])) {
            $core->settings['clearlogperiod'] = 30;
        }
        $sql = 'DELETE FROM '.Tprefix.self::TABLE_NAME.' WHERE uid = '.$this->data['uid'].' AND time < '.strtotime('-'.$core->settings['clearlogperiod'].' days');
        $query = $db->query($sql);
        if($query) {
            return true;
        }
        return false;
    }

    public function get_frequentitems($type = 'module', $limit = 3) {
        global $db, $core;
        /**
         * Query to be changed when DAL support functions in SELECT
         */
        $sql = $db->query('SELECT *, COUNT(*) as timeAccessed FROM '.self::TABLE_NAME.' WHERE uid='.$core->user_obj->get_id().' GROUP BY module ORDER BY timeAccessed DESC LIMIT 0, '.intval($limit));
        $items = array();
        if($db->num_rows($sql) > 1) {
            while($item = $db->fetch_assoc($sql)) {
                $items[$item['module']] = $item;
            }
        }

        return $items;
    }

}