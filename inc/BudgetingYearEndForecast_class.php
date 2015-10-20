<?php
/* -------Definiton-START-------- */

class BudgetingYearEndForecast extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'yefid';
    const TABLE_NAME = 'budgeting_yearendforecast';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = '';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = '';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $table_array = array(
                'identifier' => $data['identifier'],
                'year' => $data['year'],
                'affid' => $data['affid'],
                'spid' => $data['spid'],
                'isLocked' => $data['isLocked'],
                'isFinalized' => $data['isFinalized'],
                'finalizedBy' => $data['finalizedBy'],
                'lockedBy' => $data['lockedBy'],
                'status' => $data['status'],
                'createdOn' => TIME_NOW,
                'createdBy' => $core->user['uid'],
        );
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $update_array['identifier'] = $data['identifier'];
            $update_array['year'] = $data['year'];
            $update_array['affid'] = $data['affid'];
            $update_array['spid'] = $data['spid'];
            $update_array['isLocked'] = $data['isLocked'];
            $update_array['isFinalized'] = $data['isFinalized'];
            $update_array['finalizedBy'] = $data['finalizedBy'];
            $update_array['lockedBy'] = $data['lockedBy'];
            $update_array['status'] = $data['status'];
            $update_array['modifiedOn'] = TIME_NOW;
            $update_array['modifiedBy'] = $core->user['uid'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    public static function get_yef_bydata($data) {
        global $db;
        if(is_array($data)) {
            $yef = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."budgeting_yearendforecast WHERE affid='".$data['affid']."' AND spid='".$data['spid']."' AND year='".$data['year']."'"));
            if(is_array($yef)) {
                return $yef;
            }
            return false;
        }
    }

    public static function get_yefs_bydata($data = array()) {
        global $db;
        if(isset($data['years']) && !empty($data['years'])) {
            if(is_array($data['suppliers'])) {
                array_walk($data['suppliers'], intval);
                $yef_reportquery = " AND spid IN (".implode(',', $data['suppliers']).")";
            }

            if(is_array($data['affiliates'])) {
                array_walk($data['affiliates'], intval);
                $yef_reportquery .= " AND affid IN (".implode(',', $data['affiliates']).")";
            }
            $yef_reportquery = $db->query("SELECT yefid FROM ".Tprefix."budgeting_yearendforecast WHERE year=".intval($data['years']).$yef_reportquery);
        }
        if($yef_reportquery) {
            if($db->num_rows($yef_reportquery) > 0) {
                while($yef_reportids = $db->fetch_assoc($yef_reportquery)) {
                    $yefbudgetreport[$yef_reportids['yefid']] = $yef_reportids['yefid'];
                }
                return $yefbudgetreport;
            }
        }
    }

    public function get_yefLines($yefid = '', $options = array()) {
        global $db;
        if(empty($yefid)) {
            $yefid = $this->data['yefid'];
        }

        $options['order_by'] = ' ORDER BY pid ASC';

        if(isset($options['filters']['businessMgr']) && is_array($options['filters']['businessMgr'])) {
            $yefline_query_where = ' AND businessMgr IN ('.$db->escape_string(implode(',', $options['filters']['businessMgr'])).')';
        }

        if(isset($yefid) && !empty($yefid)) {
//$prevbudgetline_details = $this->read_prev_budgetbydata();
            $yefline_queryid = $db->query("SELECT * FROM ".Tprefix."budgeting_yef_lines
                                                WHERE yefid IN (".intval($yefid).")".$yefline_query_where.$options['order_by']);

            if($db->num_rows($yefline_queryid) > 0) {
                while($yefline_data = $db->fetch_assoc($yefline_queryid)) {
                    if($yefline_data['cid'] == 0) {
                        $yefline_data['cid'] = md5($yefline_data['altCid'].$yefline_data['customerCountry'].$yefline_data['saleType'].$yefline_data['pid'].$yefline_data['blid']);
                    }
                    $yefline = new BudgetingYEFLines($yefline_data['yeflid']);
                    $yefline_details[$yefline_data['cid']][$yefline_data['pid']][$yefline_data['saleType']] = $yefline->get();
                }
                return $yefline_details;
            }
        }
    }

    public function read_prev_yefbydata($data = array(), $options = array()) {
        global $db;
        if(empty($data)) {
            $data['affid'] = $this->data['affid'];
            $data['spid'] = $this->data['spid'];
            $data['year'] = $this->data['year'];
        }

        if(isset($options['filters']['businessMgr']) && is_array($options['filters']['businessMgr'])) {
            $budgetline_query_where = ' AND yefl.businessMgr IN ('.$db->escape_string(implode(',', $options['filters']['businessMgr'])).')';
        }

        for($year = $data['year']; $year >= ($data['year'] - 2); $year--) {
            if($year == $data['year']) {
                continue;
            }

            $prev_yef_bydataquery = $db->query("SELECT *
					FROM ".Tprefix."budgeting_yearendforecast yef
					JOIN ".Tprefix."budgeting_yef_lines yefl ON (bd.bid=bdl.bid)
					WHERE affid='".$data['affid']."' AND spid='".$data['spid']."' AND year='".$year."'".$budgetline_query_where);
            if($db->num_rows($prev_yef_bydataquery) > 0) {
                while($prevyef_bydata = $db->fetch_assoc($prev_yef_bydataquery)) {
                    if($prevyef_bydata['cid'] == 0) {
                        $prevyef_bydata['cid'] = md5($prevyef_bydata['altCid'].$prevyef_bydata['saleType'].$prevyef_bydata['pid']);
                    }
                    $yefline_details[$prevyef_bydata['cid']][$prevyef_bydata['pid']][$prevyef_bydata['saleType']][] = $prevyef_bydata;
                }
            }
        }

        return $yefline_details;
    }

    public static function save_budget($budgetdata = array(), $budgetline_data = array()) {
        global $db, $core, $log;
        if(is_array($budgetdata)) {
            if(is_empty($budgetdata['year'], $budgetdata['affid'], $budgetdata['spid'])) {
                return false;
            }

            /* Check if budget exists, then process accordingly */
            if(!BudgetingYearEndForecast::yef_exists_bydata($budgetdata)) {
                $budget_data = array('identifier' => substr(uniqid(time()), 0, 10),
                        'year' => $budgetdata['year'],
                        'affid' => $budgetdata['affid'],
                        'spid' => $budgetdata['spid'],
                        'createdBy' => $core->user['uid'],
                        'createdOn' => TIME_NOW
                );

                $insertquery = $db->insert_query('budgeting_yearendforecast', $budget_data);
                if($insertquery) {
                    if(is_object($this)) {
                        $this->data['yefid'] = $db->last_id();
                        $log->record('savenewyef', $this->data['yefid']);
                        $this->save_budgetlines($budgetline_data, $this->data['yefid']);
                    }
                    else {
                        $yefid = $db->last_id();
                        $yef = new BudgetingYearEndForecast($yefid);
                        $log->record('savenewyef', $yefid);
                        $yef->save_budgetlines($budgetline_data);
                    }
                }
                else {
                    return false;
                }
            }
            else {
                $existing_budget = BudgetingYearEndForecast::get_yef_bydata($budgetdata);
                if(isset($this)) {
                    $this->data['yefid'] = $existing_budget['yefid'];
                    $this->update($this->data);
                    $this->save_budgetlines($budgetline_data, $this->data['yefid']);
                }
                else {
                    $budget = new BudgetingYearEndForecast($existing_budget['yefid']);
                    $budget->update($budget->get());
                    $budget->save_budgetlines($budgetline_data);
                }
                $log->record('updatedyef', $existing_budget['yefid']);
            }
        }
        return true;
    }

    private function save_budgetlines($budgetline_data = array(), $yef = '', $options = array()) {
        global $db;
        if(isset($budgetline_data['customerName'])) {
            unset($budgetline_data['customerName']);
        }
        if(empty($yef)) {
            $yef = $this->data['yefid'];
        }
        // if the 2 budgetline are linked together
        if(is_array($budgetline_data)) {
            $required_fields = array('october', 'november', 'december');
            foreach($budgetline_data as $inputCheckSum => $data) {
                if(!isset($data['yefid']) && empty($data['yefid'])) {
                    $data['yefid'] = $yef;
                }
                if(isset($data['blid']) && !empty($data['blid'])) {
                    $data['fromBudget'] = '1';
                }
                if($data['unspecifiedCustomer'] == 1 && empty($data['cid'])) {
                    $data['altCid'] = 'Unspecified Customer';
                }

                if(!empty($data['cid'])) {
                    $data['altCid'] = NULL;
                    $data['customerCountry'] = 0;
                }
                if(isset($data['yeflid']) && !empty($data['yeflid'])) {
                    $yeflineobj = new BudgetingYEFLines($data['yeflid']);
                }
                else if(isset($data['inputCheckSum']) && !empty($data['inputCheckSum'])) {
                    $yeflineobj = BudgetingYEFLines::get_data(array('inputCheckSum' => $data['inputCheckSum']));
                    $data['yeflid'] = $yeflineobj->yeflid;
                }
                if(!is_object($yeflineobj)) {
                    $yefline = BudgetingYEFLines::get_yefline_bydata($data);
                    if($yefline != false) {
                        $yeflineobj = new BudgetingYEFLines($yefline['yeflid']);
                        $data['yeflid'] = $yeflineobj['yeflid'];
                    }
                    else {
                        $yeflineobj = new BudgetingYEFLines();
                    }
                }
                if(is_empty($data['localIncomeAmount'], $data['localIncomePercentage'])) {
                    $data['localIncomeAmount'] = $data['income'];
                    $data['invoicingEntityIncome'] = 0;
                    $data['localIncomePercentage'] = 100;

                    if(!isset($data['saleType'])) {
                        return;
                    }

                    $saletype = new SaleTypes($data['saleType']);
                    if($saletype->localIncomeByDefault == 0) {
                        $data['localIncomeAmount'] = 0;
                        $data['localIncomePercentage'] = 0;
                        $data['invoicingEntityIncome'] = $data['income'];
                    }
                }
                else {
                    $data['invoicingEntityIncome'] = $data['income'] - $data['localIncomeAmount'];
                }
                if((empty($data['pid']) && empty($data['altPid'])) || (empty($data['cid']) && (empty($data['altCid']) || empty($data['customerCountry'])))) {
                    if(!empty($data['yeflid'])) {
                        $removed_lines[] = $data['yeflid'];
                    }
                    continue;
                }
                $reqfieldempty = '';
                foreach($required_fields as $field) {
                    if(empty($data[$field]) && $data[$field] != 0) {
                        $reqfieldempty = true;
                    }
                }
                if($reqfieldempty == true) {
                    continue;
                }
                if(isset($data['invoice'])) {
                    if(empty($this->data['affid'])) {
                        $yef_obj = new BudgetingYearEndForecast($data['yefid']);
                        $this->data['affid'] = $yef_obj->affid;
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
                $yeflineobj = $yeflineobj->save($data);

                if(is_object($yeflineobj)) {
                    $yeflineobj->save_interco_line($data);
                }
                unset($yeflineobj);
            }

            if(is_array($removed_lines)) {
                foreach($removed_lines as $removedblid) {
                    if(!empty($removedblid)) {
                        $yeflineobj = new BudgetingYEFLines($removedblid);
                        if(!empty($yeflineobj->blid)) {
                            $yeflineobj->delete();
                            $yeflineobj->delete_interco_line();
                        }
                    }
                }
            }
        }
    }

    private static function yef_exists_bydata($data) {
        global $db;
        if(isset($data['affid'], $data['spid'], $data['year']) && !is_empty($data['affid'], $data['spid'], $data['year'])) {
            $budget_existquery = $db->query('SELECT yefid FROM '.Tprefix.'budgeting_yearendforecast WHERE affid='.intval($data['affid']).' AND spid='.intval($data['spid']).' AND year='.intval($data['year']));
            if($db->num_rows($budget_existquery) > 0) {
                return true;
            }
            return false;
        }
        return false;
    }

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

    public function generate_yefline_filters() {
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
                                                            $core->user['suppliers']['affid'][$entity->eid] = array_unique(array_merge($core->user['suppliers']['affid'][$entity->eid], $affids));
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

    public function get_yeflines_objs($filters = '', $configs = array()) {
        return $this->get_lines($filters, $configs);
    }

    public function get_lines($filters, $configs = null) {
        if(isset($this->data['yefid']) && !empty($this->data['yefid'])) {
            $filters['yefid'] = $this->data['yefid'];

            $configs['returnarray'] = true;

            if(!isset($configs['order'])) {
                $configs['order'] = array('by' => 'pid', 'sort' => 'ASC');
            }
            $configs['operators']['businessMgr'] = 'IN';
            return BudgetingYEFLines::get_data($filters, $configs);
        }
    }

    public function get_helptouritems() {
        global $lang;
        $touritems = array(
                'page_title' => array('ignoreid' => true, 'options' => 'tipLocation:top;', 'text' => $lang->helptour_welcomeyeffill),
                'quantity_tour' => array('options' => 'tipLocation:left;', 'text' => $lang->helptour_quantity),
                'amount_tour' => array('options' => 'tipLocation:left;', 'text' => $lang->helptour_amount),
                'localincome_tour' => array('options' => 'tipLocation:left;', 'text' => $lang->helptour_localincome),
                'commissionaff_tour' => array('options' => 'tipLocation:left;', 'text' => $lang->helptour_commissionaff),
                'month_tour' => array('options' => 'tipLocation:left;', 'text' => $lang->helptour_month),
        );

        return $touritems;
    }

    public static function get_saletype_byid($sitd) {
        global $db;
        if(!empty($sitd)) {
            return $db->fetch_field($db->query("SELECT title FROM ".Tprefix."saletypes WHERE stid='".$db->escape_string($sitd)."'"), 'title');
        }
    }

    public static function get_availableyears() {
        global $db;
        $query = $db->query('SELECT DISTINCT(year) FROM '.Tprefix.self::TABLE_NAME.' ORDER BY year DESC');
        if($db->num_rows($query) > 0) {
            while($year = $db->fetch_assoc($query)) {
                $years[$year['year']] = $year['year'];
            }
            return $years;
        }
        return false;
    }

}