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
    const REQUIRED_ATTRS = 'title,uid,pageName,moduleName';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $fields = array('title', 'alias', 'uid', 'isActive', 'columnCount', 'moduleName', 'moduleId', 'inputChecksum');
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(!is_null($data[$field])) {
                    $table_array[$field] = $data[$field];
                }
            }
        }

        if(is_array($table_array)) {
            $table_array['createdBy'] = $core->user['uid'];
            $table_array['createdOn'] = TIME_NOW;
            $table_array['alias'] = generate_alias($table_array['title']);
//create additional fields that should be automatically generated
            if(empty($table_array['uid'])) {
                $table_array['uid'] = $core->user['uid'];
            }
            $module_info = explode('/', $core->input['module']);
            if(empty($table_array['moduleName']) && !empty($core->input['module'])) {
                if(is_array($module_info)) {
                    $table_array['moduleName'] = $module_info[0];
                }
            }
            if(empty($table_array['pageName']) && !empty($core->input['module'])) {
                if(is_array($module_info)) {
                    $table_array['pageName'] = $module_info[1];
                }
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
            else {
                $this->errorcode = 3;
                return $this;
            }
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $core;
        $fields = array('title', 'alias', 'uid', 'isActive', 'columnCount', 'moduleName', 'moduleId', 'inputChecksum');
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
            $table_array['alias'] = generate_alias($table_array['title']);
//create additional fields that should be automatically generated
            if(empty($table_array['uid'])) {
                $table_array['uid'] = $core->user['uid'];
            }

            $module_info = explode('/', $core->input['module']);
            if(empty($table_array['moduleName']) && !empty($core->input['module'])) {
                if(is_array($module_info)) {
                    $table_array['moduleName'] = $module_info[0];
                }
            }
            if(empty($table_array['pageName']) && !empty($core->input['module'])) {
                if(is_array($module_info)) {
                    $table_array['pageName'] = $module_info[1];
                }
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
     * get all active widgets assigned to current instance of dashboard
     * @return boolean or array of system widgets
     */
    public function get_assigned_widgets($configs = array()) {
        $assigned_widgets = SystemAssignedWidgets::get_data(array(self::PRIMARY_KEY => $this->data[self::PRIMARY_KEY]), array('returnarray' => true, 'order' => 'sequence'));
        if(is_array($assigned_widgets)) {
//check if widget is set as active before returning it
            $active_widgets = array_map(
                    function($object) {
                $widget_instanceobj = $object->get_widgetinstance();
                if(is_object($widget_instanceobj) && $widget_instanceobj->isActive == 1) {
                    return $widget_instanceobj;
                }
            }, $assigned_widgets);
            if(is_array($active_widgets)) {
                return $active_widgets;
            }
        }
        return false;
    }

    /**
     * responsible for parsing this instance of dashboard containing all active widgets
     * @global type $core
     * @return boolean
     * @return string
     */
    public function parse_dashboard() {
        global $core;
//get assigned widgets of the dashboard
        $activewidgets = $this->get_assigned_widgets();
        if(is_array($activewidgets)) {
            $widgets_output = array();
            foreach($activewidgets as $widgetinstance) {
                if(empty($widgetinstance->{SystemWidgetInstances::PRIMARY_KEY})) {
                    continue;
                }
//parse single widget instance and concatenate it to the overall output array
                $single_instance_output = $widgetinstance->parse_widgetinstance($this->data[self::PRIMARY_KEY]);
                if($single_instance_output) {
                    $widgets_output[$widgetinstance->inputChecksum] = $single_instance_output;
                }
            }
            if(is_array($widgets_output)) {
                return $this->parse_sortablewidgets($widgets_output);
            }
        }
        return false;
    }

    /**
     * Returns extra js that should work after creation of a dashboard and loads the dashboard content div
     * @global type $template
     * @global type $core
     * @return type
     */
    public function generate_extrajs() {
        global $template, $core;
        $dashboard = $this->get();
        eval("\$extrajs = \"".$template->get('dashboard_global_extrajs')."\";");
        return $extrajs;
    }

    /**
     *  Returns dashboard management template
     * @return boolean in case of failure or empty responses
     * @return string html template of dashboard management page
     */
    public function parse_managedashboard_page() {
        global $template;
        $dashboard = $this->get();
        $dashboard_title = $this->get_displayname();
        $widget_obj = new SystemWidgets();
        $widget_list = $widget_obj->parse_widgetselect_form($dashboard);
        $dashboard_output = $this->parse_dashboard();
        eval("\$output = \"".$template->get('dashboard_global_managedashboard')."\";");
        if(!empty($output)) {
            return $output;
        }
        return false;
    }

    /**
     * Parse main dashboard page where you either create a dashboard or parse the existing dashboard
     * @global type $core
     * @global type $template
     * @global type $lang
     * @return type
     */
    public function parse_dashboardpage() {
        global $core, $template, $lang, $current_module;
        if(!empty($core->input['module'])) {
            $module_info = explode('/', $core->input['module']);
        }

        if(empty($module_info[0])) {
            $module_info = $current_module;
        }

        if(is_array($module_info)) {
            $dashboard_obj = SystemDashboard::get_data(array('uid' => $core->user['uid'], 'pageName' => $module_info[1], 'moduleName' => $module_info[0]), array('returnarray' => false));
        }
        if(is_object($dashboard_obj)) {
            $page = $dashboard_obj->parse_managedashboard_page();
        }
        else {
            $dashboard_checksum = generate_checksum();
            $add_dashboard_button = '<a style="cursor: pointer;" id="showdashboard_'.$dashboard_checksum.'_portal/dashboard_loadpopupbyid" rel="showdashboard'.$dashboard_checksum.'" ><button class="btn btn-success">'.$lang->createdashboard.'</button></a>';
            eval("\$page = \"".$template->get('dashboard_general')."\";");
        }
        return $page;
    }

    /**
     * parse all widgets
     * @global type $lang
     * @global type $template
     * @param array $widgets
     * @return type
     */
    public function parse_sortablewidgets(array $widgets) {
        global $lang, $template;
        $dashid = $this->data[self::PRIMARY_KEY];
        foreach($widgets as $instance_inputchecksum => $widget) {
            $widgets_output.='<li id="widgetinstancelist_'.$instance_inputchecksum.'_item">'.$widget.'</li>';
        }
        eval("\$widgets_grid = \"".$template->get('dashboard_widgetsgrid')."\";");
        return $widgets_grid;
    }

    public function createdefaultdashboard_home($userids = array()) {
        global $db;
        $dashboard_array = array('title' => 'Welcome To OCOS', 'alias' => 'welcome-to-ocos', 'inputChecksum' => generate_checksum(), 'isActive' => 1, 'columnCount' => 2, 'moduleName' => 'portal', 'pageName' => 'dashboard', 'createdBy' => -1, 'createdOn' => TIME_NOW);
        foreach($userids as $uid) {
            $dashboard_array['uid'] = intval($uid);
            $createdash_query = $db->insert_query(self::TABLE_NAME, $dashboard_array);
            if(!$createdash_query) {
                continue;
            }
            $dashid = $db->last_id();
            $assignedinstances_array = array(1 => GadgetTimezones::CLASSNAME, 2 => GadgetCalendarToday::CLASSNAME, 3 => GadgetLeaveBalance::CLASSNAME, 4 => GadgetFxRates::CLASSNAME, 5 => GadgetPendLvsYrApproval::CLASSNAME);
            foreach($assignedinstances_array as $sequence => $classname) {
                $created_instance = $classname::create_defaultwidget($classname, $uid, $sequence);
                if($created_instance) {
                    $assigned_instance_data = array(self::PRIMARY_KEY => intval($dashid), 'swgiid' => intval($created_instance), 'createdOn' => TIME_NOW, 'createdBy' => -1, 'sequence' => intval($sequence));
                    $creatassigendinstance_query = $db->insert_query(SystemAssignedWidgets::TABLE_NAME, $assigned_instance_data);
                }
            }
        }
        return true;
    }

}