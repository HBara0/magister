<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: managetables.php
 * Created:        @hussein.barakat    Apr 21, 2015 | 3:24:43 PM
 * Last Update:    @hussein.barakat    Apr 21, 2015 | 3:24:43 PM
 */

if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}
if($core->usergroup['admin_canManageSystemDef'] == 0) {
    error($lang->sectionnopermission);
    exit;
}
$lang = new Language('english', 'admin');
$lang->load('tables_meta');
$lang->load('global');

if($core->usergroup['admin_canManageSystemDef'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    if(!empty($core->input['d$@1á'])) {
        $columns[] = '';
        $table_data = unserialize(base64_decode($core->input['d$@1á']));
        if(is_array($table_data)) {
            $page_title = ''.$table_data['tableName'].'-'.$table_data['className'];
            $tablecols_objs = SystemTablesColumns::get_data(array('stid' => $table_data['stid']), array('returnarray' => true));
            if(!empty($tablecols_objs)) {
                foreach($tablecols_objs as $tablecols_obj) {
                    $column_data = $tablecols_obj->get();
                    if($column_data['isPrimaryKey'] == '1') {
                        $primary_check = 'checked="checked"';
                    }
                    if($column_data['isRequired'] == '1') {
                        $required_check = 'checked="checked"';
                    }
                    if($column_data['isUnique'] == '1') {
                        $unique_check = 'checked="checked"';
                    }
                    if($column_data['isSimple'] == '1') {
                        $simple_check = 'checked="checked"';
                    }
                    if($column_data['isDisplayName'] == '1') {
                        $displayname_check = 'checked="checked"';
                    }
                    $columns[] = $column_data['columnDbName'];
                    $type_selectlist = parse_selectlist('column_data['.$column_data['columnDbName'].'][dataType]', '', array('int' => 'INT', 'varchar' => 'VARCHAR', 'text' => 'TEXT', 'date' => 'DATE'), $column_data['dataType']);
//                        if($column_data['stcid']) {
//                            $filters['stcid'] = $column_data['stcid'];
//                            $filters['stid'] = $column_data['stid'];
//                        }
                    $filters = 'stcid != '.$column_data['stcid'].' AND isPrimaryKey = 1 AND stid !='.$column_data['stid'].'';
//                        $filters['isPrimaryKey'] = 1;
                    $references = SystemTablesColumns::get_data($filters, array('returnarray' => true));
                    $reference_selectlist = parse_selectlist('column_data['.$column_data['columnDbName'].'][relatedTo]', 6, $references, $column_data['relatedTo'], '', '', array('blankstart' => true));
                    eval("\$table_details .= \"".$template->get('admin_tables_managetables_rows')."\";");
                    unset($column_data, $filters, $type, $primary_check, $required_check, $unique_check, $simple_check, $displayname_check);
                }//end of foreach
            }
            if($core->input['type'] == 'showtabledata') {
                $result = $db->query('SHOW TABLES LIKE "'.$table_data['tableName'].'"');            //checking if table exists in the database
                if($result->num_rows > 0) {
                    $table_fields = $db->show_fields_from($table_data['tableName'], MYSQLI_ASSOC);

                    if(is_array($table_fields)) {
                        foreach($table_fields as $table_field) {
                            if(in_array($table_field['Field'], $columns)) {
                                continue;
                            }
                            unset($column_data);
                            //Seperating data type and length-START
                            preg_match('#\((.*?)\)#', $table_field['Type'], $match);
                            $column_data['length'] = $match[1];
                            $types = explode('(', $table_field['Type']);
                            switch($types[0]) {
                                case('int'):
                                case('smallint'):
                                case('tinyint'):
                                case('bigint'):
                                case('mediumint'):
                                    $type = 'int';
                                    break;
                                case('varchar'):
                                    $type = 'varchar';
                                    break;
                                case('date'):
                                    $type = 'date';
                                    break;
                                default:
                                    break;
                            }
                            //Seperating data type and length-END
                            //start of switch case
                            switch($table_field['Key']) {
                                case('PRI'):
                                    $primary_check = 'checked="checked"';
                                    $required_check = 'checked="checked"';
                                    $unique_check = 'checked="checked"';
                                    $simple_check = 'checked="checked"';
                                    break;
                                case('UNI'):
                                    $unique_check = 'checked="checked"';
                                    break;
                            }
                            //end of switch case
                            $column_data['columnDbName'] = $table_field['Field'];
                            $column_data['columnSystemName'] = $column_data['columnTitle'] = $column_data['columnDbName'];
                            $type_selectlist = parse_selectlist('column_data['.$column_data['columnDbName'].'][dataType]', '', array('int' => 'INT', 'varchar' => 'VARCHAR', 'text' => 'TEXT', 'date' => 'DATE'), $type, '', '', array('blankstart' => true));
                            $references = SystemTablesColumns::get_data(array('isPrimaryKey' => 1), array('returnarray' => true));
                            $reference_selectlist = parse_selectlist('column_data['.$column_data['columnDbName'].'][relatedTo]', 6, $references, null, '', '', array('blankstart' => true));
                            eval("\$table_details .= \"".$template->get('admin_tables_managetables_rows')."\";");
                            unset($column_data, $type, $primary_check, $required_check, $unique_check, $simple_check);
                        }
                    }
                }
            }
            eval("\$table_main = \"".$template->get('admin_tables_managetables_table')."\";");
        }
        eval("\$tabledata = \"".$template->get('admin_tables_managetables')."\";");
        output_page($tabledata);
    }
}
else {
    if($core->input['action'] == 'do_perform_managetables') {
        if(empty($core->input['column_data']) || !is_array($core->input['column_data'])) {
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
            exit;
        }
        $col_count = 0;
        foreach($core->input['column_data'] as $key => $column) {
            if($key == $core->input['displayName']) {
                $column['isDisplayName'] = 1;
            }
            else {
                $column['isDisplayName'] = 0;
            }
            if(!isset($column['isPrimaryKey']) || is_empty($column['isPrimaryKey'])) {
                $column['isPrimaryKey'] = 0;
            }
            if(!isset($column['relatedTo']) || empty($column['relatedTo'])) {
                $column['relatedTo'] = 0;
            }
            $column['stid'] = $core->input['stid'];
            $column_objs[$key] = new SystemTablesColumns();
            $column_objs[$key]->set($column);
            $column_objs[$key]->save();
        }
        foreach($column_objs as $column_obj) {
            if($column_obj->get_errorcode() != 0) {
                output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                exit;
            }
            $col_count++;
        }
        $table_obj = new SystemTables($core->input['stid']);
        $table_obj->save();
        output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
    }
    elseif($core->input['action'] == 'createclass') {
        $createclass = false;
        if(!empty($core->input['stid'])) {
            $table_obj = new SystemTables($core->input['stid']);
            if(is_object($table_obj)) {
                if(!is_null($table_obj->className)) {
                    $createclass = $table_obj->create_class(intval($core->input['classdef']), intval($core->input['classfunc']), intval($core->input['overwrite']));
                }
            }
        }
        if($createclass == false) {
//            echo('ERROR');
        }
    }
}