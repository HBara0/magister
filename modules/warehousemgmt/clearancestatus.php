<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: clearancestatus.php
 * Created:        @hussein.barakat    Jul 23, 2015 | 1:28:19 PM
 * Last Update:    @hussein.barakat    Jul 23, 2015 | 1:28:19 PM
 */


if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['warehousemgmt_canGenerateReports'] == 0) {
//    error($lang->sectionnopermission);
}

ini_set('max_execution_time', 0);

require_once ROOT.INC_ROOT.'IntegrationOB_class.php';

if(!$core->input['action']) {
    $affiliates = Affiliates::get_affiliates(array('affid' => $core->user['affiliates']), array('returnarray' => true));
    $affiliates_list = parse_selectlist('affid', '', $affiliates, '');
    eval("\$generatereport = \"".$template->get('warehousemgmt_generate_clearencestatus')."\";");
    output_page($generatereport);
}
if($core->input['action'] == 'do_perform_clearancestatus') {
    require_once ROOT.INC_ROOT.'integration_config.php';
    $integration = new IntegrationOB($intgconfig['openbravo']['database'], $intgconfig['openbravo']['entmodel']['client']);
    $intgdb = $integration->get_dbconn();
    $affid = intval($core->input['affid']);
    $affiliate = Affiliates::get_affiliates(array('affid' => $affid), array('simple' => false));
    $orgid = $affiliate->integrationOBOrgId;
    if(empty($orgid)) {
        output_xml('<status>true</status><message>Affiliate with no integration Id</message>');
        exit;
    }
    $nomatchfound = true;
    // $orgid = "DA0CE0FED12C4424AA9B51D492AE96D2";
    $where = "ad_org_id='".$orgid."' AND issotrx='N' AND "
            ."EXISTS (SELECT c_orderline_id FROM c_orderline WHERE (SELECT SUM(m_inoutline.MovementQty) FROM m_inoutline WHERE m_inoutline.c_orderline_id=c_orderline.c_orderline_id)<c_orderline.QtyOrdered AND c_order.c_order_id=c_orderline.c_order_id)"
            ."AND docstatus = 'CO' ORDER BY dateordered ASC";

    $orders = IntegrationOBOrder::get_data($where);

    if(is_array($orders)) {
        foreach($orders as $order) {
            $filter_where = " c_order_id='".$order->c_order_id."' AND (qtyordered-qtydelivered)!=0";
            $orderlines = IntegrationOBOrderLine::get_data($filter_where, array('returnarray' => true));
            $ca = $order->get_salesrep()->get_displayname();
            $order = $order->get();
            if(is_array($orderlines)) {
                $nomatchfound = false;
                /* sales Order data -START */
//                $order['DateOrdered_output'] = date('Y-m-d', strtotime($order['dateordered']));
//                $warehouse = IntegrationOBWarehouse::get_data("m_warehouse_id='".$order['m_warehouse_id']."'");
//                if(is_object($warehouse)) {
//                    $order['warehouse'] = $warehouse->name;
//                    $order['deliveryfrom_output'] = $warehouse->get_location()->address1;
//                }
                $order['bpaddress'] = '-';
                if(!empty($order['c_bpartner_location_id'])) {
                    $order['bpaddress'] = $order['c_bpartner_location_id'];
                }
                $order['eta'] = '-';
                if(!empty($order['em_ork_eta'])) {
                    $order['eta'] = date('Y-m-d', strtotime($order['em_ork_eta']));
                }
                $order['etd'] = '-';
                if(!empty($order['em_ork_etd'])) {
                    $order['etd'] = date('Y-m-d', strtotime($order['em_ork_etd']));
                }
                $order['atd'] = '-';
                if(!empty($order['em_ork_atd'])) {
                    $order['atd'] = date('Y-m-d', strtotime($order['em_ork_atd']));
                }
                $order['ata'] = '-';
                if(!empty($order['em_ork_ata'])) {
                    $order['ata'] = date('Y-m-d', strtotime($order['em_ork_ata']));
                }
                $order['etc'] = '-';
                if(empty($order['em_ork_etc'])) {
                    $order['etc'] = date('Y-m-d', strtotime($order['em_ork_etc']));
                }
                $order['supdocrecptdate'] = '-';
                if(!empty($order['em_ork_supdocrecptdate'])) {
                    $order['supdocrecptdate'] = date('Y-m-d', strtotime($order['em_ork_supdocrecptdate']));
                }
                $order['cadocdelvdate'] = '-';
                if(!empty($order['em_ork_cadocdelvdate'])) {
                    $order['cadocdelvdate'] = date('Y-m-d', strtotime($order['em_ork_cadocdelvdate']));
                }
                $order['delvorderreldate'] = '-';
                if(!empty($order['em_ork_delvorderreldate'])) {
                    $order['delvorderreldate'] = date('Y-m-d', strtotime($order['em_ork_delvorderreldate']));
                }
                if($order['bpaddress'] != '-') {
                    $bplocation = new IntegrationOBBusinessPartnerLocation($order['bpaddress']);
                }
                if(is_object($bplocation)) {
                    $order['bpaddress'] = $bplocation->get_location()->address1;
                }
                $sup = IntegrationOBBPartner::get_data("c_bpartner_id='".$order['c_bpartner_id']."'");
                if(is_object($sup)) {
                    $order['supplier'] = $sup->get_displayname();
                }
                /* sales Order data - END */

                /* sales Order Lines data -START */
                $orderlines_output .='<tr class="subtitle"><td style="width:15%;">'.$lang->product.'</td><td style="width:10%;">'.$lang->orderedqty.'</td><td style="width:10%;"> '.$lang->pendingqty.'</td><td tyle="width:10%;">'.$lang->uom.'</td></tr>';
                foreach($orderlines as $orderline) {
                    $orderline = $orderline->get();
                    $product = new IntegrationOBProduct($orderline['m_product_id']);
                    if(is_object($product)) {
                        $orderline['product'] = $product->get_displayname();
                    }
                    $orderline['pendingQty'] = $orderline['qtyordered'] - $orderline['qtydelivered'];
                    $uom = new IntegrationOBUom($orderline['c_uom_id']);
                    $orderline['uom'] = $uom->name;
                    eval("\$orderlines_output .= \"".$template->get('warehousemgmt_pendingdeliveries_orderline')."\";");
                    unset($product, $uom);
                }
                /* sales Order Lines data -END */
                unset($warehouse, $bplocation, $sup);
                eval("\$saleordes_output .= \"".$template->get('warehousemgmt_clearencestatus_orders')."\";");
            }
            unset($orderlines_output);
        }
        eval("\$pendingdeliveries_output = \"".$template->get('warehousemgmt_pendingdeliveries')."\";");
    }
    if(!$nomatchfound) {
        output_xml('<status></status><message><![CDATA['.$pendingdeliveries_output.']]></message>');
    }
    else {
        $output = 'No match Found';
        output_xml('<message><![CDATA['.$output.']]></message>');
    }
}