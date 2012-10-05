<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Add entities
 * $module: contents
 * $id: addentities.php	
 * Last Update: @zaher.reda 	February 15, 2012 | 10:05 AM
 */
if(!defined('DIRECT_ACCESS'))
{
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['canAddSuppliers'] == 0 && $core->usergroup['canAddCustomers'] == 0) {
	//error($lang->sectionnopermission);
	//exit;
}

$lang->load('contents_addentities');
if(!$core->input['action']) {

	eval("\$sourcingmanagesupplier = \"".$template->get('sourcing_managesupplier')."\";");
	output_page($sourcingmanagesupplier);
}
else 
{
	if($core->input['action'] == 'do_perform_addentities') {

	}



}
?>