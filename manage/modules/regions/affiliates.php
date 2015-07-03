<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 *
 * Manage Affiliates
 * $module: admin/regions
 * $id: affiliates.php
 * Last Update: @zaher.reda 	Mar 18, 2009 | 04:03 PM
 */
if(!defined("DIRECT_ACCESS")) {
    die("Direct initialization of this file is not allowed.");
}

if($core->usergroup['canManageAffiliates'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

if(!$core->input['action']) {
    $query = $db->query("SELECT * FROM ".Tprefix."affiliates ORDER BY name ASC");
    if($db->num_rows($query) > 0) {
        while($affiliate = $db->fetch_array($query)) {
            $class = alt_row($class);

            $query2 = $db->query("SELECT name FROM ".Tprefix."countries WHERE affid='{$affiliate[affid]}'");
            $countries = $comma = '';

            while($country = $db->fetch_array($query2)) {
                $countries .= $comma.$country['name'];
                $comma = ', ';
            }
            $editpopup = '<a href="#'.$affiliate['affid'].'" id="editaffiliate_'.$affiliate['affid'].'_regions/affiliates_loadpopupbyid""><img src="'.$core->settings[rootdir].'/images/icons/edit.gif"/></a>';
            $affiliates_list .= "<tr class='{$class}'><td>{$affiliate[affid]}</td><td>{$affiliate[name]}</td><td>{$countries}</td><td>{$editpopup}</td></tr>"; //<td>{$description}</td>
        }
    }
    else {
        $affiliates_list = "<tr><td colspan='3' style='text-align: center;'>{$lang->noaffiliatesavailable}</td></tr>";
    }

    $countries_attributes = array('coid', 'name');
    $countries_order = array(
            'by' => 'name',
            'sort' => 'ASC'
    );

    $countries = get_specificdata('countries', $countries_attributes, 'coid', 'name', $countries_order);
    if(empty($countries)) {
        $countries[] = '';
    }
    $countries_list = parse_selectlist("coid[]", 2, $countries, '', 1);

    eval("\$affiliatespage = \"".$template->get("admin_regions_affiliates")."\";");
    output_page($affiliatespage);
}
else {
    if($core->input['action'] == 'do_add_affiliates') {
        if(empty($core->input['name']) || empty($core->input['coid'])) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
        }

        if(value_exists('affiliates', 'name', $core->input['name'])) {
            output_xml("<status>false</status><message>{$lang->affiliatealreadyexists}</message>");
            exit;
        }

        $countries = $core->input['coid'];

        unset($core->input['coid'], $core->input['module'], $core->input['action'], $core->input['sup_numrows']);

        $query = $db->insert_query('affiliates', $core->input);
        if($query) {
            $affiliate_id = $db->last_id();
            /* if(is_array($supervisors)) {
              foreach($supervisors as $key => $val) {
              $db->insert_query('affiliatessupervisors', array('affid' => $affiliate_id, 'uid'=>$val));
              }
              } */
            foreach($countries as $key => $val) {
                $db->update_query('countries', array('affid' => $affiliate_id), "coid='{$val}'");
            }
            log_action($core->input['name']);
            $lang->affiliateadded = $lang->sprint($lang->affiliateadded, $core->input['name']);
            output_xml("<status>true</status><message>{$lang->affiliateadded}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->erroraddingaffiliate}</message>");
        }
    }
//    elseif($core->input['action'] == 'update_charspec') {
//        $affiliates = Affiliates::get_affiliates();
//        if(is_array($affiliates)) {
//            foreach($affiliates as $affiliate) {
//                $affiliate->match_charspec();
//                $affiliate->upload_chartspecs();
//            }
//            echo('Saving might have happened, but again it might not.');
//            exit;
//        }
//        echo('No Saving Was DONE!');
//    }
    elseif($core->input['action'] == 'get_editaffiliate') {
        $id = $db->escape_string($core->input['id']);
        $affiliate = new Affiliates($id, false);
        if(is_object($affiliate)) {
            if(!empty($affiliate->chartSpec)) {
                $chartspec = unserialize($affiliate->chartSpec);
            }
        }
        eval("\$editaff = \"".$template->get("popup_edit_affiliates")."\";");
        echo($editaff);
    }
    elseif($core->input['action'] = 'perform_editaffiliate') {
        $specs = $core->input['spec'];
        $affiliate_obj = new Affiliates($db->escape_string($core->input['id']), false);
        $affiliate = $affiliate_obj->get();
        if(is_array($specs)) {
            $specs = serialize($specs);
            $affiliate['chartSpec'] = $specs;
        }
        $affiliate['chartColor'] = $db->escape_string($core->input['chartColor']);
        $query = $db->update_query('affiliates', $affiliate, 'affid ='.$affiliate['affid']);
        if($query) {
            output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
            exit;
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorsaving}</message>");
            exit;
        }
    }
}
?>