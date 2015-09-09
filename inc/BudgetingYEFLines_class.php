<?php
/* -------Definiton-START-------- */

class BudgetingYEFLines extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'yeflid';
    const TABLE_NAME = 'budgeting_yef_lines';
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
                'inputCheckSum' => $data['inputCheckSum'],
                'yefid' => $data['yefid'],
                'pid' => $data['pid'],
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
                'createdBy' => $core->user['id'],
                'createdOn' => TIME_NOW,
                'commissionSplitAffid' => $data['commissionSplitAffid'],
                'purchasingEntity' => $data['purchasingEntity'],
                'purchasingEntityId' => $data['purchasingEntityId'],
                'linkedBudgetLine' => $data['linkedBudgetLine'],
                'psid' => $data['psid'],
                'fromBudget' => $data['fromBudget'],
        );
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(is_array($data)) {
            $update_array['inputCheckSum'] = $data['inputCheckSum'];
            $update_array['yefid'] = $data['yefid'];
            $update_array['pid'] = $data['pid'];
            $update_array['cid'] = $data['cid'];
            $update_array['altCid'] = $data['altCid'];
            $update_array['prevyeflid'] = $data['prevyeflid'];
            $update_array['customerCountry'] = $data['customerCountry'];
            $update_array['businessMgr'] = $data['businessMgr'];
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
            $update_array['modifiedBy'] = $core->user['id'];
            $update_array['modifiedOn'] = TIME_NOW;
            $update_array['commissionSplitAffid'] = $data['commissionSplitAffid'];
            $update_array['purchasingEntity'] = $data['purchasingEntity'];
            $update_array['purchasingEntityId'] = $data['purchasingEntityId'];
            $update_array['linkedBudgetLine'] = $data['linkedBudgetLine'];
            $update_array['psid'] = $data['psid'];
            $update_array['fromBudget'] = $data['fromBudget'];
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
            $budgetline_bydataquery = $db->query("SELECT * FROM ".Tprefix."budgeting_yef_lines WHERE pid='".$data['pid']."' AND cid='".$data['cid']."' AND altCid='".$db->escape_string($data['altCid'])."' AND saleType='".$data['saleType']."' AND bid='".$data['bid']."' AND customerCountry='".$data['customerCountry']."' AND psid='".$data['psid']."' AND businessMgr='".$data['businessMgr']."'");
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
        $data_toremove = array('yefid', 'yeflid', 'cid', 'interCompanyPurchase');
        $data_zerofill = array('invoicingEntityIncome'); //'localIncomePercentage', 'localIncomeAmount',
        $yef = $this->get_yef();
        $data['inputChecksum'] = generate_checksum('yefl');
        $data['linkedBudgetLine'] = $this->data['yeflid'];
        $data['altCid'] = $yef->get_affiliate()->name;
        $data['customerCountry'] = $yef->get_affiliate()->country;
        $data['saleType'] = 6; //Need to be acquire through DAL where isInterCoSale
        $data['amount'] = $data['amount'] - $data['income'];
        /* Apply Default Margin */
        $data['income'] = $data['localIncomeAmount'] = $data['amount'] * 0.03;
        $data['localIncomePercentage'] = 100;

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

        $data['yefid'] = $ic_budget->yefid;
        if(empty($data['yefid'])) {
            $ic_budget = BudgetingYearEndForecast::get_data(array('affid' => $budgetdata_intercompany['affid'], 'spid' => $yef->spid, 'year' => $yef->year), array('simple' => false));
            $data['yefid'] = $ic_budget->yefid;
        }

        $ic_budgetline = new BudgetingYEFLines();
        $ic_budgetline->create($data);

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

}