<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: GorupPurchaseForecastLines_class.php
 * Created:        @rasha.aboushakra    Dec 15, 2014 | 11:49:45 AM
 * Last Update:    @rasha.aboushakra    Dec 15, 2014 | 11:49:45 AM
 */

class GroupPurchaseForecastLines extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'gpflid';
    const TABLE_NAME = 'grouppurchase_forecastlines';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'gpflid, gpfid, inputChecksum, pid, psid, saleType, businessMgr';
    const UNIQUE_ATTRS = 'businessMgr,gpfid,pid,psid,saleType';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            if(!$this->validate_requiredfields($data)) {
                $fields = array('gpfid', 'inputChecksum', 'pid', 'psid', 'saleType', 'businessMgr', 'month1', 'month2', 'month3', 'month4', 'month5', 'month6', 'month7', 'month8', 'month9', 'month10', 'month11', 'month12');
                foreach($fields as $field) {
                    if($field == 'psid' && empty($data[$field])) {
                        $product = new Products($data['pid']);
                        $data['psid'] = $product->get_genericproduct()->get_segment()->psid;
                    }
                    $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                    $data[$field] = $db->escape_string($data[$field]);
                    $forecastline_data[$field] = $data[$field];
                }
                $forecastline_data['createdOn'] = TIME_NOW;
                $forecastline_data['createdBy'] = $core->user['uid'];
                $query = $db->insert_query(self::TABLE_NAME, $forecastline_data);
            }
        }
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            if(!$this->validate_requiredfields($data)) {
                $fields = array('pid', 'psid', 'saleType', 'month1', 'month2', 'month3', 'month4', 'month5', 'month6', 'month7', 'month8', 'month9', 'month10', 'month11', 'month12');
                foreach($fields as $field) {
                    if($field == 'psid' && empty($data[$field])) {
                        $product = new Products($data['pid']);
                        $data['psid'] = $product->get_genericproduct()->get_segment()->psid;
                    }
                    $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                    $data[$field] = $db->escape_string($data[$field]);
                    $forecastline_data[$field] = $data[$field];
                }
                $forecastline_data['modifiedOn'] = TIME_NOW;
                $forecastline_data['modifiedBy'] = $core->user['uid'];
                $db->update_query(self::TABLE_NAME, $forecastline_data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
            }
        }
    }

    private function validate_requiredfields(array $data = array()) {
        global $core, $db;
        if(is_array($data)) {
            $required_fields = array('pid', 'saleType', 'month1', 'month2', 'month3', 'month4', 'month5', 'month6', 'month7', 'month8', 'month9', 'month10', 'month11', 'month12');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

}