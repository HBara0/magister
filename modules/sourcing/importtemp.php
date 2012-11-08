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

	$tempquery = $db->query("SELECT id,REPLACE(companyName,\"'\",\"\") AS companyName,country,producerTrader,Email,phone,cell,website,briefing,Historical,Approachvia,
							SourcingAction,Generalcomments,Marketcompetitors,Commentstoshare,Text32682,Text32678,Combo32670
							FROM importtemp
							WHERE  companyName is not null AND companyNAme <>''");
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
	$mainproductsquery = $db->query("SELECT ".$mainproduct_field." FROM importtemp
										WHERE id= ".$id."");

	while($mainproduct = $db->fetch_assoc($mainproductsquery)) {
		$mainproducts = $mainproduct;
	}
	return $mainproducts;
}

function get_Contactperson($id = '') {
	global $db;

	$contact_query = $db->query("SELECT Contactperson,Email,Phone FROM importtemp
											WHERE id= ".$id."");
	while($contactperson = $db->fetch_assoc($contact_query)) {
		$contactpersons = preg_split("/[;\/]/", $contactperson['Contactperson']);
	}
	return $contactpersons;
}

function get_Contacthistory($id = '') {
	global $db;

	$contacthistory_query = $db->query("SELECT id,market,product,DestinationCountry,BMname,ClassGrade,Origin,Application,Marketcompetitors,Generalcomments
									FROM importtemp WHERE id= ".$id."");
	while($contacthistory = $db->fetch_assoc($contacthistory_query)) {

		if(!empty($contacthistory['BMname'])) {
			$querybname = $db->fetch_assoc($db->query("SELECT uid from users where displayName ='".$contacthistory['BMname']."'"));
			if($querybname) {
				$contacthistories = $db->fetch_assoc($db->query("SELECT uid from users where displayName ='".$contacthistory['BMname']."'"));
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
			$activityarea_field .=" ActivitywithOrkila".$i."";
		}
		else {
			$activityarea_field .=", ActivitywithOrkila".$i."";
		}
	}
	$activityarea_query = $db->query("SELECT ".$activityarea_field." FROM importtemp
										 WHERE id= ".$id."");
	while($activityarea = $db->fetch_assoc($activityarea_query)) {
		$activityareas = $activityarea;
	}
	return $activityareas;
}

function get_Mainapplication($id = '') {
	global $db;

	$mainapplication_query = $db->query("SELECT MainApplicationsCovered FROM importtemp
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
		echo '<pre>';

		/* insert supplier details */
		foreach($company['supplierdetails'] as $attr => $supplierdetails) {
			//echo ("SELECT companyName FROM entities WHERE companyName= '".$supplierdetails['companyName']."'");
			$checkcountry = $db->query("SELECT coid FROM countries WHERE name='".$company['supplierdetails']['country']."'");
			if($db->num_rows($checkcountry) > 0) {
				while($country = $db->fetch_assoc($checkcountry)) {
					$supplier_data['country'] = $country['coid'];
				}
			}
			else {
				unset($supplier_data['country']);
			}
			$checksupplier = $db->query("SELECT eid, companyName,companyNameAbbr FROM entities WHERE companyName = '".$company['supplierdetails']['companyName']."' AND type='s'");
			if($db->num_rows($checksupplier) > 0) {
				while($supplier = $db->fetch_assoc($checksupplier)) {
					$supplier_data['foreignid'] = $supplier['eid'];
					$supplier_data['companyabbr'] = $supplier['companyNameAbbr'];
				}
			}
			else {
				unset($supplier_data['foreignid']);
				/* insert here */
			}
		}

		$supplier_data = array('eid' => $supplier_data['foreignid'], 'companyName' => $company['supplierdetails']['companyName'], 'companyNameAbbr' => $supplier_data['companyabbr'], 'type' => $company['supplierdetails']['producerTrader'],
				'country' => $supplier_data['country'],
				'phone1' => $company['supplierdetails']['phone'],
				'phone1' => $company['supplierdetails']['cell'],
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

		$getsupplierid = $db->query("SELECT ssid FROM sourcing_suppliers ");
		while($supplierid = $db->fetch_assoc($getsupplierid)) {
			//$ssid['ssid'][] = $supplierid['ssid'];
		}
		if(!value_exists('sourcing_suppliers', 'eid', $supplier_data['eid'], 'companyName="'.$company['supplierdetails']['companyName'].'"', 'ssid='.$ssid)) {
			$query = $db->insert_query('sourcing_suppliers', $supplier_data);
			$supplier_id['ssid'] = $db->last_id();
		}
		print_r($supplier_id['ssid']);

		/* insert supplier details end */

		/* start mainproducts */
		foreach($company['mainproducts'] as $key => $mainproduct) {
			
			$checkchemical = $db->query("SELECT csid FROM chemicalsubstances WHERE name= '".$mainproduct."'");
			if($db->num_rows($checkchemical) > 0) {
				while($chemical = $db->fetch_assoc($checkchemical)) {
					/* record company founded chmeical */
					$chemical_data['chemicalid'] = $chemical['csid'];
					$chemicalsfound['found'][$compkey] = $company['supplierdetails'];
				}
			}
			else {
				unset($chemical_data['chemicalid']);
			}
			
			/* insert exist csid into  sourcing_suppliers_chemicals */

				$chemical_data = array('ssid' => $ssid['ssid'], 'csid' => $chemical_data['chemicalid']);
				if(!value_exists('sourcing_suppliers_chemicals', 'csid', $chemical_data['csid'], ' ssid='.$supplier_id['ssid'])) {
					$query = $db->insert_query('sourcing_suppliers_chemicals', $chemical_data);
				}
	
			else {
				$datanotfound[$compkey]['chemicalsubstances'][] = $mainproduct;
			}
		}/* end mainproducts */

		/* start activityarea */

		foreach($company['activityarea'] as $key => $activityarea) {
			$checkactivityarea = $db->query("SELECT coid,name FROM countries WHERE name= '".$activityarea."'");
			if($db->num_rows($checkactivityarea) > 0) {
				while($row_activityarea = $db->fetch_assoc($checkactivityarea)) {
					$area_data['countryid'] = $row_activityarea['coid'];
				}
			}
			else {
				unset($area_data['countryid']);
			}

			$area_data = array('ssid' => $supplier_id['ssid'], 'coid' => $area_data['countryid']);
			if(!value_exists('sourcing_suppliers_activityareas', 'coid', $area_data['coid'], ' ssid='.$supplier_id['ssid'])) {
				$query = $db->insert_query('sourcing_suppliers_activityareas', $area_data);
			}
			else {
				$datanotfound[$compkey]['activityarea'][] = $activityarea;
			}
		}/* end activityarea */

		/* start productSegments */
		foreach($company['mainapplicationscovered'] as $key => $productsegments) {
			echo '<pre>';
			$checkproductsegments = $db->query("SELECT psid,title FROM productsegments WHERE title= '".trim($productsegments)."'");
			if($db->num_rows($checkproductsegments) > 0) {
				while($row_productsegments = $db->fetch_assoc($checkproductsegments)) {
					$productsegments_data['productid'] = $row_productsegments['psid'];
					/* record company founded chmeical */
					$productsegmentsfound['area'][$compkey] = $company['activityarea'];
					/* insert sourcing_suppliers_activityareas */
				}
			}
			else {
				unset($productsegments_data['productid']);
			}
			
				$productsegments_data = array('ssid' => $supplier_id['ssid'], 'psid' => $productsegments_data['productid']);
				if(!value_exists('sourcing_suppliers_productsegments', 'psid', $productsegments_data['psid'], ' ssid='.$supplier_id['ssid'])) {
					$query = $db->insert_query('sourcing_suppliers_productsegments', $productsegments_data);
				} /* if no supplier id exist in the database */
	
			else {
				$datanotfound[$compkey]['productsegment'][] = $productsegments;
			}
		}/* end productSegments */


		/* start contactperson */
		foreach($company['contactperson'] as $contactperson) {
			echo '<pre>';  //echo ("SELECT rpid,name FROM representatives WHERE name= '".trim($contactperson['name'])."'");
			$checkcontactperson = $db->query("SELECT rpid,name FROM representatives WHERE name= '".trim($contactperson)."'");
			if($db->num_rows($checkcontactperson) > 0) {
				while($row_contactperson = $db->fetch_assoc($checkcontactperson)) {
					/* record representatives founded chmeical */
					$contactperson_data['repid'] = $row_contactperson['rpid'];
					$contactpersonfound['person'][$compkey] = $company['contactperson'];
					/* insert sourcing_suppliers_contactpersons */
				}
			}
			else {
				unset($contactperson_data['repid']);
			}
		
				$contactperson_data = array('ssid' => $supplier_id['ssid'], 'rpid' => $contactperson_data['repid']);
				if(!value_exists('sourcing_suppliers_contactpersons', 'rpid', $contactperson_data['rpid'], ' ssid='.$supplier_id['ssid'])) {
					$query = $db->insert_query('sourcing_suppliers_contactpersons', $contactperson_data);
				} /* if no contact id exist in the database */
	
			else {
				$datanotfound[$compkey]['contactperson'][] = $contactperson;
			}
		}/* end contactperson */

		/* start contacthistory */
		if(isset($company['contacthistory'])) {
			foreach($company['contacthistory'] as $contact) {
				//$contacthistory_data['affid'] = $company['contacthistory']['affid'];
				//$contacthistory_data['uid'] = $company['contacthistory']['uid'];
				foreach($company['contacthistory']['contact'] as $contactdetails) {
					$checkproductsegments = $db->query("SELECT psid,title FROM productsegments WHERE title= '".trim($company['contacthistory']['contact']['market'])."'");
					if($db->num_rows($checkproductsegments) > 0) {
						while($row_productsegments = $db->fetch_assoc($checkproductsegments)) {
							$contacthistory_data['market'] = $row_productsegments['psid'];
						}
					}
					else {
						unset($contacthistory_data['market']);
					}
					$checkchemical = $db->query("SELECT csid FROM chemicalsubstances WHERE name= '".$company['contacthistory']['contact']['product']."'");
					if($db->num_rows($checkchemical) > 0) {
						while($chemical = $db->fetch_assoc($checkchemical)) {
							$contacthistory_data['chemical'] = $chemical['csid']; //chemical id must reset
						}
					}
					else {
						unset($contacthistory_data['chemical']);
					}
				}
			}
			$contacthistory_data = array('ssid' => $supplier_id['ssid'], 'affid' => $company['contacthistory']['affid'], 'uid' => $company['contacthistory']['uid'], 'market' => $contacthistory_data['market'], 'chemical' => $contacthistory_data['chemical'], 'description' => $company['contacthistory']['contact']['Generalcomments'], 'grade' => $company['contacthistory']['contact']['ClassGrade'], 'competitors' => $company['contacthistory']['contact']['Marketcompetitors'],
					'application' => $company['contacthistory']['contact']['Application'], 'date' => TIME_NOW);
			$query = $db->insert_query('sourcing_suppliers_contacthist', $contacthistory_data);
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

		<form  name="perform_sourcing/importtemp_Form" action="index.php?module=sourcing/importtemp" method="post"  id="perform_sourcing/importtemp_Form">

			<div align="center"></div>
			<input class="rounded" type="hidden" value="do_import" name="action" id="action" />
			<input type="submit" class="button" value="import" id="perform_sourcing/importtemp_Button" />

		</form>
	</body>
</html>