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
    $psaid = $core->input['id'];
    $segmapplication_obj = new SegmentApplications($core->input['id']);
    $application['title'] = $segmapplication_obj->get_displayname();
    $safids = array();
    $eptids = array();
    $application['prodsegtitle'] = '<a href="index.php?module=profiles/segmentprofile&id='.$segmapplication_obj->get_segment()->psid.'" target="_blank">'.$segmapplication_obj->get_segment()->get_displayname().'</a>';
    //Functional properties block---Start

    $segapfunctions = SegApplicationFunctions::get_data_byattr('psaid', $psaid);
    if(is_object($segapfunctions)) {
        $segapfunctions = array($segapfunctions);
    }
    if(is_array($segapfunctions)) {
        $itemscount['functions'] = count($segapfunctions);
        foreach($segapfunctions as $segapfunction) {
            if(is_object($segapfunction)) {
                $safids[] = $segapfunction->safid;
                $application_funtionalproperties .= '<tr><td>'.$segapfunction->get_function()->get_displayname().'</td></tr>';
            }
        }
    }
    //Functional properties block---End
    //looping through all collected SAFIDs
    // foreach(c as $safid) {
    //products block ---Start
    if(!empty($safids)) {
        $chemicalprodfunction_objs = ChemFunctionProducts::get_data(array('safid' => $safids), array('returnarray' => true));
        if(is_array($chemicalprodfunction_objs)) {
            $itemscount['products'] = count($chemicalprodfunction_objs);
            foreach($chemicalprodfunction_objs as $chemicalprodfunction_obj) {
                $application_productdetails .= '<tr>';

                $product_obj = $chemicalprodfunction_obj->get_produt();
                $supplier = $product_obj->get_supplier();
                $objectids['spid'][] = $supplier->get_id();

                $application_productdetails .= '<td>'.$product_obj->parse_link().'</td>';
                $application_productdetails .= '<td>'.$supplier->parse_link().'</td>';
                $application_productdetails .= '</tr>';
            }
        }
    }
    //produt block end
    //supplier block start
    if($core->usergroup['canViewAllSupp'] == 0) {
        if(is_array($core->user['suppliers']['eid'])) {
            $spid_allowed = array_intersect($objectids['spid'], $core->user['suppliers']['eid']);
        }
    }
    else {
        $spid_allowed = $objectids['spid'];
    }
    if(!empty($spid_allowed)) {
        $suppliers = Entities::get_data(array('eid' => $spid_allowed), array('returnarray' => true));
        if(is_array($suppliers)) {
            $itemscount['suppliers'] = count($suppliers);
            foreach($suppliers as $supplier) {
                $supplier_output .= '<tr>';
                $supplier_output .= '<td>'.$supplier->parse_link().'</td><td>'.$supplier->get_country()->get_displayname().'</td>';
                $supplier_output .= '</tr>';
            }
        }
    }
    //supplier block ---End
    //Chemical Substances block---Start
    if(!empty($safids)) {
        $chemicalfunctchems = ChemFunctionChemicals::get_data(array('safid' => $safids), array('returnarray' => true));
        if(is_array($chemicalfunctchems)) {
            $itemscount['chemsubstances'] = count($chemicalfunctchems);
            foreach($chemicalfunctchems as $chemicalfunctchem) {
                $chemicalsubstancedetails .= '<tr>';
                $chemicalsubst = $chemicalfunctchem->get_chemicalsubstance()->parse_link();
                $chemicalsubstancedetails .= '<td>'.$chemicalsubst.'</td>';
                $chemicalsubstancedetails .= '</tr>';
            }
        }
    }
    //Chemical Substances block---End
    // }//loop over SAFIDs end
