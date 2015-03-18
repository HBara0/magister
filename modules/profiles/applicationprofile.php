<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: applicationprofile.php
 * Created:        @hussein.barakat    Mar 12, 2015 | 9:49:20 AM
 * Last Update:    @hussein.barakat    Mar 12, 2015 | 9:49:20 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}


if(!$core->input['action']) {
    $psaid = $db->escape_string($core->input['id']);
    $segmapplication_obj = new SegmentApplications($psaid);
    $application['title'] = $segmapplication_obj->get_displayname();
    $safids = array();
    $eptids = array();
    $application['prodsegtitle'] = '<a href="index.php?module=profiles/segmentprofile&id='.$segmapplication_obj->get_segment()->psid.'" target="_blank"><h2>'.$segmapplication_obj->get_segment()->get_displayname().'</h2></a>';
    //Functional properties block---Start
    $segapfunctions = SegApplicationFunctions::get_data_byattr('psaid', $psaid);
    if(is_object($segapfunctions)) {
        $segapfunctions = array($segapfunctions);
    }
    if(is_array($segapfunctions)) {
        foreach($segapfunctions as $segapfunction) {
            if(is_object($segapfunction)) {
                $safids[] = $segapfunction->safid;
                $application_funtionalproperties.='<tr><td>'.$segapfunction->get_function()->get_displayname().'</td></tr>';
            }
        }
    }
    //Functional properties block---End
    //looping through all collected SAFIDs
    // foreach(c as $safid) {
    //products block ---Start
    $chemicalprodfunction_objs = ChemFunctionProducts::get_data(array('safid' => $safids), array('returnarray' => true));
//        if(is_object($chemicalprodfunction_objs)) {
//            $chemicalprodfunction_objs = array($chemicalprodfunction_objs);
//        }
    if(is_array($chemicalprodfunction_objs)) {
        foreach($chemicalprodfunction_objs as $chemicalprodfunction_obj) {
            $application_productdetails.='<tr>';

            $product_obj = $chemicalprodfunction_obj->get_produt();
            $supplier = $product_obj->get_supplier();
            $objectids['spid'][] = $supplier->get_id();

            $application_productdetails .= '<td>'.$product_obj->get_displayname().'</td>';
            $application_productdetails.='<td>'.$supplier->parse_link().'</td>';
            $application_productdetails.='</tr>';
        }
    }

    $suppliers = Entities::get_data(array('eid' => $objectids['spid']), array('returnarray' => true));
    if(is_object($suppliers)) {
        $suppliers = array($suppliers);
    }
    if(is_array($suppliers)) {
        foreach($suppliers as $supplier) {
            $supplier_output.='<tr>';
            $supplier_output.='<td>'.$supplier->parse_link().'</td><td>'.$supplier->get_country()->get_displayname().'</td>';
            $supplier_output .= '</tr>';
        }
    }
    //products block ---End
    //Chemical Substances block---Start
    $chemicalfunctchems = ChemFunctionChemicals::get_data(array('safid' => $safids), array('returnarray' => true));
    if(is_object($chemicalfunctchems)) {
        $chemicalfunctchems = array($chemicalfunctchems);
    }
    if(is_array($chemicalfunctchems)) {
        foreach($chemicalfunctchems as $chemicalfunctchem) {
            $chemicalsubstancedetails .='<tr>';
            $chemicalsubst = $chemicalfunctchem->get_chemicalsubstance()->get_displayname();
            $chemicalsubstancedetails .= '<td>'.$chemicalsubst.'</td>';
            $chemicalsubstancedetails .= '</tr>';
        }
    }
    //Chemical Substances block---End
    // }//loop over SAFIDs end
//End product type---Start
    $endproducttype_objs = EndProducTypes::get_data_byattr('psaid', $psaid);
    if(is_object($endproducttype_objs)) {
        $endproducttype_objs = array($endproducttype_objs);
    }
    if(is_array($endproducttype_objs)) {
        foreach($endproducttype_objs as $endproducttype_obj) {
            $eptids[] = $endproducttype_obj->get_primarykey();
            $endproducttype_name = $endproducttype_obj->get_displayname();
            $endproducttype_output.='<tr><td>'.$endproducttype_name.'</td></tr>';
        }
    }
    //End product type---End
    //looping through all eptids collected in end product type
    foreach($eptids as $eptid) {
        //Entity Brand block---Start
        $entitybrandproduct_objs = EntBrandsProducts::get_data_byattr('eptid', $eptid);
        if(is_object($entitybrandproduct_objs)) {
            $entitybrandproduct_objs = array($entitybrandproduct_objs);
        }
        if(is_array($entitybrandproduct_objs)) {
            foreach($entitybrandproduct_objs as $entitybrandproduct_obj) {
                $entitybrand_objs = $entitybrandproduct_obj->get_entitybrand();
                if(is_object($entitybrand_objs)) {
                    $entitybrand_objs = array($entitybrand_objs);
                }
                if(is_array($entitybrand_objs)) {
                    foreach($entitybrand_objs as $entitybrand_obj) {
                        $brandlist.='<tr>';
                        $entitie_obj = $entitybrand_obj->get_entity();
                        $brandname = $entitybrand_obj->get_displayname();
                        $entity_country = $entitie_obj->get_country()->get_displayname();
                        $brandlist .= '<td>'.$brandname.'</td><td>'.$entitie_obj->parse_link().'</td><td>'.$entity_country.'</td>';
                        $brandlist.='</tr>';
                    }
                }
            }
        }//Entity Brand block---End
    }//end of loop through eptids

    eval("\$application_brand_list .= \"".$template->get('profiles_applicationprofile_brand')."\";");
    eval("\$application_supplier_list .= \"".$template->get('profiles_applicationprofile_supplier')."\";");
    eval("\$application_endproducttype_list .= \"".$template->get('profiles_applicationprofile_endproducttype')."\";");
    eval("\$application_chemicalsubst_list .= \"".$template->get('profiles_applicationprofile_chemicalsubstance')."\";");
    eval("\$application_funtionalproperties_list .= \"".$template->get('profiles_applicationprofile_functionalproperty')."\";");
    eval("\$application_products_list .= \"".$template->get('profiles_applicationprofile_product')."\";");
    eval("\$applicationprofilepage = \"".$template->get('profiles_applicationprofile')."\";");
    output_page($applicationprofilepage);
}


