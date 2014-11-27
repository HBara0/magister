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
    protected $status = 0;
    protected $data = array();

    const PRIMARY_KEY = 'ceid';
    const TABLE_NAME = 'calendar_events';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;
    const SIMPLEQ_ATTRS = 'ceid, title, description,fromDate,toDate,place';

    public function __construct($id = '', $simple = false, $options = array()) {
        parent::__construct($id, $simple);
    }

//    protected function read($id, $simple, $options = array()) {
//        global $db;
//        if(empty($id)) {
//            return false;
//        }
//
//        $query_select = 'ceid, title, identifier, description, type';
//        if($simple == false) {
//            $query_select = '*';
//        }
//
//        if(isset($options['privateonly'])) {
//            $query_where = ' AND isPublic=1';
//            if($options['privateonly'] == true) {
//                $query_where = ' AND isPublic=0';
//            }
//        }
//        return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."calendar_events WHERE ceid=".$db->escape_string($id).$query_where));
//    }

    protected function create(array $data) {

    }

    public function save(array $data = array()) {

    }

    protected function update(array $data) {

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
        $invitess_query = $db->query("SELECT ceiid, uid FROM ".Tprefix."calendar_events_invitees WHERE ceid=".$db->escape_string($this->data['ceid']));
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

    public function uploadlogo() {
        if($this->data['publishOnWebsite'] == 1) {
            $cms = new Cms('db');
            $settings = $cms->get_settings();
            $upload_param['upload_allowed_types'] = array('image/jpg', 'image/jpeg', 'image/gif', 'image/png');
            $ftp_settings = array('server' => $settings['ftpserver'], 'username' => $settings['ftpusername'], 'password' => $settings['ftppassword']);
            $upload = new Uploader('logo', $this->logo, $upload_param['upload_allowed_types'], 'ftp', 5242880, 1, 1);
            $upload->establish_ftp($ftp_settings);
            $upload->set_upload_path('./uploads/eventslogos');
            $upload->process_file();
            $upload->close_ftp();
        }
    }

}
?>