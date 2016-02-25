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
    const REQUIRED_ATTRS = '';

    public function __construct($id = '', $simple = true) {
        if(isset($id) && !empty($id)) {
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
        $unique_attr = static::UNIQUE_ATTRS;
        if(!empty($unique_attr)) {
            $unique_attrs = explode(',', static::UNIQUE_ATTRS);
            if(is_array($unique_attrs)) {
                foreach($unique_attrs as $attr) {
                    $attr = trim($attr);
                    if(empty($data[$attr]) && $data[$attr] != 0) {
                        $checks = null;
                        break;
                    }
                    $checks[$attr] = $data[$attr];
                }
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

    /**
     * Inserts data into specific DB table after performing validation
     *
     * @global type $db
     * @param array $data   Data to insert
     * @return boolean
     */
    protected function create(array $data) {
        global $db;
        if(!$this->validate_data($data)) {
            return false;
        }
        $db->insert_query(static::TABLE_NAME, $data);
        $this->data = $data;
        $this->{static::PRIMARY_KEY} = $db->last_id();
    }

    abstract protected function update(array $data);
    /**
     * Function to validate data before creating or modifying.
     * This function have to be overriden in the class where it is required.
     *
     * @param array $data   Data to be validated
     * @return boolean  Boolean to specify if data has issues or not
     */
    protected function validate_data(array $data) {
        return true;
    }

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

    public static function get_column($column, $filters = '', array $configs = array()) {
        $data = new DataAccessLayer(static::CLASSNAME, static::TABLE_NAME, static::PRIMARY_KEY);
        return $data->get_column($column, $filters, $configs);
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
        return $this;
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

    protected function validate_requiredfields($data) {
        global $errorhandler;
        $required_fields = static::REQUIRED_ATTRS;
        if(!empty($required_fields)) {
            $required_fields = explode(',', $required_fields);
            if(is_array($required_fields) && is_array($data)) {
                foreach($required_fields as $field) {
                    if(!isset($data[$field]) || empty($data[$field])) {
                        $errorhandler->record('requiredfields', $field);
                        return false;
                    }
                }
                return true;
            }
        }
        return true;
    }

    /**
     *
     * function will check if current object has a conversation based on its id, and table name (and if any additional configs are feeded)
     *
     * if alternative ID is set, the search will only run it and disregard any other filter
     *
     * @param type $configs
     * @return boolean
     */
    public function get_conversation($configs = '') {
        $filters = array('recordId' => $this->data[$this::PRIMARY_KEY], 'tableName' => $this::TABLE_NAME);
        if(is_array($configs)) {
            if(isset($configs['module']) && !empty($configs['module'])) {
                $filters['module'] = $configs['module'];
            }
            if(isset($configs['alternativeId']) && !empty($configs['alternativeId'])) {
                $filters = array('alternativeId' => 'alternativeId');
            }
        }
        $conversation_obj = SystemConversations::get_data(array($filters), array('returnarray' => false));
        if($conversation_obj) {
            return $conversation_obj;
        }
        return false;
    }

    /**
     * function will fill minimum required info to create a conversation in an array , later on serialized AND THEN encoded.
     *
     * if conversation id is send through configs array then only that id will be passed
     * @global type $core
     * @param type $configs
     * @return type
     */
    public function get_conversation_url($configs = array()) {
        global $core;
        if(isset($configs['scid']) && !empty($configs['scid'])) {
            $waddings = '&scid='.intval($configs['scid']);
        }
        else {
            $stuffings = array('recordId' => $this->data[$this::PRIMARY_KEY], 'tableName' => $this::TABLE_NAME, 'title' => $this->get_displayname());
            if(isset($core->input['module']) && !empty($core->input['module'])) {
                if(strpos($core->input['module'], '/')) {
                    $stuffings['module'] = substr($core->input['module'], 0, strpos($core->input['module'], '/'));
                }
            }
            $waddings = '&d$@1á='.base64_encode(serialize($stuffings));
        }
        $url = $core->settings['rootdir'].'/index.php?module=portal/conversation'.$waddings;

        return $url;
    }

}