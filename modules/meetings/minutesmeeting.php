<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: minutesmeeting.php
 * Created:        @tony.assaad    Nov 11, 2013 | 11:42:49 AM
 * Last Update:    @tony.assaad    Nov 11, 2013 | 11:42:49 AM
 */

if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['meetings_canCreateMeeting'] == 0) {
	error($lang->sectionnopermission);
}

if(!$core->input['action']) {
	if(isset($core->input['mtid']) && !empty($core->input['mtid'])) {
		$meeting_obj = new Meetings($core->input['mtid'], false);
		$meeting = $meeting_obj->get();
		$meeting_list = '<input type="hidden" value="'.$meeting['mtid'].'" name="mof[mtid]" /><strong><a href="index.php?module=meetings/viewmeeting&mtid='.$meeting['mtid'].'" target="_blank">'.$meeting['title'].' | '.$meeting['location'].'</a></strong>';
	
		if($meeting['hasMoM'] == 1) {
			$action = 'edit';
		}
		else {
			$action = 'add';
		}
	}
	else {
		$action = 'add';
	}
	if($action == 'edit') {
		$mom_obj = $meeting_obj->get_mom();
		$mof = $mom_obj->get();
	}
	else {
		$multiple_meetings = Meetings::get_multiplemeetings('', array(), array('hasmom' => 0));
		if(is_array($multiple_meetings)) {
			if(empty($meeting_list)) {
				$meeting_list = '<select name="mof[mtid]">';
				foreach($multiple_meetings as $mid => $meeting) {
					if(!empty($meeting['title'])) {
						$meeting_list .='<option value="'.$meeting['mtid'].'"> '.$meeting['title'].'</option>';
					}
				}
				$meeting_list .= '</select>';
			}
		}
		else {
			$meeting_list = $lang->nomeetingavailable;
		}
	}
	eval("\$setminutesmeeting = \"".$template->get('meetings_minutesofmeetings')."\";");
	output_page($setminutesmeeting);
}
elseif($core->input['action'] == 'do_add' || $core->input['action'] == 'do_edit') {
	if(empty($core->input['mof']['momid'])) {
		if(!empty($core->input['mof']['mtid'])) {
			$meeting_obj = new Meetings($core->input['mof']['mtid']);
			$mom_obj = $meeting_obj->get_mom();
			if($mom_obj == false) {
				$action = 'add';
			}
			else {
				$action = 'edit';
			}
		}
		else {
			$action = 'error';
		}
	}
	else {
		$mom_obj = new MeetingsMOM($core->input['mof']['momid']);
		$action = 'edit';
	}

	if($action == 'edit') {
		$mom_obj->update($core->input['mof']);
	}
	elseif($action == 'add') {
		MeetingsMOM::create($core->input['mof']);
	}
	else {
		output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
		exit;
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
