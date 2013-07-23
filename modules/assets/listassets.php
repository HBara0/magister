<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: 'listassets.php
 * Created:        @tony.assaad    Jun 25, 2013 | 2:56:12 PM
 * Last Update:    @tony.assaad    Jun 25, 2013 | 2:56:12 PM
 */


if($core->usergroup['assets_canManageAssets'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

if(!$core->input['action']) {
	$assets = new Asset();
	$sort_url = sort_url();

	/* Perform inline filtering - START */
	$filters_config = array(
			'parse' => array('filters' => array('title', 'affid', 'description', 'type', 'status'),
					'overwriteField' => array('type' => parse_selectlist('filters[type]', 4, get_specificdata('assets_types', array('astid', 'title'), 'astid', 'title', 'title'), '', '', '', array('blankstart' => true)),
							'status' => parse_selectlist('filters[status]', 4, array('damaged' => 'damaged', 'notfunctional' => 'not-functional', 'fullyfunctional' => 'fully-functional'), '', '', '', array('blankstart' => true)),
							'asid' => parse_selectlist('filters[affid]', 2, get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', 'affid in('.implode(',', $core->user['affiliates']).')'), '')
					),
					'fieldsSequence' => array('title' => 1, 'affid' => 2, 'description' => 3, 'type' => 4, 'status' => 5)
			/* get the busieness potential and parse them in select list to pass to the filter array */
			),
			'process' => array(
					'filterKey' => 'asid',
					'mainTable' => array(
							'name' => 'assets',
							'filters' => array('title' => 'title', 'affid' => array('operatorType' => 'multiple', 'name' => 'affid'), 'description' => 'description', 'type' => 'type', 'status' => 'status'),
					)
			),
	);

	$filter = new Inlinefilters($filters_config);
	$filter_where_values = $filter->process_multi_filters();
	$filters_row_display = 'hide';


	if(is_array($filter_where_values)) {
		$filters_row_display = 'show';
		$filter_where = $filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
		$multipage_where .= $filters_config['process']['filterKey'].' IN ('.implode(',', $filter_where_values).')';
	}

	$all_assets = $assets->get_affiliateassets('', $filter_where);

	$multipage_where .= $db->escape_string($attributes_filter_options['prefixes'][$core->input['filterby']].$core->input['filterby']).$filter_value;
	$multipages = new Multipages('assets', $core->settings['itemsperlist'], $multipage_where);
	$assets_listrow .= '<tr><td colspan="6">'.$multipages->parse_multipages().'</td></tr>';

	if(is_array($all_assets)) {
		foreach($all_assets as $asset) {
			$rowclass = alt_row($rowclass);
			if($asset['isActive'] == 0) {
				$notactive = 'unapproved';
			}
			$affilate = new Affiliates($asset['affid']);
			if(!empty($asset['createdon'])) {
				$asset['createdon_ouput'] = date($core->settings['dateformat'].' '.$core->settings['timeformat'], $asset['createdon']);
			}
			$asset['affiliate'] = $affilate->get_country()->get()['name'];
			eval("\$assets_listrow .= \"".$template->get('assets_listrow')."\";");
		}
	}
	$filters_row = $filter->prase_filtersrows(array('tags' => 'table', 'display' => $filters_row_display));
	/* Perform inline filtering - END */


	eval("\$assets_list = \"".$template->get('assets_list')."\";");
	output_page($assets_list);
}
elseif($core->input['action'] == 'get_deleteasset') {
	$asid = $db->escape_string($core->input['id']);
	$asset = new Asset($asid);
	$asset = $asset->get();

	eval("\$deleteasset = \"".$template->get("popup_assets_listassetsdelete")."\";");
	echo $deleteasset;
}
elseif($core->input['action'] == 'get_editasset') {
	$asid = $db->escape_string($core->input['id']);
	$asset = new Asset($asid);
	$assets = $asset->get();
	if($assets['isActive'] == 1) {
		$ischecked = "checked";
	}

	$assetstype = get_specificdata('assets_types', array('astid', 'name', 'title'), 'astid', 'title', 'title');
	$assets_status = array('damaged' => 'damaged', 'notfunctional' => 'not-functional', 'fullyfunctional' => 'fully-functional');

	$assets_type = parse_selectlist('asset[type]', 3, $assetstype, $assets['type']);
	$assetsstatus = parse_selectlist('asset[status]', 4, $assets_status, $assets['status']);

	$affilate = new Affiliates($assets['affid']);
	$assets['affiliate'] = $affilate->get_country()->get()['name'];
	$affiliate_list = '<option value="'.$assets['affid'].'">'.$assets['affiliate'].'</option>';
	$actiontype = $lang->edit;
	eval("\$editasset = \"".$template->get("popup_assets_listassetsedit")."\";");
	echo $editasset;
}
elseif($core->input['action'] == 'perform_delete') {
	$asid = $db->escape_string($core->input['todelete']);
	$asset = new Asset($asid);
	$asset_relatedtables = array('assets_users', 'assets_trackingdevices');
	foreach($asset_relatedtables as $assettable) {
		if(value_exists($assettable, 'asid', $asid)) {
			$asset->deactivate_asset();
			break;
		}
		else {
			$asset->delete_asset();
		}
	}
	switch($asset->get_errorcode()) {
		case 3:
			output_xml("<status>true</status><message>{$lang->successfullydeactivated}</message>");
			break;
		case 4:
			output_xml("<status>true</status><message>{$lang->successfullydeleted}</message>");
			break;
	}
}
?>
