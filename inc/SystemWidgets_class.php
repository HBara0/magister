<?php
/* -------Definiton-START-------- */

class SystemWidgets extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'swdgid';
    const TABLE_NAME = 'system_widgets';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'moduleId,moduleName,title';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'title';
    const REQUIRED_ATTRS = 'title';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $fields = array('swdgid', 'moduleId', 'moduleName', 'title', 'alias', 'isActive', 'isTable', 'isForm', 'isChart', 'availableConfig', 'className');
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(!is_null($data[$field])) {
                    $table_array[$field] = $data[$field];
                }
            }
        }
        $this->errorcode = 3;
        if(is_array($table_array)) {
            $table_array['createdOn'] = TIME_NOW;
            $table_array['createdBy'] = $core->user['id'];
            if(!$this->validate_requiredfields($table_array)) {
                $this->errorcode = 3;
                return $this;
            }
            $query = $db->insert_query(self::TABLE_NAME, $table_array);
            if($query) {
                $this->errorcode = 0;
                $this->data[self::PRIMARY_KEY] = $db->last_id();
            }
            else {
                $this->errorcode = 3;
            }
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        $fields = array('swdgid', 'moduleId', 'moduleName', 'title', 'alias', 'isActive', 'isTable', 'isForm', 'isChart', 'availableConfig', 'className');
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(!is_null($data[$field])) {
                    $table_array[$field] = $data[$field];
                }
            }
        }
        if(is_array($table_array)) {
            $table_array['modifiedOn'] = TIME_NOW;
            $table_array['modifiedBy'] = $core->user['id'];
            if(!$this->validate_requiredfields($table_array)) {
                $this->errorcode = 3;
                return $this;
            }
            $db->update_query(self::TABLE_NAME, $table_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
            $this->errorcode = 0;
        }
        else {
            $this->errorcode = 3;
        }
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    /**
     * responsible for linking the widget with the gadget object and
     * then parse the gadget
     * @param type $instancedata
     * @return boolean
     * @return string
     */
    public function parse_widget($instancedata) {
        $gadget_object = $this->get_gadgetobject();
        if($gadget_object) {
            if(is_object($gadget_object)) {
                return $gadget_object->parse();
            }
        }
        return false;
    }

    /**
     *
     * get the class name recorded in the db row of current instance if existing, else return false
     * @global type $errorhandler
     * @global type $lang
     * @return boolean
     * @return string
     */
    protected function get_gadgetclass() {
        global $errorhandler, $lang;
        if(empty($this->data['className'])) {
            return false;
        }
        $gadget_class = $this->data['className'];
        if(!file_exists($gadget_class) && !class_exists($gadget_class)) {
            $errorhandler->record($lang->missingfile, $gadget_class);
            return false;
        }

        return $gadget_class;
    }

    /**
     * get object of saved class name if existing, else return false
     * @return boolean
     * @return object
     */
    protected function get_gadgetobject() {
        if(empty($this->data['className'])) {
            return false;
        }
        $gadget_class = $this->get_gadgetclass();
        if($gadget_class) {
            return $gadgetinstance_obj = new $gadget_class();
        }
        return false;
    }

}