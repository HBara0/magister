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
 * The global abstract class that
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

    /**
     *
     * @global \Log $log
     * @param array $data   Data to be saved
     * @return      Function that will perform the saving
     */
    public function save(array $data = array()) {
        global $log;
        if(empty($data)) {
            $data = $this->data;
        }

        $log->record($this->data[static::PRIMARY_KEY], $data['inputChecksum']);

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
                $attr = trim($attr);
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
        elseif(empty($this->data[static::PRIMARY_KEY]) && !empty($this->data['inputChecksum'])) {
            $query = $db->delete_query(static::TABLE_NAME, 'inputChecksum="'.$db->escape_string($this->data['inputChecksum']).'"');
        }
        else {
            $query = $db->delete_query(static::TABLE_NAME, static::PRIMARY_KEY.'='.intval($this->data[static::PRIMARY_KEY]));
        }
        if($query) {
            return true;
        }
        return false;
    }

    public static function get_data($filters = '', array $configs = array()) {
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

    /**
     * Returns a specific value from the array of object values.
     *
     * @param string $name The index of the array.
     *
     * @return string|boolean $this->data The value of that specific index.
     */
    public function __get($name) {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }
        return false;
    }

    /**
     * Returns the id of the object.
     *
     * @return int|null $this->data The id of the object.
     */
    public function get_id() {
        return $this->data[static::PRIMARY_KEY];
    }

    /**
     * Returns the display name of the object.
     *
     * @uses AbstractClass::PRIMARY_KEY to specify the requirement attribute.
     *
     * @return string|null $this->data The display name of the object.
     */
    public function get_displayname() {
        return $this->data[static::DISPLAY_NAME];
    }

    public function get_uniqueattributes() {
        return $this->data[static::UNIQUE_ATTRS];
    }

    /**
     * @return string|null The unique attributes of the object
     */
    public function get_uniqueattributes_columns() {
        return $this::UNIQUE_ATTRS;
    }

    /**
     * @return array|null The object data
     */
    public function get() {
        return $this->data;
    }

    public function get_errorcode() {
        return $this->errorcode;
    }

    public function __toString() {
        if(is_null($this->data[static::DISPLAY_NAME])) {
            return '';
        }
        return $this->get_displayname();
    }

    function __sleep() {

    }

    function __wakeup() {

    }

    function __destruct() {

    }

}