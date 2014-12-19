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
    const UNIQUE_ATTRS = null;

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

    public function save(array $data = array()) {
        if(empty($data)) {
            $data = $this->data;
        }
        if(isset($this->data[static::PRIMARY_KEY]) && !empty($this->data[static::PRIMARY_KEY])) {
            return $this->update($data);
        }
        if(empty($data[static::PRIMARY_KEY])) {
            unset($data[static::PRIMARY_KEY]);
        }

        if(isset($data['inputChecksum']) && !empty($data['inputChecksum'])) {
            $object = self::get_data(array('inputChecksum' => $data['inputChecksum']));
            if(is_object($object)) {
                return $object->update($data);
            }
        }
        $unique_attrs = explode(',', static::UNIQUE_ATTRS);
        if(is_array($unique_attrs)) {
            foreach($unique_attrs as $attr) {
                if(empty($data[$attr])) {
                    $checks = null;
                    break;
                }
                $checks[$attr] = $data[$attr];
            }
        }
        if(is_array($checks)) {
            $object = self::get_data($checks);
            if(is_object($object)) {
                return $object->update($data);
            }

            if(is_array($object)) {
                foreach($object as $obj) {
                    $obj->delete();
                }
            }
        }
        return $this->create($data);
    }

    abstract protected function create(array $data);
    abstract protected function update(array $data);
    public function delete() {
        global $db;
        if(empty($this->data[static::PRIMARY_KEY]) && empty($this->data['inputChecksum'])) {
            return false;
        }
        else if(empty($this->data[static::PRIMARY_KEY]) && !empty($this->data['inputChecksum'])) {
            $query = $db->delete_query(static::TABLE_NAME, 'inputChecksum ="'.$this->data['inputChecksum'].'"');
        }
        else {
            $query = $db->delete_query(static::TABLE_NAME, static::PRIMARY_KEY.'='.intval($this->data[static::PRIMARY_KEY]));
        }
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