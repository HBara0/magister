<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: intermedaffshipment.php
 * Created:        @rasha.aboushakra    Jul 14, 2015 | 3:30:40 PM
 * Last Update:    @rasha.aboushakra    Jul 14, 2015 | 3:30:40 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['warehousemgmt_canGenerateReports'] == 0) {
    error($lang->sectionnopermission);
}
require_once ROOT.INC_ROOT.'IntegrationOB_class.php';
ini_set('max_execution_time', 0);
if(!$core->input['action']) {
    $intermedaffiliates = Affiliates::get_affiliates('', array('returnarray' => true));
    $intermedaffiliates_list = parse_selectlist('intermedid', '', $intermedaffiliates, '', '', '', array('width' => '150px'));

    $buyingaffiliates = Affiliates::get_affiliates(array('affid' => $core->user['affiliates']), array('returnarray' => true));
    $buyingaffiliates_list = parse_selectlist('buyingid', '', $buyingaffiliates, '', '', '', array('width' => '150px'));

    eval("\$generatereport = \"".$template->get('warehousemgmt_generate_intermedaffshipmentreport')."\";");
    output_page($generatereport);
}
elseif($core->input['action'] == 'do_perform_intermedaffshipment') {
    require_once ROOT.INC_ROOT.'integration_config.php';
    $integration = new IntegrationOB($intgconfig['openbravo']['database'], $intgconfig['openbravo']['entmodel']['client']);
    $intgdb = $integration->get_dbconn();

    $intermedaffid = intval($core->input['intermedid']);
    $buyingaffid = intval($core->input['buyingid']);

    if(!in_array($buyingaffid, $core->user['affiliates'])) {
        output_xml('<status>true</status><message>No permission to view this report</message>');
        exit;
    }

    $intermedaffiliate = Affiliates::get_affiliates(array('affid' => $intermedaffid), array('simple' => false));
    $buyingaffiliate = Affiliates::get_affiliates(array('affid' => $buyingaffid), array('simple' => false));
    $intermedaff_orgid = $intermedaffiliate->integrationOBOrgId;
    $buyingaff_orgid = $buyingaffiliate->integrationOBOrgId;

    //$intermedaff_orgid = "DA0CE0FED12C4424AA9B51D492AE96D2";
    //$buyingaff_orgid = "B0C07E1A9946477AB47805413D4624F1";

    if(empty($intermedaff_orgid) || empty($buyingaff_orgid)) {
        output_xml('<status>true</status><message>Affiliate with no integration Id</message>');
        exit;
    }

    /* Get Buying Aff covered Countries- START */
    $buyingaff_countries = Countries::get_data(array('affid' => $buyingaffid), array('returnarray' => true));
    if(is_array($buyingaff_countries)) {
        foreach($buyingaff_countries as $coid => $buyingff_country) {
            $coveredcountries .= "'".$coid."',";
        }
    }
    $coveredcountries = substr($coveredcountries, 0, -1);
    /* Get Buying Aff covered Countries -END */

    $buyingaff = new IntegrationOBOrgInfo($buyingaff_orgid);
    $where = " ad_org_id='".$intermedaff_orgid."' AND c_bpartner_id='".$buyingaff->get_bp()->get_id()."'";
    $where .= //"OR EXISTS (SELECT * FROM c_bpartner_location "
            // ."WHERE EXISTS (SELECT * FROM c_location where c_country_id IN (".$coveredcountries.") AND c_location.c_location_id=c_bpartner_location.c_location_id)"
            //." AND c_bpartner_location.c_bpartner_location_id=c_order.c_bpartner_location_id)"
            // .") "
            " AND docstatus = 'CO' "
            ."AND NOT EXISTS (SELECT c_order_id FROM m_inout WHERE m_inout.c_order_id=c_order.c_order_id)"
            ."ORDER BY dateordered ASC";

    $orders = IntegrationOBOrder::get_data($where, array('returnarray' => true));
    if(is_array($orders)) {
        foreach($orders as $order_obj) {
            $order = $order_obj->get();
            $order['DateOrdered_output'] = date('Y-m-d', strtotime($order['dateordered']));
            $order['updated_output'] = date('Y-m-d', strtotime($order['updated']));
            $cust = new IntegrationOBBPartner($order['c_bpartner_id']);
            if(is_object($cust)) {
                $order['customer'] = $cust->get_displayname();
            }
            $custlocation = new IntegrationOBBusinessPartnerLocation($order['c_bpartner_location_id']);
            if(is_object($custlocation)) {
                $cust_countryid = $custlocation->get_location()->c_country_id;
                $cust_country = new IntegrationOBCountry($cust_countryid);
                if(is_object($cust_country)) {
                    $order['customercountry'] = $cust_country->name;
                }
            }
            $order['currency'] = $order_obj->get_currency()->iso_code;
            $order['salesrep_output'] = $order_obj->get_salesrep()->get_displayname();
            $order['paymentterm'] = $order_obj->get_paymentterm()->get_displayname();
            $order['incoterms'] = $order_obj->get_incoterms()->get_displayname();

            $status_fields = array('em_ork_incotermsdesc', 'currency', 'salesrep_output', 'paymentterm', 'incoterms', 'em_ork_incotermsdesc', 'em_ork_shiptstatus', 'em_ork_shipstatusdesc', 'em_ork_eta', 'em_ork_ets');
            foreach($status_fields as $status_field) {
                if(empty($order[$status_field])) {
                    $order[$status_field] = '-';
                }
            }
            eval("\$statusinfo_output = \"".$template->get('warehousemgmt_intermedaffshipment_status')."\";");

            $filter_where = " c_order_id='".$order_obj->c_order_id."'";
            $orderlines = IntegrationOBOrderLine::get_data($filter_where, array('returnarray' => true));
            if(is_array($orderlines)) {
                foreach($orderlines as $orderline_obj) {
                    $orderline = $orderline_obj->get();
                    $product = new IntegrationOBProduct($orderline['m_product_id']);
                    if(is_object($product)) {
                        $orderline['product'] = $product->get_displayname();
                    }
                    $orderline['packaging'] = $orderline_obj->get_packaging();
                    $uom = new IntegrationOBUom($orderline['c_uom_id']);
                    $orderline['uom'] = $uom->name;
                    eval("\$lines_output .= \"".$template->get('warehousemgmt_intermedaffshipment_line')."\";");
                    unset($product, $uom);
                }
            }
            else {
                $lines_output .='<tr><td colspan=7>'.$lang->na.'</td></tr>';
            }

            $display = 'none';
            $attachments = IntegrationOBIAttachments::get_data("ad_record_id='".$order['c_order_id']."'"); //("ad_record_id='123424070965407F8B69A0D787FD9D2D'"); //
            if(is_array($attachments)) {
                $display = 'block';
                foreach($attachments as $attachment) {
                    eval("\$attachments_ouput .= \"".$template->get('warehousemgmt_intermedaffshipment_attachments')."\";");
                }
            }
            else if(is_object($attachments)) {
                $display = 'block';
                $attachment = $attachments;
                eval("\$attachments_ouput .= \"".$template->get('warehousemgmt_intermedaffshipment_attachments')."\";");
            }
            eval("\$intermedaffshipment_orders .= \"".$template->get('warehousemgmt_intermedaffshipment_orders')."\";");
            unset($lines_output, $attachments_ouput);
        }
    }
    if(empty($intermedaffshipment_orders)) {
        $intermedaffshipment_orders = 'No Match Found';
    }
    output_xml('<status></status><message><![CDATA['.$intermedaffshipment_orders.']]></message>');
}