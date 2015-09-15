<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetForecastBalanceSheet.php
 * Created:        @tony.assaad    Oct 1, 2014 | 2:56:22 PM
 * Last Update:    @tony.assaad    Oct 1, 2014 | 2:56:22 PM
 */

/**
 * Description of BudgetForecastBalanceSheet
 *
 * @author tony.assaad
 */
class BudgetForecastBalanceSheet extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'bfbsid';
    const TABLE_NAME = 'budgeting_forecastbs';
    const DISPLAY_NAME = 'amount';
    const SIMPLEQ_ATTRS = 'bfbsid, batid, bfbid,amount';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $required_fields = array('bfbid', 'batid', 'amount');
            foreach($required_fields as $field) {
                $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data[$field] = $db->escape_string($data[$field]);
                $forecastbs_data[$field] = $data[$field];
            }
            $forecastbs_data['createdOn'] = TIME_NOW;
            $forecastbs_data['createdBy'] = $core->user['uid'];
            $query = $db->insert_query(self::TABLE_NAME, $forecastbs_data);
        }
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $required_fields = array('bfbid', 'batid', 'amount');
            foreach($required_fields as $field) {
                $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data[$field] = $db->escape_string($data[$field]);
                $forecastbs_data[$field] = $data[$field];
            }

            $forecastbs_data['modifiedOn'] = TIME_NOW;
            $forecastbs_data['modifiedBy'] = $core->user['uid'];
            $db->update_query(self::TABLE_NAME, $forecastbs_data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        }
    }

    public function save(array $data = array()) {

        global $core;
        if(empty($data)) {
            $data = $this->data;
        }
        if(!$this->validate_requiredfields($data)) {
            $forecastbs = self::get_data(array('bfbsid' => $this->data[self::PRIMARY_KEY]));
            if(is_object($forecastbs)) {
                $forecastbs->update($data);
            }
            else {

                $forecastbs = self::get_data(array('bfbid' => $data['bfbid'], 'batid' => $data['batid']));
                if(is_object($forecastbs)) {
                    $forecastbs->update($data);
                }
                else {
                    $this->create($data);
                }
            }
        }
    }

    protected function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('batid', 'amount');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

}