<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: viewmeeting.php
 * Created:        @tony.assaad    Nov 13, 2013 | 12:42:10 PM
 * Last Update:    @tony.assaad    Nov 13, 2013 | 12:42:10 PM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['meetings_canCreateMeeting'] == 0) {
    error($lang->sectionnopermission);
}

if(!$core->input['action']) {
    $meeting_obj = new Meetings($core->input['mtid']);
    if(!$meeting_obj->can_viewmeeting()) {
        error($lang->sectionnopermission);
    }
    $meeting = $meeting_obj->get();

    if(!empty($meeting['fromDate'])) {
        $meeting['fromDate_output'] = date($core->settings['dateformat'], $meeting['fromDate']);
        $meeting['fromTime_output'] = date($core->settings['timeformat'], $meeting['fromDate']);
    }
    if(!empty($meeting['toDate'])) {
        $meeting['toDate_output'] = date($core->settings['dateformat'], $meeting['toDate']);
        $meeting['toTime_output'] = date($core->settings['timeformat'], $meeting['toDate']);
    }

    $meeting['createdby'] = $meeting_obj->get_createdby()->get()['displayName'];

    /* Parse Attachments - START */
    $meeting_attachmentobjs = $meeting_obj->get_attachments();
    if(is_array($meeting_attachmentobjs)) {
        foreach($meeting_attachmentobjs as $meeting_attachmentobj) {
            $meeting_attachment = $meeting_attachmentobj->get();

            $meeting_attachment['size_output'] = format_size($meeting_attachment['size']);
            eval("\$meeting_attachments .= \"".$template->get('meetings_viewmeeting_attachment')."\";");
        }
        eval("\$meeting_attachmentssection = \"".$template->get('meetings_viewmeeting_attachments')."\";");
        unset($meeting_attachments);
    }
    /* Parse Attachments - END */

    if($meeting['hasMoM'] == 1) {
        $minsofmeeting = $meeting_obj->get_mom()->get();
        if(!empty($minsofmeeting['createdOn'])) {
            $minsofmeeting['createdOn_date_output'] = date($core->settings['dateformat'], $minsofmeeting['createdOn']);
            $minsofmeeting['createdOn_time_output'] = date($core->settings['timeformat'], $minsofmeeting['createdOn']);
            $minsofmeeting['createdOn_output'] = $lang->sprint($lang->createdon, $minsofmeeting['createdOn_date_output'], $minsofmeeting['createdOn_time_output']);
        }

        if(!empty($minsofmeeting['modifiedOn'])) {
            $minsofmeeting['modifiedOn_date_output'] = date($core->settings['dateformat'], $minsofmeeting['modifiedOn']);
            $minsofmeeting['modifiedOn_time_output'] = date($core->settings['timeformat'], $minsofmeeting['modifiedOn']);
            $minsofmeeting['modifiedOn_output'] = ' | '.$lang->sprint($lang->modifiedon, $minsofmeeting['modifiedOn_date_output'], $minsofmeeting['modifiedOn_time_output']);
        }
        $minsofmeeting['actions_output'] = '<strong>'.$lang->specificfollowactions.'</strong>';
        $minsofmeeting['actions_output'] .= $meeting_obj->get_mom()->parse_actions();

        eval("\$meetings_viewmeeting_mom = \"".$template->get('meetings_viewmeeting_mom')."\";");
    }

    $meeting['attendees_output'] = $meeting_obj->parse_attendees();
    $meeting['locationoutput'] = $meeting_obj->get_location();


    $share_meeting .= ' <a href="#" id="sharemeeting_'.$meeting['mtid'].'_meetings/list_loadpopupbyid" rel="share_'.$meeting['mtid'].'" title="'.$lang->sharewith.'"><img src="'.$core->settings['rootdir'].'/images/icons/sharedoc.png" alt="'.$lang->sharewith.'" border="0"></a>';

    eval("\$meeting_viewmeeting = \"".$template->get('meetings_viewmeeting')."\";");
    output_page($meeting_viewmeeting);
}
elseif($core->input['action'] == 'download') {
    $meeting_obj = new Meetings($core->input['mtid']);
    if(!$meeting_obj->can_viewmeeting()) {
        error($lang->sectionnopermission);
    }

    if(!isset($core->input['mattid']) || empty($core->input['mattid'])) {
        redirect($_SERVER['HTTP_REFERER']);
    }
    $meeting_attachmentobj = new MeetingsAttachments($core->input['mattid']);
    $download_objs = $meeting_attachmentobj->download();
}
?>
