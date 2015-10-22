<?php
/* -------Definiton-START-------- */

class BudgetingYEFLines extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'yeflid';
    const TABLE_NAME = 'budgeting_yef_lines';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'yefid,pid,cid,altCid,saleType,linkedBudgetLine,blid';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = '';
    const REQUIRED_ATTRS = 'yefid,saleType,inputCheckSum';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        if(!$this->validate_requiredfields($data)) {
            return false;
        }
        $table_array = array(
                'inputCheckSum' => $data['inputCheckSum'],
                'yefid' => $data['yefid'],
                'pid' => $data['pid'],
                'blid' => $data['blid'],
                'cid' => $data['cid'],
                'altCid' => $data['altCid'],
                'prevyeflid' => $data['prevyeflid'],
                'customerCountry' => $data['customerCountry'],
                'businessMgr' => $data['businessMgr'],
                'actualQty' => $data['actualQty'],
                'actualIncome' => $data['actualIncome'],
                'actualAmount' => $data['actualAmount'],
                'localIncomePercentage' => $data['localIncomePercentage'],
                'localIncomeAmount' => $data['localIncomeAmount'],
                'amount' => $data['amount'],
                'unitPrice' => $data['unitPrice'],
                'income' => $data['income'],
                'incomePerc' => $data['incomePerc'],
                'invoice' => $data['invoice'],
                'invoiceAffid' => $data['invoiceAffid'],
                'invoicingEntityIncome' => $data['invoicingEntityIncome'],
                'interCompanyPurchase' => $data['interCompanyPurchase'],
                'quantity' => $data['quantity'],
                'originalCurrency' => $data['originalCurrency'],
                'saleType' => $data['saleType'],
                'october' => $data['october'],
                'november' => $data['november'],
                'december' => $data['december'],
                'octoberqty' => $data['octoberqty'],
                'novemberqty' => $data['novemberqty'],
                'decemberqty' => $data['decemberqty'],
                'createdBy' => $core->user['uid'],
                'createdOn' => TIME_NOW,
                'commissionSplitAffid' => $data['commissionSplitAffid'],
                'purchasingEntity' => $data['purchasingEntity'],
                'purchasingEntityId' => $data['purchasingEntityId'],
                'linkedBudgetLine' => $data['linkedBudgetLine'],
                'psid' => $data['psid'],
                'fromBudget' => $data['fromBudget'],
        );

        if(empty($table_array['createdBy'])) {
            $table_array['createdBy'] = $core->user['uid'];
        }
        if(empty($table_array['businessMgr'])) {
            $table_array['businessMgr'] = $core->user['uid'];
        }
        if(empty($table_array['psid'])) {
            $product = new Products($table_array['pid']);
            $table_array['psid'] = $product->get_segment()['psid'];
        }

        $this->split_income($data);
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data = $table_array;
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $core;
        if(!$this->validate_requiredfields($data)) {
            return false;
        }
        $this->split_income($data);
        if(is_array($data)) {
            $update_array['inputCheckSum'] = $data['inputCheckSum'];
            $update_array['yefid'] = $data['yefid'];
            $update_array['pid'] = $data['pid'];
            $update_array['blid'] = $data['blid'];
            $update_array['cid'] = $data['cid'];
            $update_array['altCid'] = $data['altCid'];
            $update_array['prevyeflid'] = $data['prevyeflid'];
            $update_array['customerCountry'] = $data['customerCountry'];
            $update_array['actualQty'] = $data['actualQty'];
            $update_array['actualIncome'] = $data['actualIncome'];
            $update_array['actualAmount'] = $data['actualAmount'];
            $update_array['localIncomePercentage'] = $data['localIncomePercentage'];
            $update_array['localIncomeAmount'] = $data['localIncomeAmount'];
            $update_array['amount'] = $data['amount'];
            $update_array['unitPrice'] = $data['unitPrice'];
            $update_array['income'] = $data['income'];
            $update_array['incomePerc'] = $data['incomePerc'];
            $update_array['invoice'] = $data['invoice'];
            $update_array['invoiceAffid'] = $data['invoiceAffid'];
            $update_array['invoicingEntityIncome'] = $data['invoicingEntityIncome'];
            $update_array['interCompanyPurchase'] = $data['interCompanyPurchase'];
            $update_array['quantity'] = $data['quantity'];
            $update_array['originalCurrency'] = $data['originalCurrency'];
            $update_array['saleType'] = $data['saleType'];
            $update_array['october'] = $data['october'];
            $update_array['november'] = $data['november'];
            $update_array['december'] = $data['december'];
            $update_array['octoberqty'] = $data['octoberqty'];
            $update_array['novemberqty'] = $data['novemberqty'];
            $update_array['decemberqty'] = $data['decemberqty'];
            $update_array['modifiedBy'] = $core->user['uid'];
            $update_array['modifiedOn'] = TIME_NOW;
            $update_array['commissionSplitAffid'] = $data['commissionSplitAffid'];
            $update_array['purchasingEntity'] = $data['purchasingEntity'];
            $update_array['purchasingEntityId'] = $data['purchasingEntityId'];
            $update_array['linkedBudgetLine'] = $data['linkedBudgetLine'];
            $update_array['psid'] = $data['psid'];
            $update_array['fromBudget'] = $data['fromBudget'];
        }

        if(empty($update_array['psid']) && !empty($update_array['pid'])) {
            $product = new Products($update_array['pid']);
            $update_array['psid'] = $product->get_segment()['psid'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    /* -------GETTER FUNCTIONS-START-------- */
    public function get_yef() {
        return new BudgetingYearEndForecast($this->data['yefid']);
    }

    /* -------GETTER FUNCTIONS-END-------- */
    public static function get_yefline_bydata($data) {
        global $db;
        if(is_array($data)) {
            if(!isset($data['yef']) || empty($data['yef'])) {
                return false;
            }
            $budgetline_bydataquery = $db->query("SELECT * FROM ".Tprefix."budgeting_yef_lines WHERE pid='".$data['pid']."' AND cid='".$data['cid']."' AND altCid='".$db->escape_string($data['altCid'])."' AND saleType='".$data['saleType']."' AND yefid='".$data['yefid']."' AND customerCountry='".$data['customerCountry']."' AND psid='".$data['psid']."'");
            if($db->num_rows($budgetline_bydataquery) > 0) {
                return $db->fetch_assoc($budgetline_bydataquery);
            }
            return false;
        }
    }

    public function save_interco_line($data) {
        global $core;

        if(empty($data['interCompanyPurchase'])) {
            return;
        }
        $data_toremove = array('yefid', 'yeflid', 'cid', 'interCompanyPurchase', 'blid');
        $data_zerofill = array('invoicingEntityIncome'); //'localIncomePercentage', 'localIncomeAmount',
        $yef = $this->get_yef();
        $data['inputCheckSum'] = generate_checksum();
        $data['linkedBudgetLine'] = $this->data['yeflid'];
        $data['altCid'] = $yef->get_affiliate()->name;
        $data['customerCountry'] = $yef->get_affiliate()->country;
        $data['saleType'] = 6; //Need to be acquire through DAL where isInterCoSale
        $data['amount'] = $data['amount'] - $data['income'];
        /* Apply Default Margin */
        $data['income'] = $data['localIncomeAmount'] = $data['amount'] * 0.03;
        $data['localIncomePercentage'] = 100;
        unset($data['blid']);

        if(!empty($this->data['linkedBudgetLine'])) {
            $ic_budgetline = new BudgetingYearEndForecast($this->data['linkedBudgetLine']);
            if(!empty($ic_budgetline->modifiedOn)) {
                return;
            }
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


        $ic_yef = BudgetingYearEndForecast::get_data(array('affid' => $data['interCompanyPurchase'], 'spid' => $yef->spid, 'year' => $yef->year), array('simple' => false));
        if(!is_object($ic_yef)) {
            $ic_yef = new BudgetingYearEndForecast();
            $budgetdata_intercompany = array(
                    'identifier' => substr(uniqid(time()), 0, 10),
                    'year' => $yef->year,
                    'affid' => $data['interCompanyPurchase'],
                    'spid' => $yef->spid,
                    'createdBy' => $core->user['uid'],
                    'createdOn' => TIME_NOW
            );

            $ic_yef->save_budget($budgetdata_intercompany, null);
        }

        foreach($data_toremove as $attr) {
            unset($data[$attr]);
        }
        foreach($data_zerofill as $attr) {
            $data[$attr] = 0;
        }

        $data['yefid'] = $ic_yef->yefid;
        if(empty($data['yefid'])) {
            $ic_yef = BudgetingYearEndForecast::get_data(array('affid' => $budgetdata_intercompany['affid'], 'spid' => $yef->spid, 'year' => $yef->year), array('simple' => false));
            $data['yefid'] = $ic_yef->yefid;
        }

        $ic_budgetline = new BudgetingYEFLines();
        $ic_budgetline->save($data);

        $this->update(array('linkedBudgetLine' => $ic_budgetline->yeflid));
    }

    public function delete_interco_line() {
        if(empty($this->data['linkedBudgetLine'])) {
            return;
        }

        $linked_bdlineobj = new BudgetingYearEndForecast($this->data['linkedBudgetLine']);
        /* If this is the initiator bugdet line, don't delete it */
        if(!empty($linked_bdlineobj->interCompanyPurchase)) {
            return;
        }
        /* If linked budget line has not been mondified, then delete it */
        if(empty($linked_bdlineobj->modifiedOn)) {
            $linked_bdlineobj->delete();
        }
    }

    public function delete() {
        global $db;
        $db->delete_query('budgeting_yef_lines', 'yeflid='.$this->data['yeflid']);
    }

    public function get_customer() {
        return new Entities($this->data['cid'], '', false);
    }

    public function get_currency() {
        return new Currencies($this->data['originalCurrency']);
    }

    public function get_product() {
        return new Products($this->data['pid']);
    }

    public function get_saletype() {
        return $this->data['saleType'];
    }

    public function get_createuser() {
        return new Users($this->data['createdBy']);
    }

    public function get_businessMgr() {
        return new Users($this->data['businessMgr']);
    }

    public function get_modifyuser() {
        return new Users($this->data['modifiedBy']);
    }

    public static function get_aggregate_bysupplier(Entities $supplier, $by, $filters = array(), $configs = array()) {
        global $db;

        $dal = new DataAccessLayer(self::CLASSNAME, self::TABLE_NAME, self::PRIMARY_KEY);
        if($configs['toCurrency']) {
            $fxrate_query = "*(CASE WHEN budgeting_yef_lines.originalCurrency=".intval($configs['toCurrency'])." THEN 1 ELSE (SELECT rate FROM budgeting_fxrates WHERE affid=(SELECT affid FROM budgeting_yearendforecast WHERE yefid=budgeting_yef_lines.yefid) AND year=(SELECT year FROM budgeting_yearendforecast WHERE yefid=budgeting_yef_lines.yefid) AND fromCurrency=budgeting_yef_lines.originalCurrency AND toCurrency=".intval($configs['toCurrency']).") END)";
        }
        $total = $db->fetch_assoc($db->query('SELECT SUM('.$by.$fxrate_query.') AS total, (SELECT spid FROM budgeting_yearendforecast WHERE budgeting_yearendforecast.yefid = '.self::TABLE_NAME.'.yefid) AS spid FROM '.self::TABLE_NAME.$dal->construct_whereclause_public($filters, $configs['operators']).' GROUP BY spid HAVING spid='.$supplier->eid));
        return $total['total'];
    }

    public static function get_aggregate_bycountry(Countries $country, $by, $filters = array(), $configs = array()) {
        global $db;

        $dal = new DataAccessLayer(self::CLASSNAME, self::TABLE_NAME, self::PRIMARY_KEY);
        if($configs['toCurrency']) {
            $fxrate_query = "*(CASE WHEN budgeting_yef_lines.originalCurrency=".intval($configs['toCurrency'])." THEN 1 ELSE (SELECT rate FROM budgeting_fxrates WHERE affid=(SELECT affid FROM budgeting_yearendforecast WHERE yefid=budgeting_yef_lines.yefid) AND year=(SELECT year FROM budgeting_yearendforecast WHERE yefid=budgeting_yef_lines.yefid) AND fromCurrency=budgeting_yef_lines.originalCurrency AND toCurrency=".intval($configs['toCurrency']).") END)";
        }

        if(isset($configs['vsAffid']) && !empty($configs['vsAffid'])) {
            $by = '(CASE '.$configs['vsAffid'].' = (SELECT affid FROM budgeting_yearendforecast WHERE budgeting_yearendforecast.yefid = '.self::TABLE_NAME.'.yefid) THEN localIncome ELSE (income-LocalIncome) END)';
        }

        $total = $db->fetch_assoc($db->query('SELECT SUM('.$by.$fxrate_query.') AS total, (CASE WHEN customerCountry = 0 THEN (SELECT country FROM entities WHERE entities.eid = '.self::TABLE_NAME.'.cid) ELSE customerCountry END) AS coid FROM '.self::TABLE_NAME.$dal->construct_whereclause_public($filters, $configs['operators']).' GROUP BY coid HAVING coid = '.$country->coid));
        return $total['total'];
    }

    public static function get_aggregate_byaffiliate(Affiliates $affiliate, $by, $filters = array(), $configs = array()) {
        global $db;

        $dal = new DataAccessLayer(self::CLASSNAME, self::TABLE_NAME, self::PRIMARY_KEY);

        if($configs['toCurrency']) {
            $fxrate_query = "*(CASE WHEN budgeting_yef_lines.originalCurrency=".intval($configs['toCurrency'])." THEN 1 ELSE (SELECT rate FROM budgeting_fxrates WHERE affid=(SELECT affid FROM budgeting_yearendforecast WHERE yefid=budgeting_yef_lines.yefid) AND year=(SELECT year FROM budgeting_yearendforecast WHERE yefid=budgeting_yef_lines.yefid) AND fromCurrency=budgeting_yef_lines.originalCurrency AND toCurrency=".intval($configs['toCurrency']).") END)";
        }
        $total = $db->fetch_assoc($db->query('SELECT SUM('.$by.$fxrate_query.') AS total, (SELECT affid FROM budgeting_yearendforecast WHERE budgeting_yearendforecast.yefid = '.self::TABLE_NAME.'.yefid) AS affid FROM '.self::TABLE_NAME.$dal->construct_whereclause_public($filters, $configs['operators']).' GROUP BY affid HAVING affid ='.$affiliate->affid));
        return $total['total'];
    }

    public static function get_top($percent, $attr, $filters = '', $configs = array()) {
        global $db;

        $dal = new DataAccessLayer(self::CLASSNAME, self::TABLE_NAME, self::PRIMARY_KEY);

        if(empty($configs['group'])) {
            $configs['group'] = 'cid, altCid';
        }

        $fx_query = '*(CASE WHEN yefl.originalCurrency = 840 THEN 1
            ELSE (SELECT bfr.rate from budgeting_fxrates bfr WHERE bfr.affid = yefb.affid AND bfr.year = yefb.year AND bfr.fromCurrency = yefl.originalCurrency AND bfr.toCurrency = 840) END)';
        $sql = 'SELECT SUM('.$attr.$fx_query.') AS '.$attr.' FROM '.self::TABLE_NAME.' yefl JOIN budgeting_yearendforecast yefb ON (yefb.yefid = yefl.yefid)'.$dal->construct_whereclause_public($filters, $configs['operators']).' GROUP BY '.$configs['group'].' ORDER BY '.$attr.' DESC';
        $data = $db->query($sql);
        $total = $db->fetch_field($db->query('SELECT SUM('.$attr.$fx_query.') AS total FROM '.self::TABLE_NAME.' yefl JOIN budgeting_yearendforecast yefb ON (yefb.yefid = yefl.yefid)'.$dal->construct_whereclause_public($filters, $configs['operators'])), 'total');
        while($values = $db->fetch_assoc($data)) {
            $info['count'] += 1;
            $info['contribution'] += $values[$attr];

            if((($info['contribution'] * 100) / $total) >= $percent) {
                break;
            }
        }
        return $info;
    }

    public function parse_country() {
        global $lang;

        if(!empty($this->data['customerCountry'])) {
            $country = new Countries($this->data['customerCountry']);
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

    private function split_income(&$data) {
        global $core;
        if($core->usergroup['budgeting_canFillLocalIncome'] == 1) {
            if($data['localIncomePercentage'] == 100 && $data['localIncomeAmount'] == 0) {
                $data['localIncomeAmount'] = $data['income'];
            }

            if(!empty($data['linkedBudgetLine']) && !isset($data['yeflid'])) {
                if(empty($data['interCompanyPurchase'])) {
                    return;
                }
            }
            if(empty($data['localIncomeAmount']) && $data['localIncomeAmount'] != '0') {
                if(!isset($data['saleType'])) {
                    return;
                }

                $saletype = new SaleTypes($data['saleType']);
                $data['localIncomeAmount'] = $data['income'];
                $data['localIncomePercentage'] = 100;
                $data['invoicingEntityIncome'] = 0;
                if($saletype->localIncomeByDefault == 0) {
                    $data['localIncomeAmount'] = 0;
                    $data['localIncomePercentage'] = 0;
                    $data['invoicingEntityIncome'] = $data['income'];
                }
            }
            else {
                $data['invoicingEntityIncome'] = $data['income'] - $data['localIncomeAmount'];
            }
        }
    }

}