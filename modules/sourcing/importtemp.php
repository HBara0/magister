<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 * Import Temp
 *  Import Temp
 *  $module: Sourcing
 * $id: importtemp.php	
 * Created By: 		@tony.assaad		november 5, 2012 | 12:30 PM
 * Last Update: 	@tony.assaad		november 5, 2012 | 4:13 PM
 */


/* load temp data START */
function get_importtemp_data() {
	global $db;

	$tempquery = $db->query("SELECT id,REPLACE(companyName,\"'\",\"\") AS companyName,country,ProducerTrader,Email,SUBSTRING_INDEX(phone, '+', -1)as phone ,SUBSTRING_INDEX(cell, '+', -1) AS cell,website,briefing,Historical,Approachvia,
							SourcingAction,Generalcomments,Marketcompetitors,Commentstoshare
							FROM importtemp2
							WHERE  companyName is not null AND companyNAme <>'' limit 0,20");


	while($tempdata = $db->fetch_assoc($tempquery)) {
		$importtemp[$tempdata['id']]['mainproducts'] = get_mainproducts($tempdata['id']);
		$importtemp[$tempdata['id']]['mainapplicationscovered'] = get_Mainapplication($tempdata['id']);
		$importtemp[$tempdata['id']]['activityarea'] = get_ActivityArea($tempdata['id']);
		$importtemp[$tempdata['id']]['contactperson'] = get_Contactperson($tempdata['id']);
		$importtemp[$tempdata['id']]['contacthistory'] = get_Contacthistory($tempdata['id']);
		$importtemp[$tempdata['id']]['supplierdetails'] = $tempdata;
	}
	return $importtemp;
}

/* load temp data END */
function get_mainproducts($id = '') {
	global $db;
	for($i = 1; $i <= 30; $i++) {
		if($i == 1) {
			$mainproduct_field .=" Mainproducts".$i."";
		}
		else {
			$mainproduct_field .=", Mainproducts".$i."";
		}
	}
	$mainproductsquery = $db->query("SELECT ".$mainproduct_field." FROM importtemp2
										WHERE id= ".$id."");

	while($mainproduct = $db->fetch_assoc($mainproductsquery)) {
		$mainproducts = $mainproduct;
	}
	return $mainproducts;
}

function get_Contactperson($id = '') {
	global $db;

	$contact_query = $db->query("SELECT Contactperson,Email,Phone FROM importtemp2
											WHERE id= ".$id."");
	while($contactperson = $db->fetch_assoc($contact_query)) {
		$contactpersons = preg_split("/[;\/]/", $contactperson['Contactperson']);
	}

	return $contactpersons;
}

function get_Contacthistory($id = '') {
	global $db;

	$contacthistory_query = $db->query("SELECT id,market,product,DestinationCountry,REPLACE(BMname,\"'\",\"\") as BMname ,ClassGrade,Origin,Application,Marketcompetitors,Generalcomments
									FROM importtemp WHERE id= ".$id."");
	while($contacthistory = $db->fetch_assoc($contacthistory_query)) {

		if(!empty($contacthistory['BMname'])) {
			$querybname = $db->fetch_assoc($db->query("SELECT uid from users where displayName ='".($contacthistory['BMname']."'")));
			if($querybname) {
				$contacthistories = $db->fetch_assoc($db->query("SELECT uid from users where displayName ='".($contacthistory['BMname']."'")));
			}
		}
		if(!empty($contacthistory['DestinationCountry'])) {
			$queryaff = $db->fetch_assoc($db->query("SELECT affid from affiliates where name ='Orkila ".$contacthistory['DestinationCountry']."'"));
			if($queryaff) {
				$contacthistories = $db->fetch_assoc($db->query("SELECT affid from affiliates where name ='Orkila ".$contacthistory['DestinationCountry']."'"));
			}
		}
		$contacthistories['contact'] = $contacthistory;
	}
	return $contacthistories;
}

function get_ActivityArea($id = '') {
	global $db;
	for($i = 1; $i <= 13; $i++) {
		if($i == 1) {
			$activityarea_field .=" ActivitywithOrkila".$i;
		}
		else {
			$activityarea_field .=", ActivitywithOrkila".$i;
		}
	}
	$activityarea_query = $db->query("SELECT ".$activityarea_field." FROM importtemp2
										 WHERE id= ".$id."");
	while($activityarea = $db->fetch_assoc($activityarea_query)) {
		$activityareas = $activityarea;
	}
	return $activityareas;
}

function get_Mainapplication($id = '') {
	global $db;

	$mainapplication_query = $db->query("SELECT MainApplicationsCovered FROM importtemp2
											WHERE id= ".$id."");
	while($mainapplication = $db->fetch_assoc($mainapplication_query)) {
		$mainapplications = preg_split("/[,&.]/", $mainapplication['MainApplicationsCovered']);
	}
	return $mainapplications;
}

/* * ***************************************************************************************** */



if($core->input['action'] == 'do_import') {
	$alldata = get_importtemp_data();
	foreach($alldata as $compkey => $company) {
		array_unique($company['mainproducts'], SORT_STRING);
		echo '<pre> *********<strong><div style="background-color:red">NEW COMPANY id = '.$compkey.'</div></strong> <hr>';

		/* insert supplier details */

		if(!empty($company['supplierdetails']['country'])) {
			$checkcountry = $db->query("SELECT coid FROM countries WHERE name='".$company['supplierdetails']['country']."'");
		}
		if($db->num_rows($checkcountry) > 0) {
//while($country = $db->fetch_assoc($checkcountry)) {
			$supplier_data['country'] = $db->fetch_assoc($checkcountry)['coid'];
//	}
		}
		else {
			unset($supplier_data['country']);
		}

		$checksupplier = $db->query("SELECT eid as foreignid, companyName,companyNameAbbr as companyabbr FROM entities WHERE companyName = '".$company['supplierdetails']['companyName']."' AND type='s'");
		if($db->num_rows($checksupplier) > 0) {
//while($supplier = $db->fetch_assoc($checksupplier)) {
			$supplier_data = $db->fetch_assoc($checksupplier);

//}
		}
		else {
			$supplier_data['foreignid'] = 0;
			/* insert here */
		}

		echo '<pre> *********<strong><div style="background-color:red"> END NEW COMPANY id = '.$compkey.'</div></strong> <hr>';

		$allsupplier_data = array('eid' => $supplier_data['foreignid'],
				'companyName' => $company['supplierdetails']['companyName'],
				'companyNameAbbr' => $supplier_data['companyabbr'],
				'type' => $company['supplierdetails']['ProducerTrader'],
				'country' => $supplier_data['country'],
				'phone1' => $company['supplierdetails']['phone'],
				'phone2' => $company['supplierdetails']['cell'],
				'mainEmail' => $company['supplierdetails']['Email'],
				'website' => $company['supplierdetails']['website'],
				'dateCreated' => TIME_NOW,
				'commentsToShare' => $company['supplierdetails']['Commentstoshare'],
				'marketingRecords' => $company['supplierdetails']['Marketcompetitors'],
				'coBriefing' => $company['supplierdetails']['briefing'],
				'historical' => $company['supplierdetails']['Historical'],
				'sourcingRecords' => $company['supplierdetails']['SourcingAction'],
				'productFunction' => $company['supplierdetails']['Generalcomments'],
				'approchedVia' => $company['supplierdetails']['Approachvia'],
		);

// spliting phone 
//echo'</br> before removing zeroo :';print_r($supplier_data['phone1']); 
//$supplier_data['phone1'] = explode(' ',ltrim($supplier_data['phone1'],"0"));

		if(count($allsupplier_data['phone1']) == 3) {
			
		}
		if(!value_exists('sourcing_suppliers', 'eid', $allsupplier_data['eid'], 'companyName="'.$company['supplierdetails']['companyName'].'"')) {
			$query = $db->insert_query('sourcing_suppliers', $allsupplier_data);
			$supplier_id = $db->last_id();
		}
		else {
//record error
		}
		/* insert supplier details end */

		/* start mainproducts */
		if(is_array($company['mainproducts'])) {
			foreach($company['mainproducts'] as $key => $mainproduct) {
				if(!empty($mainproduct)) {
					$checkchemical = $db->query("SELECT csid ,name FROM chemicalsubstances WHERE name= '".$mainproduct."'");
				}
				if($db->num_rows($checkchemical) > 0) {
					while($chemical = $db->fetch_assoc($checkchemical)) {
						/* record company founded chmeical */
						$chemicalsfound['found'][$compkey] = $company['supplierdetails'];
						if(!value_exists('sourcing_suppliers_chemicals', 'csid', $chemical['csid'], ' ssid='.$supplier_id)) {
							$query = $db->insert_query('sourcing_suppliers_chemicals', array('ssid' => $supplier_id, 'csid' => $chemical['csid'], 'supplyType' => $company[supplierdetails]['ProducerTrader']));
						}
						else {
							$datanotfound[$compkey]['chemicalsubstances'][] = $mainproduct;
						}
//echo '<br>chemical found  : '.$chemical['csid'].' ---'.$chemical['name'].' for  <span style="background-color:yellow">'.$company['supplierdetails']['companyName'].'</span><br>';
					}
				}

				/* insert exist csid into  sourcing_suppliers_chemicals */
			}
		}/* end mainproducts */

		/* start activityarea */

		$activityareas = array('lebanon', 'nigeria', 'Tunisia',
				'Algeria', 'Egypt', 'UAE', 'Iran', 'Syria', 'Jordan', 'Lebanon', 'Morocco', 'Pakistan', 'Nigeria', 'Mauritius', 'Mozambique', 'Namibia', 'Sierra Leone', 'Swaziland', 'Zambia', 'Togo', 'Eritrea', 'Estonia', 'Lesotho', 'Liberia', 'Malawi', 'Bahrein', 'Koweit', 'Angola', 'Benin', 'Botswana', 'Burkina Faso', 'Ghana', 'Oman', 'Yemen', 'Qatar', 'Iraq', 'Ivory Coast', 'Mali', 'Lybia', 'Cyprus', 'Sudan', 'Zimbabwe', 'East Africa', 'Saudi Arabia', 'South Africa', 'Senegal');

		foreach($activityareas as $activityarea) {
			$checkactivityarea = $db->query("SELECT coid,name FROM countries WHERE name= '".$activityarea."'");
			if($db->num_rows($checkactivityarea) > 0) {
				print_r($row_activityarea);
				while($row_activityarea = $db->fetch_assoc($checkactivityarea)) {
					if(!value_exists('sourcing_suppliers_activityareas', 'coid', $row_activityarea['coid'], ' ssid='.$supplier_id)) {
						$query = $db->insert_query('sourcing_suppliers_activityareas', array('ssid' => $supplier_id, 'availability' => $company['supplierdetails'][$activityarea], 'coid' => $row_activityarea['coid']));
					}
				}
			}
		}
//		if(is_array($company['activityarea'])) {
//			foreach($company['activityarea'] as $key => $activityarea) {
//				if(!empty($activityarea)) {
//					$checkactivityarea = $db->query("SELECT coid,name FROM countries WHERE name= '".$activityarea."'"); //echo ("SELECT coid,name FROM countries WHERE name= '".$activityarea."'");
//				}echo ("SELECT coid,name FROM countries WHERE name= '".$activityarea."'");
//				if($db->num_rows($checkactivityarea) > 0) {
//					while($row_activityarea = $db->fetch_assoc($checkactivityarea)) {
//						if(!value_exists('sourcing_suppliers_activityareas', 'coid', $row_activityarea['coid'], ' ssid='.$supplier_id)) {
//							//$query = $db->insert_query('sourcing_suppliers_activityareas', array('ssid' => $supplier_id, 'coid' => $row_activityarea['coid']));
//						}
//						//$area_data['countryid'] = $row_activityarea['coid'];
//					}
//				}
//				else {
//					unset($area_data['countryid']);
//				}
//			}/* end activityarea */
//}
		/* start productSegments */
		foreach($company['mainapplicationscovered'] as $key => $productsegments) {
//echo '<pre>'; echo ("SELECT psid,title FROM productsegments WHERE title= '".trim($productsegments)."'");
			$checkproductsegments = $db->query("SELECT psid,title FROM productsegments WHERE title= '".trim($productsegments)."'");
			if($db->num_rows($checkproductsegments) > 0) {
				while($row_productsegments = $db->fetch_assoc($checkproductsegments)) {
					if(!value_exists('sourcing_suppliers_productsegments', 'psid', $row_productsegments['psid'], ' ssid='.$supplier_id)) {
//$productsegments_data['ssid'] = $supplierid['ssid'];
						$query = $db->insert_query('sourcing_suppliers_productsegments', array('ssid' => $supplier_id, 'psid' => $row_productsegments['psid']));
					}
					else {
						$datanotfound[$compkey]['productsegment'][] = $productsegments;
					}
					/* record company founded segment */
					$productsegmentsfound['area'][$compkey] = $company['activityarea'];
					/* insert sourcing_suppliers_segments */
				}
			}
			else {
				unset($productsegments_data['productid']);
			}
//		if(is_array($productsegments_data) & !empty($productsegments_data['productid'])) {
//			$productsegments_data = array('psid' => $productsegments_data['productid']);
//			$getsupplierid = $db->query("SELECT ssid FROM sourcing_suppliers ");
//			while($supplierid = $db->fetch_assoc($getsupplierid)) {
//				if(!value_exists('sourcing_suppliers_productsegments', 'psid', $productsegments_data['psid'], ' ssid='.$supplierid['ssid'])) {
//					$productsegments_data['ssid'] = $supplierid['ssid'];
//					//$query = $db->insert_query('sourcing_suppliers_productsegments', $productsegments_data);
//				}
//				/* if no supplier id exist in the database */
//				else {
//					$datanotfound[$compkey]['productsegment'][] = $productsegments;
//				}
//			}
//		}
		}/* end productSegments */


		/* start contactperson */
		foreach($company['contactperson'] as $contactperson) {
			echo '<pre>';
			$checkcontactperson = $db->query("SELECT rpid,name FROM representatives WHERE name= '".trim($contactperson)."'");
			if($db->num_rows($checkcontactperson) > 0) {
				while($row_contactperson = $db->fetch_assoc($checkcontactperson)) {
					/* record representatives founded chmeical */
					if(!value_exists('sourcing_suppliers_contactpersons', 'rpid', $row_contactperson['rpid'], ' ssid='.$supplier_id)) {
						$query = $db->insert_query('sourcing_suppliers_contactpersons', array('ssid' => $supplier_id, 'rpid' => $row_contactperson['rpid']));
					}
					/* if no contact id exist in the database */
					else {
						$datanotfound[$compkey]['contactperson'][] = $contactperson;
					}

					$contactperson_data['repid'] = $row_contactperson['rpid'];
					$contactpersonfound['person'][$compkey] = $company['contactperson'];
					/* insert sourcing_suppliers_contactpersons */
				}
			}
			else {
				unset($contactperson_data['repid']);
			}
		}/* end contactperson */

		/* start contacthistory */
		if(isset($company['contacthistory'])) {

			foreach($company['contacthistory'] as $contact) {
//$contacthistory_data['affid'] = $company['contacthistory']['affid'];
//$contacthistory_data['uid'] = $company['contacthistory']['uid'];
//foreach($company['contacthistory']['contact'] as $contactdetails) {
				if(!empty($company['contacthistory']['contact']['market'])) {

					$checkproductsegments = $db->query("SELECT psid,title FROM productsegments WHERE title= '".trim($company['contacthistory']['contact']['market'])."'");
					if($db->num_rows($checkproductsegments) > 0) {
//while($row_productsegments = $db->fetch_assoc($checkproductsegments)) {
						$contacthistory_data['market'] = $db->fetch_assoc($checkproductsegments)['psid'];
//}
					}
				}
				if(!empty($company['contacthistory']['contact']['product'])) {
					$checkchemical = $db->query("SELECT csid FROM chemicalsubstances WHERE name= '".$company['contacthistory']['contact']['product']."'");
					if($db->num_rows($checkchemical) > 0) {
//while($chemical = $db->fetch_assoc($checkchemical)) {
						$contacthistory_data['chemical'] = $db->fetch_assoc($checkchemical)['csid']; //chemical id must reset
//}
					}
				}
//}
			}
			$contacthistory_data = array('ssid' => $supplier_id, 'affid' => $company['contacthistory']['affid'], 'uid' => $company['contacthistory']['uid'], 'market' => $contacthistory_data['market'], 'chemical' => $contacthistory_data['chemical'], 'description' => $company['contacthistory']['contact']['Generalcomments'], 'grade' => $company['contacthistory']['contact']['ClassGrade'], 'competitors' => $company['contacthistory']['contact']['Marketcompetitors'],
					'application' => $company['contacthistory']['contact']['Application'], 'date' => TIME_NOW);
			if(is_array($contacthistory_data)) {
				$query = $db->insert_query('sourcing_suppliers_contacthist', $contacthistory_data);
			}
		}/* end contacthistory */
	}
}
?>

<html>
	<head>
		<title></title>
		<!-- start: headerinc -->
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta http-equiv="Content-Script-Type" content="text/javascript" />
		<link rel="shortcut icon" href="http://127.0.0.1/development/ocos//images/favicon.ico" />
		<script src="http://127.0.0.1/development/ocos//js/jquery-current.min.js" type="text/javascript"></script>
		<script src="http://127.0.0.1/development/ocos//js/jquery-ui-current.custom.min.js" type="text/javascript"></script>
		<script src="http://127.0.0.1/development/ocos//js/jquery.cookie.min.js" type="text/javascript"></script>
		<script src="http://127.0.0.1/development/ocos//js/jquery.qtip.min.js" type="text/javascript"></script>
		<script src="http://127.0.0.1/development/ocos//js/jscript.js" type="text/javascript"></script>
		<link href="http://127.0.0.1/development/ocos//styles.css" rel="stylesheet" type="text/css" />


		<!-- end: headerinc -->

	</head>
	<body>

		<form name="perform_sourcing/importtemp_Form" action="index.php?module=sourcing/importtemp" method="post"  id="perform_sourcing/importtemp_Form">

			<div align="center"></div>
			<input class="rounded" type="hidden" value="do_import" name="action" id="action" />
			<input type="submit" class="button" value="import" id="perform_sourcing/importtemp_Button" />

		</form>
	</body>
</html>