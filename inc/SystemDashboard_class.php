<?php
/* -------Definiton-START-------- */

class SystemDashboard extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'sdid';
    const TABLE_NAME = 'system_dashboard';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'alias,uid';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'title';
    const REQUIRED_ATTRS = '';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $fields = array('title', 'alias', 'uid', 'isActive', 'columnCount', 'moduleName', 'moduleId');
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
            else {
                $this->errorcode = 3;
                return $this;
            }
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        $fields = array('title', 'alias', 'uid', 'isActive', 'columnCount', 'moduleName', 'moduleId');
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
     * get all active widgets assigned to current instance of dashboard
     * @return boolean or array of system widgets
     */
    public function get_active_widgets() {
        $assigned_widgets = SystemAssignedWidgets::get_data(array(self::PRIMARY_KEY => $this->data[self::PRIMARY_KEY]), array('returnarray' => true, 'order' => 'sequence'));
        if(is_array($assigned_widgets)) {
            //check if widget is set as active before returning it
            $active_widgets = array_map(
                    function($object) {
                if($object->isActive == 1) {
                    return $object;
                }
            }, $assigned_widgets);
            if(is_array($active_widgets)) {
                return $active_widgets;
            }
        }
        return false;
    }

}