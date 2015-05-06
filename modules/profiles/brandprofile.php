<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: brandprofile.php
 * Created:        @hussein.barakat    May 5, 2015 | 10:25:09 AM
 * Last Update:    @hussein.barakat    May 5, 2015 | 10:25:09 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    if($core->input['ebid'] && !empty($core->input['ebid'])) {
        $ebid = $db->escape_string($core->input['ebid']);
        $brand_obj = new EntitiesBrands($ebid);
        $page_title = $page_title_header = $brand_obj->get_displayname();
        $customer = $brand_obj->get_entity();
        if(is_object($customer)) {
            $customername = $customer->parse_link();
        }
        $endproducts_objs = $brand_obj->get_producttypes();
        if(is_array($endproducts_objs)) {
            $itemscount['endproducts'] = 0;
            foreach($endproducts_objs as $endproducts_obj) {
                $endproduct_rows.='<tr><td>'.$endproducts_obj->parse_link().'</td></tr>';
                $itemscount['endproducts'] ++;
            }
        }
        $entitybrandproduct_objs = EntBrandsProducts::get_data(array('ebid' => $brand_obj->ebid), array('returnarray' => true));
        if(is_array($entitybrandproduct_objs)) {
            foreach($entitybrandproduct_objs as $entitybrandproduct_obj) {
                $marketintel_objs = MarketIntelligence::get_marketdata_dal(array('ebpid' => $entitybrandproduct_obj->ebpid), array('simple' => false));
                if(is_array($marketintel_objs)) {
                    foreach($marketintel_objs as $marketintel_obj) {
                        $cfc_ids[] = $marketintel_obj->cfcid;
                    }
                }
            }
        }
        if(is_array($cfc_ids) && !empty($cfc_ids)) {
            $cfc_ids = array_unique($cfc_ids);
            $zero_cfc = array_search('0', $cfc_ids);
            if(isset($zero_cfc) && $zero_cfc != FALSE) {
                unset($cfc_ids[$zero_cfc]);
            }
            $itemscount['chemicals'] = 0;
            foreach($cfc_ids as $cfc_id) {
                $chemfuncobj = new ChemFunctionChemicals($cfc_id);
                if($chemfuncobj->cfcid == NULL) {
                    continue;
                }
                $itemscount['chemicals'] ++;
                $chemfuncobj = $chemicalsubstances_rows.='<tr><td>'.$chemfuncobj->get_chemicalsubstance()->parse_link().'</td></tr>';
            }
        }
        eval("\$chemsubstance_list = \"".$template->get('profiles_brands_chemicalsubstanceslist')."\";");
        eval("\$endproducts_list = \"".$template->get('profiles_brand_endproductslist')."\";");
        eval("\$brandsprofile = \"".$template->get('profiles_brand')."\";");
        output_page($brandsprofile);
    }
    if($core->input['ebpid'] && !empty($core->input['ebpid'])) {
        $ebpid = $db->escape_string($core->input['ebpid']);
        $entbrandprod_obj = new EntBrandsProducts($ebpid);
        $endproduct_type = $entbrandprod_obj->get_endproduct();
        $entitybrand = $entbrandprod_obj->get_entitybrand();
        $page_title_header = $entitybrand->parse_link().'/'.$endproduct_type->parse_link();
        $page_title = $entitybrand->get_displayname().$endproduct_type->get_displayname();
        $customer = $entitybrand->get_entity();
        if(is_object($customer)) {
            $customername = $customer->parse_link();
        }
        $marketintel_objs = MarketIntelligence::get_marketdata_dal(array('ebpid' => $ebpid), array('simple' => false));
        if(is_array($marketintel_objs)) {
            foreach($marketintel_objs as $marketintel_obj) {
                $cfc_ids[] = $marketintel_obj->cfcid;
                $cfp_ids[] = $marketintel_obj->cfpid;
                $ing_ids[] = $marketintel_obj->get_basicingredients();
            }
        }
        if(is_array($cfc_ids) && !empty($cfc_ids)) {
            $cfc_ids = array_unique($cfc_ids);
            $zero_cfc = array_search('0', $cfc_ids);
            if(isset($zero_cfc) && $zero_cfc != FALSE) {
                unset($cfc_ids[$zero_cfc]);
            }
            $itemscount['chemicals'] = 0;
            foreach($cfc_ids as $cfc_id) {
                $chemfuncobj = new ChemFunctionChemicals($cfc_id);
                if($chemfuncobj->cfcid == NULL) {
                    continue;
                }
                $itemscount['chemicals'] ++;
                $chemfuncobj = $chemicalsubstances_rows.='<tr><td>'.$chemfuncobj->get_chemicalsubstance()->parse_link().'</td></tr>';
            }
        }
        if(is_array($cfp_ids) && !empty($cfp_ids)) {
            $itemscount['products'] = 0;
            $cfp_ids = array_unique($cfp_ids);
            $zero_cfp = array_search('0', $cfp_ids);
            if(isset($zero_cfp) && $zero_cfp != FALSE) {
                unset($cfp_ids[$zero_cfp]);
            }
            foreach($cfp_ids as $cfp_id) {
                $chemfuncprod = new ChemFunctionProducts($cfp_id);
                if($chemfuncprod->cfpid == NULL) {
                    continue;
                }
                $itemscount['products'] ++;
                $products_rows.='<tr><td>'.$chemfuncprod->get_produt()->parse_link().'</td></tr>';
            }
        }
        if(is_array($ing_ids) && !empty($ing_ids)) {
            $ing_ids = array_unique($ing_ids);
            $zero_ing = array_search('0', $ing_ids);
            if(isset($zero_ing) && $zero_ing != FALSE) {
                unset($ing_ids[$zero_ing]);
            }
            $itemscount['ingre'] = 0;
            foreach($ing_ids as $ing_id) {
                $ingredient = new BasicIngredients($ing_id);
                if($ingredient->biid == NULL) {
                    continue;
                }
                $itemscount['ingre'] ++;
                $ingredients_rows.='<tr><td>'.$ingredient->get_displayname().'</td></tr>';
            }
        }
        eval("\$products_list = \"".$template->get('profiles_brands_productslist')."\";");
        eval("\$ingredients_list = \"".$template->get('profiles_brands_ingredientslist')."\";");
        eval("\$chemsubstance_list = \"".$template->get('profiles_brands_chemicalsubstanceslist')."\";");
        eval("\$brandsprofile = \"".$template->get('profiles_brand')."\";");
        output_page($brandsprofile);
    }
}
?>