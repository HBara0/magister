<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: stockreportlive.php
 * Created:        @zaher.reda    Mar 16, 2015 | 8:57:18 PM
 * Last Update:    @zaher.reda    Mar 16, 2015 | 8:57:18 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

//if($core->usergroup['warehousemgmt_canGenerateReports'] == 0) {
//    error($lang->sectionnopermission);
//}
ini_set('max_execution_time', 0);

if(!$core->input['action']) {
    $affiliates = Affiliates::get_affiliates(array('affid' => $core->user['affiliates']), array('returnarray' => true));
    $affiliates_list = parse_selectlist('affid', 2, $affiliates, '');

    eval("\$generatepage = \"".$template->get('warehousemgmt_valuedstkreport')."\";");
    output_page($generatepage);
}
else {
    if($core->input['action'] == 'do_generatereport') {
        require_once ROOT.INC_ROOT.'integration_config.php';
        if(empty($core->input['affid'])) {
            redirect('index.php?module=warehousemgmt/valuedstockreport');
        }

        $report_period = array('from' => '2005-01-01');
        $report_period['to'] = 'tomorrow -1 second';
        if(!empty($core->input['asOf'])) {
            $report_period['to'] = $core->input['asOf'];
        }
        $date_info = getdate_custom(strtotime($report_period['to']));
        /* In-line CSS styles in form of array in order to be compatible with email message */
        $css_styles['table-datacell'] = 'text-align: right;';
        $css_styles['altrow'] = 'background-color: #f7fafd;';
        $css_styles['altrow2'] = 'background-color: #F2FAED;';
        $css_styles['greenrow'] = 'background-color: #F2FAED;';

        $currency_obj = new Currencies('USD');

        $integration = new IntegrationOB($intgconfig['openbravo']['database'], $intgconfig['openbravo']['entmodel']['client']);
        //$integration = new IntegrationOB($db_info, 'C08F137534222BD001345B7B2E8F182D', $affiliates_index, 3, array('from' => '2010-01-01'));
        /* Configurations Section - START */
        $report_options = array('roundto' => 0);

        $affiliateobj = new Affiliates($core->input['affid'], false);
        if(!in_array($affiliateobj->affid, $core->user['affiliates']) && !in_array($affiliateobj->affid, $core->user['auditedaffids'])) {
            error($lang->sectionnopermission);
        }
        $orgid = $affiliateobj->integrationOBOrgId;
        $affiliate = $affiliateobj->get();
        $affiliate['currency'] = $affiliateobj->get_country()->get_maincurrency()->get()['alphaCode'];

        $integration->set_organisations(array($orgid));
        $integration->set_sync_interval($report_period);

        $transactions = IntegrationOBTransaction::get_data('movementdate <= \''.date('Y-m-d 23:59:59', strtotime($report_period['to'])).'\' AND ad_org_id=\''.$affiliateobj->integrationOBOrgId.'\' ORDER BY movementdate ASC');

        foreach($transactions as $transaction) {
            $locator = new IntegrationOBLocator($transaction->m_locator_id, $integration->get_dbconn());
            $warehouse = $locator->get_warehouse();
            if($transaction->costing_status != 'CC') {
                continue;
            }
            $items[$warehouse->m_warehouse_id][$transaction->m_product_id]['qty'] += $transaction->movementqty;
            if($transaction->movementqty < 0) {
                $items[$warehouse->m_warehouse_id][$transaction->m_product_id]['value'] -= $transaction->transactioncost;
            }
            else {
                $items[$warehouse->m_warehouse_id][$transaction->m_product_id]['value'] += $transaction->transactioncost;
            }
            if($items[$warehouse->m_warehouse_id][$transaction->m_product_id]['qty'] == 0) {
                $items[$warehouse->m_warehouse_id][$transaction->m_product_id]['value'] = 0;
            }
        }

        foreach($items as $warehouse_id => $whitems) {
            $warehouse = new IntegrationOBWarehouse($warehouse_id, $integration->get_dbconn());
            array_multisort_bycolumn($whitems, 'qty');
            foreach($whitems as $product_id => $item) {
                if($item['qty'] == 0) {
                    continue;
                }
                $product = new IntegrationOBProduct($product_id, $integration->get_dbconn());
                $total['qty'] += $item['qty'];
                $total['value'] += $item['value'];
                eval("\$itemlines .= \"".$template->get('warehousemgmt_valuedstkreport_reportlines_itemlines')."\";");
            }
            eval("\$reportlines .= \"".$template->get('warehousemgmt_valuedstkreport_reportlines')."\";");
            $grandtotal['qty'] += $total['qty'];
            $grandtotal['value'] += $total['value'];
            unset($total, $itemlines);
        }

        eval("\$report = \"".$template->get('warehousemgmt_valuedstkreport_report')."\";");
        echo($report);
    }
}