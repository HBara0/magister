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
    const SIMPLEQ_ATTRS = 'bid, year, affid, spid, isLocked';
    const attachments_path = './uploads/budget';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = '';

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
        $budget_yearquery = $db->query('SELECT bid, year FROM '.Tprefix.'budgeting_budgets WHERE spid='.$data['spid'].' AND affid='.$data['affid'].' AND isLocked=0 ORDER BY year DESC');
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
                else {
                    $this->errorcode = 3;
                    return false;
                }
            }
            else {
                $existing_budget = Budgets::get_budget_bydata($budgetdata);
                if(isset($this)) {
                    $this->data['bid'] = $existing_budget['bid'];
                    $this->save_budgetlines($budgetline_data, $this->data['bid']);
                }
                else {
                    $budget = new Budgets($existing_budget['bid']);
                    $budget->save_budgetlines($budgetline_data);
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
                }

                if(!empty($data['cid'])) {
                    $data['altCid'] = NULL;
                    $data['customerCountry'] = 0;
                }
                if(isset($data['blid']) && !empty($data['blid'])) {
                    $budgetlineobj = new BudgetLines($data['blid']);
                }
                else if(isset($data['inputChecksum']) && !empty($data['inputChecksum'])) {
                    $budgetlineobj = BudgetLines::get_data(array('inputChecksum' => $data['inputChecksum']));
                    $data['blid'] = $budgetlineobj->blid;
                }
                if(!is_object($budgetlineobj)) {
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

                if(empty($data['psid'])) {
                    $product = Products::get_data(array('pid' => $data['pid']), array('simple' => false));
                    if(is_object($product)) {
                        $data['psid'] = $product->get_segment()['psid'];
                    }
                    unset($product);
                }
                if(isset($data['invoice'])) {
                    if(empty($this->data['affid'])) {
                        $budget_obj = new Budgets($data['bid']);
                        $this->data['affid'] = $budget_obj->affid;
                    }
                    $invoiceentity = SaleTypesInvoicing::get_data(array('affid' => $this->data['affid'], 'invoicingEntity' => $data['invoice'], 'stid' => $data['saleType']));
                    if(is_object($invoiceentity)) {
                        if($invoiceentity->isAffiliate == 1) {
                            $data['invoiceAffid'] = $invoiceentity->invoiceAffid;
                        }
                    }
                }

                if(isset($data['purchasingEntity'])) {
                    $data['purchasingEntityId'] = 0;
                    switch($data['purchasingEntity']) {
                        case'customer':
                            if(!empty($data['cid'])) {
                                $data['purchasingEntityId'] = $data['cid'];
                            }
                            else if(empty($data['cid']) && !empty($data['altCid'])) {
                                $data['purchasingEntityId'] = $data['altCid'];
                            }
                            break;

//                    default:
//                      if(empty($this->data['affid'])) {
//                        $budget_obj = new Budgets($data['bid']);
//                        $this->data['affid'] = $budget_obj->affid;
//                    }
//                    $purchasingentity = SaleTypesInvoicing::get_data(array('affid' => $this->data['affid'], 'invoicingEntity' => $data['purchasingEntity'], 'stid' => $data['saleType']));
//                    if(is_object($purchasingentity)) {
//                        if($purchasingentity->isAffiliate == 1) {
//                            $data['purchasingEntityId'] = $purchasingentity->invoiceAffid;
//                            if($data['purchasingEntity'] !='direct'){
//                            $data['commissionSplitAffid'] = $data['commissionSplitAffid'];}
//                        }
//                    }
                        case 'direct':
                            $data['purchasingEntityId'] = $this->data['affid'];
                            break;
                        case 'fze':
                            $data['purchasingEntityId'] = 14;
                            $data['commissionSplitAffid'] = $data['purchasingEntityId'];
                            break;
                        case 'int':
                            $data['purchasingEntityId'] = 27;
                            $data['commissionSplitAffid'] = $data['commissionSplitAffid'];
                            break;
                        case 'alex':
                            $data['purchasingEntityId'] = 28;
                            $data['commissionSplitAffid'] = $data['commissionSplitAffid'];
                            break;
                    }
                }
                unset($data['unspecifiedCustomer']);
                if(isset($data['blid']) && !empty($data['blid'])) {
                    $errorcodebl = $budgetlineobj->update($data);
                    $errorcodeint = $budgetlineobj->save_interco_line($data);
                    if(!is_empty($errorcodeint, $errorcodebl)) {
                        $this->errorcode = 3;
                    }
                    $this->errorcode = 0;
                }
                else {
                    $errorcodebl = $budgetlineobj->create($data);
                    $errorcodeint = $budgetlineobj->save_interco_line($data);
                    if(!is_empty($errorcodeint, $errorcodebl)) {
                        $this->errorcode = 3;
                    }
                    $this->errorcode = 0;
                }
            }

            if(is_array($removed_lines)) {
                foreach($removed_lines as $removedblid) {
                    if(!empty($removedblid)) {
                        $budgetlineobj = new BudgetLines($removedblid);
                        if(!empty($budgetlineobj->blid)) {
                            $budgetlineobj->delete();
                            $budgetlineobj->delete_interco_line();
                        }
                    }
                }
            }
        }
        else {
            $this->errocode = 2;
            return;
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
                $budget_reportquery .= " AND affid IN (".implode(',', $data['affiliates']).")";
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

    public function read_prev_budgetbydata($data = array(), $options = array(), $source = null) {
        global $db;
        if(empty($data)) {
            $data['affid'] = $this->data['affid'];
            $data['spid'] = $this->data['spid'];
            $data['year'] = $this->data['year'];
        }

        if(isset($options['filters']['businessMgr']) && is_array($options['filters']['businessMgr'])) {
            $budgetline_query_where = ' AND bdl.businessMgr IN ('.$db->escape_string(implode(',', $options['filters']['businessMgr'])).')';
        }

        if(isset($options['filters']['blid']) && is_array($options['filters']['blid'])) {
            if(empty($options['operators']['blid'])) {
                $options['operators']['blid'] = 'IN';
            }
            $budgetline_query_where = ' AND bdl.blid '.$options['operators']['blid'].' ('.$db->escape_string(implode(',', $options['filters']['blid'])).')';
        }

        for($year = $data['year']; $year >= ($data['year'] - 1); $year--) {
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
                        $prevbudget_bydata['cid'] = md5($prevbudget_bydata['altCid'].$prevbudget_bydata['saleType'].$prevbudget_bydata['pid'].$prevbudget_bydata['prevblid'].$prevbudget_bydata['linkedBudgetLine']);
                    }
                    if($source == 'userprevlines') {
                        $prevbudget_bydata['source'] = 'userprevlines';
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

    public function get_budgetlines_objs($filters = '', $configs = array()) {
        return $this->get_lines($filters, $configs);
    }

    public function get_lines($filters, $configs = null) {
        if(isset($this->data['bid']) && !empty($this->data['bid'])) {
            $filters['bid'] = $this->data['bid'];

            $configs['returnarray'] = true;

            if(!isset($configs['order'])) {
                $configs['order'] = array('by' => 'pid', 'sort' => 'ASC');
            }
            $configs['operators']['businessMgr'] = 'IN';
            return BudgetLines::get_data($filters, $configs);
        }
    }

    public function get_budgetLines($bid = '', $options = array(), $source = NULL) {
        global $db;
        if(empty($bid)) {
            $bid = $this->data['bid'];
        }

        $options['order_by'] = ' ORDER BY pid ASC';

        if(isset($options['filters']['businessMgr']) && is_array($options['filters']['businessMgr'])) {
            $budgetline_query_where = ' AND businessMgr IN ('.$db->escape_string(implode(',', $options['filters']['businessMgr'])).')';
        }
        if(isset($options['filters']['blid']) && is_array($options['filters']['blid']) && !empty($options['filters']['blid'])) {
            if(empty($options['operators']['blid'])) {
                $options['operators']['blid'] = 'IN';
            }
            $budgetline_query_where = ' AND blid '.$options['operators']['blid'].' ('.$db->escape_string(implode(',', $options['filters']['blid'])).')';
        }
        if(isset($bid) && !empty($bid)) {
//$prevbudgetline_details = $this->read_prev_budgetbydata();
            $budgetline_queryid = $db->query("SELECT * FROM ".Tprefix."budgeting_budgets_lines
                                                WHERE bid IN (".intval($bid).")".$budgetline_query_where.$options['order_by']);

            if($db->num_rows($budgetline_queryid) > 0) {
                while($budgetline_data = $db->fetch_assoc($budgetline_queryid)) {
                    if($budgetline_data['cid'] == 0) {
                        $budgetline_data['cid'] = md5($budgetline_data['altCid'].$budgetline_data['customerCountry'].$budgetline_data['saleType'].$budgetline_data['pid'].$budgetline_data['prevblid'].$budgetline_data['linkedBudgetLine']);
                    }
                    $budgetline = new BudgetLines($budgetline_data['blid']);
                    $prevbudgetline = new BudgetLines($budgetline_data['prevblid']);
                    $budgetline_arr = $budgetline->get();
                    if($source == 'userprevlines') {
                        $budgetline_arr['source'] = $source;
                    }
                    $budgetline_details[$budgetline_data['cid']][$budgetline_data['pid']][$budgetline_data['saleType']] = $budgetline_arr;
                    $budgetline_details[$budgetline_data['cid']][$budgetline_data['pid']][$budgetline_data['saleType']]['prevbudget'][] = $prevbudgetline->get();
                    unset($budgetline_arr);
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

    public function generate_budgetline_filters() {
        global $core;
        if($core->usergroup['canViewAllSupp'] == 0 && $core->usergroup['canViewAllAff'] == 0) {
            $filter['filters']['suppliers'] = $core->user['suppliers']['eid'];
            if(is_array($core->user['auditfor'])) {
                $filter['filters']['suppliers'] = $core->user['suppliers']['eid'] + $core->user['auditfor'];
                if(!in_array($this->data['spid'], $core->user['auditfor'])) {
                    if(is_array($core->user['auditedaffids'])) {
                        if(!in_array($this->data['affid'], $core->user['auditedaffids'])) {
//if user is coordinator append more options
                            $segmentscoords = ProdSegCoordinators::get_data(array('uid' => $core->user['uid']), array('returnarray' => true));
                            if(is_array($segmentscoords)) {
                                $psids = array();
                                $affids = array();
                                $spids = array();
                                foreach($segmentscoords as $segmentscoord) {
                                    if(in_array($segmentscoord->psid, $psids)) {
                                        continue;
                                    }
                                    $psids[] = $segmentscoord->psid;
                                    $entitysegments = EntitiesSegments::get_data(array('psid' => $segmentscoord->psid), array('returnarray' => true));
                                    if(is_array($entitysegments)) {
                                        foreach($entitysegments as $entitysegment) {
                                            if($entitysegment->eid == $this->data['spid']) {
                                                $entity = new Entities($entitysegment->eid);
                                                if($entity->type == 's') {
                                                    $affiliatedsegs = AffiliatedEntities::get_column('affid', array('eid' => $entitysegment->eid), array('returnarray' => true));
                                                    if(is_array($affiliatedsegs)) {
                                                        foreach($affiliatedsegs as $affiliatedseg) {
                                                            if(!in_array($affiliatedseg, $affids)) {
                                                                $affids[] = $affiliatedseg;
                                                            }
                                                        }
                                                        if(is_array($affids)) {
                                                            if(is_array($core->user['suppliers']['affid'][$entity->eid])) {
                                                                $core->user['suppliers']['affid'][$entity->eid] = array_unique(array_merge($core->user['suppliers']['affid'][$entity->eid], $affids));
                                                            }
                                                            else {
                                                                $core->user['suppliers']['affid'][$entity->eid] = array_unique($affids);
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    if(is_array($affids)) {
                                        $core->user['affiliates'] = array_unique(array_merge($core->user['affiliates'], $affids));
                                    }
                                }
                            }
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

    public function lockbudget($operation) {
        global $db, $core;
        $fields = array('isFinalized', 'isLocked', 'finalizedBy', 'lockedBy');
        foreach($fields as $field) {
            $update_budget[$field] = 0;
            if(isset($operation) && $operation == 'lock') {
                switch($field) {
                    case 'finalizedBy':
                    case 'lockedBy':
                        $update_budget[$field] = $core->user['uid'];
                        break;
                    default:
                        $update_budget[$field] = 1;
                        break;
                }
            }
        }
        $query = $db->update_query(self::TABLE_NAME, $update_budget, 'bid='.$this->bid);
        if($query) {
            return $this;
        }
        return false;
    }

}
?>
