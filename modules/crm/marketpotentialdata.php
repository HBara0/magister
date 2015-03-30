<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: marketpotentialdata.php
 * Created:        @hussein.barakat    Mar 24, 2015 | 2:44:55 PM
 * Last Update:    @hussein.barakat    Mar 24, 2015 | 2:44:55 PM
 */
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['profiles_canUseMktIntel']==0) {
 //error($lang->sectionnopermission);
}
if(!$core->input['action']) {
    $marketintel_objs = MarketIntelligence::get_marketdata();
    foreach($marketintel_objs as $marketintel_obj) {
        $mibdid = $marketintel_obj->mibdid;
        $affid = isAllowed($core, 'canViewAllAff', 'affiliates', $marketintel_obj->get_affiliate()->affid);
        if($affid == false) {
            continue;
        }
        $marketintel['aff'] = $marketintel_obj->get_affiliate()->get_displayname();
        $custid = isAllowed($core, 'canViewAllCust', 'customers', $marketintel_obj->get_customer()->eid);
        if($custid == false) {
            continue;
        }
        $cust = $marketintel_obj->get_customer();
        $marketintel['customer'] = $cust->get_displayname();
        $marketintel['country'] = $cust->get_country()->get_displayname();
        if($marketintel_obj->cfpid != 0) {
            $prod = $marketintel_obj->get_chemfunctionproducts()->get_produt();
            $marketintel['product'] = $prod->get_displayname();
            $supid = isAllowed($core, 'canViewAllSupp', 'suppliers', $prod->get_supplier()->eid);
            if($supid == false) {
                $marketintel['supplier'] = '-';
            }
            else {
                $marketintel['supplier'] = $prod->get_supplier()->get_displayname();
            }
        }
        else {
            $marketintel['product'] = '-';
            $marketintel['supplier'] = '-';
        }
        if($marketintel_obj->cfcid != 0) {
            $chemfunchem = $marketintel_obj->get_chemfunctionschemcials();
            $chemsub = $chemfunchem->get_chemicalsubstance();
            if(!is_object($chemsub)) {
                $marketintel['chemic'] = '-';
                $marketintel['functprop'] = '-';
                $marketintel['application'] = '-';
                $marketintel['segment'] = '-';
            }
            else {
                $marketintel['chemic'] = $chemsub->get_displayname();
                if($chemfunchem->safid != 0) {
                    $segapfunct = $chemfunchem->get_segapplicationfunction();
                    $marketintel['functprop'] = $segapfunct->get_function()->get_displayname();
                    $application = $segapfunct->get_application();
                    $marketintel['application'] = $application->get_displayname();
                    $marketintel['segment'] = $application->get_segment()->get_displayname();
                }
                else {
                    $marketintel['functprop'] = '-';
                    $marketintel['application'] = '-';
                    $marketintel['segment'] = '-';
                }
            }
        }
        else {
            $marketintel['chemic'] = '-';
            $marketintel['functprop'] = '-';
            $marketintel['application'] = '-';
            $marketintel['segment'] = '-';
        }
        if($marketintel_obj->ebpid != 0) {
            $marketintel['brand'] = $marketintel_obj->get_entitiesbrandsproducts()->get_entitybrand()->get_displayname();
        }
        else {
            $marketintel['brand'] = '-';
        }
        $marketintel['potqty'] = $marketintel_obj->potential;

        $marketintel['marketshare'] = $marketintel_obj->mktShareQty;
        $marketintel['price'] = $marketintel_obj->unitPrice;
        if($marketintel_obj->eptid != 0) {
            $marketintel['endprod'] = $marketintel_obj->get_endproducttype()->get_displayname();
        }
        else {
            $marketintel['endprod'] = '-';
        }
        eval("\$marketpotdata_list .= \"".$template->get('crm_marketpotentialdata_rows')."\";");
        unset($marketintel);
    }
    if($core->usergroup['profiles_canAddMkIntlData'] == 1) {
        $midata = new MarketIntelligence($mibdid);
        $addmarketdata_link = '<div style="float: right;" title="'.$lang->addmarketdata.'"><a href="#popup_profilesmarketdata" id="showpopup_profilesmarketdata" class="showpopup"><img alt="Add Market" src="'.$core->settings['rootdir'].'/images/icons/edit.gif" /></a></div>';
        $array_data = array('module' => 'profiles', 'elemtentid' => $affid, 'fieldlabel' => $lang->product, 'action' => 'do_addmartkerdata', 'modulefile' => 'entityprofile');
        eval("\$profiles_entityprofile_micustomerentry = \"".$template->get('profiles_micustomerentry')."\";");
        $module = 'crm';
        $action = 'do_addmartkerdata';
        $modulefile = 'marketpotentialdata';
        $css['display']['chemsubfield'] = 'none';
        $entitiesbrandsproducts_list = $lang->na;
        /* Filter by segments which the entity works in */
        $productypes_objs = EndProducTypes::get_endproductypes();
        if(is_array($productypes_objs)) {
            foreach($productypes_objs as $productype) {
                $endproducttypes_list .= '<option value="'.$productype->eptid.'">'.$productype->title.' - '.$productype->get_application()->title.'</option>';
            }
        }
        else {
            $endproducttypes_list = '<option value="0">'.$lang->na.'</option>';
        }
        eval("\$profiles_michemfuncproductentry = \"".$template->get('profiles_michemfuncsubstancentry')."\";");
        eval("\$profiles_minproductentry = \"".$template->get('profiles_michemfuncproductentry')."\";");
        eval("\$popup_marketdata= \"".$template->get('popup_profiles_marketdata')."\";");
        eval("\$popup_createbrand = \"".$template->get('popup_createbrand')."\";");
        eval("\$mkintl_section = \"".$template->get('profiles_mktintelsection')."\";");
    }

    eval("\$marketpotentialdata = \"".$template->get('crm_marketpotentialdata')."\";");
    output_page($marketpotentialdata);
}
else {
    if($core->input['action'] == 'do_addmartkerdata') {
        if($core->usergroup['profiles_canAddMkIntlData'] == 0) {
            exit;
        }
        $marketin_obj = new MarketIntelligence();
        $marketin_obj->create($core->input['marketdata']);
        switch($marketin_obj->get_errorcode()) {
            case 0:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 1:
                output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
                break;
            case 2:
            default:
                output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
                break;
        }
        exit;
    }
    else if($core->input['action'] == 'do_addbrand') {
        if($core->usergroup['profiles_canAddMkIntlData'] == 0) {
            exit;
        }

        $entitybrand_obj = new EntitiesBrands();
        $entitybrand_obj->create($core->input['entitybrand']);
        switch($entitybrand_obj->get_errorcode()) {
            case 0:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 1:
                output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
                break;
            case 2:
                output_xml('<status>false</status><message>eeee'.$lang->itemalreadyexist.'</message>');
                break;
        }
    }
       elseif($core->input['action'] == 'get_updatemktintldtls') {
        if($core->usergroup['profiles_canAddMkIntlData'] == 0) {
            exit;
        }
        $midata = new MarketIntelligence($core->input['id']);
        $customer = $midata->get_customer();
        $brandsproducts = $customer->get_brandsproducts();
        $output = '';
        if(is_array($brandsproducts)) {
            foreach($brandsproducts as $brandproduct) {
                $brandproduct_brand = $brandproduct->get_entitybrand();
                $brandproduct_productype = $brandproduct->get_endproduct();
                $options[$brandproduct->ebpid] = $brandproduct_brand->name;
                if(!is_object($brandproduct_productype)) {
                    $brandproduct_productype = new EntBrandsProducts();
                    $brandproduct_productype->title = $lang->unspecified;
                }
                else {
                    $options[$brandproduct->ebpid] .= ' - '.$brandproduct_productype->title;
                }
                eval("\$brandsendproducts .= \"".$template->get('profiles_entityprofile_brandsproducts')."\";");
            }

            $entitiesbrandsproducts_list = parse_selectlist('marketdata[ebpid]', 7, $options, $midata->ebpid);
        }

        $endproducttypes = EndProducTypes::get_endproductypes();
        if(is_array($endproducttypes)) {
            foreach($endproducttypes as $endproducttype) {
                $endproducttypes_list .= '<option value="'.$endproducttype->eptid.'">'.$endproducttype->title.' - '.$endproducttype->get_application()->title.'</option>';
            }
        }
        unset($endproducttypes);

        $chemfuncchemical = $midata->get_chemfunctionschemcials();
        if(is_object($chemfuncchemical)) {
            $chemsubstance = $chemfuncchemical->get_chemicalsubstance();
            $css['display']['chemsubfield'] = 'block';
            eval("\$profiles_michemfuncproductentry = \"".$template->get('profiles_michemfuncsubstancentry')."\";");
        }

        $chemfuncproduct = $midata->get_chemfunctionproducts();
        if(is_object($chemfuncproduct)) {
            $product = $chemfuncproduct->get_produt();
            eval("\$profiles_minproductentry= \"".$template->get('profiles_michemfuncproductentry')."\";");
        }

        list($module, $modulefile) = explode('/', $core->input['module']);
        $elementname = 'marketdata[cid]';
        $action = 'do_addmartkerdata';
        $elemtentid = $customer->get_eid();

        $packaging_list = parse_selectlist('marketdata[competitor]['.$rowid.'][packaging]', 7, Packaging::get_data('name IS NOT NULL'), '', '', '', array('blankstart' => 1));
        $incoterms_list = parse_selectlist('marketdata[competitor]['.$rowid.'][incoterms]', 8, Incoterms::get_data('titleAbbr IS NOT NULL'), '', '', '', array('blankstart' => 1));
        $saletype_list = parse_selectlist('marketdata[competitor]['.$rowid.'][saletype]', 8, SaleTypes::get_data('stid IN (1,4)'), '', '', '', array('blankstart' => 1));
        $samplacquire = parse_radiobutton('marketdata[competitor]['.$rowid.'][isSampleacquire]', array(1 => 'yes', 0 => 'no'), '', true);
        /* parse incoterms and packaging */

        eval("\$popup_marketdata = \"".$template->get('popup_profiles_marketdata')."\";");
        output($popup_marketdata);
    }
}
//function to check if user is allowed to see the affiliates/customers/suppliers
function isAllowed($core, $fieldusergroupname, $fieldname, $fieldid) {
    if($core->usergroup[$fieldusergroupname] == 1) {
        return $fieldid;
    }
    else {
        if(in_array($fieldid, $core->user[$fieldname])) {
            return $fieldid;
        }
        else {
            return false;
        }
    }
}

?>