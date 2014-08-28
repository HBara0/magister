<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * Abstract class skeleton
 * $id: AbstractClass.php
 * Created:        @zaher.reda    Jul 30, 2014 | 5:10:17 PM
 * Last Update:    @zaher.reda    Jul 30, 2014 | 5:10:17 PM
 */

/**
 * Description of AbstractClass
 *
 * @author zaher.reda
 */
Abstract class AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = '';
    const TABLE_NAME = '';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        if(isset($id)) {
            $this->read($id, $simple);
        }
    }

    protected function read($id, $simple = true) {
        global $db;

        $query_select = '*';
        if($simple == true) {
            $query_select = static::SIMPLEQ_ATTRS;
        }
        $this->data = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.static::TABLE_NAME.' WHERE '.static::PRIMARY_KEY.'='.intval($id)));
    }

    abstract public function save(array $data = array());
    abstract protected function create(array $data);
    abstract protected function update(array $data);
    public function delete() {
        global $db;
        $query = $db->delete_query(static::TABLE_NAME, static::PRIMARY_KEY.'='.intval($this->data[static::PRIMARY_KEY]));
        if($query) {
            return true;
        }
        return false;
    }

    public static function get_data($filters = '', $configs = array()) {
        $data = new DataAccessLayer(static::CLASSNAME, static::TABLE_NAME, static::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public static function get_data_byattr($attr, $value) {
        $data = new DataAccessLayer(static::CLASSNAME, static::TABLE_NAME, static::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public function __isset($name) {
        return isset($this->data[$name]);
    }

    public function set(array $data) {
        foreach($data as $name => $value) {
            $this->data[$name] = $value;
        }
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function __get($name) {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }
        return false;
    }

    public function get_displayname() {
        return $this->data[static::DISPLAY_NAME];
    }

    public function get() {
        return $this->data;
    }

    public function get_errorcode() {
        return $this->errorcode;
    }

    public function __toString() {
        return $this->get_displayname();
    }

    function __sleep() {

    }

    function __wakeup() {

    }

    function __destruct() {

    }

}