<?php
if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['budgeting_canMassUpdate'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {
    if($core->usergroup['canViewAllAff'] == 1) {
        foreach($core->user['affiliates'] as $affid) {
            $aff_objs[$affid] = new Affiliates($affid);
        }
    }
    else {
        $aff_objs = Affiliates::get_affiliates('name IS NOT NULL');
    }
    foreach($aff_objs as $affiliate) {
        $affiliates_list .= '<td><input name="budget[filter][affid][]" type="checkbox"'.$checked.'  id="affiliate_'.$affiliate->affid.'" value="'.$affiliate->affid.'">'.$affiliate->get_displayname().'</td></tr>';
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
        $suppliers_list .= '<td><input name="budget[filter][spid][]" type="checkbox"'.$checked.'  id="supplier_'.$supplier->eid.'"  value="'.$supplier->eid.'">'.$supplier->get_displayname().'</td><tr>';
    }

    $years = Budgets::get_availableyears();
    if(is_array($years)) {
        foreach($years as $key => $value) {
            $checked = $rowclass = '';
            $budget_year_list .= '<tr class="'.$rowclass.'">';
            $budget_year_list .= '<td><input name="budget[filter][year][]" required="required" type="checkbox"  id="year_'.$key.'" value="'.$key.'">'.$value.'</td></tr>';
        }
    }
    /* Can Generate users of the affiliates he belongs to */

// get affid of the above

    if(is_array(array_keys($aff_objs))) {
        foreach(array_keys($aff_objs) as $auditaffid) {
            $aff_obj = new Affiliates($auditaffid);
            $affiliate_users = $aff_obj->get_all_users(array('customfilter' => 'u.uid IN (SELECT users_usergroups.uid FROM users_usergroups WHERE gid IN (SELECT usergroups.gid FROM usergroups WHERE budgeting_canFillBudget=1))'));
            if(is_array($affiliate_users)) {
                foreach($affiliate_users as $aff_businessmgr) {
                    $business_managers[$aff_businessmgr['uid']] = $aff_businessmgr['displayName'];
                }
            }
        }
    }


    if(is_array($business_managers)) {
        foreach($business_managers as $key => $value) {
            $checked = $rowclass = '';
            $business_managerslist .= '<tr class="'.$rowclass.'">';
            $business_managerslist .= '<td><input name="budget[filterline][businessMgr][]" type="checkbox"'.$checked.' id="bm_'.$key.'" value="'.$key.'"/>'.$value.'</td></tr>';
        }
    }
    $saletype_objs = SaleTypes::get_data();
    if(is_array($saletype_objs)) {
        foreach($saletype_objs as $key => $value) {
            $checked = $rowclass = '';
            $sale_types .= '<tr class="'.$rowclass.'">';
            $sale_types .= '<td><input name="budget[filterline][saleType][]" type="checkbox"   id="saletype_'.$key.'" value="'.$key.'">'.$value.'</td></tr>';
        }
    }
    $user = new Users($core->user['uid']);
    $user_segments_objs = $user->get_segments();

    /*  configuration array for the Values to Overwrite: */
    $allaff_objs = Affiliates::get_affiliates('name IS NOT NULL');
    $overwrites_fields = array('businessMgr' => array('inputfield' => parse_selectlist('budget[overwrite][value][businessMgr]', 0, $business_managers, '', '', '', array('blankstart' => true, 'id' => 'businessMgr_'))),
            'purchasingEntity' => array('inputfield' => '<input type="text" placeholder="'.$lang->affiliate.'"  size="20" id="affiliate_pe" name="budget[overwrite][value][purchasingEntity]"    autocomplete="off" />'),
            'purchasingEntityId' => array('inputfield' => '<input type="text" placeholder="'.$lang->search.' '.$lang->affiliate.'" id=affiliate_peid_autocomplete name=""    autocomplete="off" /><input type="hidden" value=" " id="affiliate_peid_id" name="budget[overwrite][value][purchasingEntityId]"/>'),
            'localIncomePercentage' => array('inputfield' => '<input name="budget[overwrite][value][localIncomePercentage]"  value="" type="text" id="localincomeper_'.$rowid.'" size="15" accept="numeric"  />'),
            'commissionSplitAffid' => array('inputfield' => parse_selectlist('budget[overwrite][value][commissionSplitAffid]', 0, $allaff_objs, '', '', '', array('blankstart' => true, 'id' => 'commissionsplitaffid_'))),
            'Segment' => array('inputfield' => parse_selectlist('budget[overwrite][value][psid]', 0, $user_segments_objs, '', '', '', array('blankstart' => true, 'id' => 'segment_')))
    );

    foreach($overwrites_fields as $attr => $field) {
        $overwrite_fields .= '<tr><td><input name="budget[overwrite][attribute]['.$attr.']" type="checkbox" id="column_'.$attr.'" value="1"/>'.$attr.'</td>';
        $overwrite_fields .= '<td><div id="value_'.$attr.'" style="display:block;">'.$field['inputfield'].'</div></td>';
        $overwrite_fields .= '</tr>';
    }
    $pagename = 'massupdateyef';
    $lineidname = 'yeflid';
    eval("\$massupdate = \"".$template->get('budgeting_massupdate')."\";");
    output_page($massupdate);
}
else {
    if($core->input['action'] == 'do_massupdateyef') {
        $budgetfilter_where = $core->input['budget']['filter'];
        $filterline_where = $core->input['budget']['filterline'];
        $attribute = ($core->input['budget']['overwrite']['attribute']);
        unset($core->input['budget']['overwrite']['attribute']);
        $overwrite_fields = $core->input['budget']['overwrite'];
        if(!empty($filterline_where['yeflid'])) {
            $filterline_where['yeflid'] = explode(',', trim($filterline_where['yeflid']));
            if(is_array($filterline_where['yeflid'])) {
                $filterline_where['yeflid'] = array_map(intval, $filterline_where['yeflid']);
                $filterline_where['yeflid'] = array_filter($filterline_where['yeflid']);

                $yefline_wherecondition = ' AND yeflid IN ('.implode(',', $filterline_where['yeflid']).')';
            }
            else {
                if(!is_numeric($filterline_where['yeflid']) || $filterline_where['yeflid'] == 0) {
                    output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'!</message>');
                    exit;
                }
            }
        }
        else {
            $checkfilter_array = array('affid', 'spid', 'year');
            foreach($checkfilter_array as $filterval) {
                if(empty($budgetfilter_where[$filterval])) {
                    output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'!</message>');
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
                        $yefline_wherecondition .= $and.$attr.' IN ('.implode(',', $filterline).')';
                    }
                }
            }
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

        if(isset($overwrite_fields['value']['localIncomePercentage']) && (!empty($overwrite_fields['value']['localIncomePercentage']) || $overwrite_fields['value']['localIncomePercentage'] == 0)) {
            if($overwrite_fields['value']['localIncomePercentage'] != 100) {
                $overwrite_fields['value']['localIncomeAmount'] = '(amount * ('.$overwrite_fields['value']['localIncomePercentage'].' / 100))';
                // $overwrite_fields['value']['localIncomePercentage'] = '('.$overwrite_fields['value']['localIncomePercentage'].' * (100/incomePerc))';

                $overwrite_fields['value']['invoicingEntityIncome'] = 'income-'.$overwrite_fields['value']['localIncomeAmount'];
            }
            else {
                $overwrite_fields['value']['localIncomeAmount'] = 'income';
                $overwrite_fields['value']['invoicingEntityIncome'] = 0;
            }
        }


        $overwrite_fields['value']['modifiedOn'] = TIME_NOW;
        $overwrite_fields['value']['modifiedBy'] = $core->user['uid'];
        foreach($overwrite_fields['value'] as $column => $colvalue) {
            if(empty($colvalue) && $colvalue != "0") {
                continue;
            }
            if($column == 'purchasingEntity') {
                $colvalue = '\''.$colvalue.'\'';
            }
            $updatequery_set .= $comma.$column.'='.$colvalue;
            $comma = ', ';
        }

        /* acquire all rows which will be affected, */
        $budgetlines_notaffectedobjs = BudgetingYEFLines::get_data('yefid IN (SELECT yefid FROM budgeting_yearendforecast '.$budget_wherecondition.')'.$yefline_wherecondition, array('returnarray' => true));
        $sql = 'UPDATE '.Tprefix.'budgeting_yef_lines SET '.$updatequery_set.' WHERE yefid IN (SELECT yefid FROM budgeting_yearendforecast '.$budget_wherecondition.')'.$yefline_wherecondition;
        //$debug = true;
        if($debug == true) {
            output_xml('<status>true</status><message><![CDATA['.$sql.']]></message>');
            exit;
        }
        $query = $db->query($sql);
        if($query) {
            $affectedrows = $db->affected_rows();
            $filename = generate_checksum();
            $filepath = ROOT.'/tmp/'.$filename.'.csv';
            $csv = new CSV($filepath);
            $csv->write_file($budgetlines_notaffectedobjs);

            if(is_array($budgetlines_notaffectedobjs)) {
                foreach($budgetlines_notaffectedobjs as $affectedrowobj) {
                    $affectedrow = $affectedrowobj->get();
                    unset($affectedrow['inputChecksum']);
                    $affectedrow['backedupOn'] = TIME_NOW;
                    $affectedrow['backedupBy'] = $core->user['uid'];
                    $budgetlinesbk = new YefLinesBackup();
                    $budgetlinesbk->set($affectedrow);
                    $budgetlinesbk->save();
                }
            }
            output_xml('<status>true</status><message>'.$lang->successfullysaved.' '.$affectedrows.' lines.<![CDATA[ <a href="'.$core->settings['rootdir'].'/index.php?module=budgeting/massupdate&action=download&file='.$filename.'" target="_blank">Click here to downolad original values.</a>]]></message>');
        }
    }
    elseif($core->input['action'] == 'download') {
        if(empty($core->input['file'])) {
            error($lang->error);
        }

        $filepath = ROOT.'/tmp/budget/'.$core->input['file'].'.csv';

        $download = new Download();
        $download->set_real_path($filepath);
        $download->stream_file(true);
        unlink($filepath);
    }
}

