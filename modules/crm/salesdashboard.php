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

ini_set('max_execution_time', 0);
require_once ROOT.INC_ROOT.'IntegrationOB_class.php';
if($core->usergroup['crm_canGenerateSalesReports'] == 0) {
    error($lang->sectionnopermission);
}
if(!$core->input['action']) {
    eval("\$livechart = \"".$template->get('crm_salesdashboard_livechart')."\";");
    eval("\$drilldown = \"".$template->get('crm_salesdashboard_drilldown')."\";");
    eval("\$combinedsalesbudget = \"".$template->get('crm_salesdashboard_salesvsbudget')."\";");
    eval("\$generatepage = \"".$template->get('crm_salesdashboard')."\";");
    output_page($generatepage);
}
else {
    require_once ROOT.INC_ROOT.'integration_config.php';
    $integration = new IntegrationOB($intgconfig['openbravo']['database'], $intgconfig['openbravo']['entmodel']['client']);
    $intgdb = $integration->get_dbconn();

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

        $affiliates = Affiliates::get_affiliates(array('affid' => $affiliates_where), array('operators' => array('affid' => 'CUSTOMSQL')));
        if(is_array($affiliates)) {
            foreach($affiliates as $affiliate) {
                if(!empty($affiliate->integrationOBOrgId)) {
                    $orgs[] = $affiliate->integrationOBOrgId;
                }
            }
        }
        $query = $intgdb->query("SELECT totallines,dateinvoiced,c_invoice_id,c_currency_id FROM c_invoice WHERE issotrx='Y'AND docstatus='CO' AND c_invoice.ad_org_id IN ('".implode("','", $orgs)."') AND issotrx='Y' AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime((date('Y', TIME_NOW) - 2).'-01-01'))."' AND '".date('Y-m-d 00:00:00', strtotime((date('Y', TIME_NOW)).'-12-31'))."')");
        if($intgdb->num_rows($query) > 0) {
            while($line = $intgdb->fetch_assoc($query)) {
                $invoice['dateinvoiceduts'] = strtotime($line[dateinvoiced]);
                $invoice['dateparts'] = getdate($invoice[dateinvoiceduts]);
                $quarter = ceil(date('n', $invoice[dateinvoiceduts]) / 3);
                $qmonths = get_quartermonths($quarter);
                $obcurrency_obj = new IntegrationOBCurrency($line['c_currency_id']);
                $currency_obj = new Currencies($obcurrency_obj->cursymbol);
                $line['usdfxrate'] = $currency_obj->get_fxrate_bytype('real', $currency_obj->alphaCode, array('from' => strtotime(date('Y-m-d', $invoice['dateinvoiceduts']).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoice['dateinvoiceduts']).' 24:00'), 'year' => date('Y', $invoice['dateinvoiceduts']), 'month' => date('m', $invoice['dateinvoiceduts'])), array('precision' => 4), 'USD');

                if(in_array($invoice[dateparts]['mon'], $qmonths)) {
                    $data['sales'][$invoice[dateparts]['year']][$quarter]['total'] +=$line[totallines] / $line['usdfxrate'];
                    $data['sales'][$invoice[dateparts]['year']][$quarter][$invoice[dateparts]['mon']] +=$line[totallines] / $line['usdfxrate'];
                }
                $data['sales'][$invoice[dateparts]['year']]['total'] += $line[totallines] / $line['usdfxrate'];
            }
        }
        foreach($data['years'] as $year) {
            $data['salesperyear'][] = $data['sales'][intval($year)]['total'];
            for($q = 1; $q < 5; $q++) {
                $data[$year][] = $data['sales'][intval($year)][$q]['total'];
                $qmonths = get_quartermonths($q);
                foreach($qmonths as $month) {
                    if(!empty($data['sales'][$invoice[dateparts]['year']][$q][$month])) {
                        $data[$year.'_'.$q][] = $data['sales'][$invoice[dateparts]['year']][$q][$month]; //rand(100, 10000);
                    }
                }
            }
        }
        header("Content-Type: application/json");
        output(json_encode($data));
    }

    if($core->input['action'] == 'do_perform_livesales') {
        $chartproperties['title'] = $lang->livetotalsalesperorganisation." (".date('Y', TIME_NOW).")";
        $chartproperties['xaxislabel'] = $lang->affiliates;
        $chartproperties['yaxislabel'] = $lang->salestotalamount;

        $lines = new IntegrationOBInvoiceLine(null, $intgdb);
        ///////////////////////////// Replace by get_totallines() function
        $query = $intgdb->query("SELECT sum(totallines)as totallines,ad_org_id,c_currency_id,dateinvoiced from c_invoice WHERE issotrx='Y' AND docstatus='CO' AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime((date('Y', TIME_NOW)).'-01-01'))."' AND '".date('Y-m-d 00:00:00', strtotime((date('Y', TIME_NOW)).'-12-31'))."') GROUP BY ad_org_id");
        if($intgdb->num_rows($query) > 0) {
            while($invoiceline = $intgdb->fetch_assoc($query)) {
                $invoiceline['dateinvoiceduts'] = strtotime($invoiceline[dateinvoiced]);
                $obcurrency_obj = new IntegrationOBCurrency($invoiceline['c_currency_id']);
                $currency_obj = new Currencies($obcurrency_obj->cursymbol);
                $invoiceline['usdfxrate'] = $currency_obj->get_fxrate_bytype('real', $currency_obj->alphaCode, array('from' => strtotime(date('Y-m-d', $invoiceline['dateinvoiceduts']).' 01:00'), 'to' => strtotime(date('Y-m-d', $invoiceline['dateinvoiceduts']).' 24:00'), 'year' => date('Y', $invoiceline['dateinvoiceduts']), 'month' => date('m', $invoiceline['dateinvoiceduts'])), array('precision' => 4), 'USD');
                $sales['sales'][$invoiceline[ad_org_id]] = $invoiceline[totallines] / $invoiceline['usdfxrate'];
            }
        }
        //////////////////////////////


        $affiliates_where = '(affid IN ('.implode(',', $core->user['affiliates']).')';
        if(is_array($core->user['auditedaffids'])) {
            if(is_array($core->user['auditedaffids'])) {
                $affiliates_where .= ' OR (affid IN ('.implode(',', $core->user['auditedaffids']).')))';
            }
        }
        $affiliates = Affiliates::get_affiliates(array('affid' => $affiliates_where.' AND integrationOBOrgId Is not NULL'), array('operators' => array('affid' => 'CUSTOMSQL')));

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

    if($core->input ['action'] == 'do_perform_combinedbudgetsales') {
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
        $affiliates = Affiliates::get_affiliates(array('affid' => $affiliates_where.' AND integrationOBOrgId Is not NULL'), array('operators' => array('affid' => 'CUSTOMSQL')));
        foreach($affiliates as $affiliate) {
            $chartproperties['sales'][] = 0;
            $chartproperties['budget'][$affiliate->affid] = 0;
            $chartaffs['affiliates'][$affiliate->affid] = $affiliate->name;
            if(!empty($sales['sales'][$affiliate->integrationOBOrgId])) {
                $chartproperties['sales'][$affiliate->affid] = ($sales[$affiliate->integrationOBOrgId]); // rand(100, 1000); //
            }
        }
        $query = $db->query("SELECT affid,SUM(amount)as amount FROM budgeting_budgets_lines JOIN budgeting_budgets ON (budgeting_budgets_lines.bid= budgeting_budgets.bid) WHERE year=".date('Y ', TIME_NOW)." AND affid IN (".implode(', ', array_keys($affiliates)).") GROUP by affid");
        if($db->num_rows($query) > 0) {
            while($budgetline = $db->fetch_assoc($query)) {
                $chartproperties['budget'][$affiliate->affid] = rand(100, 1000); //$budgetline['amount'];
            }
        }
        header("Content-Type: application/json");
        output(json_encode($chartproperties));
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
