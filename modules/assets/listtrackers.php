<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: listtrackers.php
 * Created:        @tony.assaad    Jun 25, 2013 | 4:07:14 PM
 * Last Update:    @tony.assaad    Jun 25, 2013 | 4:07:14 PM
 */

if($core->usergroup['assets_canManageTracker'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {
	$assets = new Assets();

	$sort_url = sort_url();
	/* Perform inline filtering - START */
	$filters_config = array(
			'parse' => array('filters' => array('deviceId', 'IMEI', 'password', 'Phonenumber', 'asid'),
					'overwriteField' => array(
							'asid' => ''
					),
					'fieldsSequence' => array('deviceId' => 1, 'IMEI' => 2, 'Phonenumber' => 3, 'password' => 4, 'asid' => 5)
			/* get the busieness potential and parse them in select list to pass to the filter array */
			),
			'process' => array(
					'filterKey' => 'trackerid',
					'mainTable' => array(
							'name' => 'assets_trackers',
							'filters' => array('deviceId' => 'deviceId', 'IMEI' => 'IMEI', 'password' => 'password', 'Phonenumber' => 'Phonenumber'),
					)
			),
	);

	$filter = new Inlinefilters($filters_config);
	$filter_where_values = $filter->process_multi_filters();
	$filters_row_display = 'hide';

	if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
		$core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
	}
	if(is_array($filter_where_values)) {
		$filters_row_display = 'show';
		$filter_where = $filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
		$multipage_where .= $filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
	}

	$filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
	/* Perform inline filtering - END */

	$assets_tracker = $assets->get_trackers($filter_where);
	if(is_array($assets_tracker)) {
		foreach($assets_tracker as $tracker) {

			$rowclass = alt_row($rowclass);
			$tracker['password'] = base64_decode($tracker['password']);

			eval("\$assets_trackerslistrow.= \"".$template->get('assets_trackerslistrow')."\";");
		}
	}
	else {
		$assets_trackerslistrow.='<tr><td>'.$lang->na.'</td></tr>';
	}
	$multipage_where .= $db->escape_string($attributes_filter_options['prefixes'][$core->input['filterby']].$core->input['filterby']).$filter_value;
	$multipages = new Multipages('asssets_trackers', $core->settings['itemsperlist'], $multipage_where);
	$assets_trackerslistrow .= '<tr><td colspan="6">'.$multipages->parse_multipages().'</td></tr>';
	eval("\$assets_trackerslist= \"".$template->get('assets_trackerslist')."\";");
	output_page($assets_trackerslist);
}
?>
