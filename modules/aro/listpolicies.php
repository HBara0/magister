<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: listpolicies.php
 * Created:        @rasha.aboushakra    Feb 4, 2015 | 12:44:23 PM
 * Last Update:    @rasha.aboushakra    Feb 4, 2015 | 12:44:23 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['aro_canManagePolicies'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    /* Advanced filter search */
    $purchasetypes = PurchaseTypes::get_data('', array('returnarray' => true));

    $filters_config = array(
            'parse' => array('filters' => array('affid', 'coid', 'purchaseType', 'effectiveFrom', 'effectiveTo', 'isActive'),
                    'overwriteField' => array('purchaseType' => parse_selectlist('filters[purchaseType]', '', $purchasetypes, $core->input['filters']['purchaseType'], '', '', array('placeholder' => 'select purchase type')),
                            'coid' => '<input id="countries_1_autocomplete" name="filters[country]" autocomplete="off" type="text" style="width:150px;" value="'.$core->input['filters']['country'].'">
                            <input id="countries_1_id" name="filters[coid]"  type="hidden">'
                            , 'isActive' => parse_selectlist('filters[isActive]', '', array('' => '', '0' => 'Not active', '1' => 'Active'), $core->input['filters']['isActive'])),
                    'fieldsSequence' => array('affid' => 1, 'coid' => 2, 'purchaseType' => 3, 'effectiveFrom' => 4, 'effectiveTo' => 5, 'isActive' => 6)
            ),
            'process' => array(
                    'filterKey' => 'apid',
                    'mainTable' => array(
                            'name' => 'aro_policies',
                            'filters' => array('affid' => array('operatorType' => 'multiple', 'name' => 'affid'), 'coid' => array('operatorType' => 'equal', 'name' => 'coid'), 'purchaseType' => array('operatorType' => 'equal', 'name' => 'purchaseType'), 'effectiveFrom' => array('operatorType' => 'date', 'name' => 'effectiveFrom'), 'effectiveTo' => array('operatorType' => 'date', 'name' => 'effectiveTo'), 'isActive' => array('operatorType' => 'equal', 'name' => 'isActive')),
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
                'operators' => array('affid' => 'IN')
        );
    }
    else {
        $dal_config = array(
                'returnarray' => true,
                'operators' => array('affid' => 'IN')
        );
    }
    $aropolicies = AroPolicies::get_data(array('affid' => array_keys($affiliates)), $dal_config);
    if(!empty($filter_where)) {
        $aropolicies = AroPolicies::get_data($filter_where, array('returnarray' => true));
    }

    if(is_array($aropolicies)) {
        foreach($aropolicies as $policy) {
            if($policy->effectiveTo < TIME_NOW) {
                $rowclass = 'unapproved';
            }
            $row_tools = '<a href="index.php?module=aro/managepolicies&id='.$policy->apid.'" title="'.$lang->edit.'"><img src="./images/icons/edit.gif" border=0 alt="'.$lang->edit.'"/></a>';
            $row_tools .= "<a href='#{$policy->apid}' id='deletearopolicy_{$policy->apid}_aro/listpolicies_loadpopupbyid' ><img src='{$core->settings[rootdir]}/images/invalid.gif' border='0' alt='{$lang->deletearopolicy}' /></a>";
            $policy->effectiveTo = date($core->settings['dateformat'], $policy->effectiveTo);
            $policy->effectiveFrom = date($core->settings['dateformat'], $policy->effectiveFrom);
            $affiliate = new Affiliates($policy->affid);
            $purchasetype = new PurchaseTypes($policy->purchaseType);
            $policy->affid = $affiliate->get_displayname();
            $policy->purchaseType = $purchasetype->get_displayname();
            $policy->isactveicon = '<img src="./images/false.gif" />';
            if($policy->isActive == 1) {
                $policy->isactveicon = '<img src="./images/true.gif" />';
            }
            $country_obj = Countries::get_data(array('coid' => $policy->coid));
            $country_output = '-';
            if(is_object($country_obj)) {
                $country_output = $country_obj->get_displayname();
            }
            $rowclass = alt_row($rowclass);
            eval("\$aropolicies_rows .= \"".$template->get('aro_policieslist_row')."\";");
            $row_tools = $rowclass = '';
        }
    }
    else {
        $aropolicies_rows = '<tr><td colspan="6">'.$lang->na.'</td></tr>';
    }
    eval("\$aro_policieslist = \"".$template->get('aro_policieslist')."\";");
    output_page($aro_policieslist);
}
else {
    if($core->input['action'] == 'perform_deletearopolicy') {
        $aropolicy = new AroPolicies($db->escape_string($core->input['todelete']));
        $aropolicy->delete();
        if($aropolicy->delete()) {
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
            exit;
        }
    }
    elseif($core->input['action'] == 'get_deletearopolicy') {
        eval("\$deletearopolicybox = \"".$template->get('popup_deletearopolicy')."\";");
        output($deletearopolicybox);
    }
}