<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: chemicalsubstanceprofile.php
 * Created:        @hussein.barakat    May 5, 2015 | 3:15:39 PM
 * Last Update:    @hussein.barakat    May 5, 2015 | 3:15:39 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if(!$core->input['action']) {
    if($core->input['csid'] && !empty($core->input['csid'])) {
        $csid = $db->escape_string($core->input['csid']);
        $chemsub_obj = new Chemicalsubstances($csid, false);
        $chemsub = $chemsub_obj->get();
        if(empty($chemsub['casNum'])) {
            $chemsub['casNum'] = 'NA';
        }
        if(empty($chemsub['synonyms'])) {
            $chemsub['synonyms'] = 'NA';
        }
        /* Get sourcing supp chem-START */
        $sourcingsupps_objs = SourcingSuppliersChemicals::get_data(array('csid' => $csid), array('returnarray' => true));
        if(is_array($sourcingsupps_objs)) {
            foreach($sourcingsupps_objs as $sourcingsupps_obj) {
                $sourcingsupp_ids[] = $sourcingsupps_obj->ssid;
            }
        }
        /* Get sourcing supp chem-END */

        /* Get Chem Function Chem-START */
        $chemfuncchems = ChemFunctionChemicals::get_data(array('csid' => $csid), array('returnarray' => true));
        if(is_array($chemfuncchems)) {
            foreach($chemfuncchems as $chemfuncchem) {
                /* Get MI Data-START */
                $marktetintel_objs = MarketIntelligence::get_marketdata_dal(array('cfcid' => $chemfuncchem->cfcid));
                if(is_array($marktetintel_objs)) {
                    foreach($marktetintel_objs as $marktetintel_obj) {
                        $customer_ids[] = $marktetintel_obj->cid;
                        $entitybrandprod_objs = EntBrandsProducts::get_data(array('ebpid' => $marktetintel_obj->ebpid), array('returnarray' => true));
                        if(is_array($entitybrandprod_objs)) {
                            foreach($entitybrandprod_objs as $entitybrandprod_obj) {
                                $ebids[] = $entitybrandprod_obj->ebid;
                            }
                        }
                    }
                }
                /* Get MI Data-END */
                /* Get FunctionalProp-START */
                $segapfunctions_objs = SegApplicationFunctions::get_data(array('safid' => $chemfuncchem->safid), array('returnarray' => true));
                if(is_array($segapfunctions_objs)) {
                    foreach($segapfunctions_objs as $segapfunctions_obj) {
                        $segapfunct[$segapfunctions_obj->cfid] = $segapfunctions_obj->psaid;
                    }
                    /* Get FunctionalProp-END */
                }
            }
        }
        /* Get Chem Function Chem-END */
        /* Get products and suppliers-START */
        $prodchemsubs_objs = ProductsChemicalSubstances::get_data(array('csid' => $csid), array('returnarray' => true));
        if(is_array($prodchemsubs_objs)) {
            foreach($prodchemsubs_objs as $prodchemsubs_obj) {
                $product_ids[] = $prodchemsubs_obj->pid;
            }
        }
        /* Get products and suppliers-END */
        /* START PARSING */
        if(is_array($ebids)) {
            $itemscount['brandproducts'] = 0;
            $ebids = array_unique($ebids);
            foreach($ebids as $ebid) {
                $entbrand_obj = new EntitiesBrands($ebid);
                $brandproducts_rows.='<tr><td>'.$entbrand_obj->parse_link().'</td></tr>';
                $itemscount['brandproducts'] ++;
            }
        }
        else {
            $brandproducts_rows = '<tr><td>N/A</td></tr>';
            $itemscount['brandproducts'] = 0;
        }
        if(is_array($segapfunct)) {
            $itemscount['functprod'] = 0;
            foreach($segapfunct as $cfid => $psaid) {
                $function_obj = new ChemicalFunctions($cfid);
                $prodsegapp = new SegmentApplications($psaid);
                $apps[] = $prodsegapp;
                $functprod_rows.='<tr><td>'.$function_obj->get_displayname().'</td><td>'.$prodsegapp->parse_link().'</td><td>'.$prodsegapp->get_segment()->parse_link().'</td></tr>';
            }
            $itemscount['functprod'] ++;
        }
        else {
            $functprod_rows = '<tr><td>N/A</td></tr>';
            $itemscount['functprod'] = 0;
        }
        if(is_array($apps)) {
            $itemscount['relatedapplications'] = 0;
            $apps = array_unique($apps, SORT_REGULAR);
            foreach($apps as $app) {
                $relatedapplications_rows .= '<tr><td>'.$app->parse_link().'</td><td>'.$app->get_segment()->parse_link().'</td></tr>';
                $itemscount['relatedapplications'] ++;
            }
        }
        else {
            $relatedapplications_rows = '<tr><td>N/A</td></tr>';
            $itemscount['relatedapplications'] = 0;
        }
        if(is_array($product_ids)) {
            $itemscount['products'] = 0;
            $product_ids = array_unique($product_ids);
            foreach($product_ids as $product_id) {
                $product_obj = new Products($product_id);
                if(isset($product_obj->spid) && !empty($product_obj->spid)) {
                    $suppliers[] = $product_obj->spid;
                }
                $products_rows.='<tr><td>'.$product_obj->parse_link().'</td><td>'.$product_obj->get_supplier()->parse_link().'</td></tr>';
                $itemscount['products'] ++;
            }
        }
        else {
            $products_rows = '<tr><td>N/A</td></tr>';
            $itemscount['products'] = 0;
        }
        if(is_array($customer_ids)) {
            $itemscount['customers'] = 0;
            $customer_ids = array_unique($customer_ids);
            foreach($customer_ids as $customer_id) {
                $customer_obj = new Entities($customer_id);
                if($customer_obj->eid == 0) {
                    continue;
                }
                $customers_rows.='<tr><td>'.$customer_obj->parse_link().'</td><td>'.$customer_obj->get_country()->get_displayname().'</td><td>'.$customer_obj->get_type().'</td></tr>';
                $itemscount['customers'] ++;
            }
        }
        else {
            $customers_rows = '<tr><td>N/A</td></tr>';
            $itemscount['customers'] = 0;
        }
        if(is_array($suppliers)) {
            $suppliers = array_unique($suppliers);
            $itemscount['suppliers'] = 0;
            foreach($suppliers as $supplierid) {
                $supp = new Entities($supplierid);
                if($supp->contractExpiryDate > TIME_NOW) {
                    $agree = $core->settings['rootdir'].'images/true.gif';
                }
                else {
                    $agree = $core->settings['rootdir'].'/images/false.gif';
                }
                $suppliers_rows = '<tr><td>'.$supp->parse_link().'</td><td>'.$supp->get_country()->get_displayname().'</td><td>'.$supp->get_type().'</td><td><img src="'.$agree.'"></td></tr>';
                $itemscount['suppliers'] ++;
            }
        }
        else {
            $suppliers_rows = '<tr><td>N/A</td></tr>';
            $itemscount['suppliers'] = 0;
        }
        if(is_array($sourcingsupp_ids)) {
            $itemscount['possiblesuppliers'] = 0;
            $sourcingsupp_ids = array_unique($sourcingsupp_ids);
            foreach($sourcingsupp_ids as $sourcingsupp_id) {
                $sourcsupp_obj = new SourcingSuppliers($sourcingsupp_id, false);
                $possiblesuppliers_rows.='<tr><td>'.$sourcsupp_obj->get_displayname().'</td><td>'.$sourcsupp_obj->get_country()->get_displayname().'</td><td>'.$sourcsupp_obj->get_type().'</td></tr>';
                $itemscount['possiblesuppliers'] ++;
            }
        }
        else {
            $possiblesuppliers_rows = '<tr><td>N/A</td></tr>';
            $itemscount['possiblesuppliers'] = 0;
        }
        /* END PARSING */
        eval("\$funcprop_list = \"".$template->get('profiles_chemicalsubstance_functionproplist')."\";");
        eval("\$app_list = \"".$template->get('profiles_chemicalsubstance_relatedapplicationslist')."\";");
        eval("\$supplier_list = \"".$template->get('profiles_chemicalsubstance_supplierslist')."\";");
        eval("\$customer_list = \"".$template->get('profiles_chemicalfunctions_customerslist')."\";");
        eval("\$possible_supp_list = \"".$template->get('profiles_chemicalsubstance_possiblesupplist')."\";");
        eval("\$products_list = \"".$template->get('profiles_chemicalsubstances_productslist')."\";");
        eval("\$endproducts_list = \"".$template->get('profiles_chemicalsubstance_brandproductlist')."\";");
        eval("\$chemsubprof = \"".$template->get('profiles_chemicalsubstances')."\";");
        output_page($chemsubprof);
    }
}