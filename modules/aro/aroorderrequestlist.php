<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: arodocumentsequencelist.php
 * Created:        @hussein.barakat    Mar 3, 2015 | 2:42:00 PM
 * Last Update:    @hussein.barakat    Mar 3, 2015 | 2:42:00 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['canUseAro'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    /* Advanced filter search -START */

    $ordertypes = PurchaseTypes::get_data('', array('returnarray' => true));
    $ordercurrency = Currencies::get_data('', array('returnarray' => true));
    $filters_config = array(
            'parse' => array('filters' => array('affid', 'orderType', 'orderReference', 'currency', 'createdOn'),
                    'overwriteField' => array('orderType' => parse_selectlist('filters[orderType]', '', $ordertypes, $core->input['filters']['orderType'], '', '', array('placeholder' => 'Select Order Type')), 'currency' => parse_selectlist('filters[currency]', '', $ordercurrency, $core->input['filters']['currency'], '', '', array('placeholder' => 'Select Currency'))),
                    'fieldsSequence' => array('affid' => 1, 'orderType' => 2, 'orderReference' => 3, 'currency' => 4, 'createdOn' => 5)
            ),
            'process' => array(
                    'filterKey' => 'aorid',
                    'mainTable' => array(
                            'name' => 'aro_requests',
                            'filters' => array('affid' => array('operatorType' => 'multiple', 'name' => 'affid'), 'orderType' => array('operatorType' => 'equal', 'name' => 'orderType'), 'orderReference' => array('single', 'name' => 'orderReference'), 'currency' => array('multiple', 'name' => 'currency'), 'createdOn' => array('operatorType' => 'date', 'name' => 'createdOn')),
                    ),
    ));
    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        if($filters_config['process']['filterKey'] == 'aorid') {
            $filters_config['process']['filterKey'] = 'aorid';
        }
        $filter_where = ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(', ', $filter_where_values).')';
    }
    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
    /* Advanced filter search -END */

    $affiliate_where = ' name LIKE "%orkila%" AND isActive=1';
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = implode(',', $core->user['affiliates']);
        $affiliate_where .= ' AND affid IN ('.$inaffiliates.')';
    }
    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0, $affiliate_where);


    $sort_url = sort_url();

    if(isset($core->input['sortby']) && !empty($core->input['sortby'])) {
        $dal_config = array(
                'order' => array('by' => $core->input['sortby'], 'sort' => $core->input['order']),
                'returnarray' => true,
                'operators' => array('affid' => 'IN'),
                'simple' => false
        );
    }
    else {
        $dal_config = array(
                'returnarray' => true,
                'operators' => array('affid' => 'IN'),
                'simple' => false
        );
    }
    $seperator = '';
    if(is_array($affiliates)) {
        $seperator = ' OR ';
        $filterarray = 'affid IN ('.implode(',', array_keys($affiliates)).')';
    }
    /* get AROs where product's segment is withing current user coordinated segments */

    $segmentsaroids = AroRequests::get_aros_byproductsegments($core->user['uid']);
    if(is_array($segmentsaroids)) {
        $filterarray .= $seperator.' aorid IN ('.implode(',', $segmentsaroids).')';
    }

    $arodocumentrequest = AroRequests::get_data($filterarray.$filter_where, array('returnarray' => true, 'simple' => false));
    if(is_array($arodocumentrequest)) {
        foreach($arodocumentrequest as $documentrequest) {
            $icons['pending'] = "";
            $style = $icons['rejected'] = "";
            $row_tools = '<a href="index.php?module=aro/managearodouments&id='.$documentrequest->aorid.'" title="'.$lang->edit.'"><img src="./images/icons/edit.gif" border=0 alt="'.$lang->edit.'"/></a>';
            $affiliate = new Affiliates($documentrequest->affid);
            $purchasetype = new PurchaseTypes($documentrequest->orderType);
            $buyingcurr = new Currencies($documentrequest->currency);
            $orderrefference = $documentrequest->orderReference;
            $documentrequest->createdOn = date($core->settings['dateformat'], $documentrequest->createdOn);
            $documentrequest->affid = $affiliate->get_displayname();
            $documentrequest->orderType = $purchasetype->get_displayname();
            $documentrequest->currency = $buyingcurr->get_displayname();
            $rowclass = 'trowtools unapproved';
            $approvals = AroRequestsApprovals::get_data(array('aorid' => $documentrequest->aorid, 'isApproved' => 1), array('returnarray' => true));
            if(is_array($approvals)) {
                $rowclass = "trowtools yellowbackground";
            }
            if($documentrequest->isRejected == 1) {
                $style = 'style="color:red;"';
                $rowclass = "";
                $icons['rejected'] = '<span class="glyphicon glyphicon-ban-circle"></span> ';
            }
            if($documentrequest->isFinalized == 1) {
                if($documentrequest->isApproved == 1) {
                    $rowclass = 'trowtools greenbackground';
                }
                else {
                    $approvalobj = $documentrequest->get_nextapprover();
                    if(is_object($approvalobj)) {
                        if($approvalobj->uid == $core->user['uid']) {
                            $icons['pending'] = '<span class="glyphicon glyphicon-exclamation-sign"></span>';
                            $row_tools = '<a href="index.php?module=aro/managearodouments&referrer=toapprove&id='.$documentrequest->aorid.'" title="'.$lang->edit.'"><img src="./images/icons/edit.gif" border=0 alt="'.$lang->edit.'"/></a>';
                        }
                    }
                }
            }

            eval("\$aroorderrequest_rows .= \"".$template->get('aro_orderrequestlist_row')."\";");
            $row_tools = $rowclass = '';
        }
    }
    else {
        $aroorderrequest_rows = '<tr><td colspan="6">'.$lang->na.'</td></tr>';
    }
    eval("\$aro_orderrequestlist = \"".$template->get('aro_orderrequestlist')."\";");
    output_page($aro_orderrequestlist);
}
//else {
//    if($core->input['action'] == 'perform_deletearopolicy') {
//        $arodocument = new AroRequests($db->escape_string($core->input['todelete']));
//        $arodocument->delete();
//        if($arodocument->delete()) {
//            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
//            exit;
//        }
//    }
//    elseif($core->input['action'] == 'get_deletearopolicy') {
//        eval("\$deletearopolicybox = \"".$template->get('popup_deletearopolicy')."\";");
//        output($deletearopolicybox);
//    }
//}