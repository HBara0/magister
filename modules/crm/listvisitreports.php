<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * List CRM visit reports
 * $module: CRM
 * $id: listvisitreports.php
 * Created: 	@zaher.reda 	July 27, 2009 | 10:20 AM
 * Last Update: @zaher.reda 	July 06, 2012 | 04:29 PM
 */

if(!defined("DIRECT_ACCESS")) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['crm_canViewVisitReports'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$lang->load('crm_visitreport');
if(!$core->input['action']) {
    $sort_query = 'date DESC';
    if(isset($core->input['sortby'], $core->input['order'])) {
        $sort_query = $core->input['sortby'].' '.$core->input['order'];
    }
    $sort_url = sort_url();
    $limit_start = 0;

    if(isset($core->input['start'])) {
        $limit_start = intval($core->input['start']);
    }

    if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
        $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
    }

    /**
     * Get business permissions of user and parse where statement part
     */
    $permissions = $core->user_obj->get_businesspermissions();
    $permissiontypes = array('affid' => 'affid', 'cid' => 'cid', 'uid' => 'vr.uid');
    foreach($permissiontypes as $type => $col) {
        if(isset($permissions[$type]) && !empty($permissions[$type])) {
            $permissionsfilter .= ' AND '.$col.' IN ('.implode(',', $permissions[$type]).')';
        }
    }
//    if($core->usergroup['canViewAllAff'] == 0) {
//        $query_where = ' AND affid IN ('.implode(', ', array_unique($core->user['affiliates'])).')';
//        $query_where_and = ' AND ';
//    }
//    if($core->usergroup['canViewAllCust'] == 0) {
//        if(is_array($core->user['customers'])) {
//            $query_where .= $query_where_and.'cid IN ('.implode(', ', $core->user['customers']).')';
//        }
//    }
//    else {
//        if(isset($core->user['auditedaffids'])) {
//            $query_where = ' AND affid IN ('.implode(',', $core->user['auditedaffids']).')';
//            $query_where_and = ' AND ';
//        }
//    }
    if(isset($permissionsfilter) && !empty($permissionsfilter)) {
        $query_or_usercreator = 'OR vr.uid='.$core->user['uid'];
    }
    /* Perform inline filtering - START */
    $filters_config = array(
            'parse' => array('filters' => array('checkbox', 'customer', 'employee', 'type', 'date'),
                    'overwriteField' => array('checkbox' => '')
            ),
            'process' => array(
                    'filterKey' => 'vrid',
                    'mainTable' => array(
                            'name' => 'visitreports',
                            'filters' => array('customer' => 'cid', 'employee' => 'uid', 'date', 'type')
                    )
            )
    );

    $filter = new Inlinefilters($filters_config);
    $filter_where_values = $filter->process_multi_filters();
    $filters_row_display = 'hide';
    if(is_array($filter_where_values)) {
        $filters_row_display = 'show';
        $filter_where = ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
        $multipage_filter_where = ' AND '.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
    }
    $filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
    /* Perform inline filtering - END */

    $query = $db->query("SELECT vr.*, vr.cid AS customer, displayName AS employeename, e.companyName as customername, e.companyNameAbbr
						 FROM ".Tprefix."visitreports vr
						 JOIN ".Tprefix."users u ON (u.uid=vr.uid)
						 JOIN ".Tprefix."entities e ON (vr.cid=e.eid)
                                                 WHERE (isDraft IN (0,1){$permissionsfilter}){$query_or_usercreator}{$filter_where}
						 ORDER BY {$sort_query}");

    if($db->num_rows($query) > 0) {
        while($visitreport = $db->fetch_assoc($query)) {
            //$query2 = $db->query("SELECT * FROM ".Tprefix."visitreports_reportsuppliers WHERE vrid='$visitreport[vrid]' AND ");
            if($core->usergroup['canViewAllSupp'] == 0) {
                if(is_array($core->user['suppliers']['eid'])) {
                    if($visitreport['hasSupplier'] == 1 && !value_exists('visitreports_reportsuppliers', 'vrid', $visitreport['vrid'], 'spid IN ('.implode(', ', $core->user['suppliers']['eid']).')')) {
                        continue;
                    }
                }
            }
            $row_class = alt_row($row_class);

            $vrid_cache[] = $visitreport['vrid'];

            $icon_locked = '';
            if($visitreport['isLocked'] == 1) {
                $icon_locked = '_locked';
                $visitreport['status_text'] = $lang->finalized.$lang->andlocked;
            }

            if($core->usergroup['canLockUnlockReports'] == 1 || $core->user['uid'] == $visitreport['uid']) {
                $checkbox[$visitreport['vrid']] = "<input type='checkbox' id='checkbox_{$visitreport[vrid]}' name='listCheckbox[]' value='{$visitreport[vrid]}'/>";

                $icon[$visitreport['vrid']] = "<a href='index.php?module=crm/previewvisitreport&amp;referrer=list&amp;vrid={$visitreport[vrid]}'><img src='images/icons/report{$icon_locked}.gif' alt='{$visitreport[status_text]}' border='0'/></a>";
                if($visitreport['isDraft'] == 1) {
                    $draft[$visitreport['vrid']] = "<a href='index.php?module=crm/listvisitreports&amp;val=0&amp;action=do_draft&amp;vrid={$visitreport[vrid]}'><img src='images/valid.gif' title='".$lang->isdraft."' alt='".$lang->undraft."' border='0'/></a>";
                }
                else {
                    $draft[$visitreport['vrid']] = "<a href='index.php?module=crm/listvisitreports&amp;val=1&amp;action=do_draft&amp;vrid={$visitreport[vrid]}'><img src='images/invalid.gif' title='".$lang->isnotdraft."' alt='".$lang->draft."' border='0'/></a>";
                }
            }
            list($visitreport['suppliername']) = get_specificdata('entities', array('companyName'), '0', 'companyName', '', 0, "eid = '{$visitreport[spid]}'");
            //list($visitreport['customername']) = get_specificdata('entities', 'companyName', '0', 'companyName', '', 0, "eid = '{$visitreport[cid]}'");
            if(!empty($visitreport['companyNameAbbr'])) {
                $visitreport['customername'] .= ' ('.$visitreport['companyNameAbbr'].')';
            }
            parse_calltype($visitreport['type']);

            $visitreport['formatteddate'] = date($core->settings['dateformat'], $visitreport['date']);

            eval("\$reportslist .= \"".$template->get('crm_visitreportslist_reportrow')."\";");
        }

        if($core->usergroup['canLockUnlockReports'] == 1) {
            $buttons_row = "<tr><td colspan='4'><div id='moderation_crm/listvisitreports_Results'></div>&nbsp;</td><td style='text-align: right;' colspan='2'><select id='moderationtools' name='moderationtools'><option>&nbsp;</option><option value='lockunlock'>{$lang->lockunlock}</option></select></td></tr>";
        }

        if(!empty($vrid_cache)) {
            $multipages = new Multipages('visitreports', $core->settings['itemsperlist'], 'vrid IN ('.implode(', ', $vrid_cache).')');
            $reportslist .= "<tr><td colspan='4'>".$multipages->parse_multipages()."&nbsp;</td><td style='text-align: right;' colspan='2'>&nbsp;</td></tr>"; //<a href='".$_SERVER['REQUEST_URI']."&amp;action=exportexcel'><img src='images/icons/xls.gif' alt='{$lang->exportexcel}' border='0' /></a></td></tr>";
        }
        else {
            $reportslist = "<tr><td colspan='7' align='center'>{$lang->novisitreportsavailable}</td></tr>";
        }
    }
    else {
        $reportslist = '<tr><td colspan="7" align="center">'.$lang->novisitreportsavailable.'</td></tr>';
    }
    eval("\$listpage = \"".$template->get('crm_visitreportslist')."\";");
    output_page($listpage);
}
else {
    if($core->input['action'] == 'exportexcel') {
        $sort_query = 'date DESC';
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = $core->input['sortby']." ".$core->input['order'];
        }

        if($core->usergroup['canViewAllEmp'] == 0) {
            $extra_where = " AND vr.uid = '{$core->user[uid]}' ";
        }
        else {
            if($core->usergroup['canViewAllAff'] == 0) {
                $inaffiliates = implode(',', $core->user['affiliates']);

                $query = $db->query("SELECT uid FROM ".Tprefix."affiliatedemployees WHERE affid IN ({$inaffiliates})");
                while($user_uid = $db->fetch_array($query)) {
                    $inuid .= "{$comma}{$user_uid[uid]}";
                    $comma = ', ';
                }
                $extra_where .= " AND vr.uid IN ({$inuid}) ";
            }
        }

        if($core->usergroup['canViewAllSupp'] == 0) {
            $insuppliers = implode(',', $core->user['suppliers']);
            $extra_where .= "  AND vr.spid IN ({$insuppliers}) ";
        }

        if($core->usergroup['canViewAllCust'] == 0) {
            $incustomers = implode(',', $core->user['customers']);
            $extra_where .= "  AND vr.cid IN ({$incustomers}) ";
        }

        $query = $db->query("SELECT vr.cid AS customer, vr.spid AS supplier, Concat(u.firstName, ' ', u.lastName) AS employeename, vr.type, vr.date
						 FROM ".Tprefix."visitreports vr, ".Tprefix."users u, ".Tprefix."entities e
						 WHERE u.uid=vr.uid AND vr.cid=e.eid {$filter_where}{$extra_where}
						 ORDER BY {$sort_query}");

        if($db->num_rows($query) > 0) {
            $visitreports[0]['customername'] = $lang->customername;
            $visitreports[0]['suppliername'] = $lang->suppliername;
            $visitreports[0]['employeename'] = $lang->prepareby;
            $visitreports[0]['type'] = $lang->calltype;
            $visitreports[0]['date'] = $lang->dateofvisit;

            $i = 1;
            while($visitreport[$i] = $db->fetch_assoc($query)) {
                list($visitreports[$i]['customername']) = get_specificdata('entities', 'companyName', '0', 'companyName', '', 0, "eid='{$visitreport[$i][customer]}'");
                list($visitreports[$i]['suppliername']) = get_specificdata('entities', array('companyName'), '0', 'companyName', '', 0, "eid='{$visitreport[$i][supplier]}'");

                $visitreports[$i]['employeename'] = $visitreport[$i]['employeename'];

                $visitreports[$i]['type'] = $visitreport[$i]['type'];
                parse_calltype($visitreports[$i]['type']);

                $visitreports[$i]['date'] = date($core->settings['dateformat'], $visitreport[$i]['date']);
                $i++;
            }

            $excelfile = new Excel('array', $visitreports);
        }
    }
    elseif($core->input['action'] == 'do_lockunlock_listvisitreports') {
        if($core->input['moderationtools'] != 'lockunlock') {
            exit;
        }

        if(count($core->input['listCheckbox']) > 0) {
            foreach($core->input['listCheckbox'] as $key => $val) {
                $vrid = $db->escape_string($val);

                list($current_status) = get_specificdata('visitreports', array('isLocked'), '0', 'isLocked', '', 0, "vrid='{$vrid}'");

                if($current_status == 0) {
                    $new_status = 1;
                }
                else {
                    $new_status = 0;
                }
                $db->update_query('visitreports', array('isLocked' => $new_status), "vrid='{$vrid}'");
            }
            output_xml("<status>true</status><message>{$lang->lockchanged}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->selectatleastonereport}</message>");
        }
    }
    elseif($core->input['action'] == 'do_draft') {
        $visitreport = new VisitReports(intval($core->input['vrid']), false);
        $visitreport->isDraft = intval($core->input['val']);
        $visitreport->update($visitreport->get());
        redirect(''.$core->settings['rootdir'].'/index.php?module=crm/listvisitreports');
    }
}
function parse_calltype(&$value) {
    global $lang;

    switch($value) {
        case '1':
            $value = $lang->facetoface;
            break;
        case '2':
            $value = $lang->telephonecall;
            break;
        default: break;
    }
}

?>