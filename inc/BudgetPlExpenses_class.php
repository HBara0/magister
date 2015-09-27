<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetPlExpenses_class.php
 * Created:        @rasha.aboushakra    Oct 15, 2014 | 1:29:54 PM
 * Last Update:    @rasha.aboushakra    Oct 15, 2014 | 1:29:54 PM
 */

class BudgetPlExpenses extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'bpleid';
    const TABLE_NAME = 'budgeting_plexpenses';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'bpleid, bpliid, bfbid,actualPrevThreeYears, actualPrevTwoYears, yefPrevYear, budgetCurrent';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $required_fields = array('bpliid', 'bfbid', 'actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent');
            foreach($required_fields as $field) {
                $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data[$field] = $db->escape_string($data[$field]);
                $placcount_data[$field] = $data[$field];
            }
            $placcount_data['createdOn'] = TIME_NOW;
            $placcount_data['createdBy'] = $core->user['uid'];
            $query = $db->insert_query(self::TABLE_NAME, $placcount_data);
        }
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $required_fields = array('bpliid', 'bfbid', 'actualPrevThreeYears', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent');
            foreach($required_fields as $field) {
                $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data[$field] = $db->escape_string($data[$field]);
                $placcount_data[$field] = $data[$field];
            }
            $placcount_data['modifiedOn'] = TIME_NOW;
            $placcount_data['modifiedBy'] = $core->user['uid'];
            $db->update_query(self::TABLE_NAME, $placcount_data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        }
    }

    public function save(array $data = array()) {
        if(empty($data)) {
            $data = $this->data;
        }
        if(!$this->validate_requiredfields($data)) {
            $headcounts = self::get_data(array('bpleid' => $this->data[self::PRIMARY_KEY]));
            if(is_object($headcounts)) {
                $headcounts->update($data);
            }
            else {
                $headcounts = self::get_data(array('bfbid' => $data['bfbid'], 'bpliid' => $data['bpliid']));
                if(is_object($headcounts)) {
                    $headcounts->update($data);
                }
                else {
                    $this->create($data);
                }
            }
        }
    }

    protected function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('bpliid', 'bfbid', 'actualPrevTwoYears', 'yefPrevYear', 'budgetCurrent');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

}
?>