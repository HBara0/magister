<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Icalendar_class.php
 * Created:        @tony.assaad    Feb 6, 2014 | 5:15:00 PM
 * Last Update:    @tony.assaad    Feb 18, 2014 | 5:15:00 PM
 */

/**
 * Description of Icalendar_class
 *
 * @author tony.assaad
 */
class Icalendar {
    private $icalendarfile = array();
    private $icalendar = array();
    private $types = array(
            'event' => 'VEVENT',
            'task' => 'VTODO',
            'todo' => 'VTODO',
            'journal' => 'VJOURNAL'
    );
    private $icalendar_tz = null;

    public function __construct(array $config = array('component' => 'event')) {
        $this->icalendarfile = "BEGIN:VCALENDAR\r\n";
        $this->icalendarfile .= "VERSION:2.0\r\n";
        $this->icalendarfile .= "PRODID:-//Orkila//OCalendar//EN\r\n";
        $this->set_method($config['method']);

        /* Set Timezone - START */
        $this->icalendar_tz = new iCalendar_TimeZone();
        //$this->icalendarfile .= $this->icalendar_tz->get();
        /* Set Timezone - END */


        $this->set_type($config['component']);
        $this->icalendarfile .= "BEGIN:{$this->icalendar[type]}\r\n";
        $this->set_sequence();
        $this->set_identifier($config['identifier'], $config['uidtimestamp']);

        $this->icalendarfile .= $this->set_datestamp();
    }

    private function set_sequence($sequence = 0) {
        $this->icalendarfile .= 'SEQUENCE:'.$sequence."\r\n";
    }

    private function set_type($component = '') {
        if(empty($component)) {
            $component = 'event';
        }

        if(!isset($this->types[$component])) {
            $component = 'event';
        }

        $this->icalendar['type'] = $this->types[$component];
    }

    public function set_method($method = '') {
        if(empty($method)) {
            $method = 'PUBLISH';
        }
        $this->icalendarfile .= 'METHOD:'.$method."\r\n";
    }

    private function set_identifier($identifier = '', $timestamp = '') {
        if(empty($identifier)) {
            $identifier = substr(md5(uniqid(microtime())), 1, 10);
        }
        $this->icalendar['uid'] = $identifier;

        if(empty($timestamp)) {
            $timestamp = TIME_NOW;
        }

        $this->icalendarfile .= 'UID:'.$this->parse_datestamp($timestamp).'-'.$identifier."-@orkila.com\r\n";
    }

    private function set_datestamp() {
        $this->icalendarfile .= 'DTSTAMP:'.$this->parse_datestamp(TIME_NOW)."Z\r\n";
    }

    public function set_datestart($datestart, $timezone = '', $use_utc = true) {
        if(empty($timezone)) {
            $timezone = $this->icalendar_tz->get_name();
        }

        if($use_utc == true) {
            $datestart = $this->convert_toutc($datestart, $timezone);
            $this->icalendarfile .= 'DTSTART:'.$this->parse_datestamp($datestart, true)."\r\n";
        }
        else {
            $this->icalendarfile .= 'DTSTART;TZID='.$timezone.':'.$this->parse_datestamp($datestart)."\r\n";
        }
    }

    public function set_datend($datend, $timezone = '', $use_utc = true) {
        if(empty($timezone)) {
            $timezone = $this->icalendar_tz->get_name();
        }
        if($use_utc == true) {
            $datend = $this->convert_toutc($datend, $timezone);
            $this->icalendarfile .= 'DTEND:'.$this->parse_datestamp($datend, true)."\r\n";
        }
        else {
            $this->icalendarfile .= 'DTEND;TZID='.$timezone.':'.$this->parse_datestamp($datend)."\r\n";
        }
    }

    public function set_duedate($duedate, $timezone = '', $use_utc = true) {
        if(empty($timezone)) {
            $timezone = $this->icalendar_tz->get_name();
        }
        if($use_utc == true) {
            $duedate = $this->convert_toutc($duedate, $timezone);
            $this->icalendarfile .= 'DUE:'.$this->parse_datestamp($duedate, true)."\r\n";
        }
        else {
            $this->icalendarfile .= 'DUE;TZID='.$timezone.':'.$this->parse_datestamp($duedate)."\r\n";
        }
    }

    public function set_categories($categories) {
        $this->icalendarfile .= "CATEGORIES:{$categories}\r\n";
    }

    public function set_completed($completedate, $timezone = '') {
        $this->icalendarfile .= 'COMPLETED:'.date('Ymd\THis', $completedate)."\r\n";
    }

    public function set_percentcomplete($percent) {
        $this->icalendarfile .= "PERCENT-COMPLETE:{$percent}\r\n";
    }

