<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: FinancialBudget_Class.php
 * Created:        @rasha.aboushakra    Sep 25, 2014 | 9:48:20 AM
 * Last Update:    @rasha.aboushakra    Sep 25, 2014 | 9:48:20 AM
 */

Class FinancialBudget extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'bfbid';
    const TABLE_NAME = 'budgeting_financialbudget';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'bfbid, affid, year';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $financialdata['bfbid'] = self::PRIMARY_KEY;
            // $required_fields = array('affid', 'year', 'finGenAdmExpAmtApty', 'finGenAdmExpAmtBpy', 'finGenAdmExpAmtYpy', 'finGenAdmExpAmtCurrent');  // this will not be applicable for the other expenses
            $required_fields = array('affid', 'year');
            foreach($required_fields as $field) {
                if(empty($data['financialbudget'][$field]) && $data['financialbudget'][$field] == 0) {
                    $this->errorcode = 1;
                    return false;
                }
                $data['financialbudget'][$field] = $core->sanitize_inputs($data['financialbudget'][$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data['financialbudget'][$field] = $db->escape_string($data['financialbudget'][$field]);
                $financialdata[$field] = $data['financialbudget'][$field];
            }
            $financialdata['createdOn'] = TIME_NOW;
            $financialdata['createdBy'] = $core->user['uid'];
            $query = $db->insert_query(self::TABLE_NAME, $financialdata);
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        $financialexpenses = $data['budgetexps'];
        if(is_array($financialexpenses)) {
            foreach($financialexpenses as $expense) {
                $expense['bfbid'] = $this->data[self::PRIMARY_KEY];
                $comadminexpense = new BudgetComAdminExpenses();
                $comadminexpense->set($expense);
                $comadminexpense->save();
                //  $this->errorcode = 0;
            }
        }

        $financialinvest = $data['budgetinvst'];
        if(is_array($financialinvest)) {
            foreach($financialinvest as $invest) {
                $invest['bfbid'] = $this->data[self::PRIMARY_KEY];
                $investfollowup = new BudgetInvestExpenses();
                $investfollowup->set($invest);
                $investfollowup->save();
                //  $this->errorcode = 0;
            }
        }
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $required_fields = array('affid', 'year', 'finGenAdmExpAmtApty', 'finGenAdmExpAmtBpy', 'finGenAdmExpAmtYpy', 'finGenAdmExpAmtCurrent');
            foreach($required_fields as $field) {
                if(is_empty($data['financialbudget'][$field])) {
                    $this->errorcode = 1;
                    //  return false;
                }
                $data['financialbudget'][$field] = $core->sanitize_inputs($data['financialbudget'][$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data['financialbudget'][$field] = $db->escape_string($data['financialbudget'][$field]);
                $financialdata[$field] = $data['financialbudget'][$field];
            }
            $financialdata['modifiedOn'] = TIME_NOW;
            $financialdata['modifiedBy'] = $core->user['uid'];
            $db->update_query(self::TABLE_NAME, $financialdata, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));

            $financialexpenses = $data['budgetexps'];
            if(is_array($financialexpenses)) {
                foreach($financialexpenses as $expense) {
                    $expense['bfbid'] = $this->data[self::PRIMARY_KEY];
                    $comadminexpense = new BudgetComAdminExpenses();
                    $comadminexpense->set($expense);
                    $comadminexpense->save();
                    //  $this->errorcode = 0;
                }
            }
        }
    }

    public function save(array $data = array()) {
        if(empty($data)) {
            $data = $this->data;
        }

        $financialbudget = FinancialBudget::get_data(array('bfbid' => $this->data[self::PRIMARY_KEY]));
        if(is_object($financialbudget)) {
            $financialbudget->update($data);
        }
        else {
            $financialbudget = FinancialBudget::get_data(array('affid' => $data['financialbudget']['affid'], 'year' => $data['financialbudget']['year']));
            if(is_object($financialbudget)) {
                $financialbudget->update($data);
            }
            else {
                $this->create($data);
            }
        }
    }

}
?>