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
        else {
            $endproduct_rows = 'NA';
        }
        $entitybrandproduct_objs = EntBrandsProducts::get_data(array('ebid' => $brand_obj->ebid), array('returnarray' => true));
        if(is_array($entitybrandproduct_objs)) {
            foreach($entitybrandproduct_objs as $entitybrandproduct_obj) {
                $marketintel_objs = MarketIntelligence::get_marketdata_dal(array('ebpid' => $entitybrandproduct_obj->ebpid), array('simple' => false));
                if(is_array($marketintel_objs)) {
                    foreach($marketintel_objs as $marketintel_obj) {
                        $cfc_ids[] = $marketintel_obj->cfcid;
                        $cfp_ids[] = $marketintel_obj->cfpid;
                        $ing_ids[] = $marketintel_obj->biid;
                    }
                }
            }
        }
        if(is_array($cfc_ids) && !empty($cfc_ids)) {
            $cfc_ids = array_unique($cfc_ids);
            $zero_cfc = array_search('0', $cfc_ids);
            if($zero_cfc !== FALSE) {
                unset($cfc_ids[$zero_cfc]);
            }
            $itemscount['chemicals'] = 0;
            foreach($cfc_ids as $cfc_id) {
                $chemfuncobj = new ChemFunctionChemicals($cfc_id);
                if($chemfuncobj->cfcid == NULL) {
                    continue;
                }
                $itemscount['chemicals'] ++;
                $chemicalsubstances_rows.='<tr><td>'.$chemfuncobj->get_chemicalsubstance()->parse_link().'</td></tr>';
            }
        }
        if(empty($chemicalsubstances_rows)) {
            $itemscount['chemicals'] = 0;
            $chemicalsubstances_rows = '<tr><td>N/A</td></tr>';
        }
        if(is_array($cfp_ids) && !empty($cfp_ids)) {
            $cfp_ids = array_unique($cfp_ids);
            $zero_cfp = array_search('0', $cfp_ids);
            if($zero_cfp !== FALSE) {
                unset($cfp_ids[$zero_cfc]);
            }
            $itemscount['products'] = 0;
            foreach($cfp_ids as $cfp_ids) {
                $chemfuncprod = new ChemFunctionProducts($cfp_ids);
                if($chemfuncprod->cfpid == NULL) {
                    continue;
                }
                $itemscount['products'] ++;
                $products_rows.='<tr><td>'.$chemfuncprod->get_produt()->parse_link().'</td></tr>';
            }
        }
        if(empty($products_rows)) {
            $itemscount['products'] = 0;
            $products_rows = '<tr><td>N/A</td></tr>';
        }
        if(is_array($ing_ids) && !empty($ing_ids)) {
            $ing_ids = array_unique($ing_ids);
            $zero_ing = array_search('0', $ing_ids);
            if($zero_cfc !== FALSE) {
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
        if(empty($ingredients_rows)) {
            $ingredients_rows = '<tr><td colspan="2">N/A</td></tr>';
            $itemscount['ingre'] = 0;
        }
        eval("\$chemsubstance_list = \"".$template->get('profiles_brands_chemicalsubstanceslist')."\";");
        eval("\$endproducts_list = \"".$template->get('profiles_brand_endproductslist')."\";");
        eval("\$products_list = \"".$template->get('profiles_brands_productslist')."\";");
        eval("\$ingredients_list = \"".$template->get('profiles_brands_ingredientslist')."\";");
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
                $ing_ids[] = $marketintel_obj->biid;
            }
        }
        if(is_array($cfc_ids) && !empty($cfc_ids)) {
            $cfc_ids = array_unique($cfc_ids);
            $zero_cfc = array_search('0', $cfc_ids);
            if(isset($zero_cfc)) { // && $zero_cfc == 0) {
                unset($cfc_ids[$zero_cfc]);
            }
            $itemscount['chemicals'] = 0;
            foreach($cfc_ids as $cfc_id) {
                $chemfuncobj = new ChemFunctionChemicals($cfc_id);
                if($chemfuncobj->cfcid == NULL) {
                    continue;
                }
                $chem = $chemfuncobj->get_chemicalsubstance();
                if($chem->csid == null) {
                    continue;
                }
                $itemscount['chemicals'] ++;
                $chemicalsubstances_rows.='<tr><td>'.$chem->parse_link().'</td></tr>';
                $chemfuncobj_clone .= '<tr><td><input type="checkbox" checked="checked" name="marketdata[cfcid][]" value="'.$chemfuncobj->cfcid.'">'.$chem->parse_link().'</td></tr>';
            }
        }
        if(!isset($chemfuncobj_clone) || empty($chemfuncobj_clone)) {
            $chemicalsubstances_rows = '<tr><td colspan="2">N/A</td></tr>';
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
                $product = $chemfuncprod->get_produt();
                $itemscount['products'] ++;
                $products_rows.='<tr><td>'.$product->parse_link().'</td></tr>';
                $products_clone.='<tr><td><input type="checkbox" checked="checked" name="marketdata[cfpid][]" value="'.$chemfuncprod->cfpid.'">'.$product->parse_link().'</td></tr>';
            }
        }
        if(!isset($products_clone) || empty($products_clone)) {
            $products_rows = '<tr><td colspan="2">N/A</td></tr>';
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
                $ingredients_clone.='<tr><td><input type="checkbox" checked="checked" name="marketdata[biid][]" value="'.$ingredient->biid.'">'.$ingredient->get_displayname().'</td></tr>';
            }
        }
        if(!isset($ingredients_clone) || empty($ingredients_clone)) {
            $ingredients_rows = '<tr><td colspan="2">N/A</td></tr>';
        }
        if($core->usergroup['canManageProducts'] == 1) {
            $mkdchem_rowid = $mkdprod_rowid = $mkdbing_rowid = 1;
            $clone_button = "<span> <a style='cursor: pointer;' class='showpopup' href='#' id='showpopup_clonebrandprod'><img src='".$core->settings['rootdir']."/images/addnew.png' title='".$lang->cloneentitybrand."' alt='Add' border='0'>".$lang->cloneentitybrand."</a> </span>";
            eval("\$pop_clone = \"".$template->get('popup_clonebrandprod')."\";");
        }
        eval("\$products_list = \"".$template->get('profiles_brands_productslist')."\";");
        eval("\$ingredients_list = \"".$template->get('profiles_brands_ingredientslist')."\";");
        eval("\$chemsubstance_list = \"".$template->get('profiles_brands_chemicalsubstanceslist')."\";");
        eval("\$brandsprofile = \"".$template->get('profiles_brand')."\";");
        output_page($brandsprofile);
    }
}
else {
    if($core->input['action'] == "do_clonebrand") {
        if(($core->input['brand'] == 0 || empty($core->input['brand'])) && !empty($core->input['newbrand'])) {
            $brand['name'] = $core->input['newbrand'];
            $brand['eid'] = $core->input['customer'];
            $cid = $core->input['customer'];
            $brand_obj = new EntitiesBrands();
            $brand_obj->set($brand);
            $brand_obj->save();
            switch($brand_obj->errorocode) {
                case 0:
                    $brandprod['ebid'] = $brand_obj->ebid;
                    break;
                case 1:
                    output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                    exit;
            }
        }
        else {
            $brandprod['ebid'] = $core->input['brand'];
            $brand_obj = new EntitiesBrands($core->input['brand']);
            $cid = $brand_obj->eid;
        }
        if(empty($brandprod['ebid']) || !isset($brandprod['ebid'])) {
            output_xml("<status>false</status><message>No Brand Selected</message>");
            exit;
        }
        $brandprod['eptid'] = $core->input['endproduct'];
        $brandprod_obj = EntBrandsProducts::get_data(array('eptid' => $brandprod['eptid'], 'ebid' => $brandprod['ebid']));
        if(is_object($brandprod_obj)) {
            $brandprod['ebpid'] = $brandprod_obj->ebpid;
        }
        $brandprod_obj = new EntBrandsProducts();
        $brandprod_obj->set($brandprod);
        $brandprod_obj->save();
        switch($brandprod_obj->errorocode) {
            case 0:
                $marketdata['ebpid'] = $brandprod_obj->ebpid;
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                exit;
        }
        if($marketdata['ebpid'] == 0 || empty($marketdata['ebpid'])) {
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
            exit;
        }
        $marketdata['potential'] = $marketdata['mktSharePerc'] = $marketdata['mktShareQty'] = $marketdata['unitPrice'] = 0;
//save midata with products
        if(verify($core->input['marketdata']['cfpid'])) {
            foreach($core->input['marketdata']['cfpid'] as $cfpid) {
                $marketdata['cfpid'][] = $cfpid;
            }
            $marketdata['cid'] = $cid;
            $marketdata['eptid'] = $core->input['endproduct'];
            $midata_obj = new MarketIntelligence();
            $midata_obj->create($marketdata);
            $errors[] = $midata_obj->get_errorcode();
        }
//  }
// }
//end savind midata with products
//save midata with chems
        unset($marketdata['cfpid']);
        if(verify($core->input['marketdata']['cfcid'])) {
            foreach($core->input['marketdata']['cfcid'] as $cfcid) {
                $marketdata['cfcid'][] = $cfcid;
            }
            $marketdata['cid'] = $cid;
            $marketdata['eptid'] = $core->input['endproduct'];
            $midata_obj = new MarketIntelligence();
            $midata_obj->create($marketdata);
            $errors[] = $midata_obj->get_errorcode();
        }
//end savind midata with chems
//save midata with ingr
        unset($marketdata['cfcid']);
        if(verify($core->input['marketdata']['biid'])) {
            foreach($core->input['marketdata']['biid'] as $biid) {
                $marketdata['biid'][] = $db->escape_string($biid);
            }
            $marketdata['cid'] = $cid;
            $marketdata['eptid'] = $core->input['endproduct'];
            $midata_obj = new MarketIntelligence();
            $midata_obj->create($marketdata);
            $errors[] = $midata_obj->get_errorcode();
        }
        if(is_array($errors)) {
            foreach($errors as $error) {
                if($error == 1) {
                    output_xml('<status>false</status><message>Error Saving One Of The Records</message>');
                    exit;
                }
            }
        }
        output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
    }
    elseif($core->input['action'] == 'ajaxaddmore_profmkdchemical') {
        $mkdchem_rowid = $db->escape_string($core->input['value']) + 1;
        eval("\$profiles_michemfuncproductentry_rows = \"".$template->get('profiles_michemfuncsubstancentry')."\";");
        echo $profiles_michemfuncproductentry_rows;
    }
    elseif($core->input['action'] == 'ajaxaddmore_profmkdbasicing') {
        $mkdbing_rowid = $db->escape_string($core->input['value']) + 1;
        eval("\$profiles_mibasicingredientsentry_rows = \"".$template->get('profiles_mibasicingredientsentry')."\";");
        echo $profiles_mibasicingredientsentry_rows;
    }
    elseif($core->input['action'] == 'ajaxaddmore_profmkdproduct') {
        $mkdprod_rowid = $db->escape_string($core->input['value']) + 1;
        eval("\$profiles_minproductentry_rows = \"".$template->get('profiles_michemfuncproductentry')."\";");
        echo $profiles_minproductentry_rows;
    }
}
function verify($array) {
    if(empty($array) || !is_array($array)) {
        return false;
    }
    return true;
}

?>