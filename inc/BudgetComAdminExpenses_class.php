<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetComAdminExpenses_Class.php
 * Created:        @rasha.aboushakra    Sep 23, 2014 | 3:49:48 PM
 * Last Update:    @rasha.aboushakra    Sep 23, 2014 | 3:49:48 PM
 */

Class BudgetComAdminExpenses extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'bcaeid';
    const TABLE_NAME = 'budgeting_commadminexps';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'bcaeid, beciid, actualPrevTwoYears, actualPrevYear, budgetPrevYear, yefPrevYear, budgetCurrent';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $required_fields = array('bfbid', 'beciid', 'actualPrevTwoYears', 'actualPrevYear', 'budgetPrevYear', 'yefPrevYear', 'budgetCurrent', 'budYefPerc');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != 0) {
                    $this->errorcode = 1;
                    return false;
                }
                $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data[$field] = $db->escape_string($data[$field]);
                $comadminexpense_data[$field] = $data[$field];
            }
            $comadminexpense_data['createdOn'] = TIME_NOW;
            $comadminexpense_data['createdBy'] = $core->user['uid'];
            $query = $db->insert_query(self::TABLE_NAME, $comadminexpense_data);
        }
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $required_fields = array('bfbid', 'beciid', 'actualPrevTwoYears', 'actualPrevYear', 'budgetPrevYear', 'yefPrevYear', 'budgetCurrent', 'budYefPerc');
            foreach($required_fields as $field) {
                if(is_empty($data[$field])) {
                    $this->errorcode = 1;
                    return false;
                }
                $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data[$field] = $db->escape_string($data[$field]);
                $comadminexpense_data[$field] = $data[$field];
            }

            $comadminexpense_data['modifiedOn'] = TIME_NOW;
            $comadminexpense_data['modifiedBy'] = $core->user['uid'];
            $db->update_query(self::TABLE_NAME, $comadminexpense_data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        }
    }

    public function save(array $data = array()) {
        if(empty($data)) {
            $data = $this->data;
        }
        if(!$this->validate_requiredfields($data)) {
            $commadminexps = self::get_data(array('bcaeid' => $this->data[self::PRIMARY_KEY]));
            if(is_object($commadminexps)) {
                $commadminexps->update($data);
            }
            else {
                $commadminexps = self::get_data(array('bfbid' => $data['bfbid'], 'beciid' => $data['beciid']));
                if(is_object($commadminexps)) {
                    $commadminexps->update($data);
                }
                else {
                    $this->create($data);
                }
            }
        }
    }

    private function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('bfbid', 'beciid', 'actualPrevTwoYears', 'actualPrevYear', 'budgetPrevYear', 'yefPrevYear', 'budgetCurrent', 'budYefPerc');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {

                    $this->errorcode = 1;
                    return true;
                }
            }
        }
    }

}
?>