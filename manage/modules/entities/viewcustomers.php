<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 *
 * View customers
 * $module: admin/entities
 * $id: viewcustomers.php
 * Last Update: @zaher.reda 	Apr 07, 2009 | 11:27 PM
 */
if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canManageSuppliers'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $sort_query = 's.companyName ASC';
    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query = $core->input['sortby']." ".$core->input['order'];
    }
    $sort_url = sort_url();

    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('id', 'customers', 'affiliate', 'coid'),
                    'overwriteField' => array(
                            'id' => '',
                    )
            ),
            'process' => array(
                    'filterKey' => 'eid',
                    'mainTable' => array(
                            'name' => 'entities',
                            'filters' => array('customers' => array('name' => 'companyName'), 'coid' => array('operatorType' => 'multiple', 'name' => 'country')),
                    ),
                    'secTables' => array(
                            'affiliatedentities' => array(
                                    'filters' => array('affiliate' => array('operatorType' => 'multiple', 'name' => 'affid'))
                            ),
                    )
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    $filter_where = null;
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));

    /* Perform inline filtering - END */

    $limit_start = 0;
    if(isset($core->input['start'])) {
        $limit_start = $db->escape_string($core->input['start']);
    }

    $query = $db->query("SELECT s.companyName AS entityname, s.*, c.name as country
						FROM ".Tprefix."entities s, ".Tprefix."countries c
						WHERE s.country=c.coid AND s.type='c'".$filter_where."
						ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");

    if($db->num_rows($query) > 0) {
        while($customer = $db->fetch_array($query)) {
            $class = alt_row($class);

            $query2 = $db->query("SELECT ae.*, a.name FROM ".Tprefix."affiliatedentities ae LEFT JOIN ".Tprefix."affiliates a ON (a.affid=ae.affid) WHERE ae.eid='$customer[eid]' ORDER BY a.name ASC");
            $comma = $affiliates = $mergedelete_icon = '';
            while($affiliate = $db->fetch_array($query2)) {
                $affiliates .= $comma.$affiliate['name'];
                $comma = ', ';
            }

            //if($core->usergroup['canDeleteCutomers'] == 1) {
            $mergedelete_icon = '<a href="#" id="mergeanddeleteentities_'.$customer['eid'].'_entities/viewcustomers_loadpopupbyid"><img src="'.$core->settings['rootdir'].'/images/invalid.gif" border="0" /></a>';
            //}

            $entities_list .= '<tr class="'.$class.'"><td>'.$customer['eid'].'</td><td>'.$customer['companyName'].'</td><td>'.$affiliates.'</td><td>'.$customer['country'].'</td>';
            $entities_list .= '<td><a href="index.php?module=entities/edit&amp;eid='.$customer['eid'].'"><img src="'.$core->settings['rootdir'].'/images/edit.gif" alt="'.$lang->edit.'" border="0" /></a></td><td>'.$mergedelete_icon.'</td></tr>';
            //  $entities_list .= "<td style='text-align: right;'>{$approve_icon}<a href='index.php?module=entities/edit&amp;eid={$supplier[eid]}'><img src='{$core->settings[rootdir]}/images/edit.gif' alt='{$lang->edit}' border='0' /></a>";
            //  $entities_list .= "<a href='#' id='mergeanddeleteentities_{$supplier[eid]}_entities/viewsuppliers_loadpopupbyid'><img src='{$core->settings[rootdir]}/images/invalid.gif' border='0' /></td></tr>";
        }
        $multipages = new Multipages("entities", $core->settings['itemsperlist'], "type='c'");
        $entities_list .= "<tr><td colspan='4'>".$multipages->parse_multipages()."</td><td style='text-align: right;'><a href='".$_SERVER['REQUEST_URI']."&amp;action=exportexcel'><img src='../images/xls.gif' alt='{$lang->exportexcel}' border='0' /></a></td></tr>";
    }
    else {
        $entities_list = "<tr><td colspan='5' style='text-align: center;'>{$lang->nocustomersavailable}</td></tr>";
    }

    $lang->listavailableentities = $lang->listavailablecustomers;

    eval("\$customerspage = \"".$template->get("admin_entities_view")."\";");
    output_page($customerspage);
}
else {
    /*    if($core->input['action'] == "get_mergeanddelete") {
      //        $entitytype = 'customer';
      //        $filename = 'viewcustomers';
      //        eval("\$mergeanddeletebox = \"".$template->get("popup_entities_mergeanddelete")."\";");
      //        echo $mergeanddeletebox;
      //    }
      //    elseif($core->input['action'] == "perform_mergeanddelete") {
      //        if(empty($core->input['todelete'])) {
      //            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
      //            exit;
      //        }
      //        $error_handler = new ErrorHandler(true);
      //        $oldid = $db->escape_string($core->input['todelete']);
      //
      //        //if($core->usergroup['entities_canDeleteCustomers'] == 1) {
      //        $fields_array = array(
      //                'affiliatedentities' => array('keyattr' => 'eid', 'checkattr' => 'affid'),
      //                'entitiessegments' => array('keyattr' => 'eid', 'checkattr' => 'psid'),
      //                'entitiesrepresentatives' => array('keyattr' => 'eid', 'checkattr' => 'rpid'),
      //                'visitreports' => array('keyattr' => 'cid'),
      //                'assignedemployees' => array('keyattr' => 'eid', 'checkattr' => 'uid'),
      //                'keycustomers' => array('keyattr' => 'cid'),
      //                'stockorder_customers' => array('keyattr' => 'cid', 'primaryattr' => 'socid', 'relatedtables' => array('stockorder_customers_products' => array('keyattr' => 'socid')))
      //        );
      //
      //        if(!empty($core->input['mergeeid'])) {
      //            $newid = $db->escape_string($core->input['mergeeid']);
      //            if($newid == $oldid) {
      //                output_xml("<status>false</status><message>{$lang->mergesamedelete}</message>");
      //                exit;
      //            }
      //            $old_entity_details = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."entities WHERE eid={$oldid}"));
      //            $new_entity_details = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."entities WHERE eid={$newid}"));
      //
      //            foreach($new_entity_details as $key => $val) {
      //                if(empty($new_entity_details[$key]) && !empty($old_entity_details[$key])) {
      //                    $update_details[$key] = $old_entity_details[$key];
      //                }
      //            }
      //
      //            if(is_array($update_details)) {
      //                $update_details_query = $db->update_query('entities', $update_details, "eid='{$newid}'");
      //                if(!$query) {
      //                    $error_handler->record('entities', $newid, 'failedinupdating');
      //                }
      //                else {
      //                    if($db->affected_rows() == 0) {
      //                        $error_handler->record('entities', $newid, 'notupdated');
      //                    }
      //                }
      //            }
      //
      //            foreach($fields_array as $table => $field) {
      //                if(isset($field['checkattr']) && !empty($field['checkattr'])) {
      //                    $old_data = $db->fetch_assoc($db->query("SELECT {$field[checkattr]} FROM ".Tprefix."{$table} WHERE {$field[keyattr]}={$oldid}"));
      //                    $new_data = $db->fetch_assoc($db->query("SELECT {$field[checkattr]} FROM ".Tprefix."{$table} WHERE {$field[keyattr]}={$newid}"));
      //                    if(is_array($old_data) && is_array($new_data)) {
      //                        $difference = array_diff($old_data, $new_data);
      //                        $intersect = array_intersect($old_data, $new_data);
      //                    }
      //                    if(is_array($difference) && !empty($difference)) {
      //                        $update_query = $db->update_query($table, array($field['keyattr'] => $newid), "{$field[keyattr]}={$oldid} AND {$field[checkattr]} IN (".implode(', ', $difference).")");
      //                        if(!$update_query) {
      //                            $error_handler->record($table, $oldid, 'failedinupdating');
      //                        }
      //                        else {
      //                            if($db->affected_rows() == 0) {
      //                                $error_handler->record($oldid, $table, 'notupdated');
      //                            }
      //                        }
      //                    }
      //
      //                    if(is_array($intersect) && !empty($intersect)) {
      //                        $delete_query = $db->delete_query($table, "{$field[keyattr]}='{$oldid}' AND {$field[checkattr]} IN (".implode(', ', $intersect).")");
      //                        if(!$delete_query) {
      //                            $error_handler->record($table, $oldid, 'errordeleting');
      //                        }
      //                    }
      //                }
      //                else {
      //                    $query = $db->update_query($table, array($field['keyattr'] => $newid), "{$field[keyattr]}={$oldid}");
      //                    if(!$query) {
      //                        $error_handler->record($table, $oldid, 'failedinupdating');
      //                    }
      //                    else {
      //                        if($db->affected_rows() == 0) {
      //                            $error_handler->record($oldid, $table, 'notupdated');
      //                        }
      //                    }
      //                }
      //            }
      //        }
      //        else {
      //            foreach($fields_array as $table => $field) {
      //                if(isset($field['primaryattr'])) {
      //                    $old_data = $db->fetch_assoc($db->query("SELECT {$field[primaryattr]} FROM ".Tprefix."{$table} WHERE {$field[keyattr]}={$oldid}"));
      //                }
      //
      //                $query = $db->delete_query($table, "{$field[keyattr]}={$oldid}");
      //                if(!$query) {
      //                    $error_handler->record('entities', $oldid, 'errorindeleting');
      //                }
      //                else {
      //                    if(isset($field['relatedtables']) && is_array($field['relatedtables'])) {
      //                        foreach($field['relatedtables'] as $table => $attributes) {
      //                            $deleted_related = $db->delete_query($table, "{$attributes[keyattr]}='{$old_data[$field[primaryattr]]}'");
      //                            if(!$deleted_related) {
      //                                $error_handler->record($old_data['primaryattr'], $table, 'errorindeletingrelated');
      //                            }
      //                        }
      //                    }
      //                }
      //            }
      //        }
      //
      //        $query = $db->delete_query('entities', "eid='{$oldid}'");
      //
      //        $errors = $error_handler->get_errors_inline();
      //
      //        if($query) {
      //            $log->record($oldid, $newid);
      //            output_xml("<status>true</status><message>{$lang->successdeletemerge}<![CDATA[<br /><span class='red_text'>{$errors}</span>]]></message>");
      //        }
      //        else {
      //            //$error[$lang->errorindeleting][$oldid] = $lang->entities;
      //            output_xml("<status>false</status><message>{$lang->errordeleting}</message>");
      //        }
      //        /* }
      //          else
      //          {
      //          output_xml("<status>false</status><message>{$lang->sectionnopermission}</message>");
      //          }
      //    } */
    if($core->input['action'] == "exportexcel") {
        $sort_query = "s.companyName ASC";
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = $core->input['sortby']." ".$core->input['order'];
        }
        $query = $db->query("SELECT s.eid, s.companyName AS entityname, c.name as cname
						FROM ".Tprefix."entities s, ".Tprefix."countries c
						WHERE s.country=c.coid AND s.type='c'
						ORDER BY {$sort_query}");
        if($db->num_rows($query) > 0) {
            $customers[0]['eid'] = $lang->id;
            $customers[0]['entityname'] = $lang->companyname;
            $customers[0]['cname'] = $lang->country;
            $customers[0]['affiliates'] = $lang->affiliate;

            $i = 1;
            while($customers[$i] = $db->fetch_assoc($query)) {
                $query2 = $db->query("SELECT ae.*, a.name FROM ".Tprefix."affiliatedentities ae LEFT JOIN ".Tprefix."affiliates a ON (a.affid=ae.affid) WHERE ae.eid='{$customers[$i][eid]}' ORDER BY a.name ASC");
                $comma = $customers[$i]['affiliates'] = "";
                while($affiliate = $db->fetch_array($query2)) {
                    $customers[$i]['affiliates'] .= "{$comma}{$affiliate[name]}";
                    $comma = ", ";
                }
                $i++;
            }
            $excelfile = new Excel("array", $customers);
        }
    }
    elseif($core->input['action'] == 'get_mergeanddeleteentities') {
        $filename = 'viewcustomers';
        $entitytype = 'customer';
        eval("\$mergeanddelete = \"".$template->get('popup_mergeanddeleteentities')."\";");
        output($mergeanddelete);
    }
    elseif($core->input['action'] == 'perform_mergeanddeleteentities') {
        if($core->usergroup['canAddCustomers'] == 0) {
            output_xml("<status>false</status><message>{$lang->errordeleting}</message>");
            exit;
        }
        $oldid = intval($core->input['todelete']);
        $newid = $db->escape_string($core->input['mergeeid']);
        $oldentity = new Entities($oldid);
        $merge = $oldentity->mergeanddelete($oldid, $newid);
        if($merge == true) {
            output_xml("<status>true</status><message>{$lang->successdeletemerge}</message>");
        }
        elseif($merge == false) {
            output_xml("<status>false</status><message>{$lang->errordeleting}</message>");
        }
    }
    elseif($core->input['action'] == 'perform_deleteentity') {
        if($core->usergroup['canAddCustomers'] == 0) {
            output_xml("<status>false</status><message>{$lang->errordeleting}</message>");
            exit;
        }
        $todeleteid = intval($core->input['todelete']);
        $customer_columns = array('eid', 'cid');
        foreach($customer_columns as $column) {
            $tables[$column] = $db->get_tables_havingcolumn($column);
        }
        if(is_array($tables)) {
            foreach($tables as $key => $columntables) {
                if(is_array($columntables)) {
                    $customer_tables[$key] = array_fill_keys(array_values($columntables), $key);
                }
            }
        }
        $exclude_tables = array('entitiesrepresentatives', 'affiliatedentities', 'assignedemployees');

        foreach($customer_tables as $tables) {
            if(is_array($tables)) {
                foreach($tables as $table => $attr) {
                    if(in_array($table, $exclude_tables)) {
                        continue;
                    }
                    $query = $db->query("SELECT * FROM ".Tprefix.$table." WHERE ".$attr." = ".$todeleteid);
                    if($db->num_rows($query) > 0) {
                        $usedin_tables[] = $table;
                    }
                }
            }
        }
        if(is_array($usedin_tables)) {
            $result = implode(", ", $usedin_tables);
            output_xml("<status>false</status><message>{$lang->deleteerror}</message>");
            exit;
        }
        /* Delete Entity */
        $deletequery = $db->delete_query('entities', "eid = '{$todeleteid}'");

        /* Delete "deleted entity" data from excluded tabes if found ? */
        if($deletequery) {
            output_xml("<status>true</status><message>{$lang->successdelete}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->errordeleting}</message>");
        }
    }
}
?>