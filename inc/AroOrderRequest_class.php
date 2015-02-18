<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroOrderIdentification_class.php
 * Created:        @tony.assaad    Feb 11, 2015 | 11:40:28 AM
 * Last Update:    @tony.assaad    Feb 11, 2015 | 11:40:28 AM
 */

/**
 * Description of AroOrderIdentification_class
 *
 * @author tony.assaad
 */
class AroOrderRequest extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'aorid';
    const TABLE_NAME = 'aro_order_requests';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'aorid,affid,orderType';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'affid,orderType';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core, $log;
        $orderid_data = $data['orderid'];
        $required_fields = array('affid', 'orderType'); //warehsuoe
        foreach($required_fields as $field) {
            $orderid_data[$field] = $core->sanitize_inputs($orderid_data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
            if(is_empty($orderid_data[$field])) {
                $this->errorcode = 2;
                return false;
            }
        }

        $policies_array = array('affid' => $orderid_data['affid'],
                'orderType' => $orderid_data['orderType'],
                'orderReference' => $orderid_data['orderReference'],
                'inspectionType' => $orderid_data['inspectionType'],
                'currency' => $orderid_data['currency'],
                'exchangeRateToUSD' => $orderid_data['exchangeRateToUSD'],
                'ReferenceNumber' => $orderid_data['ReferenceNumber'],
                'createdBy' => $core->user['uid'],
                'createdOn' => TIME_NOW,
        );
        $query = $db->insert_query(self::TABLE_NAME, $policies_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            $this->errorcode = 0;
        }
        $this->save_productlines($data['productline']);
    }

    protected function update(array $data) {
        ;
    }

    private function save_productlines($arorequestlines) {
        if(is_array($arorequestlines)) {
            foreach($arorequestlines as $arorequestline) {
                $arorequestline['aorid'] = $this->data[self::PRIMARY_KEY];
                $requestline = new AroRequestLines();
                $requestline->set($arorequestline);
                $requestline->save();
                $this->errorcode = $requestline->errorcode;
                switch($this->get_errorcode()) {
                    case 0:
                        continue;
                    case 2:
                        return;
                }
            }
        }
    }

}