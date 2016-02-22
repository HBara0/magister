<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: salesdashboard.php
 * Created:        @rasha.aboushakra    Aug 5, 2015 | 5:55:53 PM
 * Last Update:    @rasha.aboushakra    Aug 5, 2015 | 5:55:53 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['crm_canGenerateSalesReports'] == 0) {
    error($lang->sectionnopermission);
}

ini_set('max_execution_time', 100);
require_once ROOT.INC_ROOT.'IntegrationOB_class.php';

$lang->load('crm_salesdashboard');
$dashbboard_currency = 'USD';
if(!$core->input['action']) {
    eval("\$livechart = \"".$template->get('crm_salesdashboard_livechart')."\";");
    eval("\$drilldown = \"".$template->get('crm_salesdashboard_drilldown')."\";");
    eval("\$combinedsalesbudget = \"".$template->get('crm_salesdashboard_salesvsbudget')."\";");
    eval("\$generatepage = \"".$template->get('crm_salesdashboard')."\";");
    output_page($generatepage);
}
else {
    require_once ROOT.INC_ROOT.'integration_init.php';
    $chartcurrency = new Currencies($dashbboard_currency);
    if($core->input['action'] == 'do_perform_totalsalesperyear') {
        $data['title'] = $lang->drilldownchartsalesperyear;
        $data['xaxislabel'] = $lang->affiliates;
        $data['yaxislabel'] = $lang->salestotalamount;
        $data['years'] = array("".(date('Y', TIME_NOW) - 2), "".(date('Y', TIME_NOW) - 1), "".(date('Y', TIME_NOW)));

        if(isset($core->input['affs'])) {
            $core->input['affs'] = explode(",", $core->input['affs']);
            if(is_array($core->input['affs'])) {
                $core->input['affs'] = implode("','", $core->input['affs']);
            }
            $affiliates_where = "(name IN ('".$core->input['affs']."'))";
            $affiliates = Affiliates::get_affiliates(array('name' => $affiliates_where, 'integrationOBOrgId' => 'integrationOBOrgId IS NOT NULL'), array('operators' => array('integrationOBOrgId' => 'CUSTOMSQL', 'name' => 'CUSTOMSQLSECURE')));
            $extrafilters['affid'] = $affiliates;
        }


        $permissionfilters = get_permissions($intgdb, $extrafilters);
        if(is_array($permissionfilters)) {
            foreach($permissionfilters as $key => $value) {
                $where .=' AND '.$key.' IN('.implode(', ', $value).')';
            }
        }
        $sql = "SELECT SUM(totallines) AS totallines, date_part('month', dateinvoiced) AS month, date_part('year', dateinvoiced) AS year, c_currency_id "
                ."FROM c_invoice "
                ."WHERE issotrx='Y' AND docstatus='CO' "
                ."AND issotrx='Y' AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime((date('Y', TIME_NOW) - 2).'-01-01'))."' AND '".date('Y-m-d 23:59:59', strtotime((date('Y', TIME_NOW)).'-12-31'))."')"
                .$where." GROUP BY c_currency_id,month, year";
        $query = $intgdb->query($sql);
        if($intgdb->num_rows($query) > 0) {
            while($line = $intgdb->fetch_assoc($query)) {
                $quarter = ceil($line['month'] / 3);
                $qmonths = get_quartermonths($quarter);
                $obcurrency_obj = new IntegrationOBCurrency($line['c_currency_id']);
                $currency_obj = new Currencies($obcurrency_obj->iso_code);

                if($chartcurrency->get_id() != $currency_obj->get_id()) {
                    $line['usdfxrate'] = $chartcurrency->get_fxrate_bytype('mavg', $currency_obj->alphaCode, array('year' => $line['year'], 'month' => $line['month']), array('precision' => 4), 'USD');
                    if(empty($line['usdfxrate'])) {
                        $line['usdfxrate'] = $chartcurrency->get_latest_fxrate($currency_obj->alphaCode, array('precision' => 4), 'USD');
                    }
                    if(empty($line['usdfxrate'])) {
                        continue;
                    }
                    if(in_array($line['month'], $qmonths)) {
                        $data['sales'][$line['year']][$quarter]['total'] += $line['totallines'] / $line['usdfxrate'];
                        $data['sales'][$line['year']][$quarter][$line['month']] += $line['totallines'] / $line['usdfxrate'];
                    }
                    $data['sales'][$line['year']]['total'] += $line['totallines'] / $line['usdfxrate'];
                }
                else {
                    if(in_array($line['month'], $qmonths)) {
                        $data['sales'][$line['year']][$quarter]['total'] += $line['totallines'];
                        $data['sales'][$line['year']][$quarter][$line['month']] += $line['totallines'];
                    }
                    $data['sales'][$line['year']]['total'] += $line['totallines'];
                }
            }
        }
        foreach($data['years'] as $year) {
            $year = intval($year);
            $data['salesperyear'][] = $data['sales'][$year]['total'];
            for($q = 1; $q < 5; $q++) {
                $data[$year][] = $data['sales'][$year][$q]['total'];
                $qmonths = get_quartermonths($q);
                foreach($qmonths as $month) {
                    if(!empty($data['sales'][$year][$q][$month])) {
                        $data[$year.'_'.$q][] = $data['sales'][$year][$q][$month]; //rand(100, 10000);
                    }
                    else {
                        $data[$year.'_'.$q][] = 0;
                    }
                }
            }
        }
        header("Content-Type: application/json");
        output(json_encode($data));
    }
    elseif($core->input['action'] == 'do_perform_livesales') {
        $chartproperties['title'] = $lang->livetotalsalesperorganisation.' ('.date('Y', TIME_NOW).')';
        $chartproperties['xaxislabel'] = $lang->affiliates;
        $chartproperties['yaxislabel'] = $lang->salestotalamount;

        if(isset($core->input['affs'])) {
            $extrafilters['affid'] = $core->input['affs'];
            $core->input['affs'] = explode(",", $core->input['affs']);
            if(is_array($core->input['affs'])) {
                $core->input['affs'] = implode("','", $core->input['affs']);
            }
            $affiliates_where = "(name IN ('".$core->input['affs']."'))";
            $affiliates = Affiliates::get_affiliates(array('name' => $affiliates_where, 'integrationOBOrgId' => 'integrationOBOrgId IS NOT NULL'), array('operators' => array('integrationOBOrgId' => 'CUSTOMSQL', 'name' => 'CUSTOMSQLSECURE'), 'returnarray' => true));
            $extrafilters['affid'] = $affiliates;
        }

        $lines = new IntegrationOBInvoiceLine(null, $intgdb);
        $permissionfilters = get_permissions($intgdb, $extrafilters);

        if(is_array($permissionfilters)) {
            foreach($permissionfilters as $key => $value) {
                $where .=' AND '.$key.' IN('.implode(', ', $value).')';
            }
        }

        $sales = $lines->get_totallines($where);
        if(is_array($permissionfilters['c_invoice.ad_org_id'])) {
            foreach($permissionfilters['c_invoice.ad_org_id'] as $orgid) {
                $orgid = trim($orgid, "'");
                $aff = Affiliates::get_affiliates(array('integrationOBOrgId' => $orgid));
                if(!empty($aff->name) && $aff->name != NULL) {
                    $chartproperties['affiliates'][] = $aff->name;
                    if(!empty($sales[$orgid]) && $sales[$orgid] != NULL) {
                        $chartproperties['sales'][] = $sales[$orgid];
                    }
                    else {
                        $chartproperties['sales'][] = 0;
                    }
                }
            }
        }
        header("Content-Type: application/json");
        output(json_encode($chartproperties));
    }
    elseif($core->input ['action'] == 'do_perform_combinedbudgetsales') {
        $chartproperties['title'] = $lang->budvsactualbyaff.' ('.date('Y', TIME_NOW).')';
        $chartproperties['linechartlabel'] = $lang->budget;

        $lines = new IntegrationOBInvoiceLine(null, $intgdb);
        if(isset($core->input['affs'])) {
            $core->input['affs'] = explode(",", $core->input['affs']);
            if(is_array($core->input['affs'])) {
                $core->input['affs'] = implode("','", $core->input['affs']);
            }
            $affiliates_where = "(name IN ('".$core->input['affs']."'))";
            $filteredaffiliates = Affiliates::get_affiliates(array('name' => $affiliates_where, 'integrationOBOrgId' => 'integrationOBOrgId IS NOT NULL'), array('returnarray' => true, 'operators' => array('integrationOBOrgId' => 'CUSTOMSQL', 'name' => 'CUSTOMSQLSECURE')));
            $extrafilters['affid'] = $filteredaffiliates;
        }

        $permissionfilters = get_permissions($intgdb, $extrafilters);
        if(is_array($permissionfilters)) {
            foreach($permissionfilters as $key => $value) {
                $where .=' AND '.$key.' IN('.implode(', ', $value).')';
            }
        }
        $sales = $lines->get_totallines($where);
        $fx_query = '*(CASE WHEN bbl.originalCurrency = 840 THEN 1
            ELSE (SELECT bfr.rate from budgeting_fxrates bfr WHERE bfr.affid = bb.affid AND bfr.year = bb.year AND bfr.fromCurrency = bbl.originalCurrency AND bfr.toCurrency = 840 AND isBudget=1) END)';


        if(is_array($permissionfilters['c_invoice.ad_org_id'])) {
            foreach($permissionfilters['c_invoice.ad_org_id'] as $orgid) {
                $orgid = trim($orgid, "'");
                $affiliates[] = Affiliates::get_affiliates(array('integrationOBOrgId' => $orgid));
            }
        }
        if(is_array($affiliates)) {
            foreach($affiliates as $affiliate) {
                $affiliatesfilter[$affiliate->affid] = $affiliate;
            }
        }
        if(is_array($affiliatesfilter)) {
            $affiliatesfilter = " AND affid IN (".implode(', ', array_keys($affiliatesfilter))." )";
        }
        $query = $db->query("SELECT affid, SUM(amount".$fx_query.") AS amount FROM budgeting_budgets_lines bbl JOIN budgeting_budgets bb ON (bbl.bid=bb.bid) WHERE year=".date('Y', TIME_NOW).$affiliatesfilter." GROUP by affid");
        if($db->num_rows($query) > 0) {
            while($budgetline = $db->fetch_assoc($query)) {
                $budget[$budgetline['affid']] = $budgetline['amount'];
            }
        }
        foreach($affiliates as $affiliate) {
            $chartproperties['affiliates'][] = $affiliate->name;
            if(!empty($budget[$affiliate->affid]) && $budget[$affiliate->affid] != NULL) {
                $chartproperties['budget'][] = $budget[$affiliate->affid];
            }
            else {
                $chartproperties['budget'][] = 0;
            }
            if(!empty($sales[$affiliate->integrationOBOrgId])) {
                $chartproperties['sales'][] = ($sales[$affiliate->integrationOBOrgId]);
            }
            else {
                $chartproperties['sales'][] = 0;
            }
        }

        header("Content-Type: application/json");
        output(json_encode($chartproperties));
    }
    elseif($core->input['action'] == 'get_affiliates') {
        $permissionfilters = get_permissions($intgdb, $extrafilters);
        if(is_array($permissionfilters['c_invoice.ad_org_id'])) {
            $where = 'integrationOBOrgId IN('.implode(', ', $permissionfilters['c_invoice.ad_org_id']).') AND integrationOBOrgId IS NOT NULL';
            $affiliates = Affiliates::get_affiliates(array('integrationOBOrgId' => $where), array('operators' => array('integrationOBOrgId' => 'CUSTOMSQLSECURE'), 'returnarray' => true));
            foreach($affiliates as $affiliate) {
                $data['affiliates'][] = $affiliate->name;
            }
            output(json_encode($data));
        }
    }
}
function get_quartermonths($q) {
    switch($q) {
        case 1:
            $qmonths = array(1, 2, 3);
            break;
        case 2:
            $qmonths = array(4, 5, 6);
            break;
        case 3:
            $qmonths = array(7, 8, 9);
            break;
        case 4:
            $qmonths = array(10, 11, 12);
            break;
        default:
            break;
    }
    return $qmonths;
}

