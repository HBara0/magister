<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Icalendar_class.php
 * Created:        @tony.assaad    Feb 6, 2014 | 5:15:00 PM
 * Last Update:    @tony.assaad    Feb 6, 2014 | 5:15:00 PM
 */

/**
 * Description of Icalendar_class
 *
 * @author tony.assaad
 */
class Icalendar {
	private $icalendarfile = array();
	private $icalendar = array();

	public function __construct(array $config = array('component' => "VEVENT")) {
		$this->calendar_component = $config['component'];
		if(!isset($config['identifier'])) {
			$config['identifier'] = substr(md5(uniqid(microtime())), 1, 10);
		}
		$this->icalendarfile = "BEGIN:VCALENDAR\n";
		$this->icalendarfile.="VERSION:2.0\n";
		$this->icalendarfile.="PRODID:-//OCOS Corporation//EN\n";
		$this->icalendarfile.="METHOD:REQUEST\n";
		$this->icalendarfile.="BEGIN:{$this->calendar_component}\n";
		$this->icalendarfile.="UID:".date('Ymd')."T".date('His')."-".$config['identifier']."-@orkila.com\n";
		$this->icalendarfile.=self::set_datestamp();
	}

	private static function set_eventidentifier() {
		$this->icalendarfile.="UID:".date('Ymd').'T'.date('His')."-".rand()."-'@orkila.com'\n"; // required by Outlok
	}

	private function set_datestamp() {
		$this->icalendarfile.="DTSTAMP:".date('Ymd').'T'.date('His')."\n";
	}

	public function set_datestart($datestart) {
		$this->icalendarfile.="DTSTART:".date('Ymd\THis', $datestart)."\n";
	}

	public function set_datend($datend) {
		$this->icalendarfile.="DTEND:".date('Ymd\THis', $datend)."\n";
	}

	public function set_duedate($duedate) {
		$this->icalendarfile.="DUE:".date('Ymd\THis', $duedate)."\n";
	}

	public function set_categories($categories) {
		$this->icalendarfile.="CATEGORIES:{$categories}\n";
	}

	public function set_completed($completedate) {
		$this->icalendarfile.="COMPLETED:".date('Ymd\THis', $completedate)."\n";
	}

	public function set_percentcomplete($percent) { /* APPOINTMENT,EDUCATION MEETING */
		$this->icalendarfile.="PERCENT-COMPLETE:{$percent}\n";
	}

	public function set_summary($summary) {
		$this->icalendar['summary'] = $summary;
		$this->icalendarfile.="SUMMARY:{$summary}\n";
	}

	public function set_location($location) {
		$this->icalendar['location'] = $location;
		$this->icalendarfile.="LOCATION:{$location}\n";
	}

	public function set_priority($priority) {
		$this->icalendarfile.="PRIORITY.{$priority}\n";
	}

	public function sentby(Users $organizer = null) {
		global $core;
		if(is_object($organizer)) {
			$organizer = $organizer;
		}
		else {
			$organizer = $core->user_obj;
		}
		$this->icalendarfile.="ORGANIZER;SENT-BY: MAILTO:{$organizer->get()['email']}\n";
	}

	public function set_status($status) {
		$this->icalendarfile.="STATUS.{$status}\n";
	}

	public function set_organizer(Users $organizer = null) {
		global $core;
		if(is_object($organizer)) {
			$organizer = $organizer;
		}
		else {
			$organizer = $core->user_obj;
		}
		$this->icalendarfile.="ORGANIZER;CN={$organizer->get()['displayName']}:MAILTO:{$organizer->get()['email']}\n";
	}

	public function set_icalattendees($attendees) {
		/* loop over the attendees of the meetings object and   defines teh  "Attendee" within the calendar component. */
		if(is_array($attendees)) {
			foreach($attendees as $attendee) {
				$this->icalattendees.="ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=TENTATIVE;CN={$attendee['displayName']}
			:MAILTO:{$attendee['email']}\n";
			}
		}
		//if single attendee
		else {
			$user_object = new Users($attendees);
			$attendee = $user_object->get();
			$this->icalattendees = "ATTENDEE;ROLE=REQ-PARTICIPANT;PARTSTAT=TENTATIVE;CN={$attendee['displayName']}
			:MAILTO:{$attendee['email']}\n";
		}
		$this->icalendarfile.=$this->icalattendees;
	}

	public function set_description($description) {
		$this->icalendarfile.="DESCRIPTION: {$description}\n";
	}

	public function set_recurrence($recur) {
		switch($recur){
			case 86400:
			$recurfreq="DAILY";
			break;
		}	$recurfreq="DAILY";
		$this->icalendarfile.="RECUR=FREQ={$recurfreq};\n";
	}

	public function endical() {
		$this->icalendarfile.="END:{$this->calendar_component}\n";
		$this->icalendarfile.="END:VCALENDAR\n";
	}

	public function download() {
		header('Content-Type: text/Calendar');
		header('Content-Disposition: inline; filename=appointment.ics');
		echo $icalendarfile = $this->geticalendar();
	}

	public function save() {
		$filename = $this->icalendar['summary'].".ics";
		$fp = fopen("./tmp/{$filename}", "wr");
		fwrite($fp, $this->icalendarfile);
		fclose($fp);
	}

	public function delete($filename) {
		unlink('./tmp/'.$filename);
	}

	public function geticalendar() {
		return $this->icalendarfile;
	}

	public function get() {
		return $this->icalendar;
	}

}
?>
