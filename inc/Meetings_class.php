<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Meetings.php
 * Created:        @tony.assaad    Nov 7, 2013 | 3:09:17 PM
 * Last Update:    @tony.assaad    Nov 7, 2013 | 3:09:17 PM
 */

/**
 * Description of Meetings
 *
 * @author tony.assaad
 */
class Meetings {
    private $meeting = array();
    private $errorcode = 0;

    public function __construct($id = '', $simple = false) {
        if(isset($id) && !empty($id)) {
            $this->meeting = $this->read($id, $simple);
        }
    }

    private function read($id, $simple = false) {
        global $db;
        $query_select = '*';
        if($simple == true) {
            $query_select = 'mtid, title, identifier, description';
        }

        return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."meetings WHERE mtid=".$db->escape_string($id)));
    }

    public function is_sharedwithuser() {
        global $core;
        if(value_exists('meetings_sharedwith', 'uid', $core->user['uid'], 'mtid='.intval($this->meeting['mtid']))) {
            return true;
        }
        return false;
    }

    public function create($meeting_data = array()) {
        global $db, $core, $log;
        if(is_array($meeting_data)) {
            if(!is_empty($meeting_data['fmfid'])) {
                $fmfid = intval($meeting_data['fmfid']);
            }
            $this->meeting = $meeting_data;
            if(empty($this->meeting['title'])) {
                $this->errorcode = 1;
                return false;
            }

            if(value_exists('meetings', 'title', $this->meeting['title'], ' createdBy='.$core->user['uid'].'')) { // Add date filter
                $this->errorcode = 4;
                return false;
            }

            if(!empty($meeting_data['altfromDate'])) {
                $fromdate = explode('-', $meeting_data['altfromDate']);

                if(checkdate($fromdate[1], $fromdate[0], $fromdate[2])) {
                    $this->meeting['fromDate'] = strtotime($this->meeting['altfromDate'].' '.$this->meeting['fromTime']);
                    $this->meeting['toDate'] = strtotime($this->meeting['alttoDate'].' '.$this->meeting['toTime']);
                }
            }
            if($meeting_data['fromDate'] > $meeting_data['toDate']) {
                $this->errorcode = 3;
                return false;
            }

            if(is_empty($this->meeting['title'], $this->meeting['fromDate'], $this->meeting['toDate'], $this->meeting['fromTime'], $this->meeting['toTime'])) {
                $this->errorcode = 1;
                return false;
            }

            if(value_exists('meetings', 'title', $this->meeting['title'], 'createdBy='.$core->user['uid'])) { /* ADD TIME CHECK, OTHERWISE OKAY */
                $this->errorcode = 2;
                return false;
            }

            /* Check if meeting intersects with another for the same user - START */
            /* Check if meeting intersects with another for the same user - END */

            $this->meeting['title'] = ucwords(strtolower($this->meeting['title']));

            $sanitize_fields = array('title', 'fromDate', 'toDate');
            foreach($sanitize_fields as $val) {
                $this->meeting[$val] = $core->sanitize_inputs($this->meeting[$val], array('removetags' => true));
            }

            $meeting_data = array(
                    'title' => $this->meeting['title'],
                    'fmfid' => $this->meeting['fmfid'],
                    'identifier' => substr(md5(uniqid(microtime())), 1, 10),
                    'fromDate' => $this->meeting['fromDate'],
                    'toDate' => $this->meeting['toDate'],
                    'description' => $this->meeting['description'],
                    'location' => $this->get_location(),
                    'createdBy' => $core->user['uid'],
                    'createdOn' => TIME_NOW
            );

            $insertquery = $db->insert_query('meetings', $meeting_data);
            if($insertquery) {
                $this->meeting['mtid'] = $db->last_id();
                if(!is_empty($fmfid)) {
                    $facilityreservation = array('fmfid' => $fmfid, 'mtid' => $this->meeting['mtid'], 'fromDate' => $meeting_data['fromDate'], 'toDate' => $meeting_data['toDate'], 'purpose' => $lang->meeting, 'reservedBy' => $meeting_data['createdBy']);
                    $facilityreservation_obj = new FacilityMgmtReservations();
                    $facilityreservation_obj->set($facilityreservation);
                    $facilityreservation_obj->save();
                }
                $this->meeting['identifier'] = $meeting_data['identifier'];
                $log->record('addedmeeting', $this->meeting['mtid']);
                //$this->get_meetingassociations($this->meeting['mtid'])->set_associations($this->meeting['associations']);

                $this->set_associations($this->meeting['associations']);
                /* insert meetings Attendees */
                $this->set_attendees($this->meeting['attendees']);
                if(isset($this->meeting['attachments']) && !empty($this->meeting['attachments'])) {
                    $this->add_attachments($this->meeting['attachments']);
                }
                /* we need to explain code here */
                if(!($meeting_data['fromDate'] < TIME_NOW)) {
                    $this->send_invitations();
                }
                $this->errorcode = 0;
                return true;
            }
        }
    }

    public function add_attachments($attachments) {
        foreach($attachments['attachments'] as $field => $items) {
            foreach($items as $id => $item) {
                $transposed_attachments[$id][$field] = $item;
            }
        }

        if(is_array($transposed_attachments)) {
            foreach($transposed_attachments as $attachmentraw) {
                $meetingsattachments_obj = new MeetingsAttachments();

                foreach($attachmentraw as $key => $val) {
                    $attachment[$key][0] = $val;
                }
                $meetingsattachments_obj->add($attachment, $this->meeting['mtid']);
            }
        }
    }

    public function send_invitations() {
        global $core, $log;

        if($this->meeting['notifyuser'] == 1) {
            $filters[] = 'uid';
        }
        if($this->meeting['notifyrep'] == 1) {
            $filters[] = 'rpid';
        }

        if(!empty($filters)) {
            $attendes_objs = $this->get_attendees(array('atttypes' => $filters));
            if(is_array($attendes_objs)) {


                foreach($attendes_objs as $key => $attendes_obj) {
                    if($attendes_obj->is_representative()) {
                        $receipient_attendees[$key] = $attendes_obj->get_rep()->get();
                        $email_data['to'][] = $receipient_attendees[$key]['email'];
                    }
                    else {
                        $receipient_attendees[$key] = $attendes_obj->get_user()->get();
                        $receipient_attendees[$key]['name'] = $receipient_attendees[$key]['displayName'];
                        $email_data['to'][] = $receipient_attendees[$key]['email'];
                    }
                }

                if(is_array($receipient_attendees)) {
                    $ical_obj = new iCalendar(array('identifier' => $this->meeting['identifier'], 'uidtimestamp' => $this->meeting['createdOn'], 'component' => 'event', 'method' => 'REQUEST'));  /* pass identifer to outlook to avoid creation of multiple file with the same date */
                    $ical_obj->set_datestart($this->meeting['fromDate']);
                    $ical_obj->set_datend($this->meeting['toDate']);
                    $ical_obj->set_location($this->get_location());
                    $ical_obj->set_summary($this->meeting['title']);
                    $ical_obj->set_categories('Appointment');
                    $ical_obj->set_organizer();
                    $ical_obj->set_icalattendees($receipient_attendees);
                    $ical_obj->set_description($this->meeting['description']);
                    $ical_obj->endical();

                    //$email_data['message'] = $ical_obj->geticalendar();


                    $mailer = new Mailer();
                    $mailer = $mailer->get_mailerobj();
                    $mailer->set_type('ical', array('content-class' => 'appointment', 'method' => 'REQUEST'));
                    $mailer->set_from(array('name' => $core->user['displayName'], 'email' => $core->user['email']));
                    $mailer->set_subject($this->meeting['title']);
                    $mailer->set_message($ical_obj->geticalendar());
                    $mailer->set_to($email_data['to']);

                    /* Add multiple Attachments */
                    $meeting_attachobjs = $this->get_attachments();
                    if(is_array($meeting_attachobjs)) {
                        $attachments_path = './uploads/meetings';
                        foreach($meeting_attachobjs as $meeting_attachobj) {
                            $attachment = $meeting_attachobj->get();
                            $mailer->add_attachment($attachments_path.'/'.$attachment['filename'], $attachment['type'], array('filename' => $attachment['title']));
                        }
                    }

                    $mailer->send();
                }

                $log->record('meetings_appointment', array('to' => $receipient_attendees));
                unset($receipient_attendees);
                return true;
            }
        }
        return false;
    }

    private function set_attendees(array $attendees) {
        global $core;
        unset($attendees['notifyuser'], $attendees['notifyrep']);
        if(empty($attendees)) {
            $attendees = $this->meeting['attendees'];
        }

        //if(!isset($attendees)) {
        $attendees['uid'][] = array(array('idAttr' => 'uid', 'mtid' => $this->meeting['mtid'], 'id' => $core->user['uid']));
        //}

        if(!empty($attendees)) {
            foreach($attendees as $type => $type_attendees) {
                foreach($type_attendees as $key => $attendee) {
                    if(empty($attendee['id'])) {
                        continue;
                    }
                    $new_attendee['mtid'] = $this->meeting['mtid'];
                    $new_attendee['idAttr'] = $type;
                    $new_attendee['attendee'] = intval($attendee['id']);
                    MeetingsAttendees::set_attendee($new_attendee);
                }
            }
        }
    }

    private function set_associations($associations = '') {
        if(empty($associations)) {
            $associations = $this->meeting['associations'];
        }
        //array_keys($associations)
        if(is_array($associations)) {
            foreach($associations as $key => $association) {
                if(empty($association)) {
                    continue;
                }
                if(is_array($association)) {
                    foreach($association as $id => $val) {
                        if(empty($val)) {
                            continue;
                        }
                        $new_associations['idAttr'] = $key;
                        $new_associations['id'] = $val;
                        $new_associations['mtid'] = $this->meeting['mtid'];
                        MeetingsAssociations::set_association($new_associations);
                    }
                }
                else {
                    $new_association['mtid'] = $this->meeting['mtid'];
                    $new_association['idAttr'] = $key;
                    $new_association['id'] = $association;
                    MeetingsAssociations::set_association($new_association);
                }
            }
        }
    }

    public function update($meeting_data = array()) {
        global $db, $log, $core;
        if(!is_empty($meeting_data['fmfid'])) {
            $fmfid = intval($meeting_data['fmfid']);
        }
        else {
            $meeting_data['fmfid'] = 0;
        }
        $this->meeting['mtid'] = $this->meeting['mtid'];

        $this->meeting['notifyuser'] = $meeting_data['notifyuser'];

        $this->meeting['notifyrep'] = $meeting_data['notifyrep'];

        $associations = $meeting_data['associations'];
        $attendees = $meeting_data['attendees'];
        $this->meeting['attachments'] = $meeting_data['attachments'];
        if(!empty($meeting_data['altfromDate'])) {
            $fromdate = explode('-', $meeting_data['altfromDate']);

            if(checkdate($fromdate[1], $fromdate[0], $fromdate[2])) {
                $meeting_data['fromDate'] = strtotime($meeting_data['altfromDate'].' '.$meeting_data['fromTime']);
                $meeting_data['toDate'] = strtotime($meeting_data['alttoDate'].' '.$meeting_data['toTime']);
            }
        }
        if($meeting_data['fromDate'] > $meeting_data['toDate']) {
            $this->errorcode = 3;
            return false;
        }
        if($meeting_data['fromDate'] > $meeting_data['toDate']) {
            $this->errorcode = 3;
            return false;
        }
        if(is_empty($meeting_data['title'], $meeting_data['fromDate'], $meeting_data['toDate'], $meeting_data['fromTime'], $meeting_data['toTime'])) {
            $this->errorcode = 1;
            return false;
        }

        unset($meeting_data['attendees'], $meeting_data['attachments'], $meeting_data['associations'], $meeting_data['notifyuser'], $meeting_data['notifyrep']);



        unset($meeting_data['fromTime'], $meeting_data['toTime'], $meeting_data['altfromDate'], $meeting_data['alttoDate']);
        if(!isset($meeting_data['isPublic'])) {
            $meeting_data['isPublic'] = 0;
        }
        $meeting_data['modifiedBy'] = $core->user['uid'];
        $meeting_data['modifiedOn'] = TIME_NOW;

        $query = $db->update_query('meetings', $meeting_data, 'mtid='.intval($this->meeting['mtid']));
        if($query) {
            if(!is_empty($fmfid)) {
                $facilityreservation = array('fmfid' => $fmfid, 'mtid' => intval($this->meeting['mtid']), 'fromDate' => $meeting_data['fromDate'], 'toDate' => $meeting_data['toDate'], 'purpose' => $meeting_data['description'], 'reservedBy' => $meeting_data['createdBy']);
                $facilityreservation_obj = new FacilityMgmtReservations();
                $facilityreservation_obj->set($facilityreservation);
                $facilityreservation_obj->save();
            }
            else {
                $facilityreservation_obj = FacilityMgmtReservations::get_data(array('mtid' => intval($this->meeting['mtid'])));
                if(is_object($facilityreservation_obj) && !empty($facilityreservation_obj->fmrid)) {
                    $facilityreservation_obj->delete();
                }
            }
            if(isset($this->meeting['attachments']) && !empty($this->meeting['attachments'])) {
                $this->add_attachments($this->meeting['attachments']);
            }
            $this->meeting = $meeting_data + $this->meeting;
            if(is_array($attendees)) {
                foreach($attendees as $type => $type_attendees) {
                    foreach($type_attendees as $attendee) {
                        if(is_array($attendee)) {
                            if(!empty($attendee['matid'])) {
                                $meetingatt_obj = new MeetingsAttendees($attendee['matid']);
                                if(!isset($attendee['id']) || empty($attendee['id'])) {
                                    $meetingatt_obj->delete();
                                }
                                else {
                                    $meetingatt_obj->update($attendee);
                                }
                            }
                            else {
                                $new_attendee['mtid'] = $this->meeting['mtid'];
                                $new_attendee['idAttr'] = $type;
                                $new_attendee['attendee'] = intval($attendee['id']);
                                MeetingsAttendees::set_attendee($new_attendee);
                            }
                        }
                    }
                }
                if(!($meeting_data['fromDate'] < TIME_NOW)) {
                    $this->send_invitations();
                }
            }

            $db->delete_query('meetings_associations', 'mtid='.intval($this->meeting['mtid']));
            $this->set_associations($associations);
            $log->record('updatedmeeting', $this->meeting['mtid']);
            $this->errorcode = 0;
            return true;
        }
    }

    public static function get_multiplemeetings(array $options = array()) {
        global $db, $core;
        $sort_query = 'fromDate DESC';
        if(isset($options['order']['sortby'], $options['order']['order']) && !is_empty($options['order']['sortby'], $options['order']['order'])) {
            $sort_query = $options['order']['sortby'].' '.$options['order']['order'];
        }

        $query_where_and = ' AND ';
        if(isset($options['hasmom'])) {
            $query_where = ' WHERE hasMOM='.intval($options['hasmom']);
        }
        else {
            $query_where_and = ' WHERE ';
        }

        if($options['filter_where']) {
            $query_where .= $query_where_and.$options['filter_where'];
            $query_where_and = ' AND ';
        }

        if($core->usergroup['meetings_canViewAllMeetings'] == 0) {
            $query_where .= $query_where_and.'(createdBy='.$core->user['uid'].' OR isPublic=1';
            $meetings_sharedwith = Meetings::get_meetingsshares_byuser();
            if(is_array($meetings_sharedwith)) {
                $query_where .= ' OR mtid IN ('.implode(', ', array_keys($meetings_sharedwith)).')';
            }
            $query_where .= ')';
        }
        $meetingsquery = $db->query("SELECT * FROM ".Tprefix."meetings{$query_where} ORDER BY {$sort_query}");

        if($db->num_rows($meetingsquery) > 0) {
            while($rowmeetings = $db->fetch_assoc($meetingsquery)) {
                $meeting[$rowmeetings['mtid']] = $rowmeetings;
            }
        }
        return $meeting;
    }

    public static function get_meetingsshares_byuser($uid = '') {
        global $core, $db;
        if(empty($uid)) {
            $uid = $core->user['uid'];
        }

        $query = $db->query('SELECT mtid FROM '.Tprefix.'meetings_sharedwith WHERE uid='.intval($uid));
        if($db->num_rows($query) > 0) {
            while($share = $db->fetch_assoc($query)) {
                $shares[$share['mtid']] = new Meetings($share['mtid']);
            }
            return $shares;
        }
        return false;
    }

    public function get_attendees($filters = array()) {
        global $db;

        if(is_array($filters['atttypes'])) {
            $filter_where = ' WHERE idAttr IN("'.implode('","', $filters[atttypes]).'") AND mtid='.intval($this->meeting['mtid']).'';
        }
        else {
            $filter_where = ' WHERE mtid='.intval($this->meeting['mtid']).'';
        }
        $query = $db->query('SELECT matid FROM '.Tprefix.'meetings_attendees '.$filter_where.'');
        if($db->num_rows($query)) {
            while($rowattendee = $db->fetch_assoc($query)) {
                $attendees[$rowattendee['matid']] = new MeetingsAttendees($rowattendee['matid']);
            }
            return $attendees;
        }
        return false;
    }

    public function parse_attendees($displayas = 'line') {
        $attendees_objs = $this->get_attendees();
        if(is_array($attendees_objs)) {
            foreach($attendees_objs as $id => $attendee) {
                if($attendee->is_representative()) {
                    $attendees[] = $attendee->get_attendee()->get()['name'];
                }
                else {
                    $attendees[] = $attendee->get_attendee()->get()['displayName'];
                }
            }

            if($displayas == 'list') {
                return '<ul><li>'.implode('</li><li>', $attendees).'</li></ul>';
            }
            else {
                return implode(', ', $attendees);
            }
        }
        return false;
    }

    public function can_viewmeeting() {
        global $core;
        if($core->usergroup['meetings_canViewAllMeetings'] == 0) {
            if($this->meeting['isPublic'] == 0) {
                if($this->meeting['createdBy'] != $core->user['uid']) {
                    if(!value_exists('meetings_sharedwith', 'mtid', $this->meeting['mtid'], 'uid='.$core->user['uid'])) {
                        return false;
                    }
                    else {
                        return true;
                    }
                }
                else {
                    return true;
                }
            }
            else {
                return true;
            }
        }
        else {
            return true;
        }
    }

    public function share($meeting_data = array()) {
        global $db, $core;
        if(is_array($meeting_data)) {
            foreach($meeting_data as $key => $val) {
                if(empty($val)) {
                    continue;
                }
                /* get exist users for the current meeting */
                $existing_users = $this->get_shared_users();
                /* get the difference between the exist users and the slected users */
                if(is_array($existing_users)) {
                    $existing_users = array_keys($existing_users);
                    $users_toremove = array_diff($existing_users, $meeting_data);
                    if(!empty($users_toremove)) {
                        $db->delete_query('meetings_sharedwith', 'uid IN ('.$db->escape_string(implode(',', $users_toremove)).') AND mtid='.$this->meeting['mtid']);
                    }
                }
                $meeting_shares['mtid'] = $this->meeting['mtid'];
                $meeting_shares['createdBy'] = $core->user['uid'];
                $meeting_shares['createdOn'] = TIME_NOW;
                $meeting_shares['uid'] = $core->sanitize_inputs($val);
                if(!value_exists('meetings_sharedwith', 'uid', $val, ' mtid='.$this->meeting['mtid'])) {
                    $db->insert_query('meetings_sharedwith', $meeting_shares);
                    $this->notify_shareduser($meeting_shares['uid']);
                    $this->errorcode = 0;
                }
            }
            /* Share meeting with owner */
            $createdby = $this->get_createdby();
            $this->notify_shareduser($createdby);

            //$this->send_mom();
        }
    }

    private function notify_shareduser($uid) {
        global $core, $lang;

        $user_obj = new Users($uid);
        if(!is_object($user_obj)) {
            return false;
        }
        $share_user = $user_obj->get();

        $lang->load('messages');
        $meetinglink = '<a href="'.DOMAIN.'/index.php?module=meetings/viewmeeting&amp;referrer=list&amp;mtid='.$this->meeting['mtid'].'">'.$this->meeting['title'].'</a>';
        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();
        $mailer->set_subject($lang->sprint($lang->meetings_sharemeeting_subject, $this->meeting['title']));
        $mailer->set_message($lang->sprint($lang->meetings_sharemeeting_message, $share_user['displayName'], $meetinglink));
        $mailer->set_from(array('name' => $core->user['displayName'], 'email' => $core->user['email']));
        $mailer->set_to($share_user['email']);
        $mailer->send();
    }

    public function send_mom($type = 'regular') {
        global $core;
        $mom = $this->get_mom();

        $email_data = array(
                'from_email' => $core->settings['maileremail'],
                'from' => 'OCOS Mailer',
                'subject' => $this->meeting['title']
        );

        $users = $this->get_shared_users();
        foreach($users as $user) {
            $email_data['to'][] = $user->get()['email'];
        }

        if($type == 'ical') {
            $ical_obj = new iCalendar(array('identifier' => $this->meeting['identifier'].'mom', 'uidtimestamp' => $this->meeting['createdOn'], 'component' => 'journal'));
            $ical_obj->set_summary($this->meeting['title']);
            $ical_obj->set_description($this->meeting['description']);
            $ical_obj->set_relatedto($ical_obj->parse_datestamp($this->meeting['createdOn']).'-'.$this->meeting['identifier'].'-@orkila.com');
            $ical_obj->endical();

            $email_data['message'] = $ical_obj->geticalendar();
            $mail = new Mailer($email_data, 'php', true, array(), array('content-class' => 'appointment'));
        }
        else {
            $email_data['message'] = '';
            $mail = new Mailer($email_data, 'php');
        }
    }

    public function get_shared_users() {
        global $db;

        $query = $db->query('SELECT uid FROM '.Tprefix.'meetings_sharedwith WHERE mtid='.$db->escape_string($this->meeting['mtid'].''));
        if($db->num_rows($query)) {
            while($user = $db->fetch_assoc($query)) {
                $users[$user['uid']] = new Users($user['uid']);
            }
            return $users;
        }
        return false;
    }

    public function get_attachments() {
        global $db;
        $query = $db->query('SELECT mattid FROM '.Tprefix.'meetings_attachments WHERE mtid='.$db->escape_string($this->meeting['mtid'].''));
        if($db->num_rows($query)) {
            while($attachment = $db->fetch_assoc($query)) {
                $attachments[$attachment['mattid']] = new MeetingsAttachments($attachment['mattid']);
            }
            return $attachments;
        }
        return false;
    }

    public function get_createdby() {
        return new Users($this->meeting['createdBy']);
    }

    public function get_modifiedby() {
        return new Users($this->meeting['modifiedBy']);
    }

    public function get_mom() {
        return MeetingsMOM::get_mom_bymeeting($this->meeting['mtid']);
    }

    public function get_errorcode() {
        return $this->errorcode;
    }

    public function get_meetingassociations() {
        global $db;
        /* Get all associatiosn related to this meeting */
        $query = $db->query('SELECT * FROM '.Tprefix.'meetings_associations WHERE mtid = '.$db->escape_string($this->meeting['mtid'].''));
        if($db->num_rows($query)) {
            while($meeting_assoc = $db->fetch_assoc($query)) {
                $meeting_associations[$meeting_assoc['mtaid']] = new MeetingsAssociations($meeting_assoc['mtaid']);
            }
            return $meeting_associations;
        }
        return false;
    }

    public function __get($name) {
        if(isset($this->meeting[$name])) {
            return $this->meeting[$name];
        }
        return false;
    }

    public function get() {
        return $this->meeting;
    }

    public function get_location() {
        if(isset($this->meeting['fmfid']) && !is_empty($this->meeting['fmfid'])) {
            $facility = $this->get_facility();
            if(is_object($facility) && !is_empty($facility->fmfid)) {
                return $facility->getfulladdress();
            }
        }
        else {
            return $this->meeting['location'];
        }
    }

    public function get_facility() {
        if(isset($this->meeting['fmfid']) && !empty($this->meeting['fmfid'])) {
            return new FacilityMgmtFacilities($this->meeting['fmfid']);
        }
        return false;
    }

}
?>