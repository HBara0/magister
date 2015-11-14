<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: lockbudgets.php
 * Created:        @rasha.aboushakra    Oct 27, 2015 | 2:17:49 PM
 * Last Update:    @rasha.aboushakra    Oct 27, 2015 | 2:17:49 PM
 */
if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['budgeting_canFinalizeBudgets'] == 0) {
    error($lang->sectionnopermission);
}

$session->start_phpsession();

if(!$core->input['action']) {

    $permissions = $core->user_obj->get_businesspermissions();

    if(is_array($permissions['affid'])) {
        $affiliate_where = "affid IN (".implode(',', $permissions['affid']).")";
    }
    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, "{$affiliate_where}");
    if(is_array($affiliates)) {
        foreach($affiliates as $key => $value) {
            if($key == 0) {
                continue;
            }
            $checked = $rowclass = '';
            $affiliates_list .='<tr class="'.$rowclass.'">';
            $affiliates_list .='<td><input id="affiliatefilter_check_'.$key.'" name="budget[affiliates][]"  type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td></tr>';
        }
    }

    $supplier_where = " type='s'";
    if(is_array($permissions['spid'])) {
        $supplier_where .= "AND eid IN(".implode(',', $permissions['spid']).")";
    }
    $suppliers = get_specificdata('entities', array('eid', 'companyName'), 'eid', 'companyName', array('by' => 'companyName', 'sort' => 'ASC'), 1, "{$supplier_where}");
    if(is_array($suppliers)) {
        foreach($suppliers as $key => $value) {
            if($key == 0) {
                continue;
            }
            $checked = $rowclass = '';
            $suppliers_list .= ' <tr class="'.$rowclass.'">';
            $suppliers_list .= '<td><input id="supplierfilter_check_'.$key.'" name="budget[suppliers][]" type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td><tr>';
        }
    }


    $years = Budgets::get_availableyears();
    if(is_array($years)) {
        foreach($years as $key => $value) {
            $checked = $rowclass = '';
            $budget_year_list .= '<tr class="'.$rowclass.'">';
            $budget_year_list .= '<td><input name="budget[years]"  required="required" type="radio" value="'.$key.'">'.$value.'</td></tr>';
        }
    }

    $budgettypes = array('budget' => 'Budget', 'yef' => 'Year End Foreast');
    if(is_array($budgettypes)) {
        foreach($budgettypes as $key => $type) {
            $checked = $rowclass = '';
            $budget_type_list .= '<tr class="'.$rowclass.'">';
            $budget_type_list .= '<td><input name="budget[type]"  required="required" type="radio" value="'.$key.'">'.$type.'</td></tr>';
        }
    }

    $operations = array('lock' => $lang->lock, 'unlock' => $lang->unlock);
    if(is_array($operations)) {
        foreach($operations as $key => $type) {
            $checked = $rowclass = '';
            $operation_type_list .= '<tr class="'.$rowclass.'">';
            $operation_type_list .= '<td><input name="budget[operation]"  required="required" type="radio" value="'.$key.'">'.$type.'</td></tr>';
        }
    }

    eval("\$budgetlock = \"".$template->get('budgeting_lock')."\";");
    output_page($budgetlock);
}
elseif($core->input['action'] == 'do_perform_lockbudgets') {
    unset($core->input['module'], $core->input['action']);
    $budget_data = $core->input['budget'];
    $dal_config = array(
            'operators' => array('affid' => 'IN', 'spid' => 'IN'),
            'returnarray' => true,
            'simple' => false
    );
    $filters = array('affid' => 'affiliates', 'spid' => 'suppliers', 'year' => 'years');
    foreach($filters as $key => $filter) {
        if(isset($budget_data[$filter]) && !empty($budget_data[$filter])) {
            $where[$key] = $budget_data[$filter];
        }
    }
    if($budget_data['type'] == 'budget') {
        $budgets = Budgets::get_data($where, $dal_config);
    }
    elseif($budget_data['type'] == 'yef') {
        $budgets = BudgetingYearEndForecast::get_data($where, $dal_config);
    }
    $operation = $budget_data['operation'];
    if(is_array($budgets)) {
        foreach($budgets as $budget) {
            $budget->lockbudget($operation);
            $errorcode = $budget->get_errorcode();
            if($errorcode != 0) {
                output_xml('<status>true</status><message>'.$lang->error.'</message>');
                exit;
            }
        }

        switch($errorcode) {
            case 0:
            case 1:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 2:
                output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
                break;
        }
    }
}
?>
