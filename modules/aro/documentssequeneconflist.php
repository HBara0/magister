<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: documentsequeneconflist.php
 * Created:        @rasha.aboushakra    Feb 17, 2015 | 9:45:09 AM
 * Last Update:    @rasha.aboushakra    Feb 17, 2015 | 9:45:09 AM
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
            'parse' => array('filters' => array('affid', 'coid', 'ptid', 'effectiveFrom', 'effectiveTo'),
                    'overwriteField' => array('ptid' => parse_selectlist('filters[ptid]', '', $purchasetypes, $core->input['filters']['ptid'], '', '', array('placeholder' => 'select purchase type')),
                            'coid' => '<input id="countries_1_autocomplete" name="filters[country]" autocomplete="off" type="text" style="width:150px;" value="'.$core->input['filters']['country'].'">
                            <input id="countries_1_id" name="filters[coid]"  type="hidden">'
                    ),
                    'fieldsSequence' => array('affid' => 1, 'coid' => 2, 'ptid' => 3, 'effectiveFrom' => 4, 'effectiveTo' => 5)
            ),
            'process' => array(
                    'filterKey' => 'adsid',
                    'mainTable' => array(
                            'name' => 'aro_documentsequences',
                            'filters' => array('affid' => array('operatorType' => 'multiple', 'name' => 'affid'), 'coid' => array('operatorType' => 'equal', 'name' => 'coid'), 'ptid' => array('operatorType' => 'equal', 'name' => 'ptid'), 'effectiveFrom' => array('operatorType' => 'date', 'name' => 'effectiveFrom'), 'effectiveTo' => array('operatorType' => 'date', 'name' => 'effectiveTo')),
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
                'order' => array('by' => 'createdOn', 'sort' => 'DESC'),
                'returnarray' => true,
                'operators' => array('affid' => 'IN')
        );
    }
    $arodocumentsseqconf = AroDocumentsSequenceConf::get_data(array('affid' => array_keys($affiliates)), $dal_config);
    if(!empty($filter_where)) {
        $arodocumentsseqconf = AroDocumentsSequenceConf::get_data($filter_where, array('returnarray' => true, 'simple' => false));
    }

    if(is_array($arodocumentsseqconf)) {
        foreach($arodocumentsseqconf as $arodocumentconf) {
            $row_tools = '<a href="index.php?module=aro/arodocumentsequeneconf&id='.$arodocumentconf->adsid.'" title="'.$lang->edit.'"><img src="./images/icons/edit.gif" border=0 alt="'.$lang->edit.'"/></a>';
            $row_tools .= " <a href='#{$arodocumentconf->adsid}' id='deletedocumentsequenceconf_{$arodocumentconf->adsid}_aro/documentssequeneconflist_loadpopupbyid'><img src='{$core->settings[rootdir]}/images/invalid.gif' border='0' alt='{$lang->delete}' /></a>";
            if($arodocumentconf->effectiveTo < TIME_NOW) {
                $rowclass = 'unapproved';
            }
            $arodocumentconf->effectiveTo = date($core->settings['dateformat'], $arodocumentconf->effectiveTo);
            $arodocumentconf->effectiveFrom = date($core->settings['dateformat'], $arodocumentconf->effectiveFrom);
            $affiliate = new Affiliates($arodocumentconf->affid);
            $purchasetype = new PurchaseTypes($arodocumentconf->ptid);
            $arodocumentconf->affid = $affiliate->get_displayname();
            $arodocumentconf->ptid = $purchasetype->get_displayname();
            $country_obj = Countries::get_data(array('coid' => $arodocumentconf->coid));
            $country_output = '-';
            if(is_object($country_obj)) {
                $country_output = $country_obj->get_displayname();
            }
            eval("\$documentsequenceconf_rows .= \"".$template->get('aro_documentssequenceconf_row')."\";");
            $row_tools = $rowclass = '';
        }
    }
    else {
        $documentsequenceconf_rows = '<tr><td colspan="5">'.$lang->na.'</td></tr>';
    }
    eval("\$documentsequenceconf_list = \"".$template->get('aro_documentssequenceconflist')."\";");
    output_page($documentsequenceconf_list);
}
else {
    if($core->input['action'] == 'perform_deletedocumentsequenceconf') {
        $arodocumentseqconf = new AroDocumentsSequenceConf($db->escape_string($core->input['todelete']));
        $arodocumentseqconf->delete();
        if($arodocumentseqconf->delete()) {
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
            exit;
        }
    }
    elseif($core->input['action'] == 'get_deletedocumentsequenceconf') {
        eval("\$deletearodocseqconf = \"".$template->get('popup_deletedocumentsequenceconf')."\";");
        output($deletearodocseqconf);
    }
}