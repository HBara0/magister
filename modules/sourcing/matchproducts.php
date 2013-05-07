<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: matchproducts.php
 * Created:        @tony.assaad    May 3, 2013 | 10:41:15 AM
 * Last Update:    @tony.assaad    May 6, 2013 | 10:41:15 AM
 */
function get_importtemp_data() {
	global $db;

	$tempquery = $db->query("SELECT id,REPLACE(companyName,\"'\",\"\") AS companyName FROM importtemp 
		WHERE  companyName is not null AND companyNAme <>''");
	while($tempdata = $db->fetch_assoc($tempquery)) {
		$importtemp[$tempdata['id']] = get_mainproducts($tempdata['id']);
	}
	return $importtemp;
}

function get_mainproducts($id = '') {
	global $db;
	if(!empty($id)) {

		for($i = 1; $i <= 30; $i++) {
			if($i == 1) {
				$mainproduct_field .=" Mainproducts".$i."";
			}
			else {
				$mainproduct_field .=", Mainproducts".$i."";
			}
		}
		$mainproductsquery = $db->query("SELECT  ".$mainproduct_field." FROM importtemp WHERE  id= ".$id." ");
		while($mainproduct = $db->fetch_assoc($mainproductsquery)) {
			$mainproducts = $mainproduct;
		}
		return $mainproducts;
	}
}

function matchWithChemical($chemical_id, $product) {
	global $db;

	$checkmatch_query = $db->query("SElECT casNum,name  AS matchchemical,csid FROM chemicalsubstances
									WHERE SOUNDEX(name)= SOUNDEX('".$product."') OR name like '%".$product."%' OR name SOUNDS LIKE'%".$product."%' ");

	if($db->num_rows($checkmatch_query) > 0) {
		//$matchproduct .='<td><select id=matchwith['.$match['csid'].'] name= matchwith['.$match['csid'].'] size="1" tabindex="2">';
		$matchproduct .= '<option value="0">&nbsp;</option>';
		while($match = $db->fetch_assoc($checkmatch_query)) {
			$matchproduct = '';
			$matchproduct .='<td> <select id=matchwith['.$match['csid'].'] name= matchwith['.$match['csid'].'] size="1" tabindex="2">';
			$option_selected = 'selected="selected"';
			$matchproduct .= '<option value="'.$match['csid'].'"'.$option_selected.'>'.$match['matchchemical'].'</option>';
		}
		$matchproduct .= '</select></td>';
	}
	else {
		$matchproduct = '<td><input id="chemicalproducts_'.$chemical_id.'_QSearch" value="" autocomplete="off" size="40px" type="text">
						<input id="chemicalproducts_'.$chemical_id.'_id" name="matchwith['.$match['csid'].']" value="" type="hidden">
						<div id="searchQuickResults_chemicalproducts_'.$chemical_id.'" class="searchQuickResults" style="display:none;"></div></td>';
	}
	return $matchproduct;
}

if(!$core->input['action']) {

	$mainproducts = get_importtemp_data();
	foreach($mainproducts as $companyid => $products) {

		foreach($products as $product) {
			$matchproduct.='<tr>';
			if(isset($product) && !empty($product)) {
				$matchproduct.='<td>#'.$companyid.'<select id=matchproduct['.$companyid.'] name= matchproduct['.$companyid.'] size="1" tabindex="1">';
				$matchproduct.='<option value="'.$product.'"'.$option_selected.'>'.$product.'</option>';
				//$matchproduct.='<td>#'.$companyid.'   '.parse_selectlist('matchproduct['.$companyid.']', 1, $products, 1).'</td>';
				$matchproduct.='</select>';
				$matchproduct.='<td>&lt;-&gt;</td>';
				$matchproduct.= matchWithChemical(rand(1, 100), $product);
			}
		}
		$matchproduct.='</tr>';
	}

	eval("\$matchproductsstage = \"".$template->get('sourcing_matchproductsstage')."\";");
	output_page($matchproductsstage);
}
elseif($core->input['action'] == 'do_match') {
	
	foreach( $core->input['matchproduct'] as $chem){
		print_r($chem); echo'<br>';
	}
	
}
?>






