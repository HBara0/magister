<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: managewindows.php
 * Created:        @hussein.barakat    Apr 27, 2015 | 4:11:32 PM
 * Last Update:    @hussein.barakat    Apr 27, 2015 | 4:11:32 PM
 */
if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}
if($core->usergroup['admin_canManageSystemDef'] == 0) {
    error($lang->sectionnopermission);
    exit;
}
$lang = new Language('english', 'admin');
$lang->load('managewindows');
$lang->load('global');
$fieldtypes = array(
        'int' => 'Integer',
        'decimal' => 'Decimal',
        'autocomplete' => 'Search/Autocomplete',
        'textfield' => 'Text Field',
        'datepicker' => 'Date Picker',
        'datepicker_from' => 'Date Picker From',
        'datepicker_to' => 'Date Picker To',
        'richtextarea' => 'Rich Text Area',
        'textarea' => 'Text Area',
        'color' => 'Color',
        'password' => 'Password',
        'uploadsinglefile' => 'Upload Single File',
        'uploadmultiplefile' => 'Upload Multiple files',
        'yesno' => 'Yes/No',
        'list' => 'Reference List',
);
if(!$core->input['action']) {
    $window_obj = new SystemWindows($core->input['wid']);
    if(isset($core->input['wid']) && !empty($core->input['wid'])) {
        if(is_object($window_obj)) {
            $window = $window_obj->get();
            $swid = $window['swid'];
            $window_type_list = parse_selectlist("window[type]", 0, array('interactive' => 'Interactive', 'readonly' => 'Read-Only'), $window['type']);
            $sections_objs = SystemWindowsSection::get_data(array('swid' => $window['swid']), array('simple' => false, 'returnarray' => true));
            if(is_array($sections_objs)) {
                $fieldrow_id = 1;
                foreach($sections_objs as $sections_obj) {
                    $section = $sections_obj->get();
                    $swsid = $sections_obj->swsid;
                    $tabnum = $section['swstid'];
                    if($section['isMain'] == 1) {
                        $section_ismain_check = 'checked="checked"';
                    }
                    if($section['isActive'] == 1) {
                        $section_isactive_check = 'checked="checked"';
                    }
                    $section_displaytype_selectlist = parse_selectlist("section[".$section['inputChecksum']."][displayType]", 2, array(1 => 'Tab', 2 => 'Inline-Section'), $section['displayType'], '', array('blankstart' => true));
                    $tables_objs = SystemTables::get_data('', array('returnarray' => true));
                    if(is_array($tables_objs)) {
                        $section_tables_selectlist = parse_selectlist("section[".$section['inputChecksum']."][dbTable]", 1, $tables_objs, $section['dbTable'], '', '', array('blankstart' => true));
                    }
                    $section_type_selectlist = parse_selectlist("section[".$section['inputChecksum']."][type]", 0, array('form' => 'Form', 'list' => 'List', 'record' => 'Record'), $section['type']);
                    $fields_objs = SystemWindowsSectionFields::get_data('', array('returnarray' => true));
                    if(is_array($fields_objs)) {
                        foreach($fields_objs as $fields_obj) {
                            $field = $fields_obj->get();
                            $field_fieldtype_list = parse_selectlist("field[".$swsid."][".$field['inputChecksum']."][fieldType]", 0, $fieldtypes, $section['type']);
                            eval("\$section_fields .= \"".$template->get('admin_system_windows_section_fieldrow')."\";");
                            $fieldrow_id++;
                        }
                        eval("\$section_table_fields = \"".$template->get('admin_system_windows_section_tablefield')."\";");
                    }
                    eval("\$section_content .= \"".$template->get('admin_system_windows_sectionstabs')."\";");
                }
            }
        }
    }
    else {
        $window_type_list = parse_selectlist("window[type]", 0, array('interactive' => 'Interactive', 'readonly' => 'Read-Only'), $window['type']);
        $disable_tablink = ' style="pointer-events: none;cursor: default;"';
//             $fieldrow_id = 1;
//        $section['inputChecksum'] = generate_checksum('window_section');
//        $sectiontabs = '<li><a href="#sectionstabs-1">Section 1</a></li>';
//        eval("\$section_fields = \"".$template->get('admin_system_windows_section_fieldrow')."\";");
//        eval("\$section_table_fields = \"".$template->get('admin_system_windows_section_tablefield')."\";");
//        eval("\$section_content = \"".$template->get('admin_system_windows_sectionstabs')."\";");
    }
    eval("\$sections = \"".$template->get('admin_system_windows_sections')."\";");
    eval("\$manage_windows = \"".$template->get('admin_system_windows')."\";");
    output_page($manage_windows);
}
else {
    if($core->input['action'] == 'ajaxaddmore_fields') {
        $field['inputChecksum'] = generate_checksum('section_field');
        $fieldrow_id = $core->input['value'] + 1;
        $swsid = $field['swsid'] = $db->escape_string($core->input['ajaxaddmoredata']['swsid']);
        $field['swstid'] = $db->escape_string($core->input['ajaxaddmoredata']['swstid']);
        if(empty($swsid)) {
            exit;
        }
        $field_fieldtype_list = parse_selectlist("field[".$swsid."][".$field['inputChecksum']."][fieldType]", 0, $fieldtypes, '');
        eval("\$section_fields = \"".$template->get('admin_system_windows_section_fieldrow')."\";");
        echo ($section_fields);
    }
    elseif($core->input['action'] == 'add_section') {
        $tabnum = $db->escape_string($core->input['sequence']);
        $swsid = 0;
        $windowid = $db->escape_string($core->input['windowid']);
        $section['inputChecksum'] = generate_checksum('section');
        $disabled_fields = 'disabled="disabled"';
        $tables_objs = SystemTables::get_data('', array('returnarray' => true));
        if(is_array($tables_objs)) {
            $section_tables_selectlist = parse_selectlist("section[".$section['inputChecksum']."][dbTable]", 1, $tables_objs, '', '', '', array('blankstart' => true));
        }
        $disable_morerows = 'disabled="disabled"';
        $section_type_selectlist = parse_selectlist("section[".$section['inputChecksum']."][type]", 0, array('form' => 'Form', 'list' => 'List', 'record' => 'Record'), '');
        $section_displaytype_selectlist = parse_selectlist("section[".$section['inputChecksum']."][displayType]", 2, array(1 => 'Tab', 2 => 'Inline-Section'), '');
        eval("\$section_table_fields = \"".$template->get('admin_system_windows_section_tablefield')."\";");
        eval("\$section_content = \"".$template->get('admin_system_windows_sectionstabs')."\";");
        output($section_content);
    }
    if($core->input['action'] == 'save_windows_managewindows') {
        $window_object = new SystemWindows();
        if(!is_array($core->input['window']) || empty($core->input['window']['title']) || empty($core->input['window']['name'])) {
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
            return;
        }
        $window_object->set($core->input['window']);
        $window_object->save();
        switch($window_object->get_errorcode()) {
            case 0:
                $swid = json_encode($window_object->swid);
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'<![CDATA[<script>$("a[id=\'createtab\']").removeAttr("style");$("input[id=\'window_id\']").val('.$swid.');</script>]]></message>');
                break;
            case 1:
                output_xml("<status>false</status><message>".$lang->errorsaving."</message>");
                exit;
            case 2:
                output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
                break;
        }
    }
    if($core->input['action'] == 'save_sections_managewindows') {
        foreach($core->input['section'] as $inputchecksum => $section) {
            if(!isset($section['inputChecksum']) || empty($section['inputChecksum'])) {
                output_xml("<status>false</status><message>".$lang->errorsaving."</message>");
                return;
            }
            $window_section = new SystemWindowsSection();
            $window_section->set($section);
            $window_section->save();
            switch($window_section->get_errorcode()) {
                case 0:
                    output_xml('<status>true</status><message>'.$lang->successfullysaved.'<![CDATA[<script>$("input[name=\'ajaxaddmoredata[swsid]\']").val('.$window_section->swsid.');$("input[id^=\'ajaxaddmore_managesystem/managewindows_fields\']").removeAttr("style");$("a[id=\'createtab\']").removeAttr("style")</script>]]></message>');
                    break;
                case 1:
                    output_xml("<status>false</status><message>".$lang->errorsaving."</message>");
                    exit;
                case 2:
                    output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
                    break;
            }
        }
    }
    if($core->input['action'] == 'save_fields_managewindows') {
        foreach($core->input['field'] as $swsid => $fields) {
            foreach($fields as $inputchecksum => $field) {
                if(!isset($field['inputChecksum']) || empty($field['inputChecksum'])) {
                    output_xml("<status>false</status><message>".$lang->errorsaving."</message>");
                    return;
                }
                $window_field = new SystemWindowsSectionFields();
                $window_field->set($field);
                $window_field->save();
                switch($window_field->get_errorcode()) {
                    case 1:
                        output_xml("<status>false</status><message>".$lang->errorsaving."</message>");
                        exit;
                    case 2:
                        output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
                        exit;
                }
            }
        }
        output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
    }
    if($core->input['action'] == 'deletesection') {
        $sectionid = $db->escape_string($core->input['sectionid']);
        if(!empty($sectionid)) {
            $plan_classes = array('SystemWindowsSection', 'SystemWindowsSectionFields');
            if(is_array($plan_classes)) {
                foreach($plan_classes as $object) {
                    $data = $object::get_data('swstid = '.$sectionid.'', array('returnarray' => true));
                    if(is_array($data)) {
                        foreach($data as $object_todelete) {
                            $object_todelete->delete();
                        }
                    }
                }
            }
        }
    }
}