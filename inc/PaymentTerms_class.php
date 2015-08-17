<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Paymentterms_class.php
 * Created:        @rasha.aboushakra    Feb 11, 2015 | 10:38:26 AM
 * Last Update:    @rasha.aboushakra    Feb 11, 2015 | 10:38:26 AM
 */

class PaymentTerms extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'ptid';
    const TABLE_NAME = 'paymentterms';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'title';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core, $log;
        if(!$this->validate_requiredfields($data)) {
            $fields = array('title', 'description');
            foreach($fields as $field) {
                $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data[$field] = $db->escape_string($data[$field]);
            }
            $data['name'] = generate_alias($this->data['title']);
            $db->insert_query(self::TABLE_NAME, $data);
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            return;
        }
    }

    protected function update(array $data) {
        global $db, $core, $log;
        if(!$this->validate_requiredfields($data)) {
            $fields = array('title', 'description');
            foreach($fields as $field) {
                $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data[$field] = $db->escape_string($data[$field]);
            }
            if(!isset($data['nextBusinessDay'])) {
                $data['nextBusinessDay'] = 0;
            }
            $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            return;
        }
    }

    private function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('title', 'overduePaymentDays');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != 0) {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

}