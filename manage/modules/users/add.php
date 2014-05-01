<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Add users
 * $module: admin/users
 * $id: add.php	
 * Created: 	@zaher.reda 		Februar, 2009
 * Last Update: @zaher.reda 		March 21, 2012 | 05:23 PM
 */
if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canAddUsers'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$lang->load('users_add');

if(!$core->input['action']) {
    $usergroup_attributes = array('gid', 'title');
    $usergroup_order = array(
            'by' => 'title',
            'sort' => 'ASC'
    );

    $usergroups = get_specificdata('usergroups', $usergroup_attributes, 'gid', 'title', $usergroup_order);
    if($core->user['gid'] != 1) {
        unset($usergroups[1]);
    }
    $usergroups_list = parse_selectlist('maingid', 5, $usergroups, 3);
    $addusergroups_list = parse_selectlist('addgids[]', 5, $usergroups, '', 1);

    $affiliates_attributes = array('affid', 'name');
    $countries_attributes = array('coid', 'name');
    $countries_order = array(
            'by' => 'name',
            'sort' => 'ASC'
    );

    $affiliates = get_specificdata('affiliates', $affiliates_attributes, 'affid', 'name', $countries_order);
    //$main_affiliate_list = parse_selectlist('mainaffid', 6, $affiliates, '', 0);
    //$affiliates_list = parse_selectlist("affids[]", 7, $affiliates, '', 1);	
    $affiliates_checkboxes_items = array('affids' => 'otheraffiliates', 'canHR' => 'affiliatehr', 'canAudit' => 'affiliateaudit');
    foreach($affiliates as $affid => $name) {
        $rowclass = alt_row($rowclass);
        $mainaffiliate_radiobutton = parse_radiobutton('affiliates[mainaffid]', array($affid => ''));
        foreach($affiliates_checkboxes_items as $attr => $item) {
            $checkboxes[$item] = parse_checkboxes('affiliates['.$attr.']', array($affid => ''));
        }

        eval("\$affiliates_list .= \"".$template->get('admin_users_addedit_affiliaterow')."\";");
    }

    //$affiliateshr_list = parse_selectlist("hraffids[]", 8, $affiliates, '', 1);	

    $countries = get_specificdata('countries', $countries_attributes, 'coid', 'name', $countries_order);
    $countries_list = parse_selectlist('country', 10, $countries, '');

    $positions_list = parse_selectlist("posid[]", 24, get_specificdata('positions', '*', 'posid', 'title', array('by' => 'name', 'sort' => 'ASC')), '', 1);

    $segments = get_specificdata('productsegments', array('psid', 'title'), 'psid', 'title', 'title'); //$segments_list = parse_selectlist("psid[]", 25, get_specificdata('productsegments', array('psid', 'title'), 'psid', 'title', 'title'), '', 1);
    foreach($segments as $psid => $value) {
        $rowclass = alt_row($rowclass);
        eval("\$segments_list .= \"".$template->get('admin_users_addedit_segmentrow')."\";");
    }

    $entity_attributes = array('eid', 'companyName');
    $entity_order = array(
            'by' => 'companyName',
            'sort' => 'ASC'
    );
    /*
      $suppliers = get_specificdata('entities', $entity_attributes, 'eid', 'companyName', $entity_order, 0, "type='s'");
      $suppliers_list = parse_selectlist("spid[]", 25, $suppliers,'', 1);
     */
    $supp_counter = 1;
    $affiliates_list_supplierssection = parse_selectlist("supplier[{$supp_counter}][affiliates][]", 0, $affiliates, '', 1);
    eval("\$suppliers_rows = \"".$template->get('admin_users_addedit_supplierrow')."\";");

    $customers = get_specificdata('entities', $entity_attributes, 'eid', 'companyName', $entity_order, 0, "type='c'");
    foreach($customers as $cid => $value) {
        $rowclass = alt_row($rowclass);
        eval("\$customer_list .= \"".$template->get('admin_users_addedit_customerrow')."\";");
    }

    $actiontype = 'add';
    $pagetitle = $lang->adduser;
    eval("\$addpage = \"".$template->get('admin_users_addedit')."\";");
    output_page($addpage);
}
else {
    if($core->input['action'] == 'do_perform_add') {
        if(empty($core->input['password'])) {
            output_xml("<status>false</status><message>{$lang->specifypassword}</message>");
            exit;
        }

        if($core->input['password'] == $core->input['password2']) {
            $log->record($core->input['username']);
            unset($core->input['module'], $core->input['action'], $core->input['password2']);
            $account = new CreateAccount($core->input);
        }
        else {
            output_xml("<status>false</status><message>{$lang->passwordsnomatch}</message>");
        }
    }
}
?>