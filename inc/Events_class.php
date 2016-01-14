<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Events Class
 * $id: Events.php
 * Created:        @tony.assaad    Oct 16, 2013 | 1:53:26 PM
 * Last Update:    @tony.assaad    Oct 16, 2013 | 1:53:26 PM
 */

/**
 * Description of Events
 *
 * @author tony.assaad
 */
class Events extends AbstractClass {
    protected $errorcode = 0;
    protected $data = array();

    const PRIMARY_KEY = 'ceid';
    const TABLE_NAME = 'calendar_events';
    const DISPLAY_NAME = 'title';
    const CLASSNAME = __CLASS__;
    const SIMPLEQ_ATTRS = 'ceid, title, description,fromDate,toDate,place,publishOnWebsite,isCreatedFromCMS,isPublic';
    const UNIQUE_ATTRS = 'alias';

    public function __construct($id = '', $simple = false, $options = array()) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;

        if($this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return false;
        }
        $fields = array('title', 'description', 'place', 'boothNum', 'type', 'isPublic', 'publishOnWebsite', 'isFeatured', 'isCreatedFromCMS');
        foreach($fields as $field) {
            $event_data[$field] = $data[$field];
        }
        $event_data['alias'] = generate_alias($data['title']);
        $event_data['identifier'] = substr(md5(uniqid(microtime())), 0, 10);
        $event_data['description'] = $event_data['description'];
        $event_data['fromDate'] = strtotime($data['fromDate'].' '.$data['fromTime']);
        $event_data['toDate'] = strtotime($data['toDate'].' '.$data['toTime']);
        $event_data['createdOn'] = TIME_NOW;
        $event_data['createdBy'] = $data['uid'] = $core->user['uid'];
        $event_data['isFeatured'] = $data['isFeatured'];
        $event_data['isPublic'] = $data['isPublic'];
        $event_data['refreshLogoOnWebsite'] = $data['refreshLogoOnWebsite'];
        $event_data['tags'] = $data['tags'];
        $event_data['lang'] = $data['lang'];
        $event_data['affid'] = $data['affid'];
        $event_data['spid'] = $data['spid'];
        unset($event_data['restrictto']);
        // $data['restricto'] = implode(',', $ $data['restricto']);
        // 'affid' => $core->input['event']['affid'];
        // 'spid' => $core->input['event']['spid'];
        parent::create($event_data);
        //$query = $db->insert_query(self::TABLE_NAME, $event_data);
        //$this->data = $event_data;
        //$this->data[self::PRIMARY_KEY] = $db->last_id();

        /* Parse incoming Attachemtns - START */
        $data['attachments'] = $_FILES['attachments'];

        if(!empty($data['attachments']['name'][0])) {
            $upload_param['upload_allowed_types'] = array('image/jpeg', 'image/gif', 'image/png', 'application/zip', 'application/pdf', 'application/x-pdf', 'application/msword', 'application/vnd.ms-powerpoint', 'text/plain', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.presentationml.presentation');
            if(is_array($data['attachments'])) {
                $upload_obj = new Uploader('attachments', $core->input, $upload_param['upload_allowed_types'], 'putfile', 5242880, 1, 1); //5242880 bytes = 5 MB (1024);
                $attachments_path = './uploads/eventsattachments';
                $upload_obj->set_upload_path($attachments_path);
                $upload_obj->process_file();
                $attachments = $upload_obj->get_filesinfo();

                if($upload_obj->get_status() != 4) {
                    ?>
                    <script language="javascript" type="text/javascript">
                        $(function () {
                            top.$("#upload_Result").html("<span class='red_text'><?php echo $upload_obj->parse_status($upload_obj->get_status());?></span>");
                        });
                    </script>
                    <?php
                    exit;
                }
            }
        }
        /* Parse incoming Attachemtns - END */
    }

