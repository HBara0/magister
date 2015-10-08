<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: copybudgets.php
 * Created:        @rasha.aboushakra    Oct 8, 2015 | 9:33:48 AM
 * Last Update:    @rasha.aboushakra    Oct 8, 2015 | 9:33:48 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canUseBudgeting'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {
    $affiliate_where = ' name LIKE "%orkila%" AND isActive=1';
    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, "{$affiliate_where}");

    $affiliated_budget = parse_selectlist('copybudget[affid]', 1, $affiliates, '', '', '', array('id' => 'affid'));


    $supplier_where .= " approved=1 AND isActive=1 AND type='s' ";
    $suppliers = get_specificdata('entities', array('eid', 'companyName'), 'eid', 'companyName', array('by' => 'companyName', 'sort' => 'ASC'), 1, "{$supplier_where}");

    $budget_supplierslist = parse_selectlist('copybudget[spid]', 1, $suppliers, '', '', '', array('id' => 'spid'));

    $years = array_combine(range(date('Y'), date('Y') + 1), range(date('Y'), date('Y') + 1));

    $budget_year = parse_selectlist('copybudget[year]', 1, $years, '', '', '', array('id' => 'year'));


    $frombm_list = "<select name=copybudget[frombm] id='from_bm' ><option value='0'>&nbsp;</option></select>";
    $tobm_list = "<select name=copybudget[tobm] id='to_bm' ><option value='0'>&nbsp;</option></select>";

    eval("\$budgetcopy = \"".$template->get('budgeting_copybudget')."\";");
    output_page($budgetcopy);
}
else {
    if($core->input['action'] == 'get_businessmgrs') {
        $affid = $db->escape_string($core->input['id']);
        $affiliate = new Affiliates($affid);

        $gid_filter = " gid IN (SELECT gid FROM usergroups WHERE canUseBudgeting= 1 AND budgeting_canFillBudget = 1)";
        $affiliate_bms = $affiliate->get_users(array('customfilter' => $gid_filter));

        $bm_list = '<option value="0"></option>';
        if(is_array($affiliate_bms)) {
            foreach($affiliate_bms as $bm) {
                $bm_list .= '<option value="'.$bm['uid'].'">'.$bm['name'].'</option>';
            }
            output($bm_list);
        }
    }
    else
    if($core->input['action'] == 'do_perform_copybudgets') {
        $keydata = array('year', 'spid', 'affid', 'frombm', 'tobm');
        foreach($keydata as $attr) {
            $budget_data[$attr] = $core->input['copybudget'][$attr];
        }

        $budget = new Budgets();
        $currentbudget = Budgets::get_budget_bydata(array('year' => $budget_data['year'] + 1, 'affid' => $budget_data['affid'], 'spid' => $budget_data['spid']));

        $prevbudget = Budgets::get_budget_bydata(array('year' => $budget_data['year'], 'affid' => $budget_data['affid'], 'spid' => $budget_data['spid']));
        if(is_array($prevbudget) && !empty($prevbudget['bid'])) {
            $filter = array('filters' => array('businessMgr' => array($budget_data['frombm'])));
            $budgetlinesdata = $budget->get_budgetLines($prevbudget['bid'], $filter);

            if(is_array($budgetlinesdata)) {
                foreach($budgetlinesdata as $cid => $budgetlines) {
                    if(is_array($budgetlines)) {
                        foreach($budgetlines as $pid => $budgetline) {
                            foreach($budgetline as $saletype => $bline) {
                                $exisitingline_obj = BudgetLines::get_data(array('prevblid' => $bline['blid']));
                                if(is_object($exisitingline_obj)) {
                                    continue;
                                }
                                unset($bline['prevbudget'], $bline['bid'], $bline['linkedBudgetLine']);
                                $bline_data = $bline;
                                $bline_data['inputChecksum'] = generate_checksum('bl');
                                $bline_data['businessMgr'] = $budget_data['tobm'];
                                $bline_data['prevblid'] = $bline_data['blid'];
                                unset($bline_data['blid']);
                                $bline_data['modifiedBy'] = $bline_data['modifiedOn'] = 0;
                                $bline_data['createdBy'] = $budget_data['tobm'];
                                $newbudget_lines[] = $bline_data;
                            }
                        }
                        if(is_array()) {
                            Budgets::save_budget(array('year' => $budget_data['year'] + 1, 'affid' => $budget_data['affid'], 'spid' => $budget_data['spid']), $newbudget_lines);
                        }
                    }
                }
            }
        }

        switch($budget->get_errorcode()) {
            case 0:
                output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
                break;
            case 2:
                output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
                break;
            case 602:
                output_xml('<status>false</status><message>'.$lang->budgetexist.'</message>');
                break;
            default:
                output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
                break;
        }
    }
}
?>