<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: marketintelligencereport.php
 * Created:        @tony.assaad    Mar 10, 2014 | 2:59:34 PM
 * Last Update:    @tony.assaad    Mar 10, 2014 | 2:59:34 PM
 */


if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['crm_canGenerateMIRep'] == 0) {
    //  error($lang->sectionnopermision);
}
if(!$core->input['action']) {
    $identifier = substr(md5(microtime(uniqid())), 0, 10);
    // Here we get affiliate for user assigned to, or he can audit

    $afffiliates_users = $core->user['affiliates'] + $core->user['auditfor'];
    $afffiliates_users = array_unique($afffiliates_users);
    foreach($afffiliates_users as $affid => $affiliates) {
        $selected = '';
        $affiliate_obj = new Affiliates($affiliates);
        $affiliates_data = $affiliate_obj->get();
        if($affiliates_data['affid'] == $core->user['mainaffiliate']) {
            $selected = " selected='selected'";
        }
        $affiliates_list.='<tr><td><input type="checkbox" name="mireport[filter][affid][]"  id="mireport_filter_affid_'.$affiliates_data['affid'].'" value="'.$affiliates_data['affid'].'"/>&nbsp;'.$affiliates_data['name'].'</td></tr>';
    }

    // Here we get  suppliers that the user is assigned to or work with an affiliate that he can audit
    if($core->usergroup['canViewAllSupp'] == 0) {
        if(is_array($core->user['suppliers']['eid'])) {
            $insupplier = implode(',', $core->user['suppliers']['eid']);
            $supplier_where = " eid IN ({$insupplier})";
        }
        else {
            $supplier_where = " eid = 0";
        }
    }
    else {
        $supplier_where = " type='s'";
    }
    $user_suppliers = get_specificdata('entities', array('eid', 'companyName'), 'eid', 'companyName', array('by' => 'companyName', 'sort' => 'ASC'), 1, "{$supplier_where}");

    $user_obj = new Users();
    $affiliatesaudit_objs = $user_obj->get_auditedaffiliates();
    if(is_array($affiliatesaudit_objs)) {
        foreach($affiliatesaudit_objs as $affid => $affiliatesaudit_obj) {
            $affiliatedaudit_suppliers = $affiliatesaudit_obj->get_suppliers();
            $selected = '';
            $suppliers_list = '';
            foreach($affiliatedaudit_suppliers as $affiliatedaudit_supplier) {

                if(in_array($affiliatedaudit_supplier['eid'], $core->user['suppliers']['eid'])) {
                    $selected = " selected='selected'";
                }
                $suppliers_list.='<tr><td><input type="checkbox" name="mireport[filter][spid][]"  id="mireport_filter_spid_'.$affiliatedaudit_supplier['eid'].'" value="'.$affiliatedaudit_supplier['eid'].'"/>&nbsp;'.$affiliatedaudit_supplier['companyName'].'</td></tr>';
            }
        }
    }
    //Here we get customer that the user is assigned to or work with an affiliate that he can audit
    $incusomters = implode(',', $core->user['customers']);
    $customer_where = " eid IN ({$incusomters})";

    $users_customers = get_specificdata('entities', array('eid', 'companyName'), 'eid', 'companyName', array('by' => 'companyName', 'sort' => 'ASC'), 1, "{$customer_where}");
    if(is_array($affiliatesaudit_objs)) {
        foreach($affiliatesaudit_objs as $affid => $affiliatesaudit_obj) {
            $affiliatesaudit_customersobjs = $affiliatesaudit_obj->get_customers();
            if(is_array($affiliatesaudit_customersobjs)) {
                foreach($affiliatesaudit_customersobjs as $affiliatesaudit_customersobj) {
                    $affiliatesaudit_customer = $affiliatesaudit_customersobj->get();
                    if(in_array($affiliatesaudit_customer['eid'], $core->user['customers'])) {
                        $selected = " selected='selected'";
                    }
                    $customers_list.='<tr><td><input type="checkbox" name="mireport[filter][cid][]" id="mireport_filter_cid_'.$affiliatesaudit_customer['eid'].'" value="'.$affiliatesaudit_customer['eid'].'"/>&nbsp;'.$affiliatesaudit_customer['companyName'].'</td> </tr>';
                }
            }
        }
    }
    /* get object of customers  with filter  by sujpliertypes */
    //$potential_custobjs = Customers::get_customers(array('type' => 'c', 'supplierType' => 'pc'));
//    if(is_array($potential_custobjs)) {
//        foreach($potential_custobjs as $potential_custobj) {
//            $potential_customername = $potential_custobj->companyName;
//            $potential_customerlist .='<tr><td><input type="checkbox" name="mireport[filter][cid][]"  value="'.$potential_custobj->eid.'"/>&nbsp; '.$potential_custobj->companyName.'</td></tr>';
//        }
//    }
    // Get User  segments the user is assigned to, assigned to supervise, or is coordinator for
    $user = new Users($core->user['uid']);
    $user_segmentsobjs = $user->get_segments();
    if(is_array($user_segmentsobjs)) {

        foreach($user_segmentsobjs as $key => $user_segmentsobj) {
            $userassigned_segments[$user_segmentsobj->get()['psid']] = $user_segmentsobj->get()['title'];
        }
    }
    //$userassigned_segments = $user->get_segments();
    //$user_coordinator_segments = $user->get_coordinatesegments();
    //Get segment cooridnator
    if(is_array($userassigned_segments) && is_array($user_coordinator_segments)) {

        $user_segments = array_merge($userassigned_segments, $user_coordinator_segments);
    }
    else {
        $user_segments = $userassigned_segments;
    }
    $selected = '';

    if(is_array($user_segments)) {
        foreach($user_segments as $psid => $user_segment) {
            if(in_array($psid, $core->user['suppliers']['eid'])) {
                $selected = " selected='selected'";
            }
            $segmentlist .='<tr><td><input type="checkbox" name="mireport[filter][psid][]"  id="mireport_filter_psid_'.$psid.'" value="'.$psid.'"/>&nbsp;'.$user_segment.'</td></tr>';
        }
    }

    // Get business manager that report to the user or are assigned to a main affiliate that the user is auditing
    $bmreported = $user->get_reportingto();
    // user assigned= $core->user['auditfor']
    if(is_array($core->user['auditedaffids'])) {
        foreach($core->user['auditedaffids'] as $auditaffid) {
            $aff_obj = new Affiliates($auditaffid);
            $affiliate_users = $aff_obj->get_users(array('ismain' => 1));
            if(is_array($affiliate_users)) {
                foreach($affiliate_users as $aff_businessmgr) {
                    $business_managers[$aff_businessmgr['uid']] = $aff_businessmgr['displayName'];
                }
            }
        }
        $business_managerslist = parse_selectlist('mireport[filter][managers][]', 5, $business_managers, $core->user['uid'], 1, '', '');
    }

    //, 'spid' => $lang->supplier,'spid' => $lang->supplier, 'cid' => $lang->customer, 'psid' => $lang->segment, 'coid' => $lang->customercountry
    $dimensions = array('affid' => $lang->affiliate, 'eptid' => $lang->endproductype, 'pid' => $lang->product, 'cid' => $lang->customer, 'spid' => $lang->supplier, 'csid' => $lang->chemicalsubstance, 'psid' => $lang->segment, 'affid' => $lang->affiliate, 'psaid' => $lang->application, 'ctype' => $lang->customertype);

    foreach($dimensions as $dimensionid => $dimension) {
        $dimension_item.='<li class = "ui-state-default" id = '.$dimensionid.' title = "Click and Hold to move the '.$dimension.'">'.$dimension.'</li>';
    }
    eval("\$mireport_options = \"".$template->get('crm_marketintelligence_report_options')."\";");
    output_page($mireport_options);
}

