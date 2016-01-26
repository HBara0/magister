<?php
exit;
require '../inc/init.php';

$period['from'] = '30 minutes ago';
$period['to'] = 'tomorrow';
$current_date = getdate(TIME_NOW);
$connection = pg_connect("host=localhost port=5432 dbname=openbrav_main user=openbrav_appuser password=8w8;MFRy4g^3");
$affiliates_index = array(
        'C08F137534222BD001345BAA60661B97' => 19
);
$exclude['products'] = array('0A36650996654AD2BA6B26CBC8BA7347');
$newdata = array();

$query = pg_query("SELECT o.ad_org_id, o.c_order_id, o.dateordered, o.documentNo, bp.name AS bpname, bp.c_bpartner_id AS bpid, bp.value AS bpname_abv, c.iso_code AS currency, o.salesrep_id, u.username, u.name AS salesrep, pt.netdays AS paymenttermsdays
					FROM c_order o JOIN c_bpartner bp ON (bp.c_bpartner_id=o.c_bpartner_id)
					JOIN c_currency c ON (c.c_currency_id=o.c_currency_id)
					JOIN ad_user u ON (u.ad_user_id=o.salesrep_id)
					JOIN c_paymentterm pt ON (o.c_paymentterm_id=pt.c_paymentterm_id)
					WHERE o.ad_org_id='C08F137534222BD001345BAA60661B97' AND docstatus NOT IN ('VO', 'CL') AND issotrx='Y' AND (dateordered BETWEEN '".date('Y-m-d 00:00:00', strtotime($current_date['year'].'-1-1'))."' AND '".date('Y-m-d 00:00:00', strtotime('last day of this month'))."')
					ORDER by dateordered ASC");

while($order = pg_fetch_assoc($query)) {
    $order_newdata = array(
            'foreignSystem' => 3,
            'foreignId' => $order['c_order_id'],
            'docNum' => $order['documentno'],
            'date' => strtotime($order['dateordered']),
            'cid' => $order['bpid'],
            'affid' => $affiliates_index[$order['ad_org_id']],
            'currency' => $order['currency'],
            'paymentTerms' => $order['paymenttermsdays'],
            'salesRep' => $order['salesrep']
    );

    $order_newdata['salesRepLocalId'] = $db->fetch_field($db->query("SELECT uid FROM ".Tprefix."users WHERE username='".$db->escape_string($order['username'])."'"), 'uid');

    if(value_exists('integration_mediation_salesorders', 'foreignId', $order['c_order_id'])) {
        //echo 'Update: '.$order['documentno'].'<br />';
        $query2 = $db->update_query('integration_mediation_salesorders', $order_newdata, 'foreignId="'.$order['c_order_id'].'"');
    }
    else {
        //echo 'Created: '.$order['documentno'].'<br />';
        $query2 = $db->insert_query('integration_mediation_salesorders', $order_newdata);
    }

    if($query2) {
        if(value_exists('integration_mediation_salesorderlines', 'foreignOrderId', $order['c_order_id'])) {
            //echo '--- Deleted Lines Of: '.$order['documentno'].'<br />';
            $db->delete_query('integration_mediation_salesorderlines', 'foreignOrderId="'.$order['c_order_id'].'"');
        }

        $orderline_query = pg_query("SELECT ol.*, ct.cost, ppo.c_bpartner_id, u.x12de355 AS uom, c.iso_code AS costcurrency
								FROM c_orderline ol
								JOIN m_product p ON (p.m_product_id=ol.m_product_id)
								JOIN c_uom u ON (u.c_uom_id=ol.c_uom_id)
								LEFT JOIN m_costing ct ON (ct.m_product_id=p.m_product_id)
								LEFT JOIN c_currency c ON (c.c_currency_id=ct.c_currency_id)
								LEFT JOIN m_product_po ppo ON (p.m_product_id=ppo.m_product_id)
								LEFT JOIN c_bpartner bp ON (bp.c_bpartner_id=ppo.c_bpartner_id)
								WHERE c_order_id='{$order[c_order_id]}' AND ('".$order['dateordered']."' BETWEEN ct.datefrom AND ct.dateto) AND ol.m_product_id NOT IN ('".implode('\',\'', $exclude['products'])."')");

        //echo pg_num_rows($orderline_query).' entries<br />';
        while($orderline = pg_fetch_assoc($orderline_query)) {
            $orderline_newdata = array(
                    'foreignId' => $orderline['c_orderline_id'],
                    'foreignOrderId' => $order['c_order_id'],
                    'pid' => $orderline['m_product_id'],
                    'spid' => $orderline['c_bpartner_id'],
                    'affid' => $affiliates_index[$order['ad_org_id']],
                    'price' => $orderline['priceactual'],
                    'quantity' => $orderline['qtyordered'],
                    'quantityUnit' => $orderline['uom'],
                    'cost' => $orderline['cost'],
                    'costCurrency' => $orderline['costcurrency']
            );
            //echo ' --- Created: '.$orderline['c_orderline_id'].'<br />';
            //print_r($orderline_newdata);
            //echo ' <br />';

            $db->insert_query('integration_mediation_salesorderlines', $orderline_newdata);
        }
    }
}
?>