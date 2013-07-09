<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: listuser.php
 * Created:        @tony.assaad    Jul 4, 2013 | 12:06:47 PM
 * Last Update:    @tony.assaad    Jul 4, 2013 | 12:06:47 PM
 */

if($core->usergroup['assets_canManageAssets'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {
	$asset = new Asset();
	$assignee = $asset->get_allassignee();
	$sort_url = sort_url();


	if(is_array($assignee)) {
		foreach($assignee as $auid => $assigneduser) {
			$assigneduser['fromDate_output'] = date($core->settings['dateformat'], $assigneduser['fromDate']);
			$assigneduser['toDate_output'] = date($core->settings['dateformat'], $assigneduser['toDate']);
			$auid = $assigneduser['auid'];
			/* Get assigned assets by assets object */
			$asset = new Asset($assigneduser['asid']);
			$assigneduser['asset'] = $asset->get()['title'];

			/* Get assigned USER by user object */
			$user = new Users($assigneduser['uid']);
			$employee = $user->get();
			eval("\$assignee_list .= \"".$template->get('assets_assigneelist')."\";");
		}
	}
	else {
		$assignee_list = '<tr><td colspan="7">'.$lang->na.'</td></tr>';
	}

	/* Perform inline filtering - START */
	$filters_config = array(
			'parse' => array('filters' => array('assignee', 'asid', 'fromDate', 'toDate'),
					'overwriteField' => array('assignee' => parse_selectlist('filters[assignee][]', 1, $asset->get_assignto(), ''),
							'asid' => parse_selectlist('filters[asid]', 2, $asset->get_affiliateassets('titleonly'), '')
					),
					'fieldsSequence' => array('assignee' => 1, 'asid' => 2, 'fromDate' => 3, 'toDate' => 4)
			/* get the busieness potential and parse them in select list to pass to the filter array */
			),
			'process' => array(
					'filterKey' => 'auid',
					'mainTable' => array(
							'name' => 'assets_users',
							'filters' => array('assignee' => array('operatorType' => 'multiple', 'name' => 'uid'), 'asid' => array('operatorType' => 'multiple', 'asid' => 'asid'), 'fromDate' => array('operatorType' => 'date', 'name' => 'fromDate'), 'toDate', 'type' => array('operatorType' => 'multiple', 'name' => 'type')),
					)
			),
			'secTables' => array(
					'assets' => array(
							'keyAttr' => 'asid',
							'joinKeyAttr' => 'asid',
							'joinWith' => 'assets_users',
					)
			)
	);

	$filter = new Inlinefilters($filters_config);
	//$filter_where_values = $filter->process_multi_filters();
	$filters_row_display = 'hide';
	$limit_start = 0;
	if(isset($core->input['start'])) {
		$limit_start = $db->escape_string($core->input['start']);
	}

	if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
		$core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
	}
	if(is_array($filter_where_values)) {
		$filters_row_display = 'show';
		$filter_where = 'ss.'.$filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
		$multipage_where .= $filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
	}

	$filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
	/* Perform inline filtering - END */
	$multipage_where .= $db->escape_string($attributes_filter_options['prefixes'][$core->input['filterby']].$core->input['filterby']).$filter_value;
	$multipages = new Multipages('sourcing_suppliers', $core->settings['itemsperlist'], $multipage_where);
	$assignee_list .= '<tr><td colspan="6">'.$multipages->parse_multipages().'</td></tr>';

	eval("\$assetsassignlist = \"".$template->get('assets_assignlist')."\";");
	output_page($assetsassignlist);
}
elseif($core->input['action'] == 'get_deleteuser') {
	eval("\$deleteassignee = \"".$template->get("popup_assets_listuserdelete")."\";");
	echo $deleteassignee;
}
elseif($core->input['action'] == 'get_edituser') {
	$asset = new Asset();
	$auid = $db->escape_string($core->input['id']);
	$assignee = $asset->get_assigneduser($auid);
	$assetslist = $asset->get_affiliateassets();  
	$assets_list = parse_selectlist('assignee[asid]', 1, $assetslist, $assignee['asid']);
	$assigners = $asset->get_assignto();
	$employees_list = parse_selectlist('assignee[uid]', 1, $assigners, $assignee['uid']);
	$actiontype = $lang->edit;
	eval("\$editassignee = \"".$template->get("popup_assets_listuseredit")."\";");
	echo $editassignee;
}
elseif($core->input['action'] == 'perform_delete') {
	$auid = $db->escape_string($core->input['todelete']);
	$asset = new Asset();
	$asset->delete_userassets($auid);
}
?>
