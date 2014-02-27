<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * Meeting Minutes of Meeting Class
 * $id: MeetingsMOM_class.php
 * Created:        @zaher.reda    Nov 15, 2013 | 12:54:20 PM
 * Last Update:    @zaher.reda    Nov 15, 2013 | 12:54:20 PM
 */

class MeetingsMOM {
	private $mom = array();
	private $errorcode = 0;

	public function __construct($momid = '') {
		if(isset($momid) && !empty($momid)) {
			$this->mom = $this->read($momid);
		}
	}

	private function read($momid = '') {
		global $db;
		return $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."meetings_minsofmeeting WHERE momid=".$db->escape_string($momid)));
	}

	public function create($mom_data = array()) {
		global $db, $core, $log;

		if(empty($mom_data['mtid']) || empty($mom_data['meetingDetails'])) {
			$this->errorcode = 1;
			return false;
		}
		$mom_data['meetingDetails'] = $core->sanitize_inputs($mom_data['meetingDetails'], array('removetags' => false));
		$mom_data['followup'] = $core->sanitize_inputs($mom_data['followup'], array('removetags' => false));

		if(!value_exists('meetings_minsofmeeting', 'mtid', $mom_data['mtid'])) {
			$query = $db->insert_query('meetings_minsofmeeting', array('mtid' => $mom_data['mtid'], 'meetingDetails' => $mom_data['meetingDetails'], 'followup' => $mom_data['followup'], 'createdBy' => $core->user['uid'], 'createdOn' => TIME_NOW));
			if($query) {
				$db->update_query('meetings', array('hasMOM' => 1), 'mtid='.$mom_data['mtid']);
				$this->errorcode = 0;
				$log->record('addedmom', $this->mom_data['mtid']);
			}
		}
		else {
			$mom_obj = MeetingsMOM::get_mom_bymeeting($mom_data['mtid']);
			$mom_data['momid'] = $mom_obj->get()['momid'];
			$mom_obj->update($mom_data);
		}
	}

	public function update($mom_data = array()) {
		global $db, $core, $log;
		$mom_data['modifiedBy'] = $core->user['uid'];
		$mom_data['modifiedOn'] = TIME_NOW;

		$query = $db->update_query('meetings_minsofmeeting', array('meetingDetails' => $mom_data['meetingDetails'], 'followup' => $mom_data['followup'], 'modifiedBy' => $mom_data['modifiedBy'], 'modifiedOn' => $mom_data['modifiedOn']), 'momid='.$db->escape_string($this->mom['momid']).'');
		if($query) {
			$this->errorcode = 2;
			$log->record('updatedmom', $this->mom_data['mtid']);
		}
	}

	public static function get_mom_bymeeting($mtid) {
		global $db;

		$momid = $db->fetch_field($db->query('SELECT momid FROM '.Tprefix.'meetings_minsofmeeting WHERE mtid='.intval($mtid)), 'momid');
		if(!empty($momid)) {
			return new MeetingsMOM($momid);
		}
		return false;
	}

	public function get_errorcode() {
		return $this->errorcode;
	}

	public function get() {
		return $this->mom;
	}

}
?>
