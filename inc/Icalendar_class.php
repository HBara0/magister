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

	public function __construct(array $config = array('component' => 'event')) {
		$this->icalendarfile = "BEGIN:VCALENDAR\r\n";
		$this->icalendarfile .= "VERSION:2.0\r\n";
		$this->icalendarfile .= "PRODID:-//Orkila//OCalendar//EN\r\n";
		$this->set_method($config['method']);
		$this->set_type($config['component']);
		$this->icalendarfile .= "BEGIN:{$this->icalendar[type]}\r\n";
		$this->set_sequence();
		$this->set_identifier($config['identifier'], $config['uidate']);

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

	private function set_identifier($identifier = '', $uidate) {
		if(empty($identifier)) {
			$identifier = substr(md5(uniqid(microtime())), 1, 10);
		}
		$this->icalendar['uid'] = $identifier;
		// $this->icalendarfile .="UID:1392735681-961846949c-@orkila.com\r\n"; 

		$this->icalendarfile .= 'UID:'.$uidate."-".$identifier."-@orkila.com\r\n";
	}

	private function set_datestamp() {
		$this->icalendarfile .= 'DTSTAMP:'.date('Ymd').'T'.date('His')."\r\n";
	}

	public function set_datestart($datestart) {
		$this->icalendarfile .= 'DTSTART:'.date('Ymd\THis', $datestart)."\r\n";
	}

	public function set_datend($datend) {
		$this->icalendarfile .= 'DTEND:'.date('Ymd\THis', $datend)."\r\n";
	}

	public function set_duedate($duedate) {
		$this->icalendarfile .= 'DUE:'.date('Ymd\THis', $duedate)."\r\n";
	}

	public function set_categories($categories) {
		$this->icalendarfile .= "CATEGORIES:{$categories}\r\n";
	}

	public function set_completed($completedate) {
		$this->icalendarfile .= 'COMPLETED:'.date('Ymd\THis', $completedate)."\r\n";
	}

	public function set_percentcomplete($percent) { /* APPOINTMENT,EDUCATION MEETING */
		$this->icalendarfile .= "PERCENT-COMPLETE:{$percent}\r\n";
	}

	public function set_summary($summary) {
		global $core;
		$this->icalendar['summary'] = $core->sanitize_inputs($summary);
		$this->icalendarfile .= "SUMMARY:{$this->icalendar[summary]}\r\n";
	}

	public function set_location($location) {
		$this->icalendar['location'] = $location;
		$this->icalendarfile .= "LOCATION:{$location}\r\n";
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

	public function set_status($status) {
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
		$this->icalendarfile .= "ORGANIZER;CN={$organizer->get()['displayName']}:MAILTO:{$organizer->get()['email']}\r\n";
	}

	public function set_icalattendees($attendees) {
		/* loop over the attendees of the meetings object and   defines teh  "Attendee" within the calendar component. */
		if(is_array($attendees)) {
			foreach($attendees as $attendee) {
				$this->icalattendees .= "ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=TENTATIVE;CN={$attendee['displayName']}:MAILTO:{$attendee['email']}\r\n";
			}
		}
		//if single attendee
		else {
			$user_object = new Users($attendees);
			$attendee = $user_object->get();
			$this->icalattendees = "ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=TENTATIVE;CN={$attendee['displayName']}:MAILTO:{$attendee['email']}\r\n";
		}
		$this->icalendarfile .= $this->icalattendees;
	}

	public function set_description($description) {
		$this->icalendarfile .= "DESCRIPTION: {$description}\r\n";
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
		$this->icalendarfile .= "END:VCALENDAR\r\n";
	}

	public function download() {
		header('Content-Type: text/Calendar');
		header('Content-Disposition: inline; filename='.$this->icalendar['name'].'.ics');
		output($this->geticalendar());
	}

	public function save() {
		if(empty($this->icalendar['name'])) {
			$this->set_name();
		}

		$filename = $this->icalendar['name'].'.ics';
		$fp = fopen("./tmp/{$filename}", 'wr');
		fwrite($fp, $this->icalendarfile);
		fclose($fp);
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
		@unlink('./tmp/'.$this->icalendar['name'].'.ics');
	}

	public function geticalendar() {
		return $this->icalendarfile;
	}

	public function get() {
		return $this->icalendar;
	}

}
?>
