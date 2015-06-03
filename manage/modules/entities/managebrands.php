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
        foreach($values as $key => $value) {
            $checked = $rowclass = '';
            $endproducttypes_list .= ' <tr class="'.$rowclass.'">';
            $endproducttypes_list .= '<td><input id="producttypefilter_check_'.$key.'" type="checkbox"'.$checked.' value="'.$key.'" name="entitybrand[endproducttypes]['.$key.']">'.$value.'</td><tr>';
        }
        //$endproducttypes_list.='<option value="'.$endproduct_types['eptid'].'">'.$endproduct_types['title'].'</option>';
    }

    $characteristics = ProductCharacteristicValues::get_data(null, array('returnarray' => true));
    $characteristics_list = parse_selectlist('entitybrand[pcvid]', 4, $characteristics, null, 0, null, array('blankstart' => true));
    $module = 'entities';
    $modulefile = 'managebrands';
    eval("\$popup_createbrand = \"".$template->get('popup_createbrand')."\";");
    eval("\$brandspage = \"".$template->get('admin_entities_brands')."\";");
    output_page($brandspage);
}
else {
    if($core->input['action'] == 'do_addbrand') {
        $entitybrand_obj = new EntitiesBrands();
        $entitybrand_obj->create($core->input['entitybrand']);
        switch($entitybrand_obj->get_errorcode()) {
            case 0:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 1:
                output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
                break;
            case 2:
                output_xml('<status>false</status><message>eeee'.$lang->itemalreadyexist.'</message>');
                break;
        }
    }
}
?>
