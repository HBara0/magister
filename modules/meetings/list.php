<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: list.php
 * Created:        @tony.assaad    Nov 8, 2013 | 4:54:21 PM
 * Last Update:    @tony.assaad    Nov 8, 2013 | 4:54:21 PM
 */


if(!defined('DIRECT_ACCESS')) {
	die('Direct initialization of this file is not allowed.');
}
if($core->usergroup['meetings_canCreateMeeting'] == 0) {
	error($lang->sectionnopermission);
}
if(!$core->input['action']) {
	$sort_url = sort_url();
	$multiple_meetings = Meetings::get_multiplemeetings(array('order' => array('sortby' => $core->input['sortby'], 'order' => $core->input['order'])));
	if(is_array($multiple_meetings)) {
		foreach($multiple_meetings as $mid => $meeting) {
			$meeting_obj = new Meetings($mid);
			$row_tools = '';
			if($meeting['createdBy'] == $core->user['uid']) {
				if($meeting['hasMoM'] == 1) {
					$action = '&do=edit';
				}

				$row_tools = '<a href=index.php?module=meetings/create&mtid='.$meeting['mtid'].' title="'.$lang->edit.'"><img src=./images/icons/edit.gif border=0 alt='.$lang->edit.'/></a>';
				$row_tools .= ' <a href=index.php?module=meetings/minutesmeeting'.$action.'&referrer=list&mtid='.$meeting['mtid'].' title="'.$lang->setmof.'" rel="setmof_'.$meeting['mtid'].'"><img src="'.$core->settings['rootdir'].'/images/icons/boundreport.gif" alt="'.$lang->mom.'" border="0"></a>';
				$row_tools .= ' <a href="#'.$meeting['mtid'].'" id="sharemeeting_'.$meeting['mtid'].'_meetings/list_loadpopupbyid" rel="share_'.$meeting['mtid'].'" title="'.$lang->sharewith.'"><img src="'.$core->settings['rootdir'].'/images/icons/sharedoc.png" alt="'.$lang->sharewith.'" border="0"></a>';
			}

			$meeting['fromDate_output'] = date($core->settings['dateformat'], $meeting['fromDate']);
			$meeting['toDate_output'] = date($core->settings['dateformat'], $meeting['toDate']);

			if(strlen($meeting['description']) > 50) {
				$meeting['description'] = substr($meeting['description'], 0, 50).'...';
			}
			eval("\$meeting_list_row .= \"".$template->get('meeting_list_row')."\";");
		}
	}

	eval("\$meeting_list = \"".$template->get('meeting_list')."\";");
	output_page($meeting_list);
}
if($core->input['action'] == 'get_sharemeeting') {
	$mtid = $db->escape_string($core->input['id']);
	$aff_obj = new Affiliates($core->user['mainaffiliate']);
	$affiliates_users = $aff_obj->get_users();
	$meeting_obj = new Meetings($mtid);

	$shared_users = $meeting_obj->get_shared_users();
	if(is_array($shared_users)) {
		$shared_users = array_keys($shared_users);
	}

	foreach($affiliates_users as $uid => $user) {
		$checked = '';
		if($uid == $core->user['uid']) {
			continue;
		}

		if(is_array($shared_users)) {
			if(in_array($uid, $shared_users)) {
				$checked = ' checked="checked"';
			}
		}

		eval("\$sharewith_rows .= \"".$template->get('popup_meetings_sharewith_rows')."\";");
	}
	eval("\$share_meeting = \"".$template->get('popup_meetings_share')."\";");
	output($share_meeting);
}
elseif($core->input['action'] == 'do_share') {
	$mtid = $db->escape_string($core->input['mtid']);
	if(is_array($core->input['sharemeeting'])) {
		$meeting_obj = new Meetings($mtid);
		$meeting_obj->share($core->input['sharemeeting']);

		switch($meeting_obj->get_errorcode()) {
			case 0:
				output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
				break;
		}
	}
	else {
		output_xml('<status>false</status><message>'.$lang->fillrequiredfields.'</message>');
	}
}
?>