<?php

/**
 * serializedConfig is a serialized multidimensional array with the following format :
 * array('required'=>array(),array())
 * It needs to be multi dimensonal since there is a check on the required fields when parsing and saving
 */
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
        $fields = array('swdgid', 'moduleId', 'moduleName', 'title', 'alias', 'isActive', 'isTable', 'isForm', 'isChart', 'serializedConfig', 'className');
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
        $fields = array('swdgid', 'moduleId', 'moduleName', 'title', 'alias', 'isActive', 'isTable', 'isForm', 'isChart', 'serializedConfig', 'className');
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
        global $template, $lang;
        $gadget_object = $this->get_gadgetobject();
        if($gadget_object) {
            if(is_object($gadget_object)) {
                return $gadget_object->parse($instancedata);
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
        if(!file_exists(ROOT.INC_ROOT.$gadget_class.'_class.php') && !class_exists($gadget_class)) {
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
            $gadget_obj = new $gadget_class();
            return $gadget_obj;
        }
        return false;
    }

    /**
     *
     * @param array $config
     * @return array
     */
    public function get_active_widgets($config = '') {
        $active_widgets = self::get_data(array('isActive' => 1), array('returnarray' => true));
        return $active_widgets;
    }

    /**
     * Parse select list of active widget types
     * @global type $lang
     * @global type $template
     * @param type $config
     * @return boolean
     */
    public function parse_active_widgets($config = '') {
        global $lang, $template;
        $active_widgets = self::get_active_widgets();
        if(!$active_widgets) {
            return false;
        }
        foreach($active_widgets as $widget_obj) {
            $widget_options.='<li><a id="select_'.$widget_obj->{self::PRIMARY_KEY}.'_widgettype" href="#">'.$widget_obj->get_displayname().'</a></li>';
        }
        eval("\$widgetselectlist = \"".$template->get('system_parseactivewidgets')."\";");
        return $widgetselectlist;
    }

    /**
     *
     * @global type $template
     * @param array $dashboard
     * @param type $config
     * @return type
     */
    public function parse_widgetselect_form(array $dashboard, $config = '') {
        global $template, $lang;
        //load dashboad lang file
        $lang->load('dashboard');
        $widget_list = self::parse_active_widgets($config);

        eval("\$widgetselectform= \"".$template->get('system_parse_widgetselectform')."\";");
        return $widgetselectform;
    }

}