<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: manageapplication.php
 * Created:        @tony.assaad    Dec 5, 2013 | 11:50:21 AM
 * Last Update:    @tony.assaad    Dec 5, 2013 | 11:50:21 AM
 */

if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canManageSegments'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$lang->load('products_applications');
$lang->load('global');
if(!$core->input['action']) {
    $sort_url = sort_url();
    $applications_obj = SegmentApplications::get_segmentsapplications();
    if(is_array($applications_obj)) {
        /* loop over the returned objects and get their related data */
        foreach($applications_obj as $application_obj) {
            $altrow_class = alt_row($altrow_class);
            $application = $application_obj->get();
            $application['segment'] = $application_obj->get_segment()->get()['title'];
            eval("\$productsapplications_list .= \"".$template->get("admin_products_applications_row")."\";");
        }
    }

    $segments_obj = ProductsSegments::get_segments();
    if(is_array($segments_obj)) {
        /* for best preformance loop over the returned segments objects and get their related data */
        foreach($segments_obj as $segment_obj) {
            $segment = $segment_obj->get();
            $segments_list .= '<option value='.$segment['psid'].'>'.$segment['title'].'</option>';
        }
    }
    $chemicals_obj = ChemicalFunctions::get_functions();
    if(is_array($chemicals_obj)) {
        /* for best preformance loop over the returned ChemicalFunctions objects and get their related data */
        $checmicalfunctions_list = '<option value="" selected="selected" > </option>';
        foreach($chemicals_obj as $chemical_obj) {
            $chemical = $chemical_obj->get();
            $checmicalfunctions_list .= '<option value='.$chemical['cfid'].'>'.$chemical['title'].'</option>';
        }
    }
    $multipages = new Multipages("segmentapplications", $core->settings['itemsperlist']);
    $productsapplications_list .= "<tr><td colspan='5'>".$multipages->parse_multipages()."</td></tr>";
    eval("\$applicationpage = \"".$template->get("admin_products_applications")."\";");
    output_page($applicationpage);
}
else {
    if($core->input['action'] == 'do_create') {
        $segmentapplications_obj = new SegmentApplications();
        $segmentapplications_obj->create($core->input['segmentapplications']);
        switch($segmentapplications_obj->get_errorcode()) {
            case 0:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 1:
                output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
                break;
            case 2:
                output_xml('<status>false</status><message>'.$lang->entryexist.'</message>');
                break;
        }
    }
}
?>
