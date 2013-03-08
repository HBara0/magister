<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: reporting_class.php
 * Created:			@tony.assaad          |  
 * Last Update:     @tony.assaad    March 06, 2013 | 11:44:29 AM
 */

class reporting {
	protected $report;
	public function __construct($reportdata = array()) {
		if(isset($reportdata['rid']) && !empty($reportdata['rid'])) {
			$this->get_report_byid($reportdata['rid']);
		}
		else {
			if(isset($reportdata['year'], $reportdata['affid'], $reportdata['spid'], $reportdata['quarter']) && !empty($reportdata['year']) && !empty($reportdata['affid']) && !empty($reportdata['spid']) && !empty($reportdata['quarter'])) {
				$this->get_report($reportdata);
			}
			else {
				return false;
			}
		}
	}

	protected function get_report($reportdata = array()) {
		global $db;

		if(isset($reportdata['year'], $reportdata['affid'], $reportdata['spid'], $reportdata['quarter']) && !empty($reportdata['year']) && !empty($reportdata['affid']) && !empty($reportdata['spid']) && !empty($reportdata['quarter'])) {
			$this->report = $db->fetch_assoc($db->query("SELECT  r.* FROM ".Tprefix."reports r  WHERE  r.year='".$db->escape_string($reportdata['year'])."' AND r.affid='".$db->escape_string($reportdata['affid'])."' AND r.quarter='".$db->escape_string($reportdata['quarter'])."' AND r.spid='".$db->escape_string($reportdata['spid'])."'"));
			
			
		}

		return false;
	}

	protected function get_report_byid($rid) {
		global $db;
		if(!empty($rid)) {
			$this->report = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."reports WHERE  rid='".$db->escape_string($rid)."'"));
		}
		return false;
	}

	public function get() {
		return $this->report;
	}

}
?>