    public function set_summary($summary) {
        global $core;
        $this->icalendar['summary'] = $core->sanitize_inputs($summary);
        $this->icalendarfile .= "SUMMARY:".$this->apply_icalstd($this->icalendar['summary'])."\r\n";
    }

    public function set_location($location) {
        $this->icalendar['location'] = $location;
        $this->icalendarfile .= "LOCATION:".$this->apply_icalstd($location)."\r\n";
    }

    public function set_priority($priority) {
        $this->icalendarfile .= "PRIORITY:{$priority}\r\n";
    }

    public function sentby(Users $organizer = null) {
        global $core;
        if(is_object($organizer)) {
            $organizer = $organizer;
        }
        else {
            $organizer = $core->user_obj;
        }
        $this->icalendarfile .= "ORGANIZER;SENT-BY: MAILTO:{$organizer->get()['email']}\r\n";
    }

    public function set_status($status = '') {
        if(empty($status)) {
            $status = 'CONFIRMED';
        }
        $this->icalendarfile .= "STATUS.{$status}\r\n";
    }

    public function set_organizer(Users $organizer = null) {
        global $core;
        if(is_object($organizer)) {
            $organizer = $organizer;
        }
        else {
            $organizer = $core->user_obj;
        }
        $this->icalendarfile .= "ORGANIZER;CN=".$this->apply_icalstd($organizer->get()['displayName']).":MAILTO:{$organizer->get()['email']}\r\n";
    }

    public function set_icalattendees($attendees) {
        /* loop over the attendees of the meetings object and   defines teh  "Attendee" within the calendar component. */
        if(is_array($attendees)) {
            foreach($attendees as $attendee) {
                if(empty($attendee['email'])) {
                    continue;
                }
                if(empty($attendee['name'])) {
                    $attendee['name'] = $attendee['email'];
                }
                $this->icalattendees .= "ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=TENTATIVE;CN=".$this->apply_icalstd($attendee['name']).";RSVP=TRUE:MAILTO:{$attendee['email']}\r\n";
            }
        }
        //if single attendee
        else {
            $user_object = new Users($attendees);
            $attendee = $user_object->get();
            $this->icalattendees = "ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=TENTATIVE;CN=".$this->apply_icalstd($attendee['displayName']).";RSVP=TRUE:MAILTO:{$attendee['email']}\r\n";
        }
        $this->icalendarfile .= $this->icalattendees;
    }

    public function set_description($description) {
        global $core;
        $description = $this->apply_icalstd(trim($description));
        $pre_description = preg_replace("/\r|\n/", '\n', $description);
        $description_sanitized = $core->sanitize_inputs($pre_description, array('method' => 'convert', 'removetags' => true));
        $this->icalendarfile .= 'DESCRIPTION: '.$description_sanitized."\r\n";
        if(!empty($pre_description)) {
            $this->icalendarfile .= 'X-ALT-DESC;FMTTYPE=text/html:<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 3.2//EN">\n<html>\n<body>\n'.$pre_description.'\n</body>\n</html>'."\r\n";
        }
    }

    public function set_transparency($trasp = 'PUBLIC') {
        if(empty($trasp)) {
            $trasp = 'TRANSPARENT';
        }
        $this->icalendarfile .= "TRANSP=".$trasp."\r\n";
    }

    public function set_recurrence($recur) {
        switch($recur) {
            case 86400:
                $recurfreq = 'DAILY';
                break;
        }
        $recurfreq = 'DAILY';
        $this->icalendarfile .= "RECUR=FREQ={$recurfreq};\r\n";
    }

    public function endical() {
        $this->icalendarfile .= "END:{$this->icalendar[type]}\r\n";
        $this->icalendarfile .= "END:VCALENDAR";
    }

    public function parse_datestamp($timestamp, $utc = false) {
        if($utc == true) {
            $utc = 'Z';
        }
        return date('Ymd', $timestamp).'T'.date('His', $timestamp).$utc;
    }

    public function download() {
        header('Content-Type: text/Calendar');
        header('Content-Disposition: inline; filename='.$this->icalendar['name'].'.ics');
        output($this->geticalendar());
    }

    public function save() {
        global $core;
        if(empty($this->icalendar['name'])) {
            $this->set_name();
        }

        $filename = $this->icalendar['name'].'.ics';
        $fp = fopen($core->sanitize_path("./tmp/{$filename}"), 'wr');
        fwrite($fp, $this->icalendarfile);
        fclose($fp);
    }

    public function get_filepath() {
        global $core;
        return $core->sanitize_path('./tmp/'.$this->icalendar['name'].'.ics');
    }

