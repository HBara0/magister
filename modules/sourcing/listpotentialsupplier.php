<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * List Potential Supplier
 * $module: Sourcing
 * $id:  Listpotentialaupplier.php	
 * Created By: 		@tony.assaad		October 10, 2012 | 12:30 PM
 * Last Update: 	@tony.assaad		October 25, 2012 | 4:13 PM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['sourcing_canListSuppliers'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {
	if(!$core->input['action']) {
		$criteriaandstars = $rating_section = '';
		$maxstars = 5;
		$readonlyratings = true;
		$sort_url = sort_url();
		
		$opportunity_scale = range(0, 5);
		array_unshift($opportunity_scale, ''); /* Prepend empty elements to the beginning */
		
		/* Perform inline filtering - START */
		$filters_config = array(
				'parse' => array('filters' => array('companyName', 'type', 'segment', 'country', 'opportunity'),
						'overwriteField' => array('opportunity' => parse_selectlist('filters[opportunity]', 5, array_combine($opportunity_scale, $opportunity_scale), $core->input['filters']['opportunity']))
				/* get the busieness potential and parse them in select list to pass to the filter array */
				),
				'process' => array(
						'filterKey' => 'ssid',
						'mainTable' => array(
								'name' => 'sourcing_suppliers',
								'filters' => array('companyName' => 'companyName', 'type' => 'type', 'opportunity' => 'businessPotential'),
						),
						'secTables' => array(
								'sourcing_suppliers_productsegments' => array(
										'filters' => array('segment' => array('operatorType' => 'multiple', 'name' => 'psid')),
								),
								'sourcing_suppliers_activityareas' => array(
										'filters' => array('country' => 'name'),
										'keyAttr' => 'coid',
										'joinKeyAttr' => 'coid',
										'joinWith' => 'countries'
								)
						)
				)
		);

		$filter = new Inlinefilters($filters_config);
		$filter_where_values = $filter->process_multi_filters();

		$filters_row_display = 'hide';
		if(is_array($filter_where_values)) {
			$filters_row_display = 'show';
			$filter_where = 'ss.'.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
			$multipage_where .= $filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
		}

		$filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
		/* Perform inline filtering - END */

		$sourcing = new Sourcing();
		$potential_suppliers = $sourcing->get_all_potential_suppliers($filter_where);  /* this function return array with all associated sgements and activity area of the supplier */
		if(is_array($potential_suppliers)) {
			foreach($potential_suppliers as $key => $potential_supplier) {
				if($core->usergroup['sourcing_canManageEntries'] == 1) {
					$readonlyratings = false;
					$edit = '<a href="'.DOMAIN.'index.php?module=sourcing/managesupplier&type=edit&id='.$potential_supplier['supplier']['ssid'].'"><img src="././images/icons/edit.gif" border="0"/></a>';
				}
				$segments_counter = 0;
				$segments = $hidden_segments = $show_segments = '';
				if(is_array($potential_supplier['segments'])) {
					foreach($potential_supplier['segments'] as $psid=> $segments) {
						if(++$segments_counter > 2) {
							$hidden_segments .= $segments.'<br />';
						}
						elseif($segments_counter == 2)
						{
							$show_segments .= $segments;
						}
						else
						{
							$show_segments .= $segments.'<br />';
						}

						if($segments_counter > 2) {
							$segments = $show_segments.", <a href='#segment' id='showmore_segments_{$key}'>...</a><br /> <span style='display:none;' id='segments_{$key}'>{$hidden_segments}</span>";
						}
						else
						{
							$segments = $show_segments;
						}
					}
					}
				if(is_array($potential_supplier['activityarea'])) {
					foreach($potential_supplier['activityarea'] as $area) {
						$potential_supplier['activityarea'] = implode(',', $area);
					}
				}

				
				$rowclass = alt_row($rowclass);
				$criteriaandstars = '<div class="evaluation_criterium" name="'.$potential_supplier['supplier']['ssid'].'">';
				$criteriaandstars .= '<div class="ratebar" style="width:40%; display:inline-block;">';

				if($readonlyratings) {
					$criteriaandstars .= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-value="'.$potential_supplier['supplier']['businessPotential'].'"></div>';
				}
				else {
					$criteriaandstars .= '<input type="range" min="0" max="'.$maxstars.'" value="'.$potential_supplier['supplier']['businessPotential'].'" step="1" id="rating_'.$potential_supplier['supplier']['ssid'].'" class="ratingscale">';
					$criteriaandstars .= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-resetable="false" data-rateit-backingfld="#rating_'.$potential_supplier['supplier']['ssid'].'" data-rateit-value="'.$potential_supplier['supplier']['businessPotential'].'"></div>';
				}
				$criteriaandstars .= '</div></div>';

				if($potential_supplier['supplier']['isBlacklisted'] == 1) {
					$criteriaandstars = '<img  title ="blackListed" src="././images/icons/notemark.gif" border="0"/>';
				}

				if(!$readonlyratings) {
					$header_ratingjs = '$(".rateit").live("click",function() {
						if(sharedFunctions.checkSession() == false) {
							return;
						}				
						ssid=$(this).parent().parent().attr("name");
						rateid =$("#rating_"+ssid).val();				
						sharedFunctions.requestAjax("post", "index.php?module=sourcing/listpotentialsupplier&action=do_ratepotential","value="+rateid+"&ssid="+ssid, "html");
						});';
				}
				else {
					$header_ratingjs = '';
				}
				$rating_section = '<div>'.$criteriaandstars.'</div>';


				

				eval("\$sourcing_listpotentialsupplier_rows.= \"".$template->get('sourcing_listpotentialsupplier_rows')."\";");
			} /* foreach loop END */

			$multipage_where .= $db->escape_string($attributes_filter_options['prefixes'][$core->input['filterby']].$core->input['filterby']).$filter_value;
			$multipages = new Multipages('sourcing_suppliers', $core->settings['itemsperlist'], $multipage_where);
			$sourcing_listpotentialsupplier_rows .= "<tr><td colspan='6'>".$multipages->parse_multipages()."</td></tr>";
		}
		else {
			$sourcing_listpotentialsupplier_rows .= '<tr><td colspan="5">'.$lang->na.'</td></tr>';
		}
	}
	eval("\$listpotentialsupplier = \"".$template->get('sourcing_listpotentialsupplier')."\";");
	output_page($listpotentialsupplier);
}
elseif($core->input['action'] == 'do_ratepotential') {
	$sourcing['businessPotential'] = $db->escape_string($core->sanitize_inputs($core->input['value'], array('removetags' => true)));

	$db->update_query('sourcing_suppliers', array('businessPotential' => $sourcing['businessPotential']), 'ssid="'.intval($core->input['ssid']).'"');
}
?>