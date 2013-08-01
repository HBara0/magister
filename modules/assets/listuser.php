<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * List userd assigned to assets
 * $id: listuser.php
 * Created:        @tony.assaad    Jul 4, 2013 | 12:06:47 PM
 * Last Update:    @tony.assaad    Jul 4, 2013 | 12:06:47 PM
 */

if($core->usergroup['assets_canManageAssets'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {
	$asset = new Assets();

	/* Perform inline filtering - START */
	$filters_config = array(
			'parse' => array('filters' => array('uid', 'asid', 'fromDate', 'toDate', 'toDate'),
					'overwriteField' => array('uid' => parse_selectlist('filters[uid]', 1, $asset->get_assignto(), '', '', '', array('blankstart' => true)),
							'asid' => parse_selectlist('filters[asid]', 2, get_specificdata('assets', array('asid', 'title'), 'asid', 'title', 'affid in('.implode(',', $core->user['affiliates']).')'), '', '', '', array('blankstart' => true))
					),
					'fieldsSequence' => array('uid' => 1, 'asid' => 2, 'fromDate' => 3, 'toDate' => 4)
			/* get the busieness potential and parse them in select list to pass to the filter array */
			),
			'process' => array(
					'filterKey' => 'auid',
					'mainTable' => array(
							'name' => 'assets_users',
							'filters' => array('uid' => 'uid', 'asid' => 'asid', 'fromDate' => array('operatorType' => 'date', 'name' => 'fromDate'), 'toDate' => array('operatorType' => 'date', 'name' => 'toDate')),
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
	
	$multipage_where .= $db->escape_string($attributes_filter_options['prefixes'][$core->input['filterby']].$core->input['filterby']).$filter_value;
	$assignee = $asset->get_allassignee($filter_where);
	
	$sort_url = sort_url();

	if(is_array($assignee)) {
		foreach($assignee as $auid => $assigneduser) {
			$rowclass = alt_row($rowclass);
			$assigneduser['fromDate_output'] = date($core->settings['dateformat'], $assigneduser['fromDate']);
			$assigneduser['toDate_output'] = date($core->settings['dateformat'], $assigneduser['toDate']);
			$auid = $assigneduser['auid'];
			/* Get assigned assets by assets object */
			$asset = new Assets($assigneduser['asid']);
			$assigneduser['asset'] = $asset->get()['title'];

			/* Get assigned USER by user object */
			$user = new Users($assigneduser['uid']);
			$employee = $user->get();

			$tools = ' <a href="#'.$assigneduser['auid'].'" id="deleteuser_'.$assigneduser['auid'].'_assets/listuser_loadpopupbyid" rel="delete_'.$assigneduser['auid'].'"><img src="'.$core->settings['rootdir'].'/images/invalid.gif" alt="'.$lang->delete.'" border="0"></a>   ';
			if(TIME_NOW > ($assigneduser['assignedon'] + ($core->settings['assets_preventeditasgnafter']))) {
				$tools = '<a href="#'.$assigneduser['auid'].'" id="deleteuser_'.$assigneduser['auid'].'_assets/listuser_loadpopupbyid" rel="delete_'.$assigneduser['auid'].'"><img src="'.$core->settings['rootdir'].'/images/invalid.gif" alt="'.$lang->delete.'" border="0"></a>   ';
			}

			eval("\$assignee_list .= \"".$template->get('assets_assignlist_row')."\";");
		}
		
		$multipages = new Multipages('assets_users', $core->settings['itemsperlist'], $multipage_where);
		$assignee_list .= '<tr><td colspan="7">'.$multipages->parse_multipages().'</td></tr>';
	}
	else {
		$assignee_list = '<tr><td colspan="7">'.$lang->na.'</td></tr>';
	}

	eval("\$assetsassignlist = \"".$template->get('assets_assignlist')."\";");
	output_page($assetsassignlist);
}
elseif($core->input['action'] == 'get_deleteuser') {
	eval("\$deleteassignee = \"".$template->get('popup_assets_listuserdelete')."\";");
	echo $deleteassignee;
}
elseif($core->input['action'] == 'perform_delete') {
	$auid = $db->escape_string($core->input['todelete']);
	
	$asset = new Assets();
	$assignee = $asset->get_assigneduser($auid);
	if(TIME_NOW > ($assignee['assignedon'] + ($core->settings['assets_preventeditasgnafter']))) {
		output_xml("<status>false</status><message>{$lang->notpossibledelete}</message>");
		exit;
	}
		
	$asset->delete_userassets($auid);
	switch($asset->get_errorcode()) {
		case 3:
			output_xml("<status>true</status><message>{$lang->successfullydeleted}</message>");
			break;
	}
}
elseif($core->input['action'] == 'get_edituser') {
	$asset = new Assets();
	$auid = $db->escape_string($core->input['id']);
	$assignee = $asset->get_assigneduser($auid);
	$assetslist = $asset->get_affiliateassets('titleonly');

	if(TIME_NOW > ($assignee['assignedon'] + ($core->settings['assets_preventeditasgnafter']))) {
		$disable_list = array('disabled' => 'disabled');
		$disable_text = ' disabled="disabled"';
	}
	if(TIME_NOW > ($assignee['assignedon'] + ($core->settings['assets_preventconditionupdtafter']))) {
		$disable_cor = ' disabled="disabled"';
	}
	$assets_list = parse_selectlist('assignee[asid]', 1, $assetslist, $assignee['asid'], '', '', $disable_list);
	$assigners = $asset->get_assignto();
	$employees_list = parse_selectlist('assignee[uid]', 1, $assigners, $assignee['uid'], '', '', $disable_list);
	$actiontype = 'edit';

	eval("\$editassignee = \"".$template->get('popup_assets_listuseredit')."\";");
	echo $editassignee;
}
?>