//'get_report
if($core->input['action'] == 'do_perform_marketintelligencereport') {

    // $mireportdata = json_decode($core->input['mireport'], true);
    $mireportdata = ($core->input['mireport']);
//    foreach($mireportdata['filter'] as $filteritem => $arr) {
//        if(empty($arr)) {
//            unset($mireportdata['filter'][$filteritem]);
//        }
//    }
    /* split the dimension and explode them into chuck of array */
    $mireportdata['dimension'] = explode(',', $mireportdata['dimension'][0]);
    $mireportdata['dimension'] = array_filter($mireportdata['dimension']);

    /* to create array using existing values (using array_values()) and range() to create a new range from 1 to the size of the  dimension array */
    $mireportdata['dimension'] = array_combine(range(0, count($mireportdata['dimension'])), array_values($mireportdata['dimension']));

    $marketdata_indexes = array('potential', 'mktSharePerc', 'mktShareQty', 'turnover');
    /* get Market intellgence baisc Data  --START */

    /* Get cfpid of segment  ----END */
    $dimensionalize_ob = new DimentionalData();
    /* split the dimension and explode them into chuck of array */


    if(!isset($mireportdata['filter']['spid']) && empty($mireportdata['filter']['spid'])) {
        $mireportdata['filter']['spid'] = $core->user['suppliers']['eid'];
    }
    /* Get cfpid of segment ----START */

    if(isset($mireportdata['filter']['spid'])) {
        $mireportdata['filter']['cfpid'] = 'SELECT cfpid FROM '.Tprefix.'chemfunctionproducts WHERE pid IN (SELECT pid FROM '.Tprefix.'products WHERE spid IN ('.implode(',', $mireportdata['filter']['spid']).'))';
    }

    if(isset($mireportdata['filter']['psid'])) {
        $mireportdata['filter']['psid'] = array_map(intval, $mireportdata['filter']['psid']);
        $mireportdata['filter']['cfpid2'] = 'SELECT cfpid FROM '.Tprefix.'chemfunctionproducts WHERE safid IN (SELECT safid FROM '.Tprefix.'segapplicationfunctions WHERE psaid IN (SELECT psaid FROM '.Tprefix.'segmentapplications WHERE psid IN ('.implode(',', $mireportdata['filter']['psid']).')))';

        if(isset($mireportdata['filter']['cfpid'])) {
            $mireportdata['filter']['cfpid'] .= ' AND cfpid IN ('.$mireportdata['filter']['cfpid2'].')';
        }
        else {
            $mireportdata['filter']['cfpid'] = $mireportdata['filter']['cfpid2'];
        }
    }
    if(isset($mireportdata['filter']['psid']) && !empty($mireportdata['filter']['cfpid'])) {
        $mireportdata['filter']['cfcid'] = 'SELECT cfcid FROM '.Tprefix.'chemfunctionchemcials WHERE safid IN (SELECT safid FROM '.Tprefix.'segapplicationfunctions WHERE psaid IN (SELECT psaid FROM '.Tprefix.'segmentapplications WHERE psid IN ('.implode(',', $mireportdata['filter']['psid']).')))';
    }
    if(isset($mireportdata['filter']['ctype'])) {
        $mireportdata['filter']['ctype'] = array_map($db->escape_string, $mireportdata['filter']['ctype']);  /* apply the call bak function dbescapestring to the the value */
        $mireportdata['filter']['cid'] = 'SELECT eid FROM '.Tprefix.'entities WHERE type IN  (\''.implode('\',\'', $mireportdata['filter']['ctype']).'\')';
    }

    unset($mireportdata['filter']['coid'], $mireportdata['filter']['cfpid2'], $mireportdata['filter']['spid'], $mireportdata['filter']['psid'], $mireportdata['filter']['ctype']);
    /* Get cfpid of segment ----END */


    if(empty($mireportdata['filter']['cid'])) {
        $mireportdata['filter']['cid'] = $core->user['customers'];
    }
    if(empty($mireportdata['filter']['affid'])) {
        $afffiliates_users = $core->user['affiliates'] + $core->user['auditfor'];
        $mireportdata['filter']['affid'] = array_unique($afffiliates_users);
    }

    $marketin_objs = MarketIntelligence::get_marketdata_dal($mireportdata['filter'], array('simple' => false, 'operators' => array('coid' => 'IN', 'cid' => 'IN', 'cfpid' => 'IN', 'cfcid' => 'IN')));

    /* START presentiation layer */
    /* Get the id related to the chemfunctionproducts  from the object and send them to the dimensional data class   */
    if(is_array($marketin_objs)) {
        foreach($marketin_objs as $marketin_obj) {
            $market_data[$marketin_obj->get()['mibdid']] = $marketin_obj->get();
            $customer_obj = new Customers($market_data[$marketin_obj->get()['mibdid']]['cid'], '', false);
            $market_data[$marketin_obj->get()['mibdid']]['ctype'] = $customer_obj->get()['type'];
            $chmfuncproduct = $marketin_obj->get_chemfunctionproducts();
            if(is_object($chmfuncproduct)) {
                $product = $marketin_obj->get_chemfunctionproducts()->get_produt();

                $market_data[$marketin_obj->get()['mibdid']]['spid'] = $product->get_supplier()->get()['eid'];
                $market_data[$marketin_obj->get()['mibdid']]['pid'] = $product->pid;
                $market_data[$marketin_obj->get()['mibdid']]['psid'] = $marketin_obj->get_chemfunctionproducts()->get_segapplicationfunction()->get_segment()->psid;
                $market_data[$marketin_obj->get()['mibdid']]['psaid'] = $marketin_obj->get_chemfunctionproducts()->get_segapplicationfunction()->get_application()->psaid;
            }
            else {
                if(is_object($marketin_obj->get_chemfunctionschemcials())) {
                    $market_data[$marketin_obj->get()['mibdid']]['csid'] = $marketin_obj->get_chemfunctionschemcials()->get_chemicalsubstance()->csid;
                    $market_data[$marketin_obj->get()['mibdid']]['psid'] = $marketin_obj->get_chemfunctionschemcials()->get_segapplicationfunction()->get_segment()->psid;
                    $market_data[$marketin_obj->get()['mibdid']]['psaid'] = $marketin_obj->get_chemfunctionschemcials()->get_segapplicationfunction()->get_application()->psaid;
                }

                if(empty($market_data[$marketin_obj->get()['mibdid']]['psid'])) {
                    $application = $marketin_obj->get_endproducttype()->get_application();
                    $market_data[$marketin_obj->get()['mibdid']]['psaid'] = $application->get_id();
                    $market_data[$marketin_obj->get()['mibdid']]['psid'] = $application->get_segment()->get_id();
                }
            }
        }
        $dimensionalize_ob = new DimentionalData();
        $mireportdata['dimension'] = $dimensionalize_ob->set_dimensions($mireportdata['dimension']);
        $dimensionalize_ob->set_requiredfields($marketdata_indexes);
        $dimensionalize_ob->set_data($market_data);

        $overwrite = array('mktSharePerc' => array('fields' => array('divider' => 'mktShareQty', 'dividedby' => 'potential'), 'operation' => '/'),
                'uniPrice' => array('fields' => array('divider' => 'mktShareQty', 'dividedby' => 'potential'), 'operation' => '/'));

        $formats = array('mktSharePerc' => array('style' => NumberFormatter::PERCENT, 'pattern' => '#0.##'));

        $parsed_dimension = $dimensionalize_ob->get_output(array('outputtype' => 'table', 'noenclosingtags' => true, 'formats' => $formats, 'overwritecalculation' => $overwrite));
        $headers_title = $dimensionalize_ob->get_requiredfields();
        foreach($headers_title as $report_header => $header_data) {
            $header_data = strtolower($header_data);
            $dimension_head .= '<th>'.$lang->{$header_data}.'</th>';
        }
        eval("\$mireport_output = \"".$template->get('dimensionalreport_section')."\";");
    }
    else {
        $mireport_output = $lang->nomatchfound;
        // redirect($_SERVER['HTTP_REFERER'], 2, $lang->nomatchfound);
    }
    /* get Market intellgence baisc Data  --END */
    output_xml('<status></status><message><![CDATA['.$mireport_output.']]></message>');
}
?>
