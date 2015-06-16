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

    $segments = ProductsSegments::get_segments();
    $segments_list = parse_selectlist('segmentapplications[psid]', 2, $segments, $application->psid);

    $chemicals_obj = ChemicalFunctions::get_functions();
    if(is_array($chemicals_obj)) {
        /* for best preformance loop over the returned ChemicalFunctions objects and get their related data */
        $checmicalfunctions_list = '<option value="" selected="selected"> </option>';
        foreach($chemicals_obj as $chemical_obj) {
            $chemical = $chemical_obj->get();
            $checmicalfunctions_list .= '<option value='.$chemical['cfid'].'>'.$chemical['title'].'</option>';
        }
    }
    $multipages = new Multipages("segmentapplications", $core->settings['itemsperlist']);
    $productsapplications_list .= "<tr><td colspan='5'>".$multipages->parse_multipages()."</td></tr>";
    $publishonwebcheckbox = '<input type="checkbox" value="1" name="segmentapplications[publishOnWebsite]">';
    eval("\$dialog_managerapplication = \"".$template->get("admin_popup_manageapplication")."\";");
    eval("\$applicationpage = \"".$template->get("admin_products_applications")."\";");
    output_page($applicationpage);
}
else {
    if($core->input['action'] == 'do_create') {
        $segmentapplications_obj = new SegmentApplications($core->input['segmentapplications'][SegmentApplications::PRIMARY_KEY]);
        $segmentapplications_obj->save($core->input['segmentapplications']);
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
    elseif($core->input['action'] == 'get_editbox') {
        $application = new SegmentApplications($core->input['id'], false);
        if($application->publishOnWebsite == 1) {
            $checked = 'checked="checked"';
        }
        $segments = ProductsSegments::get_segments();
        $segments_list = parse_selectlist('segmentapplications[psid]', 2, $segments, $application->psid);
        $publishonwebcheckbox = '<input type="checkbox" value="1" name="segmentapplications[publishOnWebsite]" '.$checked.'>';
        //$dialog_managerapplication = $headerinc;
        eval("\$dialog_managerapplication = \"".$template->get('admin_popup_manageapplication')."\";");
        output($dialog_managerapplication);
    }
}
?>
