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
			$criteriaandstars = '';
			$maxstars = 5;
			$rating_section = '';
			$readonlyratings = true;
			foreach($potential_suppliers as $potential_supplier) {
				if($core->usergroup['sourcing_canManageEntries'] == 1) {
					$readonlyratings = false;
					$edit = '<a href="'.DOMAIN.'index.php?module=sourcing/managesupplier&type=edit&id='.$potential_supplier['ssid'].'"><img src="././images/icons/edit.gif" border="0"/></a>';
				}
					$criteriaandstars  = '<div class="evaluation_criterium" name="'.$potential_supplier['ssid'].'">';
					$criteriaandstars .= '<div class="ratebar" style="width:40%; display:inline-block;">';

					if($readonlyratings) {
						$criteriaandstars .= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-value="'.$potential_supplier['businessPotential'].'"></div>';
					}
					else
					{
						$criteriaandstars .= '<input type="range" min="0" max="'.$maxstars.'" value="'.$potential_supplier['businessPotential'].'" step="1" id="rating_'.$potential_supplier['ssid'].'" class="ratingscale">';
						$criteriaandstars .= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-resetable="false" data-rateit-backingfld="#rating_'.$potential_supplier['ssid'].'" data-rateit-value="'.$potential_supplier['businessPotential'].'"></div>';
					}
					$criteriaandstars .= '</div></div>';
	
					if(!$readonlyratings) {
						$header_ratingjs = '$(".rateit").live("click",function() {
						if(sharedFunctions.checkSession() == false) {
							return;
						}
	
						var returndiv = "";
						targetid = 3;
						ssid=64;
						sharedFunctions.requestAjax("post", "index.php?module=sourcing/listpotentialsupplier&action=do_rateentity","value="+targetid+"&ssid="+ssid, "html");
						});';
					}
					else
					{
					   $header_ratingjs = '';
					}
					$rating_section = '<div>'.$criteriaandstars.'</div><hr>';
			
					$rowclass = alt_row($rowclass);
		
				eval("\$sourcing_listpotentialsupplier_rows.= \"".$template->get('sourcing_listpotentialsupplier_rows')."\";");
			}
			
			$multipage_where .= $db->escape_string($attributes_filter_options['prefixes'][$core->input['filterby']].$core->input['filterby']).$filter_value;
			$multipages = new Multipages('sourcing_suppliers ss', $core->settings['itemsperlist'], $multipage_where);
			$sourcing_listpotentialsupplier_rows .= "<tr><td colspan='6'>".$multipages->parse_multipages()."</td></tr>";
		}		
		else
		{
			$sourcing_listpotentialsupplier_rows .= '<tr><td colspan="5">'.$lang->na.'</td></tr>';
		}
	}
	eval("\$listpotentialsupplier = \"".$template->get('sourcing_listpotentialsupplier')."\";");
	output_page($listpotentialsupplier);

}

 	elseif($core->input['action'] == 'do_rateentity')
	{
		
		$sourcing['businessPotential'] = $db->escape_string($core->sanitize_inputs($core->input['value'], array('removetags' => true)));
		$active_rating =  $db->escape_string($core->input['ssid']);
		echo $active_rating;
		$db->update_query('sourcing_suppliers',array('businessPotential' => $sourcing['businessPotential']), 'ssid="'.$active_rating.'"');
	}
	


?>