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
if(!$core->input['action']) {
    eval("\$livechart = \"".$template->get('crm_salesdashboard_livechart')."\";");
    eval("\$drilldown = \"".$template->get('crm_salesdashboard_drilldown')."\";");
    eval("\$combinedsalesbudget = \"".$template->get('crm_salesdashboard_salesvsbudget')."\";");
    eval("\$generatepage = \"".$template->get('crm_salesdashboard')."\";");
    output_page($generatepage);
}
else {
    require_once ROOT.INC_ROOT.'integration_init.php';
    //$integration = new IntegrationOB($intgconfig['openbravo']['database'], $intgconfig['openbravo']['entmodel']['client']);
    //$intgdb = $integration->get_dbconn();
    $chartcurrency = new Currencies('USD');
    if($core->input['action'] == 'do_perform_totalsalesperyear') {

        $data['title'] = $lang->drilldownchartsalesperyear;
        $data['xaxislabel'] = $lang->affiliates;
        $data['yaxislabel'] = $lang->salestotalamount;
        $data['years'] = array("".(date('Y', TIME_NOW) - 2), "".(date('Y', TIME_NOW) - 1), "".(date('Y', TIME_NOW)));


        $affiliates_where = '(affid IN ('.implode(',', $core->user['affiliates']).')';
        if(is_array($core->user['auditedaffids'])) {
            if(is_array($core->user['auditedaffids'])) {
                $affiliates_where .= ' OR (affid IN ('.implode(',', $core->user['auditedaffids']).')))';
            }
        }

        $affiliates = Affiliates::get_affiliates(array('affid' => $affiliates_where, 'integrationOBOrgId' => 'integrationOBOrgId IS NOT NULL'), array('operators' => array('integrationOBOrgId' => 'CUSTOMSQL', 'affid' => 'CUSTOMSQL')));

        $orgs = array_map(function ($value) {
            return $value->integrationOBOrgId;
        }, $affiliates);

        $sql = "SELECT SUM(totallines) AS totallines, date_part('month', dateinvoiced) AS month, date_part('year', dateinvoiced) AS year, c_currency_id FROM c_invoice WHERE issotrx='Y' AND docstatus='CO' AND c_invoice.ad_org_id IN ('".implode("','", $orgs)."') AND issotrx='Y' AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime((date('Y', TIME_NOW) - 2).'-01-01'))."' AND '".date('Y-m-d 00:00:00', strtotime((date('Y', TIME_NOW)).'-12-31'))."') GROUP BY c_currency_id, month, year";
        $query = $intgdb->query($sql);
        if($intgdb->num_rows($query) > 0) {
            while($line = $intgdb->fetch_assoc($query)) {
                $quarter = ceil($line['month'] / 3);
                $qmonths = get_quarter($quarter);
                $obcurrency_obj = new IntegrationOBCurrency($line['c_currency_id']);
                $currency_obj = new Currencies($obcurrency_obj->iso_code);
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
        }
        foreach($data['years'] as $year) {
            $year = intval($year);
            $data['salesperyear'][] = $data['sales'][$year]['total'];
            for($q = 1; $q < 5; $q++) {
                $data[$year][] = $data['sales'][$year][$q]['total'];
                $qmonths = get_quarter($q);
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
        $chartproperties['title'] = $lang->livetotalsalesperorganisation." (".date('Y', TIME_NOW).")";
        $chartproperties['xaxislabel'] = $lang->affiliates;
        $chartproperties['yaxislabel'] = $lang->salestotalamount;

        $affiliates_where = '(affid IN ('.implode(',', $core->user['affiliates']).')';
        if(is_array($core->user['auditedaffids'])) {
            if(is_array($core->user['auditedaffids'])) {
                $affiliates_where .= ' OR (affid IN ('.implode(',', $core->user['auditedaffids']).')))';
            }
        }

        $affiliates = Affiliates::get_affiliates(array('affid' => $affiliates_where, 'integrationOBOrgId' => 'integrationOBOrgId IS NOT NULL'), array('operators' => array('integrationOBOrgId' => 'CUSTOMSQL', 'affid' => 'CUSTOMSQL')));
        $orgs = array_map(function ($value) {
            return $value->integrationOBOrgId;
        }, $affiliates);

        $lines = new IntegrationOBInvoiceLine(null, $intgdb);
        ///////////////////////////// Replace by get_totallines() function
        $sql = "SELECT SUM(totallines) AS totallines, ad_org_id, c_currency_id, date_part('month', dateinvoiced) AS month, date_part('year', dateinvoiced) AS year FROM c_invoice WHERE c_invoice.ad_org_id IN ('".implode("','", $orgs)."') AND issotrx='Y' AND docstatus='CO' AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime((date('Y', TIME_NOW)).'-01-01'))."' AND '".date('Y-m-d 00:00:00', strtotime((date('Y', TIME_NOW)).'-12-31'))."') GROUP BY ad_org_id, c_currency_id, year, month";
        $query = $intgdb->query($sql);
        if($intgdb->num_rows($query) > 0) {
            while($invoiceline = $intgdb->fetch_assoc($query)) {
                $obcurrency_obj = new IntegrationOBCurrency($invoiceline['c_currency_id']);
                $currency_obj = new Currencies($obcurrency_obj->iso_code);
                $invoiceline['usdfxrate'] = $chartcurrency->get_fxrate_bytype('mavg', $currency_obj->alphaCode, array('year' => $invoiceline['year'], 'month' => $invoiceline['month']), array('precision' => 4), 'USD');
                if(empty($invoiceline['usdfxrate'])) {
                    $invoiceline['usdfxrate'] = $chartcurrency->get_latest_fxrate($currency_obj->alphaCode, array('precision' => 4), 'USD');
                }
                if(empty($invoiceline['usdfxrate'])) {
                    continue;
                }
                $sales['sales'][$invoiceline['ad_org_id']] = $invoiceline['totallines'] / $invoiceline['usdfxrate'];
            }
        }
        if(is_array($affiliates)) {
            foreach($affiliates as $affiliate) {
                $chartproperties['affiliates'][] = $affiliate->name;
                if(!empty($sales['sales'][$affiliate->integrationOBOrgId])) {
                    $chartproperties['sales'][] = ($sales['sales'][$affiliate->integrationOBOrgId]);
                }
                else {
                    $chartproperties['sales'][] = 0;
                }
            }
            header("Content-Type: application/json");
            output(json_encode($chartproperties));
        }
    }
    elseif($core->input ['action'] == 'do_perform_combinedbudgetsales') {
        $chartproperties['title'] = $lang->budvsactualbyaff." (".date('Y', TIME_NOW).")";
        $chartproperties['linechartlabel'] = $lang->budget;

        $lines = new IntegrationOBInvoiceLine(null, $intgdb);
        $sales = $lines->get_totallines();

        $affiliates_where = '(affid IN ('.implode(',', $core->user['affiliates']).')';
        if(is_array($core->user['auditedaffids'])) {
            if(is_array($core->user['auditedaffids'])) {
                $affiliates_where .= ' OR (affid IN ('.implode(',', $core->user['auditedaffids']).')))';
            }
        }
        $affiliates = Affiliates::get_affiliates(array('affid' => $affiliates_where.' AND integrationOBOrgId IS NOT NULL'), array('operators' => array('affid' => 'CUSTOMSQL')));
        foreach($affiliates as $affiliate) {
            $chartproperties['sales'][] = 0;
            $chartproperties['budget'][$affiliate->affid] = 0;
            $chartaffs['affiliates'][$affiliate->affid] = $affiliate->name;
            if(!empty($sales['sales'][$affiliate->integrationOBOrgId])) {
                $chartproperties['sales'][$affiliate->affid] = ($sales[$affiliate->integrationOBOrgId]); // rand(100, 1000); //
            }
        }
        $query = $db->query("SELECT affid, SUM(amount) AS amount FROM budgeting_budgets_lines JOIN budgeting_budgets ON (budgeting_budgets_lines.bid= budgeting_budgets.bid) WHERE year=".date('Y ', TIME_NOW)." AND affid IN (".implode(', ', array_keys($affiliates)).") GROUP by affid");
        if($db->num_rows($query) > 0) {
            while($budgetline = $db->fetch_assoc($query)) {
                $chartproperties['budget'][$affiliate->affid] = $budgetline['amount'];
            }
        }
        header("Content-Type: application/json");
        output(json_encode($chartproperties));
    }
}
function get_quarter($q) {
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

?>