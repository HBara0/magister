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
                $ing_ids[] = $marketintel_obj->biid;
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
                $chem = $chemfuncobj->get_chemicalsubstance();
                $itemscount['chemicals'] ++;
                $chemicalsubstances_rows.='<tr><td>'.$chem->parse_link().'</td></tr>';
                $chemfuncobj_clone .= '<tr><td><input type="checkbox" checked="checked" name="chemicals['.$chem->csid.']" value="'.$chem->csid.'">'.$chem->parse_link().'</td></tr>';
            }
        }
        if(!isset($chemfuncobj_clone) || empty($chemfuncobj_clone)) {
            $chemfuncobj_clone = '<tr><td colspan="2">N/A</td></tr>';
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
                $products_clone.='<tr><td><input type="checkbox" checked="checked" name="products['.$product->pid.']" value="'.$product->pid.'">'.$product->parse_link().'</td></tr>';
            }
        }
        if(!isset($products_clone) || empty($products_clone)) {
            $products_clone = '<tr><td colspan="2">N/A</td></tr>';
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
                $ingredients_clone.='<tr><td><input type="checkbox" checked="checked" name="ingredients['.$ingredient->biid.']" value="'.$ingredient->biid.'">'.$ingredient->get_displayname().'</td></tr>';
            }
        }
        if(!isset($ingredients_clone) || empty($ingredients_clone)) {
            $ingredients_clone = '<tr><td colspan="2">N/A</td></tr>';
            $ingredients_rows = '<tr><td colspan="2">N/A</td></tr>';
        }
        if($core->usergroup['canManageProducts'] == 1) {
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
    if($core->input['action'] = "do_clonebrand") {
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
                $midata['ebpid'] = $brandprod_obj->ebpid;
                break;
            case 1:
                output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
                exit;
        }
        if($midata['ebpid'] == 0 || empty($midata['ebpid'])) {
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
            exit;
        }
        $midata['potential'] = $midata['mktSharePerc'] = $midata['mktShareQty'] = $midata['unitPrice'] = 0;
        //save midata with products
        if(verify($core->input['products'])) {
            foreach($core->input['products'] as $pid) {
                $product_obj = new Products($pid);
                $chemfuncprods = $product_obj->get_chemfunctionproducts();
                if(verify($chemfuncprods)) {
                    foreach($chemfuncprods as $cfpid => $obj) {
                        $midata['cfpid'] = $cfpid;
                        $midata['cid'] = $cid;
                        $midata['eptid'] = $core->input['endproduct'];
                        $midata_obj = new MarketIntelligence();
                        $midata_obj->create($midata);
                        $errors[] = $midata_obj->get_errorcode();
                    }
                }
            }
        }
        //end savind midata with products
        //save midata with chems
        if(verify($core->input['chemicals'])) {
            foreach($core->input['chemicals'] as $csid) {
                $chemsub_obj = new Chemicalsubstances($csid);
                $chemfuncchem = $chemsub_obj->get_chemfunctionchemicals();
                if(verify($chemfuncchem)) {
                    foreach($chemfuncchem as $cfcid => $obj) {
                        $midata['cfcid'] = $cfcid;
                        $midata['cid'] = $cid;
                        $midata['eptid'] = $core->input['endproduct'];
                        $midata_obj = new MarketIntelligence();
                        $midata_obj->create($midata);
                        $errors[] = $midata_obj->get_errorcode();
                        $errors[] = $midata_obj->errorcode;
                    }
                }
            }
        }
        //end savind midata with chems
        //save midata with ingr
        if(verify($core->input['ingredients'])) {
            foreach($core->input['ingredients'] as $biid) {
                $midata['biid'] = $biid;
                $midata['cid'] = $cid;
                $midata['eptid'] = $core->input['endproduct'];
                $midata_obj = new MarketIntelligence();
                $midata_obj->create($midata);
                $errors[] = $midata_obj->get_errorcode();
                $errors[] = $midata_obj->errorcode;
            }
        }
        //end savind midata with ingr
        if(verify($errors)) {
            foreach($errors as $error) {
                if($error) {
                    output_xml("<status>false</status><message>{$lang->errorsavingduring}</message>");
                    exit;
                }
            }
        }
        output_xml("<status>true</status><message>Successfully Saved</message>");
    }
}
function verify($array) {
    if(empty($array) || !is_array($array)) {
        return false;
    }
    return true;
}

?>