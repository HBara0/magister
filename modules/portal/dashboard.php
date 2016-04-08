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
//    $dashboard_obj = new SystemDashboard(1);
//    $dashboard = $dashboard_obj->get();
//    $output = SystemWidgets::parse_widgetselect_form($dashboard);
//    output_page($output);
//    exit;
    $dashboard = SystemDashboard::parse_dashboardpage();
    output_page($dashboard, array('helptourref' => 'newlayout'));
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
                if(!empty($widget_input['swgiid'])) {
                    eval("\$extrajs = \"".$template->get('dashboard_general_loadwidgetinstance_jsscript')."\";");
                }
                else {
                    $extrajs = '<script>$(function(){location.reload();});</script>';
                }
                output_xml("<status>true</status><message>{$lang->successfullysaved}<![CDATA[{$extrajs}]]></message>");
                exit;
            case 3:
                $error_output = $errorhandler->get_errors_inline();
                output_xml("<status>false</status><message>{$lang->fillrequiredfields}<![CDATA[<br/>{$error_output}]]></message>");
                exit;
        }
    }
    else if($core->input['action'] == 'populate_widgetsettings' || $core->input['action'] == 'get_editinstance') {
        if(isset($core->input['id']) && !empty($core->input['id'])) {
            $instance_inputchecksum = $core->input['id'];
            $widget_instance_obj = SystemWidgetInstances::get_data(array('inputChecksum' => $instance_inputchecksum), array('returnarray' => false));
            if(is_object($widget_instance_obj)) {
                $widget_id = $widget_instance_obj->get_widget()->{SystemWidgets::PRIMARY_KEY};
                $dash_id = intval($core->input['dashid']);
                $widgetinstance_data = $widget_instance_obj->get();
            }
        }
        else {
            $widget_id = intval($core->input['wid']);
            $dash_id = intval($core->input['dashid']);
        }
        $widget_obj = new SystemWidgets($widget_id);
        if(is_object($widget_obj) && !empty($widget_obj->className)) {
            $classname = $widget_obj->className;
        }
        if($classname && is_string($classname)) {
            $gadget_obj = new $classname();
            if(is_object($gadget_obj)) {
                $basicids = array(SystemDashboard::PRIMARY_KEY => $dash_id, SystemWidgets::PRIMARY_KEY => $widget_id);
                output($gadget_obj->parse_form($basicids, $widgetinstance_data));
                exit;
            }
        }
        output('Error');
    }
    else if($core->input['action'] == 'get_showdashboard') {
        if(isset($core->input['id']) && !empty($core->input['id'])) {
            $dash_obj = SystemDashboard::get_data(array('inputChecksum' => $core->input['id']), array('returnarray' => false));
            if(is_object($dash_obj) && !empty($dash_obj->{SystemDashboard::PRIMARY_KEY})) {
                $dashboard = $dash_obj->get();
            }
            else {
                $dashboard = array();
                $dashboard['inputChecksum'] = $core->input['id'];
                $dashboard['columnCount'] = 2;
                eval("\$dashboard = \"".$template->get('dashboard_managedashboard')."\";");
                output($dashboard);
            }
        }
        else {
            output("{$lang->error}{$error_output}");
            exit;
        }
    }
    else if($core->input['action'] == 'manage_dashboard') {
        $dahsboard = $core->input['dashboard'];
        $dashboard_obj = new SystemDashboard();
        $dashboard_obj->set($dahsboard);
        $dashboard_obj = $dashboard_obj->save();
        $error_output = $errorhandler->get_errors_inline();
        switch($dashboard_obj->get_errorcode()) {
            case 0:
                $extra_js = $dashboard_obj->generate_extrajs();
                output_xml("<status>true</status><message>{$lang->successfullysaved}<![CDATA[<br/>{$extra_js}]]></message>");
                break;
            default:
                output_xml("<status>false</status><message>{$lang->error}<![CDATA[<br/>{$error_output}]]></message>");
                exit;
        }
    }
    else if($core->input['action'] == 'parse_dashboard') {
        if(!empty($core->input['id'])) {
            $dashboard_obj = new SystemDashboard(intval($core->input['id']));
        }
        elseif(!empty($core->input['inputChecksum'])) {
            $dashboard_obj = SystemDashboard::get_data(array('inputChecksum' => $core->input['inputChecksum']), array('returnarray' => false));
        }
        if(is_object($dashboard_obj) && !empty($dashboard_obj->{SystemDashboard::PRIMARY_KEY})) {
            $output = $dashboard_obj->parse_managedashboard_page();
            output($output);
            exit;
        }

        output('Error Parsing');
    }
    else if($core->input['action'] == 'parse_widgetinstance') {
        if(!empty($core->input['id'])) {
            $instance_obj = new SystemWidgetInstances(intval($core->input['id']));
        }
        elseif(!empty($core->input['inputChecksum'])) {
            $instance_obj = SystemWidgetInstances::get_data(array('inputChecksum' => $core->input['inputChecksum']), array('returnarray' => false));
        }
        if(is_object($instance_obj) && !empty($instance_obj->{SystemWidgetInstances::PRIMARY_KEY})) {
            $output = $instance_obj->parse_widgetinstance(intval($core->input['dashid']));
            output($output);
            exit;
        }

        output('Error Parsing');
    }
}
