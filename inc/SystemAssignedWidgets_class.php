<?php
/* -------Definiton-START-------- */

class SystemAssignedWidgets extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'sawid';
    const TABLE_NAME = 'system_assignedwidgets';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'sdid,swgiid';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = '';
    const REQUIRED_ATTRS = 'sdid,swgiid';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $fields = array('sdid', 'swgiid', 'sequence');
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(!is_null($data[$field])) {
                    $table_array[$field] = $data[$field];
                }
            }
        }
        if(is_array($table_array)) {
            $table_array['createdOn'] = TIME_NOW;
            $table_array['createdBy'] = $core->user['uid'];
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
        $fields = array('sdid', 'swgiid', 'sequence');
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(!is_null($data[$field])) {
                    $table_array[$field] = $data[$field];
                }
            }
        }
        if(is_array($table_array)) {
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
    /* -------GETTER FUNCTIONS-START-------- */
    /**
     *
     * @return \SystemDashboard
     */
    public function get_dashboard() {
        return new SystemDashboard($this->data['sdid']);
    }

    /**
     *
     * @return \SystemWidgetInstances
     */
    public function get_widgetinstance() {
        return new SystemWidgetInstances($this->data['swgiid']);
    }

    /* -------GETTER FUNCTIONS-END-------- */
}