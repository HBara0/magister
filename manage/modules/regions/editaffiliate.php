<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * Manage Affiliates
 * $module: admin/regions
 * $id: editaffiliate.php
 * Created: 	@najwa.kassem   Feb 8, 2011 | 10:10 AM
 * Last Update: @najwa.kassem   May 18, 2011 | 10:10 AM
 */
if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canManageAffiliates'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $affid = $db->escape_string($core->input['affid']);
    $affiliate = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."affiliates WHERE affid={$affid}"));
    $title = $lang->editaffiliate;
    $actiontype = 'editaffiliate';
    $affidfield = "<input type='hidden' id='affid'  name='affid' value='{$affid}'/>";
    $countries = get_specificdata('countries', array('coid', 'name', 'affid'), 'coid', 'name', 1, 0);

    if(empty($countries)) {
        $countries[] = '';
    }
    $affiliate_countries = get_specificdata('countries', 'coid', 'coid', 'coid', 1, 0, "affid ={$affid}");
    $countries_list = parse_selectlist('coid[]', 2, $countries, $affiliate_countries, 1);

    $cities = get_specificdata('cities ', array('ciid', 'name'), 'ciid', 'name', 1, 1);
    $cities_list = parse_selectlist('city', 1, $cities, $affiliate['city'], 0);

    $leavetypes = get_specificdata('leavetypes', array('ltid', 'title'), 'ltid', 'title', 1, 1);
    $policies_rowid = 1;

    $leavespolicies_query = $db->query("SELECT * FROM ".Tprefix."affiliatesleavespolicies WHERE affid={$affid}");
    if($db->num_rows($leavespolicies_query) > 0) {
        while($leavepolicies = $db->fetch_array($leavespolicies_query)) {
            $leavetypes_list = parse_selectlist('ltid', 1, $leavetypes, $leavepolicies['ltid'], 0);
            $leavetype = $leavetypes[$leavepolicies['ltid']];
            $promotionpolicies = unserialize($leavepolicies['promotionPolicy']);
            $promyears = '';
            foreach($promotionpolicies as $promotionyears => $promotiondays) {
                $promyears .= "<tr><td>{$lang->promotionyears}</td><td>{$promotionyears}</td><td>{$lang->promotiondays}</td><td>{$promotiondays}</td><td>&nbsp;</td></tr>";
            }
            eval("\$leavespolicies .= \"".$template->get('admin_regions_addeditaffiliate_leavespolicies')."\";");
            $policies_rowid++;
            $years_rowid++;
        }
    }

    $management_query = $db->query("SELECT uid, CONCAT(firstName, ' ', lastName) AS employeeName FROM ".Tprefix."users WHERE uid IN ({$affiliate['supervisor']},{$affiliate['generalManager']},{$affiliate['hrManager']})");
    while($management = $db->fetch_array($management_query)) {
        $managers[$management['uid']] = $management['employeeName'];
    }

    //If this is not optional or conditional, please put it in the template
    $leavespolicies_section = "<tr><td colspan='2'><hr /><span class='subtitle'>{$lang->leavespolicies}</span></td></tr>{$leavespolicies}<tr><td colspan='4'><a href='#' id='addleavepolicy_{$core->input[affid]}_regions/{$actiontype}_loadpopupbyid'><img src='../images/add.gif'  alt='{$lang->add}'> </a></td></tr>";

    eval("\$affiliatespage = \"".$template->get('admin_regions_addeditaffiliate')."\";");
    output_page($affiliatespage);
}
else {
    if(($core->input['action'] == 'get_editleavepolicy') || ($core->input['action'] == 'get_addleavepolicy')) {
        $policies_rowid = 1;
        $leavetypes = get_specificdata('leavetypes', array('ltid', 'title'), 'ltid', 'title', 1, 0);

        if($core->input['action'] == 'get_editleavepolicy') {
            $alpid = $db->escape_string($core->input['id']);
            $leavespolicies_query = $db->query("SELECT * FROM ".Tprefix."affiliatesleavespolicies WHERE alpid={$alpid}");
            $title = $lang->edit;
            if($db->num_rows($leavespolicies_query) > 0) {
                while($leavepolicies = $db->fetch_array($leavespolicies_query)) {
                    $leavetypes_list = parse_selectlist('ltid', 1, $leavetypes, $leavepolicies['ltid'], 0);

                    $promotionpolicies = unserialize($leavepolicies['promotionPolicy']);
                    foreach($promotionpolicies as $promotionyears => $promotiondays) {
                        eval("\$promyears .= \"".$template->get('admin_regions_addeditaffiliate_leavespolicies_promotion')."\";");
                        $policies_rowid++;
                    }
                    eval("\$editaffiliate = \"".$template->get('popup_regions_editaffiliate')."\";");
                    output($editaffiliate);
                }
            }
        }
        else {
            $affid = $db->escape_string($core->input['id']);
            $leavetypes_list = parse_selectlist("ltid", 1, $leavetypes, '', 0);
            $title = $lang->add;
            eval("\$promyears .= \"".$template->get('admin_regions_addeditaffiliate_leavespolicies_promotion')."\";");
            $policies_rowid++;
            eval("\$editaffiliate = \"".$template->get('popup_regions_editaffiliate')."\";");
            output($editaffiliate);
        }
    }
    elseif($core->input['action'] == 'do_perform_editaffiliate') {
        //use our custom is_empty() function, it works just like isset
        if(empty($core->input['name']) || empty($core->input['coid'])) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
        }
        $countries = $core->input['coid'];
        $affid = $db->escape_string($core->input['affid']);
        unset($core->input['coid'], $core->input['module'], $core->input['action']);

        $query = $db->update_query('affiliates', $core->input, "affid={$affid}");
        if($query) {
            foreach($countries as $key => $val) {
                $db->update_query('countries', array('affid' => $affid), "coid='{$val}'");
            }
            log_action($core->input['name']); // Use the log class
            $lang->affiliateedited = $lang->sprint($lang->affiliateedited, $core->input['name']);
            output_xml("<status>true</status><message>{$lang->affiliateedited}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->erroreditingaffiliate}</message>");
        }
    }
    elseif($core->input['action'] == 'do_editleavespolicies') {
        //use our custom is_empty() function, it works just like isset
        if(empty($core->input['basicEntitlement']) || empty($core->input['canAccumulateFor']) || empty($core->input['entitleAfter']) || empty($core->input['oneTimeBonusDays']) || empty($core->input['halfDayMargin'])) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
        }

        foreach($core->input['promotionyears'] as $key => $val) {
            $promotionpolicies_array[$core->input['promotionyears'][$key]] = $core->input['promotiondays'][$key];
        }

        $promotionpolicy = serialize($promotionpolicies_array);

        if(!empty($core->input['alpid'])) {
            $alpid = $db->escape_string($core->input['alpid']);
            $affid = $db->fetch_field($db->query("SELECT affid as affid FROM ".Tprefix."affiliatesleavespolicies WHERE alpid = {$alpid}"), 'affid');
            if(value_exists('affiliatesleavespolicies', 'ltid', $core->input['ltid'], "affid = {$affid} AND alpid != {$alpid}")) {
                output_xml("<status>false</status><message>{$lang->policyalreadyexists}</message>");
                exit;
            }
            $leavepolicy = array(
                    'ltid' => $core->input['ltid'],
                    'promotionPolicy' => $promotionpolicy,
                    'basicEntitlement' => $core->input['basicEntitlement'],
                    'canAccumulateFor' => $core->input['canAccumulateFor'],
                    'entitleAfter' => $core->input['entitleAfter'],
                    'oneTimeBonusDays' => $core->input['oneTimeBonusDays'],
                    'oneTimeBonusAfter' => $core->input['oneTimeBonusAfter'],
                    'halfDayMargin' => $core->input['halfDayMargin']
            );
            $leavepolicy_query = $db->update_query('affiliatesleavespolicies', $leavepolicy, "alpid={$alpid}");
            $output_true_message = $lang->leavepolicyedited;
            $output_false_message = $lang->erroreditingleavepolicy;
        }
        else {
            $affid = $db->escape_string($core->input['affid']);
            if(value_exists('affiliatesleavespolicies', 'ltid', $core->input['ltid'], "affid = {$affid}")) {
                output_xml("<status>false</status><message>{$lang->policyalreadyexists}</message>");
                exit;
            }
            $leavepolicy = array(
                    'ltid' => $core->input['ltid'],
                    'affid' => $affid,
                    'promotionPolicy' => $promotionpolicy,
                    'basicEntitlement' => $core->input['basicEntitlement'],
                    'canAccumulateFor' => $core->input['canAccumulateFor'],
                    'entitleAfter' => $core->input['entitleAfter'],
                    'oneTimeBonusDays' => $core->input['oneTimeBonusDays'],
                    'oneTimeBonusAfter' => $core->input['oneTimeBonusAfter'],
                    'halfDayMargin' => $core->input['halfDayMargin']
            );
            $leavepolicy_query = $db->insert_query('affiliatesleavespolicies', $leavepolicy);
            $output_true_message = $lang->leavepolicyadded;
            $output_false_message = $lang->erroraddingleavepolicy;
        }

        if($leavepolicy_query) {
            $log->record($alpid); //Where did you get the $alpid from in case the above if statement is false? there should be a last_id
            output_xml("<status>true</status><message>{$output_true_message}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$output_false_message}</message>");
        }
    }
    elseif($core->input['action'] == 'get_deleteleavepolicy') {
        $id = $db->escape_string($core->input['id']); //No need to escape string, this is just an output, it will be escapted in the actual processing later
        eval("\$deletepolicy = \"".$template->get('popup_regions_editaffiliate_deletepolicy')."\";");
        output($deletepolicy);
    }
    elseif($core->input['action'] == 'do_deletepolicy') {
        $alpid = $db->escape_string($core->input['alpid']);
        $query = $db->delete_query("affiliatesleavespolicies", "alpid='{$alpid}'");
        if($query) {
            $log->record($alpid); //Change what is being recorded
            output_xml("<status>true</status><message>{$lang->policydeleted}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->errordeletingpolicy}</message>");
        }
    }
}
?>