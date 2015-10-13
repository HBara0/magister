<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 *
 * View customers
 * $module: admin/entities
 * $id: viewsuppliers.php
 * Created:		@zaher.reda
 * Last Update: @zaher.reda 	September 28, 2009 | 11:29 AM
 */
if(!defined("DIRECT_ACCESS")) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canManageSuppliers'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $sort_query = 's.companyName ASC';
    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query = $core->input['sortby'].' '.$core->input['order'];
    }
    $sort_url = sort_url();
    $filters_config = array(
            'parse' => array('filters' => array('id', 'suppliers', 'affiliate', 'coid'),
                    'overwriteField' => array(
                            'id' => '',
                    )
            ),
            'process' => array(
                    'filterKey' => 'eid',
                    'mainTable' => array(
                            'name' => 'entities',
                            'filters' => array('suppliers' => array('name' => 'companyName'), 'coid' => array('operatorType' => 'multiple', 'name' => 'country')),
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

    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
    }

    $query = $db->query("SELECT s.companyName AS entityname, s.*, c.name as cname
						FROM ".Tprefix."entities s, ".Tprefix."countries c
						WHERE s.country=c.coid AND s.type='s'".$filter_where."
						ORDER BY {$sort_query}
						LIMIT {$limit_start}, {$core->settings[itemsperlist]}");

    if($db->num_rows($query) > 0) {
        while($supplier = $db->fetch_array($query)) {
            $query2 = $db->query("SELECT ae.*, a.name FROM ".Tprefix."affiliatedentities ae LEFT JOIN ".Tprefix."affiliates a ON (a.affid=ae.affid) WHERE ae.eid='$supplier[eid]' ORDER BY a.name ASC");
            $comma = $affiliates = $approve_icon = '';
            while($affiliate = $db->fetch_array($query2)) {
                $affiliates .= "{$comma}{$affiliate[name]}";
                $comma = ', ';
            }
            if($supplier['approved'] == 0) {
                $class = 'unapproved';
                $approve_icon = "<a href='#' id='approve_entities/edit_approved_1_{$supplier[eid]}' class='green_text'><img src='{$core->settings[rootdir]}/images/valid.gif' alt='{$lang->approve}' border='0' /></a>";
            }
            else {
                $class = alt_row($class);
            }
            $entities_list .= "<tr class='{$class}'><td>{$supplier[eid]}</td><td><a href='../index.php?module=profiles/entityprofile&amp;eid={$supplier[eid]}'>{$supplier[companyName]}</a></td><td>{$affiliates}</td><td>{$supplier[cname]}</td>";
            $entities_list .= "<td style='text-align: right;'>{$approve_icon}<a href='index.php?module=entities/edit&amp;eid={$supplier[eid]}'><img src='{$core->settings[rootdir]}/images/edit.gif' alt='{$lang->edit}' border='0' /></a>";
            $entities_list .= "<a href='#' id='mergeanddeleteentities_{$supplier[eid]}_entities/viewsuppliers_loadpopupbyid'><img src='{$core->settings[rootdir]}/images/invalid.gif' border='0' /></td></tr>";
        }
        $multipages = new Multipages('entities', $core->settings['itemsperlist'], "type='s'");
        $entities_list .= "<tr><td colspan='4'>".$multipages->parse_multipages()."</td><td style='text-align: right;'><a href='".$_SERVER['REQUEST_URI']."&amp;action=exportexcel'><img src='../images/xls.gif' alt='{$lang->exportexcel}' border='0' /></a></td></tr>";
    }
    else {
        $entities_list = "<tr><td colspan='5' style='text-align: center;'>{$lang->nosuppliersavailable}</td></tr>";
    }

    $lang->listavailableentities = $lang->listavailablesuppliers;

    eval("\$supplierspage = \"".$template->get('admin_entities_view')."\";");
    output_page($supplierspage);
}
else {
    if($core->input['action'] == "exportexcel") {
        $sort_query = 's.companyName ASC';
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = $core->input['sortby'].' '.$core->input['order'];
            $query = $db->query("SELECT s.eid, s.companyName AS entityname, c.name as cname
						FROM ".Tprefix."entities s, ".Tprefix."countries c
						WHERE s.country=c.coid AND s.type='s'
						ORDER BY {$sort_query}");
            if($db->num_rows($query) > 0) {
                $suppliers[0]['eid'] = $lang->id;
                $suppliers[0]['entityname'] = $lang->companyname;
                $suppliers[0]['cname'] = $lang->country;
                $suppliers[0]['affiliates'] = $lang->affiliate;
                $i = 1;
                while($suppliers[$i] = $db->fetch_assoc($query)) {
                    $query2 = $db->query("SELECT ae.*, a.name FROM ".Tprefix."affiliatedentities ae LEFT JOIN ".Tprefix."affiliates a ON (a.affid=ae.affid) WHERE ae.eid='{$suppliers[$i][eid]}' ORDER BY a.name ASC");
                    $comma = $suppliers[$i]['affiliates'] = '';
                    while($affiliate = $db->fetch_array($query2)) {
                        $suppliers[$i]['affiliates'] .= "{$comma}{$affiliate[name]}";
                        $comma = ', ';
                        $i++;
                    }
                    $excelfile = new Excel('array', $suppliers);
                }
            }
        }
    }
    elseif($core->input['action'] == 'get_mergeanddeleteentities') {
        $filename = 'viewsuppliers';
        $entitytype = 'supplier';
        eval("\$mergeanddelete = \"".$template->get('popup_mergeanddeleteentities')."\";");
        output($mergeanddelete);
    }
    elseif($core->input['action'] == 'perform_mergeanddeleteentities') {
        if($core->usergroup['canAddSuppliers'] == 0) {
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
        if($core->usergroup['canAddSuppliers'] == 0) {
            output_xml("<status>false</status><message>{$lang->errordeleting}</message>");
            exit;
        }
        $todeleteid = intval($core->input['todelete']);
        $supplier_columns = array('eid', 'spid', 'companyName');
        foreach($supplier_columns as $column) {
            $tables[$column] = $db->get_tables_havingcolumn($column);
        }
        if(is_array($tables)) {
            foreach($tables as $key => $columntables) {
                if(is_array($columntables)) {
                    $supplier_tables[$key] = array_fill_keys(array_values($columntables), $key);
                }
            }
        }
        $exclude_tables = array('entitiesrepresentatives', 'affiliatedentities', 'assignedemployees'); // sourcingsupliers,sourcing_suppliers_contactpersons,sourcing_suppliers_contactpersons_new

        foreach($supplier_tables as $tables) {
            if(is_array($tables)) {
                foreach($tables as $table => $attr) {
                    if(in_array($table, $exclude_tables)) {
                        continue;
                    }
                    $query = $db->query("SELECT * FROM ".Tprefix.$table." WHERE ".$attr."=".$todeleteid);
                    if($db->num_rows($query) > 0) {
                        $usedin_tables[] = $table;
                    }
                }
            }
        }
        if(is_array($usedin_tables)) {
            $result = implode(",", $usedin_tables);
            output_xml("<status>false</status><message>{$lang->deleteerror}</message>");
            exit;
        }
        /* Delete Entity */
        $deletequery = $db->delete_query('entities', " eid='{$todeleteid}'");

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