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
    // error($lang->sectionnopermission);
}
if(!$core->input['action']) {
    $sort_url = sort_url();
    $filters_row_display = 'hide';
    $sort_query['sort'] = 'DESC';
    $sort_query['by'] = 'createdOn';
    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query['sort'] = $core->input['order'];
        $sort_query['by'] = $core->input['sortby'];
    }
    // $marketintel_objs = MarketIntelligence::get_marketdata_dal(null, array('simple' => false, 'order' => $sort_query));

    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('affid', 'cid', 'coid', 'pid', 'supplier', 'csid', 'biid', 'functionalproperty', 'application', 'segment', 'brand', 'icon', 'eptid', 'characteristic', 'potential', 'mktShareQty', 'unitPrice', 'date'),
                    'overwriteField' => array('application' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->application.'"/>',
                            'segment' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->segment.'"/>',
                            'functionalproperty' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->functionalproperty.'"/>',
                            'supplier' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->supplier.'"/>',
                            'csid' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->chemicalsubstance.'"/>',
                            'brand' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->brand.'"/>',
                            'eptid' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->endproducttype.'"/>',
                            'characteristic' => '<input class="inlinefilterfield" type="text" style="width: 95%" placeholder="'.$lang->characteristic.'"/>',
                            'icon' => ''
                    )
            ),
            'process' => array(
                    'filterKey' => 'mibdid',
                    'mainTable' => array(
                            'name' => 'marketintelligence_basicdata',
                            'filters' => array('affid' => array('operatorType' => 'multiple', 'name' => 'affid'), 'biid' => array('operatorType' => 'equal', 'name' => 'biid'), 'cid' => array('operatorType' => 'equal', 'name' => 'cid'), 'eptid' => array('operatorType' => 'equal', 'name' => 'eptid'), 'potential' => 'potential', 'mktShareQty' => 'mktShareQty', 'unitPrice' => 'unitPrice', 'date' => array('operatorType' => 'date', 'name' => 'createdOn')),
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
    $filter_where = null;
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = ' '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_where .= ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));


    //if(!empty($filter_where)) {
    $marketintel_objs = MarketIntelligence::get_marketdata_dal($filter_where, array('returnarray' => true, 'simple' => false, 'order' => $sort_query));
    // }
    /* Perform inline filtering - END */
    if(is_array(($marketintel_objs))) {
        $midata_unitPrice = $midata_mktShareQty = $midata_mktSharePerc = $midata_potential = 0;
        foreach($marketintel_objs as $marketintel_obj) {
            $mibdid = $marketintel_obj->mibdid;
            $affid = isAllowed($core, 'canViewAllAff', 'affiliates', $marketintel_obj->get_affiliate()->affid);
            if($affid == false) {
                continue;
            }
            $marketintel['date'] = date($core->settings['dateformat'], $marketintel_obj->createdOn);
            $marketintel['aff'] = $marketintel_obj->get_affiliate()->parse_link();
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
                $marketintel['product'] = $chemfunctprod->get_produt()->parse_link();
                $marketintel['application'] = $chemfunctprod->get_segmentapplication()->parse_link();
                $marketintel['segment'] = $chemfunctprod->get_segment()->parse_link();
                $marketintel['functprop'] = $chemfunctprod->get_segapplicationfunction()->get_function()->get_displayname();
                $supid = isAllowed($core, 'canViewAllSupp', 'suppliers', $prod->get_supplier()->eid);
                if($supid == false) {
                    $marketintel['supplier'] = '-';
                }
                else {
                    $marketintel['supplier'] = $prod->get_supplier()->parse_link();
                }
                if($marketintel_obj->cfcid != 0) {
                    $chemfunchem = $marketintel_obj->get_chemfunctionschemcials();
                    $chemsub = $chemfunchem->get_chemicalsubstance();
                    if(!is_object($chemsub)) {
                        $marketintel['chemic'] = '-';
                    }
                    else {
                        $marketintel['chemic'] = $chemsub->parse_link();
                    }
                }
                else {
                    $marketintel['chemic'] = '-';
                }
                $marketintel['basicing'] = '-';
            }
            else {
                $marketintel['product'] = '-';
                $marketintel['supplier'] = '-';
                $marketintel['basicing'] = '-';
                if($marketintel_obj->biid != 0) {
                    $basicingredient_obj = BasicIngredients::get_data(array('biid' => $marketintel_obj->biid));
                    if(is_object($basicingredient_obj)) {
                        $marketintel['basicing'] = $basicingredient_obj->get_displayname();
                    }
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
                        $marketintel['chemic'] = $chemsub->parse_link();
                        if($chemfunchem->safid != 0) {
                            $segapfunct = $chemfunchem->get_segapplicationfunction();
                            $marketintel['functprop'] = $segapfunct->get_function()->get_displayname();
                            $application = $segapfunct->get_application();
                            $marketintel['application'] = $application->parse_link();
                            $marketintel['segment'] = $application->get_segment()->parse_link();
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
                $ebrandprod_obj = $marketintel_obj->get_entitiesbrandsproducts();
                $marketintel['brand'] = '-';
                $marketintel['characteristic'] = '-';
                if(is_object($ebrandprod_obj) && !is_null($ebrandprod_obj->get())) {
                    $brand_obj = $ebrandprod_obj->get_entitybrand();
                    $marketintel['brand'] = $brand_obj->parse_link();
                    $brandid = $brand_obj->ebid;
                    $charac_obj = $ebrandprod_obj->get_charactersticvalue();
                    if(is_object($charac_obj)) {
                        $marketintel['characteristic'] = $charac_obj->get_displayname();
                    }
                }
            }
            else {
                $marketintel['brand'] = '-';
            }

            $marketintel['potqty'] = number_format($marketintel_obj->potential, 3);
            $marketintel['marketshare'] = number_format($marketintel_obj->mktShareQty, 3);
            $marketintel['price'] = number_format($marketintel_obj->unitPrice, 3);
            if($marketintel_obj->eptid != 0) {
                $endproduct = $marketintel_obj->get_endproducttype();
                $marketintel['endprod'] = $endproduct->parse_link();
                if($marketintel['segment'] == '-') {
                    $application = $endproduct->get_application();
                    $marketintel['application'] = $application->parse_link();
                    $marketintel['segment'] = $application->get_segment()->parse_link();
                }
            }
            else {
                $marketintel['endprod'] = '-';
            }
            if($brandid != 0 && isset($brandid) && $marketintel_obj->eptid != 0) {
                $entbrandprod_obj = EntBrandsProducts::get_data(array('eptid' => $marketintel_obj->eptid, 'ebid' => $brandid));
                if(is_object($ebrandprod_obj) && !is_null($ebrandprod_obj->get())) {
                    $brandendprod_link = $ebrandprod_obj->parse_link();
                }
            }
            if($marketintel_obj->createdBy == $core->user['uid']) {
                $deleteicon = "<a title=".$lang->deleteentry." id='deletemientry_".$mibdid."_".$core->input['module']."_loadpopupbyid' rel='mktdetail_".$mibdid."'><img src='".$core->settings[rootdir]."/images/invalid.gif' border='0' rel='mktdetail_".$mibdid."'/></a>";
            }
            eval("\$marketpotdata_list .= \"".$template->get('crm_marketpotentialdata_rows')."\";");
            unset($marketintel, $deleteicon, $brandid, $brandendprod_link);
        }
    }
    else {
        $marketpotdata_list = '<tr><td colspan="6">'.$lang->na.'</td></tr>';
    }
    if($core->usergroup['profiles_canAddMkIntlData'] == 1) {
        $midata = new MarketIntelligence();
        $rowid = 2;
        $addmarketdata_link = '<div style="float: right;"><a href="#popup_profilesmarketdata" id="showpopup_profilesmarketdata" class="showpopup"><button type="button">'.$lang->addmarketdata.'</button></a></div>';
        $array_data = array('module' => 'proiles', 'elemtentid' => $affid, 'fieldlabel' => $lang->product, 'action' => 'do_addmartkerdata', 'modulefile' => 'entityprofile');
        $packaging_list = parse_selectlist('marketdata[competitor]['.$rowid.'][packaging]', 7, Packaging::get_data('name IS NOT NULL'), '', '', '', array('blankstart' => 1));
        $saletype_list = parse_selectlist('marketdata[competitor]['.$rowid.'][saletype]', 8, SaleTypes::get_data('stid IN (1,4)'), '', '', '', array('blankstart' => 1));
        $samplacquire = parse_radiobutton('marketdata[competitor]['.$rowid.'][isSampleacquire]', array(1 => 'yes', 0 => 'no'), '', true);
        $brandprod_rowid = 0;
        $customer_rowid = 0;
        eval("\$profiles_entityprofile_micustomerentry = \"".$template->get('crm_marketpotentialdata_micustomerentry')."\";");
        $module = 'crm';
        $action = 'do_addmartkerdata';
        $modulefile = 'marketpotentialdata';
        $css['display']['chemsubfield'] = 'none';
        $css['display']['basicingsubfield'] = 'none';
        $css['display']['product'] = 'none';
        $entitiesbrandsproducts_list = $lang->na;
        /* Filter by segments which the entity works in */
        $productypes_objs = EndProducTypes::get_endproductypes();
        if(is_array($productypes_objs)) {
            foreach($productypes_objs as $productype) {
                $value = $productype->title;
                $pplication = $productype->get_application()->parse_link();
                if($pplication !== null) {
                    $value .= ' - '.$pplication;
                }
                $parent = $productype->get_endproducttype_chain();
                if(!empty($parent)) {
                    $values[$productype->eptid] = $parent.' > '.$value;
                }
                else {
                    $values[$productype->eptid] = $value;
                }
            }

            asort($values);
            $classification_classes = array('Class A', 'Class B', 'Class C');
            $classification_classes = array_combine($classification_classes, $classification_classes);

            foreach($values as $key => $value) {
                $checked = $rowclass = '';
                $endproducttypes_list .= ' <tr class="'.$rowclass.'">';
                $endproducttypes_list .= '<td><input id="producttypefilter_check_'.$key.'" type="checkbox"'.$checked.' value="'.$key.'" name="entitybrand[endproducttypes]['.$key.'][eptid]">'.$value.'<input style="float:right;" type="text" name="entitybrand[endproducttypes]['.$key.'][description]" placeholder="'.$lang->description.'"  value="'.$brandproduct[description].'"/></td>'
                        .'<td>'.parse_selectlist("entitybrand[endproducttypes][".$key."][classificationClass]", '', $classification_classes, '', '', '', array('blankstart' => true)).'</td></tr>';
            }
        }

        $mkdchem_rowid = 0;
        eval("\$profiles_michemfuncproductentry_row = \"".$template->get('profiles_michemfuncsubstancentry')."\";");
        eval("\$profiles_michemfuncproductentry = \"".$template->get('profiles_michemfuncsubstancentry_rows')."\";");

        $mkdprod_rowid = 0;
        eval("\$profiles_minproductentry_row = \"".$template->get('profiles_michemfuncproductentry')."\";");
        eval("\$profiles_minproductentry = \"".$template->get('profiles_michemfuncproductentry_rows')."\";");
        $profiles_mincustomervisit_title = $lang->visitreport;
        $profiles_mincustomervisit = parse_selectlist('marketdata[vrid]', 7, $visitreport_objs, '', '', '', array('blankstart' => 1));
        $mkdbing_rowid = 0;
        eval("\$profiles_mibasicingredientsentry_row = \"".$template->get('profiles_mibasicingredientsentry')."\";");
        eval("\$profiles_mibasicingredientsentry = \"".$template->get('profiles_mibasicingredientsentry_rows')."\";");
        eval("\$popup_marketdata= \"".$template->get('popup_profiles_marketdata')."\";");

        $characteristics = ProductCharacteristicValues::get_data(null, array('order' => array('by' => array(ProductCharacteristicValues::DISPLAY_NAME, 'pcid')), 'returnarray' => true));
        $characteristics_list = parse_selectlist('entitybrand[pcvid]', 4, $characteristics, null, 0, null, array('blankstart' => true));
        eval("\$popup_createbrand = \"".$template->get('popup_createbrand')."\";");
        eval("\$mkintl_section = \"".$template->get('profiles_mktintelsection')."\";");
    }

    eval("\$marketpotentialdata = \"".$template->get('crm_marketpotentialdata')."\";");
    output_page($marketpotentialdata);
}
else {
    if($core->input['action'] == 'do_addmartkerdata') {
        $css[display]['chemsubfield'] = 'none';
        $css[display]['basicingsubfield'] = 'none';
        $css[display]['product'] = 'none';
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
                output_xml('<status>false</status><message>'.$lang->entryexists.'</message>');
                break;
        }
    }
    elseif($core->input['action'] == 'get_updatemktintldtls') {
        $css[display]['radiobuttons'] = 'none';
        $mkdchem_rowid = $core->input['id'];
        $mkdbing_rowid = $core->input['id'];
        $mkdprod_rowid = $core->input['id'];

        if($core->usergroup['profiles_canAddMkIntlData'] == 0) {
            exit;
        }
        $brandprod_rowid = $core->input['id'];
        $midata = new MarketIntelligence($core->input['id']);
        $mimorerowsid = $midata->mibdid;
        $customer = $midata->get_customer();
        $brandsproducts = $customer->get_brandsproducts();
        $output = '';
        $main_attr = '';
        $basic_attrs = array('cfcid', 'cfpid', 'biid');
        foreach($basic_attrs as $attr) {
            $at = "'".$attr."'";
            if($midata->$attr > 0) {
                $main_attr = $attr;
            }
        }
        if(!isset($main_attr)) {
            exit;
        }
        $mainattr = $midata->$main_attr;
        $twelvemonths = 31536000;
        $notcurrent = 'mibdid != '.$midata->mibdid;
        $mi_pastobjs = MarketIntelligence::get_marketdata_dal(array('cid' => $mkintentry->cid, 'CUSTOMSQL' => $notcurrent, 'ebpid' => $mkintentry->ebpid, 'createdBy' => $core->user['uid'], $main_attr => $mainattr), array('simple' => false, 'operators' => array('CUSTOMSQL' => 'CUSTOMSQL'), 'order' => array('by' => 'createdOn', 'sort' => 'DESC')));
        if(is_array($mi_pastobjs)) {
            foreach($mi_pastobjs as $mi_pastobj) {
//                if($mi_pastobj->mibdid == $midata->mibdid) {
//                    continue;
//                }
                if(strlen($mi_pastobj->comments) == 0) {
                    continue;
                }
                if(TIME_NOW - $mi_pastobj->createdOn > $twelvemonths) {
                    continue;
                }
                $createdby = new Users($mi_pastobj->createdBy);
                $date = date($core->settings['datetime'], $mi_pastobj->createdOn);
                $comments.="<br>".$createdby->get_displayname()."   ".$date." :<br>".$mi_pastobj->comments;
            }
        }
        if(!empty($comments)) {
            $comments = 'Past Comments: <div style="display:block">'.$comments.'</div>';
        }
        if(is_array($brandsproducts)) {
            foreach($brandsproducts as $brandproduct) {
                $brandproduct_obj = $brandproduct;
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
        $endproducttypes = EndProducTypes:: get_endproductypes();
        if(is_array($endproducttypes)) {
            foreach($endproducttypes as $endproducttype) {
                $endproducttypes_list .= '<option value="'.$endproducttype->eptid.'">'.$endproducttype->title.' - '.$endproducttype->get_application()->get_displayname().'</option>';
            }
        }
        unset($endproducttypes);

        $basicingredients_obj = $midata->get_basicingredients();
        if(is_object($basicingredients_obj)) {
            $basicingredient = $basicingredients_obj->get_displayname();
            $css['display']['basicingsubfield'] = 'block';
            eval("\$profiles_michemfuncproductentry = \"".$template->get('profiles_mibasicingredientsentry')."\";");
            unset($basicingredients_obj, $basicingredient);
        }

        $chemfuncchemical = $midata->get_chemfunctionschemcials();
        if(is_object($chemfuncchemical)) {
            $chemsubstance = $chemfuncchemical->get_chemicalsubstance();
            $css['display']['chemsubfield'] = 'block';
            eval("\$profiles_michemfuncproductentry = \"".$template->get('profiles_michemfuncsubstancentry')."\";");
        }

        $brandedendprod_obj = new EntBrandsProducts($midata->ebpid);
        if(is_object($brandedendprod_obj)) {
            $brandname = $brandedendprod_obj->get_entitybrand()->get_displayname();
        }
        /* parse visit report --START */
        $visitreport_objs = CrmVisitReports::get_visitreports(array('uid' => $core->user['uid'], 'cid' => $midata->cid, 'isDraft' => 1), array('order' => array('by' => 'date', 'sort' => 'DESC'), 'returnarray' => 1));
        if(is_array($visitreport_objs)) {
            $profiles_mincustomervisit_title = $lang->visitreport;
            $profiles_mincustomervisit = parse_selectlist('marketdata[vrid]', 7, $visitreport_objs, $midata->vrid, '', '', array('blankstart' => 1));
        }
        /* parse visit report --END */
        $chemfuncproduct = $midata->get_chemfunctionproducts();
        if(is_object($chemfuncproduct)) {
            $product = $chemfuncproduct->get_produt();
            $css[display]['product'] = 'block';
            eval("\$profiles_minproductentry= \"".$template->get('profiles_michemfuncproductentry')."\";");
        }

        list($module, $modulefile) = explode('/', $core->input['module']);
        $elementname = 'marketdata[cid]';
        $action = 'do_addmartkerdata';
        $elemtentid = $customer->get_eid();
        /* Parse competitors related market Data */
        $mrktcompetitor_objs = $midata->get_competitors();
        if(is_array($mrktcompetitor_objs)) {
            end($mrktcompetitor_objs);
            $lastkey = key($mrktcompetitor_objs);
            foreach($mrktcompetitor_objs as $mrktcompetitor_obj) {
                if($mrktcompetitor_obj->micid == $lastkey) {
                    continue;
                }
                $rowid = $i;
                $traders = $mrktcompetitor_obj->get_entitytrader();
                if(is_array($traders)) {
                    $competitor['trader'] = $traders[$mrktcompetitor_obj->trader]->get_displayname();
                }
                $producer = $mrktcompetitor_obj->get_entityproducer();
                if(is_array(producer)) {
                    $competitor['producer'] = $producer[$mrktcompetitor_obj->producer]->get_displayname();
                }
                $product = $mrktcompetitor_obj->get_products();
                if(is_array($product)) {
                    $competitor['product'] = $product[$mrktcompetitor_obj->pid]->get_displayname();
                    $competitor['pid'] = $product[$mrktcompetitor_obj->pid]->pid;
                }
                $competitor['uniprice'] = $mrktcompetitor_obj->unitPrice;
                $packaging_list = parse_selectlist('marketdata[competitor]['.$i.'][packaging]', 7, Packaging::get_data('name IS NOT NULL'), $mrktcompetitor_obj->packaging, '', '', array('blankstart' => 1));
                $incoterms_list = parse_selectlist('marketdata[competitor]['.$i.'][incoterms]', 8, Incoterms::get_data('titleAbbr IS NOT NULL'), $mrktcompetitor_obj->incoterms, '', '', array('blankstart' => 1));
                $saletype_list = parse_selectlist('marketdata[competitor]['.$i.'][saletype]', 8, SaleTypes::get_data('stid IN (1,4)'), $mrktcompetitor_obj->saletype, '', '', array('blankstart' => 1));
                $samplacquire = parse_radiobutton('marketdata[competitor]['.$i.'][isSampleacquire]', array(1 => 'yes', 0 => 'no'), $mrktcompetitor_obj->isSampleacquire, true);
                eval("\$competitors_rows .= \"".$template->get('crm_marketpotentialdata_comptetitors')."\";");
                unset($mrktcompetitor_obj, $competitor);
            }

            $rowid = 2;
            $mrktcompetitor_obj = $mrktcompetitor_objs[$lastkey];
            $traders = $mrktcompetitor_obj->get_entitytrader();
            if(is_array($traders)) {
                $competitor['trader'] = $traders[$mrktcompetitor_obj->trader]->get_displayname();
            }
            $producer = $mrktcompetitor_obj->get_entityproducer();
            if(is_array(producer)) {
                $competitor['producer'] = $producer[$mrktcompetitor_obj->producer]->get_displayname();
            }
            $product = $mrktcompetitor_obj->get_products();
            if(is_array($product)) {
                $competitor['product'] = $product[$mrktcompetitor_obj->pid]->get_displayname();
                $competitor['pid'] = $product[$mrktcompetitor_obj->pid]->pid;
            }
            $producer = $mrktcompetitor_obj->get_entityproducer();
            if(is_array($producer)) {
                $competitor['producer'] = $producer[$mrktcompetitor_obj->producer]->get_displayname();
            }
            $competitor['uniprice'] = $mrktcompetitor_obj->unitPrice;
            $packaging_list = parse_selectlist('marketdata[competitor][2][packaging]', 7, Packaging::get_data('name IS NOT NULL'), $mrktcompetitor_obj->packaging, '', '', array('blankstart' => 1));
            $incoterms_list = parse_selectlist('marketdata[competitor][2][incoterms]', 8, Incoterms::get_data('titleAbbr IS NOT NULL'), $mrktcompetitor_obj->incoterms, '', '', array('blankstart' => 1));
            $saletype_list = parse_selectlist('marketdata[competitor][2][saletype]', 8, SaleTypes::get_data('stid IN (1,4)'), $mrktcompetitor_obj->saletype, '', '', array('blankstart' => 1));
            $samplacquire = parse_radiobutton('marketdata[competitor][2][isSampleacquire]', array(1 => 'yes', 0 => 'no'), $mrktcompetitor_obj->isSampleacquire, true);
        }
        else {
            $rowid = 2;
            $packaging_list = parse_selectlist('marketdata[competitor][2][packaging]', 7, Packaging::get_data('name IS NOT NULL'), '', '', '', array('blankstart' => 1));
            $incoterms_list = parse_selectlist('marketdata[competitor][2][incoterms]', 8, Incoterms::get_data('titleAbbr IS NOT NULL'), '', '', '', array('blankstart' => 1));
            $saletype_list = parse_selectlist('marketdata[competitor][2][saletype]', 8, SaleTypes::get_data('stid IN (1,4)'), '', '', '', array('blankstart' => 1));
            $samplacquire = parse_radiobutton('marketdata[competitor][2][isSampleacquire]', array(1 => 'yes', 0 => 'no'), '', true);
        }

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
        $basic_attrs = array('cfcid', 'cfpid', 'biid');

        foreach($basic_attrs as $attr) {
            $at = "'".$attr."'";
            if($mkintentry->$attr > 0) {
                $main_attr = $attr;
            }
        }
        if(!isset($main_attr)) {
            exit;
        }
        $mainattr = $mkintentry->$main_attr;
        $twelvemonths = 31536000;
        $notcurrent = 'mibdid != '.$mkintentry->mibdid;
        $mi_pastobjs = MarketIntelligence::get_marketdata_dal(array('cid' => $mkintentry->cid, 'CUSTOMSQL' => $notcurrent, 'ebpid' => $mkintentry->ebpid, 'createdBy' => $core->user['uid'], $main_attr => $mainattr), array('simple' => false, 'operators' => array('CUSTOMSQL' => 'CUSTOMSQL'), 'order' => array('by' => 'createdOn', 'sort' => 'DESC')));
        if(is_array($mi_pastobjs)) {
            foreach($mi_pastobjs as $mi_pastobj) {
                if(strlen($mi_pastobj->comments) == 0) {
                    continue;
                }
                if(TIME_NOW - $mi_pastobj->createdOn > $twelvemonths) {
                    continue;
                }
                $createdby = new Users($mi_pastobj->createdBy);
                $date = date($core->settings['datetime'], $mi_pastobj->createdOn);
                $comments.="<br><br>".$createdby->get_displayname()."   ".$date." :<br>".$mi_pastobj->comments;
            }
        }
        if(!empty($comments)) {
            $comments = '<td><strong>Past Comments</strong></td><td><div style="width:400px; overflow:auto; height:100px; line-height:20px;">'.$comments.'</div></td>';
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
        if(is_array($mrktcom_petitor_objs)) {
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
                            $mrktintl_detials_competitorproducts.='<li>'.$competitorsproducts_obj->get()['name'].'</li>';
                        }
                    }
                }
            }
            eval("\$marketintelligencedetail_competitors .= \"".$template->get('profiles_entityprofile_marketintelligence_competitors')."\";");
        }

        eval("\$marketintelligencedetail = \"".$template->get('popup_marketintelligencedetails')."\";");
        output($marketintelligencedetail);
    }
    elseif($core->input['action'] == 'get_deletemientry') {
        if($core->usergroup['profiles_canAddMkIntlData'] == 0) {
            exit;
        }
        $id = $db->escape_string($core->input['id']);
        eval("\$mideleteentry = \"".$template->get('popup_crm_deletemientry')."\";");
        echo $mideleteentry;
    }
    elseif($core->input['action'] == 'perform_delete') {
        $id = $db->escape_string($core->input['todelete']);
        $mintentry = new MarketIntelligence($id, false);
        if(is_object($mintentry)) {
            if($core->usergroup['crm_canManageMktInteldata'] == 1 || $mintentry->createdBy == $core->user['uid']) {
                $query = $db->delete_query('marketintelligence_basicdata', "mibdid='{$id}'");
                if($query) {
                    output_xml("<status>true</status><message>{$lang->successfullydeleted}</message>");
                }
            }
            else {
                output_xml("<status>false</status><message>{$lang->nopermission}</message>");
            }
        }
    }
    elseif($core->input['action'] == 'get_addnew_chemical') {
        $module = "crm";
        $modulefile = "marketpotentialdata";
        eval("\$createchemical= \"".$template->get('popup_crm_createchemical')."\";");
        output_page($createchemical);
    }
    elseif($core->input['action'] == 'do_createchemical') {
        $chemsustance = new Chemicalsubstances();
        $chemsustance->create($core->input['chemcialsubstances']);

        switch($chemsustance->get_status()) {
            case 0:
                output_xml("<status>true</status>{$lang->successfullysaved}<message> {
        $lang->successfullysaved}</message>");
                break;
            case 4:
                output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                break;
            case 2:
                $error_output = $errorhandler->get_errors_inline();
                output_xml("<status>false</status><message><![CDATA[{$error_output}]]></message>");
                break;
        }
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
    elseif($core->input['action'] == 'enable_visitreports') {
        $visitreport_objs = CrmVisitReports::get_visitreports(array('uid' => $core->user['uid'], 'cid' => $core->input['cid'], 'isDraft' => 1), array('order' => array('by' => 'date', 'sort' => 'DESC'), 'returnarray' => 1));
        if(is_array($visitreport_objs)) {
            foreach($visitreport_objs as $visitreport_obj) {
                $visitoptions[$visitreport_obj->vrid] = $visitreport_obj->get_displayname();
            }
            output(json_encode($visitoptions));
        }
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