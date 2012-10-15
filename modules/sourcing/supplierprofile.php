<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright � 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Potential Supplier Profile
 * $module: Sourcing
 * $id: supplierprofile.php	
 * Last Update: @zaher.reda 	February 15, 2012 | 10:05 AM
 */
if(!defined('DIRECT_ACCESS'))
{
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['sourcing_canListSuppliers'] == 0 ) {
	error($lang->sectionnopermission);
	exit;
}
/* if no permission person should only see suppliers who work in the same segements he/she is working in --START*/		
			if($core->usergroup['sourcing_canManageEntries'] == 0) { 
				$user_suppliers_id = implode(',',$core->user['suppliers']['eid']);
				$join_employeessegments = "JOIN ".Tprefix."employeessegments es on es.psid = ssp.psid and es.uid=".$core->user['uid']."";
					}
			/* person should only see suppliers who work in the same segements he/she is working in --END*/
				else
				{		
					/*Return All  potentials suppliers--START*/
					$join_employeessegments = '';
					/*Return All  potentials suppliers --END*/
				}	
if(!$core->input['action']) {
	echo 'sdsssssssssfsd';

}
?>