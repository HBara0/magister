<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * [reportingQR_class]
 * $id: reportingQR_class.php
 * Created:        @[tony.assaad]    Feb 25, 2013 | 10:30:00 PM
 * Last Update:    @tony.assaad		March 26, 2013 | 3:30:00 PM
 */

class ReportingQr Extends Reporting {
    private $status = 0; //0=No errors;1=Subject missing;2=Entry exists;3=Error saving;4=validation violation

    public function __construct($reportdata = array()) {
        parent::__construct($reportdata);
    }

    public function read_products() {
        global $db;
        if(is_array($this->report['products']) && !empty($this->report['products'])) {
            $query_extrawhere = ' AND pid NOT IN ('.implode(',', array_keys($this->report['products'])).')';
        }

        $query = $db->query("SELECT pid FROM ".Tprefix."productsactivity WHERE rid=".$this->report['rid'].$query_extrawhere);
        if($db->num_rows($query) > 0) {
            while($product = $db->fetch_assoc($query)) {
                $product_obj = new Products($product['pid']);
                $this->report['products'][$product['pid']] = $product_obj->get()['name'];
            }
        }
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

//				$this->report['classifiedpactivity']['amount']['percentage'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']] = floor(($this->report['classifiedpactivity']['amount']['actual'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']]/$this->report['classifiedpactivity']['amount']['forecast'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']])*100);
//				$this->report['classifiedpactivity']['quantity']['percentage'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']] = floor(($this->report['classifiedpactivity']['quantity']['actual'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']]/$this->report['classifiedpactivity']['quantity']['forecast'][$this->report['year']][$this->report['quarter']][$this->report['affid']][$products_activityrow['psid']][$products_activityrow['pid']])*100);
//
                $this->report['products'][$products_activityrow['pid']] = $products_activityrow['productname'];
                $this->report['productssegments'][$products_activityrow['psid']] = $products_activityrow['segment'];

                if(!empty($products_activityrow['originalCurrency']) && $get_prevactivity == true) {
                    $this->report['currencies'][$products_activityrow['originalCurrency']] = $products_activityrow['originalCurrency'];
                }
            }

            $db->free_result($products_activity_query);

