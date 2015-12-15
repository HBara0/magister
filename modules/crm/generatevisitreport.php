<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Generate visit reports
 * $module: CRM
 * $id: generatevisitreport.php
 * Created: 	@zaher.reda 	August 13, 2009 | 11:23 AM
 * Last Update: @zaher.reda		April 28, 2011 | 06:47 PM
 */

if(!defined("DIRECT_ACCESS")) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['crm_canGenerateVisitReports'] == 0) {
    error($lang->sectionnopermission);
    exit;
}

$lang->load('crm_visitreport');
if(!$core->input['action']) {
    if($core->usergroup['canViewAllCust'] == 0) {
        $incustomers = implode(',', $core->user['customers']);
        $customers_extra_where = ' AND eid IN ('.$incustomers.') ';
    }
    else {
        if($core->usergroup['canViewAllAff'] == 0) {
            $customers_extra_where = ' AND affid IN ('.implode(', ', $core->user['affiliates']).')';
        }
    }
    if($core->usergroup['canViewAllSupp'] == 0) {
        $insuppliers = implode(',', $core->user['suppliers']['eid']);
        $suppliers_extra_where .= '  AND eid IN ('.$insuppliers.') ';
    }

    $additional_where = getquery_entities_viewpermissions();

    $entity_order = ' ORDER BY e.companyName ASC';

    $suppliers = array();
    $suppliers_query = $db->query("SELECT DISTINCT(e.eid), e.companyName FROM ".Tprefix."entities e  JOIN ".Tprefix."visitreports_reportsuppliers vrs ON (e.eid = vrs.spid) JOIN ".Tprefix."visitreports vr ON (vrs.vrid = vr.vrid) WHERE e.type = 's'{$suppliers_extra_where}{$entity_order}");
    while($supplier = $db->fetch_assoc($suppliers_query)) {
        $suppliers[$supplier['eid']] = $supplier['companyName'];
    }

    $customers = array();
    $customers_query = $db->query("SELECT DISTINCT(e.eid), e.companyName FROM ".Tprefix."entities e RIGHT JOIN ".Tprefix."visitreports vr ON (e.eid = vr.cid) WHERE e.type = 'c'{$customers_extra_where}{$entity_order}");
    while($customer = $db->fetch_assoc($customers_query)) {
        $customers[$customer['eid']] = $customer['companyName'];
    }

    if($core->usergroup['canViewAllEmp'] == 0) {
        $employees_where = " WHERE vr.uid = '{$core->user[uid]}'";
    }
    else {
        if($core->usergroup['canViewAllAff'] == 0) {
            $inaffiliates = implode(',', $core->user['affiliates']);

            $query = $db->query("SELECT uid FROM ".Tprefix."affiliatedemployees WHERE affid IN ({$inaffiliates})");
            while($user_uid = $db->fetch_array($query)) {
                $inuid .= $comma.$user_uid['uid'];
                $comma = ', ';
            }
            $employees_where = ' WHERE vr.uid IN ('.$inuid.')';
        }
    }

    $employee_order = ' ORDER BY u.username ASC';
    $employees_query = $db->query("SELECT DISTINCT(u.uid), u.username FROM ".Tprefix."users u RIGHT JOIN ".Tprefix."visitreports vr ON (u.uid = vr.uid){$employees_where}{$employee_order}");

    while($employee = $db->fetch_assoc($employees_query)) {
        $employees[$employee['uid']] = $employee['username'];
    }

    $customers_list = parse_selectlist('cid', 8, $customers, '');
    $suppliers_list = parse_selectlist('spid[]', 9, $suppliers, '', 1);
    if(is_array($employees)) {
        $employees_list = parse_selectlist('uid[]', 10, $employees, '', 1);
    }
    else {
        $employees_list = $lang->na;
    }

    //$headerinc .= "<link href='{$core->settings[rootdir]}/css/jqueryuitheme/jquery-ui-1.7.2.custom.css' rel='stylesheet' type='text/css' />";
    eval("\$generatereportpage = \"".$template->get('crm_generatevisitreport')."\";");
    output_page($generatereportpage);
}
else {
    if($core->input['action'] == 'generate') {
        if(is_empty($core->input['spid'], $core->input['cid'], $core->input['uid'])) {
            error($lang->inforefelectnotcomplete, 'index.php?module=crm/generatevisitreport');
        }

        if(is_empty($core->input['fromDate'], $core->input['toDate'])) {
            error($lang->nodaterange, 'index.php?module=crm/generatevisitreport');
        }

        $core->input['fromDate'] = explode('-', $core->input['fromDate']);
        $core->input['toDate'] = explode('-', $core->input['toDate']);

        if(!checkdate($core->input['fromDate'][1], $core->input['fromDate'][0], $core->input['fromDate'][2]) || !checkdate($core->input['toDate'][1], $core->input['toDate'][0], $core->input['toDate'][2])) {
            error($lang->invaliddaterange, 'index.php?module=crm/generatevisitreport');
        }

        $date_from = mktime(0, 0, 0, $core->input['fromDate'][1], $core->input['fromDate'][0], $core->input['fromDate'][2]);
        $date_to = mktime(0, 0, 0, $core->input['toDate'][1], $core->input['toDate'][0], $core->input['toDate'][2]);

        if($core->usergroup['canViewAllCust'] == 0) {
            remove_unauthorized_selections($core->input['cid'], $core->user['customers']);
        }

        if($core->input['showBy'] != '1' && is_array($core->input['cid'])) {
            $extra_where = " AND cid IN (".implode(',', $core->input['cid']).")";
        }

        if($core->usergroup['canViewAllSupp'] == 0) {
            remove_unauthorized_selections($core->input['spid'], $core->user['suppliers']['eid']);
        }

        if($core->input['showBy'] != '2' && is_array($core->input['spid'])) {
            $extra_where .= " AND vrs.spid IN (".implode(',', $core->input['spid']).")";
        }

        if($core->usergroup['canViewAllEmp'] == 0) {
            $extra_where .= ' AND vr.uid = "'.$core->user['uid'].'"';
        }
        else {
            if($core->usergroup['canViewAllAff'] == 0) {
                $query = $db->query("SELECT uid FROM ".Tprefix."affiliatedemployees WHERE affid IN (".implode(',', $core->user['affiliates']).")");
                while($user_uid = $db->fetch_array($query)) {
                    $inuid .= $comma.$user_uid['uid'];
                    $comma = ', ';
                }
                $extra_where .= ' AND vr.uid IN ('.$inuid.')';
            }
            else {
                if($core->input['showBy'] != '3' && is_array($core->input['uid'])) {
                    $extra_where .= ' AND vr.uid IN ('.implode(',', $core->input['uid']).')';
                }
            }
        }

        switch($core->input['showBy']) {
            case '1':
                $table_header_contd = '<th>'.$lang->salesperson.'</th><th>'.$lang->suppliername.'</th>';
                $query_attribute = 'cid';
                $query_attribute_prefix = 'vr';
                list($mainsubject) = get_specificdata('entities', 'companyName', '0', 'companyName', '', 0, "eid='{$core->input[cid]}'");
                break;
            case '2':
                $table_header_contd = '<th>'.$lang->customername.'</th><th>'.$lang->salesperson.'</th>';
                $query_attribute = 'spid';
                $query_attribute_prefix = 'vrs';
                list($mainsubject) = get_specificdata('entities', 'companyName', '0', 'companyName', '', 0, "eid='{$core->input[spid]}'");
                break;
            case '3':
                $table_header_contd = '<th>'.$lang->customername.'</th><th>'.$lang->suppliername.'</th>';
                $query_attribute = 'uid';
                $query_attribute_prefix = 'vr';
                list($mainsubject) = get_specificdata('users', 'Concat(firstName, \' \', lastName) AS employeename', '0', 'employeename', '', 0, "uid='{$core->input[uid]}'");
                break;
            default:
                redirect('index.php?module=crm/generatevisitreport');
                break;
        }

        if($core->input['generateType'] == 1) {
            $query = $db->query("SELECT DISTINCT(vr.vrid), vr.*, e.companyName as suppliername, Concat(u.firstName, ' ', u.lastName) AS employeename, u.uid
						 FROM ".Tprefix."visitreports vr JOIN ".Tprefix."users u
						 JOIN ".Tprefix."visitreports_reportsuppliers vrs ON (vrs.vrid = vr.vrid)
						 JOIN ".Tprefix."entities e ON (vrs.spid = e.eid)
						 WHERE u.uid=vr.uid AND (vr.date BETWEEN {$date_from} AND {$date_to}) AND {$query_attribute_prefix}.{$query_attribute} = '".$db->escape_string($core->input[$query_attribute])."'{$extra_where}
						 ORDER BY vr.date DESC");
        }
        elseif($core->input['generateType'] == 2) {
            $query = $db->query("SELECT DISTINCT(vr.vrid)
							FROM ".Tprefix."visitreports vr JOIN ".Tprefix."visitreports_reportsuppliers vrs ON (vrs.vrid = vr.vrid) JOIN ".Tprefix."entities e ON (vrs.spid = e.eid)
							WHERE (date BETWEEN {$date_from} AND {$date_to}) AND {$query_attribute_prefix}.{$query_attribute} = '".$db->escape_string($core->input[$query_attribute])."'{$extra_where}");
            if($db->num_rows($query) > 0) {
                while($report = $db->fetch_array($query)) {
                    $reports[] = $report['vrid'];
                }

                $reports_query = base64_encode(serialize($reports));

                redirect("index.php?module=crm/previewvisitreport&amp;referrer=generate&amp;vrid={$reports_query}&amp;incCompetitionDetails={$core->input[incCompetitionDetails]}&amp;incVisitDetails={$core->input[incVisitDetails]}&amp;incCommentsCompetition={$core->input[incCommentsCompetition]}&amp;showLimitedCustDetails={$core->input[showLimitedCustDetails]}");
            }
            else {
                error($lang->novisitreports, 'index.php?module=crm/generatevisitreport');
            }
        }
        else {
            redirect('index.php?module=crm/generatevisitreport');
        }

        //$pie_data =  array();
        if($db->num_rows($query) > 0) {
            //$count = 0;
            $cachearr['vrid'] = array();

            while($visitreport = $db->fetch_assoc($query)) {
                $rowclass = alt_row($rowclass);

                list($visitreport['customername']) = get_specificdata('entities', 'companyName', '0', 'companyName', '', 0, "eid='{$visitreport[cid]}'");

                parse_calltype($visitreport['type']);

                $visits_list .= '<tr class="'.$rowclass.'"><td>'.$visitreport['type'].'</td><td>'.date($core->settings['dateformat'], $visitreport['date']).'</td>';

                switch($core->input['showBy']) {
                    case '1':
                        $visits_list .= '<td>'.$visitreport['employeename'].'</td><td>'.$visitreport['suppliername'].'</td>';
                        $pie_data['titles'][$visitreport['uid']] = $visitreport['employeename'];
                        $pie_data['values'][$visitreport['uid']] += 1;
                        break;
                    case '2':
                        $visits_list .= '<td>'.$visitreport['customername'].'</td><td>'.$visitreport['employeename'].'</td>';
                        $pie_data['titles'][$visitreport['cid']] = $visitreport['customername'];
                        $pie_data['values'][$visitreport['cid']] += 1;
                        break;
                    case '3':
                        $visits_list .= '<td>'.$visitreport['customername'].'</td><td>'.$visitreport['suppliername'].'</td>';
                        $pie_data['titles'][$visitreport['cid']] = $visitreport['customername'];
                        $pie_data['values'][$visitreport['cid']] += 1;
                        break;
                    default: break;
                }

                $icon_locked = '';
                if($visitreport['isLocked'] == 1) {
                    $icon_locked = '_locked';
                    $visitreport['status_text'] = $lang->finalized.$lang->andlocked;
                }

                $visits_list .= '<td style="text-align:right;"><a href="index.php?module=crm/previewvisitreport&amp;referrer=list&amp;vrid='.$visitreport['vrid'].'"><img src="images/icons/report'.$icon_locked.'.gif" alt="'.$visitreport['status_text'].'" border="0"/></a></td>';
                $visits_list .= '</tr>';

                if(!in_array($visitreport['vrid'], $cachearr['vrid'])) {
                    $date_info = getdate($visitreport['date']);
                    if(isset($days[$date_info['mday'].'_'.$date_info['wday'].'_'.$date_info['mon'].'_'.$date_info['year']])) {
                        $days[$date_info['mday'].'_'.$date_info['wday'].'_'.$date_info['mon'].'_'.$date_info['year']] += 1;
                    }
                    else {
                        $days[$date_info['mday'].'_'.$date_info['wday'].'_'.$date_info['mon'].'_'.$date_info['year']] = 1;
                    }
                }
                $cachearr['vrid'][$visitreport['vrid']] = $visitreport['vrid'];
                //$count++;
            }

            $lang->numberreportsbetweendates = $lang->sprint($lang->numberreportsbetweendates, count($cachearr['vrid']), date($core->settings['dateformat'], $date_from), date($core->settings['dateformat'], $date_to));
            if(is_array($days)) {
                $lang->maxvisistperday = $lang->sprint($lang->maxvisistperday, max($days));
            }

            if(count($pie_data['titles']) > 1) {
                $pie = new Charts(array('titles' => $pie_data['titles'], 'values' => $pie_data['values']), 'pie');
                $piechart = '<img src='.$pie->get_chart().' />';
            }
            eval("\$generatedreportpage = \"".$template->get('crm_generatevisitreport_statisticalreport')."\";");
            output_page($generatedreportpage);
        }
        else {
            error($lang->novisitreports, 'index.php?module=crm/generatevisitreport');
        }
    }
}
function remove_unauthorized_selections(&$array, $assigns) {
    global $core;

    if(is_array($array)) {
        foreach($array as $key => $val) {
            if(!in_array($val, $assigns)) {
                unset($array[$key]);
            }
        }
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