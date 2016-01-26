<?php
exit;
require '../inc/init.php';

$period['from'] = '30 minutes ago';
$period['to'] = 'tomorrow';
$currency_obj = new Currencies('USD');

$affiliates_index = array(
        'C08F137534222BD001345BAA60661B97' => 19
);
$connection = pg_connect("host=localhost port=5432 dbname=openbrav_main user=openbrav_appuser password=8w8;MFRy4g^3");

/* Sync Products - START */
//echo 'Product<br />';
$query = pg_query("SELECT *
					FROM m_product
					WHERE ad_client_id='C08F137534222BD001345B7B2E8F182D'
					AND (updated BETWEEN '".date('Y-m-d 00:00:00', strtotime($period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($period['to']))."')");
$newdata = array();
while($product = pg_fetch_assoc($query)) {
    $newdata = array('foreignSystem' => 3, 'foreignId' => $product['m_product_id'], 'foreignName' => $product['name'], 'foreignNameAbbr' => $product['value'], 'affid' => $affiliates_index[$product['ad_org_id']]);

    $newdata['localId'] = $db->fetch_field($db->query("SELECT pid FROM ".Tprefix."products WHERE name='".$db->escape_string($product['name'])."'"), 'pid');

    if(value_exists('integration_mediation_products', 'foreignId', $product['m_product_id'])) {
        $db->update_query('integration_mediation_products', $newdata, 'foreignId="'.$product['m_product_id'].'"');
    }
    else {
        $db->insert_query('integration_mediation_products', $newdata);
    }
    //echo $product['m_product_id'].'<br />';
}

/* Sync Products - END */

/* Sync Suppliers/Customers - START */
//echo 'Vendors<br />';
$query = pg_query("SELECT *
					FROM c_bpartner
					WHERE ad_client_id='C08F137534222BD001345B7B2E8F182D' AND (iscustomer='Y' OR isvendor='Y')
					AND (updated BETWEEN '".date('Y-m-d 00:00:00', strtotime($period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($period['to']))."')");
$newdata = array();
while($bpartner = pg_fetch_assoc($query)) {
    $newdata = array('foreignSystem' => 3, 'foreignId' => $bpartner['c_bpartner_id'], 'foreignName' => $bpartner['name'], 'foreignNameAbbr' => $bpartner['value'], 'affid' => $affiliates_index[$bpartner['ad_org_id']]);
    $newdata['localId'] = $db->fetch_field($db->query("SELECT eid FROM ".Tprefix."entities WHERE companyName='".$db->escape_string($bpartner['name'])."'"), 'eid');
    if($bpartner['isvendor'] == 'Y') {
        $newdata['entityType'] = 's';
    }
    elseif($bpartner['iscustomer'] == 'Y') {
        $newdata['entityType'] = 'c';
    }

    if(value_exists('integration_mediation_entities', 'foreignId', $bpartner['c_bpartner_id'])) {
        $db->update_query('integration_mediation_entities', $newdata, 'foreignId="'.$bpartner['c_bpartner_id'].'"');
    }
    else {
        $db->insert_query('integration_mediation_entities', $newdata);
    }
    //echo $bpartner['c_bpartner_id'].'<br />';
}
/* Sync Suppliers/Customers - END */

/* Sync Purchases - START */
$purchase_type = 'order';

if($purchase_type == 'order') {
    $query = pg_query("SELECT o.c_order_id AS documentid, o.ad_org_id, o.dateordered AS documentdate, bp.name AS bpname, bp.c_bpartner_id, c.iso_code AS currency
					FROM c_order o JOIN c_bpartner bp ON (bp.c_bpartner_id=o.c_bpartner_id)
					JOIN c_currency c ON (c.c_currency_id=o.c_currency_id)
					WHERE o.ad_org_id='C08F137534222BD001345BAA60661B97' AND issotrx='N' AND docstatus = 'CO' AND ((dateordered BETWEEN '".date('Y-m-d 00:00:00', strtotime($period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($period['to']))."') OR (o.updated BETWEEN '".date('Y-m-d 00:00:00', strtotime($period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($period['to']))."'))");
}
else {
    $query = pg_query("SELECT i.c_invoice_id AS documentid, i.ad_org_id, bp.name AS bpname, bp.c_bpartner_id, c.iso_code AS currency, dateinvoiced AS documentdate
					FROM c_invoice i JOIN c_bpartner bp ON (bp.c_bpartner_id=i.c_bpartner_id)
					JOIN c_currency c ON (c.c_currency_id=i.c_currency_id)
					WHERE  i.ad_org_id='C08F137534222BD001345BAA60661B97' AND issotrx='N' AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', strtotime($period['from']))."' AND '".date('Y-m-d 00:00:00', strtotime($period['to']))."')");
}

while($document = pg_fetch_assoc($query)) {
    if($purchase_type == 'order') {
        $documentline_query = pg_query("SELECT ol.*, c_orderline_id AS documentlineid, ol.qtyordered AS quantity, p.name AS productname, u.x12de355 AS uom
							FROM c_orderline ol JOIN m_product p ON (p.m_product_id=ol.m_product_id)
							JOIN c_uom u ON (u.c_uom_id=p.c_uom_id)
							WHERE c_order_id='{$document[documentid]}'");
    }
    else {
        $documentline_query = pg_query("SELECT il.*, c_invoiceline_id AS documentlineid, il.qtyinvoiced AS quantity, p.name AS productnamem, u.x12de355 AS uom
									FROM c_invoiceline il JOIN m_product p ON (p.m_product_id=il.m_product_id)
									JOIN c_uom u ON (u.c_uom_id=p.c_uom_id)
									WHERE c_invoice_id='{$document[documentid]}'");
    }
    $newdata = array();
    while($documentline = pg_fetch_assoc($documentline_query)) {
        if(strtolower($documentline['uom']) == 'kg') {
            $documentline['uom'] = 'MT';
            $documentline['quantity'] = $documentline['quantity'] / 1000;
        }
        $newdata = array('foreignSystem' => 3,
                'spid' => $document['c_bpartner_id'],
                'affid' => $affiliates_index[$document['ad_org_id']],
                'pid' => $documentline['m_product_id'],
                'date' => strtotime($document['documentdate']),
                'currency' => $document['currency'],
                'quantity' => $documentline['quantity'],
                'amount' => $documentline['linenetamt'],
                'quantityUnit' => $documentline['uom'],
                'saleType' => 'SKI',
                'orderId' => $document['documentid'],
                'orderLineId' => $documentline['documentlineid']
        );

        $newdata['usdFxrate'] = $currency_obj->get_average_fxrate($document['currency'], array('from' => strtotime(date('Y-m-d', $newdata['date']).' 01:00'), 'to' => strtotime(date('Y-m-d', $newdata['date']).' 24:00')));
        if(empty($newdata['usdFxrate'])) {
            $newdata['usdFxrate'] = $currency_obj->get_average_fxrate($document['currency'], array('from' => strtotime(date('Y-m-d', $newdata['date']).' 01:00') - (24 * 60 * 60 * 7), 'to' => strtotime(date('Y-m-d', $newdata['date']).' 24:00')));
        }
        if(value_exists('integration_mediation_stockpurchases', 'orderLineId', $documentline['documentlineid'])) {
            $db->update_query('integration_mediation_stockpurchases', $newdata, 'orderLineId="'.$documentline['documentlineid'].'"');
        }
        else {
            $db->insert_query('integration_mediation_stockpurchases', $newdata);
        }
    }
}
//echo 'Purchases';
/* Sync Purchases - END */
?>