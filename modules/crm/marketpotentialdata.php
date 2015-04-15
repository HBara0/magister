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
if($core->usergroup['profiles_canUseMktIntel'] == 0) {
    //error($lang->sectionnopermission);
}
if(!$core->input['action']) {
    $marketintel_objs = MarketIntelligence::get_marketdata_dal(null, array('order' => array('by' => 'createdOn', 'sort' => 'DESC'), 'simple' => false));

    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('affid', 'cid', 'coid', 'pid', 'supplier', 'csid', 'functionalproperty', 'application', 'segment', 'brand', 'eptid', 'potential', 'mktShareQty', 'unitPrice'),
                    'overwriteField' => array('application' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->application.'"/>',
                            'segment' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->segment.'"/>',
                            'functionalproperty' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->functionalproperty.'"/>',
                            'supplier' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->supplier.'"/>',
                            'csid' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->chemicalsubstance.'"/>',
                            'brand' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->brand.'"/>',
                            'eptid' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->endproducttype.'"/>'
                    )
            ),
            'process' => array(
                    'filterKey' => 'mibdid',
                    'mainTable' => array(
                            'name' => 'marketintelligence_basicdata',
                            'filters' => array('affid' => array('operatorType' => 'multiple', 'name' => 'affid'), 'cid' => array('operatorType' => 'equal', 'name' => 'cid'), 'eptid' => array('operatorType' => 'equal', 'name' => 'eptid'), 'potential' => 'potential', 'mktShareQty' => 'mktShareQty', 'unitPrice' => 'unitPrice'),
                    ),
                    'secTables' => array(
                            'entities' => array(
                                    'filters' => array('coid' => array('operatorType' => 'multiple', 'name' => 'country')), 'keyAttr' => 'eid', 'joinKeyAttr' => 'cid', 'joinWith' => 'marketintelligence_basicdata'
                            ),
                            'chemfunctionproducts' => array(
                                    'filters' => array('pid' => array('operatorType' => 'equal', 'name' => 'pid')), 'keyAttr' => 'cfpid', 'joinKeyAttr' => 'cfpid', 'joinWith' => 'marketintelligence_basicdata'
                            ),
                    )
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();

    $filters_row_display = 'hide';
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = ' '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));


    if(!empty($filter_where)) {
        $marketintel_objs = MarketIntelligence::get_marketdata_dal($filter_where, array('returnarray' => true, 'simple' => false));
    }
    /* Perform inline filtering - END */
    if(is_array(($marketintel_objs))) {
        foreach($marketintel_objs as $marketintel_obj) {
            $mibdid = $marketintel_obj->mibdid;
            $affid = isAllowed($core, 'canViewAllAff', 'affiliates', $marketintel_obj->get_affiliate()->affid);
            if($affid == false) {
                continue;
            }
            $marketintel['date'] = date($core->settings['dateformat'], $marketintel_obj->createdOn);
            $marketintel['aff'] = $marketintel_obj->get_affiliate()->get_displayname();
            $custid = isAllowed($core, 'canViewAllCust', 'customers', $marketintel_obj->get_customer()->eid);
            if($custid == false) {
                continue;
            }
            $cust = $marketintel_obj->get_customer();
            $marketintel['customer'] = $cust->parse_link();
            $marketintel['country'] = $cust->get_country()->get_displayname();
            if($marketintel_obj->cfpid != 0) {

                $chemfunctprod = new ChemFunctionProducts($marketintel_obj->cfpid);
                $prod = $chemfunctprod->get_produt();
                $marketintel['product'] = $chemfunctprod->get_produt()->get_displayname();
                $marketintel['application'] = $chemfunctprod->get_segmentapplication()->get_displayname();
                $marketintel['segment'] = $chemfunctprod->get_segment()->get_displayname();
                $marketintel['functprop'] = $chemfunctprod->get_segapplicationfunction()->get_function()->get_displayname();
                $supid = isAllowed($core, 'canViewAllSupp', 'suppliers', $prod->get_supplier()->eid);
                if($supid == false) {
                    $marketintel['supplier'] = '-';
                }
                else {
                    $marketintel['supplier'] = $prod->get_supplier()->get_displayname();
                }
                if($marketintel_obj->cfcid != 0) {
                    $chemfunchem = $marketintel_obj->get_chemfunctionschemcials();
                    $chemsub = $chemfunchem->get_chemicalsubstance();
                    if(!is_object($chemsub)) {
                        $marketintel['chemic'] = '-';
                    }
                    else {
                        $marketintel['chemic'] = $chemsub->get_displayname();
                    }
                }
                else {
                    $marketintel['chemic'] = '-';
                }
            }
            else {
                $marketintel['product'] = '-';
                $marketintel['supplier'] = '-';
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
            }
            if($marketintel_obj->ebpid != 0) {
                $marketintel['brand'] = $marketintel_obj->get_entitiesbrandsproducts()->get_entitybrand()->get_displayname();
            }
            else {
                $marketintel['brand'] = '-';
            }
            $marketintel['potqty'] = number_format($marketintel_obj->potential, 3);

            $marketintel['marketshare'] = number_format($marketintel_obj->mktShareQty, 3);
            $marketintel['price'] = number_format($marketintel_obj->unitPrice, 3);
            if($marketintel_obj->eptid != 0) {
                $marketintel['endprod'] = $marketintel_obj->get_endproducttype()->get_displayname();
            }
            else {
                $marketintel['endprod'] = '-';
            }
            eval("\$marketpotdata_list .= \"".$template->get('crm_marketpotentialdata_rows')."\";");
            unset($marketintel);
        }
    }
    else {
        $marketpotdata_list = '<tr><td colspan="6">'.$lang->na.'</td></tr>';
    }
    if($core->usergroup['profiles_canAddMkIntlData'] == 1) {
        $midata = new MarketIntelligence();
        $addmarketdata_link = '<div style="float: right;" title="'.$lang->addmarketdata.'"><a href="#popup_profilesmarketdata" id="showpopup_profilesmarketdata" class="showpopup"><img alt="Add Market" src="'.$core->settings['rootdir'].'/images/icons/edit.gif" /></a></div>';
        $array_data = array('module' => 'proiles', 'elemtentid' => $affid, 'fieldlabel' => $lang->product, 'action' => 'do_addmartkerdata', 'modulefile' => 'entityprofile');
        eval("\$profiles_entityprofile_micustomerentry = \"".$template->get('crm_marketpotentialdata_micustomerentry')."\";");
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
    elseif($core->input['action'] == 'get_mktintldetails') {
        if($core->usergroup['profiles_canAddMkIntlData'] == 0) {
            exit;
        }

        $mkintentry = new MarketIntelligence($core->input['id']);
        $round_fields = array('potential', 'mktSharePerc', 'mktShareQty', 'unitPrice');
        foreach($round_fields as $round_field) {
            $mkintentry->{$round_field} = round($mkintentry->{$round_field});
        }

        $mkintentry_customer = $mkintentry->get_customer();
        $mkintentry_brand = $mkintentry->get_entitiesbrandsproducts()->get_entitybrand();
        $mkintentry_endproducttype = $mkintentry->get_entitiesbrandsproducts()->get_endproduct();
        if(!is_object($mkintentry_endproducttype)) {
            $mkintentry_endproducttype = new EntBrandsProducts();
            $mkintentry_endproducttype->title = $lang->unspecified;
        }
        /* Parse competitors related market Data */
        $mrktcompetitor_objs = $mkintentry->get_competitors();
        if(is_array($mrktcompetitor_objs)) {
            foreach($mrktcompetitor_objs as $mrktcompetitor_obj) {
                $mrktintl_detials['competitors'] = $mrktcompetitor_obj->get();
                if(is_array($mrktintl_detials['competitors'])) {
                    $marketintelligencedetail_competitors = ' <div class="thead">'.$lang->competitor.'</div>';
                    $mrktintl_detials['competitors']['unitPrice'] = round($mrktintl_detials['competitors']['unitPrice']);

                    /* Get competitor suppliers objects */
                    $competitorsentities_objs = $mrktcompetitor_obj->get_entities();
                    if(is_array($competitorsentities_objs)) {
                        foreach($competitorsentities_objs as $competitorsentities_obj) {
                            $mrktintl_detials_competitorsuppliers .= '<li>'.$competitorsentities_obj->get()['companyName'].'</li>';
                        }
                    }
                    /* Get competitor suppliers prodcuts */
                    $competitorsproducts_objs = $mrktcompetitor_obj->get_products();
                    if(is_array($competitorsproducts_objs)) {
                        foreach($competitorsproducts_objs as $competitorsproducts_obj) {
                            $mrktintl_detials_competitorproducts.= '<li>'.$competitorsproducts_obj->get()['name'].'</li>';
                        }
                    }
                }
            }
            eval("\$marketintelligencedetail_competitors .= \"".$template->get('profiles_entityprofile_marketintelligence_competitors')."\";");
        }

        eval("\$marketintelligencedetail = \"".$template->get('popup_marketintelligencedetails')."\";");
        output($marketintelligencedetail);
    }
    elseif($core->input['action'] == 'get_entityendproduct') {
        if($core->usergroup['profiles_canAddMkIntlData'] == 0) {
            exit;
        }
        /* NOTICE
         * NEED WORK
         * Check if user has access to eid */
        $entity = new Entities($core->input['eid']);
        $brandsproducts = $entity->get_brandsproducts();
        $output = '';
        if(is_array($brandsproducts)) {
            foreach($brandsproducts as $brandproduct) {
                $options[$brandproduct->ebpid] = $brandproduct->get_entitybrand()->name;
                if(!empty($brandproduct->eptid)) {
                    $options[$brandproduct->ebpid] .= ' - '.$brandproduct->get_endproduct()->title;
                }
            }

            $output = parse_selectlist('marketdata[ebpid]', 7, $options, '');
        }
        else {
            $output = $lang->na;
        }
        output($output);
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