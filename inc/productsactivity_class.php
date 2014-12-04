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
class productsactivity extends AbstractClass {
    protected $data = array();
    protected $errorcode = null;

    const PRIMARY_KEY = 'paid';
    const TABLE_NAME = 'productsactivity';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'paid, pid, rid, uid';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {

    }

    protected function update(array $data) {

    }

    public function save(array $data = array()) {

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
            return BudgetLines::get_data($filterbudget_config);
        }
    }

    public function aggregate_relatedbudgetlines() {
        global $db;
        $budget = $this->get_budget();
        $budgetlines = $this->get_relatedbudgetlines();
        $fxrate_query = '(CASE WHEN budgeting_budgets_lines.originalCurrency = 840 THEN 1
                          ELSE (SELECT bfr.rate from budgeting_fxrates bfr WHERE bfr.affid = '.$budget->affid.' AND bfr.year = '.$budget->year.' AND bfr.fromCurrency = budgeting_budgets_lines.originalCurrency  AND bfr.toCurrency = 840) END)';

        $sql = "SELECT blid, pid,   businessMgr as businessmgr , sum(amount*{$fxrate_query}) AS amount, sum(quantity) AS quantity FROM ".Tprefix."budgeting_budgets_lines WHERE blid IN (".implode(',', array_keys($budgetlines)).") GROUP By businessMgr, pid";
        $sumquery = $db->query($sql);

        if($db->num_rows($sumquery) > 0) {
            while($item = $db->fetch_assoc($sumquery)) {
                $aggregated_lines[$item['pid']][$item['businessmgr']] = $item;
            }
            return $aggregated_lines;
        }
    }

}