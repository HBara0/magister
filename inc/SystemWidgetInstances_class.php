<?php
/* -------Definiton-START-------- */

class SystemWidgetInstances extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'swgiid';
    const TABLE_NAME = 'system_widgetinstances';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'title,uid,jsonConfig';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'title';
    const REQUIRED_ATTRS = 'title,uid';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $fields = array('title', 'alias', 'uid', 'jsonConfig', 'type', 'isActive', 'swdgid');
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(!is_null($data[$field])) {
                    $table_array[$field] = $data[$field];
                }
            }
        }
        if(is_array($table_array)) {
            $table_array['createdBy'] = $core->user['id'];
            $table_array['createdOn'] = TIME_NOW;
            if(!$this->validate_requiredfields($table_array)) {
                $this->errorcode = 3;
                return $this;
            }
            $query = $db->insert_query(self::TABLE_NAME, $table_array);
            if($query) {
                $this->errorcode = 0;
                $this->data[self::PRIMARY_KEY] = $db->last_id();
            }
        }
        else {
            $this->errorcode = 3;
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        $fields = array('title', 'alias', 'uid', 'jsonConfig', 'type', 'isActive', 'swdgid');
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(!is_null($data[$field])) {
                    $table_array[$field] = $data[$field];
                }
            }
        }
        if(is_array($table_array)) {
            $table_array['modifiedBy'] = $core->user['id'];
            $table_array['modifiedOn'] = TIME_NOW;
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
     *
     * @return \SystemWidgets
     */
    public function get_widget() {
        return new SystemWidgets(intval($this->data[SystemWidgets::PRIMARY_KEY]));
    }

    /**
     * links widgetinstance with widget and parses the later, else if no widgetinstance is found returns false
     * @return boolean
     * @return string
     */
    public function parse_widgetinstance() {
        $widget = $this->get_widget();
        if(is_object($widget) && !empty($widget->{SystemWidgets::PRIMARY_KEY})) {
            return $widget->parse_widget($this->data);
        }
        return false;
    }

}