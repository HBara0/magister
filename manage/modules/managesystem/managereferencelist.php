<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: managereferenecelist.php
 * Created:        @hussein.barakat    Apr 29, 2015 | 8:38:59 AM
 * Last Update:    @hussein.barakat    Apr 29, 2015 | 8:38:59 AM
 */

if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}
if($core->usergroup['admin_canManageSystemDef'] == 0) {
    error($lang->sectionnopermission);
    exit;
}
$lang = new Language('english', 'admin');
$lang->load('managesystem');
$lang->load('global');
if(!$core->input['action']) {
    if(isset($core->input['rlid']) && !empty($core->input['rlid'])) {
        $selectlist_obj = new SystemReferenceLists($core->input['rlid']);
        if(is_object($selectlist_obj)) {
            $selectlist = $selectlist_obj->get();
            $srlid = $selectlist['srlid'];
            $list_name = ' - '.$selectlist['name'];
        }
        $select_lines = $selectlist_obj->parse_listlines();
        $select_table = $selectlist_obj->parse_tablelines();
        $l_rowid = $select_lines[1];
        $select_lines = $select_lines[0];
    }
    if(empty($select_table)) {
        $inputCheckSum = generate_checksum('system_referencelists_lines');
        /* Addin a blank row */
        $select_table.= '<tr><div>';
        $select_table.= '<td><input type="text" name="line['.$inputCheckSum.'][tableName]"></td>';
        $select_table.= '<input type="hidden" name="line['.$inputCheckSum.'][srlid]">';
        $select_table.= '<td><input type="text" name="line['.$inputCheckSum.'][keyColumn]"></td>';
        $select_table.= '<td><input type="text" name="line['.$inputCheckSum.'][displayedColumn]"></td>';
        $select_table.= '<td><textarea name="line['.$inputCheckSum.'][whereClause]"></textarea></td>';
        $select_table.='</div></tr>';
    }
    if(empty($select_lines)) {
        $inputCheckSum = generate_checksum('system_referencelists_lines');
        $select_lines.='<tr id="1"><div>';
        $select_lines.= '<td><input type="text" name="line['.$inputCheckSum.'][name]"></td>';
        $select_lines.= '<input type="hidden" value="'.$selectlist_obj->referenceType.'" name="line['.$inputCheckSum.'][type]>';
        $select_lines.= '<input type="hidden" name="line['.$inputCheckSum.'][srlid]">';
        $select_lines.= '<td><input type="text" name="line['.$inputCheckSum.'][title]"></td>';
        $select_lines.= '<td><input type="text" name="line['.$inputCheckSum.'][value]"></td>';
        $select_lines.= '<td><input type="number" name="line['.$inputCheckSum.'][sequence]"></td>';
        $select_lines.= '<td><textarea name="line['.$inputCheckSum.'][description]"></textarea></td>';
        $select_lines.= '<td><input type="checkbox" value="1" name="line['.$inputCheckSum.'][isActive]"></td>';
        $select_lines.='</div></tr>';
        $l_rowid = 1;
    }
    eval("\$referenece_lines = \"".$template->get('admin_manange_referencelist_linestable')."\";");
    $referencetype_select = parse_selectlist('selectlist[referenceType]', 0, array('list' => 'List', 'table' => 'DB-Table'), $selectlist['refereneceType']);
    $selectortype_options = array('selectlist' => 'Select-List', 'multiselectlist' => 'Multi-Selectlist', 'checkbox' => 'Checkboxes', 'radio' => 'Radio-Buttons');
    $selecttype_select = parse_selectlist('selectlist[selectorType]', 0, $selectortype_options, $selectlist['selectorType']);
    eval("\$selectlist = \"".$template->get('admin_manage_referencelist')."\";");
    output_page($selectlist);
}
else {
    if($core->input['action'] == 'do_perform_managereferencelist') {
        $referencelist_obj = new SystemReferenceLists();
        if(empty($core->input['selectlist']['name'])) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
        }
        else {
            $referencelist_obj->set($core->input['selectlist']);
            $referencelist_obj->save();
        }
        switch($referencelist_obj->get_errorcode()) {
            case 2:
                output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
                exit;
        }
        $error_codes[] = $referencelist_obj->get_errorcode();
        if(is_array($core->input['line']) && !empty($core->input['line'])) {
            foreach($core->input['line'] as $inputchecksum => $line) {
                if((empty($line['name']) && empty($line['tableName'])) || empty($line['type'])) {
                    continue;
                }
                if(!isset($line['srlid']) || empty($line['srlid'])) {
                    $line['srlid'] = $referencelist_obj->srlid;
                }
                $line['inputChecksum'] = $inputchecksum;
                $line_obj = new SystemReferenceListsLines();
                $line_obj->set($line);
                $line_obj->save();
                $error_codes[] = $line_obj->get_errorcode();
            }
        }
        foreach($error_codes as $error_code) {
            switch($error_code) {
                case 0:
                    output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
                    break;
                case 2:
                    output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
                    exit;
            }
        }
    }
    elseif($core->input['action'] == 'ajaxaddmore_lines') {
        $inputCheckSum = generate_checksum('system_referencelists_lines');
        $row_id = $core->input['value'] + 1;
        $select_lines.='<tr id="'.$row_id.'"><div>';
        $select_lines.= '<td><input type="text" name="line['.$inputCheckSum.'][name]"></td>';
        $select_lines.= '<input type="hidden" value="list" name="line['.$inputCheckSum.'][type]>';
        $select_lines.= '<input type = "hidden" value = "" name = "line_id">';
        $select_lines.= '<td><input type = "text" name = "line['.$inputCheckSum.'][title]"></td>';
        $select_lines.= '<td><input type = "text" name = "line['.$inputCheckSum.'][value]"></td>';
        $select_lines.= '<td><input type = "number" name = "line['.$inputCheckSum.'][sequence]"></td>';
        $select_lines.= '<td><textarea name = "line['.$inputCheckSum.'][description]"></textarea></td>';
        $select_lines.= '<td><input type = "checkbox" value = "1" name = "line['.$inputCheckSum.'][isActive]"></td>';
        $select_lines.='</div></tr>';
        echo ($select_lines);
    }
}