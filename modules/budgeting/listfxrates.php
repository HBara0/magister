<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: listfxrates.php
 * Created:        @tony.assaad    Nov 18, 2014 | 5:53:43 PM
 * Last Update:    @tony.assaad    Nov 18, 2014 | 5:53:43 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['budgeting_canFillFinBudgets'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {

    if($core->usergroup['budgeting_canFillFinBudgets'] == 1) {
        $create_tool = '<div style="float:right;">  <a href="#" id="showpopup_createbudgetfxrate" class="showpopup"><img alt="Add" src="./images/addnew.png" border="0">'.$lang->createfxrate.'</a>     </div>';
    }
    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('affid', 'year', 'fromCurrency', 'toCurrency', 'rate')
            ),
            'process' => array(
                    'filterKey' => 'bfxid',
                    'mainTable' => array(
                            'name' => 'budgeting_fxrates',
                            'filters' => array('affid', 'year', 'fromCurrency', 'toCurrency', 'rate')
                    ),
            )
    );
    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();

    $filters_row_display = 'hide';
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        if($filters_config['process']['filterKey'] == 'bfxid') {
            $filters_config['process']['filterKey'] = 'bfxid';
        }
        $filter_where = ' '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }
    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));

    foreach($core->user['affiliates'] as $affid) {
        /* get fxrate for each affiliate */
        if(!empty($filter_where)) { /* filter advaned search */
            $budgetrate['filter']['bfxid'] = $filter_where;
        }

        $affilaite_budgetrateobjs = BudgetFxRates::get_data(array('affid' => $affid), array('simple' => false, 'returnarray' => true));
        $row_tools = '';
        if(is_array($affilaite_budgetrateobjs)) {
            foreach($affilaite_budgetrateobjs as $fxrate) {
                $affiliate = new Affiliates($fxrate->affid);
                $fromcurrency = new Currencies($fxrate->fromCurrency);
                $tocurrency = new Currencies($fxrate->toCurrency);
                $row_tools .= ' <a href="#'.$fxrate->bfxid.'" id="deleterate_'.$fxrate->bfxid.'_budgeting/listfxrates_loadpopupbyid" rel = "delete_'.$fxrate->bfxid.'" title = "'.$lang->delete.'"><img src = "'.$core->settings['rootdir'].'/images/invalid.gif" alt = "'.$lang->delete.'" border = "0"></a>';
                $row_tools .= ' <a href="#'.$fxrate->bfxid.'" id="updaterate_'.$fxrate->bfxid.'_budgeting/listfxrates_loadpopupbyid" rel = "update_'.$fxrate->bfxid.'" title = "'.$lang->delete.'"><img src = "'.$core->settings['rootdir'].'/images/icons/edit.gif" alt = "'.$lang->delete.'" border = "0"></a>';

                $ratecategories = array('isActual', 'isYef', 'isBudget');
                foreach($ratecategories as $ratecategory) {
                    if(isset($fxrate->$ratecategory) && !empty($fxrate->$ratecategory)) {
                        $fxrate->category_output = $lang->{strtolower($ratecategory)};
                        break;
                    }
                }
                eval("\$budgetfxratess_list .= \"".$template->get('budgeting_listfxrates_rows')."\";");
                $row_tools = '';
            }
        }
    }

    /* Crate rates popup interface */
    $years = array_combine(range(date('Y') - 2, date('Y') + 1), range(date('Y') - 2, date('Y') + 1));
    foreach($years as $year) {
        $year_selected = '';
        if($year == $years[date('Y')] + 1) {
            $year_selected = 'selected = "selected"';
        }
        $budget_years .= "<option value=".$year."  {$year_selected}>{$year}</option>";
    }

    $aff_objs = Affiliates::get_affiliates(array('affid' => $core->user['affiliates']), array('returnarray' => true, 'operators' => array('affid' => 'IN')));
    if(is_array($aff_objs)) {
        $affiliate_list = parse_selectlist('budgetrate[affid]', 1, $aff_objs, $core->user['mainaffilaite']);
    }

    $currency['filter']['numCode'] = 'SELECT mainCurrency FROM countries where affid IS NOT NULL';
    $curr_objs = Currencies::get_data($currency['filter'], array('returnarray' => true, 'operators' => array('numCode' => 'IN')));
    $curr_objs[840] = new Currencies(840);
    $fromcurr_list = parse_selectlist('budgetrate[fromCurrency]', 4, $curr_objs, '840');
    $tocurr_list = parse_selectlist('budgetrate[toCurrency]', 4, $curr_objs, '840');


    $popupcreaterate = '';
    $craetereverserate = '<tr> <td>'.$lang->craetereverserate.'</td> <td><input type = "checkbox" name = "budgetrate[createreverserate]" value = "1"/></td></tr>';

    $category['checked']['isBudget'] = 'checked="checked"';
    eval("\$popupcreaterate= \"".$template->get('popup_createbudget_fxrate')."\";");
    eval("\$budgetinglistfxrates = \"".$template->get('budgeting_listfxrates')."\";");
    output_page($budgetinglistfxrates);
}
else if($core->input['action'] == 'get_updaterate') {
    $budgetrate = new BudgetFxRates($core->input['id'], array('simple' => false));

    $aff_objs = Affiliates::get_affiliates(array('affid' => $core->user['affiliates']), array('returnarray' => true, 'operators' => array('affid' => 'IN')));
    if(is_array($aff_objs)) {
        foreach($aff_objs as $affiliate) {
            $affiliates[$affiliate->affid] = $affiliate->get_displayname();
            if($affiliate->affid == $budgetrate->affid) {
                $selectedaff[] = $budgetrate->affid;
            }
        }
        $affiliate_list = parse_selectlist('budgetrate[affid]', 1, $affiliates, $selectedaff, '', '', array('disabledItems' => $affiliates));
    }
    /* Crate rates popup interface */
    $years = array_combine(range(date('Y') - 2, date('Y') + 1), range(date('Y') - 2, date('Y') + 1));
    $disabled = ' disabled = "'.$config['disabled'].'"';
    foreach($years as $year) {
        $year_selected = '';
        if($year == $budgetrate->year) {
            $year_selected = "selected=selected";
        }
        $budget_years .= "<option disabled ='disabled' value=".$year." {$year_selected}>{$year}</option>";
    }
    $disabled = 'disabled="disabled"';
    $ratecategories = array('isActual', 'isYef', 'isBudget');
    foreach($ratecategories as $ratecategory) {
        if(isset($budgetrate->$ratecategory) && !empty($budgetrate->$ratecategory)) {
            $category['checked'][$ratecategory] = " checked='checked'";
        }
    }
    $curr_objs = Currencies::get_data(null, array('returnarray' => true, 'operators' => array('numCode' => 'IN')));

    $fromcurr_list = parse_selectlist('budgetrate[fromCurrency]', 4, $curr_objs, $budgetrate->fromCurrency, '', '', array('disabledItems' => $curr_objs));
    $tocurr_list = parse_selectlist('budgetrate[toCurrency]', 4, $curr_objs, $budgetrate->toCurrency, '', '', array('disabledItems' => $curr_objs));
    eval("\$addrate = \"".$template->get('popup_createbudget_fxrate')."\";");
    output($addrate);
}
elseif($core->input['action'] == 'get_deleterate') {
    $bfxid = $db->escape_string($core->input['id']);
    $budget_rateobj = BudgetFxRates::get_data(array('bfxid' => $bfxid));
    $fromcur_obj = new Currencies($budget_rateobj->fromCurrency);
    $tocur_obj = new Currencies($budget_rateobj->toCurrency);
    eval("\$deleterate = \"".$template->get('popup_budget_delete')."\";");
    output($deleterate);
}
elseif($core->input['action'] == 'do_deleterate') {
    $bfxid = $db->escape_string($core->input['bfxid']);
    $budget_rateobj = BudgetFxRates::get_data(array('bfxid' => $bfxid));
    $budget_rateobj->delete();
    switch($budget_rateobj->get_errorcode()) {
        case 0:
            output_xml('<status>true</status><message>'.$lang->successfullydeleted.'</message>');
            break;
    }
}
elseif($core->input['action'] == 'do_createrate') {
    $budgetrate = $core->input['budgetrate'];
    $budgetfxrate_obj = new BudgetFxRates();
    if(isset($budgetrate['fromCurrency']) && isset($budgetrate['toCurrency'])) {
        if($budgetrate['fromCurrency'] == $budgetrate['toCurrency']) {
            output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
            return;
        }
    }
    if(!isset($budgetrate['rateCategory'])) {
        output_xml('<status>false</status><message>'.$lang->fillrequiredfield.'</message>');
        return;
    }
    $budgetrate['isActual'] = $budgetrate['isBudget'] = $budgetrate['isYef'] = 0;
    $budgetrate[$budgetrate['rateCategory']] = 1;
    $budgetfxrate_obj->set($budgetrate);
    $budgetfxrate_obj->save();
    switch($budgetfxrate_obj->get_errorcode()) {
        case 0:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 1:
            output_xml('<status>false</status><message>'.$lang->fillrequiredfield.'</message>');
            break;
    }
}