<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: Events.php
 * Created:        @tony.assaad    Oct 16, 2013 | 1:53:26 PM
 * Last Update:    @tony.assaad    Oct 16, 2013 | 1:53:26 PM
 */

/**
 * Description of Events
 *
 * @author tony.assaad
 */
class Events {
	protected $status = 0;
	private $event = array();

	public function __consturct($id = '', $ispublic = false) {
		
	}

	private function read($id, $ispublic = false) {
		global $db;
		if(empty($id)) {
			return false;
		}

		$query_select = '*';
		$public_where = ' AND isPublic=0';
		if($ispublic == true) {
			$public_where = ' AND isPublic=1';
		}
		return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."calendar_events WHERE ceid=".$db->escape_string($id)."{$public_where}"));
	}

	public function get_publicevents() {
		
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

	public static function get_affiliatedevents($affiliates = array(), $option = array()) {
		global $db, $core;
		if(is_array($options)) {
			if(isset($options['ismain']) && $options['ismain'] === 1) {
				$query_where_add = ' AND isMain=1';
			}
		}
		$events_aff = $db->query("SELECT ce.* FROM ".Tprefix."calendar_events ce
								JOIN ".Tprefix."affiliatedemployees a ON (a.affid=ce.affid) 
								JOIN ".Tprefix."users u  ON (a.uid=u.uid) 
								WHERE u.uid=".$core->user['uid']." AND a.affid in (".(implode(',', $affiliates)).") ".$query_where_add." AND u.gid!=7");

		while($aff_events = $db->fetch_assoc($events_aff)) {
			$affiliate_events[$aff_events['ceid']] = $aff_events;
		}

		return $affiliate_events;
	}

	public static function get_eventBytype($type) {
		global $db;

		return $this->events = $db->fetch_assoc($db->query("SELECT  ce.*,ce.title AS eventtitle FROM ".Tprefix."calendar_events ce
								JOIN ".Tprefix."calendar_eventtypes cet ON(cet.cetid=ce.type)
								WHERE cet.name=".$db->escape_string($type).""));
	}

	public function get() {
		return $this->event;
	}

}
?>