//End product type---Start
    if(!empty($psaid)) {
        $endproducttype_objs = EndProducTypes::get_data(array('psaid' => $psaid), array('returnarray' => true));
        if(is_array($endproducttype_objs)) {
            $itemscount['endproducts'] = count($endproducttype_objs);
            foreach($endproducttype_objs as $endproducttype_obj) {
                $eptids[] = $endproducttype_obj->get_primarykey();
            }
        }
    }
    unset($endproducttype_obj);
    //End product type---End
    //looping through all eptids collected in end product type
    //Entity Brand block---Start
    if(!empty($eptids)) {
        $eptids = array_filter(array_unique($eptids));
        $entitybrandproduct_objs = EntBrandsProducts::get_data(array('eptid' => $eptids), array('returnarray' => true));
        foreach($eptids as $eptid) {
            $endproducttype_obj = new EndProducTypes($eptid);
            if(is_object($endproducttype_obj)) {
                $details = $endproducttype_obj->parse_link();
                $first_parent = $endproducttype_obj->get_parent();
                if(is_object($first_parent)) {
                    $details .= '--> '.$first_parent->get_displayname();
                    $secondpar_obj = $first_parent->get_parent();
                    if(is_object($secondpar_obj)) {
                        $details.='-->'.$secondpar_obj->get_displayname();
                        $third_par = $secondpar_obj->get_parent();
                        if(is_object($third_par)) {
                            $originalpar_obj = $third_par->get_mother();
                            if(is_object($originalpar_obj)) {
                                $details.='->.....->'.$originalpar_obj->get_displayname();
                            }
                        }
                    }
                }
            }
            $endproducttype_output .= '<tr><td>'.$details.'</td></tr>';
        }
        if(is_array($entitybrandproduct_objs)) {
            foreach($entitybrandproduct_objs as $entitybrandproduct_obj) {
                $eids[] = $entitybrandproduct_obj->get_entitybrand()->eid;
                $ebids[] = $entitybrandproduct_obj->get_entitybrand()->ebid;
            }

            $eids = array_filter(array_unique($eids));
            $allowed_eid = $eids;
            if($core->usergroup['canViewAllCust'] == 0) {
                $allowed_eid = array_intersect($eids, $core->user['customers']);
            }

            if(!empty($allowed_eid)) {
                $allowed_eid = '(eid IN ('.implode(',', $allowed_eid).') OR eid IN (SELECT eid FROM '.Tprefix.'entities WHERE type="pc" AND eid IN (eid IN ('.implode(',', $eids).'))))';
                $entitybrand_objs = EntitiesBrands::get_data(array('eid' => $allowed_eid, 'ebid' => $ebids), array('operators' => array('eid' => 'CUSTOMSQLSECURE'), 'returnarray' => true));
            }
        }

        if(is_array($entitybrand_objs)) {
            $itemscount['brands'] = count($entitybrand_objs);
            foreach($entitybrand_objs as $entitybrand_obj) {
                $brandlist.= '<tr>';
                $entitie_obj = $entitybrand_obj->get_entity();
                $brandlist .= '<td>'.$entitybrand_obj->parse_link().'</td><td>'.$entitie_obj->parse_link().'</td><td>'.strtoupper($entitie_obj->type).'</td><td>'.$entitie_obj->get_country()->get_displayname().'</td>';
                $brandlist .= '</tr>';
            }
        }
    }
//}//Entity Brand block---End

    eval("\$application_brand_list .= \"".$template->get('profiles_applicationprofile_brand')."\";");
    eval("\$application_supplier_list .= \"".$template->get('profiles_applicationprofile_supplier')."\";");
    eval("\$application_endproducttype_list .= \"".$template->get('profiles_applicationprofile_endproducttype')."\";");
    eval("\$application_chemicalsubst_list .= \"".$template->get('profiles_applicationprofile_chemicalsubstance')."\";");
    eval("\$application_funtionalproperties_list .= \"".$template->get('profiles_applicationprofile_functionalproperty')."\";");
    eval("\$application_products_list .= \"".$template->get('profiles_applicationprofile_product')."\";");
    eval("\$applicationprofilepage = \"".$template->get('profiles_applicationprofile')."\";");
    output_page($applicationprofilepage);
}
?>