<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: endproducttypeprofile.php
 * Created:        @hussein.barakat    Mar 23, 2015 | 12:37:31 PM
 * Last Update:    @hussein.barakat    Mar 23, 2015 | 12:37:31 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    if(!isset($core->input['eptid'])) {
        redirect('index.php?module=profiles/segmentslist');
    }

    $eptid = intval($core->input['eptid']);
    $filter_where = 'eptid IN ('.$eptid.')';
    $endprodtype_obj = new EndProducTypes($eptid, false);
    $profile = $endprodtype_obj->get();
    $application_obj = $endprodtype_obj->get_application();
    $application = $application_obj->parse_link();
    $segment_obj = $application_obj->get_segment();
    if(is_object($segment_obj)) {
        $segment = $segment_obj->parse_link();
    }
    //start selecting all eptid in marketintelligence-basicdata
    $marketintel_objs = MarketIntelligence::get_marketdata_dal(array('eptid' => $eptid), array('simple' => false));
    //selecting all cfpids and cfcids in the marketintel objects
    if(is_array($marketintel_objs)) {
        foreach($marketintel_objs as $marketintel_obj) {
            $cfpids[] = $marketintel_obj->cfpid;
            $cfcids[] = $marketintel_obj->cfcid;
            $biids[] = $marketintel_obj->biid;
            $ebpids[] = $marketintel_obj->ebpid;
        }
        $cfpids = array_filter(array_unique($cfpids));
        if(!empty($cfpids)) {
            foreach($cfpids as $cfpid) {
                $chemfuncprod = new ChemFunctionProducts($cfpid);
                $product_obj = $chemfuncprod->get_produt();
                $pids[] = $product_obj->pid;
                $prodchemsubs = ProductsChemicalSubstances::get_data(array('pid' => $pid), array('returnarray' => true));
                if(!empty($prodchemsubs)) {
                    foreach($prodchemsubs as $prodchemsub) {
                        $chemids[] = $prodchemsub->csid;
                    }
                }
            }
        }
        if(!empty($pids)) {
            $pids = array_filter(array_unique($pids));
            foreach($pids as $pid) {
                $product_obj = new Products($pid);
                if(is_object($product_obj)) {
                    $product = $product_obj->parse_link();
                    $pid = $product_obj->pid;
                    eval("\$products_rows .= \"".$template->get('profiles_endproducttype_productlist_rows')."\";");
                }
            }
        }
        else {
            $product = 'N/A';
            eval("\$products_rows .= \"".$template->get('profiles_endproducttype_productlist_rows')."\";");
        }

        $biids = array_filter(array_unique($biids));
        if(!empty($biids)) {
            foreach($biids as $biid) {
                $basicingredient_obj = new BasicIngredients($biid);
                if(is_object($basicingredient_obj)) {
                    $basicingredient = $basicingredient_obj->get_displayname();
                }
                $biid = $basicingredien_obj->biid;
                eval("\$basicingredientss_rows .= \"".$template->get('profiles_endproducttype_basicingredientslist_rows')."\";");
            }
        }
        else {
            $basicingredient = 'N/A';
            eval("\$basicingredientss_rows .= \"".$template->get('profiles_endproducttype_basicingredientslist_rows')."\";");
        }

        $cfcids = array_filter(array_unique($cfcids));
        foreach($cfcids as $cfcid) {
            $chemfunchem = new ChemFunctionChemicals($cfcid);
            $chemids[] = $chemfunchem->get_chemicalsubstance()->csid;
        }
        if(!empty($chemids)) {
            $chemids = array_filter(array_unique($chemids));
            foreach($chemids as $chemid) {
                $chem = new Chemicalsubstances($chemid, false);
                $chemsubst = $chem->parse_link();
                eval("\$chemicalsubstances_rows .= \"".$template->get('profiles_endproducttype_chemicalsubstancestlist_rows')."\";");
            }
        }
        else {
            $chemsubst = 'N/A';
            eval("\$chemicalsubstances_rows .= \"".$template->get('profiles_endproducttype_chemicalsubstancestlist_rows')."\";");
        }
    }

    $ebpids = array_filter(array_unique($ebpids));
    if(!empty($ebpids)) {
        $itemscount['relatedbrands'] = 0;
        foreach($ebpids as $ebpid) {
            $entitybrandproduct = EntBrandsProducts::get_data(array('ebpid' => $ebpid));
            if(is_object($entitybrandproduct)) {
                $entitybrand = EntitiesBrands::get_data(array('ebid' => $entitybrandproduct->ebid));
                $entitybrand_link = $entitybrand->parse_link();
                if(is_object($entitybrand)) {
                    $entity = new Entities($entitybrand->eid);
                    if(is_object($entity)) {
                        $entity_link = $entity->parse_link();
                    }
                    eval("\$relatedbrands_rows .= \"".$template->get('profiles_endproducttype_relatedbrandslist_rows')."\";");
                    $itemscount['relatedbrands'] ++;
                }
            }
        }
    }
    else {
        $chemsubst = 'N/A';
        $itemscount['relatedbrands'] = 0;
        eval("\$relatedbrands_rows .= \"".$template->get('profiles_endproducttype_relatedbrandslist_rows')."\";");
    }



    eval("\$productslist = \"".$template->get('profiles_endproducttype_productslist')."\";");
    eval("\$chemsubstanceslist = \"".$template->get('profiles_endproducttype_chemicalsubstanceslist')."\";");
    eval("\$basicingredientlist = \"".$template->get('profiles_endproducttype_basicingredientslist')."\";");
    eval("\$relatedbrandslist = \"".$template->get('profiles_endproducttype_relatedbrandslist')."\";");
    eval("\$profilepage = \"".$template->get('profiles_endproducttype')."\";");
    output_page($profilepage);
}