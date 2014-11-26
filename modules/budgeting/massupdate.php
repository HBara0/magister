<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: massupdate.php
 * Created:        @tony.assaad    Nov 24, 2014 | 12:24:22 PM
 * Last Update:    @tony.assaad    Nov 24, 2014 | 12:24:22 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['budgeting_canMassUpdate'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {
    if($core->usergroup['canViewAllAff'] == 0) {
        foreach($core->user['affiliates'] as $affid) {
            $aff_objs[$affid] = new Affiliates($affid);
        }
    }
    else {
        $aff_objs = Affiliates::get_affiliates('name IS NOT NULL');
    }
    foreach($aff_objs as $affiliate) {
        $affiliates_list .= '<td><input name="budget[filter][affid][]" type="checkbox"'.$checked.' value="'.$affiliate->affid.'">'.$affiliate->get_displayname().'</td></tr>';
    }
//    if($core->usergroup['canViewAllSupp'] == 0) {
//        foreach($core->user['suppliers']['eid'] as $suplier) {
//            $supplier_obj[$suplier] = new Entities($suplier);
//        }
//    }
    // else {
    $supplier_obj = Entities::get_data(array('isActive' => 1, 'approved' => 1, 'type' => 's'));
    // }
    foreach($supplier_obj as $supplier) {
        $suppliers_list .= '<tr class="'.$rowclass.'">';
        $suppliers_list .= '<td><input name="budget[filter][spid][]" type="checkbox"'.$checked.' value="'.$supplier->eid.'">'.$supplier->get_displayname().'</td><tr>';
    }

    $years = Budgets::get_availableyears();
    if(is_array($years)) {
        foreach($years as $key => $value) {
            $checked = $rowclass = '';
            $budget_year_list .= '<tr class="'.$rowclass.'">';
            $budget_year_list .= '<td><input name="budget[filter][year][]" required="required" type="checkbox" value="'.$key.'">'.$value.'</td></tr>';
        }
    }
    /* Can Generate users of the affiliates he belongs to */

    if(is_array($core->user['affiliates'])) {
        foreach($core->user['affiliates'] as $auditaffid) {
            $aff_obj = new Affiliates($auditaffid);
            $affiliate_users = $aff_obj->get_all_users();
            foreach($affiliate_users as $aff_businessmgr) {
                $business_managers[$aff_businessmgr['uid']] = $aff_businessmgr['displayName'];
            }
        }
    }
//    else {
//        if($core->usergroup['canViewAllEmp'] == 1) {
//            $affiliate = new Affiliates($core->user['mainaffiliate']);
//            $business_managers = $affiliate->get_all_users(array('displaynameonly' => true, 'customfilter' => 'u.uid IN (SELECT DISTINCT(users_usergroups.uid) FROM users_usergroups WHERE gid IN (SELECT usergroups.gid FROM usergroups WHERE budgeting_canMassUpdate=1))'));
//        }
//        else {
//            $business_managers[$core->user['uid']] = $core->user['displayName'];
//        }
//    }

    if(is_array($business_managers)) {
        foreach($business_managers as $key => $value) {
            $checked = $rowclass = '';
            $business_managerslist .= '<tr class="'.$rowclass.'">';
            $business_managerslist .= '<td><input name="budget[filterline][businessMgr][]" type="checkbox"'.$checked.' value="'.$key.'"/>'.$value.'</td></tr>';
        }
    }
    $saletype_objs = SaleTypes::get_data();
    if(is_array($saletype_objs)) {
        foreach($saletype_objs as $key => $value) {
            $checked = $rowclass = '';
            $sale_types .= '<tr class="'.$rowclass.'">';
            $sale_types .= '<td><input name="budget[filterline][saleType][]" type="checkbox" value="'.$key.'">'.$value.'</td></tr>';
        }
    }
    $user = new Users($core->user['uid']);
    $user_segments_objs = $user->get_segments();

    /*  configuration array for the Values to Overwrite: */
    $overwrites_fields = array('businessMgr' => array('inputfield' => parse_selectlist('budget[overwrite][value][businessMgr]', 0, $business_managers, '', '', '', array('blankstart' => true, 'id' => 'businessMgr_'))),
            'purchasingEntity' => array('inputfield' => '<input type="text" placeholder="'.$lang->affiliate.'"  size="20" id="affiliate_pe" name="budget[overwrite][value][purchasingEntity]"    autocomplete="off" />'),
            'purchasingEntityId' => array('inputfield' => '<input type="text" placeholder="'.$lang->search.' '.$lang->affiliate.'" id=affiliate_peid_autocomplete name=""    autocomplete="off" /><input type="hidden" value=" " id="affiliate_peid_id" name="budget[overwrite][value][purchasingEntityId]"/>'),
            'localIncomePercentage' => array('inputfield' => '<input name="budget[overwrite][value][localIncomePercentage]"  value="" type="text" id="localincomeper_'.$rowid.'" size="15" accept="numeric"  />'),
            'commissionSplitAffid' => array('inputfield' => parse_selectlist('budget[overwrite][value][commissionSplitAffid]', 0, $aff_objs, '', '', '', array('blankstart' => true, 'id' => 'commissionsplitaffid_')))
            //'Segment' => array('inputfield' => parse_selectlist('budget[overwrite][segment]', 0, $user_segments_objs, '', '', '', array('blankstart' => true, 'id' => 'segment_')))
    );

    foreach($overwrites_fields as $attr => $field) {
        $overwrite_fields .= '<tr><td><input name="budget[overwrite][attribute]['.$attr.']" type="checkbox" id="column_'.$attr.'" value="1"/>'.$attr.'</td>';
        $overwrite_fields .= '<td><div id="value_'.$attr.'" style="display:block;">'.$field['inputfield'].'</div></td>';
        $overwrite_fields .= '</tr>';
    }

    eval("\$massupdate = \"".$template->get('budgeting_massupdate')."\";");
    output_page($massupdate);
}
else {
    if($core->input['action'] == 'do_massupdate') {



        $budgetfilter_where = $core->input['budget']['filter'];

        $filterline_where = $core->input['budget']['filterline'];
        $attribute = ($core->input['budget']['overwrite']['attribute']);
        unset($core->input['budget']['overwrite']['attribute']);
        $overwrite_fields = $core->input['budget']['overwrite'];

        $checkfilter_array = array('affid', 'spid', 'year');
        foreach($checkfilter_array as $filterval) {
            if(empty($budgetfilter_where[$filterval])) {
                output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
                exit;
            }
        }

        $checkfilterline_array = array('businessMgr', 'saleType');
        foreach($checkfilterline_array as $filterval) {
            if(empty($filterline_where[$filterval])) {
                output_xml('<status>false</status><message>'.$lang->fillrequiredfields.' '.$filterval.'</message>');
                exit;
            }
        }

        /* acquire all rows which will be affected, */
        //error($lang->sprint($lang->noexchangerate, $budgetline->originalCurrency, $budgetsdata['toCurrency'], $budget_obj->year), $_SERVER['HTTP_REFERER']);
        $budgetobjs = Budgets::get_data(array('affid' => $budgetfilter_where['affid'], 'spid ' => $budgetfilter_where['spid'], 'year ' => $budgetfilter_where['year']), array('returnarray' => true, 'simple' => false, 'operators' => array('affid' => 'IN', 'spid' => 'IN', 'year' => 'IN')));
        foreach($budgetobjs as $budgetobj) {
            $budgetlines_notaffectedobjs = BudgetLines::get_data('bid='.$budgetobj->bid, array('returnarray' => true));
        }

        $overwrites_fieldstocheck = array('businessMgr',
                'purchasingEntity',
                'purchasingEntityId',
                'localIncomePercentage',
                'commissionSplitAffid'
        );
        if(is_array($overwrites_fieldstocheck)) {
            foreach($overwrites_fieldstocheck as $attrfields) {
                if(!isset($attribute[$attrfields])) {
                    unset($overwrite_fields['value'][$attrfields]);
                }
            }
        }
        if(is_array($budgetfilter_where)) {
            $budget_wherecondition = ' WHERE ';
            foreach($budgetfilter_where as $attr => $filter) {
                if(is_array($filter)) {
                    $budget_wherecondition .= $and.$attr.' IN ('.implode(',', $filter).')';
                    $and = ' AND ';
                    unset($budget_where);
                }
            }
        }

        /* filter budget lines */
        if(is_array($filterline_where)) {
            $and = ' AND ';
            foreach($filterline_where as $attr => $filterline) {
                if(is_array($filterline)) {
                    $budgetline_wherecondition .= $and.$attr.' IN ('.implode(',', $filterline).')';
                }
            }
        }



        if(isset($overwrite_fields['value']['localIncomePercentage']) && !empty($overwrite_fields['value']['localIncomePercentage'])) {
            $overwrite_fields['value'] = array('localIncomeAmount' => '(amount * ('.$overwrite_fields['value']['localIncomePercentage'].' / 100))',
                    'localIncomePercentage' => $overwrite_fields['value']['localIncomePercentage'],
                    'invoicingEntityIncome' => '(amount * ((incomePerc - '.$overwrite_fields['value']['localIncomePercentage'].') / 100))',
                    'businessMgr' => intval($overwrite_fields['value']['businessMgr']),
                    'purchasingEntity' => '\''.$overwrite_fields['value']['purchasingEntity'].'\'',
                    'purchasingEntityId' => $overwrite_fields['value']['purchasingEntityId'],
                    'commissionSplitAffid' => $overwrite_fields['value']['commissionSplitAffid'],
            );
        }
        $overwrite_fields['value']['modifiedOn'] = TIME_NOW;
        $overwrite_fields['value']['modifiedBy'] = $core->user['uid'];

        foreach($overwrite_fields['value'] as $column => $colvalue) {
            if(empty($colvalue) && $colvalue != "0") {
                continue;
            }
            $updatequery_set .= $comma.$column.'='.$colvalue;
            $comma = ', ';
        }

        $query = $db->query('UPDATE '.Tprefix.'budgeting_budgets_lines SET '.$updatequery_set.' WHERE bid IN (SELECT bid FROM budgeting_budgets '.$budget_wherecondition.')'.$budgetline_wherecondition);
        if($query) {

            output_xml('<status>true</status><message>'.$lang->successfullysaved.' '.$db->affected_rows().' lines.</message>');
        }
    }
}
