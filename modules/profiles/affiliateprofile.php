<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Entity profile
 * $module: profiles
 * $id: entityprofile.php
 * Created:			@najwa.kassem		October 11, 2010 | 10:28 AM
 * Last Update: 	@zaher.reda 		September 12, 2011 | 05:15 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if(!$core->input['action']) {
    if(!isset($core->input['affid'])) {
        redirect($_SERVER['HTTP_REFERER']);
    }

    $affid = $db->escape_string($core->input['affid']);
    $filter_where = 'affid IN ('.$affid.')';
    $rowid = intval($core->input['value']) + 2;
    $affiliate_obj = new Affiliates($affid, false);
    $profile = $affiliate_obj->get();
    $workshift_obj = $affiliate_obj->get_defaultworkshift();

    if(!empty($profile['addressLine1'])) {
        $profile['fulladdress'] .= $profile['addressLine1'].' ';
    }

    if(!empty($profile['addressLine2'])) {
        $profile['fulladdress'] .= $profile['addressLine2'].', ';
    }

    if(!empty($profile['city'])) {
        $profile['fulladdress'] .= $affiliate_obj->get_city()->get()['name'].' - ';
    }

    $profile['fax'] = '+'.$profile['fax'];
    $profile['phone1'] = '+'.$profile['phone1'];
    if(isset($profile['phone2']) && !empty($profile['phone2'])) {
        $profile['phone2'] = '/+'.$profile['phone2'];
    }
    else {
        unset($profile['phone2']);
    }
    $management_fields = array('supervisor', 'generalManager', 'hrManager');
    foreach($management_fields as $managefield) {
        if(!empty($profile[$managefield])) {
            $manage_uids[] = $profile[$managefield];
        }
    }
    if(is_array($manage_uids)) {
        $management_query = $db->query("SELECT uid, CONCAT(firstName, ' ', lastName) AS generalManager FROM ".Tprefix."users WHERE uid IN ({$profile['supervisor']},{$profile['generalManager']},{$profile['hrManager']})");
        while($management = $db->fetch_array($management_query)) {
            $managers[$management['uid']] = $management['generalManager'];
        }
    }

    if($profile['generalManager'] == 0) {
        $profile['generalManager_output'] = $lang->na;
    }
    else {
        $profile['generalManager_output'] = $affiliate_obj->get_generalmanager()->parse_link();
    }
    if($profile['supervisor'] == 0) {
        $profile['supervisor_output'] = $lang->na;
    }
    else {
        $profile['supervisor_output'] = $affiliate_obj->get_supervisor()->parse_link();
    }

    if($profile['hrManager'] == 0) {
        $profile['hrManager_output'] = $lang->na;
    }
    else {
        $profile['hrManager_output'] = $affiliate_obj->get_hrmanager()->parse_link();
    }

    if($profile['finManager'] == 0) {
        $profile['finManager_output'] = $lang->na;
    }
    else {
        $profile['finManager_output'] = $affiliate_obj->get_financialemanager()->parse_link();
    }

    /* Parse default workshift - START */
    if(is_object($workshift_obj)) {
        $profile['workshift'] = $workshift_obj->get_dutyhours().' ('.$workshift_obj->get_weekdays().')';
    }
    else {
        $profile['workshift'] = $lang->na;
    }
    /* Parse default workshift - END */

    foreach($profile as $key => $val) {
        if(empty($val)) {
            $profile[$key] = $lang->na;
        }
    }

    $countries_query = $db->query("SELECT coid, name FROM ".Tprefix."countries WHERE affid={$affid} ORDER BY name");
    while($countries = $db->fetch_array($countries_query)) {
        $countrieslist[$countries['coid']] = $countries['name'];
    }
    $profile['fulladdress'] .= $countrieslist[$profile['country']];
    if(is_array($countrieslist)) {
        $countries_list = implode(', ', $countrieslist);
    }

    $suppliers_query = $db->query(" SELECT *
							FROM ".Tprefix."affiliatedentities a LEFT JOIN ".Tprefix."entities e ON (a.eid=e.eid)
							WHERE a.affid={$affid} AND e.type='s'
							ORDER BY e.companyName ASC");

    $suppliers_counter = $customers_counter = $affiliateemployees_counter = 0;
    $user_mainaff = $db->fetch_field($db->query("SELECT affid FROM ".Tprefix."affiliatedemployees WHERE uid={$core->user['uid']} AND isMain=1"), 'affid');

    while($supplier = $db->fetch_array($suppliers_query)) {
        $listitem['link'] = 'index.php?module=profiles/entityprofile&eid='.$supplier['eid'];
        $listitem['title'] = $supplier['companyName'];
        $listitem['divhref'] = 'supplier';
        $listitem['loadiconid'] = 'loadentityusers_'.$supplier['eid'].'_'.$affid;

        if(++$suppliers_counter > 3) {
            eval("\$hidden_suppliers .= \"".$template->get('profiles_affliatesentities_inlinelistitem')."\";");
        }
        else {
            eval("\$shown_suppliers .= \"".$template->get('profiles_affliatesentities_inlinelistitem')."\";");
        }
    }

    if($suppliers_counter > 3) {
        $supplierslist = $shown_suppliers." <a href='#suppliers' id='showmore_suppliers_{$supplier[eid]}' class='smalltext'><img src='{$core->settings[rootdir]}/images/add.gif' alt='{$lang->edit}' border='0' /></a> <br /><span style='display:none;' id='suppliers_{$supplier[eid]}'>{$hidden_suppliers}</span>";
    }
    else {
        $supplierslist = "<ul style='list-style:none; padding:2px;'>".$shown_suppliers."</ul>";
    }

    $affiliateemployees_query = $db->query("SELECT *, CONCAT(firstName, ' ', lastName) AS fullname
							FROM ".Tprefix."assignedemployees e RIGHT JOIN ".Tprefix."users u ON (e.uid=u.uid) JOIN ".Tprefix."affiliatedemployees ae ON (ae.uid=u.uid)
							WHERE ae.affid={$affid} AND u.gid!=7 AND ae.isMain=1
							GROUP BY u.username
							ORDER BY u.firstName ASC");
    while($affililateemployees = $db->fetch_array($affiliateemployees_query)) {
        if(++$affiliateemployees_counter > 100) {
            $hidden_affililateemployees .= "<li><a href='./users.php?action=profile&uid={$affililateemployees[uid]}' target='_blank'>{$affililateemployees[fullname]}</a></li>";
        }
        elseif($affiliateemployees_counter == 100) {
            $shown_affililateemployees .= "<li><a href='./users.php?action=profile&uid={$affililateemployees[uid]}' target='_blank'>{$affililateemployees[fullname]}</a>";
        }
        else {
            $shown_affililateemployees .= "<li><a href='./users.php?action=profile&uid={$affililateemployees[uid]}' target='_blank'>{$affililateemployees[fullname]}</a></li>";
        }

        if(!empty($affililateemployees['internalExtension'])) {
            $rowclass = alt_row($rowclass);
            $extensions.= '<tr class="'.$rowclass.'"><td>'.$affililateemployees['fullname'].'</td><td>'.$affililateemployees['internalExtension'].'</td></tr>';
        }
    }

    if($affiliateemployees_counter > 100) {
        $supplierallusers = "<ul style='list-style:none; padding:2px;margin-top:0px;'>".$shown_affililateemployees.", <a href='#affililateemployees' id='showmore_affililateemployees_{$affililateemployees[uid]}' class='smalltext'>read more</a></li> <span style='display:none;' id='affililateemployees_{$affililateemployees[uid]}'>{$hidden_affililateemployees}</span></ul>";
    }
    else {
        $supplierallusers = "<ul style='list-style:none; padding:2px;margin-top:0px;'>".$shown_affililateemployees."</li></ul>";
    }

    if($user_mainaff == $affid) {
        $customers_query = $db->query("SELECT *
								FROM ".Tprefix."affiliatedentities a LEFT  JOIN ".Tprefix."entities e ON (a.eid=e.eid) JOIN ".Tprefix."assignedemployees ae ON (ae.eid=a.eid)
								WHERE a.affid={$affid} AND e.type='c' AND ae.uid={$core->user['uid']}
								GROUP BY e.companyName
								ORDER BY e.companyName ASC");
        if($db->num_rows($customers_query) > 0) {
            while($customer = $db->fetch_array($customers_query)) {
                if(++$customers_counter > 3) {
                    $hidden_customers .= "<li><a href='index.php?module=profiles/entityprofile&eid={$customer[eid]}' target='_blank'>{$customer['companyName']}</a></li>";
                }
                elseif($customers_counter == 3) {
                    $shown_customers .= "<li><a href='index.php?module=profiles/entityprofile&eid={$customer[eid]}' target='_blank'>{$customer['companyName']}</a>";
                }
                else {
                    $shown_customers .= "<li><a href='index.php?module=profiles/entityprofile&eid={$customer[eid]}' target='_blank'>{$customer['companyName']}</a></li>";
                }
            }

            if($customers_counter > 3) {
                $customerslist = "<ul style='list-style:none; padding:2px;margin-top:0px;'>".$shown_customers.", <a href='#customers' id='showmore_customers_{$customer[eid]}' class='smalltext'>read more</a> </li><span style='display:none;' id='customers_{$customer[eid]}'>{$hidden_customers}</span></ul>";
            }
            else {
                $customerslist = '<ul style="list-style:none; padding:2px;margin-top:0px;">'.$shown_customers.'</ul>';
            }
        }

        $report_query = $db->query("SELECT *, e.companyName AS supplier_name
										FROM ".Tprefix." reports r LEFT JOIN ".Tprefix."entities e ON (r.spid=e.eid) JOIN ".Tprefix."assignedemployees ae ON (ae.eid=r.spid)
										WHERE r.affid={$affid} AND r.type='q'
										GROUP BY r.rid
										ORDER BY finishDate DESC
										LIMIT 0, 4");

        $reports_counter = 0;
        while($reports = $db->fetch_array($report_query)) {
            if(++$reports_counter < 3) {
                $shown_reports .= "<li><a href='index.php?module=reporting/preview&referrer=list&rid={$reports[rid]}' target='_blank'> Q{$reports['quarter']} / {$reports['year']} - {$reports['supplier_name']}</a></li>";
            }
            elseif($reports_counter == 3) {
                $shown_reports .= "<li><a href='index.php?module=reporting/preview&referrer=list&rid={$reports[rid]}' target='_blank'> Q{$reports['quarter']} / {$reports['year']} - {$reports['supplier_name']}</a>";
            }
            else {
                break;
            }
        }

        if($reports_counter > 3) {
            $reports_list = "<ul style='list-style:none; padding:2px;margin-top:0px;'>".$shown_reports.", <a href='index.php?module=reporting/list&filterby=affid&filtervalue={$affid}' target='_blank' class='smalltext'>read more</a></li></ul>";
        }
        else {
            $reports_list = "<ul style='list-style:none; padding:2px;margin-top:0px;'>".$shown_reports."</li></ul>";
        }
        eval("\$private_section = \"".$template->get('profiles_affiliateprofile_privatesection')."\";");
    }

    if($core->usergroup['profiles_canAddMkIntlData'] == 1) {
        $addmarketdata_link = '<div style="float: right;" title="'.$lang->addmarketdata.'"><a href="#popup_profilesmarketdata" id="showpopup_profilesmarketdata" class="showpopup"><img alt="'.$lang->addmarket.'" src="'.$core->settings['rootdir'].'/images/icons/edit.gif" /></a></div>';

        $array_data = array('module' => 'profiles', 'elemtentid' => $affid, 'fieldlabel' => $lang->product, 'action' => 'do_addmartkerdata', 'modulefile' => 'entityprofile');
        eval("\$profiles_entityprofile_micustomerentry = \"".$template->get('profiles_micustomerentry')."\";");

        $module = 'profiles';
        $elemtentid = $affid;
        $elementname = 'marketdata[affid]';
        $action = 'do_addmartkerdata';
        $modulefile = 'affiliateprofile';
        $css['display']['chemsubfield'] = 'none';
        $css['display']['basicingsubfield'] = 'none';
        $css['display']['product'] = 'none';
        $mkdchem_rowid = 0;
        eval("\$profiles_michemfuncproductentry_row = \"".$template->get('profiles_michemfuncsubstancentry')."\";");
        eval("\$profiles_michemfuncproductentry = \"".$template->get('profiles_michemfuncsubstancentry_rows')."\";");

        $mkdprod_rowid = 0;
        eval("\$profiles_minproductentry_row = \"".$template->get('profiles_michemfuncproductentry')."\";");
        eval("\$profiles_minproductentry = \"".$template->get('profiles_michemfuncproductentry_rows')."\";");

        $mkdbing_rowid = 0;
        eval("\$profiles_mibasicingredientsentry_row = \"".$template->get('profiles_mibasicingredientsentry')."\";");
        eval("\$profiles_mibasicingredientsentry = \"".$template->get('profiles_mibasicingredientsentry_rows')."\";");


        /* View detailed market intelligence box --START */
        $maktintl_mainobj = new MarketIntelligence();
        $miprofile = $maktintl_mainobj->get_miprofconfig_byname('latestaggregatebycustomer');
        $miprofile['next_miprofile'] = 'latestaggregatecustomersumbyproduct';
        $maktintl_objs = $maktintl_mainobj->get_marketintelligence_timeline(array('affid' => $affid), $miprofile);
        if(is_array($maktintl_objs)) {
            foreach($maktintl_objs as $mktintldata) {
                $mktintldata['tlidentifier']['id'] = 'tlrelation-'.$affid;
                $mktintldata['tlidentifier']['value'] = array('affid' => $affid);
                $detailmarketbox .= $maktintl_mainobj->parse_timeline_entry($mktintldata, $miprofile);
            }
        }

        /* Filter by segments which the entity works in */
        $productypes_objs = EndProducTypes::get_endproductypes();
        if(is_array($productypes_objs)) {
            foreach($productypes_objs as $endproducttype) {
                $value = $endproducttype->title;
                $application = $endproducttype->get_application()->parse_link();
                if($application !== null) {
                    $value .=' - '.$application;
                }
                $parent = $endproducttype->get_endproducttype_chain();
                if(!empty($parent)) {
                    $values[$endproducttype->eptid] = $parent.' > '.$value;
                }
                else {
                    $values[$endproducttype->eptid] = $value;
                }
                //     $endproducttypes_list .= '<option value="'.$productype->eptid.'">'.$productype->title.' - '.$productype->get_application()->title.'</option>';
            }
            asort($values);
            foreach($values as $key => $value) {
                $checked = $rowclass = '';
                $endproducttypes_list .= ' <tr class="'.$rowclass.'">';
                $endproducttypes_list .= '<td><input id="producttypefilter_check_'.$key.'" type="checkbox"'.$checked.' value="'.$key.'" name="entitybrand[endproducttypes]['.$key.']">'.$value.'</td><tr>';
            }
        }

        /* parse visit report */
        $visitreport_objs = CrmVisitReports::get_visitreports(array('uid' => $core->user['uid'], 'affid' => $elemtentid, 'isDraft' => 1), array('order' => array('by' => 'date', 'sort' => 'DESC'), 'returnarray' => 1));
        if(is_array($visitreport_objs)) {
            foreach($visitreport_objs as $visitreport) {
                $customer_name = $visitreport->get_customer($visitreport->cid)->companyName;
                $customervisit_list.='<option value="'.$visitreport->vrid.'">'.$customer_name.' - '.date($core->settings['dateformat'], $visitreport->date).'</option>';
            }
        }
        $entitiesbrandsproducts_list = $lang->na;
        $packaging_list = parse_selectlist('marketdata[competitor]['.$rowid.'][packaging]', 7, Packaging::get_data('name IS NOT NULL'), '', '', '', array('blankstart' => 1));
        $saletype_list = parse_selectlist('marketdata[competitor]['.$rowid.'][saletype]', 8, SaleTypes::get_data('stid IN (1,4)'), '', '', '', array('blankstart' => 1));
        $samplacquire = parse_radiobutton('marketdata[competitor]['.$rowid.'][isSampleacquire]', array(1 => 'yes', 0 => 'no'), '', true);
        $characteristics = ProductCharacteristicValues::get_data(null, array('returnarray' => true));
        $characteristics_list = parse_selectlist('entitybrand[pcvid]', 4, $characteristics, null, 0, null, array('blankstart' => true));
        eval("\$popup_marketdata= \"".$template->get('popup_profiles_marketdata')."\";");
        eval("\$popup_createbrand = \"".$template->get('popup_createbrand')."\";");

        eval("\$mkintl_section = \"".$template->get('profiles_mktintelsection')."\";");
    }
    eval("\$profilepage = \"".$template->get('profiles_affiliateprofile')."\";");

    output_page($profilepage);
    //}
}
else {
    if($core->input['action'] == 'getentityusers' || $core->input['action'] == 'getallusers') {
        if($core->input['action'] == 'getentityusers') {
            $query_string = " AND e.eid = '".$db->escape_string($core->input['eid'])."'";
        }

        $affid = $db->escape_string($core->input['affid']);

        $entityusers_query = $db->query("SELECT *, CONCAT(firstName, ' ', lastName) AS fullname
							FROM ".Tprefix."assignedemployees e RIGHT JOIN ".Tprefix."users u ON (e.uid=u.uid) JOIN ".Tprefix."affiliatedemployees ae ON (ae.uid=u.uid)
							WHERE (ae.affid={$affid} AND ae.isMain=1){$query_string} AND u.gid!=7
							GROUP BY u.username
							ORDER BY u.firstName ASC");
        if($db->num_rows($entityusers_query) > 0) {
            while($entityusers = $db->fetch_array($entityusers_query)) {
                $entityusers_list .= "<li><a href='./users.php?action=profile&uid={$entityusers[uid]}' target='_blank'>{$entityusers[fullname]}</a></li>";
            }
        }
        else {
            $entityusers_list = $lang->na;
        }
        $entityusers_list_output = "<ul style='list-style:none; padding:2px; margin-top: 0px;'>{$entityusers_list}</ul> ";
        echo $entityusers_list_output;
    }
    elseif($core->input['action'] == 'do_addmartkerdata') {
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
    elseif($core->input['action'] == 'do_addbrand') {
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
        //$mrktintl_detials['brand'] = $mkintentry->get_entitiesbrandsproducts()->get_entitybrand()->name;
        //$mrktintl_detials['endproduct'] = $mkintentry->get_entitiesbrandsproducts()->get_endproduct()->title;

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
    elseif($core->input['action'] == 'parse_previoustimeline') {
        if($core->usergroup['profiles_canAddMkIntlData'] == 0) {
            exit;
        }
        $next_profiles = array('latestaggregatecustomersumbyproduct' => 'allprevious', 'allprevious' => null);
        $filter = unserialize($core->input['tlrelation']);

        $mrktint_obj = new MarketIntelligence();

        $miprofile = $mrktint_obj->get_miprofconfig_byname($core->input['miprofile']);
        $miprofile['next_miprofile'] = $next_profiles[$core->input['miprofile']];

        $is_last = false;
        if(empty($miprofile['next_miprofile'])) {
            $is_last = true;
        }
        $depth = count($filter) - 1;
        echo $depth;
        exit;
        $mrkt_objs = $mrktint_obj->get_marketintelligence_timeline($filter, $miprofile);
        //$mrkt_objs = $mrktint_obj->get_marketintelligence_timeline('customer', 'cfpid', $cfpid, array('time' => 'allprevious', 'filterchemfunctprod' => 1));
        $previoustimelinerows = '';
        if(is_array($mrkt_objs)) {
            foreach($mrkt_objs as $mktintldata) {
                $mktintldata['tlidentifier']['id'] = 'tlrelation-'.implode('-', $filter);
                $mktintldata['tlidentifier']['value'] = $filter;
                $previoustimelinerows .= $mrktint_obj->parse_timeline_entry($mktintldata, $miprofile, $depth, $is_last);
            }
        }
        output($previoustimelinerows);
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
                $options[$brandproduct->ebpid] = $brandproduct->get_entitybrand()->parse_link();
                if(!empty($brandproduct->eptid)) {
                    $characteristic_output = '';
                    $characteristic = $brandproduct->get_charactersticvalue();
                    if(is_object($characteristic)) {
                        $characteristic_output = ' <small>('.$characteristic->get_displayname().')</small>';
                    }
                    $options[$brandproduct->ebpid] .= ' - '.$brandproduct->get_endproduct()->parse_link().$characteristic_output;
                }
            }

            $output = parse_selectlist('marketdata[ebpid]', 7, $options, '');
        }
        else {
            $output = $lang->na;
        }
        output($output);
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
?>