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
		$mtid = $core->input['mtid'];
		$action = 'edit';

		$lang->create = $lang->edit;

		$meeting_obj = new Meetings($mtid);

		$meeting = $meeting_obj->get();
		if(is_array($meeting)) {
			if(!empty($meeting['fromDate'])) {
				$meeting['fromDate_output'] = date($core->settings['dateformat'], $meeting['fromDate']);
				$meeting['fromTime_output'] = trim(preg_replace('/(AM|PM)/', '', date($core->settings['timeformat'], $meeting['fromDate'])));
			}
			if(!empty($meeting['toDate'])) {
				$meeting['toDate_output'] = date($core->settings['dateformat'], $meeting['toDate']);
				$meeting['toTime_output'] = trim(preg_replace('/(AM|PM)/', '', date($core->settings['timeformat'], $meeting['toDate'])));
			}
			if($meeting['isPublic'] == 1) {
				$checked_checkboxes['isPublic'] = ' checked="checked"';
			}
			$meeting_assoc = $meeting_obj->get_meetingassociations();
			if(is_array($meeting_assoc)) {
				foreach($meeting_assoc as $mtaid => $associaton) {
					$associaton_temp = $associaton->get();
					$associatons[$associaton_temp['idAttr']] = $associaton_temp['id'];
				}
				unset($associaton_temp);
			}
			$rowid = 1;
			//eval("\$createmeeting_userattendees = \"".$template->get('meeting_create_userattendees')."\";");

			$meeting_attednobjs = $meeting_obj->get_attendees();
			if(is_array($meeting_attednobjs)) {
				foreach($meeting_attednobjs as $matid => $meeting_attednobj) {
					//$meeting['attendees'] = $meeting_attednobj->get();
					//switch users Attendees
					$attendees_objs = $meeting_attednobj->switch_attendee();
					$meeting['attendees']['entityattendee'] = $attendees_objs->get();
					if(isset($meeting['attendees']['entityattendee']['uid'])) {
						$meeting['attendees']['user'] = $meeting['attendees']['entityattendee']['displayName'];
						$rowid = $meeting['attendees']['uid'] = $meeting['attendees']['entityattendee']['uid'];
						eval("\$createmeeting_userattendees .= \"".$template->get('meeting_create_userattendees')."\";");
					}
					//switch represtnetatives
					if(isset($meeting['attendees']['entityattendee']['rpid'])) {
						$meeting['attendees']['rep'] = $meeting['attendees']['entityattendee']['name'];
						$reprowid = $meeting['attendees']['repid'] = $meeting['attendees']['entityattendee']['rpid'];
						//$reprowid = $meeting['attendees']['repid'];
						eval("\$createmeeting_repattendees .= \"".$template->get('meeting_create_repattendees')."\";");
					}
				}
			}

			$entity_obj = new Entities($associatons['cid']);
			$meeting['associations']['cutomername'] = $entity_obj->get()['companyName'];
			$meeting['associations']['spid'] = $associatons['cid'];
			$entity_obj = new Entities($associatons['spid']);
			$meeting['associations']['suppliername'] = $entity_obj->get()['companyName'];
			$meeting['associations']['spid'] = $associatons['spid'];
		}
		else {
			redirect('index.php?module=meetings/list');
		}
		//$meeting['attendees'] = $meeting_obj->get_attendees();
	}
	else {
		$rowid = 0;
		$rowid = intval($core->input['value']) + 1;
		$reprowid = 0;
		$reprowid = intval($core->input['value']) + 1;
		eval("\$createmeeting_userattendees = \"".$template->get('meeting_create_userattendees')."\";");
		eval("\$createmeeting_repattendees  = \"".$template->get('meeting_create_repattendees')."\";");
		$sectionsvisibility['associationssection'] = ' display:none;';
		$action = 'create';
	}
	$pagetitle = $lang->{$action.'meeting'};
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

	eval("\$createmeeting_associations = \"".$template->get('meeting_create_associations')."\";");

	eval("\$createmeeting = \"".$template->get('meeting_create')."\";");

	output_page($createmeeting);
}
elseif($core->input['action'] == 'do_createmeeting') {
	$meeting_obj = new Meetings();
	$meetingatt_obj = new MeetingsAttendees();
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
		case 4:
			output_xml('<status>false</status><message>'.$lang->meetingintersect.'</message>');
			break;
		default:
			output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
			break;
	}
}
elseif($core->input['action'] == 'do_editmeeting') {
	$mtid = $db->escape_string($core->input['mtid']);
	$meeting_obj = new Meetings($mtid);
	$meeting_obj->update($core->input['meeting']);
	switch($meeting_obj->get_errorcode()) {
		case 2:
			output_xml('<status>true</status><message>'.$lang->successfullysaved.' </message>');
			break;
		case 1:
			output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
			break;
		case 3:
			output_xml('<status>false</status><message>'.$lang->meetingintersect.'</message>');
			break;
	}
}
?>