            if($get_prevactivity == true) {
                $this->read_prev_products_activity();
                $this->read_next_products_activity();
            }
        }
        else {
            if($get_prevactivity == true) {
                $this->read_prev_products_activity();
            }
        }
        return false;
    }

    public function read_next_products_activity() {
        if($this->report['quarter'] == 4) {
            return false;
        }
        $has_proudct_avtivity = false;
        for($quarter = $this->report['quarter'] + 1; $quarter <= 4; $quarter++) {
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
                                    if(is_array($this->report['classifiedpactivity'][$category]['forecast'][$this->report['year']][$quarter])) {
                                        foreach($this->report['classifiedpactivity'][$category]['forecast'][$this->report['year']][$quarter] as $affid => $affiliates_data) {
                                            foreach($affiliates_data as $psid => $productssegments_data) {
                                                foreach($productssegments_data as $pid => $products_data) {
                                                    $this->report['classifiedpactivity'][$category]['actual'][$this->report['year']][$quarter][$affid][$psid][$pid] = $products_data;
                                                }
                                            }
                                        }
                                    }
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
                foreach($this->report['classifiedpactivity']['amount']['forecast'][$this->report['year']][$this->report['quarter']] as $affid => $affiliates_data) {
                    foreach($affiliates_data as $psid => $productssegments_data) {
                        foreach($productssegments_data as $pid => $products_data) {
                            $this->report['classifiedpactivity']['amount']['actual'][$this->report['year']][$quarter][$affid][$psid][$pid] = 0;
                            if($products_data != 0) {
                                $this->report['classifiedpactivity']['amount']['actual'][$this->report['year']][$quarter][$affid][$psid][$pid] = $products_data;
                                for($q = $quarter - 1; $q >= 1; $q--) {
                                    $this->report['classifiedpactivity']['amount']['actual'][$this->report['year']][$quarter][$affid][$psid][$pid] -= $this->report['classifiedpactivity']['amount']['actual'][$this->report['year']][$q][$affid][$psid][$pid];
                                }
                            }

                            $this->report['classifiedpactivity']['purchasedQty']['actual'][$this->report['year']][$quarter][$affid][$psid][$pid] = 0;
                            if($this->report['classifiedpactivity']['purchasedQty']['forecast'][$this->report['year']][$this->report['quarter']][$affid][$psid][$pid] != 0) {
                                $this->report['classifiedpactivity']['purchasedQty']['actual'][$this->report['year']][$quarter][$affid][$psid][$pid] = $this->report['classifiedpactivity']['purchasedQty']['forecast'][$this->report['year']][$this->report['quarter']][$affid][$psid][$pid];
                                for($q = $quarter - 1; $q >= 1; $q--) {
                                    $this->report['classifiedpactivity']['purchasedQty']['actual'][$this->report['year']][$quarter][$affid][$psid][$pid] -= $this->report['classifiedpactivity']['purchasedQty']['actual'][$this->report['year']][$q][$affid][$psid][$pid];
                                }
                            }
                            if($this->report['quarter'] != 4) {
                                $this->report['classifiedpactivity']['amount']['actual'][$this->report['year']][$quarter][$affid][$psid][$pid] /= (5 - $quarter);
                                $this->report['classifiedpactivity']['purchasedQty']['actual'][$this->report['year']][$quarter][$affid][$psid][$pid] /= (5 - $quarter);
                                $this->report['classifiedpactivity_class']['amount']['actual'][$this->report['year']][$quarter][$affid][$psid][$pid] = 'mainbox_forecast';
                                $this->report['classifiedpactivity_class']['purchasedQty']['actual'][$this->report['year']][$quarter][$affid][$psid][$pid] = 'mainbox_forecast';
                                $this->report['forecasteditems']['purchasedQty']['actual'][$this->report['year']][$quarter][$affid][$psid][$pid] = 1;
                                $this->report['forecasteditems']['amount']['actual'][$this->report['year']][$quarter][$affid][$psid][$pid] = 1;
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
                    if(is_array($this->report['products'])) {
                        $this->report['products'] += $prev_products;
                    }
                    else {
                        $this->report['products'] = $prev_products;
                    }
                }

                $prev_productsactivity = $newreport->get_products_activity();
                if(isset($this->report['productsactivity']) && !empty($prev_productsactivity)) {
                    $this->report['productsactivity'] += $prev_productsactivity;
                }
                else {
                    $this->report['productsactivity'] = $prev_productsactivity;
                }

                $prev_productssegments = $newreport->get_productssegments();
                if(!empty($prev_productssegments)) {
                    if(isset($this->report['productssegments']) && is_array($this->report['productssegments'])) {
                        $this->report['productssegments'] += $prev_productssegments;
                    }
                    else {
                        $this->report['productssegments'] = $prev_productssegments;
                    }
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

    public function get_classified_classes() {
        return $this->report['classifiedpactivity_class'];
    }

    public function get_forecasted_items() {
        return $this->report['forecasteditems'];
    }

    public function get_affiliate() {
        return new Affiliates($this->report['affid']);
    }

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
                $representatives[$representative['erpid'].$representative['rpid']] = $representative;
            }
            return $representatives;
        }
        return false;
    }

    public function get_report_supplier_audits() {
        global $db;
        global $db;
        $suppaudits = SupplierAudits::get_data(array('eid' => $this->report['spid']), array('returnarray' => true));
        if(is_array($suppaudits)) {
            foreach($suppaudits as $suppaudit) {
                $audits[] = new Users($suppaudit->uid);
            }
        }
        return $audits;
//        return $db->fetch_assoc($db->query("SELECT u.uid,displayName AS employeeName, u.email
//			FROM ".Tprefix."users u
//			JOIN ".Tprefix."suppliersaudits sa ON (sa.uid=u.uid)
//			WHERE sa.eid=".$this->report['spid'].""));
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

    public function get_report_supplier($return_object = false) {
        global $db;
        $report_supplier = new Entities($this->report['spid'], '', false);
        if($return_object == true) {
            return $report_supplier;
        }
        else {
            return $this->reportdetails['supplier'] = $report_supplier->get();
        }
    }

    public function get_classified_productsactivity() {
        return $this->report['classifiedpactivity'];
    }

    public function get_products_activity() { /* to check if isset then  read product  */
        return $this->report['productsactivity'];
    }

    public function get_currencies() {
        return $this->report['currencies'];
    }

    public function get_status() {
        return $this->status;
    }

    public function check_outliers($method = 'standarddev', $threshold = 3) {
        global $db;

        $this->read_products();
        $products = $this->get_products();

        if(is_array($products)) {
            foreach($products as $pid => $product) {
                $pid = intval($pid);
                if($method == 'standarddev') {
                    /* Default $threshold 3 */
                    $query = $db->query('SELECT pa1.quantity, pa1.turnOver
										FROM productsactivity pa1,
										(SELECT AVG(quantity) AS qtymean, STDDEV(quantity) AS qtystddev, AVG(turnover) AS tovmean, STDDEV(turnover) AS tovstddev
										FROM productsactivity WHERE pid='.$pid.' AND (quantity!=0 OR turnOver!=0) AND rid IN (SELECT rid FROM reports WHERE affid="'.$this->report['affid'].'" AND spid="'.$this->report['spid'].'" AND rid!='.$this->report['rid'].')) as pa2
										WHERE (pa1.quantity !=0 OR pa1.turnOver!=0) AND (ABS(pa1.quantity - pa2.qtymean) / pa2.qtystddev > '.$threshold.' OR ABS(pa1.turnover - pa2.tovmean) / pa2.tovstddev > '.$threshold.') AND pa1.pid='.$pid.' AND pa1.rid='.$this->report['rid']);
                }
                elseif($method == 'avgplusdev') {
                    /* Default $threshold 1.5 */
                    $query = $db->query('SELECT pa1.quantity, pa1.turnOver
										FROM productsactivity pa1,
										(SELECT AVG(quantity)+'.$threshold.'*STDDEV(quantity) as qtythreshold, AVG(turnover/quantity)+'.$threshold.'*STDDEV(turnover/quantity) as tovthreshold
										FROM productsactivity WHERE pid='.$pid.' AND (quantity!=0 OR turnOver!=0) AND rid IN (SELECT rid FROM reports WHERE affid="'.$this->report['affid'].'" AND spid="'.$this->report['spid'].'" AND rid!='.$this->report['rid'].')) as pa2
										WHERE (pa1.quantity !=0 OR pa1.turnOver!=0) AND (pa1.quantity > pa2.qtythreshold OR pa1.turnover > pa2.tovthreshold) AND pa1.pid='.$pid.' AND pa1.rid='.$this->report['rid']);
                }
                elseif($method == 'quartiles') {
                    /* Default $threshold 0.675 */
                    $query = $db->query('SELECT pa1.quantity, pa1.turnOver
										FROM productsactivity pa1,
										(SELECT AVG(quantity) - STD(quantity) AS qtythreshold_minus, (AVG(quantity) + STD(quantity) * '.$threshold.') AS qtythreshold_plus, AVG(turnover/quantity) - STD(turnover/quantity) AS tovthreshold_minus, (AVG(turnover/quantity) + STD(turnover/quantity) * '.$threshold.') AS tovthreshold_plus
										FROM productsactivity WHERE pid='.$pid.' AND (quantity!=0 OR turnOver!=0) AND rid IN (SELECT rid FROM reports WHERE affid="'.$this->report['affid'].'" AND spid="'.$this->report['spid'].'" AND rid!='.$this->report['rid'].')) as pa2
										WHERE (pa1.quantity !=0 OR pa1.turnOver!=0) AND (pa1.quantity > pa2.qtythreshold_plus OR pa1.turnover > pa2.tovthreshold_plus) AND pa1.pid='.$pid.' AND pa1.rid='.$this->report['rid']);
                }
                else {
                    return false;
                }
                if($db->num_rows($query) > 0) {
                    while($outlier = $db->fetch_assoc($query)) {
                        $outliers[$pid] = $outlier;
                    }
                }
            }
            return $outliers;
        }
        return false;
    }

    public function validate_forecasts($data, $currencies, $options = array()) {
        global $db, $core;

        $validation_items = array('sales' => 'turnOver', 'quantity' => 'quantity');
        $correctionsign = '&ge; ';
        if($this->report['quarter'] == 4) {
            $correctionsign = '&equiv; ';
        }

        if(is_array($data)) {
            $query = $db->query("SELECT uid,pid, SUM(quantity) AS quantity, SUM(turnOver) AS turnOver
							FROM ".Tprefix."productsactivity pa
							JOIN ".Tprefix."reports r ON (r.rid=pa.rid)
							WHERE r.quarter<'".$this->report['quarter']."' AND r.year='".$this->report['year']."' AND r.affid='".$this->report['affid']."' AND r.spid='".$this->report['spid']."'
							GROUP BY pa.pid");
            if($db->num_rows($query) > 0) {
                while($prev_data_item = $db->fetch_assoc($query)) {
                    $prev_data[$prev_data_item['pid']] = $prev_data_item;
                }
            }

            foreach($data as $productactivity) {
                if(empty($productactivity['pid'])) {
                    continue;
                }

                if(isset($prev_data[$productactivity['pid']])) {
                    foreach($validation_items as $validation_key => $validation_item) {
                        $actual_current_validation = $productactivity[$validation_item];
                        if($validation_key == 'sales' && isset($productactivity['fxrate'])) {
                            $actual_current_validation = floor($productactivity[$validation_item] / $productactivity['fxrate'], 4);
                        }

                        $actual_current_data_querystring = 'uid!='.$core->user['uid'];
                        if(isset($productactivity['paid']) && !empty($productactivity['paid'])) {
                            $actual_current_data_querystring = 'pa.paid!='.$productactivity['paid'];
                        }

                        $actual_current_data = $db->fetch_assoc($db->query("SELECT SUM(".$validation_key."Forecast) AS forecastsum, SUM(".$validation_item.") AS actualsum FROM ".Tprefix."productsactivity pa JOIN ".Tprefix."reports r ON (r.rid=pa.rid) WHERE pid='".$db->escape_string($productactivity['pid'])."' AND quarter='".$this->report['quarter']."' AND year='".$this->report['year']."' AND affid='".$this->report['affid']."' AND spid='".$this->report['spid']."' AND {$actual_current_data_querystring}"));

                        $actual_forecast = ($prev_data[$productactivity['pid']][$validation_item] + $actual_current_validation + $actual_current_data['actualsum']);
                        $actual_current_forecast = $productactivity[$validation_key.'Forecast'] + $actual_current_data['forecastsum'] + $otheremplforecasts[$productactivity['pid']][$validation_item];

                        $otheremplforecasts[$productactivity['pid']][$validation_item] += $productactivity[$validation_key.'Forecast'];
                        if(floor($actual_forecast, 4) > floor($actual_current_forecast, 4) || ($this->report['quarter'] == 4 && floor($actual_forecast, 4) < floor($actual_current_forecast, 4))) {
                            if($options['source'] == 'finalize') {
                                $user = new Users($productactivity['uid']);
                                $product = new Products($productactivity['pid']);
                                $forecast_corrections[$productactivity['pid']]['name'] = $product->get_displayname();
                                $forecast_corrections[$productactivity['pid']]['user'] = $user->get_displayname();
                                unset($user, $product);
                            }
                            else {
                                $forecast_corrections[$productactivity['pid']]['name'] = $productactivity['productname'];
                                $forecast_corrections[$productactivity['pid']][$validation_key] = $correctionsign.floor($actual_forecast, 4);
                            }
                        }
                        else {
                            unset($forecast_corrections[$productactivity['pid']][$validation_key]);
                        }
                    }
                }
                else {
                    foreach($validation_items as $validation_key => $validation_item) {
                        $actual_forecast = $productactivity[$validation_item];
                        if($validation_key == 'sales') {
                            if(isset($productactivity['fxrate']) && $productactivity['fxrate'] != 1) {
                                $actual_forecast = floor($productactivity[$validation_item] / $productactivity['fxrate'], 4);
                            }
                        }
                        if($productactivity[$validation_key.'Forecast'] < floor($actual_forecast, 4) || ($this->report['quarter'] == 4 && floor($productactivity[$validation_key.'Forecast'], 4) > floor($actual_forecast, 4))) {
                            if($options['source'] == 'finalize') {
                                $user = new Users($productactivity['uid']);
                                $product = new Products($productactivity['pid']);
                                $forecast_corrections[$productactivity['pid']]['name'] = $product->get_displayname();
                                $forecast_corrections[$productactivity['pid']]['user'] = $user->get_displayname();
                                unset($user, $product);
                            }
                            else {
                                $forecast_corrections[$productactivity['pid']]['name'] = $productactivity['productname'];
                                $forecast_corrections[$productactivity['pid']][$validation_key] = $correctionsign.floor($actual_forecast, 4);
                            }
                        }
                    }
                }
            }
            if(is_array($forecast_corrections)) {
                $forecast_corrections = array_filter($forecast_corrections);
                if(!empty($forecast_corrections)) {
                    return $forecast_corrections;
                }
            }
            return true;
        }
        return false;
    }

    /* Setter Functionality - START */
    public function save_productactivity($data, $currencies, $options = array()) {
        global $db, $core, $log;

        /* Check if audit - START */
        if($options['isauditor'] != '1') {
            $existingentries_query_string = ' AND (uid='.$core->user['uid'].' OR uid=0)';
        }
        /* Check if audit - END */
        if(is_array($data)) {
            foreach($data as $productdata) {
//$currencies = $this->get_currency_Byrate(array('fxrate' => $productdata['fxrate']));
                if(!empty($productdata['pid']) && isset($productdata['pid'])) {
                    if($productdata['fxrate'] != 1 && isset($productdata['fxrate'])) {
                        $productdata['turnOverOc'] = $productdata['turnOver'];
                        $productdata['turnOver'] = floor($productdata['turnOver'] / $productdata['fxrate'], 4);
                        $productdata['originalCurrency'] = $currencies[$productdata['fxrate']];
                    }

                    if(value_exists('productsactivity', 'pid', $productdata['pid'], ' rid='.$this->report['rid'])) {
                        if(isset($productdata['paid']) && !empty($productdata['paid'])) {
                            $update_query_where = 'paid='.$db->escape_string($productdata['paid']);
                        }
                        else {
                            unset($productdata['paid']);
                            $update_query_where = 'rid='.$this->report['rid'].' AND pid='.$db->escape_string($productdata['pid']);
                        }

                        unset($productdata['fxrate'], $productdata['productname']);
                        $db->update_query('productsactivity', $productdata, $update_query_where);
                        $processed_once = true;
                    }
                    else {
                        $productdata['rid'] = $this->report['rid'];
                        $productdata['uid'] = $core->user['uid'];

                        unset($productdata['fxrate'], $productdata['productname']);
                        $db->insert_query('productsactivity', $productdata);
                        $cachearr['usedpaid'][] = $db->last_id();
                        $processed_once = true;
                    }

                    $cachearr['usedpids'][] = $productdata['pid'];
                    if(isset($productdata['paid']) && !empty($productdata['paid'])) {
                        $cachearr['usedpaid'][] = $productdata['paid'];
                    }
                }
            }

            if($processed_once === true) {
                if(is_array($cachearr['usedpaid'])) {
//$delete_query_where = ' OR paid NOT IN ('.implode(', ', $cachearr['usedpaid']).')';
                }

                $db->query("DELETE FROM ".Tprefix."productsactivity WHERE rid=".$this->report['rid']." AND (pid NOT IN (".implode(', ', $cachearr['usedpids'])."){$delete_query_where}){$existingentries_query_string}");
                $update_status = $db->update_query('reports', array('prActivityAvailable' => 1), 'rid='.$this->report['rid'].'');
            }
            /* Data to be passed */
            if($options['transFill'] != '1') {
                record_contribution($this->report['rid']);
            }

            $log->record($this->report['rid']);
        }
    }

    /* Setter Functionality --END */
    public function lock_report() {
        global $db;
        $query = $db->update_query('reports', array('isLocked' => 1), 'rid='.$this->report['rid']);
        if($query) {
            $this->status = 0;
        }
    }

    public function set_status() {
        global $db;
        $query = $db->update_query('reports', array('status' => 1), 'rid='.$this->report['rid']);
        if($query) {
            $this->status = 0;
        }
    }

    public function approve_report($rid) {
        global $db;
        if(isset($rid) && !empty($rid)) {
            $query = $db->update_query('reports', array('isApproved' => 1), 'rid='.intval($rid));
            if($query) {
                $this->status = 0;
            }
        }
    }

    public function get_products() {
        return $this->report['products'];
    }

    public function get_productssegments() {
        return $this->report['productssegments'];
    }

    private static function create_loginkey() {
        if(function_exists('random_string')) {
            return random_string(40);
        }
        else {
            return self::random_string(40);
        }
    }

    public function create_recipient($id, $type, $identifier = '') {
        global $db, $core;

        if(empty($identifier)) {
            $identifier = $this->report['identifier'];
        }

        if(value_exists('reporting_qrrecipients', $type, $id, 'reportIdentifier="'.$db->escape_string($identifier).'"')) {
            return false;
        }

        if($type == 'unregisteredRcpts') {
            $id = $core->sanitize_email($id);
        }

        $password = Accounts::random_string(10, true);
        $salt = random_string(10);
        $loginKey = $this->create_loginkey();
        $token = md5(uniqid(microtime(), true));

        $recipient_data = array(
                'reportIdentifier' => $identifier,
                $type => $id,
                'token' => $token,
                'loginKey' => $loginKey,
                'password' => base64_encode($password.$salt),
                'salt' => $salt,
                'sentOn' => TIME_NOW,
                'sentBy' => $core->user['uid']
        );
        $query = $db->insert_query('reporting_qrrecipients', $recipient_data);
        if($query) {
            return true;
        }
        else {
            return false;
        }
    }

    public function get_recipient($rpid = '') {
        global $db;
        if(is_array($rpid) && (!empty($this->report['identifier']))) {
            $where = " 	WHERE rq.rpid IN (".implode(',', $rpid).") AND reportIdentifier='".$db->escape_string($this->report['identifier'])."'";
        }
        elseif(!empty($this->report['identifier'])) {
            $where = " WHERE reportIdentifier='".$db->escape_string($this->report['identifier'])."'";
        }
        $recipients_query = $db->query("SELECT * FROM ".Tprefix."reporting_qrrecipients rq
				JOIN ".Tprefix."entitiesrepresentatives er ON(er.rpid=rq.rpid)
				JOIN ".Tprefix."representatives r ON (r.rpid=rq.rpid)
				{$where}");
        if($db->num_rows($recipients_query) > 0) {
            while($recipient = $db->fetch_assoc($recipients_query)) {
                $recipients[$recipient['rpid']] = $recipient;
            }
            return $recipients;
        }
    }

    public static function get_reports(array $options = array()) {
        global $db;

        if($options['filter_where']) {
            $query_where = ' WHERE '.$options['filter_where'];
        }

        $reports_query = $db->query("SELECT rid FROM ".Tprefix.'reports'.$query_where);
        while($allreports = $db->fetch_assoc($reports_query)) {
            $allqrreports[$allreports['rid']] = new ReportingQr(array('rid' => $allreports['rid']));
        }
        return $allqrreports;
    }

    public function get_otherrecipient($id, $type) {
        global $db, $core;

        if($type == 'unregisteredRcpts') {
            $id = $core->sanitize_email($id);
            $recipient_query = ("SELECT * FROM ".Tprefix."reporting_qrrecipients WHERE unregisteredRcpts='".$db->escape_string($id)."' AND reportIdentifier='".$db->escape_string($this->report['identifier'])."'");
        }
        else {
            $recipient_query = ("SELECT u.uid, u.email, u.displayName, rq.*
								FROM ".Tprefix."reporting_qrrecipients rq
								JOIN ".Tprefix."users u ON (u.uid=rq.uid)
								WHERE rq.uid=".intval($id)." AND reportIdentifier='".$db->escape_string($this->report['identifier'])."'");
        }
        $user_recipients_query = $db->query($recipient_query);

        if($db->num_rows($user_recipients_query) > 0) {
            return $db->fetch_assoc($user_recipients_query);
        }
        return false;
    }

    public function auditor_ratings($supplierid = '', $userid = '') {
        if(!empty($supplierid)) {
            $supplier_where = ' AND eid = '.intval($supplierid);
        }
        $suppliers = Entities::get_column('eid', 'type="s" AND approved=1 AND noQReportReq=0 AND noQReportSend=0 '.$supplier_where, array('operators' => array('filter' => 'CUSTOMSQLSECURE'), 'returnarray' => true));
        if(!empty($userid)) {
            $userwhere = ' AND uid = '.intval($userid);
        }
        $users = Users::get_column('uid', 'gid != 7'.$userwhere, array('operators' => array('filter' => 'CUSTOMSQLSECURE'), 'returnarray' => true));
        $quarter = currentquarter_info(true);
        if(is_array($suppliers) && is_array($users)) {
            if($quarter['quarter'] == 1) {
                $quarter['quarter'] = 4;
                $quarter['year'] = $quarter['year'] - 1;
            }
            else {
                $quarter['quarter'] --;
            }
            if($quarter['quarter'] == 4) {
                $month = 1;
            }
            else {
                $month = ($quarter['quarter'] * 3) + 1;
            }

            $duedate = strtotime($quarter['year'].'-'.$month.'-15');
            $supplieraudits = SupplierAudits::get_data(array('eid' => $suppliers, 'uid' => $users), array('returnarray' => true));
            if(is_array($supplieraudits)) {
                foreach($supplieraudits as $audit) {
                    $supplier_qreports = ReportingQr::get_reports(array('filter_where' => 'type = "q" AND spid = '.$audit->eid.' AND year = '.$quarter['year'].' AND quarter = '.$quarter['quarter']));
                    if(is_array($supplier_qreports)) {
                        foreach($supplier_qreports as $supplier_qreport) {
                            if(!isset($data[$audit->uid]['count'])) {
                                $data[$audit->uid]['count'] = 1;
                            }
                            else {
                                $data[$audit->uid]['count'] ++;
                            }
                            if(isset($supplier_qreport->finishDate) && !empty($supplier_qreport->finishDate) && $supplier_qreport->isLocked == 1) {
                                $data[$audit->uid][$audit->eid][$supplier_qreport->affid]['finished'] = $supplier_qreport->finishDate - $duedate;
                            }
                            else {
                                $data[$audit->uid][$audit->eid][$supplier_qreport->affid]['remaining'] = $duedate - TIME_NOW;
                            }
                        }
                    }
                }
                return $data;
            }
        }
        return false;
    }

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