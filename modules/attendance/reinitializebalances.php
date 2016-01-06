<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: reinitializebalances.php
 * Created:        @zaher.reda    Feb 16, 2015 | 9:19:50 PM
 * Last Update:    @zaher.reda    Feb 16, 2015 | 9:19:50 PM
 */

if($core->usergroup['canUseHR'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {
    if(!isset($core->input['affid']) || empty($core->input['affid'])) {
        $affid = $core->user['mainaffiliate'];
        if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
            if(is_array($core->user['hraffids']) && !empty($core->user['hraffids'])) {
                $affid = $core->user['mainaffiliate'];
                if(!in_array($core->user['mainaffiliate'], $core->user['hraffids'])) {
                    $affid = $core->user['hraffids'][current($core->user['hraffids'])];
                }
            }
            else {
                error($lang->sectionnopermission);
                exit;
            }
        }
    }
    else {
        $affid = $core->input['affid'];
        if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
            if(!in_array($core->input['affid'], $core->user['hraffids'])) {
                $affid = $core->user['mainaffiliate'];
                if(!in_array($core->user['mainaffiliate'], $core->user['hraffids'])) {
                    error($lang->sectionnopermission);
                    exit;
                }
            }
        }
    }

    /* Parse affiliates select list - START */
    if($core->usergroup['hr_canHrAllAffiliates'] == 1) {
        $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'));
    }
    else {
        if(is_array($core->user['hraffids']) && !empty($core->user['hraffids']) && count($core->user['hraffids']) > 0) {
            $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0, 'affid IN ('.implode(',', $core->user['hraffids']).')');
        }
    }

    if(is_array($affiliates)) {
        if(count($affiliates) == 1) {
            redirect($_SERVER['REQUEST_URI'].'&action=selectusers&affid='.key($affiliates));
        }
        $affid_field = parse_selectlist('affid', 1, $affiliates, $affid, 0);
    }
    else {
        $affid_field = '-';
    }
    /* Parse affiliates select list - END */

    eval("\$page[content] = \"".$template->get('attendance_reinitializebalances')."\";");
    eval("\$page_ouptput = \"".$template->get('reinbalance_container')."\";");
    output_page($page_ouptput);
}
else {
    if($core->input['action'] == 'selectusers') {
        /* Need further validation of permissions */
        if(empty($core->input['affid'])) {
            redirect('index.php?module=attendance/reinitializebalances');
        }
        /* Parse types list - START */
        $query = $db->query("SELECT * FROM ".Tprefix."leavetypes WHERE countWith=0 AND noBalance=0 ORDER BY name ASC");
        while($type = $db->fetch_assoc($query)) {
            if(!empty($lang->{$type['name']})) {
                $type['title'] = $lang->{$type['name']};
            }
            if(!empty($type['description'])) {
                $type['description'] = ' ('.$type['description'].')';
            }
            $leave_types[$type['ltid']] = $type['title'].$type['description'];
        }

        $types_list = parse_selectlist('type', 1, $leave_types, 1, 0);
        /* Parse types list - END */

        $users = Users::get_users('uid IN (SELECT uid FROM affiliatedemployees WHERE isMain=1 AND affid='.intval($core->input['affid']).')', array('returnarray' => true, 'order' => 'displayName'));
        foreach($users as $user) {
            $tablerows .= '<tr>';
            $tablerows .= '<td>'.parse_checkboxes('users', array($user->get_id() => $user->get_displayname())).'</td>';
            $tablerows .= '<td><input type="number" step="any" name="prevBalance['.$user->get_id().']"></td>';
            $tablerows .= '</tr>';
        }

        eval("\$page[content] = \"".$template->get('attendance_reinitializebalances_userselection')."\";");
        eval("\$page_ouptput = \"".$template->get('reinbalance_container')."\";");
        output_page($page_ouptput);
    }
    elseif($core->input['action'] == 'reinitialize') {
        /* Need further validation of permissions */
        if(empty($core->input['users'])) {
            redirect('index.php?module=attendance/reinitializebalances');
        }
        $core->input['users'] = array_map(intval, $core->input['users']);
        $users = Users::get_users('uid IN ('.implode(',', $core->input['users']).')', array('returnarray' => true));
        $leavetype = new LeaveTypes($core->input['type']);

        foreach($users as $user) {
            unset($leaves);
            reinitialize_balance($user, $core->input['type'], $core->input['prevBalance'][$user->get_id()]);
        }

        redirect('index.php?module=attendance/leavesstats', 1, $lang->successfullysaved);
    }
}
?>