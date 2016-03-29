<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: productypes.php
 * Created:        @tony.assaad    Dec 19, 2013 | 10:48:26 AM
 * Last Update:    @tony.assaad    Dec 19, 2013 | 10:48:26 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['canAddProducts'] == 0) {
    error($lang->sectionnopermission);
    exit;
}
$lang->load('products_types');
if(!$core->input['action']) {
    $sort_url = sort_url();
    $endproducttypes = EndProducTypes::get_endproductypes_tree();
    if(is_array($endproducttypes)) {
        $endproducttype = new EndProducTypes();
        $productstypes_list = $endproducttype->parse_endproducttype_list($endproducttypes);
//        foreach($endprod_objs as $endprod_obj) {
//            $altrow_class = alt_row($altrow_class);
//            $productypes = $endprod_obj->get();
//            $productypes['application'] = $endprod_obj->get_application()->get()['title'];
//            eval("\$productstypes_list .= \"".$template->get('admin_productstypes_rows')."\";");
//        }
    }
    else {
        $productstypes_list = '<tr><td colspan="3">'.$lang->na.'</td></tr>';
    }

    /* Parse list for the Create Product Lists popup */
    $applications_obj = SegmentApplications::get_segmentsapplications('', array('order' => array('by' => 'name', 'sort' => 'ASC')));
    if(is_array($applications_obj)) {
        $applications_list .= '<option value="0" selected placeholder=="select"></option>';
        foreach($applications_obj as $application_obj) {
            $applications = $application_obj->get();
            if(is_array($applications)) {
                $applications_list .= '<option value='.$applications['psaid'].'>'.$applications['title'].'</option>';
            }
        }
    }

    eval("\$addproductstypes = \"".$template->get('admin_productstypes')."\";");
    output_page($addproductstypes);
}
elseif($core->input['action'] == 'do_create') {
    $endprod_obj = new EndProducTypes();
    $core->input['productypes']['name'] = generate_alias($core->input['productypes']['title']);
    $endprod_obj->set($core->input['productypes']);
    $endprod_obj = $endprod_obj->save();
    switch($endprod_obj->get_errorcode()) {
        case 0:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 1:
            output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
            break;
        case 2:
            output_xml('<status>false</status><message>Fill All Required Fields</message>');
            break;
        case 5:
            output_xml('<status>true</status><message>Entry Has Been Updated</message>');
            break;
    }
}
elseif($core->input['action'] == 'get_editendproducts') {
    $endprod_obj = new EndProducTypes($db->escape_string($core->input['id']));
    $endprod = $endprod_obj->get();
    if(!empty($endprod['parent'])) {
        $parent_obj = new EndProducTypes($endprod['parent']);
        if(is_object($parent_obj)) {
            $parent = $parent_obj->get();
            $disabled = 'disabled="disabled"';
        }
    }
    $app_obj = new SegmentApplications($endprod_obj->psaid);
    $applications_list = '<option value='.$app_obj->psaid.'>'.$app_obj->title.'</option>';
    $applications_obj = SegmentApplications::get_segmentsapplications('psaid !='.$endprod_obj->psaid, array('order' => array('by' => 'name', 'sort' => 'ASC')));
    if(is_array($applications_obj)) {
        foreach($applications_obj as $application_obj) {
            $applications = $application_obj->get();
            $applications_list .= '<option value='.$applications['psaid'].'>'.$applications['title'].'</option>';
            unset($selected);
        }
    }
    eval("\$editpopup = \"".$template->get('popup_editendproducts')."\";");
    output($editpopup);
}
elseif($core->input['action'] == 'edit_endproduct') {
    $endprod_obj = new EndProducTypes();
    $core->input['endproduct']['name'] = generate_alias($core->input['endproduct']['title']);
    $endprod_obj->set($core->input['endproduct']);
    $endprod_obj = $endprod_obj->save();
    switch($endprod_obj->get_errorcode()) {
        case 0:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 1:
            output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
            break;
        case 2:
            output_xml('<status>false</status><message>Fill All Required Fields</message>');
            break;
        case 5:
            output_xml('<status>true</status><message>Entry Has Been Updated</message>');
            break;
    }
}
elseif($core->input['action'] == 'get_deleteendproducttype') {
    $id = $core->input['id'];
    eval("\$deleteendprodtype = \"".$template->get('popup_deleteendproducttype')."\";");
    output($deleteendprodtype);
}
elseif($core->input['action'] == 'delete_endproducttype') {
    $endprod_obj = new EndProducTypes($core->input['todelete']);
    $endprod_obj = $endprod_obj->delete_endproducttype($core->input['todelete']);
    switch($endprod_obj->get_errorcode()) {
        case 0:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 3:
            output_xml('<status>false</status><message>'.$lang->cannotdeleteentry.'</message>');
            break;
    }
}
elseif($core->input['action'] == 'get_cloneendproducttype') {
    $eptid = $core->input['id'];

    eval("\$cloneendprodtype = \"".$template->get('popup_cloneendproducttype')."\";");
    output($cloneendprodtype);
}
elseif($core->input['action'] == 'clone_endproducttype') {
    $endproducttype_obj = new EndProducTypes();
    $endproducttype = $endproducttype_obj->clone_endproducttype($core->input['endproduct']);
    switch($endproducttype->get_errorcode()) {
        case 0:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 2:
            output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
            break;
    }
}
?>
