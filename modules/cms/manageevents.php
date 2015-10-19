<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: manageevents.php
 * Created:        @rasha.aboushakra    Jan 27, 2015 | 9:18:40 AM
 * Last Update:    @rasha.aboushakra    Jan 27, 2015 | 9:18:40 AM
 */

if(!defined('DIRECT_ACCESS')) {
    die('Direct initialization of this file is not allowed.');
}
if(!$core->input['action']) {
    if(isset($core->input['id']) && !empty($core->input['id'])) {
        $event_obj = Events::get_data(array('ceid' => $core->input['id']), array('simple' => false));
        if(is_object($event_obj)) {
            $event = $event_obj->get();
            if($event['isFeatured'] == 1) {
                $checkedbox['isFeatured'] = "checked='checked'";
            }
            $url = 'http://'.$core->settings['websitedir'].'/events/'.$event_obj->alias.'/'.base64_encode($event_obj->ceid).'/'.$event_obj->identifier.'/preview';
            $event['fromDate_output'] = date($core->settings['dateformat'], $event['fromDate']);
            $event['toDate_output'] = date($core->settings['dateformat'], $event['toDate']);
            $event['toTime_output'] = gmdate("H:i:s", strtotime(date($core->settings['timeformat'], $event['toDate'])));
            $event['fromTime_output'] = gmdate("H:i:s", strtotime(date($core->settings['timeformat'], $event['fromDate'])));
            $disabled['alias'] = 'readonly="readonly"';
        }
    }

    $eventtypes = CalendarEventTypes::get_data('');
    $eventtypes_list = parse_selectlist('event[type]', '', $eventtypes, $event['type']);
    if($core->usergroup['canViewAllAff'] == 0) {
        $inaffiliates = implode(', ', $core->user['affiliates']);
        $affiliate_where = 'affid IN ('.$inaffiliates.')';
    }
    $affiliates = get_specificdata('affiliates', array('affid', 'name'), 'affid', 'name', array('by' => 'name', 'sort' => 'ASC'), 0, $affiliate_where);
    $eventaffiliates_selectlist = parse_selectlist('event[affid]', 2, $affiliates, $event['affid'], '', '', array('blankstart' => 1));
    $affiliates_selectlist = parse_selectlist('event[restrictto][]', 1, $affiliates, '', 1);
    if($core->usergroup['calendar_canAddPublicEvents'] == 1) {
        $restriction_selectlist = '<div style = "display:block;padding-top:5px;"><div style = "width:15%; display:inline-block; vertical-align:top;">'.$lang->restricto.'</div><div style = "width:70%; display:inline-block;">'.$affiliates_selectlist.'</div></div>';
        $notifyevent_checkbox = '<div style = "display:block;padding-top:5px;"><div style = "width:15%; display:inline-block;">'.$lang->notifyevent.'</div><div style = "width:70%; display:inline-block;"><input name = "event[notify]" type = "checkbox" value = "1" /></div></div>';
    }

    /* parse invitees - START */
    $inviteesids = array();
    if(is_object($event_obj)) {
        $invitees = $event_obj->get_invited_users();
        if(is_array($invitees)) {
            $inviteesids = array_unique(array_map(function($e) {
                        return is_object($e) ? $e->uid : null;
                    }, $invitees));
        }
    }
    $affiliatedemployees = AffiliatedEmployees::get_data(array('affid' => $core->user['affiliates']));
    $uids = array_unique(array_map(function($e) {
                return is_object($e) ? $e->uid : null;
            }, $affiliatedemployees));
    $users = Users::get_data(array('uid' => $uids), array('operators' => array('uid' => 'IN'), 'order' => array('by' => 'displayName')));
    if(is_array($users)) {
        foreach($users as $key => $value) {
            if($key == 0) {
                continue;
            }
            $checked = $rowclass = '';
            if(in_array($key, $inviteesids)) {
                $checked = 'checked="checked"';
                $rowclass = 'altrow2';
            }

            $invitees_list .= '<tr class="'.$rowclass.'">';
            $invitees_list .= '<td><input id="affiliatefilter_check_'.$key.'" name="event[invitees][]" type="checkbox"'.$checked.' value="'.$key.'">'.$value.'</td></tr>';
        }
    }

    if(!empty($event['logo'])) {
        $currentlogo = '<img src = "./uploads/eventslogos/'.$event['logo'].'">';
    }
    eval("\$createevent=\"".$template->get('cms_events_add')."\";");
    output_page($createevent);
}
else {
    if($core->input['action'] == 'do_perform_manageevents') {
        unset($core->input['identifier'], $core->input['module'], $core->input['action']);

        if(empty($core->input['event']['alias'])) {
            $core->input['event']['alias'] = generate_alias($core->input['event']['title']);
        }

        if(!empty($core->input['event'][Events::PRIMARY_KEY])) {
            $cms_event = new Events($core->input['event'][Events::PRIMARY_KEY]);
        }
        else {
            $cms_event = Events::get_data(array('alias' => $core->input['event']['alias']));
        }
        if(!is_object($cms_event)) {
            $cms_event = new Events();
        }
        else {
            if(empty($core->input['event'][Events::PRIMARY_KEY])) {
                echo $lang->entryexists;
                exit;
            }
        }

        $core->input['event']['publishOnWebsite'] = 0;
        if($core->usergroup['cms_canPublishNews'] == 1) {
            $core->input['event']['publishOnWebsite'] = 1;
        }
        $core->input['event']['isCreatedFromCMS'] = 1;
        $cms_event->set($core->input['event']);
        $cms_event->save();

        /* Parse Event Logo - START */
        if(!empty($_FILES['logo']['name'][0])) {
            $_FILES['logo']['newname'][0] = $core->input['event']['alias'];
            $upload_param['upload_allowed_types'] = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png');
            $upload_obj = new Uploader('logo', $_FILES, $upload_param['upload_allowed_types'], 'putfile', 5242880, 1, 1); //5242880 bytes = 5 MB (1024);
            $logo_path = './uploads/eventslogos';
            $upload_obj->set_upload_path($logo_path);
            $upload_obj->process_file();
            $upload_obj->resize(150, '');

            $logo = $upload_obj->get_filesinfo();
            $cms_event->set(array('logo' => $upload_obj->get_filename(), 'refreshLogoOnWebsite' => 1));
            $cms_event->save();
            if($upload_obj->get_status() != 4) {
                echo $upload_obj->parse_status($upload_obj->get_status(), $cms_event);
                exit;
            }
        }

        /* Add event Invitee */
        if(is_array($core->input['event']['invitees'])) {
            foreach($core->input['event']['invitees'] as $invitee) {
                if(empty($invitee)) {
                    continue;
                }
                $new_event_invitee_data = array(
                        'ceid' => $cms_event->get_id(),
                        'uid' => $invitee,
                        'createdOn' => TIME_NOW,
                        'createdBy' => $core->user['uid']
                );
                $invitee = new CalendarEventsInvitees();
                $invitee->set($new_event_invitee_data);
                $invitee->save();
            }
        }

        /* Get invitess by user */
        $event_users_objs = $cms_event->get_invited_users();
        if(is_array($event_users_objs)) {
            foreach($event_users_objs as $event_users_obj) {
                $event_users = $event_users_obj->get();
                /* iCal event to the users */
                $ical_obj = new iCalendar(array('identifier' => $cms_event->identifier, 'uidtimestamp' => $cms_event->createdOn));  /* pass identifer to outlook to avoid creation of multiple file with the same date */
                $ical_obj->set_datestart($cms_event->fromDate);
                $ical_obj->set_datend($cms_event->toDate);
                $ical_obj->set_location($cms_event->place);
                $ical_obj->set_summary($cms_event->title);
                $ical_obj->set_categories('Event');
                $ical_obj->set_organizer();
                $ical_obj->set_icalattendees($event_users['uid']);
                $ical_obj->set_description($cms_event->description);
                $ical_obj->endical();

                $mailer = new Mailer();
                $mailer = $mailer->get_mailerobj();
                $mailer->set_type('ical', array('content-class' => 'meetingrequest', 'method' => 'REQUEST'));
                $mailer->set_from(array('name' => 'OCOS Mailer', 'email' => $core->settings['maileremail']));
                $mailer->set_subject($cms_event->title);
                $mailer->set_message($ical_obj->geticalendar());
                $mailer->set_to($event_users['email']);

                /* Add multiple Attachments */
                if(is_array($attachments)) {
                    foreach($attachments as $attachment) {
                        $mailer->add_attachment($attachments_path.'/'.$attachment['name']);
                    }
                }
                //$mailer->send();
            }
        }

        $cms_event->email_invitees();

        switch($cms_event->get_errorcode()) {
            case 0:
                echo $lang->successfullysaved;
                exit;
            case 1:
                echo $lang->fillrequiredfields;
                exit;
        }
        /* Parse Event Logo - END */
    }
    elseif($core->input['action'] == 'togglepublish') {
        if($core->usergroup['cms_canPublishNews'] == 1 && !empty($core->input['id'])) {
            $event = new Events($core->input['id']);
            $db->update_query(Events::TABLE_NAME, array('publishOnWebsite' => !$event->publishOnWebsite), Events::PRIMARY_KEY.'='.intval($core->input['id']));
        }
        redirect('index.php?module=cms/eventlist');
    }
}