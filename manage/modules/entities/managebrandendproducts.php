<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: managebrandendproducts.php
 * Created:        @rasha.aboushakra    Jun 17, 2015 | 5:07:33 PM
 * Last Update:    @rasha.aboushakra    Jun 17, 2015 | 5:07:33 PM
 */


if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

//if($core->usergroup[''] == 0) {
//error($lang->sectionnopermission);
//exit;
//}

if(!$core->input['action']) {
    $sort_url = sort_url();

    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('endproduct', 'description', 'characteristic', 'classificationClass', 'brand'),
                    'overwriteField' => array('endproduct' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->endproduct.'"/>',
                            'description' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->description.'"/>',
                            'characteristic' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->characteristic.'"/>',
                            'classificationClass' => '<input class="inlinefilterfield" type="text" placeholder="'.$lang->classificationclass.'"/>',
                            'brand' => '<input class="inlinefilterfield" type="text" placeholder="'.$lang->brand.'"/>'
                    )),
    );

    $filter = new Inlinefilters($filters_config);
    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));


    $ebid = intval($core->input['id']);
    $brand_obj = EntitiesBrands::get_data(array('ebid' => $ebid));
    if(is_object($brand_obj)) {
        $brandproduct['brand'] = $brand_obj->parse_link();
        $brandproduct['brandname'] = $brand_obj->get_displayname();
    }
    $entitybrandproducts_objs = EntBrandsProducts::get_data(array('ebid' => $ebid), array('returnarray' => true));
    if(is_array($entitybrandproducts_objs)) {
        foreach($entitybrandproducts_objs as $entitybrandproducts_obj) {
            $entitybrandproduct = $entitybrandproducts_obj->get();
            $endproducttype = EndProducTypes::get_data(array('eptid' => $entitybrandproduct['eptid']));
            $brandproduct['characteristic'] = $brandproduct['endproductname'] = $brandproduct['classificationClass'] = '-';
            if(is_object($endproducttype)) {
                $brandproduct['endproductname'] = $endproducttype->parse_link();
                $first_parent = $endproducttype->get_parent();
                if(is_object($first_parent)) {
                    $brandproduct['endproductname'] .= '--> '.$first_parent->get_displayname();
                    $secondpar_obj = $first_parent->get_parent();
                    if(is_object($secondpar_obj)) {
                        $brandproduct['endproductname'].='-->'.$secondpar_obj->get_displayname();
                        $third_par = $secondpar_obj->get_parent();
                        if(is_object($third_par)) {
                            $originalpar_obj = $third_par->get_mother();
                            if(is_object($originalpar_obj)) {
                                $brandproduct['endproductname'].='->.....->'.$originalpar_obj->get_displayname();
                            }
                        }
                    }
                }
            }
//            $brand_obj = EntitiesBrands::get_data(array('ebid' => $entitybrandproduct['ebid']));
//            if(is_object($brand_obj)) {
//                $brandproduct['brand'] = $brand_obj->parse_link();
//            }
            $productcharacteristic = ProductCharacteristicValues::get_data(array('pcvid' => $entitybrandproduct['pcvid']));
            if(is_object($productcharacteristic)) {
                $brandproduct['characteristic'] = $productcharacteristic->get_displayname();
            }
            $brandproduct['classificationClass'] = $entitybrandproduct['classificationClass'];
            $brandproduct['description'] = $entitybrandproduct['description'];
            eval("\$brandproducts_list .= \"".$template->get('admin_entities_brandendproducts_rows')."\";");
            unset($details);
        }
    }
    else {
        $brandproducts_list = '<tr><td colspan="3">'.$lang->na.'</td></tr>';
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

        $classification_classes = array('Class A', 'Class B', 'Class C');
        $classification_classes = array_combine($classification_classes, $classification_classes);
        foreach($values as $key => $value) {
            $checked = $rowclass = '';
            $endproducttypes_list .= ' <tr class="'.$rowclass.'">';
            $endproducttypes_list .= '<td><input id="producttypefilter_check_'.$key.'" type="checkbox"'.$checked.' value="'.$key.'" name="entitybrand[endproducttypes]['.$key.'][eptid]">'.$value.'<input style="float:right;" type="text" name="entitybrand[endproducttypes]['.$key.'][description]" placeholder="'.$lang->description.'" /></td>'
                    .'<td>'.parse_selectlist("entitybrand[endproducttypes][".$key."][classificationClass]", '', $classification_classes, '', '', '', array('blankstart' => true)).'</td></tr>';
        }
    }

    $characteristics = ProductCharacteristicValues::get_data(null, array('returnarray' => true, 'order' => ProductCharacteristicValues::DISPLAY_NAME));
    $characteristics_list = parse_selectlist('entitybrand[pcvid]', 4, $characteristics, null, 0, null, array('blankstart' => true));
    $module = 'entities';
    $modulefile = 'managebrandendproducts';
    $disabled = 'disabled';
    $display['customer'] = 'style="display:none;"';
    $ebid_hiddenfield = '<input type="hidden" value="'.$ebid.'" name="entitybrand[ebid]">';
    eval("\$popup_createbrand = \"".$template->get('popup_createbrand')."\";");


    eval("\$brandendproducts = \"".$template->get('admin_entities_brandendproducts')."\";");
    output_page($brandendproducts);
}
else {
    if($core->input['action'] == 'do_addbrand') {
        $data = $core->input['entitybrand'];
        if(is_array($data['endproducttypes'])) {
            foreach($data['endproducttypes'] as $eptid => $endproduct) {
                if(isset($endproduct['eptid']) && !empty($endproduct['eptid'])) {
                    if(value_exists('entitiesbrandsproducts', 'eptid', $eptid, 'pcvid='.intval($data['pcvid']).' AND ebid='.intval($data['ebid']))) {
                        output_xml('<status>false</status><message>'.$lang->itemalreadyexist.'</message>');
                        exit;
                    }
                    $entitiesbrandsproducts_data = array(
                            'ebid' => $data['ebid'],
                            'eptid' => $eptid,
                            'pcvid' => $data['pcvid'],
                            'description' => $endproduct['description'],
                            'classificationClass' => $endproduct['classificationClass'],
                            'createdBy' => $core->user['uid'],
                            'createdOn' => TIME_NOW
                    );
                    $entitybrand_obj = new EntBrandsProducts();
                    $entitybrand_obj->set($entitiesbrandsproducts_data);
                    $entitybrand_obj->save();
                    if(($entitybrand_obj->get_errorcode()) != 0) {
                        break;
                    }
                }
            }
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
    elseif($core->input['action'] == 'get_deletebrandendproduct') {
        $ebpid = $db->escape_string($core->input['id']);
        $entitybrandproduct_obj = new EntBrandsProducts($ebpid);
        $entitybrandproduct = $entitybrandproduct_obj->get();

        eval("\$deleteendproducttype = \"".$template->get('popup_deletebrandendproduct')."\";");
        echo $deleteendproducttype;
    }
    elseif($core->input['action'] == 'perform_delete') {
        $ebpid = $db->escape_string($core->input['todelete']);
        $brandendproduct = new EntBrandsProducts($ebpid);
        $relatedtables = $db->get_tables_havingcolumn('ebpid', '(TABLE_NAME !="entitiesbrandsproducts")');
        if(is_array($relatedtables)) {

            foreach($relatedtables as $table) {
                if(value_exists($table, 'ebpid', $ebpid)) {
                    output_xml("<status>false</status><message>{$lang->deleteerror}</message>");
                    exit;
                }
            }
        }
        $brandendproduct->delete();
        switch($brandendproduct->get_errorcode()) {
            case 0:
                output_xml("<status>true</status><message>{$lang->successdelete}</message>");
                break;
        }
    }
}
?>
