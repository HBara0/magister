<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * $module: group purchase
 * $id: affiliateorder.php
 * Created:	   @najwa.kassem	Feb 10, 2010 | 10:00 AM
 * Last Update: @najwa.kassem 	Feb 18, 2010 | 10:08 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if(!$core->input['action']) {
    if(isset($core->input['affid'])) {
        $filter_where = 'OR d.affid ='.$db->escape_string($core->input['affid']).'';
    }
    $affiliate_query = $db->query("SELECT a.affid,aem.eid,a.name as affname FROM ".Tprefix."affiliates a
						JOIN ".Tprefix."affiliatedentities ae ON (a.affid=ae.affid)
						JOIN ".Tprefix."assignedemployees aem ON (ae.eid=aem.eid)
						WHERE aem.uid={$core->user[uid]}
						");

    while($affiliate = $db->fetch_array($affiliate_query)) {
        $affiliates[$affiliate['affid']] = $affiliate['affname'];
    }

    $affiliates_list = parse_selectlist('affid', 1, $affiliates, 'affid').'';

    $query2 = $db->query("SELECT *,p.name as pname FROM ".Tprefix."grouppurchase_pricing pri
						JOIN ".Tprefix."grouppurchase_pricingdetails d ON (pri.gppid=d.gppid)
						JOIN ".Tprefix."products p ON (p.pid=pri.pid)
					WHERE affid = 0
					GROUP BY pri.pid
					ORDER BY pri.setTime  ");
    while($prices = $db->fetch_array($query2)) {
        $products[$prices['pid']] = $prices['pname'];
    }
    $products_list = parse_selectlist('product', 1, $products, '');
    eval("\$order = \"".$template->get("grouppurchase_order")."\";");
    output_page($order);
}
else {

    if($core->input['action'] == 'get_productslist') {
        if(is_empty($core->input['affid'])) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
        }
        $affid = $db->escape_string($core->input['affid']);
        $query2 = $db->query("SELECT *,p.name as pname FROM ".Tprefix."grouppurchase_pricing pri
							JOIN ".Tprefix."grouppurchase_pricingdetails d ON (pri.gppid=d.gppid)
							JOIN ".Tprefix."products p ON (p.pid=pri.pid)
						WHERE affid = 0
							ORDER BY pri.setTime  ");

        while($prices = $db->fetch_array($query2)) {
            //$products_list .= "<option value='{$prices[pid]}'>{$prices[name]}</option>";
            $products[$prices['pid']] = $prices['pname'];
        }
        $products_list = parse_selectlist('pid', 1, $products, '');
        echo $products_list;
    }
    /* if($core->input['action'] == 'get_total') {
      $main_price = $db->escape_string($core->input['main_price']);
      $price = $db->escape_string($core->input['price']);
      $qty = $db->escape_string($core->input['quantity']);
      $total = $price * $qty.'/'.$main_price;
      //$qty
      //$total = $price;
      //echo var_dump((integer)($price));
      echo $total;
      } */
    if($core->input['action'] == 'get_price') {
        if(is_empty($core->input['affid'], $core->input['pid'])) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
        }
        $affid = $db->escape_string($core->input['affid']);

        $pid = $db->escape_string($core->input['pid']);

        $query2 = $db->query("SELECT * FROM ".Tprefix."grouppurchase_pricing pri
							JOIN ".Tprefix."grouppurchase_pricingdetails d ON (pri.gppid=d.gppid)
							JOIN ".Tprefix."products p ON (p.pid=pri.pid)
							WHERE pri.pid = {$pid} AND d.affid = {$affid}
						GROUP BY d.price
							ORDER BY pri.setTime  ");


        if($db->num_rows($query2) == 0) {

            $query2 = $db->query("SELECT * FROM ".Tprefix."grouppurchase_pricing pri
							JOIN ".Tprefix."grouppurchase_pricingdetails d ON (pri.gppid=d.gppid)
							JOIN ".Tprefix."products p ON (p.pid=pri.pid)
							WHERE pri.pid = {$pid} AND d.affid = 0
							ORDER BY pri.setTime");
        }

        while($prices = $db->fetch_array($query2)) {
            $price = $prices['price'];
        }

        $price = "57800.322";
        //$price = $db->fetch_field($db->query("SELECT turnOver FROM productsactivity "), 'turnOver');
        echo $price;
    }
    if($core->input['action'] == 'do_add_affiliateorder') {
        /*
          if(is_empty($core->input['affid'], $core->input['price'], $core->input['pid'], $core->input['quantity'], $core->input['currentstock'])) {
          output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
          exit;
          } */
        //$core->input['price'] = $core->input['price_output'];
        unset($core->input['module'], $core->input['action']);
        var_dump($core->input);
        //debug($core->input);
        exit;
        //settype($core->input['price'], 'float');
        //$core->input['price'] = floatval($core->input['price']);
        //$core->input['price'] = intval($core->input['price']);
        /* debug($core->input);
          //var_dump($core->input);
          exit; */
        $query = $db->insert_query('grouppurchase_orders', $core->input);
        if($query) {
            $log->record('createaffiliateorder', $db->last_id());
            output_xml("<status>true</status><message>{$lang->ordered}</message>");
        }
        else {
            output_xml("<status>false</status><message>{$lang->errorordering}</message>");
        }
    }
}
?>