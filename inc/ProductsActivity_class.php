<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: productsactivity_class.php
 * Created:        @tony.assaad    Dec 1, 2014 | 3:06:14 PM
 * Last Update:    @tony.assaad    Dec 1, 2014 | 3:06:14 PM
 */

/**
 * Description of productsactivity_class
 *
 * @author tony.assaad
 */
class ProductsActivity extends AbstractClass {
    protected $data = array();
    protected $errorcode = null;

    const PRIMARY_KEY = 'paid';
    const TABLE_NAME = 'productsactivity';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'paid, pid, rid, uid';
    const UNIQUE_ATTRS = 'pid, rid, uid';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db;
        if(empty($data['pid']) || !isset($data['pid'])) {
            $this->errorcode = 1;
            return;
        }
        if(empty($data['rid']) || !isset($data['rid'])) {
            $this->errorcode = 1;
            return;
        }
        if(empty($data['uid']) || !isset($data['uid'])) {
            $this->errorcode = 1;
            return;
        }

        $query = $db->insert_query(self::TABLE_NAME, $data);
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(is_array($data)) {
            if(empty($data['pid']) || !isset($data['pid'])) {
                $this->errorcode = 1;
                return;
            }
            if(empty($data['rid']) || !isset($data['rid'])) {
                $this->errorcode = 1;
                return;
            }
            if(empty($data['uid']) || !isset($data['uid'])) {
                $this->errorcode = 1;
                return;
            }
            $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        }
        return $this;
    }

    public function get_report() {
        return new Reporting(array('rid' => $this->data['rid']));
    }

    private function get_budget() {
        return $this->get_report()->get_budget();
    }

    public function get_relatedbudgetlines() {
        if(class_exists('BudgetLines')) {
            $budget = $this->get_budget();
            if(!is_object($budget)) {
                return;
            }

            // get BM of related budgetlines.
            $budgetlines_obs = $budget->get_budgetlines_objs(array('bid' => $budget->bid));

            $filterbudget_config = array('pid' => $this->data['pid'], 'bid' => $budget->bid);
            //switch user filter  between productacti user of budget line user
            if($this->data['uid'] != 0) {
                $filterbudget_config['businessMgr'] = $this->data['uid'];
            }
            else {
                foreach($budgetlines_obs as $budgetlines_ob) {
                    $filterbudget_config['businessMgr'] = $budgetlines_ob->businessMgr;
                }
            }
            return BudgetLines::get_data($filterbudget_config, array('returnarray' => true));
        }
    }

    public function get_product() {
        $product = new Products($this->data['pid']);
        if(is_object($product)) {
            return $product;
        }
        return false;
    }

    public function aggregate_relatedbudgetlines($config = array()) {
        global $db;
        if(!isset($config['aggregatebm'])) {
            $config['aggregatebm'] = true;
        }
        $budget = $this->get_budget();
        $budgetlines = $this->get_relatedbudgetlines();
        $fxrate_query = '(CASE WHEN budgeting_budgets_lines.originalCurrency = 840 THEN 1
                          ELSE (SELECT bfr.rate from budgeting_fxrates bfr WHERE bfr.affid = '.$budget->affid.' AND isBudget=1 AND  bfr.year = '.$budget->year.' AND bfr.fromCurrency = budgeting_budgets_lines.originalCurrency  AND bfr.toCurrency = 840) END)';

        if(empty($budgetlines)) {
            return null;
        }
        $sql = "SELECT blid, pid, businessMgr AS businessmgr , SUM(amount*{$fxrate_query}) AS amount, SUM(quantity) AS quantity FROM ".Tprefix."budgeting_budgets_lines WHERE blid IN (".implode(',', array_keys($budgetlines)).") GROUP By businessMgr";
        $sumquery = $db->query($sql);
        if($db->num_rows($sumquery) > 0) {
            while($item = $db->fetch_assoc($sumquery)) {
                if(isset($config['aggregatebm']) && $config['aggregatebm'] == true) {
                    $aggregated_lines[$item['businessmgr']] = $item;
                }
                else {
                    $aggregated_lines['amount'] += $item['amount'];
                    $aggregated_lines['quantity'] += $item['quantity'];
                }
            }
            return $aggregated_lines;
        }
    }

}