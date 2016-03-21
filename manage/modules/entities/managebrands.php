<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: managebrands.php
 * Created:        @tony.assaad    Dec 17, 2013 | 12:55:16 PM
 * Last Update:    @tony.assaad    Dec 17, 2013 | 12:55:16 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canManageapllicationsProducts'] == 0) {
    //error($lang->sectionnopermission);
    //exit;
}

if(!$core->input['action']) {
    $sort_url = sort_url();
    $entitybrand_objs = EntitiesBrands::get_entitybrands();
    if(is_array($entitybrand_objs)) {
        foreach($entitybrand_objs as $entitybrand_obj) {
            $entitybrands = $entitybrand_obj->get();
            $entities = $entitybrand_obj->get_entity();
            $entitybrands['supplier'] = $entities->get()['companyName'];
            eval("\$entitybrands_list .= \"".$template->get('admin_entities_brands_rows')."\";");
        }
    }
    else {
        $entitybrands_list = '<tr><td colspan="3">'.$lang->na.'</td></tr>';
    }

    $productypes_objs = EndProducTypes::get_endproductypes();
    if(is_array($productypes_objs)) {
        foreach($productypes_objs as $productype) {
            $value = $productype->title;
            $pplication = $productype->get_application()->parse_link();
            if($pplication !== null) {
                $value .=' - '.$pplication;
            }
            $parent = $productype->get_endproducttype_chain();
            if(!empty($parent)) {
                $values[$productype->eptid] = $parent.' > '.$value;
            }
            else {
                $values[$productype->eptid] = $value;
            }
        }
        asort($values);

        $classification_classes = array('A', 'B', 'C');
        $classification_classes = array_combine($classification_classes, $classification_classes);
        foreach($values as $key => $value) {
            $checked = $rowclass = '';
            $endproducttypes_list .= ' <tr class="'.$rowclass.'">';
            $endproducttypes_list .= '<td><input id="producttypefilter_check_'.$key.'" type="checkbox"'.$checked.' value="'.$key.'" name="entitybrand[endproducttypes]['.$key.'][eptid]">'.$value.'<input style="float:right;" type="text" name="entitybrand[endproducttypes]['.$key.'][description]" placeholder="'.$lang->description.'"  value="'.$brandproduct[description].'"/></td>'
                    .'<td>'.parse_selectlist("entitybrand[endproducttypes][".$key."][classificationClass]", '', $classification_classes, '', '', '', array('blankstart' => true)).'</td></tr>';
        }
        //$endproducttypes_list.='<option value = "'.$endproduct_types['eptid'].'">'.$endproduct_types['title'].'</option>';
    }

    $characteristics = ProductCharacteristicValues::get_data(null, array('order' => array('by' => array(ProductCharacteristicValues::DISPLAY_NAME, 'pcid')), 'returnarray' => true));
    $characteristics_list = parse_selectlist('entitybrand[pcvid]', 4, $characteristics, null, 0, null, array('blankstart' => true));
    $module = 'entities';
    $modulefile = 'managebrands';
    eval("\$popup_createbrand = \"".$template->get('popup_createbrand')."\";");
    eval("\$brandspage = \"".$template->get('admin_entities_brands')."\";");
    output_page($brandspage);
}
else {
    if($core->input['action'] == 'do_addbrand') {
        $ebid = intval($core->input['entitybrand']['ebid']);
        if(!empty($core->input['entitybrand']['ebid'])) {
            $entbrand = EntitiesBrands::get_data(array('name' => $core->input['entitybrand']['name'], 'eid' => $core->input['entitybrand']['eid']));
            if(is_object($entbrand) && $entbrand->ebid != $ebid) {
                output_xml('<status>false</status><message>'.$lang->brandexists.'</message>');
                exit;
            }
            else {
                $entitybrand_obj = new EntitiesBrands($ebid);
                $entitybrand_obj->update($core->input['entitybrand']);
            }
        }
        else {
            $entitybrand_obj = new EntitiesBrands();

            $entitybrand_obj->create($core->input['entitybrand']);
        }
        switch($entitybrand_obj->get_errorcode()) {
            case 0:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 1:
                output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
                break;
            case 2:
                output_xml('<status>false</status><message>'.$lang->itemalreadyexist.'</message>');
                break;
        }
    }
    elseif($core->input['action'] == 'get_deletebrand') {
        $ebid = $db->escape_string($core->input['id']);
        $entitybrand_obj = new EntitiesBrands($ebid);
        $entitybrand = $entitybrand_obj->get();
        eval("\$deletebrand = \"".$template->get('popup_deletebrand')."\";");
        output($deletebrand);
    }
    elseif($core->input['action'] == 'perform_delete') {
        $ebid = $db->escape_string($core->input['todelete']);
        $entbrand = new EntitiesBrands($ebid);
        $relatedtables = $db->get_tables_havingcolumn('ebid', '(TABLE_NAME !="entitiesbrands")');
        if(is_array($relatedtables)) {
            foreach($relatedtables as $table) {
                $error_output = '';
                if(value_exists($table, 'ebid', $ebid)) {
                    $errorhandler->record('Entry used in', $table);
                    if($core->usergroup['canPerformMaintenance'] == 1) {
                        $error_output = $errorhandler->get_errors_inline();
                    }
                    output_xml("<status>false</status><message>{$lang->deleteerror}<![CDATA[<br/>{$error_output}]]></message>");
                    exit;
                }
            }
        }
        $entbrandproducts = EntBrandsProducts::get_data(array('ebid' => $ebid), array('returnarray' => true));
        if(is_array($entbrandproducts)) {
            foreach($entbrandproducts as $entbrandproduct) {
                $entbrandproduct->delete();
            }
        }
        $entbrand->delete();
        switch($entbrand->get_errorcode()) {
            case 0:
                output_xml("<status>true</status><message>{$lang->successdelete}</message>");
                break;
        }
    }
    elseif($core->input['action'] == 'get_editbrand') {
        $id = intval($core->input['id']);
        $entbrand = new EntitiesBrands($id);
        $entitybrand = $entbrand->get();
        $customer = Entities::get_data(array('eid' => $entbrand->eid));
        if(is_object($customer)) {
            $entitybrand['customer'] = $customer->get_displayname();
        }
        $ebid_hiddenfield = '<input type = "hidden" value = "'.$id.'" name = "entitybrand[ebid]">';
        $module = 'entities';
        $modulefile = 'managebrands';
        eval("\$popup_editbrand = \"".$template->get('popup_editbrand')."\";");
        output($popup_editbrand);
    }
}
?>
