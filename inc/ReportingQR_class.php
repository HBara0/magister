<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * [reportingQR_class]
 * $id: reportingQR_class.php
 * Created:        @[tony.assaad]    Feb 25, 2013 | 10:30:00 PM
 * Last Update:    @tony.assaad    March 14, 2013 | 3:30:00 PM
 */

class ReportingQr Extends Reporting {
	private $status = 0; //0=No errors;1=Subject missing;2=Entry exists;3=Error saving;4=validation violation

	public function __construct($reportdata = array()) {
		parent::__construct($reportdata);
	}

	public function read_products_activity($get_prevactivity = false) {
		global $db;
		$products_activity_query = $db->query("SELECT pa.*, p.name AS productname, ps.psid, ps.title AS segment 
			FROM ".Tprefix."reports r
			JOIN ".Tprefix."productsactivity pa ON (pa.rid=r.rid)
			JOIN ".Tprefix."products p ON (pa.pid=p.pid)
			JOIN ".Tprefix."genericproducts gp ON (gp.gpid=p.gpid)
			JOIN ".Tprefix."productsegments ps ON (ps.psid=gp.psid)
			WHERE r.rid='".$this->report['rid']."' 
			ORDER BY pa.turnOver ASC");

		if($db->num_rows($products_activity_query) > 0) {
			while($products_activityrow = $db->fetch_assoc($products_activity_query)) {
				$this->report['classifiedpactivity']['amount']['actual'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']] += $products_activityrow['turnOver'];
				$this->report['classifiedpactivity']['amount']['forecast'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']] += $products_activityrow['salesForecast'];

				$this->report['classifiedpactivity']['purchasedQty']['forecast'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']] += $products_activityrow['quantityForecast'];
				$this->report['classifiedpactivity']['purchasedQty']['actual'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']] += $products_activityrow['quantity'];

				$this->report['classifiedpactivity']['soldQty']['actual'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']] += $products_activityrow['soldQty'];

				$this->report['productsactivity'][$products_activityrow['paid']] = $products_activityrow;

//				$this->report['classifiedpactivity']['amount']['percentage'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']] = round(($this->report['classifiedpactivity']['amount']['actual'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']]/$this->report['classifiedpactivity']['amount']['forecast'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']])*100);
//				$this->report['classifiedpactivity']['quantity']['percentage'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']] = round(($this->report['classifiedpactivity']['quantity']['actual'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']]/$this->report['classifiedpactivity']['quantity']['forecast'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']])*100);
//				
				$this->report['products'][$products_activityrow['pid']] = $products_activityrow['productname'];
				$this->report['productssegments'][$products_activityrow['psid']] = $products_activityrow['segment'];
			}
			if($get_prevactivity == true) {
				$this->read_prev_products_activity();
				$this->read_next_products_activity();
			}
		}
		return false;
	}

	public function read_next_products_activity() {
		if($this->report['quarter'] == 4) {
			return false;
		}
		$has_proudct_avtivity = false;
		for($quarter = $this->report['quarter']+1; $quarter <= 4; $quarter++) {
			$newreport = new ReportingQr(array('year' => $this->report['year'], 'affid' => $this->report['affid'], 'spid' => $this->report['spid'], 'quarter' => $quarter));
			/* check whether produtact exist for this report */
			if($newreport && $newreport->check_product_availability()) {
				$newreport->read_products_activity(false);
				$next_products = $newreport->get_products();

				if(is_array($next_products)) {
					$this->report['products'] += $next_products;
				}

				/* get product activity of next quarter of this report year  ex:Q2->2011 */
				$next_productsactivity = $newreport->get_products_activity();
				if(isset($this->report['productsactivity']) && !empty($next_productsactivity)) {
					$this->report['productsactivity'] += $next_productsactivity;
				}

				/* get product segments of next quarter of this report year  ex:Q2->2011 */
				$next_productssegments = $newreport->get_productssegments();
				if(isset($this->report['productssegments']) && !empty($next_productssegments)) {
					$this->report['productssegments'] += $next_productssegments;
				}

				$reportdetails = $newreport->get_classified_productsactivity();

				if(is_array($reportdetails)) {
					foreach($reportdetails as $category => $catitem) { /* amount or  quantity */
						if(is_array($catitem)) {
							foreach($catitem as $type => $typeitem) {
								if(isset($this->report['classifiedpactivity'][$category][$type][$this->report['year']])) {
									$this->report['classifiedpactivity'][$category][$type][$this->report['year']] += $typeitem[$this->report['year']];
									
								}
								else {
									$this->report['classifiedpactivity'][$category][$type][$this->report['year']] = $typeitem[$this->report['year']];
								}
							}
						}
					}
				}
			}
			else {
				/* If no future reports data exist, use forecast as actual */
				foreach($this->report['classifiedpactivity']['amount']['forecast'][$this->report['year']][$this->report['quarter']] as $affid=> $affiliates_data) {
					foreach($affiliates_data as $psid=> $productssegments_data) {
						foreach($productssegments_data as $pid=> $products_data) {print_R($products_data);
							$this->report['classifiedpactivity']['amount']['actual'][$this->report['year']][$quarter][$affid][$psid][$pid] = $products_data;
							
							if($this->report['quarter'] != 4) {
								$this->report['classifiedpactivity']['amount']['actual'][$this->report['year']][$quarter][$affid][$psid][$pid] /= (4 - $this->report['quarter']);
							}
						}
					}
				}
			}
		}
	}

	public function check_product_availability() {
		global $db;
		if(!empty($this->report['rid'])) {
			if(value_exists('productsactivity', 'rid', $this->report['rid'])) {
				return true;
			}
			return false;
		}
		return false;
	}

	public function read_prev_products_activity() {
		for($year = $this->report['year']; $year >= ($this->report['year'] - 2); $year--) { /* reverse back only 2 years  from the given report year */
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
				$newreport = new ReportingQr(array('year' => $year, 'affid' => $this->report['affid'], 'spid' => $this->report['spid'], 'quarter' => $quarter));
				$newreport->read_products_activity(false);
				$prev_products = $newreport->get_products();
				if(!empty($prev_products)) {
					$this->report['products'] += $prev_products;
				}

				$prev_productsactivity = $newreport->get_products_activity();
				if(isset($this->report['productsactivity']) && !empty($prev_productsactivity)) {
					$this->report['productsactivity'] += $prev_productsactivity;
				}

				$prev_productssegments = $newreport->get_productssegments();
				if(isset($this->report['productssegments']) && !empty($prev_productssegments)) {
					$this->report['productssegments'] += $prev_productssegments;
				}

				$reportdetails = $newreport->get_classified_productsactivity();
				if(is_array($reportdetails)) {
					foreach($reportdetails as $category => $catitem) { /* amount or  quantity */
						if(is_array($catitem)) {
							foreach($catitem as $type => $typeitem) {
								if(isset($this->report['classifiedpactivity'][$category][$type][$year])) {
									$this->report['classifiedpactivity'][$category][$type][$year] += $typeitem[$year];
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
	}

	public function get_report_affiliate() {
		global $db;
		return $report_affiliates = $db->fetch_assoc($db->query("SELECT aff.name,aff.affid,r.rid FROM ".Tprefix."affiliates aff
											JOIN ".Tprefix."reports r ON (r.affid=aff.affid)
											WHERE r.rid='".$this->report['rid']."'"));
	}

	/* To be Implemented 
	  public function get_affiliate() {
	  return new Affiliates($this->report['affid']);
	  }
	 */
	/* This thing doesn't exist
	  public function get_report_productsegment() {
	  global $db;
	  return $report_segments = $db->fetch_assoc($db->query("SELECT ps.title
	  FROM ".Tprefix."productsegments ps
	  JOIN ".Tprefix."reports r ON (ps.psid=r.spid)
	  WHERE r.rid='".$this->report['rid']."'"));
	  }
	 */
	public function get_key_customers() {
		global $db;
		$key_customers_query = $db->query("SELECT kc.*, e.companyName 
											FROM ".Tprefix."keycustomers kc
											JOIN ".Tprefix."entities e ON (e.eid=kc.cid)
											WHERE kc.rid='".$this->report['rid']."'
											ORDER BY kc.rank ASC");   // remove countrues and reportid

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
		$report_contributors_query = $db->query("SELECT rc.rcid, u.displayName, u.email
												FROM ".Tprefix."reportcontributors rc
												JOIN ".Tprefix."users u ON (u.uid=rc.uid)
												WHERE rc.rid='".$this->report['rid']."'");
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
			$marketreport_queryid = $db->query("SELECT mrid
												FROM ".Tprefix."marketreport
												WHERE rid=".$this->report['rid']."");
			if($db->num_rows($marketreport_queryid) > 0) {
				while($market_reportdata = $db->fetch_assoc($marketreport_queryid)) {

					$marketreport = new ReportingQrMarketreport($market_reportdata['mrid']);
					$reportdetails[$market_reportdata['mrid']] = $marketreport->get();
					$reportdetails[$market_reportdata['mrid']]['authors'] = $marketreport->get_authors();
				}

				return $reportdetails;
			}
		}
	}

	public function get_supplier_representatives() {
		global $db;
		$query = $db->query("SELECT er.*, r.* 
								FROM ".Tprefix."entitiesrepresentatives er 
								LEFT JOIN ".Tprefix."representatives r ON (r.rpid=er.rpid)
								WHERE er.eid='{$this->report['spid']}' 
								ORDER BY name ASC");
		if($db->num_rows($query) > 0) {
			$representatives = array();
			while($representative = $db->fetch_assoc($query)) {
				$representatives['representataive'][$representative['erpid']] = $representative;
			}
			return $representative['representataive'];
		}
		return false;
	}

	public function get_report_summary() {
		global $db;
		return $report_summary = $db->fetch_assoc($db->query("SELECT rs.rpsid, rs.summary 
						FROM ".Tprefix."reports r 
						JOIN ".Tprefix."reporting_report_summary rs ON (r.summary=rs.rpsid) 
						WHERE rs.summary!= '' 
						AND r.rid = '".$this->report['rid']."'"));
	}

	public function get_report_status() {
		global $db;
		return $this->reportdetails['status'] = $db->fetch_assoc($db->query("SELECT status, isLocked FROM ".Tprefix."reports WHERE rid='".$this->report['rid']."'"));
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

	public function get_classified_productsactivity() {
		return $this->report['classifiedpactivity'];
	}

	public function get_products_activity() { /* to check if isset then  read product  */
		return $this->report['productsactivity'];
	}

	public function get_status() {
		return $this->status;
	}

	public function get_report_supplier_audits() {
		global $db;
		return $db->fetch_assoc($db->query("SELECT displayName AS employeeName, u.email 
			FROM ".Tprefix."users u
			JOIN ".Tprefix."suppliersaudits sa ON (sa.uid=u.uid) 
			WHERE sa.eid=".$this->report['spid'].""));
	}

	/* Setter Functionality --START */
	public function lock_report() {
		global $db;
		$query = $db->update_query('report', array('isLocked', 1), 'rid='.$this->report['rid']);
		if($query) {
			$this->status = 0;
		}
	}

	public function set_status() {
		global $db;
		$query = $db->update_query('report', array('status', 1), 'rid='.$this->report['rid']);
		if($query) {
			$this->status = 0;
		}
	}

	public function get_products() {
		return $this->report['products'];
	}

	public function get_productssegments() {
		return $this->report['productssegments'];
	}

	/* Setter Functionality --END */
}
/* Market report Class --START */

class ReportingQrMarketreport {
	private $marketreport;

	public function __construct($marketid) {
		$this->read($marketid);
		$this->marketid = $marketid;
	}

	private function read($marketid) {
		global $db;
		if(isset($marketid) && !empty($marketid)) {
			$this->marketreport = $db->fetch_assoc($db->query("SELECT mr.*, ps.title AS segmenttitle 
														FROM ".Tprefix."marketreport mr
														LEFT JOIN ".Tprefix."productsegments ps ON (ps.psid=mr.psid)
														JOIN ".Tprefix."reports r ON (r.rid=mr.rid)
														WHERE mr.mrid='".$db->escape_string($marketid)."'"));
		}
		return false;
	}

	public function get() {
		return $this->marketreport;
	}

	public function get_authors() {
		global $db;
		$query = $db->query("SELECT u.displayName, email, u.uid
												FROM ".Tprefix."marketreport_authors mra
												JOIN ".Tprefix."users u ON (u.uid=mra.uid)
												WHERE mra.mrid='".$db->escape_string($this->marketreport['mrid'])."'");
		if($db->num_rows($query) > 0) {
			while($author = $db->fetch_assoc($query)) {
				$authors[$author['uid']] = $author;
			}
			return $authors;
		}
		return false;
	}

}
/* Market report Class --END */
?>