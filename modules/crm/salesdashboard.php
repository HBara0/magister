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

    eval("\$generatepage = \"".$template->get('crm_dashboard')."\";");
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

        $affiliates = Affiliates::get_affiliates(array('affid' => $core->user['affiliates']), array('returnarray' => true));
        if(is_array($affiliates)) {
            foreach($affiliates as $affiliate) {
                if(!empty($affiliate->integrationOBOrgId)) {
                    $orgs[] = $affiliate->integrationOBOrgId;
                }
            }
        }
        $query = $intgdb->query("SELECT totallines,dateinvoiced,c_invoice_id FROM c_invoice WHERE issotrx='Y'AND docstatus='CO' AND c_invoice.ad_org_id IN ('".implode("','", $orgs)."') AND issotrx='Y' AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime((date('Y', TIME_NOW) - 2).'-01-01'))."' AND '".date('Y-m-d 00:00:00', strtotime((date('Y', TIME_NOW)).'-12-31'))."')");
        if($intgdb->num_rows($query) > 0) {
            while($line = $intgdb->fetch_assoc($query)) {
                $invoice[dateinvoiceduts] = strtotime($line[dateinvoiced]);
                $invoice[dateparts] = getdate($invoice[dateinvoiceduts]);
                $quarter = ceil(date('n', $invoice[dateinvoiceduts]) / 3);
                $qmonths = get_quarter($quarter);
                if(in_array($invoice[dateparts]['mon'], $qmonths)) {
                    $data['sales'][$invoice[dateparts]['year']][$quarter]['total'] +=$line[totallines];
                    $data['sales'][$invoice[dateparts]['year']][$quarter][$invoice[dateparts]['mon']] += $line[totallines];
                }
                $data['sales'][$invoice[dateparts]['year']]['total'] += $line[totallines];
            }
        }
        foreach($data['years'] as $year) {
            $data['salesperyear'][] = $data['sales'][intval($year)]['total']; //rand(100, 1000); //
            for($q = 1; $q < 5; $q++) {
                $data[$year][] = $data['sales'][intval($year)][$q]['total']; //rand(100, 10000); //
                $qmonths = get_quarter($q);
                foreach($qmonths as $month) {
                    if(!empty($data['sales'][$invoice[dateparts]['year']][$q][$month])) {
                        $data[$year.'_'.$q][] = $data['sales'][$invoice[dateparts]['year']][$q][$month]; //rand(100, 10000); //
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
        $query = $intgdb->query("SELECT sum(totallines)as totallines,ad_org_id from c_invoice WHERE issotrx='Y' AND docstatus='CO' AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime((date('Y', TIME_NOW)).'-01-01'))."' AND '".date('Y-m-d 00:00:00', strtotime((date('Y', TIME_NOW)).'-12-31'))."') GROUP BY ad_org_id");
        if($intgdb->num_rows($query) > 0) {
            while($invoiceline = $intgdb->fetch_assoc($query)) {
                $sales['sales'][$invoiceline[ad_org_id]] = $invoiceline[totallines];
            }
        }
        $affiliates_where = '(affid IN ('.implode(',', $core->user['affiliates']).')';
        if(is_array($core->user['auditedaffids'])) {
            $affiliates_where .= ' OR (affid IN ('.implode(',', $core->user['auditedaffids']).'))';
        }
        //   $affiliates = Affiliates::get_affiliates(array('affid' => $affiliates_where), array('returnarray' => true, 'simple' => false, 'operators' => array('affid' => 'CUSTOMSQL')));
        $affiliates = Affiliates::get_affiliates(array('affid' => $core->user['affiliates']), array('returnarray' => true));
        if(is_array($affiliates)) {
            foreach($affiliates as $affiliate) {
                if(empty($affiliate->integrationOBOrgId)) {
                    continue;
                }
                $chartproperties['affiliates'][] = $affiliate->name;
                if(!empty($sales['sales'][$affiliate->integrationOBOrgId])) {
                    $chartproperties['sales'][] = ($sales['sales'][$affiliate->integrationOBOrgId]); //rand(1000, 100000); //
                }
                else {
                    $chartproperties['sales'][] = 0;
                }
            }
            header("Content-Type: application/json");
            output(json_encode($chartproperties));
        }
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
