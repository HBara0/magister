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

$file_types = array(
        'jpg' => 'jpg',
        'png' => 'png',
);
if(!$core->input['action']) {
    $window_obj = new SystemWindows($core->input['wid']);
    if(isset($core->input['wid']) && !empty($core->input['wid'])) {
        if(is_object($window_obj)) {
            $window = $window_obj->get();
            if($window != null) {
                $swid = $windowid = $window['swid'];
                if($window['isActive'] == 1) {
                    $window_isactive_check = 'checked="checked"';
                }
                $window_type_list = parse_selectlist("window[type]", 0, array('interactive' => 'Interactive', 'readonly' => 'Read-Only'), $window['type']);
                $sections_objs = SystemWindowsSection::get_data(array('swid' => $window['swid']), array('simple' => false, 'returnarray' => true));
                $fieldrow_id = 1;
                if(is_array($sections_objs)) {
                    foreach($sections_objs as $sections_obj) {
                        $section = $sections_obj->get();
                        $swsid = $sections_obj->swsid;
                        $tabnum = $swstid = $section['swstid'];
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
                        $fields_objs = SystemWindowsSectionFields::get_data(array('swsid' => $sections_obj->swsid), array('returnarray' => true));
                        if(is_array($fields_objs)) {
                            foreach($fields_objs as $fields_obj) {
                                $field = $fields_obj->get();
                                if($field['isDisplayed'] == 1) {
                                    $field_isdisplayed_check = 'checked="checked"';
                                }
                                if($field['isReadOnly'] == 1) {
                                    $field_isreadonly_check = 'checked="checked"';
                                }
                                $field_fieldtype_list = parse_selectlist("field[".$swsid."][".$field['inputChecksum']."][fieldType]", 0, $fieldtypes, $field['fieldType']);
                                $referencelists_objs = SystemReferenceLists::get_data('', array('returnarray' => true));
                                if(is_array($referencelists_objs)) {
                                    $field_fieldtypelist_list = parse_selectlist("field[".$swsid."][".$field['inputChecksum']."][srlid]", 0, $referencelists_objs, $section['srliid']);
                                }
                                $showfieldtype_reflists[$field['inputChecksum']] = 'display:none;';
                                if($field['fieldType'] == 'list') {
                                    $showfieldtype_reflists[$field['inputChecksum']] = '';
                                }
                                $field_allowedfiletypes_list = parse_selectlist("field[".$swsid."][".$field['inputChecksum']."][allowedFileTypes]", 0, $file_types, $section['allowedFileTypes'], '', '', array('blankstart' => true));
                                eval("\$section_fields .= \"".$template->get('admin_system_windows_section_fieldrow')."\";");
                                $fieldrow_id++;
                                unset($field_isdisplayed_check, $field_isreadonly_check, $field_fieldtypelist_list);
                            }
                        }
                        eval("\$section_table_fields = \"".$template->get('admin_system_windows_section_tablefield')."\";");
                        $section_content.= '<div id="sectionstabs-'.$tabnum.'">';
                        eval("\$section_content .= \"".$template->get('admin_system_windows_sectionstabs')."\";");
                        $section_content.='</div>';
                        $delete_tabicon = '<span class = "ui-icon ui-icon-close" id = "deleteseg_'.$tabnum.'"role = "presentation" title = "Close">Remove Tab</span>';
                        $sectionstabs.='<li><a href = "#sectionstabs-'.$tabnum.'">'.$sections_obj->get_displayname().'</a>'.$delete_tabicon.'</li> ';
                        unset($section_fields, $section_tables_selectlist, $section_ismain_check, $section_isactive_check);
                    }
                }
                eval("\$sections = \"".$template->get('admin_system_windows_sections')."\";");
            }
            else {
                redirect('');
            }
        }
    }
    else {
        $window_type_list = parse_selectlist("window[type]", 0, array('interactive  ' => 'Interactive', 'readonly' => 'Read-Only'), $window['type']);
        $disable_tablink = ' style = "pointer-events: none;cursor: default;"';
        eval("\$sections = \"".$template->get('admin_system_windows_sections')."\";");
//             $fieldrow_id = 1;
//        $section['inputChecksum'] = generate_checksum('window_section');
//        $sectiontabs = '<li><a href = "#sectionstabs-1">Section 1</a></li>';
//        eval("\$section_fields = \"".$template->get('admin_system_windows_section_fieldrow')."\";");
//        eval("\$section_table_fields = \"".$template->get('admin_system_windows_section_tablefield')."\";");
        //        eval("\$section_content = \"".$template->get('admin_system_windows_sectionstabs')."\";");
    }
    eval("\$manage_windows = \"".$template->get('admin_system_windows')."\";");
    output_page($manage_windows);
}
else {
    if($core->input ['action'] == 'ajaxaddmore_fields') {
        $field['inputChecksum'] = generate_checksum('section_field');
        $fieldrow_id = $core->input['value'] + 1;
        $swsid = $field['swsid'] = $db->escape_string($core->input['ajaxaddmoredata']['swsid']);
        $section_obj = new SystemWindowsSection($swsid);
        $section = $section_obj->get();
        $referencelists_objs = SystemReferenceLists::get_data('', array('returnarray' => true));
        if(is_array($referencelists_objs)) {
            $field_fieldtypelist_list = parse_selectlist("field[".$swsid."][".$field['inputChecksum']."][srlid]", 0, $referencelists_objs, $section['srliid']);
        }
        $showfieldtype_reflists[$field['inputChecksum']] = 'display:none;';
        if($field['fieldType'] == 'list') {
            $showfieldtype_reflists[$field['inputChecksum']] = '';
        }
        $field_allowedfiletypes_list = parse_selectlist("field[".$swsid."][".$field['inputChecksum']."][allowedFileTypes]", 0, $file_types, '', '', '', array('blankstart' => true));

        $field['swstid'] = $tabnum = $db->escape_string($core->input['ajaxaddmoredata']['swstid']);
        if(empty($swsid)) {
            exit;
        }
        $field_fieldtype_list = parse_selectlist("field[".$swsid."][".$field['inputChecksum']."][fieldType]", 0, $fieldtypes, '');
        eval("\$section_fields = \"".$template->get('admin_system_windows_section_fieldrow')."\";");
        echo ($section_fields);
    }
    elseif($core->input['action'] == 'add_section') {
        $tabnum = $swstid = $db->escape_string($core->input['sequence']);
        $swsid = 0;
        $windowid = $db->escape_string($core->input['windowid']);
        $section ['inputChecksum'] = generate_checksum('section');
        $disable_fieldsave = 'disabled = "disabled"';
        $tables_objs = SystemTables::get_data('', array('returnarray' => true));
        if(is_array($tables_objs)) {
            $section_tables_selectlist = parse_selectlist("section[".$section['inputChecksum']."][dbTable]", 1, $tables_objs, '', '', '', array('blankstart' => true));
        } $disable_morerows = 'style = "display:none"';
        $section_type_selectlist = parse_selectlist("section[".$section['inputChecksum']."][type]", 0, array('form' => 'Form', 'list' => 'List', 'record' => 'Record'), '');
        $section_displaytype_selectlist = parse_selectlist("section[".$section['inputChecksum']."][displayType]", 2, array(1 => 'Tab', 2 => 'Inline-Section'), '');
        eval("\$section_table_fields = \"".$template->get('admin_system_windows_section_tablefield')."\";");
        eval("\$section_content = \"".$template->get('admin_system_windows_sectionstabs')."\";");
        output($section_content);
    }
    elseif($core->input['action'] == 'save_windows_managewindows') {
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
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'<![CDATA[<script>$("a[id=\'createtab\']").removeAttr("style")   ;
                            $("input[id=\'window_id\']").val('.$swid.'); </script>]]></message>');
                break;
            case 1:
                output_xml("<status>false</status><message>".$lang->errorsaving."</message>");
                exit;
            case 2:
                output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</ message>');
                break;
        }
    }
    elseif($core->input ['action'] == 'save_sections_managewindows') {
        foreach($core->input ['section'] as $inputchecksum => $section) {
            if(!isset($section['inputChecksum']) || empty($section['inputChecksum']) || empty($section['dbTable']) || empty($section['name'])) {
                output_xml("<status>false</status><message>".$lang->errorsaving."</message>");
                return;
            }
            $window_section = new SystemWindowsSection();
            $window_section->set($section);
            $window_section->save();
            switch($window_section->get_errorcode()) {
                case 0:
                    output_xml('<status>true</status><message>'.$lang->successfullysaved.'<![CDATA[<script>$("input[name=\'ajaxaddmoredata[swsid]\']").val('.$window_section->swsid.');$("input[id^=\'fields_\']").prop("disabled", false);$("div[id=\'addmore_sectionfields_div\']").show();</script>]]></message>');
                    break;
                case 1:
                    output_xml("<status>false</status><message>".$lang->errorsaving."</message>");
                    exit;
                case 2:
                    output_xml('<status>false</st atus><messa ge>'.$lang->fillrequiredfields.'</message>');
                    break;
            }
        }
    }
    elseif($core->input['action'] == 'save_fields_managewindows') {
        if(is_array($core->input['field'])) {
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
        }
        output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
    }
    elseif($core->input['action'] == 'deletesection') {
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