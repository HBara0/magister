<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroOrderCustomers_class.php
 * Created:        @tony.assaad    Feb 13, 2015 | 2:23:37 PM
 * Last Update:    @tony.assaad    Feb 13, 2015 | 2:23:37 PM
 */

/**
 * Description of AroOrderCustomers_class
 *
 * @author tony.assaad
 */
class AroOrderCustomers extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'aocid';
    const TABLE_NAME = 'aro_order_customers';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'aocid,ptid';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'cid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function update(array $data) {
        ;
    }

    public function create(array $data) {
        global $db, $core, $log;
        $required_fields = array('affid', 'orderType'); //warehsuoe
        foreach($required_fields as $field) {
            $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
            if(is_empty($data[$field])) {
                $this->errorcode = 2;
                return false;
            }
        }
        $data['paymentTermBaseDate'] = strtotime($data['paymentTermBaseDate']);
        $policies_array = array('cid' => $data['cid'],
                'ptid' => $data['ptid'],
                'paymentTermDesc' => $data['paymentTermDesc'],
                'paymentTermBaseDate' => $data['paymentTermBaseDate'],
                'ReferenceNumber' => $data['ReferenceNumber'],
                'createdBy' => $core->user['uid'],
                'createdOn' => TIME_NOW,
        );
        $query = $db->insert_query(self::TABLE_NAME, $policies_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            $this->errorcode = 0;
        }
    }

}