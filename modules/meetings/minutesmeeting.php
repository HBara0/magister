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
        if(!is_object($meeting_obj)) {
            redirect('index.php?module=meetings/list');
        }
        $meeting = $meeting_obj->get();

        if($meeting['createdBy'] != $core->user['uid']) {
            error($lang->sectionnopermission);
        }
        $display = "none";
        //<input type="hidden" value="'.$meeting['mtid'].'" name="mof[mtid]" />
        $meeting_list = '<strong><a href="index.php?module=meetings/viewmeeting&mtid='.$meeting['mtid'].'" target="_blank">'.$meeting['title'].' | '.$meeting['location'].'</a></strong>';

        if($meeting['hasMoM'] == 1) {
            $action = 'edit';
        }
        else {
            $action = 'add';
        }
        $meetingmom = MeetingsMOM::get_mom_bymeeting($core->input['mtid']);
        //////////////////////////////////////////////
        $meetingassociations = MeetingsAssociations::get_data(array('mtid' => $meeting['mtid'], 'idAttr' => 'spid'));
        if(is_object($meetingassociations)) {
            $spid = $meetingassociations->id;
        }
        unset($meetingassociations);
        //////////////////////////////////////////
        $momactions = MeetingsMOMActions::get_data(array('momid' => $meetingmom->momid), array('returnarray' => true));
        if(is_array($momactions)) {
            $arowid = 0;
            foreach($momactions as $actions) {
                $actions_data = $actions->get();
                $checksum['actions'] = $actions_data['inputChecksum'];
                if($actions_data['date'] != 0) {
                    $actions_data['date_otput'] = date($core->settings['dateformat'], $actions_data['date']);
                    $actions_data['date_formatted'] = date($core->settings['dateformat'], $actions_data['date']);
                }
                if($actions_data['isTask'] == 1) {
                    $checked = 'checked = "checked"';
                }
                $momactionsassignees = MeetingsMOMActionAssignees::get_data(array('momaid' => $actions->momaid), array('returnarray' => true));
                $userrowid = 0;
                $reprowid = 0;
                if(is_array($momactionsassignees)) {
                    foreach($momactionsassignees as $assignee) {
                        $assignee_data = $assignee->get();
                        if(isset($assignee->uid) && !empty($assignee->uid)) {
                            $user = new Users($assignee->uid);
                            if(is_object(($user))) {
                                $assignee_data['username'] = $user->get_displayname();
                            }
                            $checksum['users'] = $assignee->inputChecksum;
                            eval("\$actions_users .= \"".$template->get('meetings_mom_actions_users')."\";");
                            $userrowid++;
                        }
                        if(isset($assignee->repid) && !empty($assignee->repid)) {
                            $representative = new Representatives($assignee->repid);
                            if(is_object(($representative))) {
                                $assignee_data['repname'] = $representative->get_displayname();
                            }
                            $checksum['representatives'] = $assignee->inputChecksum;
                            eval("\$actions_representatives .= \"".$template->get('meetings_mom_actions_representatives')."\";");
                            $reprowid++;
                        }
                    }
                }
                if(empty($actions_users)) {
                    $checksum['users'] = generate_checksum('mom');
                    eval("\$actions_users .= \"".$template->get('meetings_mom_actions_users')."\";");
                }
                if(empty($actions_representatives)) {
                    $checksum['representatives'] = generate_checksum('mom');
                    eval("\$actions_representatives .= \"".$template->get('meetings_mom_actions_representatives')."\";");
                }
                eval("\$actions_rows .= \"".$template->get('meetings_mom_actions_rows')."\";");
                unset($checked, $actions_users, $actions_representatives);
                $arowid++;
            }
            $headerclass = 'thead';
            $title = $lang->specificfollowactions;
            eval("\$actions .= \"".$template->get('meetings_mom_actions')."\";");
        }
        else {
            if($action == 'edit') {
                /* parse Actions ---START */
                $arowid = 0;
                $userrowid = 0;
                $checksum['users'] = generate_checksum('mom');
                eval("\$actions_users = \"".$template->get('meetings_mom_actions_users')."\";");
                $reprowid = 0;
                $checksum['representatives'] = generate_checksum('mom');
                eval("\$actions_representatives = \"".$template->get('meetings_mom_actions_representatives')."\";");
                $checksum['actions'] = generate_checksum('mom');
                eval("\$actions_rows = \"".$template->get('meetings_mom_actions_rows')."\";");
                $headerclass = 'thead';
                $title = $lang->specificfollowactions;
                eval("\$actions .= \"".$template->get('meetings_mom_actions')."\";");
                /* parse Attachments ---END */
            }
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
        if(empty($meeting_list)) {
            $display = "inline-block";
        }
//        $multiple_meetings = Meetings::get_multiplemeetings(array('hasmom' => 0));
//        if(is_array($multiplemeetings)) {
//            if(empty($meeting_list)) {
//                $meeting_list = '<select name = "mof[mtid]">';
//                foreach($multiple_meetings as $mid => $meeting) {
//                    if(!empty($meeting['title'])) {
//                        $meeting_list .='<option value = "'.$meeting['mtid'].'"> '.$meeting['title'].' | '.$meeting['location'].'</option>';
//                    }
//                }
//                $meeting_list .= '</select>';
//            }
//        }
//        else {
//            $meeting_list = $lang->nomeetingavailable;
//        }


        /* parse Actions ---START */
        $arowid = 0;
        $userrowid = 0;
        $checksum['users'] = generate_checksum('mom');
        eval("\$actions_users = \"".$template->get('meetings_mom_actions_users')."\";");
        $reprowid = 0;
        $checksum['representatives'] = generate_checksum('mom');
        eval("\$actions_representatives = \"".$template->get('meetings_mom_actions_representatives')."\";");
        $checksum['actions'] = generate_checksum('mom');
        eval("\$actions_rows = \"".$template->get('meetings_mom_actions_rows')."\";");
        $headerclass = 'thead';
        $title = $lang->specificfollowactions;
        eval("\$actions .= \"".$template->get('meetings_mom_actions')."\";");
        /* parse Attachments ---END */
    }

    $fieldsdisplay['sharemeeting'] = 'style="display:none;"';
    if(isset($meeting['mtid']) && !empty($meeting['mtid'])) {
        $fieldsdisplay['sharemeeting'] = 'style="display:block;"';
    }
    $share_meeting .= '<span '.$fieldsdisplay['sharemeeting'].' id="sharemeeting_span"> <a href="#" id="sharemeeting_'.$meeting['mtid'].'_meetings/list_loadpopupbyid" rel="share_'.$meeting['mtid'].'" title="'.$lang->sharewith.'"><img src="'.$core->settings['rootdir'].'/images/icons/sharedoc.png" alt="'.$lang->sharewith.'" border="0"></a></span>';

    eval("\$setminutesmeeting = \"".$template->get('meetings_minutesofmeetings')."\";");
    output_page($setminutesmeeting);
}
elseif($core->input['action'] == 'do_add' || $core->input['action'] == 'do_edit') {
    if(empty($core->input['mof']['momid'])) {
        if(!empty($core->input ['mof']['mtid'])) {
            $meeting_obj = new Meetings($core->input['mof']['mtid']);
            if($meeting_obj->get_createdby()->get()['uid'] != $core->user['uid']) {
                output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
                exit;
            }
            $mom_obj = $meeting_obj->get_mom();
            if($mom_obj == false) {
                $mom_obj = new MeetingsMOM();
                $action = 'add';
            }
            else {
                $mom_obj = MeetingsMOM::get_data(array('mtid' => $core->input['mof']['mtid']));
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
        $mom_obj->create($core->input['mof']);
    }
    else {
        output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
        exit;
    }

    switch($mom_obj->get_errorcode()) {
        case 0:
            output_xml('<status>true</status><message>'.$lang->successfullysaved.'</message>');
            break;
        case 1:
            output_xml('<status>false</status><message>'.$lang->fillallrequiredfields.'</message>');
            break;
        default:
            output_xml('<status>false</status><message>'.$lang->errorsaving.'</message>');
            break;
    }
}
elseif($core->input['action'] == 'ajaxaddmore_meetingsactions') {
    $reprowid = $userrowid = 0;
    $altrow = alt_row($altrow);
    $checksum['actions'] = generate_checksum('mom');
    $checksum['representatives'] = generate_checksum('mom');
    $checksum['users'] = generate_checksum('mom');

    $arowid = $db->escape_string($core->input['value']) + 1;
    eval("\$actions_users .= \"".$template->get('meetings_mom_actions_users')."\";");
    eval("\$actions_representatives .= \"".$template->get('meetings_mom_actions_representatives')."\";");
    eval("\$actions_rows .= \"".$template->get('meetings_mom_actions_rows')."\";");

    echo $actions_rows;
}
elseif($core->input['action'] == 'ajaxaddmore_actionsusers') {
    $checksum['users'] = generate_checksum('mom');
    $altrow = alt_row($altrow);
    $arowid = $core->input['ajaxaddmoredata']['arowid'];
    $userrowid = $db->escape_string($core->input['value']) + 1;
    eval("\$actions_users_rows .= \"".$template->get('meetings_mom_actions_users')."\";");
    echo $actions_users_rows;
}
elseif($core->input['action'] == 'ajaxaddmore_actionsrepresentatives') {
    $altrow = alt_row($altrow);
    $checksum['representatives'] = generate_checksum('mom');
    $arowid = $core->input['ajaxaddmoredata']['arowid'];
    $reprowid = $db->escape_string($core->input['value']) + 1;
    eval("\$actions_representatives_rows .= \"".$template->get('meetings_mom_actions_representatives')."\";");
    echo $actions_representatives_rows;
}
?>
