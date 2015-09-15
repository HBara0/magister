<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetInvestExpenses_class.php
 * Created:        @tony.assaad    Sep 29, 2014 | 12:28:27 PM
 * Last Update:    @tony.assaad    Sep 29, 2014 | 12:28:27 PM
 */

/**
 * Description of BudgetInvestExpenses_class
 *
 * @author tony.assaad
 */
class BudgetInvestExpenses extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'biid';
    const TABLE_NAME = 'budgeting_investexpenses';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'biid, bfbid,biiid,actualPrevThreeYears,actualPrevTwoYears,yefPrevYear,budgetCurrent';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $fields = array('bfbid', 'biiid', 'actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent'); // 'percVariation','actualPrevYear', 'budgetPrevYear'
            foreach($fields as $field) {
                $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data[$field] = $db->escape_string($data[$field]);
                $investexpenses_data[$field] = $data[$field];
            }

            $investexpenses_data['createdOn'] = TIME_NOW;
            $investexpenses_data['createdBy'] = $core->user['uid'];
            $query = $db->insert_query(self::TABLE_NAME, $investexpenses_data);
        }
    }

    protected function update(array $data) {

        global $db, $core;
        if(is_array($data)) {
            $fields = array('bfbid', 'biiid', 'actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent'); // 'percVariation','actualPrevYear', 'budgetPrevYear'
            foreach($fields as $field) {
                $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data[$field] = $db->escape_string($data[$field]);
                $investexpenses_data[$field] = $data[$field];
            }

            $investexpenses_data['modifiedOn'] = TIME_NOW;
            $investexpenses_data['modifiedBy'] = $core->user['uid'];
            $db->update_query(self::TABLE_NAME, $investexpenses_data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        }
    }

    public function save(array $data = array()) {
        global $core;
        if(empty($data)) {
            $data = $this->data;
        }
        if(!$this->validate_requiredfields($data)) {
            $investexpenses = self::get_data(array('biid' => $this->data[self::PRIMARY_KEY]));
            if(is_object($investexpenses)) {
                $investexpenses->update($data);
            }
            else {
                $investexpenses = self::get_data(array('bfbid' => $data['bfbid'], 'biiid' => $data['biiid']));
                if(is_object($investexpenses)) {
                    $investexpenses->update($data);
                }
                else {
                    $this->create($data);
                }
            }
        }
    }

    protected function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent');
            foreach($required_fields as $field) {
                $x = empty($data[$field]);
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

}