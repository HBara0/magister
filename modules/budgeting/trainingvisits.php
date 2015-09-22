<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: trainingvisits.php
 * Created:        @tony.assaad    Nov 3, 2014 | 9:47:55 AM
 * Last Update:    @tony.assaad    Nov 3, 2014 | 9:47:55 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['budgeting_canFillFinBudgets'] == 0) {
    error($lang->sectionnopermission);
}

if(!isset($core->input['action'])) {
    if(isset($core->input['financialbudget']['year']) && !empty($core->input['financialbudget']['year'])) {
        $financialbudget_year = $core->input['financialbudget']['year'];
        // $financialbudget_prevyear = $investprevyear = $financialbudget_year - 1;
    }
    else {
        redirect('index.php?module=budgeting/createfinbudget');
    }
    $affid = $core->input['financialbudget']['affid'];
    $affiliate = new Affiliates($affid);
    $rowid = 0;
    /* get budget */
    $financialbudget = FinancialBudget::get_data(array('affid' => $affid, 'year' => $financialbudget_year), array('simple' => false));
    $budgetrainingvisit_obj = BudgetTrainingVisits::get_data(array('bfbid' => $financialbudget->bfbid), array('simple' => false, 'returnarray' => true));

    if(is_array($core->user['auditedaffids'])) {
        foreach($core->user['auditedaffids'] as $auditaffid) {
            $aff_obj = new Affiliates($auditaffid);
            $affiliate_users = $aff_obj->get_users();
            foreach($affiliate_users as $aff_businessmgr) {
                $business_managers[$aff_businessmgr['uid']] = $aff_businessmgr['displayName'];
            }
        }
    }
    else {
        if($core->usergroup['canViewAllEmp'] == 1) {
            $affiliate = new Affiliates($core->user['mainaffiliate']);
            $business_managers = $affiliate->get_users(array('displaynameonly' => true));
        }
        else {
            $business_managers[$core->user['uid']] = $core->user['displayName'];
        }
    }

    $fields = array('purpose', 'event', 'costAffiliate', 'company', 'inputChecksum', 'bm', 'planeCost', 'otherCosts', 'lid'); //'actualPrevYear', 'budgetPrevYear'
    $rowid = 0;
    if(is_array($budgetrainingvisit_obj)) {
        foreach($budgetrainingvisit_obj as $btvid => $budgetvisit) {
            foreach($fields as $field) {
                if(!empty($budgetvisit->$field)) {
                    $budgetrainingvisit[$field] = $budgetvisit->$field;
                }
                $budgetrainingvisit['date_output'] = date($core->settings['dateformat'], $budgetvisit->date);
                $budgetrainingvisit['Date_formatted'] = date($core->settings['dateformat'], $budgetvisit->date);
            }
            /* move the import leave code to here  ---START */

            /* move the import leave code to here ---END */

            if($budgetvisit->classification === 'local') {
                $entityobj = new Entities($budgetvisit->company);
                $budgetrainingvisit['companyoutput'] = $entityobj->name;
                eval("\$budgettaininglocalvisits_rows .= \"".$template->get('budgeting_tainingvisits_lines')."\";");
            }
            else {
                if(is_array($business_managers)) {
                    foreach($business_managers as $uid => $bm) {
                        $selected = '';
                        if($budgetvisit->bm == $uid) {
                            $selected = ' selected="selected"';
                        }
                        $business_managers_list.='<option value='.$uid.' '.$selected.'>'.$bm.'</option>';
                    }
                    //  $business_managers_list = parse_selectlist('budgetrainingvisit[international]['.$rowid.'][bm]', 7, $business_managers, $selected_options);
                }
                eval("\$budgettaininig_intvisits_rows .= \"".$template->get('budgeting_tainingintvisits_lines')."\";");
                unset($budgetrainingvisit);
            }
            $rowid ++;
        }
    }
    unset($btvid, $budgetvisit);
    // unset($budgettaininglocalvisits_rows, $budgettaininig_intvisits_rows);

    /* Parse and fill training visit fields based on selected leaves ----START */
    /* recognize that the request includes leave IDs */
    if(isset($core->input['budgetrainingvisit']['leaves']) && $core->input['source'] == 'import' && !empty($core->input['budgetrainingvisit']['leaves'])) {
        unset($budgetrainingvisit);
        $populated_leaves = Leaves::get_data(array('lid' => $core->input['budgetrainingvisit']['leaves']), array('returnarray' => true, 'simple' => false, 'operators' => array('lid' => 'IN')));
        //financialbudget->bfbid;
        $affiliate = new Affiliates($affid);
        foreach($populated_leaves as $lid => $populated_leave) {

            $visit_type = $populated_leave->check_leavedestination();
            //   $rowid = intval($rowid) + 1;
            switch($visit_type) {
                case 'international':
                    $type = 'international';
                    $budgetrainingvisit['inputChecksum'] = generate_checksum('budget');
                    if(is_array($business_managers)) {
                        $budgetrainingvisit[$populated_leave->lid]['international']['bm'] = $populated_leave->uid;
                        // $business_managers_list = parse_selectlist('budgetrainingvisit[international]['.$rowid.'][bm]', 7, $business_managers, $selected_options);
                    }
                    foreach($business_managers as $uid => $bm) {
                        $selected = '';
                        if($uid == $populated_leave->uid) {
                            $selected = " selected='selected'";
                        }
                        $business_managers_list .= '<option value='.$uid.' '.$selected.'>'.$bm.'</option>';
                    }
                    $budgetrainingvisit[$populated_leave->lid][$type]['lid'] = $populated_leave->lid;
                    $budgetrainingvisit[$populated_leave->lid][$type]['date_output'] = date($core->settings['dateformat'], $populated_leave->fromDate);
                    $budgetrainingvisit[$populated_leave->lid][$type]['Date_formatted'] = date('d-m-Y', $populated_leave->fromDate);
                    $budgetrainingvisit[$populated_leave->lid][$type]['purpose'] = $populated_leave->reason;
                    $leave_expenses = $populated_leave->get_expensestotal();
                    if(!empty($leave_expenses)) {
                        $budgetrainingvisit[$populated_leave->lid][$type]['totalexpenses'] = $leave_expenses;
                    }
                    $expensesdetails = $populated_leave->get_expensesdetails();
                    if(is_array($expensesdetails)) {
                        foreach($expensesdetails as $expenses) {
                            if($expenses['name'] == 'airfare') {
                                $budgetrainingvisit[$populated_leave->lid][$type]['planeCost'] = $expenses['expectedAmt'];
                            }
                            //  $budgetrainingvisit[$populated_leave->lid][$type]['otherCosts'] = $budgetrainingvisit[$populated_leave->lid][$type]['totalexpenses'] - $expenses['expectedAmt'];
                        }
                    }
                    eval("\$budgettaininig_intvisits_rows .= \"".$template->get('budgeting_tainingintvisits_lines')."\";");
                    unset($budgetrainingvisit[$populated_leave->lid]);
                    break;
                case 'domestic':
                    $type = 'domestic';
                    $budgetrainingvisit['inputChecksum'] = generate_checksum('budget');
                    $budgetrainingvisit[$populated_leave->lid][$type]['lid'] = $populated_leave->lid;
                    $budgetrainingvisit[$populated_leave->lid][$type]['date_output'] = date($core->settings['dateformat'], $populated_leave->fromDate);
                    $budgetrainingvisit[$populated_leave->lid][$type]['Date_formatted'] = date('d-m-Y', $populated_leave->fromDate);
                    $budgetrainingvisit[$populated_leave->lid][$type]['purpose'] = $populated_leave->reason;
                    $leave_expenses = $populated_leave->get_expensestotal();
                    if(!empty($leave_expenses)) {
                        $budgetrainingvisit[$populated_leave->lid][$type]['costAffiliate'] = $leave_expenses;
                    }
                    eval("\$budgettaininglocalvisits_rows .= \"".$template->get('budgeting_tainingvisits_lines')."\";");
                    unset($budgetrainingvisit[$populated_leave->lid]);
                    break;
            }
            $rowid ++;
        }
    }

    /* Parse and fill training visit fields based on selected leaves ----END */
    if(empty($budgettaininglocalvisits_rows)) {
        $budgetrainingvisit['inputChecksum'] = generate_checksum('budget');
        eval("\$budgettaininglocalvisits_rows = \"".$template->get('budgeting_tainingvisits_lines')."\";");
    }
    if(empty($budgettaininig_intvisits_rows)) {
        if(is_array($business_managers)) {
            $business_managers_list.='<option value="" ></option>';
            foreach($business_managers as $uid => $bm) {
                $business_managers_list.='<option value='.$uid.' '.$selected.'>'.$bm.'</option>';
            }
        }
        $budgetrainingvisit['inputChecksum'] = generate_checksum('budget');
        eval("\$budgettaininig_intvisits_rows  = \"".$template->get('budgeting_tainingintvisits_lines')."\";");
    }

    //eval("\$budgettaininig_intvisits_rows .= \"".$template->get('budgeting_tainingintvisits_lines')."\";");
    //}

    /* Fill based on existing leaves  populate existing business leaves  ----START */

    $leave['filter']['type'] = 'SELECT ltid FROM leavetypes WHERE isBusiness=1';
    $leave['filter']['uid'] = 'SELECT uid FROM affiliatedemployees WHERE affid='.intval($affid).' AND isMain=1';
    /* avoid reshowing import text box if all leaves have been imported. Only show those that have not been imported.  */
    if(isset($financialbudget->bfbid)) {
        $leave['filter']['lid'] = 'SELECT lid FROM budgeting_trainingvisits WHERE bfbid='.$financialbudget->bfbid;
    }
    $leave['filter']['fromdate'] = 'SELECT fromdate FROM leaves WHERE FROM_UNIXTIME(fromDate, "%Y")='.($financialbudget_year - 1);
    $leaves_objs = Leaves::get_data($leave['filter'], array('returnarray' => true, 'simple' => false, 'operators' => array('uid' => 'IN', 'type' => 'IN', 'lid' => 'NOT IN', 'fromdate' => 'IN')));
    $lang->load('attendance_messages');
    if(is_array($leaves_objs)) {
        foreach($leaves_objs as $leaves_obj) {
            $leaves_obj->employee = $leaves_obj->get_requester()->get_displayname();
            $leavedate[$leaves_obj->lid] = date($core->settings['dateformat'], $leaves_obj->fromDate).' -> '.date($core->settings['dateformat'], $leaves_obj->toDate); //' from '.date($core->settings['dateformat'], $leaves_obj->fromDate).' TO '.date($core->settings['dateformat'], $leaves_obj->toDate);
            $leaveexpenses = $leaves_obj->get_expensestotal();
            if(!empty($leaveexpenses)) {
                $leaves_obj->totalexpenses = $leaveexpenses;
            }
            eval("\$budgeting_tainingvisitleaves_rows .= \"".$template->get('budgeting_tainingvisits_leavesintegration_rows')."\";");
        }
        eval("\$budgeting_tainingvisitleaves = \"".$template->get('budgeting_tainingvisits_leavesintegration')."\";");
    }

    /* Fill based on existing leaves  populate existing business leaves  ----END */


    eval("\$budgeting_tainingvisit = \"".$template->get('budgeting_tainingvisits')."\";");
    output_page($budgeting_tainingvisit);
}
else if($core->input['action'] == 'ajaxaddmore_budgetrainvisitlocal') {
    $rowid = intval($core->input['value']) + 1;
    $budgetrainingvisit['inputChecksum'] = generate_checksum('budget');
    eval("\$budgettainingvisitssrows = \"".$template->get('budgeting_tainingvisits_lines')."\";");
    output($budgettainingvisitssrows);
}
else if($core->input['action'] == 'ajaxaddmore_budgetrainvisitint') {
    $rowid = intval($core->input['value']) + 1;
    $budgetrainingvisit['inputChecksum'] = generate_checksum('budget');
    if(is_array($core->user ['auditedaffids'])) {
        foreach($core->user['auditedaffids'] as $auditaffid) {
            $aff_obj = new Affiliates($auditaffid);
            $affiliate_users = $aff_obj->get_users();
            foreach($affiliate_users as $aff_businessmgr) {

                $business_managers[$aff_businessmgr['uid']] = $aff_businessmgr['displayName'];
            }
        }
    }
    else {
        if($core->usergroup['canViewAllEmp'] == 1) {
            $affiliate = new Affiliates($core->user['mainaffiliate']);
            $business_managers = $affiliate->get_users(array('displaynameonly' => true));
        }
        else {
            $business_managers[$core->user['uid']] = $core->user['displayName'];
        }
    }
    if(is_array($business_managers)) {


        if(is_array($business_managers)) {
            $business_managers_list = '<option> </option>';
            foreach($business_managers as $uid => $bm) {
                $business_managers_list.='<option value = '.$uid.' >'.$bm.'</option>';
            }
        }

//$business_managers_list = parse_selectlist('budgetrainingvisit[international]['.$rowid.'][bm]', 7, $business_managers, $selected_optio ns);
    }

    eval("\$budgettaininig_intvisits_rows = \"".$template->get('budgeting_tainingintvisits_lines')."\";");
    output($budgettaininig_intvisits_rows);
}
else if($core->input['action'] == 'do_perform_trainingvisits') {
    unset($core->input['identifier'], $core->input['module'], $core->input['action']);
    $financialbudget = new FinancialBudget();
    $financialbudget->set($core->input);
    $financialbudget->save();
    switch($financialbudget->get_errorcode()) {
        case 0:
        case 1:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 2:
            output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
            break;
    }
}
//else if($core->input['action'] == 'impodrtleaves') {
//    if(is_array($core->input['budgetrainingvisit']['leaves']) && !empty($core->input['budgetrainingvisit']['leaves'])) {
//        $populated_leaves = populate_leaves();
//        $rowid = intval($core->input['value']) + 1;
//        $budgetrainingvisit['inputChecksum'] = generate_checksum('budget');
//        $budgetrainingvisit[Date_formatted] = $populated_leaves[0][Date_formatted];
//
//        eval("\$budgettaininig_intvisits_rows = \"".$template->get('budgeting_tainingintvisits_lines')."\";");
//
//
//
//        // parse the rows
//        eval("\$budgeting_tainingvisit = \"".$template->get('budgeting_tainingvisits')."\";");
//        output_page($budgeting_tainingvisit);
//
//        exit;
//    }
//}
function populate_leaves() {
    global $core;
    $leaves_import = Leaves::get_data(array('lid' => $core->input['budgetrainingvisit']['leaves']), array('returnarray' => true,
                    'simple' => false, 'operators' => array('lid' => 'IN')));

    foreach($leaves_import as $leave) {
        $budgetrainingvisit[] = $leave;
    }
    return $budgetrainingvisit;
}
