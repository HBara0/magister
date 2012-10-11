<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Create Survey
 * $module: hr
 * $id: listjob.php	
 * Created By: 		@tony.assaad		Augusst 10, 2012 | 12:30 PM
 * Last Update: 	@tony.assaad		October 10, 2012 | 4:13 PM
 */


if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['sourcing_canListSuppliers'] == 0) {
	error($lang->sectionnopermission);
	exit;
	}
if(!$core->input['action']) {	
	$vacancy_id = $db->escape_string($core->input['id']);
	if(!$core->input['action']) {
			$sort_url = sort_url();
			$sourcing = new Sourcing();
			$potential_suppliers = $sourcing->get_potential_supplier();
		if(is_array($potential_suppliers)) {
			foreach($potential_suppliers as $potential_supplier) {
				if($core->usergroup['sourcing_canManageEntries'] == 1) {
					$edit = '<a href="'.DOMAIN.'index.php?module=sourcing/managesupplier&type=edit&id='.$potential_supplier['ssid'].'"><img src="././images/icons/edit.gif" border="0"/></a>';
				}
				$rowclass = alt_row($rowclass);
				
				eval("\$sourcing_listpotentialsupplier_rows.= \"".$template->get('sourcing_listpotentialsupplier_rows')."\";");
			}
			
			$multipage_where .= $db->escape_string($attributes_filter_options['prefixes'][$core->input['filterby']].$core->input['filterby']).$filter_value;
			$multipages = new Multipages('sourcing_suppliers ss', $core->settings['itemsperlist'], $multipage_where);
			$hr_listjobsapplicants_rows .= "<tr><td colspan='6'>".$multipages->parse_multipages()."</td></tr>";
		}		
		else
		{
			$hr_listjobsapplicants_rows .= '<tr><td colspan="5">'.$lang->na.'</td></tr>';
		}
	}
	eval("\$listpotentialsupplier = \"".$template->get('sourcing_listpotentialsupplier')."\";");
	output_page($listpotentialsupplier);

}
	


?>