    protected function update(array $data) {
        global $db, $core;

        if($this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return false;
        }
        $fields = array('title', 'description', 'place', 'boothNum', 'type', 'isPublic', 'publishOnWebsite', 'isFeatured', 'logo', 'isCreatedFromCMS');
        foreach($fields as $field) {
            $event_data[$field] = $data[$field];
        }
        $event_data['description'] = $event_data['description'];
        $event_data['fromDate'] = strtotime($data['fromDate'].' '.$data['fromTime']);
        $event_data['toDate'] = strtotime($data['toDate'].' '.$data['toTime']);
        $event_data['editedOn'] = TIME_NOW;
        $event_data['editedBy'] = $core->user['uid'];
        $event_data['isFeatured'] = $data['isFeatured'];
        $event_data['isPublic'] = $data['isPublic'];
        $event_data['refreshLogoOnWebsite'] = $data['refreshLogoOnWebsite'];
        $event_data['tags'] = $data['tags'];
        $event_data['lang'] = $data['lang'];
        $event_data['affid'] = $data['affid'];
        $event_data['spid'] = $data['spid'];
        unset($event_data['restrictto']);
        //'affid' => $core->input['event']['affid'],
        //'spid' => $core->input['event']['spid'],
        $db->update_query(self::TABLE_NAME, $event_data, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        $event_data[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
        $this->data = $event_data;
    }

    public function get_eventbypriority($attributes = array()) {
        global $db;
        $events_query = $db->query("SELECT  ce.*,ce.title AS eventtitle FROM ".Tprefix."calendar_events ce JOIN ".Tprefix."calendar_eventtypes cet ON(cet.cetid=ce.type)
						   WHERE ce.publishOnWebsite=1  AND  (".TIME_NOW." BETWEEN ce.fromDate  AND ce.toDate)
						   ORDER BY ce.fromDate, find_in_set(ce.".key($attributes).",'".$attributes[key($attributes)]."') DESC LIMIT 0,2");

        if($db->num_rows($events_query) > 0) {
            while($eventsrows = $db->fetch_assoc($events_query)) {
                $eventsrow[$eventsrows['cmsnid']] = $eventsrows;
            }
            return $eventsrow;
        }
    }

    public static function get_affiliatedevents($affiliates = array(), $options = array()) {
        global $db, $core;
        if(is_array($options)) {
            if(isset($options['ismain']) && $options['ismain'] === 1) {
                $query_where_add = ' AND isMain=1';
            }
        }
        $events_aff = $db->query("SELECT ce.* FROM ".Tprefix."calendar_events ce
								JOIN ".Tprefix."affiliatedemployees a ON (a.affid=ce.affid)
								WHERE a.uid=".$core->user['uid']." AND a.affid in (".(implode(',', $affiliates)).") ".$query_where_add." ");
        if($db->num_rows($events_aff) > 0) {
            while($aff_events = $db->fetch_assoc($events_aff)) {
                $affiliate_events[$aff_events['ceid']] = $aff_events;
            }
            return $affiliate_events;
        }
    }

    public static function get_events_bytype($type) {
        global $db;

        return $this->events = $db->fetch_assoc($db->query("SELECT  ce.*,ce.title AS eventtitle FROM ".Tprefix."calendar_events ce
								JOIN ".Tprefix."calendar_eventtypes cet ON(cet.cetid=ce.type)
								WHERE cet.name=".$db->escape_string($type).""));
    }

    public function get_invited_users() {
        global $db;
        $invitess_query = $db->query("SELECT ceiid, uid FROM ".Tprefix."calendar_events_invitees WHERE ceid=".intval($this->data['ceid']));
        if($db->num_rows($invitess_query) > 0) {
            while($invitee = $db->fetch_assoc($invitess_query)) {
                $invitees[$invitee['ceiid']] = new Users($invitee['uid']);
            }
            return $invitees;
        }
        return false;
    }

    public function get() {
        return $this->data;
    }

    protected function validate_requiredfields(array $data = array()) {
        global $core, $db, $errorhandler, $lang;
        if(is_array($data)) {
            $required_fields = array('title', 'description', 'fromDate', 'toDate');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    $errorhandler->record('requiredfields', $lang->$field);
                    return true;
                }
            }
        }
    }

    public function delete_event($todelete) {
        global $db;
        $attributes = array(static::PRIMARY_KEY);
        foreach($attributes as $attribute) {
            $tables = $db->get_tables_havingcolumn($attribute, 'TABLE_NAME !="'.static::TABLE_NAME.'"');
            if(is_array($tables)) {
                foreach($tables as $table) {
                    $query = $db->query("SELECT * FROM ".Tprefix.$table." WHERE ".$attribute."=".$todelete." ");
                    if($db->num_rows($query) > 0) {
                        if($table == CalendarEventsInvitees::TABLE_NAME) {
                            while($invitation = $db->fetch_assoc($query)) {
                                $calinvitee = new CalendarEventsInvitees($invitation['ceiid']);
                                $deletenotification = $calinvitee->delete_invitation();
                            }
                        }
                        else if($table == CalendarEventsRestrictions::TABLE_NAME) {
                            while($restriction = $db->fetch_assoc($query)) {
                                $calrestriction_obj = new CalendarEventsRestrictions($restriction[CalendarEventsRestrictions::PRIMARY_KEY]);
                                if(is_object($calrestriction_obj)) {
                                    $calrestriction_obj->delete();
                                }
                            }
                        }
                        else {
                            $this->errorcode = 3;
                            return false;
                        }
                    }
                }
            }
        }
        $delete = $this->delete();
        if($delete) {
            $this->errorcode = 0;
            return true;
        }
    }

    public function email_invitees() {
        global $core;
        if($core->input['event']['isPublic'] == 1 && $core->usergroup['calendar_canAddPublicEvents'] == 1) {
            if(isset($core->input['event']['restrictto'])) {
                if(is_array($core->input['event']['restrictto'])) {
                    foreach($core->input['event']['restrictto'] as $affid) {
                        $restriction = new CalendarEventsRestrictions();
                        $restriction->set(array('affid' => $affid, 'ceid' => $this->get_id()))->save();
                    }
                    if(isset($core->input['event']['notify']) && $core->input['event']['notify'] == 1) {
                        /* Send the event notification - START */
                        $notification_mails = get_specificdata('affiliates', array('affid', 'mailingList'), 'affid', 'mailingList', '', 0, 'mailingList != "" AND affid IN('.implode(',', $core->input['event']['restrictto']).')');

                        $ical_obj = new iCalendar(array('identifier' => $this->identifier.'all', 'uidtimestamp' => $this->createdOn));  /* pass identifer to outlook to avoid creation of multiple file with the same date */
                        $ical_obj->set_datestart($this->fromDate);
                        $ical_obj->set_datend($this->toDate);
                        $ical_obj->set_location($this->place);
                        $ical_obj->set_summary($this->title);
                        $ical_obj->set_name();
                        $ical_obj->set_status();
                        $ical_obj->set_transparency();
                        $ical_obj->set_icalattendees($notification_mails);
                        $ical_obj->set_description($this->description);
                        $ical_obj->endical();

                        $mailer = new Mailer();
                        $mailer = $mailer->get_mailerobj();
                        $mailer->set_type('ical', array('content-class' => 'meetingrequest', 'method' => 'REQUEST', 'filename' => $this->title.'.ics'));
                        $mailer->set_from(array('name' => 'Orkila Events Notifier', 'email' => 'events@orkila.com'));
                        $mailer->set_subject($this->title);
                        $mailer->set_message($ical_obj->geticalendar());
                        $mailer->set_to($notification_mails);

                        /* Add multiple Attachments */
                        if(is_array($attachments)) {
                            foreach($attachments as $attachment) {
                                $mailer->add_attachment($attachments_path.'/'.$attachment['name']);
                            }
                        }
                        $mailer->send();

                        if($mailer->get_status() === true) {
                            $log->record($notification_mails, $last_id);
                        }
                        else {
                            $errors['notification'] = false;
                        }
                        /* Send the event notification - END */
                    }
                }
            }
        }
    }

}
?>