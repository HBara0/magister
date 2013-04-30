<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * List Potential Supplier
 * $module: Sourcing
 * $id: listpotentialaupplier.php	
 * Created By: 		@tony.assaad		October 10, 2012 | 12:30 PM
 * Last Update: 	@tony.assaad		December 3, 2012 | 04:51 PM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['sourcing_canListSuppliers'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {
//	if(!$core->input['action']) {
	$criteriaandstars = $rating_section = '';
	if($core->usergroup['sourcing_canManageEntries'] == 1) {
		$readonlyratings = false;
		$header_ratingjs = '$(".rateit").live("click",function() {
				if(sharedFunctions.checkSession() == false) {
					return;
				}				
				ssid= $(this).parent().parent().attr("name");
				rateid = $("#rating_"+ssid).val();				
				sharedFunctions.requestAjax("post", "index.php?module=sourcing/listpotentialsupplier&action=do_ratepotential", "value="+rateid+"&ssid="+ssid, "html");
			});';
	}
	else {
		$readonlyratings = true;
		$header_ratingjs = '';
	}


	$maxstars = 5;
	$sort_url = sort_url();

	$opportunity_scale = range(0, 5);
	array_unshift($opportunity_scale, ''); /* Prepend empty elements to the beginning */

	/* Perform inline filtering - START */
	$filters_config = array(
			'parse' => array('filters' => array('companyName', 'type', 'segment', 'country', 'opportunity', 'chemicalsubstance', 'genericproducts'),
					'overwriteField' => array('opportunity' => parse_selectlist('filters[opportunity]', 5, array_combine($opportunity_scale, $opportunity_scale), $core->input['filters']['opportunity']),
							'genericproducts' => parse_selectlist('filters[genericproducts][]', 1, get_specificdata('genericproducts', array('gpid', 'title'), 'gpid', 'title', '', 0), $core->input['filters']['genericproducts'], 0, '', ''),
							'type' => parse_selectlist('filters[type]', 2, array('' => '', 'b' => $lang->both, 't' => $lang->trader, 'p' => $lang->producer), $core->input['filters']['type']))
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
							'sourcing_suppliers_chemicals' => array(
									'havingFilters' => array('chemicalsubstance' => 'fullchemicalname'),
									'keyAttr' => 'csid',
									'joinKeyAttr' => 'csid',
									'joinWith' => 'chemicalsubstances',
									'extraSelect' => 'CONCAT(casNum,"-",name,"-",synonyms) AS fullchemicalname'
							),
							'sourcing_suppliers_activityareas' => array(
									'filters' => array('country' => 'name'),
									'keyAttr' => 'coid',
									'joinKeyAttr' => 'coid',
									'joinWith' => 'countries'
							),
							'sourcing_suppliers_genericprod' => array(
									'filters' => array('operatorType' => 'multiple', 'title' => 'gpid'),
									'keyAttr' => 'gpid',
									'joinKeyAttr' => 'gpid',
									'joinWith' => 'genericproducts'
							)
					)
			)
	);

	$filter = new Inlinefilters($filters_config);
	$filter_where_values = $filter->process_multi_filters();

	$chemicals_query = $db->query("SELECT csid, casNum, name FROM ".Tprefix."chemicalsubstances ORDER BY name ASC");
	while($chemicals = $db->fetch_assoc($chemicals_query)) {
		$chemicals_selectlist_otps .= '<option value='.$chemicals['csid'].'>'.$chemicals['casNum'].' - '.$chemicals['name'].'</option>';
	}
	$db->free_result($chemicals_query);

	$filters_row_display = 'hide';
	if(is_array($filter_where_values)) {
		$filters_row_display = 'show';
		$filter_where = 'ss.'.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
		$multipage_where .= $filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
	}

	$filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display), array('chemicalsubstance'));
	/* Perform inline filtering - END */

	$sourcing = new Sourcing();
	$potential_suppliers = $sourcing->get_all_potential_suppliers($filter_where);  /* this function return array with all associated sgements and activity area of the supplier */

	if(is_array($potential_suppliers)) {
		foreach($potential_suppliers as $key => $potential_supplier) {
			if(!empty($potential_supplier['supplier']['companyNameAbbr'])) {
				$potential_supplier['supplier']['companyName'] .= ' ('.$potential_supplier['supplier']['companyNameAbbr'].')';
			}
			/* Check if supplier is blacklisted - if yes and user is sourcing agent, display it, else skip */
			if($potential_supplier['supplier']['isBlacklisted'] == 1 && $core->usergroup['sourcing_canManageEntries'] == 1) {
				$rating_section = '<img title="blackListed" src="./images/icons/notemark.gif" border="0" />';
			}
			elseif($potential_supplier['supplier']['isBlacklisted'] == 1 && $core->usergroup['sourcing_canManageEntries'] == 0) {
				continue;
			}

			$edit_link = '';
			if($core->usergroup['sourcing_canManageEntries'] == 1) {
				$edit_link = '<a href="'.DOMAIN.'/index.php?module=sourcing/managesupplier&amp;type=edit&amp;id='.$potential_supplier['supplier']['ssid'].'"><img src="./images/icons/edit.gif" border="0"/></a>';
			}

			/* Parse segements column - START */
			$potential_supplier['segments_output'] = '';
			if(is_array($potential_supplier['segments'])) {
				$potential_supplier['segments_imploded'] = implode('<br />', $potential_supplier['segments']);
				reset($potential_supplier['segments']);
				$first_segmentlen = strlen(current($potential_supplier['segments']));
				if(strlen($potential_supplier['segments_imploded']) > $first_segmentlen) {
					$potential_supplier['segments_output'] = substr($potential_supplier['segments_imploded'], 0, $first_segmentlen).' <a href="#segment_'.$potential_supplier['supplier']['ssid'].'" id="showmore_segments_'.$potential_supplier['supplier']['ssid'].'">...</a><span style="display:none;" id="segments_'.$potential_supplier['supplier']['ssid'].'">'.substr($potential_supplier['segments_imploded'], $first_segmentlen).'</span>';
				}
				else {
					$potential_supplier['segments_output'] = current($potential_supplier['segments']);
				}
				unset($potential_supplier['segments_imploded']);
			}
			/* Parse segements column - END */

			/* Parse activity area column - START */
			$potential_supplier['activityarea_output'] = '';
			if(is_array($potential_supplier['activityarea'])) {
				foreach($potential_supplier['activityarea'] as $info) {
					$potential_supplier['activityarea_merged'][] = $info['country'].' - '.$info['affiliate'];
				}
				$potential_supplier['activityarea'] = $potential_supplier['activityarea_merged'];
				unset($potential_supplier['activityarea_merged']);

				if(is_array($potential_supplier['activityarea'])) {
					$potential_supplier['activityarea_imploded'] = implode('<br />', $potential_supplier['activityarea']);

					reset($potential_supplier['activityarea']);
					$first_activityarealen = strlen(current($potential_supplier['activityarea']));

					if(strlen($potential_supplier['activityarea_imploded']) > $first_segmentlen) {
						$potential_supplier['activityarea_output'] = substr($potential_supplier['activityarea_imploded'], 0, $first_activityarealen).' <a href="#activityarea_'.$potential_supplier['supplier']['ssid'].'" id="showmore_activityarea_'.$potential_supplier['supplier']['ssid'].'">...</a><span style="display:none;" id="activityarea_'.$potential_supplier['supplier']['ssid'].'">'.substr($potential_supplier['activityarea_imploded'], $first_activityarealen).'</span>';
					}
					else {
						$potential_supplier['activityarea_output'] = current($potential_supplier['activityarea']);
					}
				}
				unset($potential_supplier['activityarea_imploded']);
			}
			/* Parse activity area column - END */

			$rowclass = alt_row($rowclass);
			/* Parse rating section - START */
			if($potential_supplier['supplier']['isBlacklisted'] == 0) {
				$criteriaandstars = '<div class="evaluation_criterium" name="'.$potential_supplier['supplier']['ssid'].'">';
				$criteriaandstars .= '<div class="ratebar" style="width:40%; display:inline-block;">';

				if($readonlyratings == true) {
					$criteriaandstars .= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-readonly="true" data-rateit-value="'.$potential_supplier['supplier']['businessPotential'].'"></div>';
				}
				else {
					$criteriaandstars .= '<input type="range" min="0" max="'.$maxstars.'" value="'.$potential_supplier['supplier']['businessPotential'].'" step="1" id="rating_'.$potential_supplier['supplier']['ssid'].'" class="ratingscale">';
					$criteriaandstars .= '<div class="rateit" data-rateit-starwidth="18" data-rateit-starheight="16" data-rateit-ispreset="true" data-rateit-resetable="false" data-rateit-backingfld="#rating_'.$potential_supplier['supplier']['ssid'].'" data-rateit-value="'.$potential_supplier['supplier']['businessPotential'].'"></div>';
				}
				$criteriaandstars .= '</div></div>';

				$rating_section = '<div>'.$criteriaandstars.'</div>';
			}
			/* Parse rating section - END */

			eval("\$sourcing_listpotentialsupplier_rows .= \"".$template->get('sourcing_listpotentialsuppliers_row')."\";");
		} /* foreach loop END */

		$multipage_where .= $db->escape_string($attributes_filter_options['prefixes'][$core->input['filterby']].$core->input['filterby']).$filter_value;
		$multipages = new Multipages('sourcing_suppliers', $core->settings['itemsperlist'], $multipage_where);
		$sourcing_listpotentialsupplier_rows .= '<tr><td colspan="6">'.$multipages->parse_multipages().'</td></tr>';

		unset($potential_supplier);
	}
	else {
		$sourcing_listpotentialsupplier_rows .= '<tr><td colspan="5"><a href="#" id="showpopup_requestchemical" class="showpopup"><img alt="'.$lang->requestchemical.'" src="./images/addnew.png" border="0" /> '.$lang->requestchemical.'</a></td></tr>';
	}
	//} 

	eval("\$listpotentialsupplier = \"".$template->get('sourcing_listpotentialsuppliers')."\";");
	output_page($listpotentialsupplier);
}
else {
	if($core->input['action'] == 'do_ratepotential') {
		if($core->usergroup['sourcing_canManageEntries'] == 1) {
			$sourcing['businessPotential'] = $db->escape_string($core->sanitize_inputs($core->input['value'], array('removetags' => true)));
			$db->update_query('sourcing_suppliers', array('businessPotential' => $sourcing['businessPotential']), 'ssid="'.intval($core->input['ssid']).'"');
		}
	}
	elseif($core->input['action'] == 'do_requestchemical') {
		$potential_supplier = new Sourcing();
		$request = $potential_supplier->request_chemical($core->input['request']);
		switch($potential_supplier->get_status()) {
			case 1:
				output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
				break;
			case 2:
				output_xml("<status>false</status><message>{$lang->descriptionalreadyexsist}</message>");
				break;
			case 0:
				output_xml("<status>true</status><message>{$lang->successfullysaved}</message>");
				break;
		}
	}
}
?>