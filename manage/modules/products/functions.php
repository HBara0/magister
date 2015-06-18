<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: functions.php
 * Created:        @tony.assaad    Dec 6, 2013 | 11:25:34 AM
 * Last Update:    @tony.assaad    Dec 6, 2013 | 11:25:34 AM
 */

if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canAddProducts'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$lang->load('products_functions');
if(!$core->input['action']) {
    $sort_url = sort_url();
    $applications_obj = SegmentApplications::get_segmentsapplications();
    if(is_array($applications_obj)) {
        foreach($applications_obj as $application_obj) {
            $application = $application_obj->get();
            $application['segment'] = $application_obj->get_segment()->get()['title'];

            if(is_array($application)) {
                $applications_list .= '<option value='.$application['psaid'].'>'.$application['segment'].' - '.$application['title'].'</option>';
                unset($application);
            }
        }
    }

    $functions_obj = ChemicalFunctions::get_functions();
    if(is_array($functions_obj)) {
        /* loop over the returned objects and get their related data */
        foreach($functions_obj as $function_obj) {
            $altrow_class = alt_row($altrow_class);
            $function = $function_obj->get();
            $functionsappseg_objs = $function_obj->get_applications();
            if(is_array($functionsappseg_objs)) {
                foreach($functionsappseg_objs as $safid => $functionsappseg_obj) {
                    $functions_applications = $functionsappseg_obj->get();
                    if(empty($functions_applications)) {
                        $functions_application = $lang->na;
                    }
                    if(is_array($functions_applications)) {
                        $functions_application .= $functions_applications['title'].' - '.$functionsappseg_obj->get_segment()->get()['title'];
                        $functions_application.= ' <a href="#'.$safid.'" id="segapdescription_'.$safid.'_products/functions_loadpopupbyid" title="'.$lang->description.'"><img src="'.$core->settings[rootdir].'/images/icons/report.gif" border="0"></a>';
                        $functions_application .= "<a href='#".$safid."' id='deleteappfunc_".$safid."_products/functions_loadpopupbyid'><img src='".$core->settings[rootdir]."/images/invalid.gif' border='0' /></a><br>";
                    }
                }
            }
            else {
                $functions_application = $lang->na;
            }
            eval("\$productsapplicationsfunctions_list .= \"".$template->get('admin_products_functions_row')."\";");
            $functions_application = '';
        }
    }
    else {
        $productsapplicationsfunctions_list = '<tr><td colspan="3">'.$lang->na.'</td></tr>';
    }
    $publishonwebsite = '<input type="checkbox" name="chemicalfunctions[publishOnWebsite]" value=1>';
    eval("\$popup_createfunction = \"".$template->get('admin_products_popup_createfunction')."\";");
    eval("\$functionpage = \"".$template->get('admin_products_functions')."\";");
    output_page($functionpage);
}
elseif($core->input['action'] == 'do_create') {
    $function_obj = new ChemicalFunctions();
    $function_obj->save($core->input['chemicalfunctions']);
    switch($function_obj->get_errorcode()) {
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
elseif($core->input['action'] == 'save_descr') {
    $segapfunct_obj = new SegApplicationFunctions($core->input['segfuncapp']);
    $publishonweb = "0";
    if($core->input['publishOnWebsite'] == 1) {
        $publishonweb = "1";
    }
    $fields = array('cfid' => $segapfunct_obj->cfid, 'psaid' => $segapfunct_obj->psaid, 'publishOnWebsite' => $publishonweb, 'description' => $core->input['segapdescription']);
    $segapfunct_obj->save($fields);
    switch($segapfunct_obj->get_errorcode()) {
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
elseif($core->input['action'] == 'deleteappfunc') {
    $todeleteid = intval($core->input['safid']);
    $tables['safid'] = $db->get_tables_havingcolumn('safid');
    if(is_array($tables)) {
        foreach($tables as $key => $functables) {
            if(is_array($functables)) {
                $func_tables[$key] = array_fill_keys(array_values($functables), $key);
            }
        }
    }
    $exclude_tables = array('segapplicationfunctions');

    foreach($func_tables as $tables) {
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
        $result = implode(', ', $usedin_tables);
        output_xml("<status>false</status><message>{$lang->cannotdeleteuseditem}</message>");
        exit;
    }
    /* Delete segappfunc */
    $deletequery = $db->delete_query('segapplicationfunctions', "safid='{$todeleteid}'");

    if($deletequery) {
        output_xml("<status>true</status><message>{$lang->successdelete}</message>");
    }
    else {
        output_xml("<status>false</status><message>{$lang->errordeletingsega}</message>");
    }
}
elseif($core->input['action'] == 'get_segapdescription') {
    $segapfunct_obj = new SegApplicationFunctions($core->input['id']);
    $safid = $segapfunct_obj->safid;
    $segapdescriptions = $segapfunct_obj->get_description();
    if($segapfunct_obj->publishOnWebsite == '1') {
        $checked = 'checked = "checked"';
    }
    $publishonwebsite = '<input type = "checkbox" name = "publishOnWebsite" value = "1" '.$checked.'>';
    eval("\$popup_applicationdescription = \"".$template->get('admin_products_popup_applicationdescription')."\";");
    output($popup_applicationdescription);
}
elseif($core->input['action'] == 'get_deleteappfunc') {
    $segapfunct_obj = new SegApplicationFunctions($core->input['id']);
    $safid = $segapfunct_obj->safid;
    eval("\$popup_deleteappfunc = \"".$template->get('popup_admin_product_deletesegappfunction')."\";");
    output($popup_deleteappfunc);
}
elseif($core->input['action'] == 'get_updatefunction') {
    $function = new ChemicalFunctions($core->input['id']);
    $publishonwebsite = '<input type = "checkbox" name = "chemicalfunctions[publishOnWebsite]" value = 1>';
    $existing_funcapplications = SegApplicationFunctions::get_data(array(ChemicalFunctions::PRIMARY_KEY => $function->get_id()), array('returnarray' => true));
    $applicationsfilters = null;
    if(is_array($existing_funcapplications)) {
        foreach($existing_funcapplications as $existing_funcapplication) {
            $applicationids[] = $existing_funcapplication->{SegmentApplications::PRIMARY_KEY};
        }
        $applicationsfilters = array(SegmentApplications::PRIMARY_KEY => implode(', ', $applicationids));
    }
    $applications_obj = SegmentApplications::get_data($applicationsfilters, array('returnarray' => true, 'operators' => array(SegmentApplications::PRIMARY_KEY => 'NOT IN')));
    if(is_array($applications_obj)) {
        foreach($applications_obj as $application) {
            $segment = $application->get_segment();
            if(is_object($application)) {
                $applications_list .= '<option value = '.$application->get_id().'>'.$segment->get_displayname().' - '.$application->title.'</option>';
                unset($segment);
            }
        }
    }
    eval("\$popup_updatefunction = \"".$template->get('admin_products_popup_createfunction')."\";");
    output($popup_updatefunction);
}
?>
