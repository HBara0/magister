<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: create.php
 * Created:        @tony.assaad    Feb 20, 2014 | 3:27:43 PM
 * Last Update:    @tony.assaad    Feb 20, 2014 | 3:27:43 PM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}
if(!$core->input['action']) {

	$user_obj = new Users();
	$allusers_objs = $user_obj->get_allusers();
	$reports_to = $user_obj->get_reportingto();
	foreach($allusers_objs as $allusers_obj) {
		$allusers[] = $allusers_obj->get();
	}
	foreach($allusers as $user) {
		$requestedby_list .='<option value="'.$user['uid'].'"> '.$user['displayName'].' </option>';
	}
	$assignedto_list = '<option value="" selected="selected"> </option>';
	foreach($reports_to as $assignedto) {
		$assignedto_list.='<option value="'.$assignedto['uid'].'"> '.$assignedto['displayName'].' </option>';
	}
	$query = $db->query("SELECT  * FROM ".Tprefix."development_requirements ");
	while($rowparent = $db->fetch_assoc($query)) {

		$parent_list .='<option value="'.$rowparent['drid'].'"> '.$rowparent['title'].' </option>';
	}


	eval("\$createrequirment = \"".$template->get('development_createrequirment')."\";");
	output($createrequirment);
}
elseif($core->input['action'] == 'do_add') {
	$modulesplit = substr($core->input['development']['parent'], 0, 5);
	$titlesplit = substr($core->input['development']['title'], 0, 3);
	$core->input['development']['refWord'] = $modulesplit.'/'.$titlesplit;

	$refKey = $db->fetch_field($db->query("SELECT (refKey)+0.1 as refKey  FROM ".Tprefix."development_requirements WHERE parent=".$db->escape_string($core->input['development']['parent'])." ORDER BY refKey DESC"), 'refKey');
	if(empty($refKey)) {
		$refKey+=1;
	}
	$requi_array = Array
			(
			'module' => $core->input['development']['modulefield'],
			'title' => $core->input['development']['title'],
			'refKey' => $refKey,
			'parent' => $core->input['development']['parent'],
			'description' => $core->input['development']['description'],
			'userInterface' => $core->input['development']['userInterface'],
			'security' => $core->input['development']['security'],
			'performance' => $core->input['development']['performance'],
			'requestedby' => $core->input['development']['requestedby'],
			'isApproved' => $core->input['development']['isApproved'],
			'isCompleted' => $core->input['development']['isCompleted'],
			'assignedTo' => $core->input['development']['assignedTo'],
			'refWord' => $core->input['development']['refWord'],
			'createdBy' => $core->user['uid'],
			'dateCreated' => TIME_NOW
	);

	$db->insert_query('development_requirements', $requi_array);
	print_R($requi_array);
}
?>
