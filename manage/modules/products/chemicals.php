<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: chemical.php
 * Created:        @tony.assaad    Dec 13, 2013 | 2:42:32 PM
 * Last Update:    @tony.assaad    Dec 13, 2013 | 2:42:32 PM
 */


if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canManageapllicationsProducts'] == 0) {
	//error($lang->sectionnopermission);
	//exit;
}
$lang->load('products_chemicals');
if(!$core->input['action']) {
	/* Chemical List - START */
	$chemsubstances_objs = Chemicalsubstances::get_chemicalsubstances();
	$chemicalslist_section = '';
	if(is_array($chemsubstances_objs)) {
		foreach($chemsubstances_objs as $chemsubstances_obj) {
			$rowclass = alt_row($rowclass);
			$chemical = $chemsubstances_obj->get();
			$chemicalslist_section .= '<tr class="'.$rowclass.'" style="vertical-align:top;"><td width="33%">'.$chemical['casNum'].'</td><td align="left" width="33%">'.$chemical['name'].'</td><td width="33%">'.$chemical['synonyms'].'</td></tr>';
		}
	}
	else {
		$chemicalslist_section = '<tr><td colspan="2">'.$lang->na.'</td></tr>';
	}
	/* Chemical List - END */
	eval("\$chemicalsubstances = \"".$template->get("admin_products_chemicalsubstances")."\";");
	output_page($chemicalsubstances);
}
?>
