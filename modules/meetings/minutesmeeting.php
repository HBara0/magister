<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: minutesmeeting.php
 * Created:        @tony.assaad    Nov 11, 2013 | 11:42:49 AM
 * Last Update:    @tony.assaad    Nov 11, 2013 | 11:42:49 AM
 */

if(!$core->input['action']) {

	if(!defined('DIRECT_ACCESS')) {
		die('Direct initialization of this file is not allowed.');
	}
	if($core->usergroup['meetings_canCreateMeeting'] == 0) {
		error($lang->sectionnopermission);
	}
	if(($core->input['referrer'] == 'list') && ($core->input['do'] == 'edit')) {
		$action = 'edit';
		$mtid = $db->escape_string = $core->input['mtid'];
		$meeting_obj = new Meetings($mtid, true);
		$meeting = $meeting_obj->get();
		$mom_obj = new MeetingsMOM($mtid);
		$mof = $mom_obj->get();
		$meeting_list = '<select name="mof[mtid]"> <option value="'.$meeting['mtid'].'"> '.$meeting['title'].'</option> </select>';
	}
	else {
		$action = 'add';
		$multiple_meetings = Meetings::get_multiplemeetings('', array(), array('hasmom' => 0));
		if(is_array($multiple_meetings)) {
			$meeting_list = '<select name="mof[mtid]">';
			foreach($multiple_meetings as $mid => $meeting) {
				if(!empty($meeting['title'])) {
					$meeting_list.='<option value="'.$meeting['mtid'].'"> '.$meeting['title'].'</option>';
				}
			}
			$meeting_list.='</select>';
		}
	}
	eval("\$setminutesmeeting = \"".$template->get('meetings_minutesofmeetings')."\";");
	output_page($setminutesmeeting);
}
elseif($core->input['action'] == 'do_add') {
	$mtid = $db->escape_string($core->input['mof']['mtid']);
	$meeting_obj = new Meetings();
	$mom_obj = new MeetingsMOM($mtid);

	if(is_array($core->input['mof'])) {
		$mom_obj->save($core->input[mof]);
	}

	switch($mom_obj->get_errorcode()) {
		case 0:
			output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
			break;
		case 1:
			output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
			break;
	}
}
elseif($core->input['action'] == 'do_edit') {
	$mtid = $db->escape_string($core->input['mof']['mtid']);
	$meeting_obj = new Meetings();
	$mom_obj = new MeetingsMOM($mtid);
	
	if(is_array($core->input['mof'])) {
		$mom_obj->update($core->input[mof]);
	}
		switch($mom_obj->get_errorcode()) {
		case 2:
			output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
			break;
		case 1:
			output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
			break;
	}
}
?>