    public function set_relatedto($relatedto) {
        $this->icalendarfile .= 'RELATED-TO:<'.$relatedto.'>'."\r\n";
    }

    public function set_name($name = '') {
        global $core;

        if(empty($name)) {
            $this->icalendar['name'] = $this->icalendar['summary'];
        }
        else {
            $this->icalendar['name'] = $name;
        }

        $this->icalendar['name'] = $core->sanitize_inputs($this->icalendar['name']);
    }

    public function delete() {
        global $core;
        @unlink($core->sanitize_path('./tmp/'.$this->icalendar['name'].'.ics'));
    }

    private function convert_toutc($timestamp, $timezone) {
        $timezone = new DateTimeZone($timezone);
        $time = new DateTime('@'.$timestamp);
        $time->setTimezone($timezone);
        $offset = $timezone->getOffset($time);

        return $timestamp - $offset;
    }

    private function apply_icalstd($value) {
        return str_replace(array(';', ',', ':'), array('\\;', '\\,', '\\:'), $value);
    }

    public function geticalendar() {
        return $this->icalendarfile;
    }

    public function get() {
        return $this->icalendar;
    }

    public function set_url($url) {
        $this->icalendarfile .= 'URL: '.$url."\r\n";
    }

}

class iCalendar_TimeZone {
    private $vtimezone = '';
    private $timezone = null;

    public function __construct($timezone = '') {
        global $core;
        if(empty($timezone)) {
            $timezone = DateTimeZone::listIdentifiers(DateTimeZone::PER_COUNTRY, $core->user_obj->get_mainaffiliate()->get_country()->get()['acronym'])[0];
        }
        $this->timezone = new DateTimeZone($timezone);
    }

    public function parse() {
        $this->vtimezone = 'BEGIN:VTIMEZONE'."\r\n";
        $this->timenow = new DateTime();
        $this->timenow->setTimezone($this->timezone);
        $this->set_tzid();
        $this->set_transitions();
        $this->vtimezone .= 'END:VTIMEZONE'."\r\n";
    }

    private function set_tzid() {
        $this->vtimezone .= 'TZID:'.$this->timezone->getName()."\r\n";
        $this->vtimezone .= 'X-LIC-LOCATION:'.$this->timezone->getName()."\r\n";
    }

    private function set_offsetfrom($offset = null) {
        if(empty($offset)) {
            $offset = $this->timenow->format('O');
        }

        $this->vtimezone .= 'TZOFFSETFROM:'.$offset."\r\n";
    }

    private function set_offsetto($offset = null) {
        if(empty($offset)) {
            $offset = $this->timenow->format('O');
        }

        $this->vtimezone .= 'TZOFFSETTO:'.$offset."\r\n";
    }

    private function set_transitions() {
        $transitions = $this->timezone->getTransitions(strtotime($this->timenow->format('Y').'-01-01'));

        for($i = 0; $i < 2; $i++) {
            if(empty($transitions[$i]['isdst'])) {
                $this->vtimezone .= 'BEGIN:STANDARD'."\r\n";
            }
            else {
                $this->vtimezone .= 'BEGIN:DAYLIGHT'."\r\n";
            }

            if($this->timenow->format('I') == 1 && $transitions[$i]['isdst'] == 1) {
                $this->set_offsetfrom();
                $offset_to = $this->parse_offset($transitions[$i]['offset']);
                $this->set_offsetto($offset_to);
            }
            else {
                $offset_from = $this->parse_offset($transitions[$i + 1]['offset']);
                $offset_to = $this->parse_offset($transitions[$i]['offset']);
                $this->set_offsetfrom($offset_from);
                $this->set_offsetto($offset_to);
            }

            $this->set_tzname();
            $this->vtimezone .= 'DTSTART:'.str_replace(array('-', ':', '+0000'), '', $transitions[$i]['time'])."\r\n";

            if(empty($transitions[$i]['isdst'])) {
                $this->vtimezone .= 'END:STANDARD'."\r\n";
            }
            else {
                $this->vtimezone .= 'END:DAYLIGHT'."\r\n";
            }
        }
    }

    private function parse_offset($offset) {
        $tz_sign = '+';
        if($offset < 0) {
            $tz_sign = '-';
        }
        return $tz_sign.sprintf('%02d%02d', floor($offset / 60 / 60), abs($offset % 60));
    }

    private function set_tzname() {
        $this->vtimezone .= 'TZNAME:'.$this->timenow->format('T')."\r\n";
    }

    public function get_name() {
        return $this->timezone->getName();
    }

    public function get() {
        return $this->vtimezone;
    }

}
?>
