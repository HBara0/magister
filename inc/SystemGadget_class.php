<?php
/*
 * Copyright Â© 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: SystemGadget.php
 * Created:        @zaher.reda    Feb 11, 2016 | 10:35:55 PM
 * Last Update:    @zaher.reda    Feb 11, 2016 | 10:35:55 PM
 */

Abstract class SystemGadget extends AbstractClass {
    protected $data = array();
    protected $widget_id = '';

    const DISPLAY_NAME = 'title';

    public function __construct() {
        $widget_obj = new SystemWidgets($this->get_widgetid());
        $this->data = $widget_obj->get();
    }

    //main abstract class functions - START
    public function create(array $data) {

    }

    public function update(array $data) {

    }

    public function get_widgetid() {
        return $this->widget_id;
    }

    //main abstract class functions - END
    /**
     * get system widget related to this instance of gadget through the
     * classname variable in the widget row
     * @return \SystemWidgets|boolean
     */
    public function get_systemwidget() {
        $widget = SystemWidgets::get_data(array('className' => self::CLASSNAME), array('returnarray' => false));
        if(is_object($widget)) {
            return $widget;
        }
        if(self::WIDGET_ID) {
            $widget = new SystemWidgets(intval(self::WIDGET_ID));
            if(is_object($widget) && !empty($widget->{SystemWidgets::PRIMARY_KEY})) {
                return $widget;
            }
            return false;
        }
    }

    /**
     * get settings fields set in the database for the widget.
     * These fields should be filled by the user each time he creates a widget isntance
     * @param type $existingwidget
     * @return boolean
     */
    public function get_settings($existingwidget = array()) {
        if(!empty($existingwidget) && is_array($existingwidget)) {
            $serialized_settings = $existingwidget['serializedConfig'];
        }
        else {
            $serialized_settings = $this->data['serializedConfig'];
        }
        if($serialized_settings) {
            $unserialized_settings = unserialize($serialized_settings);
            if($unserialized_settings) {
                return $unserialized_settings;
            }
        }
        return false;
    }

    /**
     * Function will parse the gadget's settings form for the user to fill.
     * Funtion handles both creation and modify cases
     * @param array $basicids containig widget id and dashboard id
     * @param type $existingwidget
     * @param type $configs
     * @return boolean
     */
    public function parse_gadget_settings(array $basicids, $existingwidget = array(), $configs = array()) {
        global $lang;
        //get settings array for current widget
        $settings = $this->get_settings($existingwidget);
        if(!is_array($settings)) {
            return false;
        }
        //loop through each setting and parse the related input field by type
        foreach($settings as $type => $type_settings) {
            if(is_array($type_settings)) {
                $sectiontitle = '';
                if(!empty($lang->$type) && is_string($lang->$type)) {
                    $sectiontitle = $lang->$type;
                }
                $inputs.='<div class="panel panel-default">';
                $inputs.='<div class="panel-heading"><h4>'.trim($sectiontitle).'</h4></div>';
                $inputs.='<div class="panel-body">';
                foreach($type_settings as $key => $setting) {
                    //if there is a selected field for this setting then parse it
                    if(isset($setting) && !is_empty($setting)) {
                        $inputs.=$this->parse_single_setting($type, $key, $setting);
                    }
                    else {
                        $inputs.=$this->parse_single_setting($type, $key);
                    }
                }
                $inputs.='</div></div>';
            }
        }
        if(empty($inputs)) {
            return false;
        }
        return $inputs;
    }

    /**
     * Parse overall form
     * @global type $core
     * @global type $template
     * @global type $lang
     * @param array $basicids
     * @param type $inputfields
     * @param type $existingwidget
     * @return string html form
     */
    public function parse_form(array $basicids, $existingwidget = null, $configs = '') {
        global $core, $template, $lang;
        //load dashboad lang file
        $lang->load('dashboard');
        if($core->input['module']) {
            $module = $core->input['module'];
        }
        $inputchecksum = generate_checksum();
        $widgetname = $this->get_displayname();
        //parse fields of the existing widget (if any)
        if(!is_null($existingwidget) && is_array($existingwidget)) {
            if(!empty($existingwidget[SystemWidgetInstances::PRIMARY_KEY])) {
                $widgetinstance_id = $existingwidget[SystemWidgetInstances::PRIMARY_KEY];
                $widgetinstance_obj = new SystemWidgetInstances($existingwidget[SystemWidgetInstances::PRIMARY_KEY]);
                if(is_object($widgetinstance_obj)) {
                    $widgetname = $widgetinstance_obj->get_displayname();
                }
            }
            if(!empty($existingwidget['inputChecksum'])) {
                $inputchecksum = $existingwidget['inputChecksum'];
            }
        }
        $titlefield = "<div class='form-group'><label for='widget_title'>{$lang->title}</label><input class='form-control' id='widget_title' type='text' name='widget[title]' value='{$widgetname}'></div>";
        $inputfields = $this->parse_gadget_settings($basicids, $existingwidget, $configs);
        //parse widget title input
        $widgettype_output = $this->get_displayname();
        eval("\$widgetform = \"".$template->get('popup_system_widgetform')."\";");
        return $widgetform;
    }

    /**
     * parse setting depending on type and required level
     * @global type $lang
     * @global type $core
     * @global type $template
     * @param type $level
     * @param type $type
     * @param type $required
     * @param type $existingvalue
     * @return type
     */
    public function parse_single_setting($level, $type, $existingvalue = NULL) {
        global $lang, $core, $template;
        if($level == 'required') {
            $symbol = '*';
            $required_field = 'required = "required"';
        }
        switch($type) {
            case 'currency':
                //if no currency has been set then get default currency of the user
                if(!($existingvalue) || empty($existingvalue)) {
                    $existingvalue = $core->user_obj->get_default_currencies('main');
                }
                else {
                    $existingvalue = $this->fix_settingsarray($existingvalue);
                }
                if(is_array($existingvalue)) {
                    foreach($existingvalue as $curid) {
                        $selectedcur_obj = new Currencies($curid);
                        $existingdata.=' {
                    id: '.$curid.', value:\''.$selectedcur_obj->get_displayname().' '.$selectedcur_obj->name.'\'},';
                    }
                }

                $tokenfields = 'currencies';
                $tokenidentifier = '_'.TIME_NOW;
                eval("\$prodinput = \"".$template->get('jquery_tokeninput')."\";");
                eval("\$field = \"".$template->get('system_dashboard_gadgets_currency')."\";");
                break;
            default:
                $field = "<div class='form-group'><label for='widget_{$type}'>{$lang->$type}{$symbol}</label><input {$required_field} class='form-control' id='widget_{$type}' type='text' name='widget[settings][{$level}][{$type}]' value='{$existingvalue}'></div>";
                break;
        }
        return $field;
    }

    /**
     *
     * @param type $existingvalue
     */
    protected function fix_settingsarray($existingvalue = '') {

    }

}