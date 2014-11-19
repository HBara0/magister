<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Budgets_class.php
 * Created:        @tony.assaad    Aug 12, 2013 | 2:10:18 PM
 * Last Update:    @tony.assaad    Aug 13, 2013 | 4:15:18 PM
 */

class Budgets extends AbstractClass {
    protected $data = array();
    protected $errorcode = null;

    const PRIMARY_KEY = 'bid';
    const TABLE_NAME = 'budgeting_budgets';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'bid, year, affid, spid';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = false, $budgetdata = '', $isallbudget = false) {
        if(isset($id) && !empty($id)) {
            $this->data = $this->read($id, $simple, $isallbudget);
        }
    }

    protected function create(array $data) {

    }

    protected function update(array $data) {

    }

    public function save(array $data = array()) {

    }

    protected function read($id, $simple = false, $isallbudget = false) {
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
									FROM ".Tprefix."budgeting_budgets GROUP BY year ORDER BY year DESC");
            if($db->num_rows($queryall) > 0) {
                while($budget = $db->fetch_assoc($queryall)) {
                    $allbudgets[] = $budget;
                }
            }
            return $allbudgets;
        }
        else {
            return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."budgeting_budgets WHERE bid=".intval($id)));
        }
    }

    private function budget_exists($id) {
        global $db;
        if(!empty($id)) {
            if(value_exists('budgeting_budgets', 'bid', $data['bid'])) { //Where is $data coming from?
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
                if(value_exists('budgeting_budgets_lines', 'bid', $this->data['bid'], 'pid='.intval($data['pid']).' AND cid='.intval($data['cid']).' AND saleType='.intval($data['saleType']))) {
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
                        $this->data['bid'] = $db->last_id();
                        $log->record('savenewbudget', $this->data['bid']);
                        $this->save_budgetlines($budgetline_data, $this->data['bid']);
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
                    $this->data['bid'] = $existing_budget['bid'];
                    $this->save_budgetlines($budgetline_data, $this->data['bid'], '', array('budgetdata' => $budgetdata));
                }
                else {
                    $budget = new Budgets($existing_budget['bid']);
                    $budget->save_budgetlines($budgetline_data, '', array('budgetdata' => $budgetdata));
                }
                $log->record('updatedbudget', $existing_budget['bid']);
            }
        }
    }

    private function save_budgetlines($budgetline_data = array(), $bid = '', $options = array()) {
        global $db;
        if(isset($budgetline_data['customerName'])) {
            unset($budgetline_data['customerName']);
        }

        if(empty($bid)) {
            $bid = $this->data['bid'];
        }
        // if the 2 budgetline are linked together
        if(is_array($budgetline_data)) {
            foreach($budgetline_data as $blid => $data) {
                if(!isset($data['bid']) && empty($data['bid'])) {
                    $data['bid'] = $bid;
                }

                if($data['unspecifiedCustomer'] == 1 && empty($data['cid'])) {
                    $data['altCid'] = 'Unspecified Customer';
                    if(empty($data['customerCountry'])) {
                        $data['customerCountry'] = $this->get_affiliate()->get_country()->coid;
                    }
                }

                if(!empty($data['cid']) && $data['unspecifiedCustomer'] != 1) {
                    $data['altCid'] = NULL;
                    $data['customerCountry'] = 0;
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

                if((empty($data['pid']) && empty($data['altPid'])) || (empty($data['cid']) && empty($data['altCid']))) {
                    if(!empty($data['blid'])) {
                        $removed_lines[] = $data['blid'];
                    }
                    continue;
                }

                if(empty($data['s1Perc']) && empty($data['s2Perc'])) {
                    $data['s1Perc'] = $data['s2Perc'] = 50;
                }

                if(isset($data['invoice'])) {
                    $invoiceentity = InvoiceTypes::get_data(array('affid' => $options['budgetdata']['affid'], 'invoicingEntity' => $data['invoice'], 'stid' => $data['saleType']));
                    if(is_object($invoiceentity)) {
                        if($invoiceentity->isAffiliate == 1) {
                            $data['invoiceAffid'] = $invoiceentity->invoiceAffid;
                        }
                    }
                }

                unset($data['unspecifiedCustomer']);
                if(isset($data['blid']) && !empty($data['blid'])) {
                    $budgetlineobj->update($data);
                    $budgetlineobj->save_interco_line($data);
                    $this->errorcode = 0;
                }
                else {
                    $budgetlineobj->create($data);
                    $budgetlineobj->save_interco_line($data);
                }
            }

            if(is_array($removed_lines)) {
                foreach($removed_lines as $removedblid) {
                    $budgetlineobj = new BudgetLines($removedblid);
                    $budgetlineobj->delete();
                    $budgetlineobj->delete_interco_line();
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
        if(isset($data['years']) && !empty($data['years'])) {
            if(is_array($data['suppliers'])) {
                array_walk($data['suppliers'], intval);
                $budget_reportquery = " AND spid IN (".implode(',', $data['suppliers']).")";
            }

            if(is_array($data['affiliates'])) {
                array_walk($data['affiliates'], intval);
                $budget_reportquery = " AND affid IN (".implode(',', $data['affiliates']).")";
            }
            $budget_reportquery = $db->query("SELECT bid FROM ".Tprefix."budgeting_budgets WHERE year=".intval($data['years']).$budget_reportquery);
        }
        if($budget_reportquery) {
            if($db->num_rows($budget_reportquery) > 0) {
                while($budget_reportids = $db->fetch_assoc($budget_reportquery)) {
                    $budgetreport[$budget_reportids['bid']] = $budget_reportids['bid'];
                }
                return $budgetreport;
            }
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
            $data['affid'] = $this->data['affid'];
            $data['spid'] = $this->data['spid'];
            $data['year'] = $this->data['year'];
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
                        $prevbudget_bydata['cid'] = md5($prevbudget_bydata['altCid'].$prevbudget_bydata['saleType'].$prevbudget_bydata['pid']);
                    }
                    $budgetline_details[$prevbudget_bydata['cid']][$prevbudget_bydata['pid']][$prevbudget_bydata['saleType']][] = $prevbudget_bydata;
                }
            }
        }

        return $budgetline_details;
    }

    public function read_prev_sales($data = array(), $options = array()) {
        global $intgconfig;

        $integration = new IntegrationOB($intgconfig['openbravo']['database'], $intgconfig['openbravo']['entmodel']['client']);

        $mediationproducts = IntegrationMediationProducts::get_products('affid='.$data['affid'].' AND localId IN (SELECT pid FROM '.Tprefix.'products WHERE spid IN ('.$data['spid'].')');
        foreach($mediationproducts as $product) {
            $products[] = $product->foreignId;
        }
        $orderline_query_where = ' AND m_product_id IN (\''.implode('\',\'', $products).'\')';
        $filters = "ad_org_id IN ('".$data['integrationOBOrgId']."') AND docstatus NOT IN ('VO', 'CL') AND (dateinvoiced BETWEEN '".date('Y-m-d 00:00:00', ($data['year'] - 1).'01-01')."' AND '".date('Y-m-d 00:00:00', ($data['year'] - 1).'-12-31')."')";
        $filters .= ' AND c_invoice IN (SELECT DISTINCT(c_invoice) FROM c_invoiceline WHERE ad_org_id IN (\''.$data['integrationOBOrgId'].'\')'.$orderline_query_where.')';

        $invoices = new IntegrationOBInvoice(null, $integration->get_dbconn());
        $invoices->get_aggregates('qty', $groupby, $filters);
        $invoices = $integration->get_saleinvoices($filters);

        if(is_array($invoices)) {
            foreach($invoices as $invoice) {
                $customer = $invoice->get_customer()->get_bp_local();
                $prevsales['cid'] = $customer->eid;

                $invoicelines = $invoice->get_invoicelines();
                foreach($invoicelines as $invoiceline) {
                    $product = $invoiceline->get_product_local();
                    $prevsales['pid'] = $product->pid;
                    if(empty($prevsales['pid'])) {
                        $prevsales['pid'] = $invoiceline->m_product_id;
                    }
                    $prevsales['saleType'] = $invoiceline->c_doctype_id;
                    if($prevsales['cid'] == 0) {
                        $prevsales['cid'] = md5($customer->companyName.$invoiceline->c_doctype_id.$prevsales['pid']);
                    }
                }
            }

            $budgetline_details[$prevsales['cid']][$prevsales['pid']][$prevsales['saleType']][0] = $prevsales;
        }
    }

    public function get_budgetLines($bid = '', $options = array()) {
        global $db;
        if(empty($bid)) {
            $bid = $this->data['bid'];
        }

        $options['order_by'] = ' ORDER BY pid ASC';

        if(isset($options['filters']['businessMgr']) && is_array($options['filters']['businessMgr'])) {
            $budgetline_query_where = ' AND businessMgr IN ('.$db->escape_string(implode(',', $options['filters']['businessMgr'])).')';
        }

        if(isset($bid) && !empty($bid)) {
//$prevbudgetline_details = $this->read_prev_budgetbydata();
            $budgetline_queryid = $db->query("SELECT * FROM ".Tprefix."budgeting_budgets_lines
                                                WHERE bid IN (".intval($bid).")".$budgetline_query_where.$options['order_by']);

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

    public function get_budgetlines_objs($filters = '', $configs = array()) {
        $filters['bid'] = $this->data['bid'];
        $configs['returnarray'] = true;
        return BudgetLines::get_data($filters, $configs);
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

    public function generate_budgetline_filters() {
        global $core;

        if($core->usergroup['canViewAllSupp'] == 0 && $core->usergroup['canViewAllAff'] == 0) {
            $filter['filters']['suppliers'] = $core->user['suppliers']['eid'];
            if(is_array($core->user['auditfor'])) {
                $filter['filters']['suppliers'] = $core->user['suppliers']['eid'] + $core->user['auditfor'];
                if(!in_array($this->data['spid'], $core->user['auditfor'])) {
                    if(is_array($core->user['auditedaffids'])) {
                        if(!in_array($this->data['affid'], $core->user['auditedaffids'])) {
                            $filter['filters']['affiliates'] = $core->user['affiliates'];
                            if(is_array($core->user['suppliers']['affid'][$this->data['spid']])) {
                                if(in_array($this->data['affid'], $core->user['suppliers']['affid'][$this->data['spid']])) {
                                    $filter['filters']['businessMgr'] = array($core->user['uid']);
                                }
                                else {
                                    return false;
                                }
                            }
                            else {
                                $filter['filters']['businessMgr'] = array($core->user['uid']);
                            }
                        }
                    }
                    else {
                        $filter['filters']['businessMgr'] = array($core->user['uid']);
                    }
                }
            }
            else {
                $filter['filters']['businessMgr'] = array($core->user['uid']);
            }
        }
        return $filter;
    }

    /* function return object Type --START */
    public function get_supplier() {
        return new Entities($this->data['spid']);
    }

    public function get_affiliate() {
        return new Affiliates($this->data['affid']);
    }

    public function get_currency() {
        return new Currencies($this->data['originalCurrency']);
    }

    public function get_CreateUser() {
        return new Users($this->data['createdBy']);
    }

    public function get_ModifyUser() {
        return new Users($this->data['modifiedBy']);
    }

    public function get_FinalizeUser() {
        return new Users($this->data['finalizedBy']);
    }

    public function get_LockUser() {
        return new Users($this->data['lockedBy']);
    }

    /* function return object Type --END */
    public function get() {
        return $this->data;
    }

    public function get_errorcode() {
        return $this->errorcode;
    }

}
/* Budgeting Line Class --START */

class BudgetLines {
    private $budgetline = array();

    const PRIMARY_KEY = 'blid';
    const TABLE_NAME = 'budgeting_budgets_lines';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

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
                                                WHERE bdl.blid='".intval($budgetlineid)."'"));
        }
    }

    public function create($budgetline_data = array()) {
        global $db, $core;

        if(is_array($budgetline_data)) {
            if(empty($budgetline_data['createdBy'])) {
                $budgetline_data['createdBy'] = $core->user['uid'];
            }
            if(empty($budgetline_data['businessMgr'])) {
                $budgetline_data['businessMgr'] = $core->user['uid'];
            }
            unset($budgetline_data['customerName'], $budgetline_data['blid']);

            $this->split_income($budgetline_data);
            $insertquery = $db->insert_query('budgeting_budgets_lines', $budgetline_data);
            if($insertquery) {
                $this->budgetline = $budgetline_data;
                $this->budgetline['blid'] = $db->last_id();
                $this->errorcode = 0;
            }
        }
    }

    public function update($budgetline_data = array()) {
        global $db, $core;
        unset($budgetline_data['customerName']);
        $budgetline_data['modifiedBy'] = $core->user['uid'];

        $this->split_income($budgetline_data);

        if(!isset($budgetline_data['blid'])) {
            $budgetline_data['blid'] = $this->budgetline['blid'];
        }
        $db->update_query('budgeting_budgets_lines', $budgetline_data, 'blid='.$budgetline_data['blid']);
    }

    public function save_interco_line($data) {
        global $core;

        if(empty($data['interCompanyPurchase'])) {
            return;
        }
        $data_toremove = array('bid', 'blid', 'cid', 'customerCountry', 'interCompanyPurchase');
        $data_zerofill = array('localIncomePercentage', 'localIncomeAmount', 'invoicingEntityIncome');
        $budget = $this->get_budget();

        $data['linkedBudgetLine'] = $this->budgetline['blid'];
        $data['altCid'] = $budget->get_affiliate()->name;
        $data['saleType'] = 6; //Need to be acquire through DAL where isInterCoSale

        if(!empty($this->budgetline['linkedBudgetLine'])) {
            $ic_budgetline = new BudgetLines($this->budgetline['linkedBudgetLine']);

            if(is_object($ic_budgetline)) {
                foreach($data_toremove as $attr) {
                    unset($data[$attr]);
                }
                foreach($data_zerofill as $attr) {
                    $data[$attr] = 0;
                }
                $ic_budgetline->update($data);
                return;
            }
        }

        $ic_budget = Budgets::get_data(array('affid' => $data['interCompanyPurchase'], 'spid' => $budget->spid, 'year' => $budget->year), array('simple' => false));
        if(!is_object($ic_budget)) {
            $ic_budget = new Budgets();
            $budgetdata_intercompany = array(
                    'identifier' => substr(uniqid(time()), 0, 10),
                    'year' => $budget->year,
                    'affid' => $data['interCompanyPurchase'],
                    'spid' => $budget->spid,
                    'createdBy' => $core->user['uid'],
                    'createdOn' => TIME_NOW
            );

            $ic_budget->save_budget($budgetdata_intercompany, null);
        }

        foreach($data_toremove as $attr) {
            unset($data[$attr]);
        }
        foreach($data_zerofill as $attr) {
            $data[$attr] = 0;
        }

        $data['bid'] = $ic_budget->bid;
        if(empty($data['bid'])) {
            $ic_budget = Budgets::get_data(array('affid' => $budgetdata_intercompany['affid'], 'spid' => $budget->spid, 'year' => $budget->year), array('simple' => false));
            $data['bid'] = $ic_budget->bid;
        }
        $ic_budgetline = new BudgetLines();
        $ic_budgetline->create($data);

        $this->update(array('linkedBudgetLine' => $ic_budgetline->blid));
    }

    private function split_income(&$budgetline_data) {
        global $core;

        if($core->usergroup['budgeting_canFillLocalIncome'] == 1) {
            if(empty($budgetline_data['localIncomeAmount']) && $budgetline_data['localIncomeAmount'] != '0') {
                if(!isset($budgetline_data['saleType'])) {
                    return;
                }
                $saletype = new SaleTypes($budgetline_data['saleType']);
                $budgetline_data['localIncomeAmount'] = $budgetline_data['income'];
                $budgetline_data['localIncomePercentage'] = 100;
                $budgetline_data['invoicingEntityIncome'] = 0;
                if($saletype->localIncomeByDefault == 0) {
                    $budgetline_data['localIncomeAmount'] = 0;
                    $budgetline_data['localIncomePercentage'] = 0;
                    $budgetline_data['invoicingEntityIncome'] = $budgetline_data['income'];
                }
            }
            else {
                $budgetline_data['invoicingEntityIncome'] = $budgetline_data['income'] - $budgetline_data['localIncomeAmount'];
            }
        }
    }

    public function delete_interco_line() {
        global $db;
        $db->delete_query('budgeting_budgets_lines', 'blid='.$this->budgetline['linkedBudgetLine']);
    }

    public function delete() {
        global $db;
        $db->delete_query('budgeting_budgets_lines', 'blid='.$this->budgetline['blid']);
    }

    public function get_budget() {
        return new Budgets($this->budgetline['bid']);
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

    public static function get_data($filters = '', $configs = array()) {
        $data = new DataAccessLayer(self::CLASSNAME, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public static function get_aggregate_bycountry(Countries $country, $by, $filters = array(), $configs = array()) {
        global $db;

        $dal = new DataAccessLayer(self::CLASSNAME, self::TABLE_NAME, self::PRIMARY_KEY);
        if($configs['toCurrency']) {
            $fxrate_query = "*(CASE WHEN budgeting_budgets_lines.originalCurrency=".intval($configs['toCurrency'])." THEN 1 ELSE (SELECT rate FROM budgeting_fxrates WHERE affid=(SELECT affid FROM budgeting_budgets WHERE bid=budgeting_budgets_lines.bid) AND year=(SELECT year FROM budgeting_budgets WHERE bid=budgeting_budgets_lines.bid) AND fromCurrency=budgeting_budgets_lines.originalCurrency AND toCurrency=".intval($configs['toCurrency']).") END)";
        }

        if(isset($configs['vsAffid']) && !empty($configs['vsAffid'])) {
            $by = '(CASE '.$configs['vsAffid'].'=(SELECT affid FROM budgeting_budgets WHERE budgeting_budgets.bid='.self::TABLE_NAME.'.bid) THEN localIncome ELSE (income-LocalIncome) END)';
        }

        $total = $db->fetch_assoc($db->query('SELECT SUM('.$by.$fxrate_query.') AS total, (CASE WHEN customerCountry=0 THEN (SELECT country FROM entities WHERE entities.eid='.self::TABLE_NAME.'.cid) ELSE customerCountry END) AS coid FROM '.self::TABLE_NAME.$dal->construct_whereclause_public($filters, $configs['operators']).' GROUP BY coid HAVING coid='.$country->coid));
        return $total['total'];
    }

    public static function get_aggregate_byaffiliate(Affiliates $affiliate, $by, $filters = array(), $configs = array()) {
        global $db;

        $dal = new DataAccessLayer(self::CLASSNAME, self::TABLE_NAME, self::PRIMARY_KEY);

        if($configs['toCurrency']) {
            $fxrate_query = "*(CASE WHEN budgeting_budgets_lines.originalCurrency=".intval($configs['toCurrency'])." THEN 1 ELSE (SELECT rate FROM budgeting_fxrates WHERE affid=(SELECT affid FROM budgeting_budgets WHERE bid=budgeting_budgets_lines.bid) AND year=(SELECT year FROM budgeting_budgets WHERE bid=budgeting_budgets_lines.bid) AND fromCurrency=budgeting_budgets_lines.originalCurrency AND toCurrency=".intval($configs['toCurrency']).") END)";
        }
        $total = $db->fetch_assoc($db->query('SELECT SUM('.$by.$fxrate_query.') AS total, (SELECT affid FROM budgeting_budgets WHERE budgeting_budgets.bid='.self::TABLE_NAME.'.bid) AS affid FROM '.self::TABLE_NAME.$dal->construct_whereclause_public($filters, $configs['operators']).' GROUP BY affid HAVING affid='.$affiliate->affid));
        return $total['total'];
    }

    public static function get_aggregate_bysupplier(Entities $supplier, $by, $filters = array(), $configs = array()) {
        global $db;

        $dal = new DataAccessLayer(self::CLASSNAME, self::TABLE_NAME, self::PRIMARY_KEY);
        if($configs['toCurrency']) {
            $fxrate_query = "*(CASE WHEN budgeting_budgets_lines.originalCurrency=".intval($configs['toCurrency'])." THEN 1 ELSE (SELECT rate FROM budgeting_fxrates WHERE affid=(SELECT affid FROM budgeting_budgets WHERE bid=budgeting_budgets_lines.bid) AND year=(SELECT year FROM budgeting_budgets WHERE bid=budgeting_budgets_lines.bid) AND fromCurrency=budgeting_budgets_lines.originalCurrency AND toCurrency=".intval($configs['toCurrency']).") END)";
        }
        $total = $db->fetch_assoc($db->query('SELECT SUM('.$by.$fxrate_query.') AS total, (SELECT spid FROM budgeting_budgets WHERE budgeting_budgets.bid='.self::TABLE_NAME.'.bid) AS spid FROM '.self::TABLE_NAME.$dal->construct_whereclause_public($filters, $configs['operators']).' GROUP BY spid HAVING spid='.$supplier->eid));
        return $total['total'];
    }

    public static function get_top($percent, $attr, $filters = '', $configs = array()) {
        global $db;

        $dal = new DataAccessLayer(self::CLASSNAME, self::TABLE_NAME, self::PRIMARY_KEY);

        if(empty($configs['group'])) {
            $configs['group'] = 'cid, altCid';
        }

        $fx_query = '*(CASE WHEN bbl.originalCurrency = 840 THEN 1
                          ELSE (SELECT bfr.rate from budgeting_fxrates bfr WHERE bfr.affid = bb.affid AND bfr.year = bb.year AND bfr.fromCurrency = bbl.originalCurrency AND bfr.toCurrency = 840) END)';
        $sql = 'SELECT SUM('.$attr.$fx_query.') AS '.$attr.' FROM '.self::TABLE_NAME.' bbl JOIN budgeting_budgets bb ON (bb.bid=bbl.bid)'.$dal->construct_whereclause_public($filters, $configs['operators']).' GROUP BY '.$configs['group'].' ORDER BY '.$attr.' DESC';
        $data = $db->query($sql);
        $total = $db->fetch_field($db->query('SELECT SUM('.$attr.$fx_query.') AS total FROM '.self::TABLE_NAME.' bbl JOIN budgeting_budgets bb ON (bb.bid=bbl.bid)'.$dal->construct_whereclause_public($filters, $configs['operators'])), 'total');
        while($values = $db->fetch_assoc($data)) {
            $info['count'] += 1;
            $info['contribution'] += $values[$attr];

            if((($info['contribution'] * 100) / $total) >= $percent) {
                break;
            }
        }
        return $info;
    }

    public function __get($name) {
        if(isset($this->budgetline[$name])) {
            return $this->budgetline[$name];
        }
        return false;
    }

    public function get() {
        return $this->budgetline;
    }

    public function get_invoicingentity_income($tocurrency, $year, $affid) {
        global $db;
        $fxrate_query = "(CASE WHEN budgeting_budgets_lines.originalCurrency=".intval($tocurrency)." THEN 1 ELSE (SELECT rate FROM budgeting_fxrates WHERE affid=budgeting_budgets_lines.invoiceAffid AND year=".intval($year)." AND fromCurrency=budgeting_budgets_lines.originalCurrency AND toCurrency=".intval($tocurrency).") END)";
        $sql = "SELECT saleType, invoice, SUM(amount*{$fxrate_query}) AS amount, SUM(invoicingEntityIncome*{$fxrate_query}) AS invoicingEntityIncome FROM ".Tprefix."budgeting_budgets_lines Where invoiceAffid= ".intval($affid)." GROUP BY saleType";
        $query = $db->query($sql);
        if($db->num_rows($query) > 0) {
            while($budget = $db->fetch_assoc($query)) {
                if($budget['invoice'] == 'supplier' || $budget['invoice'] == 'direct') {
                    return;
                }

                $saletype = new SaleTypes($budget['saleType']);
                if(!empty($saletype->invoiceAffStid)) {
                    $budget['saleType'] = $saletype->invoiceAffStid;
                }

                $data['current'][$budget['saleType']]['amount'] = $budget['amount'];
                $data['current'][$budget['saleType']]['invoicingentityincome'] = $budget['invoicingEntityIncome'];
            }
        }
        return $data;
    }

}
/* Budgeting Line Class --END */
?>
