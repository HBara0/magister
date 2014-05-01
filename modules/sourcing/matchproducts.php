<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: matchproducts.php
 * Created:        @tony.assaad    May 3, 2013 | 10:41:15 AM
 * Last Update:    @tony.assaad    May 6, 2013 | 10:41:15 AM
 */

set_time_limit(0);
ini_set('memory_limit', '200M');

if($core->usergroup['sourcing_canManageEntries'] == 0) {
    error($lang->sectionnopermission);
    exit;
}
if(!$core->input['action']) {
    $rowid = 1;
    $mainproducts = get_importtemp_data($core->input['fromrow']);

    foreach($mainproducts as $companyid => $products) {
        foreach($products as $product) {
            if(empty($product)) {
                continue;
            }

            $matchproduct.='<tr>';
            if(isset($product) && !empty($product)) {
                $matchproduct.='<td><input type="hidden" id="matchproduct['.$rowid.']" name=" matchproduct['.$rowid.']" size="1" tabindex="1"  value="'.$product.'">'.$product;
                $matchproduct.='<td>&lt;-&gt;</td>';
                $matchproduct.= matchWithChemical($rowid, $product);
            }
            $rowid++;
        }

        $matchproduct.='</tr>';
    }

    eval("\$matchproductsstage = \"".$template->get('sourcing_matchproductsstage')."\";");
    output_page($matchproductsstage);
}
elseif($core->input['action'] == 'do_match') {
    if(is_array($core->input['matchproduct'])) {
        foreach($core->input['matchproduct'] as $id => $mainproduct) {
            //swipe the product name
            if(isset($core->input['matchwith'][$id]) && !empty($core->input['matchwith'][$id])) {
//				echo '<b>mainproduct : </b> '.($mainproduct).' match --->>>>'.$core->input['matchwith'][$id].' <br> ';
//				echo'<br>';
                for($i = 1; $i <= 30; $i++) {
                    $db->update_query("importtemp", array('Mainproducts'.$i => $core->input['matchwith'][$id]), 'Mainproducts'.$i.''.'="'.$db->escape_string($core->input['matchproduct'][$id]).'"');
                }
            }
        }
    }
    output_xml("<status>true</status><message>Successfully Saved</message>");
}
function get_importtemp_data($limit = 0) {
    global $db;

    $tempquery = $db->query("SELECT id,REPLACE(companyName,\"'\",\"\") AS companyName FROM importtemp 
		WHERE companyName is not null AND companyNAme != '' LIMIT ".$limit.", 20");
    while($tempdata = $db->fetch_assoc($tempquery)) {
        $importtemp[$tempdata['id']] = get_mainproducts($tempdata['id']);
    }
    return $importtemp;
}

function get_mainproducts($id = '') {
    global $db;
    if(!empty($id)) {

        for($i = 1; $i <= 3; $i++) {
            if($i == 1) {
                $mainproduct_field .=" Mainproducts".$i."";
            }
            else {
                $mainproduct_field .=",Mainproducts".$i."";
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

    $checkmatch_query = $db->query("SELECT casNum, name AS matchchemical,csid FROM chemicalsubstances
									WHERE SOUNDEX(name)= SOUNDEX('".$db->escape_string($product)."') OR name like '%".$db->escape_string($product)."%' OR name SOUNDS LIKE'%".$db->escape_string($product)."%' ");

    if($db->num_rows($checkmatch_query) > 0) {
        //$matchproduct .='<td><select id=matchwith['.$match['csid'].'] name= matchwith['.$match['csid'].'] size="1" tabindex="2">';
        $matchproduct = '';
        $matchproduct .= '<td><b><select id="matchwith['.$chemical_id.']" name= "matchwith['.$chemical_id.']" size="1" tabindex="2">';
        $matchproduct .= '<option value=""></option>';
        while($match = $db->fetch_assoc($checkmatch_query)) {
            //$option_selected = 'selected="selected"';

            $matchproduct .= '<option value="'.$match['matchchemical'].'"'.$option_selected.'>'.$match['matchchemical'].'</option>';
        }
        $matchproduct .= '</select></td>';
    }
    else {
        $matchproduct = '<td><input id="chemicalproducts_'.$chemical_id.'_QSearch" value="" autocomplete="off" size="40px" type="text" name="matchwith['.$chemical_id.']">
						<input id="chemicalproducts_'.$chemical_id.'_id"  value="" type="hidden">
						<div id="searchQuickResults_chemicalproducts_'.$chemical_id.'" class="searchQuickResults" style="display:none;"></div></td>';
    }

    return $matchproduct;
}

?>