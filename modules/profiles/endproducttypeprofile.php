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

    $eptid = $db->escape_string($core->input['eptid']);
    $filter_where = 'eptid IN ('.$eptid.')';
    $endprodtype_obj = new EndProducTypes($eptid, false);
    $profile = $endprodtype_obj->get();
    $application_obj = $endprodtype_obj->get_application();
    $application = $application_obj->get_displayname();
    $segment_obj = $application_obj->get_segment();
    if(is_object($segment_obj)) {
        $segment = $segment_obj->get_displayname();
    }
    //start selecting all eptid in marketintelligence-basicdata
    $marketintel_objs = MarketIntelligence::get_marketdata_dal(array('eptid' => $eptid), array(simple => false));
    //selecting all cfpids and cfcids in the marketintel objects
    if(is_array($marketintel_objs)) {
        foreach($marketintel_objs as $marketintel_obj) {
            $cfpids[] = $marketintel_obj->cfpid;
            $cfcids[] = $marketintel_obj->cfcid;
        }
        $cfpids = array_filter(array_unique($cfpids));
        if(!empty($cfpids)) {
            foreach($cfpids as $cfpid) {
                $chemfuncprod = new ChemFunctionProducts($cfpid);
                $product_obj = $chemfuncprod->get_produt();
                $product = $product_obj->get_displayname();
                $pid = $product_obj->pid;
                eval("\$products_rows .= \"".$template->get('profiles_endproducttype_productlist_rows')."\";");
                $prodchemsubs = ProductsChemicalSubstances::get_data(array('pid' => $pid), array('returnarray' => true));
                if(!empty($prodchemsubs)) {
                    foreach($prodchemsubs as $prodchemsub) {
                        $chemids[] = $prodchemsub->csid;
                    }
                }
            }
        }
        $cfcids = array_filter(array_unique($cfcids));
        foreach($cfcids as $cfcid) {
            $chemfunchem = new ChemFunctionChemicals($cfcid);
            $chemids[] = $chemfunchem->get_chemicalsubstance()->csid;
        }
        if(!empty($chemids)) {
            $chemids = array_filter(array_unique($chemids));
            foreach($chemids as $chemid) {
                $chem = new Chemicalsubstances($chemid);
                $chemsubst = $chem->get_displayname();
                eval("\$chemicalsubstances_rows .= \"".$template->get('profiles_endproducttype_chemicalsubstancestlist_rows')."\";");
            }
        }
    }

    eval("\$productslist = \"".$template->get('profiles_endproducttype_productslist')."\";");
    eval("\$chemsubstanceslist = \"".$template->get('profiles_endproducttype_chemicalsubstanceslist')."\";");
    eval("\$profilepage = \"".$template->get('profiles_endproducttype')."\";");
    output_page($profilepage);
}

