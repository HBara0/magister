<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * Reporting Class
 * $id: reporting_class.php
 * Created:			@tony.assaad    March 06, 2013 | 11:44:29 AM
 * Last Update:     @tony.assaad    March 06, 2013 | 11:44:29 AM
 */

class Reporting {
	protected $report;
	public function __construct($reportdata = array()) {
		if(isset($reportdata['rid']) && !empty($reportdata['rid'])) {
			$this->get_report_byid($reportdata['rid']);
		}
		else {
			if(isset($reportdata['year'], $reportdata['affid'], $reportdata['spid'], $reportdata['quarter']) && !is_empty($reportdata['year'], $reportdata['affid'], $reportdata['spid'], $reportdata['quarter'])) {
				$this->get_report_byinfo($reportdata);
			}
			else {
				return false;
			}
		}
	} 

	protected function get_report_byinfo($reportdata = array()) {
		global $db;

		if(isset($reportdata['year'], $reportdata['affid'], $reportdata['spid'], $reportdata['quarter']) && !empty($reportdata['year']) && !empty($reportdata['affid']) && !empty($reportdata['spid']) && !empty($reportdata['quarter'])) {
			$this->report = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."reports WHERE  year='".$db->escape_string($reportdata['year'])."' AND affid='".$db->escape_string($reportdata['affid'])."' AND quarter='".$db->escape_string($reportdata['quarter'])."' AND spid='".$db->escape_string($reportdata['spid'])."'"));
			if(is_array($this->report) && !empty($this->report)) {
				return true;
			}
			return false;
		}
		return false;
	}

	protected function get_report_byid($rid) {
		global $db;
		if(!empty($rid)) {
			$this->report = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."reports WHERE rid='".$db->escape_string($rid)."'"));
			if(is_array($this->report) && !empty($this->report)) {
				return true;
			}
			return false;
		}
		return false;
	}

	public function get() {
		return $this->report;
	}

}
?>