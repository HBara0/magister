<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * $module: stock
 * $id: stockorder.php	
 * Created: 	@najwa.kassem		March 19, 2010 | 9:30 AM
 * Last Update: @najwa.kassem 		April 20, 2011 | 04:44 PM
 */
 
if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['stock_canOrderStock'] == 0) {
	error($lang->sectionnopermission);
}

$session->start_phpsession();

if(!$core->input['action']) {
	$packtypes = get_specificdata('packingtypes', array('ptid', 'name'), 'ptid', 'name', 1, 1);
	$users = get_specificdata('users ', array('uid', "CONCAT(firstName, ' ', lastName)as fullname"), 'uid', 'fullname', 'uid', 0,"assistant={$core->user[uid]} OR uid ={$core->user[uid]}");	
	
	if(isset($core->input['identifier']) && !empty($core->input['identifier'])) {
		$stockorder_data = unserialize($session->get_phpsession('stockorder_'.$db->escape_string($core->input['identifier'])));

		if(!empty($stockorder_data['spid'])) {
			$spid =  $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities WHERE eid='{$stockorder_data[spid]}'"), 'companyName');
		}
		
		if(!empty($stockorder_data['timeLine']) && isset($stockorder_data['timeLine'])) {
			$stockorder_data['timeLine'] = TIME_NOW;
		}
	
		if(!empty($stockorder_data['expectedShipingDate']) && isset($stockorder_data['expectedShipingDate'])) {
			$etsdate = explode('-', $stockorder_data['expectedShipingDate']);
			$stockorder_data['expectedShipingDate_output'] = date($core->settings['dateformat'], mktime(0, 0, 0, $etsdate[1], $etsdate[0], $etsdate[2]));
		}
		
		if(!empty($stockorder_data['supplierPaymentDate']) && isset($stockorder_data['supplierPaymentDate'])) {
			$supplier_payment_date = explode('-', $stockorder_data['supplierPaymentDate']);
			$stockorder_data['supplierPaymentDate_output'] = date($core->settings['dateformat'], mktime(0, 0, 0, $supplier_payment_date[1], $supplier_payment_date[0], $supplier_payment_date[2]));	
		}
		
		if(!empty($stockorder_data['customerPaymentDate']) && isset($stockorder_data['customerPaymentDate'])) {
			$customer_payment_date = explode('-', $stockorder_data['customerPaymentDate']);
			$stockorder_data['customerPaymentDate_output'] = date($core->settings['dateformat'], mktime(0, 0, 0, $customer_payment_date[1], $customer_payment_date[0], $customer_payment_date[2]));
		}
		
		if(!empty($stockorder_data['customers']) && is_array($stockorder_data['customers'])) {
			foreach($stockorder_data['customers'] as $key => $customer) {
				if(empty($customer)) {
					continue;
				}
				
				$altrow_class = alt_row($altrow_class);
				
				$customer_rowid = $key;
				if(!isset($customer['companyName'])) {
					$customer['companyName'] = $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities WHERE eid='{$customer[eid]}'"), 'companyName');
				}
				$customer_payment_terms_from = parse_selectlist("customers[{$key}][paymentTermsFrom]", 1, array(1=>'B/L EOM 15th', 2=> 'AWB', 3=> $lang->invoice, 4 => 'B/L'), $customer["customerPaymentTermsFrom"], '');
				
				if(is_array($customer['products'])) {
					
					eval("\$customer[productsoutput] .= \"".$template->get('stock_order_customerrow_products')."\";");
				}
				eval("\$customer_row .= \"".$template->get('stock_order_customerrow')."\";");
			}
			if(empty($customer_row)) {
				$customer_rowid = $customerproduct_rowid = 1;
				$customer_payment_terms_from = parse_selectlist("customers[{$customer_rowid}][customerPaymentTermsFrom]", 1, array(1=> 'B/L EOM', 2=> 'BL/AWB'), '', '');
				eval("\$customer[productsoutput] = \"".$template->get('stock_order_customerrow_products')."\";");
				eval("\$customer_row = \"".$template->get('stock_order_customerrow')."\";");
			}
		}
		else
		{
			$customer_rowid = $customerproduct_rowid = 1;
			$customer_payment_terms_from = parse_selectlist("customers[{$customer_rowid}][customerPaymentTermsFrom]", 1, array(1 => 'B/L EOM', 2=> 'BL/AWB'), '', '');
			eval("\$customer[productsoutput] = \"".$template->get('stock_order_customerrow_products')."\";");
			eval("\$customer_row = \"".$template->get('stock_order_customerrow')."\";");
			
			
			$customer_rowid = 0;
			eval("\$unallocated_quantities = \"".$template->get('stock_order_customerrow_products')."\";");
		}
		
		if(!empty($stockorder_data['pid']) && is_array($stockorder_data['pid'])) {
			foreach($stockorder_data['pid'] as $key => $pid) {
				if(empty($pid)){
					continue;	
				}
				
				$product_rowid = $key;
				
				$productname = $db->fetch_field($db->query("SELECT name FROM ".Tprefix."products WHERE pid={$pid}"), 'name');
				
				$types = get_specificdata('packingtypes', array('ptid', 'name'), 'ptid', 'name', 1, 1);
				
				$packing = $db->query("SELECT packingType FROM ".Tprefix."productpacking WHERE pid={$pid}");
				$packingtype .= "<option value='0'></option>";
				while($pack = $db->fetch_assoc($packing)) {
					if($optionvalue == $stockorder_data["packingType"][$key]) {
						$selected = " selected = 'selected'";
					}
					else
					{
						$selected ='';
					}
					
					$packingtype .= '<option value="'.$pack['packingType'].'"'.$selected.'>'.$types[$pack['packingType']].'</option>';
				}
				
				eval("\$product_row .= \"".$template->get('stock_order_productrow')."\";");
			}
			
			if(empty($product_row)) {
				$product_rowid = 1;
				eval("\$product_row = \"".$template->get('stock_order_productrow')."\";");
			}
		}
		else
		{
			$product_rowid = 1;
			eval("\$product_row = \"".$template->get('stock_order_productrow')."\";");
		}
		
		$submittedby_list = parse_selectlist('submittedBy', 1, $users, $stockorder_data['submittedBy'], '');	
	}
	else
	{
		$identifier = md5(uniqid(microtime()));
	
		$customer_rowid = $customerproduct_rowid = 1;
		$customer_payment_terms_from = parse_selectlist("customers[$customer_rowid][paymentTermsFrom]", 1, array(1=>'B/L EOM 15th', 2=> 'AWB', 3=> $lang->invoice, 4 => 'B/L'), '', '');
		
		eval("\$customer[productsoutput] = \"".$template->get('stock_order_customerrow_products')."\";");
		eval("\$customer_row .= \"".$template->get('stock_order_customerrow')."\";");
		$customer_rowid++;
			
		$product_rowid = 1;
		$pack_type = parse_selectlist("packingType[{$prowid}]", 1, $packtypes, $stockorder_data["packingType[{$prowid}]"], '');		
		eval("\$product_row .= \"".$template->get('stock_order_productrow')."\";");
		$product_rowid++;
			
		$submittedby_list = parse_selectlist('submittedBy', 1, $users, $core->user['uid'], '');	
	}
	
	$supplier_payment_terms_from = parse_selectlist('supplier[paymentTermsFrom]', 1, array(1=>'B/L EOM 15th', 2=> 'AWB', 3=> $lang->invoice, 4 => 'B/L'), '', '');
	$types = array(0=> '', 1 => 'Stock', 2 => 'Reinvoicing');
	$type_order = parse_selectlist('type', 1, $types, $stockorder_data['type'], '');
	$affiliate_query = $db->query("SELECT *, a.affid, a.name as affname 
									FROM ".Tprefix."affiliates a JOIN ".Tprefix."affiliatedemployees ae ON (a.affid=ae.affid)
									WHERE ae.uid={$core->user[uid]}");
	
	while($affiliate = $db->fetch_array($affiliate_query)) {
		$affiliates[$affiliate['affid']] = $affiliate['affname'];
	}				
		
	$affiliates_list = parse_selectlist('affid', 1, $affiliates, $stockorder_data['affid']);

	$users = get_specificdata('users ', array('uid', "CONCAT(firstName, ' ', lastName) AS fullname"), 'uid', 'fullname', 'uid', 0,"assistant={$core->user[uid]} OR uid={$core->user[uid]}");
	
	if(empty($stockorder_data['currency'])) {
		$stockorder_data['currency'] = 'USD';
	}
	
	$currencies =  get_specificdata('currencies ', array('alphaCode', 'alphaCode'), 'alphaCode', 'alphaCode', 'alphaCode', 0);
	$currency = parse_selectlist('currency', 1, $currencies, $stockorder_data['currency'], '');
	$methods = array('0' => '','1' => 'CNF','2' => 'FOB source', '3' => 'FOB dest', '4' => 'Ex Works', '5' => 'CIP', '6' => 'CIF');
	$incoterms = parse_selectlist('supplier[incoTerms]', 1, $methods, $stockorder_data['incoTerms'], 0);
	
	$cities = get_specificdata('cities ', array('ciid', 'name'), 'ciid', 'name', 1, 1);
	$incotermslocation = parse_selectlist('supplier[incoTermsLocation]', 1, $cities, $stockorder_data['incoTermsLocation'], 0);
	
	$affiliate_info = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."affiliates WHERE affid={$core->user[mainaffiliate]}"));
	$management_query = $db->query("SELECT uid, CONCAT(firstName, ' ', lastName) AS emplopyeeName FROM ".Tprefix."users WHERE uid IN ({$affiliate_info[supervisor]},{$affiliate_info[generalManager]})");
	while($management = $db->fetch_array($management_query)) {
		$managers[$management['uid']] = $management['emplopyeeName'];
	}
	
	$affiliate_info['bankInterest'] = 7.5;
	$affiliate_info['risk'] = 3;
	$affiliate_info['warehouseCharges'] = 0.12;
	
	$selectlist['warehouseunit'] = parse_selectlist('warehouseUnit', 4, array(1 => $lang->palets, 2 => 'CBM'), $stockorder_data['warehouseUnit'], '');
	
	eval("\$form_page = \"".$template->get('stock_order')."\";");
	output_page($form_page);
}
else
{
	if($core->input['action'] == 'get_orderNumber') {
		$date = explode('-', $db->escape_string($core->input['timeLine']));
		if((($core->input['type']) != 0) || (!empty($core->input['timeLine']))) {
			$orderNumbers = $db->fetch_field($db->query("SELECT Max(orderNumber) as nb FROM ".Tprefix."stockorder WHERE type= ".$db->escape_string($core->input['type'])." AND affid = ".$db->escape_string($core->input['affid'])." AND timeLine BETWEEN ".mktime(0, 0, 0, 0, 0, $date[2])." AND ".mktime(0, 0, 0, 12, 31, $date[2]).""), 'nb');
		}
		if(empty($orderNumbers)) {
			$orderNumbers = 1;
		}
		echo $orderNumbers;
	}
	elseif($core->input['action'] == 'get_supplierPaymentTermsDays') {
		if(empty($core->input['spid'])) {
			exit;
		}
		echo $db->fetch_field($db->query("SELECT paymentTerms FROM ".Tprefix."entities WHERE eid=".$db->escape_string($core->input['spid']).""), 'paymentTerms');
	}
	elseif($core->input['action'] == 'get_customerpayments') {	
		if(empty($core->input['eid'])) {
			exit;
		}
		echo $db->fetch_field($db->query("SELECT paymentTerms FROM ".Tprefix."entities WHERE eid=".$db->escape_string($core->input['eid']).""), 'paymentTerms');
	}
	elseif($core->input['action'] == 'get_packingWeight') {
		if(is_empty($core->input['pid'], $core->input['packingType'])) {
			exit;
		}
		echo $db->fetch_field($db->query("SELECT packingWeight FROM ".Tprefix."productpacking WHERE pid=".$db->escape_string($core->input['pid'])." AND packingType=".$db->escape_string($core->input['packingType']).""), 'packingWeight');
	}
	elseif($core->input['action'] == 'get_packingType') {

		$types = get_specificdata('packingtypes', array('ptid', 'name'), 'ptid', 'name', 1, 1);
		$packing = $db->query("SELECT packingType FROM ".Tprefix."productpacking WHERE pid=".$db->escape_string($core->input['pid'])."");
	
		$packingType .= "<option value='0'></option>";
		while($pack = $db->fetch_assoc($packing)) {
			$packingType .= '<option value="'.$pack['packingType'].'">'.$types[$pack['packingType']].'</option>';
		}
		//$packingType = "<option value='0'></option><option value='1'>eeeee</option><option value='2'>gg</option>";
		echo $packingType;
	}
	elseif($core->input['action'] == 'get_fxrate') {
		require_once './inc/currency_functions.php';
		
		$xml = simplexml_load_string(fx_fromfeed($core->input['currency'], 'USD'));
		echo $xml[0];
	}	
	elseif($core->input['action'] == 'do_stockorder') {
	$session->set_phpsession(array('stockorder_'.$core->input['identifier'] => serialize($core->input)));

	if(is_empty($core->input['affid'], $core->input['type'], $core->input['orderNumber'], $core->input['fxUSD'], $core->input['warehouseUnitSize'], $core->input['submittedBy'])) {
		error($lang->fillrequiredfields, 'index.php?module=stock/order&identifier='.$core->input['identifier']); 
	}
			
	foreach($core->input['supplier'] as $key => $value) {
		if(empty($value)) {
			error($lang->fillrequiredfields, 'index.php?module=stock/order&identifier='.$core->input['identifier']);
		}
	}
	
	/* Parameters*/
	$parameters['incoterms'] = array('0' => '','1' => 'CNF','2' => 'FOB source', '3' => 'FOB dest', '4' => 'Ex Works', '5' => 'CIP', '6' => 'CIF');
	$parameters['paymenttermsfrom'] = array(1=> 'B/L EOM 15th', 2=> 'AWB', 3=> $lang->invoice, 4 => 'B/L');
	$parameters['types'] = array(0=> '', 1 => 'Stock', 2 => 'Reinvoicing');
	$parameters['currencies'] = array(1=>'$', 2=> 'Euro');
	$parameters['warehouseunits'] = array(1 => $lang->palets, 2 => 'CBM');
	$parameters['nextorderintervals'] = array(1 => $lang->dailyorder, 7 => $lang->weeklyorder, 30 => $lang->monthlyorder, 91 => $lang->quarterlyorder, 182 => $lang->semiannualorder, 365 => $lang->yearlyorder); 
		
	$order = $core->input;
	/* Prepare & parse order headers - Start */
	$order['date'] = TIME_NOW;
	$order['date_output'] = date($core->settings['dateformat'], $order['date']);
	
	$order['affiliatename'] = $db->fetch_field($db->query("SELECT name FROM ".Tprefix."affiliates WHERE affid=".$db->escape_string($core->input['affid']).""), 'name');
	$order['type_output'] = $parameters['types'][$order['type']];
	
	$order['number_output'] = strtoupper(substr($parameters['types'][$order['type']], 0, 2)).'|'.$core->input['orderNumbers'].'/'.date('y', $order['date']);
	$order['currency_output'] = $parameters['currencies'][$order['currency']];
	$order['warehouseUnit_output'] = $parameters['warehouseunits'][$order['warehouseUnit']];
	/* Prepare & parse order headers - Start */

	/* Prase the Supplier Section - Start */
	$supplier = $core->input['supplier'];
	$supplier['incoTerms_output'] = $parameters['incoterms'][$supplier['incoTerms']];
	$supplier['incoTermsLocation_ouput'] = $db->fetch_field($db->query("SELECT name FROM ".Tprefix."cities WHERE ciid=".$db->escape_string($supplier['incoTermsLocation']).""), 'name');
	$supplier['paymentTermsFrom_output'] = $parameters['paymenttermsfrom'][$supplier['paymentTermsFrom']];
	
	$expectedshippingdate = explode('-', $supplier['expectedShippingDate']);
	$supplier['expectedShippingDate'] = mktime(1, 0, 0, $expectedshippingdate[1], $expectedshippingdate[0], $expectedshippingdate[2]);  
	$supplier['expectedShippingDate_output'] = date($core->settings['dateformat'], $supplier['expectedShippingDate']);
	$order['eststockentry'] = $supplier['expectedShippingDate']+($supplier['daysToDeliver']*60*60*24);
	
	if(empty($order['supplierPaymentDate'])) {
		$order['supplierPaymentDate'] = $supplier['expectedShippingDate']+($supplier['paymentTermsDays']*60*60*24);
	}
	else
	{
		$supplierpaymentdate = explode('-', $order['supplierPaymentDate']);
		$order['supplierPaymentDate'] = mktime(1, 0, 0, $supplierpaymentdate[1], $supplierpaymentdate[0], $supplierpaymentdate[2]);  
		unset($supplierpaymentdate);
	}
	eval("\$supplier_section = \"".$template->get('stock_order_page_suppliersection')."\";");
	/* Prase the Supplier Section - Start */

	/* Prase the Customers Section - Start */
	$order['maxcustpaymentterms'] = 0;
	foreach($order['customers'] as $key => $customer) {
		$customer_products_rows = '';
		if(empty($customer['cid'])) {
			unset($order['customers'][$key]);
			continue;
		}

		if($order['maxcustpaymentterms'] < $customer['paymentTermsDays']) {
			$order['maxcustpaymentterms'] = $customer['paymentTermsDays'];
		}	
						
		if(is_array($customer['products'])) {
			foreach($customer['products'] as $customerproduct_rowid => $customer_products) {		
				$cache['productnames'][$customer_products['pid']] = $customer_products['productName'];
				/* Calculate days in stock - Start */
				$customerproducts_daysinstock_temp = 0;
								
				$firstorderdate_parts = explode('-', $customer_products['firstOrderDate']);
				$customer_products['firstOrderDate'] = mktime(1, 0, 0, $firstorderdate_parts[1], $firstorderdate_parts[0], $firstorderdate_parts[2]);
				
				$customerproducts_daysinstock_temp = (($customer_products['firstOrderDate']-TIME_NOW)/60/60/24)+($customer_products['nextOrdersInterval']*$customer_products['numOrders']);
				if(isset($customerproducts_daysinstock[$customer_products['pid']])) {
					if($customerproducts_daysinstock[$customer_products['pid']] < $customerproducts_daysinstock_temp) {
						$customerproducts_daysinstock[$customer_products['pid']] = $customerproducts_daysinstock_temp;
					}
				}
				else
				{
					$customerproducts_daysinstock[$customer_products['pid']] = $customerproducts_daysinstock_temp;
				}
				
				$customer_products['expectedQuantity'] = $customer_products['firstOrderQty']+($customer_products['quantityPerNextOrder']*$customer_products['numOrders']);
				/* Calculate days in stock - End */
				
				$customer_products['nextOrdersInterval_output'] = $parameters['nextorderintervals'][$customer_products['nextOrdersInterval']];
				$customer_products['firstOrderDate_output'] = date($core->settings['dateformat'], $customer_products['firstOrderDate']);
				$order['totalNetWeight'] += $customer_products['expectedQuantity'];
				eval("\$customer_products_rows .= \"".$template->get('stock_order_preview_customers_products')."\";");
			}
		}
		
		$customer['paymentTermsFrom_output'] = $parameters['paymenttermsfrom'][$customer['paymentTermsFrom']];
		
		eval("\$customers_sections_rows .= \"".$template->get('stock_order_page_customerssection_entry')."\";");
	}
	eval("\$customers_section = \"".$template->get('stock_order_page_customerssection')."\";");
	
	
	if(empty($order['customersPaymentDate'])) {
		$order['customersPaymentDate'] = $order['eststockentry']+($order['maxcustpaymentterms']*60*60*24);
	}
	else
	{
		$customerpaymentdate = explode('-', $order['customersPaymentDate']);
		$order['customersPaymentDate'] = mktime(1, 0, 0, $customerpaymentdate[1], $customerpaymentdate[0], $customerpaymentdate[2]);  
		unset($customerpaymentdate);
	}
	
	/* Prase the Customers Section - End */

	/* Parse unallocated quantities - Start */
	if(is_array($order['unallocatedquantity'])) {
		foreach($order['unallocatedquantity'] as $key => $product) {
			if(empty($product['pid'])) {
				continue;
			}

			$order['totalNetWeight'] += $product['firstOrderQty'];
		}
 	}
	/* Parse unallocated quantities - End */
	
	/* Parse payments section - Start */
	$order['customersPaymentDate_output'] = date($core->settings['dateformat'], $order['customersPaymentDate']);
	$order['supplierPaymentDate_output'] = date($core->settings['dateformat'], $order['supplierPaymentDate']);
	/* Parse payments section - End */
	
	/* Finalize warehouse charges cost - Start */
	foreach($order['customers'] as $key => $customer) {
		if(empty($customer['cid'])) {
			continue;
		}
		
		if(is_array($customer['products'])) {
			foreach($customer['products'] as $customerproduct_rowid => $customer_products) {
				/* Calculate warehouse cost - Start */
				$customerproducts_warehousecharges[$customer_products['pid']] += ($customer_products['firstOrderQty']/$order['totalNetWeight'])*(($customer_products['firstOrderDate']-$order['eststockentry'])/60/60/24)*$order['warehousecharges']*$order['warehouseUnitSize']; //should be by CBM
				for($i=1;$i<=$customer_products['numOrders'];$i++) {
					$customerproducts_warehousecharges[$customer_products['pid']] += ($customer_products['quantityPerNextOrder']/$order['totalNetWeight'])*((($customer_products['firstOrderDate']+($customer_products['nextOrdersInterval']*60*60*24))-$order['eststockentry'])/60/60/24)*$order['warehousecharges']*$order['warehouseUnitSize']; //should be by CBM
				}
				/* Calculate warehouse cost - End */
			}
		}
		
	}
	
	if(is_array($order['unallocatedquantities'])) {
		foreach($order['unallocatedquantities'] as $key => $product) {
			if(empty($product['pid'])) {
				continue;
			}
			
			$customerproducts_warehousecharges[$product['pid']] += ($product['firstOrderQty']/$order['totalNetWeight'])*($product['daysInStock'])*$order['warehousecharges']*$order['warehouseUnitSize']; //should be by CBM
		}
 	}
	
	/* Finalize warehouse charges cost - End */
	
	/* Reinitialize Some Totals */
		$order['totalNetWeight'] = 0;
	/* Reinitialize Some Totals */
	
	/* Prase the products Section - Start */
	foreach($order['products'] as $key => $product) {
		//print_r($product);
		//echo '<hr />';
		if(empty($product['pid'])) {
			unset($order['products'][$key]);
			continue;
		}
		
		$product['purchaseAmount'] = $product['quantity']*$product['purchasePrice'];
		$product['sellingAmount'] = $product['quantity']*$product['sellingPrice'];
		
		$product['name'] = $cache['productnames'][$product['pid']];
		$product['costPrice'] = round((($product['clearingFees']+$product['lcFees'])/$product['quantity'])+$product['purchasePrice'], 3);
		$product['grossMargin'] = ($product['sellingPrice']-$product['costPrice'])*$product['quantity'];
		
		$product['riskcost'] = (($core->input['risk']/100)*$product['purchaseAmount'])*$core->input['fxUSD'];
		$product['daysinstockcost'] = $customerproducts_daysinstock[$product['pid']]*$core->input['warehouseCharges']*($core->input['warehouseUnitSize']);
		
		if($order['customersPaymentDate'] > $order['supplierPaymentDate']) {
			$product['interestcost'] = $product['purchaseAmount']*($order['bankInterest']/100)*((($order['customersPaymentDate']-$order['supplierPaymentDate'])/60/60/24)/360);
		}
		
		$product['netMargin'] = round($product['grossMargin']-$product['riskcost']-$product['daysinstockcost']-$product['interestcost'], 3);			
		$product['netMarginPerc'] = round(($product['netMargin']*100)/$product['sellingAmount'], 2);
		
		$product['estsaledate'] = $order['eststockentry']+($customerproducts_daysinstock[$product['pid']]*60*60*24);
		
		$product['estsaledate_output'] = date($core->settings['dateformat'], $product['estsaledate']);
		/* Increment Totals - Start */
		$order['totalPurchaseValue'] += $product['purchaseAmount'];
		$order['totalSellingAmount'] += $product['sellingAmount'];
		$order['totalNetMargin'] += $product['netMargin'];
		$order['totalNetWeight'] += $product['quantity'];
		/* Increment Totals - End */
			
		eval("\$product_section_rows .= \"".$template->get('stock_order_page_poductssection_entry')."\";");
	}
	
	eval("\$products_section = \"".$template->get('stock_order_page_poductssection')."\";");
	/* Prase the Products Section - End */
	
	/* Caluclate Additional Totals - Start */
	$order['numberOfItems'] = count($order['products']);
	$order['totalNetMarginPerc'] = ($order['totalNetMargin']*100/$order['totalSellingAmount']);
	/* Calculate Additional Totals - End */

	//exit;
	//print_r($core->input);
	//exit;
		/*$session->set_phpsession(array('stockorder_'.$core->input['identifier'] => serialize($core->input)));
	
		if(is_empty($core->input['affid'], $core->input['type'], $core->input['timeLine'], $core->input['orderNumbers'], $core->input['currency'], $core->input['fxUSD'], $core->input['volume'], $core->input['spid'], $core->input['incoTerms'], $core->input['incoTermsLocation'], $core->input['supplierPaymentTermsDays'], $core->input['supplierPaymentTermsFrom'], $core->input['expectedShipingDate'], $core->input['daysToDeliver'], $core->input['submittedBy'])) {
			error($lang->fillrequiredfields, 'index.php?module=stock/order&identifier='.$core->input['identifier']); 
		}
		
		$methods = array('0' => '','1' => 'CNF','2' => 'FOB source', '3' => 'FOB dest', '4' => 'Ex Works', '5' => 'CIP', '6' => 'CIF');
		foreach($core->input['eid'] as $key => $eid) 
		{		
			$row_class = alt_row($row_class);
			if(is_empty($eid, $core->input['customerpayments'][$key], $core->input['customerPaymentTermsFrom'][$key])) {
				 error($lang->fillrequiredfields, 'index.php?module=stock/order&identifier='.$core->input['identifier']); 
			}
			else
			{
				$customername = $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities WHERE eid=".$db->escape_string($eid).""), 'companyName');
				$customer_row .= "<tr class='{$row_class}'><td colspan='2' class='tdleft'>{$customername}</td><td>{$core->input[customerpayments][$key]}</div></td><td class='tdright'>{$methods[$core->input[customerPaymentTermsFrom][$key]]}</td></tr>";
			}
		}
		
		$etsdate = explode('-', $db->escape_string($core->input['expectedShipingDate']));
		$core->input['expectedShipingDate'] = mktime(0, 0, 0, $etsdate[1], $etsdate[0], $etsdate[2]);
		$expectedshipingdate = date($core->settings['dateformat'], $core->input['expectedShipingDate']);
		
		$supplier_payment_date = explode('-', $db->escape_string($core->input['supplierPaymentDate']));
		$core->input['supplierPaymentDate'] = mktime(0, 0, 0, $supplier_payment_date[1], $supplier_payment_date[0], $supplier_payment_date[2]);
		$supplierpaymentdate = date($core->settings['dateformat'], $core->input['supplierPaymentDate']);	
		
		$customer_payment_date = explode('-', $db->escape_string($core->input['customerPaymentDate']));
		$core->input['customerPaymentDate'] = mktime(0, 0, 0, $customer_payment_date[1], $customer_payment_date[0], $customer_payment_date[2]);
		$customerpaymentdate = date($core->settings['dateformat'], $core->input['customerPaymentDate']);
		
		
		$eststock = date($core->settings['dateformat'], mktime(0, 0, 0, $etsdate[1], $etsdate[0]+$core->input['daysToDeliver'], $etsdate[2]));
		
		$bankinterest = 7.5;
		$risk = 3;
		$warehouse = 0.12;
		//$affiliate['warehousecost'] = 0.12;
		
		$nbitems = sizeof($core->input['pid']);
		
		foreach($core->input['pid'] as $key => $pid) {	
			//$pid = 1;
			if(is_empty($pid, $core->input['packingType'][$key], $core->input['packingWeight'][$key], $core->input['quantity'][$key], $core->input['unitPurchasePrice'][$key], $core->input['daysInStock'][$key], $core->input['clearningFees'][$key], $core->input['lcFees'][$key], $core->input['purchaseAmount'][$key], $core->input['sellingPrice'][$key])) {
				error($lang->fillrequiredfields, 'index.php?module=stock/order&identifier='.$core->input['identifier']); 
			}
			else
			{ 
				$productname = $db->fetch_field($db->query("SELECT name FROM ".Tprefix."products WHERE pid = ".$db->escape_string($pid)), 'name');
				
				$net_weight += $core->input['quantity'][$key];	 
				$core->input['purchaseAmount'][$key] = $core->input['quantity'][$key] * $core->input['unitPurchasePrice'][$key];
				$ordervalue += $core->input['purchaseAmount'][$key];
				
				$costprice = $core->input['clearningFees'][$key] + $core->input['lcFees'][$key] + $core->input['purchasePrice'][$key];/// MAKE SURE IT IS PRICE NOT AMOUT
				$gross_margin = ($core->input['sellingPrice'][$key] - $costprice ) * $core->input['quantity'][$key] * $core->input['fxUSD'];
				
				if($core->input['customerPaymentDate'] > $core->input['supplierPaymentDate']) {	
					$nbdays = abs($core->input['customerPaymentDate'] - $core->input['supplierPaymentDate'])/86400;
					$bank_interest = $core->input['purchaseAmount'][$key] * $bankinterest / 100 * $nbdays / 360;
				}
				else
				{
					$bank_interest = 0;
				}
				
				$packtypes = get_specificdata('packingtypes', array('ptid', 'name'), 'ptid', 'name', 1, 1);
				$net_margin_usd = round(($gross_margin - (($bank_interest + ($risk/100 * $core->input['purchaseAmount'][$key]))* $core->input['fxUSD'])) - ($core->input['volume'] * $core->input['daysInStock'][$key] * $warehouse), 3);
				$total_net_margin_usd += $net_margin_usd;
				$net_margin = round($net_margin_usd / (($core->input['sellingPrice'][$key]* $core->input['quantity'][$key])*$core->input['fxUSD'])*100, 2);
				$total_net_margins += $net_margin;
				$row_class = alt_row($row_class);
				$product_row .= " <tr class='{$row_class}'>
						<td class='tdleft' style='width:23%;'>{$productname}</td>
						<td style='width:11%;'>{$packtypes[$core->input[packingType][$key]]}</td>
						<td style='width:5%;'>{$core->input[packingWeight][$key]}</td> 
						<td style='width:5%;'>{$core->input[quantity][$key]}</td> 
						<td style='width:6%;'>{$core->input[unitPurchasePrice][$key]}</td>
						<td style='width:5%;'>{$core->input[daysInStock][$key]}</td> 
						<td style='width:5%;'>{$core->input[clearningFees][$key]}</td>
						<td style='width:5%;'>{$core->input[lcFees][$key]}</td>
						<td style='width:6%;'>{$core->input[sellingPrice][$key]}</td>
						<td style='width:6%;'>{$core->input[purchaseAmount][$key]}</td>
						<td style='width:6%;'>{$costprice}</td>
						<td style='width:6%;'>{$gross_margin}</td>
						<td style='width:6%;'>{$net_margin}</td>
						<td class='tdright' style='width:6%;'>{$net_margin_usd}%</td>
					</tr>";
				
				$shelflife = $db->fetch_field($db->query("SELECT shelfLife FROM ".Tprefix."products WHERE pid=".$db->escape_string($pid)), 'shelfLife');
				$estsale = date($core->settings['dateformat'], mktime(0, 0, 0, $etsdate[1], $etsdate[0]+$core->input['daysToDeliver'] + $core->input['daysInStock'][$key], $etsdate[2]));
				$actualpurchase .= "<tr class='{$row_class}'><td class='tdleft'>{$core->input[quantity][$key]}</td><td>{$core->input[purchaseAmount][$key]}</td><td>{$eststock}</td><td>{$shelflife}</td><td class='tdright'>{$estsale}</td></tr>";
			}
		}
		
		$supplier = $db->fetch_field($db->query("SELECT companyName FROM ".Tprefix."entities WHERE eid=".$db->escape_string($core->input['spid']).""), 'companyName');
		$year  = explode('-', $core->input['timeLine']);
		$timeline = explode('-', $db->escape_string($core->input['timeLine']));
		$core->input['timeLine'] = mktime(0, 0, 0, $timeline[1], $timeline[0], $timeline[2]);
		$date = date($core->settings['dateformat'], $core->input['timeLine']);
		$types = array(0=> '', 1 => 'Stock', 2 => 'Reinvoicing');
		$core->input['orderNumbers'] = $types[$core->input['type']].'|'.$core->input['orderNumbers'].'|'.substr($year[2],-2,2);;
		
		$total_net_margin = $total_net_margins / $nbitems;
		
		$currency_array = array(1=>'$', 2=> 'Euro');
		$currency = $currency_array[$core->input['currency']];
		
		$type = $types[$core->input['type']];
		$affiliate = $db->fetch_field($db->query("SELECT name FROM ".Tprefix."affiliates WHERE affid=".$db->escape_string($core->input['affid']).""), 'name');
		$users = get_specificdata('users ', array('uid', "CONCAT(firstName, ' ', lastName)as fullname"), 'uid', 'fullname', 'uid', 0,"assistant={$core->user[uid]} OR uid ={$core->user[uid]}");		
		$incoterms = $methods[$core->input['incoTerms']];
		$cities = get_specificdata('cities ', array('ciid', "name"), 'ciid', 'name', 1, 1);
		$incotermslocation = $cities[$core->input['incoTermsLocation']];
		$supplier_payment_terms_from = array(1=>'B/L EOM', 2=> 'BL/AWB');
		$supplierpaymenttermsfrom = $supplier_payment_terms_from[$core->input['supplierPaymentTermsFrom']];
				*/
		
		eval("\$stockorder = \"".$template->get('stock_order_page')."\";");
		eval("\$form_page = \"".$template->get('stock_order_preview')."\";");
		output_page($form_page);
		
	}
	elseif($core->input['action'] == 'do_add_order') {

		$core->input = unserialize($session->get_phpsession('stockorder_'.$db->escape_string($core->input['identifier'])));

		unset($core->input['supplier']['companyName']);
		$neworder_supplier = $core->input['supplier'];
		unset($core->input['supplier']);
		
		$required_fields = $db->show_fields_from('stockorder');
		foreach($required_fields as $field) {
			if(isset($core->input[$field['Field']])) {
				$neworder_supplier[$field['Field']] = $core->input[$field['Field']];
			}
		}
		
		$query = $db->insert_query('stockorder', $neworder_supplier);
		$soid = $db->last_id();
		if($query) {
			foreach($core->input['customers'] as $key => $customer) {
				if(empty($customer['cid'])) {
					unset($core->input['customers'][$key]);
					continue;
				}
				
				$neworder_customer = array(
					'soid' => $soid,
					'cid' => $customer['cid'],
					'paymentTermsDays' => $customer['paymentTermsDays'],
					'paymentTermsFrom' => $customer['paymentTermsFrom']
				);
				
				if(is_array($customer['products'])) {
					$db->insert_query('stockorder_customers', $neworder_customer);
					$socid = $db->last_id();
					
					foreach($customer['products'] as $customerproduct_rowid => $customer_product) {
						$neworder_customerporduts = array(
							'socid' => $socid,
							'pid'	=> $customer_product['pid'],
							'firstOrderQty' => $customer_product['firstOrderQty'],
							'firstOrderDate' => $customer_product['firstOrderDate'],
							'numOrders' => $customer_product['numOrders'],
							'quantityPerNextOrder' => $customer_product['quantityPerNextOrder'],
							'nextOrdersInterval' => $customer_product['nextOrdersInterval']
						);
						$db->insert_query('stockorder_customers_products', $neworder_customerporduts);
					}
				}
			}
			
			$required_fields = $db->show_fields_from('stockorder_products');
			foreach($core->input['products'] as $key => $product) {
				$neworder_products = array();
				foreach($required_fields as $field) {
					if(isset($product[$field['Field']])) {
						$neworder_products[$field['Field']] = $product[$field['Field']];
					}
				}
			
				$db->insert_query('stockorder_products', $neworder_products);
			}
		}
		
		//$session->destroy_phpsession();
		/*
		foreach($core->input['eid'] as $key => $eid) 
		{
			$cutomers[$key] = array(
				'soid' 	=> $soid,
				'eid'	  => $eid,
				'paymentTermsDays'   => $core->input["customerpayments"][$key],
				'paymentTermsFrom'   => $core->input["customerPaymentTermsFrom"][$key]
				);
		}		
		foreach($core->input['pid'] as $key => $pid) 
		{	
			$products[$key] = array(
					'soid' 	=> $soid,
					'pid'	  => $pid,
					'packing'   => $core->input['packingType'][$key],
					'packingWeight'   => $core->input['packingWeight'][$key],
					'quantity'   => $core->input['quantity'][$key],
					'daysInStock'   => $core->input['daysInStock'][$key],
					'clearingFees'   => $core->input['clearingFees'][$key],
					'lcFees'   => $core->input['lcFees'][$key],
					'unitPurchasePrice'   => $core->input['unitPurchasePrice'][$key],
					'sellingPrice'   => $core->input['sellingPrice'][$key]
					); 
		}
		
		$core->input['preparedBy'] = $core->user['uid'];
		$core->input['eid'] = $core->input['spid'];
		
		//PROBABLY IT IS BETTER TO CREATE A NEW ARRAY
		unset($core->input['module'], $core->input['action'], $core->input['unitPurchasePrice'][$key], $core->input["customerpayments"], $core->input["customerPaymentTermsFrom"], $core->input["packingType"], $core->input["packingWeight"], $core->input["quantity"], $core->input["daysInStock"], $core->input["clearningFees"], $core->input["lcFees"], $core->input["purchaseAmount"], $core->input["sellingPrice"], $core->input['pid'], $core->input['spid']);
		
		$stockorder_query = $db->insert_query('stockorder', $core->input);
		
		if(!$stockorder_query) {
			
			output_xml("<status>false</status><message>{$lang->errorordering}{$error}</message>");
			exit;
		}			
		$soid = $db->last_id();
		$log->record($soid);
		foreach($products as $key => $pid) 
		{		
			$pid['soid'] = $soid;
			$product_query = $db->insert_query('stockorder_products', $pid);
			if($product_query) {
				$log->record($db->last_id());
			}
			else
			{
				$errors[] = $pid;
			}
		}	
		foreach($cutomers as $key => $eid) 
		{
			$eid['soid'] = $soid;
			$customer_query = $db->insert_query('stockorder_customers', $eid);	
			if($customer_query) {
				$log->record($db->last_id());
			}
			else
			{
				$errors[] = $eid;
			}
		}
		
		if(!isset($errors))		
		{
			output_xml("<status>true</status><message>{$lang->ordered}</message>");
		}
		else
		{ 	foreach($errors as $oid) 
			{
				$error .= $oid.'-';
			}
			output_xml("<status>false</status><message>{$lang->errorordering}{$error}</message>");
		}*/
	}
	elseif($core->input['action'] == 'ajaxaddmore_customer')
	{
		$customer_rowid = $db->escape_string($core->input['value'])+1;
		$customerproduct_rowid = 1;
		eval("\$customer[productsoutput] = \"".$template->get('stock_order_customerrow_products')."\";");
		
		$customer_payment_terms_from = parse_selectlist("customers[$customer_rowid][paymentTermsFrom]", 1, array(1=>'B/L EOM 15th', 2=> 'AWB', 3=> $lang->invoice, 4 => 'B/L'), '', '');

		eval("\$newsection = \"".$template->get('stock_order_customerrow')."\";");
		echo $newsection;
	}
}	
?>