function get_permissions($intgdb, $extrafilters) {
    global $core;
    $permissions = $core->user_obj->get_businesspermissions();
    $permissiontypes = array('affid' => 'c_invoice.ad_org_id'); //, 'uid' => 'c_invoice.salesrep_id');
    foreach($permissiontypes as $type => $col) {
        if(is_array($permissions[$type])) {
            $where = $type.' IN ('.implode(',', $permissions[$type]).')';
        }
        else {
            $where = $type.' <> 0';
        }
        $configs = array('operators' => array($type => 'CUSTOMSQL'), 'simple' => 'false', 'returnarray' => true);
        switch($type) {
            case 'affid':
                $configs['operators']['integrationOBOrgId'] = 'CUSTOMSQL';
                if(isset($extrafilters[$type]) && !empty($extrafilters[$type])) {
                    $affiliates = $extrafilters[$type];
                }
                else {
                    $affiliates = Affiliates::get_affiliates(array($type => $where, 'integrationOBOrgId' => 'integrationOBOrgId IS NOT NULL'), $configs);
                }
                foreach($affiliates as $affiliate) {
                    $filters[$col][] = "'".$affiliate->integrationOBOrgId."'";
                }
                break;
            case 'uid':
                $users = Users::get_data(array($type => $where), $configs);
                if(is_array($users)) {
                    foreach($users as $user) {
                        $usernames[] = $user->get_displayname();
                    }
                }
                $sql = "SELECT ad_user_id FROM ad_user WHERE name IN ('".implode("','", $usernames)."')";
                $query = $intgdb->query($sql);
                if($intgdb->num_rows($query) > 0) {
                    while($obuser = $intgdb->fetch_assoc($query)) {
                        $filters[$col][] = "'".$obuser['ad_user_id']."'";
                    }
                }
                break;
            default:
                break;
        }
        // $permissionsfilter .=' AND '.$col.' IN('.implode(', ', $filters[$type]).')';
        // }
    }
    return $filters; //$permissionsfilter;
}

?>