<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 *
 * Add entities
 * $module: admin/entities
 * $id: add.php
 * Last Update: @zaher.reda 	July 19, 2010 | 03:49 PM
 */
if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canAddSuppliers'] == 0 && $core->usergroup['canAddCustomers'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    if(isset($core->input['type'])) {
        if($core->input['type'] == 'supplier') {
            $selected_type = 's';
        }
        else {
            $selected_type = 'c';
            $noqreportsend_disabled = $noqreportreq_disabled = $createreports_disabled = ' disabled';
        }
    }
    else {
        $noqreportsend_disabled = $noqreportreq_disabled = $createreports_disabled = ' disabled';
    }

    if($core->usergroup['canCreateReports'] == 0) {
        $createreports_disabled = ' disabled';
    }

    if($core->usergroup['canAddCustomers'] == 1) {
        $types['c'] = $lang->customer;
        $types['pc'] = $lang->potentialcustomer;
    }

    if($core->usergroup['canAddSuppliers'] == 1) {
        $types['s'] = $lang->supplier;
        $types['cs'] = $lang->competitorsupplier;
    }

    $types_list = parse_selectlist('type', 1, $types, $selected_type);
    $supptypes = array('t' => $lang->trader, 'p' => $lang->producer, 'b' => $lang->both);
    $supptypes_list = parse_selectlist('supplierType', 1, $supptypes, '', '', '');
    $presence = array('regional' => $lang->regional, 'local' => $lang->local, 'multinational' => $lang->multinational);
    $presence_list = parse_selectlist('presence', 2, $presence, $entity['presence']);

    $segments_list = parse_selectlist('psid[]', 3, get_specificdata('productsegments', array('psid', 'title'), 'psid', 'title', 'title'), '', 1);

    $affiliates_attributes = array('affid', 'name');
    $affiliates_order = array(
            'by' => 'name',
            'sort' => 'ASC'
    );

    $affiliates = get_specificdata('affiliates', $affiliates_attributes, 'affid', 'name', $affiliates_order);
    $affiliates_list = parse_selectlist("affid[]", 4, $affiliates, '', 1);

    $users_list = parse_selectlist("uids[]", 5, get_specificdata('users', array('uid', 'Concat(firstName, \' \', lastName) AS fullName'), 'uid', 'fullName', 'firstName'), '', 1);

    $countries_attributes = array('coid', 'name');
    $countries_order = array(
            'by' => 'name',
            'sort' => 'ASC'
    );

    $countries = get_specificdata('countries', $countries_attributes, 'coid', 'name', $countries_order);
    $countries_list = parse_selectlist('country', 8, $countries, '');

    //$representative_rows = " <tr id='1'><td><input type='text' id='repName_1' name='repName_1'/></td><td><input type='text' id='email' name='repEmail_1'/> <span id='repEmail_1_Validation'></span></td></tr>";
    $representative_rows = " <tr id='1'><td><input type='text' id='representative_1_autocomplete' autocomplete='off' size='40px'/><input type='hidden' id='representative_1_id' name='representative[1][rpid]'/><a href='#' id='addnew_entities/add_representative'><img src='../images/addnew.png' border='0' alt='{$lang->add}'></a><div id='searchQuickResults_1' class='searchQuickResults' style='display:none;'></div></td></tr>";
    $rep_counter = 1;

    $users_counter = 1;
    $affiliates_list_userssection = parse_selectlist("users[{$users_counter}][affiliates][]", 0, $affiliates, '', 1);
    eval("\$users_rows = \"".$template->get('admin_entities_addedit_userrow')."\";");

    $actiontype = 'add';
    $pagetitle = $lang->addentity;

    /* coverd countires section */
    $countries_objs = Countries::get_coveredcountries();

    if(is_array($countries_objs)) {
        foreach($countries_objs as $country) {
            $country->displayname = $country->get_displayname();
            eval("\$coveredcountries_rows .= \"".$template->get('admin_entities_addedit_contractinfo_ctryrow')."\";");
        }
    }
    eval("\$contractinfo_section = \"".$template->get('admin_entities_addedit_contractinfo')."\";");
    unset($coveredcountries_rows);
    $display['enttobeassigned'] = 'display:none;';
    eval("\$addpage = \"".$template->get('admin_entities_addedit')."\";");
    output_page($addpage);
}
else {
    if($core->input['action'] == 'do_perform_add') {
        if(isset($core->input['createReports']) && $core->input['createReports'] == 1) {
            $create_reports = true;
        }

        $entity_data = $core->input;
        unset($entity_data['module'], $entity_data['action'], $entity_data['createReports']);

        $entity_data['approved'] = 1;
        $entity = new Entities($entity_data);
        if($entity->get_status() === true) {
            $log->record($entity->get_eid());
            if($create_reports === true) {
                $current_quarter = currentquarter_info();
                foreach($core->input['affid'] as $key => $val) {
                    $newreport = array(
                            'quarter' => $current_quarter['quarter'],
                            'year' => $current_quarter['year'],
                            'affid' => $val,
                            'spid' => $entity->get_eid(),
                            'initDate' => TIME_NOW,
                            'status' => 0
                    );
                    $db->insert_query('reports', $newreport);
                }
            }
        }
    }
    elseif($core->input['action'] == 'do_add_representative') {
        $representative = new Entities($core->input, 'add_representative');

        if($representative->get_status() === true) {
            output_xml("<status>true</status><message>{$lang->representativecreated}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorcreatingreprentative}</message>");
        }
    }
    elseif($core->input['action'] == 'do_uploadlogo') {
        $file = new Entities(array('fieldname' => 'uploadfile', 'file' => $_FILES), 'set_entitylogo');
        ?>
        <script language="javascript" type="text/javascript">
            window.top.$('input[id="logo"]').attr('value', '<?php echo $file->get_uploaded_logo();?>');
            window.top.$('#entitylogo_placeholder').html('<img src="../uploads/entitieslogos/<?php echo $file->get_uploaded_logo();?>" width="200" />');
            window.top.$('#popup_setentitylogo').dialog('close');
        </script>
        <?php
    }
    elseif($core->input['action'] == 'get_addnew_representative') {
        eval("\$addrepresentativebox = \"".$template->get("popup_addrepresentative")."\";");
        echo $addrepresentativebox;
    }
    elseif($core->input['action'] == 'getentitiestobeassigned') {
        $display['enttobeassigned'] = 'display:block;';
        $fullcompanyname = explode(" ", $core->input['companyName']);
        if(is_array($fullcompanyname)) {
            foreach($fullcompanyname as $name) {
                $extra_where_filter = ' isActive=1 AND approved=1 AND type="'.$core->input['type'].'" AND eid IN (SELECT eid from affiliatedentities where affid='.$core->user['mainaffiliate'].')';
                if(isset($core->input['type']) && !empty($core->input['type'])) {
                    $extra_where_filter .=' AND supplierType="'.$core->input['supplierType'].'"';
                }
                $results_list[] = quick_search('entities', array('companyName', 'companyNameAbbr'), $name, array('companyName'), 'eid', array('extrainput' => $extrainput, 'returnType' => $core->input['returnType'], 'order' => array('by' => 'companyName', 'sort' => 'ASC'), 'extra_where' => $extra_where_filter, 'descinfo' => 'country', 'disableSoundex' => $disableSoundex, 'source' => 'addentity'));
            }
        }
        if(is_array($results_list)) {
            foreach($results_list as $results) {
                if(is_array($results)) {
                    foreach($results as $eid => $companyname) {
                        $ent_tobeassigned[$eid] = $companyname;
                    }
                }
            }
        }
        switch($core->input['type']) {
            case 'c':
                $type = 'Customer';
                break;
            case 'pc':
                $type = 'Potential Customer';
                break;
            case 's':
                $type = 'Supplier';
                break;
            case 'cs':
                $type = 'Competitor Supplier';
                break;

            default:
                break;
        }
        $usermain_affilite = new Affiliates($core->user['mainaffiliate']);
        if(is_array($ent_tobeassigned)) {
            foreach($ent_tobeassigned as $key => $value) {
                $checked = $rowclass = '';
                $ent_tobeassigned_list .='<tr class="'.$rowclass.'">';
                $ent_tobeassigned_list .='<td><input id="tobeassigned_'.$key.'" type="checkbox"'.$checked.' value="'.$key.'"><a href='.$core->settings['rootdir'].'"index.php?module=profiles/entityprofile&eid='.$key.'" target="_blank">'.$value.'</a></td><td>'.$type.'</td><td>'.$usermain_affilite->get_displayname().'</td></tr>';
            }
        }

        eval("\$ent_list = \"".$template->get("admin_entities_add_entlist")."\";");
        output($ent_list);
    }
    elseif($core->input['action'] == 'assignemployee') {
        $eids = explode(",", $core->input['eid']);
        $eids = array_filter($eids);
        $assignedemployee = new AssignedEmployees();
        if(is_array($eids)) {
            foreach($eids as $eid) {
                $assignedemployee->save(array('eid' => $eid, 'uid' => $core->user['uid'], 'affid' => $core->user['mainaffiliate']));
            }
        }
    }
}
?>