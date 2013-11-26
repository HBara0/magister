<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: create.php
 * Created:        @tony.assaad    Nov 7, 2013 | 3:17:03 PM
 * Last Update:    @tony.assaad    Nov 7, 2013 | 3:17:03 PM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['meetings_canCreateMeeting'] == 0) {
	error($lang->sectionnopermission);
}

if(!$core->input['action']) {
	if(isset($core->input['mtid']) && !empty($core->input['mtid'])) {
		$mtid = $db->escape_string = $core->input['mtid'];
		$action = 'edit';
		$lang->create = $lang->edit;

		$meeting_obj = new Meetings($mtid);
		$meeting = $meeting_obj->get();
		if(!empty($meeting['fromDate'])) {
			$meeting['fromDate_output'] = date($core->settings['dateformat'], $meeting['fromDate']);
			$meeting['fromTime_output'] = trim(preg_replace('/(AM|PM)/', '', date($core->settings['timeformat'], $meeting['fromDate'])));
		}
		if(!empty($meeting['toDate'])) {
			$meeting['toDate_output'] = date($core->settings['dateformat'], $meeting['toDate']);
			$meeting['toTime_output'] = trim(preg_replace('/(AM|PM)/', '', date($core->settings['timeformat'], $meeting['toDate'])));
		}

		$meeting_assoc = $meeting_obj->get_meetingassociations();
		if(is_array($meeting_assoc)) {
			foreach($meeting_assoc as $mtaid => $associaton) {
				$associaton_temp = $associaton->get();
				$associatons[$associaton_temp['idAttr']] = $associaton_temp['id'];
			}
			unset($associaton_temp);
		}

		$entity_obj = new Entities($associatons['cid']);
		$meeting['associations']['cutomername'] = $entity_obj->get()['companyName'];
		$meeting['associations']['spid'] = $associatons['cid'];
		$entity_obj = new Entities($associatons['spid']);
		$meeting['associations']['suppliername'] = $entity_obj->get()['companyName'];
		$meeting['associations']['spid'] = $associatons['spid'];

		//$meeting['attendees'] = $meeting_obj->get_attendees();
	}
	else {
		$sectionsvisibility['associationssection'] = ' display:none;';
		$action = 'create';
	}

	$afiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, 'affid IN ('.implode(',', $core->user['affiliates']).')');
	$afiliates[0] = '';
	asort($afiliates);
	$affiliates_list = parse_selectlist('meeting[associations][affid]', 5, $afiliates, $associatons['affid']);

	$aff_events = Events::get_affiliatedevents($core->user['affiliates']);
	foreach($aff_events as $ceid => $event) {
		if($associatons['ceid'] == $ceid) {
			$selected = ' selected="selected"';
		}
		$events_list .= '<option value="'.$ceid.'" "'.$selected.'">'.$event['title'].'</option>';
	}
	//$user_obj = new Users($core->user['uid']);
	//$business_leaves = $user_obj->get_leaves();
//	foreach($business_leaves as $leaveid => $leave) {
//		$business_leaves_list .='<option  value="'.$leaveid.'">'.$leave['title'].'</option>';
//	}
	eval("\$createmeeting_associations = \"".$template->get('meeting_create_associations')."\";");

	eval("\$createmeeting = \"".$template->get('meeting_create')."\";");
	output_page($createmeeting);
}
elseif($core->input['action'] == 'do_createmeeting') {
	$meeting_obj = new Meetings();
	$meeting_obj->create($core->input['meeting']);

	switch($meeting_obj->get_errorcode()) {
		case 0:
			output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
			break;
		case 1:
			output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
			break;
		case 2:
			output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
			break;
		case 3:
			output_xml('<status>false</status><message>'.$lang->invaliddate.'</message>');
			break;
	}
}
elseif($core->input['action'] == 'do_editmeeting') {
	$mtid = $db->escape_string($core->input['mtid']);

	$meeting_obj = new Meetings($mtid);
	$meeting_obj->update($core->input['meeting']);

	switch($meeting_obj->get_errorcode()) {
		case 2:
			output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
			break;
		case 1:
			output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
			break;
	}
}
?>
