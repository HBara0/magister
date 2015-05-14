<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Add products
 * $module: contents
 * $id: addproducts.php
 * Last Update: @zaher.reda 	Mar 21, 2009 | 11:03 AM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canAddProducts'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$lang->load('contents_addproducts');
if(!$core->input['action']) {
    if($core->input['referrer'] != 'budgeting' || $core->usergroup['canUseBudgeting'] == 0) {
        /* Quick workaround to be replaced by request product form */
        $requiredinfo = array('Full Name (Trade)', 'Chemcial Name', 'Supplier', 'Producer', 'Segment', 'Application', 'Chemical function');
        $emailink = 'mailto:'.$core->settings['adminemail'].'&subject=[OCOS] New Product Addition Request&body='.implode(":%0D%0A", $requiredinfo);
        eval("\$addproductspage = \"".$template->get('contents_products_add_limited')."\";");
    }
    else {
        $generic_order = array(
                'by' => 'title',
                'sort' => 'ASC'
        );

        $generics = get_specificdata('genericproducts', '*', 'gpid', 'title', $generic_order);
        $generics_list = parse_selectlist('gpid', 3, $generics, '', '', '', array('required' => 'required', 'blankstart' => true));
        /* Parse all  segapplicationfunctions and get the associatives functions and segment  */
//        $segappfunc_objs = Segapplicationfunctions::get_segmentsapplicationsfunctions();
//        foreach($segappfunc_objs as $segappfunc_obj) {
//            $rowclass = alt_row($rowclass);
//            /* call the associatives objects */
//            $segmentapp_data['segappfuncs'] = $segappfunc_obj->get();
//            $segmentapp_data['chemicalfunction'] = $segappfunc_obj->get_function()->get();
//            $segmentapp_data['segment'] = $segappfunc_obj->get_segment()->get()['title'];
//            $segmentapp_data['application'] = $segappfunc_obj->get_application()->get()['title'];
//            eval("\$contents_products_add_segappfunc_rows .= \"".$template->get('contents_products_add_segappfunc_row')."\";");
//        }

        eval("\$addproductspage = \"".$template->get('contents_products_add')."\";");
    }
    output_page($addproductspage);
}
else {
    if($core->input['action'] == 'do_perform_addproducts') {
        if(empty($core->input['spid']) || empty($core->input['gpid']) || empty($core->input['name'])) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
        }

        if(value_exists('products', 'name', $core->input['name'])) {
            output_xml("<status>false</status><message>{$lang->productalreadyexists}</message>");
            exit;
        }
        unset($core->input['action'], $core->input['module'], $core->input['applicationfunction']);
        //Temporary hardcode
        $core->input['defaultCurrency'] = 'USD';

        $core->input['createdBy'] = $core->user['uid'];
        $core->input['createdOn'] = TIME_NOW;
        $query = $db->insert_query('products', $core->input);

        if($query) {
            $pid = $db->last_id();
            $entity = new Entities($core->input['spid']);
            $entity->auto_assignsegment($core->input['gpid']);
            /* insert chemical functions produts */
            if(isset($chemicalfunctionsproducts)) {
                foreach($chemicalfunctionsproducts as $chemicalfunctions) {
                    foreach($chemicalfunctions as $safid) {
                        $chemfunctionproducts_arary = array('pid' => $pid,
                                'safid' => $safid,
                                'createdBy' => $core->user['uid'],
                                'createdOn' => TIME_NOW
                        );
                        $chemfunctionquery = $db->insert_query("chemfunctionproducts", $chemfunctionproducts_arary);
                        /* In case a default function is not selected, system would automatically pick the first checked checkbox */
                        if(empty($core->input['defaultFunction']) && !isset($core->input['defaultFunction'])) {
                            $cfpid = array_shift(array_values($chemicalfunctions)); /* shift the array and get the first element value */
                        }
                        else if($chemfunctionquery && isset($core->input['defaultFunction'])) {
                            $cfpid = $db->last_id();
                        }
                        $db->update_query('products', array('defaultFunction' => $cfpid), 'pid='.$pid);
                    }
                }
            }
            $log->record($core->input['name']);



            $lang->productadded = $lang->sprint($lang->productadded, $core->input['name']);
            output_xml("<status>true</status><message>{$lang->productadded}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->erroraddingproduct}</message>");
        }
    }
}
?>