<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: warehousespolicieslist.php
 * Created:        @tony.assaad    Feb 3, 2015 | 2:08:22 PM
 * Last Update:    @tony.assaad    Feb 3, 2015 | 2:08:22 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['aro_canManageWarehousePolicies'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {


    /* Perform inline filtering - START */
    $warhouses = Warehouses::get_data(array('affid' => $core->user['affiliates']), array('returnarray' => true, 'operators' => array('affid' => 'IN')));
    $filters_config = array(
            'parse' => array('filters' => array('warehouse', 'fromDate', 'toDate'),
                    'overwriteField' => array('warehouse' => parse_selectlist('filters[warehouse]', 1, $warhouses, '', 0, '', array('blankstart' => true)),),
            ),
            'process' => array(
                    'filterKey' => 'awpid',
                    'mainTable' => array(
                            'name' => 'aro_wareshouses_policies',
                            'filters' => array('warehouse' => array('operatorType' => 'equal', 'name' => 'warehouse'), 'fromDate' => array('operatorType' => 'date', 'name' => 'effectiveFrom'), 'toDate' => array('operatorType' => 'date', 'name' => 'effectiveTo')),
                    )
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();

    $filters_row_display = 'hide';
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        if($filters_config['process']['filterKey'] == 'awpid') {
            $filters_config['process']['filterKey'] = 'awpid';
        }
        $filter_where = $filters_config['process']['filterKey'].' IN ('.implode(', ', $filter_where_values).')';
        $multipage_where .= $filters_config['process']['filterKey'].' IN ('.implode(', ', $filter_where_values).')';
    }

    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));

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


    $warehouse_objs = Warehouses::get_data(array('affid' => $core->user['affiliates'], 'isActive' => 1), array('returnarray' => true));
    if(is_array($warehouse_objs)) {
        $warehousefilter_where = ' warehouse IN ('.implode(',', array_keys($warehouse_objs)).')';
    }
    else {
        $warehousefilter_where = ' warehouse IN (0)';
    }
    if(!empty($filter_where)) {
        if(!empty($warehousefilter_where)) {
            $warehousefilter_where .= ' AND ';
        }
        $warehousefilter_where .=$filter_where;
    }

    $aroobjs = AroManageWarehousesPolicies::get_data($warehousefilter_where, $dal_config);

    if(is_array($aroobjs)) {
        foreach($aroobjs as $aro) {
            if($aro->effectiveTo < TIME_NOW) {
                $rowclass = 'unapproved';
            }
            $aro->effectiveTo = date($core->settings['dateformat'], $aro->effectiveTo);
            $aro->effectiveFrom = date($core->settings['dateformat'], $aro->effectiveFrom);
            $warehouse = new Warehouses($aro->warehouse);
            $aro->warehouse = $warehouse->get_displayname();
            $row_tools = '<a href = index.php?module=aro/managewarehousepolicies&id='.$aro->awpid.' title = "'.$lang->edit.'"><img src ="./images/icons/edit.gif" border = 0 alt = '.$lang->edit.'/></a>';
            $row_tools .= ' <a href = "#'.$aro->awpid.'" id = "deletepolicy_'.$aro->awpid.'_aro/warehousespolicieslist_loadpopupbyid" rel = "delete_'.$aro->awpid.'" title = "'.$lang->delete.'"><img src="./images/invalid.gif" alt = "'.$lang->delete.'" border = "0"></a>';
            eval("\$policies_listrow .= \"".$template->get('aro_warehouses_policies_list_rows')."\";");
            $rowclass = '';
        }
    }

    eval("\$aro_warehousespolicieslist = \"".$template->get('aro_warehouses_policies_list')."\";");
    output_page($aro_warehousespolicieslist);
}
elseif($core->input['action'] == 'get_deletepolicy') {
    eval("\$deletebox = \"".$template->get('popup_aro_deletewarehousepolicy')."\";");
    output($deletebox);
}
elseif($core->input['action'] == 'perform_deletepolicy') {
    $areotodel = new AroManageWarehousesPolicies($core->input[todelelete]);
    if(is_object($areotodel)) {
        if($areotodel->delete()) {
            output_xml('<status>true</status><message>'.$lang->successfullydeleted.'</message>');
        }
    }
}