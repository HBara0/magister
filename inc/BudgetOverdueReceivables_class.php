<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetingOverdueReceivables_class.php
 * Created:        @rasha.aboushakra    Nov 3, 2014 | 11:48:42 AM
 * Last Update:    @rasha.aboushakra    Nov 3, 2014 | 11:48:42 AM
 */

class BudgetOverdueReceivables extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'boid';
    const TABLE_NAME = 'budgeting_overduereceivables';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'boid,inputChecksum, bfbid, cid, legalAction, oldestUnpaidInvoiceDate, totalAmount, reason, action';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'bfbid,cid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(!$this->validate_requiredfields($data)) {
            if(is_array($data)) {
                $required_fields = array('inputChecksum', 'bfbid', 'cid', 'legalAction', 'oldestUnpaidInvoiceDate', 'totalAmount', 'reason', 'action');
                foreach($required_fields as $field) {
                    $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                    $data[$field] = $db->escape_string($data[$field]);
                    $overdues_data[$field] = $data[$field];
                }
                $overdues_data['oldestUnpaidInvoiceDate'] = strtotime($overdues_data['oldestUnpaidInvoiceDate']);
                $overdues_data['createdOn'] = TIME_NOW;
                $overdues_data['createdBy'] = $core->user['uid'];
                $query = $db->insert_query(self::TABLE_NAME, $overdues_data);
            }
        }
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $required_fields = array('bfbid', 'cid', 'legalAction', 'oldestUnpaidInvoiceDate', 'totalAmount', 'reason', 'action');
            foreach($required_fields as $field) {
                $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data[$field] = $db->escape_string($data[$field]);
                $overdues_data[$field] = $data[$field];
            }
            $overdues_data['oldestUnpaidInvoiceDate'] = strtotime($overdues_data['oldestUnpaidInvoiceDate']);
            $overdues_data['modifiedOn'] = TIME_NOW;
            $overdues_data['modifiedBy'] = $core->user['uid'];
            $db->update_query(self::TABLE_NAME, $overdues_data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        }
    }

    protected function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('cid', 'totalAmount', 'oldestUnpaidInvoiceDate');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

    public function delete_clientoverdues() {
        global $db, $core;
        if(empty($data)) {
            $data = $this->data;
        }
        if(isset($data[self::PRIMARY_KEY]) && !empty($data[self::PRIMARY_KEY])) {
            $clientoverdue = self::get_data(array('boid' => $data[self::PRIMARY_KEY]));
            $clientoverdue->delete();
        }
        if(isset($data['inputChecksum']) && !empty($data['inputChecksum'])) {
            $clientoverdue = self::get_data(array('inputChecksum' => $data['inputChecksum']));
            if(is_object($clientoverdue)) {
                $clientoverdue->delete();
            }
        }
    }

}
?>