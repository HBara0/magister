<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: approvalchainspolicies_list.php
 * Created:        @tony.assaad    Feb 4, 2015 | 4:47:15 PM
 * Last Update:    @tony.assaad    Feb 4, 2015 | 4:47:15 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['aro_canManageApprovalPolicies'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {

    /* Advanced filter search */
    $purchasetypes = PurchaseTypes::get_data('', array('returnarray' => true));

    $filters_config = array(
            'parse' => array('filters' => array('affid', 'effectiveFrom', 'effectiveTo', 'purchaseType'),
                    'overwriteField' => array('purchaseType' => parse_selectlist('filters[purchaseType]', '', $purchasetypes, $core->input['filters']['purchaseType'], '', '', array('placeholder' => 'select purchase type')), 'isActive' => parse_selectlist('filters[isActive]', '', array('' => '', '0' => 'Not active', '1' => 'Active'), $core->input['filters']['isActive']),
                    ),
                    'fieldsSequence' => array('affid' => 1, 'effectiveFrom' => 2, 'effectiveTo' => 3, 'purchaseType' => 4)
            ),
            'process' => array(
                    'filterKey' => 'aapcid',
                    'mainTable' => array(
                            'name' => 'aro_approvalchain_policies',
                            'filters' => array('affid' => array('operatorType' => 'multiple', 'name' => 'affid'), 'effectiveFrom' => array('operatorType' => 'date', 'name' => 'effectiveFrom'), 'effectiveTo' => array('operatorType' => 'date', 'name' => 'effectiveTo'), 'purchaseType' => array('operatorType' => 'equal', 'name' => 'purchaseType')),
                    ),
    ));
    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        if($filters_config['process']['filterKey'] == 'apid') {
            $filters_config['process']['filterKey'] = 'apid';
        }
        $filter_where = ' '.$filters_config['process']['filterKey'].' IN ('.implode(', ', $filter_where_values).')';
    }
    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
    /* Advanced filter search -END */



    $sort_url = sort_url();

    if(isset($core->input['sortby']) && !empty($core->input['sortby'])) {
        $dal_config = array(
                'simple' => false,
                'order' => array('by' => $core->input['sortby'], 'sort' => $core->input['order']),
                'returnarray' => true
        );
    }
    else {
        $dal_config = array(
                'simple' => false,
                'returnarray' => true
        );
    }
    $aroappr_pol = AroApprovalChainPolicies::get_data('effectiveFrom IS NOT NULL and effectiveTo IS NOT NULL AND affid IN ('.implode(',', $core->user['affiliates']).')', $dal_config);
    if(!empty($filter_where)) {
        $filter_where .='AND effectiveFrom IS NOT NULL and effectiveTo IS NOT NULL AND affid IN ('.implode(',', $core->user['affiliates']).')';
        $aroappr_pol = AroApprovalChainPolicies::get_data($filter_where, $dal_config);
    }
    if(is_array($aroappr_pol)) {
        foreach($aroappr_pol as $approvers) {
            $approvers->effectiveTo = date($core->settings['dateformat'], $approvers->effectiveTo);
            $approvers->effectiveFrom = date($core->settings['dateformat'], $approvers->effectiveFrom);
            $affobj = new Affiliates($approvers->affid);
            $purchasetype_obj = new PurchaseTypes($approvers->purchaseType);

            $row_tools = '<a href="index.php?module=aro/manageapprovalchainspolicies&id='.$approvers->aapcid.'" title="'.$lang->edit.'"><img src=./images/icons/edit.gif border=0 alt='.$lang->edit.'/></a>';
            $row_tools .= ' <a href="#'.$approvers->aapcid.'" id="deletepolicy_'.$approvers->aapcid.'_aro/approvalchainspolicieslist_loadpopupbyid" rel="delete_'.$approvers->aapcid.'" title="'.$lang->delete.'"><img src="./images/invalid.gif" alt="'.$lang->delete.'" border="0"></a>';

            eval("\$policies_approverpolicieslistrow .= \"".$template->get('aro_warehouses_approverpolicies_list_rows')."\";");
        }
    }

    eval("\$aro_warehousesapppolicieslist = \"".$template->get('aro_warehouses_approverpolicies_list')."\";");
    output_page($aro_warehousesapppolicieslist);
}
elseif($core->input['action'] == 'get_deletepolicy') {
    eval("\$deletebox = \"".$template->get('popup_aro_deleteapprovalpolicy')."\";");
    output($deletebox);
}
elseif($core->input['action'] == 'perform_deletepolicy') {
    $core->input['todelelete'] = explode('_', $core->input['todelelete']);
    $areotodel = new AroApprovalChainPolicies($core->input['todelelete'][0]);
    if(is_object($areotodel)) {
        if($areotodel->delete()) {
            output_xml('<status>true</status><message>'.$lang->successfullydeleted.'</message>');
        }
    }
}