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

			$rowid = $reprowid = $rowattachmentid = 1;
			$meeting_attednobjs = $meeting_obj->get_attendees();
			if(is_array($meeting_attednobjs)) {
				foreach($meeting_attednobjs as $matid => $meeting_attednobj) {
					$attendees_objs = $meeting_attednobj->get_attendee();
					$meeting['attendees'][$matid] = $attendees_objs->get();
					if(isset($meeting['attendees'][$matid]['uid'])) {
						$meeting['attendees'][$matid]['name'] = $meeting['attendees'][$matid]['displayName'];
						$meeting['attendees'][$matid]['id'] = $meeting['attendees'][$matid]['uid'];
						eval("\$createmeeting_userattendees .= \"".$template->get('meeting_create_userattendee')."\";");
						$rowid++;
					}
					if(isset($meeting['attendees'][$matid]['rpid'])) {
						$meeting['attendees'][$matid]['id'] = $meeting['attendees'][$matid]['rpid'];
						eval("\$createmeeting_repattendees .= \"".$template->get('meeting_create_repattendee')."\";");
						$reprowid++;
					}
				}
				unset($meeting['attendees'], $matid);
			}

			if(empty($createmeeting_userattendees)) {
				eval("\$createmeeting_userattendees = \"".$template->get('meeting_create_userattendee')."\";");
			}

			if(empty($createmeeting_repattendees)) {
				eval("\$createmeeting_repattendees  = \"".$template->get('meeting_create_repattendee')."\";");
			}

			$entity_obj = new Entities($associatons['cid']);
			$meeting['associations']['cutomername'] = $entity_obj->get()['companyName'];
			$meeting['associations']['spid'] = $associatons['cid'];
			$entity_obj = new Entities($associatons['spid']);
			$meeting['associations']['suppliername'] = $entity_obj->get()['companyName'];
			$meeting['associations']['spid'] = $associatons['spid'];
			/* parse Attachments ---START */
			$attachmentrow = 0;
			$meeting_attachobjs = $meeting_obj->get_attachments();
			foreach($meeting_attachobjs as $meeting_attachobj) {
				$altrow = alt_row('trow');
				$attachmentrow++;
				$meeting_attachments = $meeting_attachobj->get();
				if(is_array($meeting_attachments)) {
					eval("\$createmeeting_edit_attachmentsfiles .= \"".$template->get('meeting_edit_attachments_files')."\";");
				}
			}

			/* parse Attachments ---END */

			eval("\$meeting_attachments = \"".$template->get('meeting_edit_attachements')."\";");
		}
		else {
			redirect('index.php?module=meetings/list');
		}
		//$meeting['attendees'] = $meeting_obj->get_attendees();
	}
	else {
		$rowid = 1;
		$reprowid = 1;
		eval("\$createmeeting_userattendees = \"".$template->get('meeting_create_userattendee')."\";");
		eval("\$createmeeting_repattendees  = \"".$template->get('meeting_create_repattendee')."\";");
		eval("\$meeting_attachments = \"".$template->get('meeting_create_attachments')."\";");
		$sectionsvisibility['associationssection'] = ' display:none;';
		$action = 'create';
	}
	$pagetitle = $lang->{$action.'meeting'};
	$afiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 1, 'affid IN ('.implode(',', $core->user['affiliates']).')');
	$afiliates[0] = '';
	asort($afiliates);
	$affiliates_list = parse_selectlist('meeting[associations][affid]', 5, $afiliates, $associatons['affid']);

	$aff_events = Events::get_affiliatedevents($core->user['affiliates']);
	if(is_array($aff_events)) {
		foreach($aff_events as $ceid => $event) {
			if($associatons['ceid'] == $ceid) {
				$selected = ' selected="selected"';
			}
			$events_list .= '<option value="'.$ceid.'" "'.$selected.'">'.$event['title'].'</option>';
		}
	}

	eval("\$createmeeting_associations = \"".$template->get('meeting_create_associations')."\";");

	eval("\$createmeeting = \"".$template->get('meeting_create')."\";");

	output($createmeeting);
}
elseif($core->input['action'] == 'deletefile') {
	$mattid = $db->escape_string($core->input[mattid]);
	if(!empty($mattid)) {
		$meetingattach_obj = new MeetingsAttachments($mattid);
		$deleted = $meetingattach_obj->delete();
		header('Content-type: text/javascript');
		if($deleted) {
			echo '$("tr[id=\'file_'.$mattid.'\']").css("display","none");';
		}
	}
}
elseif($core->input['action'] == 'do_createmeeting') {
	$core->input['meeting']['attachments'] = $_FILES;
	$meeting_obj = new Meetings();
	$meeting_obj->create($core->input['meeting']);

	echo $headerinc;
	switch($meeting_obj->get_errorcode()) {
		case 0:
			$output_class = 'green_text';
			$output_message = $lang->successfullysaved;
			break;
		case 1:
			$output_class = 'red_text';
			$output_message = $lang->fillallrequiredfields;
			//output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
			break;
		case 2:
			$output_class = 'red_text';
			$output_message = $lang->errorsaving;
			//output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
			break;
		case 3:
			$output_class = 'red_text';
			$output_message = $lang->invaliddate;
			//output_xml('<status>false</status><message>'.$lang->invaliddate.'</message>');
			break;
		case 4:
			$output_class = 'red_text';
			$output_message = $lang->meetingintersect;
			//output_xml('<status>false</status><message>'.$lang->meetingintersect.'</message>');
			break;

		default:
			$output_class = 'red_text';
			$output_message = $lang->errorsaving;
			//output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
			break;
	}
	?>
	<script language="javascript" type="text/javascript">
		$(function() {
			top.$("#upload_Result").html("<span class='<?php echo $output_class;?>'><?php echo $output_message;?></span>");
		});
	</script>   
	<?php
}
elseif($core->input['action'] == 'do_editmeeting') {
	$mtid = $db->escape_string($core->input['mtid']);
	$core->input['meeting']['attachments'] = $_FILES;

	$meeting_obj = new Meetings($mtid);
	$meeting_obj->update($core->input['meeting']);
	switch($meeting_obj->get_errorcode()) {
		case 2:
			output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
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