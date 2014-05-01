<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Budgets_class.php
 * Created:        @tony.assaad    Aug 12, 2013 | 2:10:18 PM
 * Last Update:    @tony.assaad    Aug 13, 2013 | 4:15:18 PM
 */

class Budgets {
    private $budget = array();
    private $errorcode = 0;

    public function __construct($id = '', $simple = false, $budgetdata = '', $isallbudget = false) {
        if(isset($id) && !empty($id)) {
            $this->budget = $this->read($id, $simple, $isallbudget);
        }
    }

    private function read($id, $simple = false, $isallbudget = false) {
        global $db;
        if(empty($id) && empty($isallbudget)) {
            return false;
        }

        $query_select = '*';
        if($simple == true) {
            $query_select = 'year, description';
        }
        if($isallbudget == true) {
            $queryall = $db->query("SELECT DISTINCT(year), bid, identifier, description, affid, spid, currency,isLocked,isFinalized,finalizedBy,status,createdOn,createdBy,modifiedBy 
									FROM ".Tprefix."budgeting_budgets GROUP BY year ORDER BY year DESC  ");
            if($db->num_rows($queryall) > 0) {
                while($budget = $db->fetch_assoc($queryall)) {
                    $allbudgets[] = $budget;
                }
            }
            return $allbudgets;
        }
        else {
            return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."budgeting_budgets WHERE bid=".$db->escape_string($id)));
        }
    }

    private function budget_exists($id) {
        global $db;
        if(!empty($id)) {
            if(value_exists('budgeting_budgets', 'bid', $data['bid'])) {
                return true;
            }
            return false;
        }
        return false;
    }

    private static function budget_exists_bydata($data) {
        global $db;
        if(isset($data['affid'], $data['spid'], $data['year']) && !is_empty($data['affid'], $data['spid'], $data['year'])) {
            $budget_existquery = $db->query('SELECT bid FROM '.Tprefix.'budgeting_budgets WHERE affid='.intval($data['affid']).' AND spid='.intval($data['spid']).' AND year='.intval($data['year']));
            if($db->num_rows($budget_existquery) > 0) {
                return true;
            }
            return false;
        }
        return false;
    }

    private function budgetline_exists_bydata($data) {
        global $db;
        if(!empty($data)) {
            if(isset($data['pid'], $data['cid'], $data['saleType'])) {
                if(value_exists('budgeting_budgets_lines', 'bid', $this->budget['bid'], 'pid='.intval($data['pid']).' AND cid='.intval($data['cid']).' AND saleType='.intval($data['saleType']))) {
                    return true;
                }
            }
            return false;
        }
        return false;
    }

    /*
     * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     * The below function needs improvement
     * !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
     */
    public function populate_budgetyears($data = array()) {
        global $db;
        $budget_yearquery = $db->query('SELECT bid,year FROM '.Tprefix.'budgeting_budgets WHERE spid='.$data['spid'].' AND affid='.$data['affid'].' AND isLocked=0 ORDER BY year DESC');
        if($db->num_rows($budget_yearquery) > 0) {
            while($budget_year = $db->fetch_assoc($budget_yearquery)) {
                $budget_years[] = $budget_year['year'];
            }
        }
//get next year and return budget
        $next_budgetyear = date('Y', strtotime('+1 year'));
        $budget_nextyearquery = $db->query('SELECT bid,year,isLocked FROM '.Tprefix.'budgeting_budgets WHERE spid='.$data['spid'].' 
												AND affid='.$data['affid'].' AND year='.$next_budgetyear.' ORDER BY year DESC');
        if($db->num_rows($budget_nextyearquery) > 0) {
            while($budget_nextyear = $db->fetch_assoc($budget_nextyearquery)) {
                if($budget_nextyear['isLocked'] == 0) {
                    $budget_years[] = $budget_nextyear['year'];
                }
            }
        }
        else {
            $budget_years[] = $next_budgetyear;
        }

        if(is_array($budget_years)) {
            $budget_years = array_unique($budget_years);
        }
        return $budget_years;
    }

//	private function populate_nextyear($data = array()) {
//		global $db;
//		$budget_nextyear = $db->query('SELECT year FROM '.Tprefix.'budgeting_budgets WHERE spid='.$data['spid'].' AND affid='.$data['affid'].' AND year = year(CURDATE()) + 1');
//		if($db->num_rows($budget_nextyear) < 0) {
//			return true;
//		}
//		else {
//			return false;
//		}
//	}

    public static function save_budget($budgetdata = array(), $budgetline_data = array()) {
        global $db, $core, $log;

        if(is_array($budgetdata)) {
            if(is_empty($budgetdata['year'], $budgetdata['affid'], $budgetdata['spid'])) {
                $this->errorcode = 2;
                return false;
            }
            /* Check if budget exists, then process accordingly */
            if(!Budgets::budget_exists_bydata($budgetdata)) {
                $budget_data = array('identifier' => substr(uniqid(time()), 0, 10),
                        'year' => $budgetdata['year'],
                        'affid' => $budgetdata['affid'],
                        'spid' => $budgetdata['spid'],
                        'createdBy' => $core->user['uid'],
                        'createdOn' => TIME_NOW
                );

                $insertquery = $db->insert_query('budgeting_budgets', $budget_data);
                if($insertquery) {
                    if(is_object($this)) {
                        $this->budget['bid'] = $db->last_id();
                        $log->record('savenewbudget', $this->budget['bid']);
                        $this->save_budgetlines($budgetline_data, $this->budget['bid']);
                    }
                    else {
                        $bid = $db->last_id();
                        $budget = new Budgets($bid);
                        $log->record('savenewbudget', $bid);
                        $budget->save_budgetlines($budgetline_data);
                    }
                }
            }
            else {
                $existing_budget = Budgets::get_budget_bydata($budgetdata);
                if(isset($this)) {
                    $this->budget['bid'] = $existing_budget['bid'];
                    $this->save_budgetlines($budgetline_data, $this->budget['bid']);
                }
                else {
                    $budget = new Budgets($existing_budget['bid']);
                    $budget->save_budgetlines($budgetline_data);
                }
                $log->record('updatedbudget', $existing_budget['bid']);
            }
        }
    }

    private function save_budgetlines($budgetline_data = array(), $bid = '') {
        global $db;
        if(isset($budgetline_data['customerName'])) {
            unset($budgetline_data['customerName']);
        }

        if(empty($bid)) {
            $bid = $this->budget['bid'];
        }

        if(is_array($budgetline_data)) {
            foreach($budgetline_data as $blid => $data) {
                if(!isset($data['bid'])) {
                    $data['bid'] = $bid;
                }
                if(isset($data['blid']) && !empty($data['blid'])) {
                    $budgetlineobj = new BudgetLines($data['blid']);
                }
                else {
                    $budgetline = BudgetLines::get_budgetline_bydata($data);
                    if($budgetline != false) {
                        $budgetlineobj = new BudgetLines($budgetline['blid']);
                        $data['blid'] = $budgetline['blid'];
                    }
                    else {
                        $budgetlineobj = new BudgetLines();
                    }
                }

                if($data['unspecifiedCustomer'] == 1 && empty($data['cid'])) {
                    $data['altCid'] = 'Unspecified Customer';
                    if(empty($data['customerCountry'])) {
                        $data['customerCountry'] = $this->get_affiliate()->get_country()->get()['name'];
                    }
                }

                if(empty($data['pid']) || (empty($data['cid']) && empty($data['altCid']))) {
                    if(!empty($data['blid'])) {
                        $removed_lines[] = $data['blid'];
                    }
                    continue;
                }

                if(!empty($data['cid']) && $data['unspecifiedCustomer'] != 1) {
                    $data['altCid'] = NULL;
                    $data['customerCountry'] = 0;
                }

                if(empty($data['s1Perc']) && empty($data['s2Perc'])) {
                    $data['s1Perc'] = $data['s2Perc'] = 50;
                }

                unset($data['unspecifiedCustomer']);
                if(isset($data['blid']) && !empty($data['blid'])) {
                    $budgetlineobj->update($data);
                    $this->errorcode = 0;
                }
                else {
                    $budgetlineobj->create($data);
                }
            }
            if(is_array($removed_lines)) {
                foreach($removed_lines as $removedblid) {
                    $budgetlineobj = new BudgetLines($removedblid);
                    $budgetlineobj->delete();
                }
            }
        }
    }

    public function import_budgetlines($budgetline_data = array()) {
        global $db, $core;
        if(is_array($budgetline_data)) {
            $budgetlineobj = new BudgetLines();
            $budget_data = array(
                    'pid' => $budgetline_data['pid'],
                    'cid' => $budgetline_data['cid'],
                    'amount' => $budgetline_data['amount'],
                    'income' => $budgetline_data['income'],
                    'saleType' => $budgetline_data['saleType'],
                    'createdBy' => $budgetline_data['createdBy']
            );
            $insertquery = $db->insert_query('budgeting_budgets_lines', $budget_data);
        }
    }

    public static function parse_saletype($abbrv) {
        global $db;
        if(!empty($abbrv)) {
            return $db->fetch_field($db->query("SELECT stid FROM ".Tprefix."saletypes WHERE abbreviation='".$db->escape_string($abbrv)."'"), 'stid');
        }
    }

    public static function get_saletype_byid($sitd) {
        global $db;
        if(!empty($sitd)) {
            return $db->fetch_field($db->query("SELECT title FROM ".Tprefix."saletypes WHERE stid='".$db->escape_string($sitd)."'"), 'title');
        }
    }

    public static function get_budgets_bydata($data = array()) {
        global $db;


        if(isset($data['affilliates'], $data['suppliers'], $data['years']) && !empty($data['affilliates']) && !empty($data['suppliers']) && !empty($data['years'])) {
            $budget_reportquery = $db->query("SELECT bid FROM ".Tprefix."budgeting_budgets 
														  WHERE year in(".$db->escape_string(implode(',', $data['years'])).") 
														  AND affid in(".$db->escape_string(implode(',', $data['affilliates'])).") 
														  AND spid in(".$db->escape_string(implode(',', $data['suppliers'])).")");
        }

        if($db->num_rows($budget_reportquery) > 0) {
            while($budget_reportids = $db->fetch_assoc($budget_reportquery)) {
                $budgetreport[$budget_reportids['bid']] = $budget_reportids['bid'];
            }
            return $budgetreport;
        }
    }

    public static function get_budget_bydata($data) {
        global $db;
        if(is_array($data)) {
            $budget = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."budgeting_budgets WHERE affid='".$data['affid']."' AND spid='".$data['spid']."' AND year='".$data['year']."'"));
            if(is_array($budget)) {
                return $budget;
            }
            return false;
        }
    }

    public function read_prev_budgetbydata($data = array(), $options = array()) {
        global $db;
        if(empty($data)) {
            $data['affid'] = $this->budget['affid'];
            $data['spid'] = $this->budget['spid'];
            $data['year'] = $this->budget['year'];
        }

        if(isset($options['filters']['businessMgr']) && is_array($options['filters']['businessMgr'])) {
            $budgetline_query_where = ' AND bdl.businessMgr IN ('.$db->escape_string(implode(',', $options['filters']['businessMgr'])).')';
        }

        for($year = $data['year']; $year >= ($data['year'] - 2); $year--) {
            if($year == $data['year']) {
                continue;
            }

            $prev_budget_bydataquery = $db->query("SELECT * 
					FROM ".Tprefix."budgeting_budgets bd 
					JOIN ".Tprefix."budgeting_budgets_lines bdl ON (bd.bid=bdl.bid) 
					WHERE affid='".$data['affid']."' AND spid='".$data['spid']."' AND year='".$year."'".$budgetline_query_where);
            if($db->num_rows($prev_budget_bydataquery) > 0) {
                while($prevbudget_bydata = $db->fetch_assoc($prev_budget_bydataquery)) {
                    if($prevbudget_bydata['cid'] == 0) {
                        $prevbudget_bydata['cid'] = md5($prevbudget_bydata['altCid'].$prevbudget_bydata['saltType'].$prevbudget_bydata['pid']);
                    }
                    $budgetline_details[$prevbudget_bydata['cid']][$prevbudget_bydata['pid']][$prevbudget_bydata['saleType']][] = $prevbudget_bydata;
                }
            }
        }

        return $budgetline_details;
    }

    public function get_budgetLines($bid = '', $options = array()) {
        global $db;
        if(empty($bid)) {
            $bid = $this->budget['bid'];
        }

        $options['order_by'] = ' ORDER BY pid ASC';

        if(isset($options['filters']['businessMgr']) && is_array($options['filters']['businessMgr'])) {
            $budgetline_query_where = ' AND businessMgr IN ('.$db->escape_string(implode(',', $options['filters']['businessMgr'])).')';
        }

        if(isset($bid) && !empty($bid)) {
//$prevbudgetline_details = $this->read_prev_budgetbydata();
            $budgetline_queryid = $db->query("SELECT * FROM ".Tprefix."budgeting_budgets_lines
											  WHERE bid IN (".$db->escape_string($bid).")".$budgetline_query_where.$options['order_by']);

            if($db->num_rows($budgetline_queryid) > 0) {
                while($budgetline_data = $db->fetch_assoc($budgetline_queryid)) {
                    if($budgetline_data['cid'] == 0) {
                        $budgetline_data['cid'] = md5($budgetline_data['altCid'].$budgetline_data['saltType'].$budgetline_data['pid']);
                    }
                    $budgetline = new BudgetLines($budgetline_data['blid']);
                    $prevbudgetline = new BudgetLines($budgetline_data['prevblid']);
                    $budgetline_details[$budgetline_data['cid']][$budgetline_data['pid']][$budgetline_data['saleType']] = $budgetline->get();
                    $budgetline_details[$budgetline_data['cid']][$budgetline_data['pid']][$budgetline_data['saleType']]['prevbudget'][] = $prevbudgetline->get();
                }
                return $budgetline_details;
            }
        }
    }

    public function get_actual_meditaiondata($data = array()) {
        global $db;
        if(is_array($data)) {
            if(empty($data['cid'])) {
                return false;
            }
            if(!empty($data['pid'])) {
                $where = "ms.pid =".$data['pid']."";
            }

            $mediation_result = $db->query("SELECT ime.imspid,ime.localid,ime.foreignname,ime.entityType,bl.cid, ims.quantity ,ims.price,ims.cost FROM ".Tprefix." integration_mediation_entities ime
					JOIN ".Tprefix."budgeting_budgets_lines bl ON (bl.cid = ime.localid)
					JOIN  ".Tprefix."integration_mediation_salesorderlines  ims ON (ims.pid=bl.pid) 
					WHERE ims.pid =".$data['pid']."  AND  ime.localid ='".$data['cid']."' AND ims.saleType=".$data['saleType']." AND ime.entityType='e'");

            if($db->num_rows($mediation_result) > 0) {
                while($rowmediationdata = $db->fetch_assoc($mediation_result)) {
                    $actual_mediationdata[$rowmediationdata['imspid']] = $rowmediationdata;
                }
                return $actual_mediationdata;
            }
        }
    }

    public static function get_availableyears() {
        global $db;
        $query = $db->query('SELECT DISTINCT(year) FROM '.Tprefix.'budgeting_budgets ORDER BY year DESC');
        if($db->num_rows($query) > 0) {
            while($year = $db->fetch_assoc($query)) {
                $years[$year['year']] = $year['year'];
            }
            return $years;
        }
        return false;
    }

    /* function return object Type --START */
    public function get_supplier() {
        return new Entities($this->budget['spid']);
    }

    public function get_affiliate() {
        return new Affiliates($this->budget['affid']);
    }

    public function get_currency() {
        return new Currencies($this->budget['originalCurrency']);
    }

    public function get_CreateUser() {
        return new Users($this->budget['createdBy']);
    }

    public function get_ModifyUser() {
        return new Users($this->budget['modifiedBy']);
    }

    public function get_FinalizeUser() {
        return new Users($this->budget['finalizedBy']);
    }

    public function get_LockUser() {
        return new Users($this->budget['lockedBy']);
    }

    /* function return object Type --END */
    public function get() {
        return $this->budget;
    }

    public function get_errorcode() {
        return $this->errorcode;
    }

}
/* Budgeting Line Class --START */

class BudgetLines {
    private $budgetline = array();

    public function __construct($budgetlineid = '') {
        if(!empty($budgetlineid)) {
            $this->budgetline = $this->read($budgetlineid);
        }
    }

    private function read($budgetlineid) {
        global $db;
        if(isset($budgetlineid) && !empty($budgetlineid)) {
            return $db->fetch_assoc($db->query("SELECT bdl.*, bd.bid
														FROM ".Tprefix."budgeting_budgets bd
														JOIN ".Tprefix."budgeting_budgets_lines bdl ON (bd.bid=bdl.bid)
														WHERE bdl.blid='".$db->escape_string($budgetlineid)."'"));
        }
    }

    public function create($budgetline_data = array()) {
        global $db, $core;

        if(is_array($budgetline_data)) {
//$budgetline_data['bid'] = $bid;
            if(empty($budgetline_data['createdBy'])) {
                $budgetline_data['createdBy'] = $core->user['uid'];
            }
            if(empty($budgetline_data['businessMgr'])) {
                $budgetline_data['businessMgr'] = $core->user['uid'];
            }
            unset($budgetline_data['customerName'], $budgetline_data['blid']);

            $insertquery = $db->insert_query('budgeting_budgets_lines', $budgetline_data);
            if($insertquery) {
                $this->errorcode = 0;
            }
        }
    }

    public function update($budgetline_data = array()) {
        global $db, $core;
        unset($budgetline_data['customerName']);
        $budgetline_data['modifiedBy'] = $core->user['uid'];
        $db->update_query('budgeting_budgets_lines', $budgetline_data, 'blid='.$budgetline_data['blid']);
    }

    public function delete() {
        global $db;
        $db->delete_query('budgeting_budgets_lines', 'blid='.$this->budgetline['blid']);
    }

    public function get_customer() {
        return new Entities($this->budgetline['cid'], '', false);
    }

    public function get_product() {
        return new Products($this->budgetline['pid']);
    }

    public function get_saletype() {
        return $this->budgetline['saleType'];
    }

    public function get_createuser() {
        return new Users($this->budgetline['createdBy']);
    }

    public function get_businessMgr() {
        return new Users($this->budgetline['businessMgr']);
    }

    public function get_modifyuser() {
        return new Users($this->budgetline['modifiedBy']);
    }

    public function parse_country() {
        global $lang;

        if(!empty($this->budgetline['customerCountry'])) {
            $country = new Countries($this->budgetline['customerCountry']);
        }
        else {
            $country = new Countries($this->get_customer()->get()['country']);
        }

        $country_name = $country->get()['name'];
        if(empty($country_name)) {
            return $lang->na;
        }
        else {
            return $country_name;
        }
    }

    public static function get_budgetline_bydata($data) {
        global $db;
        if(is_array($data)) {
            if(!isset($data['bid']) || empty($data['bid'])) {
                return false;
            }
            $budgetline_bydataquery = $db->query("SELECT * FROM ".Tprefix."budgeting_budgets_lines WHERE pid='".$data['pid']."' AND cid='".$data['cid']."' AND altCid='".$data['altCid']."' AND saleType='".$data['saleType']."' AND bid='".$data['bid']."'");
            if($db->num_rows($budgetline_bydataquery) > 0) {
                return $db->fetch_assoc($budgetline_bydataquery);
            }
            return false;
        }
    }

    public function get() {
        return $this->budgetline;
    }

}
/* Budgeting Line Class --END */
?>
