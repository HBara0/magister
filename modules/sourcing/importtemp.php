<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 * Import Temp
 *  Import Temp
 *  $module: Sourcing
 * $id: importtemp.php	
 * Created By: 		@tony.assaad		October 31, 2012 | 12:30 PM
 * Last Update: 	@tony.assaad		October 31, 2012 | 4:13 PM
 */




/* load temp data*/
	
function get_importtemp_data(){
	global $db;
	
	$tempquery = $db->query("SELECT id, companyName,country,producerTrader,Email,phone,cell,website,MainApplicationsCovered FROM importtemp");
	while ($tempdata = $db->fetch_assoc($tempquery)) {
		$importtemp[$tempdata['id']]['Mainproducts'] = array();
		$importtemp[$tempdata['id']] = $tempdata;
		$importtemp[$tempdata['id']]['Mainproducts'] = get_mainproducts($tempdata['id']);
		
		}
		return $importtemp;
	}
	
	function get_mainproducts($id='') {
		global $db;
		for($i=1;$i<=30;$i++){
			$mainproduct_field .=", Mainproducts".$i."";
		}
		$mainproductsquery = $db->query("SELECT id ".$mainproduct_field." FROM importtemp");
		while ($mainproduct = $db->fetch_assoc($mainproductsquery)) {
			$mainproducts[$mainproduct['id']] =  $mainproduct;
		}
		return $mainproducts;
	}
	
$alldata = get_importtemp_data();
$allmainproducts = get_mainproducts();
print_R($alldata);
?>
