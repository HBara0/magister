<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Edit users
 * $module: admin/users
 * $id: edit.php
 * Last Update: @zaher.reda 	June 18, 2010 | 3:05 PM
 */
if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canManageUsers'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$lang->load('users_add');

if(!$core->input['action']) {
    if(!isset($core->input['uid']) || empty($core->input['uid'])) {
        redirect('index.php?module=users/view');
    }

    $uid = $db->escape_string($core->input['uid']);
    $userobj = new Users($uid, false);
    $user = $userobj->get();

    if(!empty($user['reportsTo'])) {
        $user['reportsToName'] = $userobj->get_reportsto()->get()['displayName'];
    }

    if(!empty($user['assistant'])) {
        $user['assistantName'] = $userobj->get_assistant()->get()['displayName'];
    }

    $usergroup_attributes = array('gid', 'title');
    $usergroup_order = array(
            'by' => 'title',
            'sort' => 'ASC'
    );

    $usergroups = get_specificdata('usergroups', $usergroup_attributes, 'gid', 'title', $usergroup_order);
    if($core->user['gid'] != 1) {
        unset($usergroups[1]);
    }
    $user_usergroups = $userobj->get_usergroups(array('classified' => true));
    if(is_array($user_usergroups['additional'])) {
        $user_usergroups['additionalids'] = array_keys($user_usergroups['additional']);
    }
    $usergroups_list = parse_selectlist('maingid', 5, $usergroups, $user_usergroups['main']['gid']);
    $addusergroups_list = parse_selectlist('addgids[]', 5, $usergroups, $user_usergroups['additionalids'], 1);

    $affiliates_attributes = array('affid', 'name');
    $countries_attributes = array('coid', 'name');
    $countries_order = array(
            'by' => 'name',
            'sort' => 'ASC'
    );

    $affiliates_checkboxes_items = array('affids' => 'otheraffiliates', 'canHR' => 'affiliatehr', 'canAudit' => 'affiliateaudit');

    $query2 = $db->query("SELECT a.affid, ae.*, a.name
						  FROM ".Tprefix."affiliates a LEFT JOIN ".Tprefix."affiliatedemployees ae ON (ae.affid=a.affid)
						  WHERE ae.uid='{$uid}'
						  ORDER BY a.name ASC");

    while($affiliate = $db->fetch_array($query2)) {
        if($affiliate['isMain'] == '1') {
            $main_useraffiliate = $affiliate['affid'];
        }
        //else
        //{
        foreach($affiliates_checkboxes_items as $attr => $item) {
            if($affiliate[$attr] == 1) {
                $useraffiliates[$attr][] = $affiliate['affid'];
            }
        }
        $useraffiliates['affids'][] = $affiliate['affid'];
        //}
    }

    $affiliates = get_specificdata('affiliates', $affiliates_attributes, 'affid', 'name', $countries_order);
    //$main_affiliate_list = parse_selectlist('mainaffid', 6, $affiliates, $main_useraffiliate, 0);
    //$affiliates_list = parse_selectlist("affids[]", 7, $affiliates, $useraffiliates, 1);
    //$affiliateshr_list = parse_selectlist("hraffids[]", 8, $affiliates, $userhraffiliates, 1);
    foreach($affiliates as $affid => $name) {
        $rowclass = alt_row($rowclass);
        $mainaffiliate_radiobutton = parse_radiobutton('affiliates[mainaffid]', array($affid => ''), $main_useraffiliate);
        foreach($affiliates_checkboxes_items as $attr => $item) {
            $checkboxes[$item] = parse_checkboxes('affiliates['.$attr.']', array($affid => ''), $useraffiliates[$attr]);
        }
        eval("\$affiliates_list .= \"".$template->get('admin_users_addedit_affiliaterow')."\";");
    }

    $countries = get_specificdata('countries', $countries_attributes, 'coid', 'name', $countries_order);
    $countries_list = parse_selectlist('country', 10, $countries, $user['country']);

    $userpositions = get_specificdata('userspositions', '*', 'upid', 'posid', '', 0, "uid='{$uid}'");
    $positions_list = parse_selectlist('posid[]', 24, get_specificdata('positions', '*', 'posid', 'title', array('by' => 'name', 'sort' => 'ASC')), $userpositions, 1);

    /* Parse Segments Section - START */
    $employeessegments = get_specificdata('employeessegments', '*', 'emsid', 'psid', '', 0, "uid='{$uid}'");
    $segments = get_specificdata('productsegments', array('psid', 'title'), 'psid', 'title', 'title');

    foreach($segments as $psid => $value) {
        $rowclass = alt_row($rowclass);

        if(is_array($employeessegments)) {
            $checked = '';
            if(in_array($psid, $employeessegments)) {
                $checked = ' checked="checked"';
                $rowclass = 'greenbackground';
            }
        }

        eval("\$segments_list .= \"".$template->get('admin_users_addedit_segmentrow')."\";");
    }
    /* Parse Segments Section - END */

    /* $suppliers = get_specificdata('entities', $entity_attributes, 'eid', 'companyName', $entity_order, 0, "type='s'");
      $suppliers_list = parse_selectlist("spid[]", 25, $suppliers, $assignedsuppliers, 1);
     */
    $query3 = $db->query("SELECT ase.eid, ase.affid, e.companyName AS name
						FROM ".Tprefix."assignedemployees ase, ".Tprefix."entities e
						WHERE e.eid=ase.eid AND e.type='s' AND ase.uid='{$uid}'");

    while($supplier = $db->fetch_array($query3)) {
        $suppliers[$supplier['eid']]['eid'] = $supplier['eid'];
        $suppliers[$supplier['eid']]['name'] = $supplier['name'];
        $suppliers[$supplier['eid']]['affid'][] = $supplier['affid'];
    }

    $supp_counter = 1;
    if(is_array($suppliers)) {
        foreach($suppliers as $key => $val) {
            $affiliates_list_supplierssection = parse_selectlist("supplier[{$supp_counter}][affiliates][]", 0, $affiliates, $val['affid'], 1);
            if(value_exists('suppliersaudits', 'uid', $uid, "eid='{$val[eid]}'")) {
                $validator_checked[$val['eid']] = " checked='checked'";
            }
            eval("\$suppliers_rows .= \"".$template->get('admin_users_addedit_supplierrow')."\";");
            $supp_counter++;
        }
    }
    else {
        $affiliates_list_supplierssection = parse_selectlist("supplier[{$supp_counter}][affiliates][]", 0, $affiliates, '', 1);
        eval("\$suppliers_rows = \"".$template->get('admin_users_addedit_supplierrow')."\";");
    }

    /* Parse Customers Section - START */
    $query4 = $db->query("SELECT ase.eid
						FROM ".Tprefix."assignedemployees ase, ".Tprefix."users u, ".Tprefix."entities e
						WHERE ase.uid=u.uid AND e.eid=ase.eid AND e.type='c' AND ase.uid='{$uid}'");
    while($customer = $db->fetch_array($query4)) {
        $assignedcustomers[] = $customer['eid'];
    }

    $entity_attributes = array('eid', 'companyName');
    $entity_order = array(
            'by' => 'companyName',
            'sort' => 'ASC'
    );

    $customers = get_specificdata('entities', $entity_attributes, 'eid', 'companyName', $entity_order, 0, "type='c'");
    if(is_array($customers)) {
        foreach($customers as $cid => $value) {
            $rowclass = alt_row($rowclass);
            $aff_customers_query = $db->query("SELECT ae.*, a.name FROM ".Tprefix."affiliatedentities ae JOIN ".Tprefix."affiliates a ON (a.affid=ae.affid) WHERE ae.eid='{$cid}' ORDER BY a.name ASC");
            $comma = '';
            while($affiliatecustomers = $db->fetch_array($aff_customers_query)) {
                $affiliatedcustomers[$cid] .= $comma.$affiliatecustomers['name'];
                $comma = ', ';
            }

            $segments_customers_query = $db->query("SELECT ps.*, es.* FROM ".Tprefix." entitiessegments es JOIN ".Tprefix."productsegments ps ON (ps.psid=es.psid) WHERE es.eid='{$cid}' ORDER BY ps.title ASC");
            $comma = '';
            while($customerssegment = $db->fetch_array($segments_customers_query)) {
                $customerssegments[$cid] .= $comma.$customerssegment['title'];
                $comma = ', ';
            }

            $checked = '';
            if(is_array($assignedcustomers)) {
                if(in_array($cid, $assignedcustomers)) {
                    $rowclass = 'greenbackground';
                    $checked = ' checked="checked"';
                }
            }

            eval("\$customer_list .= \"".$template->get('admin_users_addedit_customerrow')."\";");
        }
    }
    /* Parse Customers Section - END */
    $telephone[1] = explode('-', $user['phone1']);
    $telephone[2] = explode('-', $user['phone2']);

    $telephone[1]['intcode'] = &$telephone[1][0];
    $telephone[1]['areacode'] = &$telephone[1][1];
    $telephone[1]['number'] = &$telephone[1][2];

    $telephone[2]['intcode'] = &$telephone[2][0];
    $telephone[2]['areacode'] = &$telephone[2][1];
    $telephone[2]['number'] = &$telephone[2][2];

    $user_position[$user['position']] = " selected='selected'";

    $actiontype = 'edit';
    $pagetitle = $user['username'];

    $uidfield = "<input type='hidden' value='{$uid}' name='uid'>";
    eval("\$editpage = \"".$template->get('admin_users_addedit')."\";");
    output_page($editpage);
}
else {
    if($core->input['action'] == 'do_perform_edit') {
        if(!empty($core->input['password']) || !empty($core->input['password2'])) {
            if($core->input['password'] != $core->input['password2']) {
                output_xml("<status>false</status><message>{$lang->passwordsnomatch}</message>");
                exit;
            }
        }

        if(empty($core->input['affiliates']['mainaffid'])) {
            output_xml("<status>false</status><message>{$lang->selectaffiliate}</message>");
            exit;
        }

        /* if(empty($core->input['spid'])) {
          output_xml("<status>false</status><message>{$lang->selectsupplier}</message>");
          exit;
          }

          if(empty($core->input['cid'])) {
          output_xml("<status>false</status><message>{$lang->selectcustomer}</message>");
          exit;
          } */

        if(is_empty($core->input['username'], $core->input['email'], $core->input['firstName'], $core->input['lastName'], $core->input['displayName'])) {
            output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
            exit;
        }

        $log->record($core->input['username']);
        unset($core->input['password2'], $core->input['action'], $core->input['module']);
        $modify = new ModifyAccount($core->input);
        if($modify->get_status() === true) {
            output_xml("<status>true</status><message>{$lang->profilesuccessfullyupdated}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorupdatingprofile}</message>");
        }
    }
}
?>