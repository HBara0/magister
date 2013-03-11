<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * [reportingQR_class]
 * $id: reportingQR_class.php
 * Created:        @[user.name]    Feb 25, 2013 | 10:30:00 PM
 * Last Update:    @tony.assaad    March 07, 2013 | 3:30:00 PM
 */

class reportingQr Extends reporting {
	private $status = 0; //0=No errors;1=Subject missing;2=Entry exists;3=Error saving;4=validation violation

	public function __construct($reportdata = array()) {
		parent::__construct($reportdata);
	}

	public function read_products_activity($get_prevyear = false) {
		global $db;
		$products_activity_query = $db->query("SELECT pa.*,p.name,ps.psid,ps.title AS segment FROM ".Tprefix."reports r
			JOIN ".Tprefix."productsactivity pa ON (pa.rid = r.rid)
			JOIN ".Tprefix."products p  ON (pa.pid=p.pid)
			JOIN ".Tprefix."genericproducts gp ON (gp.gpid = p.gpid)
			JOIN ".Tprefix."productsegments ps ON (ps.psid = gp.psid)
			WHERE  r.rid='".$this->report['rid']."' ORDER BY pa.turnOver ASC");

		if($db->num_rows($products_activity_query) > 0) {
			while($products_activityrow = $db->fetch_assoc($products_activity_query)) {
				$this->report['classifiedpactivity']['amount']['actual'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']] += $products_activityrow['turnOver'];
				$this->report['classifiedpactivity']['amount']['forecast'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']] += $products_activityrow['salesForecast'];
				$this->report['classifiedpactivity']['quantity']['forecast'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']] += $products_activityrow['quantityForecast'];
				$this->report['classifiedpactivity']['quantity']['actual'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']] += $products_activityrow['quantity'];
				$this->report['productsactivity'][$products_activityrow['paid']] = $products_activityrow;
			}
			if($get_prevyear == true) {
				$this->read_prev_products_activity();
			}
			if($get_prevyear == true) {
				print_r($this->report['classifiedpactivity']);
				echo'<hr>';
			}
			return $this->report['classifiedpactivity'];
		}
		return false;
	}

	public function read_prev_products_activity() {
		global $db;
		for($year = $this->report['year']; $year >= ($this->report['year'] - 2); $year--) {
			if($year == $this->report['year']) {
				if($this->report['quarter'] == 1) {
					continue;
				}
				$start_quarter = $this->report['quarter'] - 1;
			}
			else {
				$start_quarter = 4;
			}
			for($quarter = 4; $quarter >= 1; $quarter--) {
				$newreport = new reportingQr(array('year' => $year, 'affid' => $this->report['affid'], 'spid' => $this->report['spid'], 'quarter' => $quarter));
				$reportdetails = $newreport->read_products_activity(false);
				if(is_array($reportdetails)) {
					foreach($reportdetails as $category => $catitem) { /* amount or  quantity */
						if(is_array($catitem)) {
							foreach($catitem as $type => $typeitem) {
								if(isset($this->report['classifiedpactivity'][$category][$type][$year])) {
									$this->report['classifiedpactivity'][$category][$type][$year] +=$typeitem[$year];
								}
								else {
									$this->report['classifiedpactivity'][$category][$type][$year] = $typeitem[$year];
								}
							}
						}
					}
				}
			}
		}
//		for($i = 1; $i <= 2; $i++) {
//			$previous_year = $this->report['year'] - $i;
//			for($quarter = 1; $quarter <= 4; $quarter++) {
//				$newreport = new reportingQr(array('year' => $previous_year, 'affid' => $this->report['affid'], 'spid' => $this->report['spid'], 'quarter' => $quarter));
//				$reportdetails = $newreport->read_products_activity(false);
//
//				if(is_array($reportdetails)) {
//					foreach($reportdetails as $category => $catitem) { /* amount or  quantity */
//						if(is_array($catitem)) {
//							foreach($catitem as $type => $typeitem) {
//								if(isset($this->report['classifiedpactivity'][$category][$type][$previous_year])) {
//									$this->report['classifiedpactivity'][$category][$type][$previous_year] +=$typeitem[$previous_year];
//								}
//								else {
//									$this->report['classifiedpactivity'][$category][$type][$previous_year] = $typeitem[$previous_year];
//								}
//							}
//						}
//					}
//				}
//			}
//		}
	}

	public function get_product_name() {
		global $db;
		$products_name_query = $db->query("SELECT pa.paid,p.name FROM ".Tprefix."reports r
			JOIN ".Tprefix."productsactivity pa ON (pa.rid = r.rid)
			JOIN ".Tprefix."products p  ON (pa.pid=p.pid)
			JOIN ".Tprefix."genericproducts gp ON (gp.gpid = p.gpid)
			JOIN ".Tprefix."productsegments ps ON (ps.psid = gp.psid)
			WHERE  r.rid='".$this->report['rid']."'");

		if($db->num_rows($products_name_query) > 0) {
			while($products_namerow = $db->fetch_assoc($products_name_query)) {
				$reportdetails['productsname'][$products_namerow['paid']] = $products_namerow;
			}
			return $reportdetails;
		}
		return false;
	}

	public function get_report_affiliate($affid = '') {
		global $db;
		return $report_affiliates = $db->fetch_assoc($db->query("SELECT aff.name,aff.affid,r.rid FROM ".Tprefix."affiliates aff
											JOIN ".Tprefix."reports r ON (r.affid=aff.affid)
											WHERE  r.rid='".$this->report['rid']."'"));
	}

	public function get_report_productsegment() {
		global $db;
		return $report_segments = $db->fetch_assoc($db->query("SELECT ps.title FROM ".Tprefix."productsegments ps
											JOIN ".Tprefix."reports r ON (ps.psid=r.spid)
											WHERE  r.rid='".$this->report['rid']."'"));
	}

	public function get_key_customers() {
		global $db;
		$key_customers_query = $db->query("SELECT kc.*,e.companyName FROM ".Tprefix."keycustomers kc
											JOIN ".Tprefix."entities e ON (e.eid=kc.cid)
											JOIN ".Tprefix."reports r ON (r.rid=kc.rid)
											WHERE e.type='c' AND kc.rid='".$this->report['rid']."'ORDER BY kc.Rank ASC");   // remove countrues and reportid
		if($db->num_rows($key_customers_query) > 0) {
			while($key_customersrow = $db->fetch_assoc($key_customers_query)) {
				$reportdetails[$key_customersrow['kcid']] = $key_customersrow;
			}
			return $reportdetails;
		}
		return false;
	}

	public function get_report_contributors() {
		global $db;
		$report_contributors_query = $db->query("SELECT  rc.rcid,u.displayName FROM ".Tprefix."reportcontributors rc
											JOIN ".Tprefix."users u ON (u.uid=rc.uid)
											WHERE  rc.rid='".$this->report['rid']."'");
		if($db->num_rows($report_contributors_query) > 0) {
			while($report_contributorsrow = $db->fetch_assoc($report_contributors_query)) {
				$reportdetails[$report_contributorsrow['rcid']] = $report_contributorsrow;
			}
			return $reportdetails;
		}
		return false;
	}

	public function get_market_reports() {
		global $db;
		if(isset($this->report['rid']) && !empty($this->report['rid'])) {
			$marketreport_queryid = $db->query("SELECT mr.mrid,mra.mkra FROM ".Tprefix."marketreport mr
												LEFT JOIN  ".Tprefix."marketreport_authors mra ON(mra.mrid=mr.mrid)
												WHERE mr.rid=".$this->report['rid']."");
			if($db->num_rows($marketreport_queryid) > 0) {
				while($market_reportdata = $db->fetch_assoc($marketreport_queryid)) {

					$marketreport = new reportingQrMarketreport($market_reportdata['mrid']);
					$reportdetails['market'][$market_reportdata['mrid']] = $marketreport->get();
					$reportdetails['marketauthors'][$market_reportdata['mrid']] = $marketreport->get_authors();
				}

				return $reportdetails;
			}
		}
	}

	public function get_supplier_representatives() { 
		global $db;
		$repquery = $db->query("SELECT er.*, r.* FROM ".Tprefix."entitiesrepresentatives er LEFT JOIN ".Tprefix."representatives r ON (r.rpid=er.rpid)
								WHERE er.eid='{$this->report['spid']}' ORDER BY name ASC");
		while($representative_row = $db->fetch_array($repquery)) {
			$reportdetails['representataive'][$representative_row['erpid']] = $representative_row;
		}
		return $reportdetails['representataive'];
	}

	public function get_report_summary() {
		global $db;
		return $report_summary = $db->fetch_assoc($db->query("SELECT rs.rpsid, rs.summary FROM ".Tprefix."reports r 
				 JOIN ".Tprefix."reporting_report_summary rs
				ON(r.summary=rs.rpsid) WHERE rs.summary!= '' 
				AND r.rid IN('".$this->report['rid']."') LIMIT 0, 1"));
	}

	public function get_market_reportsauthors() {
		$reportdetails['authors'][$market_reportdata['mkra']] = $marketreport->get_authors();
	}

	public function get_report_stats() {
		global $db;
		return $this->reportdetails['stauts'] = $db->fetch_assoc($db->query("SELECT status,isLocked FROM ".Tprefix."reports WHERE  rid='".$db->escape_string($this->report['rid'])."'"));
	}

	public function get_report_finalizer() {
		global $db;
		$report_finalizer = new Users($this->report['uidFinish']);
		return $this->reportdetails['finalizer'] = $report_finalizer->get();
	}

	public function get_report_supplier() {
		global $db;
		$report_supplier = new Entities($this->report['spid']);
		return $this->reportdetails['supplier'] = $report_supplier->get($this->report['spid']);
	}

	public function get_products_activity() { /* to check if isset then  read product  */
		return $this->report['productsactivity'];
	}

	public function get_status() {
		return $this->status;
	}

	public function get_report_supplier_audits() {
		global $db;
		return $auditors = $db->fetch_assoc($db->query("SELECT displayName AS employeeName, u.email FROM ".Tprefix."users u
														JOIN ".Tprefix."suppliersaudits sa ON (sa.uid=u.uid) WHERE sa.eid=".$this->report['spid'].""));
	}

	/* Setter Functionality --START */
	public function lock_report() {
		global $db;
		$query = $db->update_query("report", array('isLocked', 1), 'rid='.$db->escape_string($this->report['rid']));
		if($query) {
			$this->status = 0;
		}
	}

	public function set_status_report() {
		global $db;
		$query = $db->update_query("report", array('status', 1), 'rid='.$db->escape_string($this->report['rid']));
		if($query) {
			$this->status = 0;
		}
	}

	/* Setter Functionality --END */
}
/* Market report Class --START */

class reportingQrMarketreport {
	private $marketreport;

	public function __construct($marketid) {
		$this->read($marketid);
		$this->marketid = $marketid;
	}

	private function read($marketid) {
		global $db;
		if(isset($marketid) && !empty($marketid)) {
			$this->marketreport = $db->fetch_assoc($db->query("SELECT mr.* ,ps.title AS segmenttitle FROM ".Tprefix."marketreport mr
														LEFT JOIN ".Tprefix."productsegments ps ON (ps.psid=mr.psid)
														JOIN ".Tprefix."reports r ON (r.rid=mr.rid)
														WHERE  mr.mrid='".$db->escape_string($marketid)."'"));
		}
		return false;
	}

	public function get() {
		return $this->marketreport;
	}

	public function get_authors() {
		global $db;
		return $db->fetch_assoc($db->query("SELECT u.displayName,u.email FROM ".Tprefix."marketreport_authors mra
												JOIN ".Tprefix."users u ON (u.uid=mra.uid)
												JOIN ".Tprefix."marketreport mr ON (mr.mrid=mra.mrid)
												WHERE  mra.mrid='".$db->escape_string($this->marketid)."'"));
	}

}
/* Market report Class --END */
?>
