<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: dashboard.php
 * Created:        @hussein.barakat    10-Mar-2016 | 15:49:02
 * Last Update:    @hussein.barakat    10-Mar-2016 | 15:49:02
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    $dash_obj = new SystemDashboard(1);
    $dashboard = $dash_obj->get();
    $widgets = SystemWidgets::parse_widgetselect_form($dashboard);
    $widgets = $dash_obj->parse_dashboard();

    output_page($widgets);
}
else {
    if($core->input['action'] == 'managewidgets') {
        $widget_input = $core->input['widget'];
        if(!$widget_input) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}<![CDATA[<br/>{$error_output}]]></message>");
            exit;
        }
        $widget_instance = new SystemWidgetInstances();
        $widget_instance = $widget_instance->save_instance($widget_input);
        switch($widget_instance->get_errorcode()) {
            case 0:
                output_xml("<status>true</status><message>{$lang->successfullysaved}<![CDATA[{$extrajs}]]></message>");
                exit;
            case 3:
                $error_output = $errorhandler->get_errors_inline();
                output_xml("<status>false</status><message>{$lang->fillrequiredfields}<![CDATA[<br/>{$error_output}]]></message>");
                exit;
        }
    }
    else if($core->input['action'] == 'populate_widgetsettings') {
        $widget_id = intval($core->input['wid']);
        $dash_id = intval($core->input['dashid']);
        $widget_obj = new SystemWidgets($widget_id);
        if(is_object($widget_obj) && !empty($widget_obj->className)) {
            $classname = $widget_obj->className;
        }
        $gadget_obj = new $classname();
        if(is_object($gadget_obj)) {
            $basicids = array(SystemDashboard::PRIMARY_KEY => $dash_id, SystemWidgets::PRIMARY_KEY => $widget_id);
            echo($gadget_obj->parse_form($basicids));
        }
    }
}
