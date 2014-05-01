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

    $productypes_objs = EndproducTypes::get_endproductypes();
    if(is_array($productypes_objs)) {
        foreach($productypes_objs as $productypes_obj) {
            $endproduct_types = $productypes_obj->get();
            $endproducttypes_list.='<option value="'.$endproduct_types['eptid'].'">'.$endproduct_types['title'].'</option>';
        }
    }
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
