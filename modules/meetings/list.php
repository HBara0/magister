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
	$multiple_meetings = Meetings::get_multiplemeetings('', array('sortby' => $core->input['sortby'], 'order' => $core->input['order']));
	if(is_array($multiple_meetings)) {
		foreach($multiple_meetings as $mid => $meeting) {
			$row_tools = '';
			if($meeting['createdBy'] == $core->user['uid']) {
				if($meeting['hasMoM'] == 1) {
					$action = '&do=edit';
				}
				$row_tools = '<a href=index.php?module=meetings/create&mtid='.$meeting['mtid'].' title="'.$lang->edit.'"><img src=./images/icons/edit.gif border=0 alt='.$lang->edit.'/></a>';
				$row_tools .= ' <a href=index.php?module=meetings/minutesmeeting'.$action.'&referrer=list&mtid='.$meeting['mtid'].' title="'.$lang->setmof.'" rel="setmom_'.$meeting['mtid'].'"><img src="'.$core->settings['rootdir'].'/images/icons/boundreport.gif" alt="'.$lang->mom.'" border="0"></a>';
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
?>
