<?php

/**
 * serializedConfig is a serialized multidimensional array with the following format :
 * array('required'=>array(),array())
 * It needs to be multi dimensonal since there is a check on the required fields when parsing and saving
 */
/* -------Definiton-START-------- */

class SystemWidgetInstances extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'swgiid';
    const TABLE_NAME = 'system_widgetinstances';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'title,uid,serializedConfig';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'title';
    const REQUIRED_ATTRS = 'title,uid,inputChecksum';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $fields = array('title', 'alias', 'uid', 'serializedConfig', 'type', 'isActive', 'swdgid', 'inputChecksum');
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
            if(empty($table_array['uid'])) {
                $table_array['uid'] = $core->user['uid'];
            }
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
        global $db, $core;
        $fields = array('title', 'alias', 'uid', 'serializedConfig', 'type', 'isActive', 'swdgid', 'inputChecksum');
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
            if(empty($table_array['uid'])) {
                $table_array['uid'] = $core->user['uid'];
            }
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
     * @global type $template
     * @return boolean
     */
    public function parse_widgetinstance() {
        global $template;
        $widget = $this->get_widget();
        if(is_object($widget) && !empty($widget->{SystemWidgets::PRIMARY_KEY})) {
            $header = $this->{SystemWidgetInstances::DISPLAY_NAME};
            $body = $widget->parse_widget($this->data);
            eval("\$widget = \"".$template->get('system_dashboard_defaultwidget')."\";");
            return $widget;
        }
        return false;
    }

    /**
     * cutom save function dealing with various levels of input validation and saving methods
     * @param array $input
     * @return \SystemWidgetInstances
     */
    public function save_instance(array $input) {
        //check basic input before going through any saving method
        $basic_input_validation = $this->validate_basic_input($input);
        if(!$basic_input_validation) {
            $this->errorcode = 3;
            return $this;
        }
        //arrange input variables to fit with the DB
        $input['serializedConfig'] = serialize($input['settings']);
        $this->set($input);
        $this->save();
        if($this->get_errorcode() != 0) {
            return $this;
        }
        $this->errorcode = $this->assign_instancetodashboard($input);
        return $this;
    }

    /**
     * validatet basic input array
     * @global type $errorhandler
     * @global type $lang
     * @param array $input
     * @return boolean
     */
    public function validate_basic_input(array $input) {
        global $errorhandler, $lang;
        if(empty($input[SystemDashboard::PRIMARY_KEY])) {
            $errorhandler->record($lang->missingfields, $lang->dashboard);
            return false;
        }
        if(empty($input[SystemWidgets::PRIMARY_KEY])) {
            $errorhandler->record($lang->missingfields, $lang->widgettype);
            return false;
        }

        if(!$this->validate_settings($input)) {
            return false;
        }

        return true;
    }

    /**
     * asign currenct widget instance to related dashboard
     * @param type $input
     * @return type
     */
    public function assign_instancetodashboard($input) {
        if(empty($input[self::PRIMARY_KEY]) && !empty($input['inputChecksum'])) {
            $current_object = self::get_data(array('inputChecksum' => $input['inputChecksum']), array('returnarray' => false));
            if(is_object($current_object) && !empty($current_object->{self::PRIMARY_KEY})) {
                $this->data[self::PRIMARY_KEY] = $current_object->{self::PRIMARY_KEY};
            }
        }
        $assign_data = array(SystemDashboard::PRIMARY_KEY => $input[SystemDashboard::PRIMARY_KEY], self::PRIMARY_KEY => $this->data[self::PRIMARY_KEY], 'sequence' => $input['sequence']);
        $assigned_widgetdashboard_obj = new SystemAssignedWidgets();
        $assigned_widgetdashboard_obj->set($assign_data);
        $assigned_widgetdashboard_obj->save();
        return $assigned_widgetdashboard_obj->get_errorcode();
    }

    /**
     * Validate addtional settings by matching input settings with widget settings
     * @param array $input
     * @return boolean
     */
    public function validate_settings(array $input) {
        global $lang, $errorhandler;
        //get widget object of current instance
        $widget_obj = new SystemWidgets(intval($input[SystemWidgets::PRIMARY_KEY]));
        if(!is_object($widget_obj) || is_empty($widget_obj->{SystemWidgets::PRIMARY_KEY})) {
            $errorhandler->record($lang->missingfields, $lang->widgettype);
            return false;
        }
        //get the settings required for the widget and validate the input accordingly
        $widget_settings_serialized = $widget_obj->serializedConfig;
        if($widget_settings_serialized) {
            $widget_settings = unserialize($widget_settings_serialized);
            //check if widget requires extra fields
            if(is_array($widget_settings) && isset($widget_settings['required'])) {
                if(is_array($widget_settings['required'])) {
                    //check if input has all required extra fields
                    if(empty($input['settings'])) {
                        $errorhandler->record($lang->missingfields, $lang->widgetsettings);
                        return false;
                    }
                    foreach($widget_settings['required'] as $fieldtitle => $requiredsetting) {
                        if(empty($input['settings']['required'][$fieldtitle])) {
                            $errorhandler->record($lang->missingfields, $lang->$fieldtitle);
                            return false;
                        }
                    }
                }
            }
        }
        return true;
    }

}