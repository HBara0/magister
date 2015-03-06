<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroRequestsLinesSupervision_class.php
 * Created:        @rasha.aboushakra    Feb 13, 2015 | 1:32:20 PM
 * Last Update:    @rasha.aboushakra    Feb 13, 2015 | 1:32:20 PM
 */

class AroRequestLinesSupervision extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'arlsid';
    const TABLE_NAME = 'aro_requests_linessupervision';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $log;
        if(!$this->validate_requiredfields($data)) {
            $aorid = $data['aorid'];
            $actualpurchase = $this->calculate_actualpurchasevalues($data);
            $actualpurchase['aorid'] = $aorid;
            unset($actualpurchase['estDateOfStockEntry_output'], $actualpurchase['estDateOfSale_output'], $actualpurchase['productName'], $actualpurchase['daysInStock']);
            $query = $db->insert_query(self::TABLE_NAME, $actualpurchase);
            if($query) {
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            }
        }
    }

    protected function update(array $data) {
        global $db, $log;
        if(!$this->validate_requiredfields($data)) {
            $actualpurchase['aorid'] = $data['aorid'];
            $actualpurchase = $this->calculate_actualpurchasevalues($data);
            unset($actualpurchase['estDateOfStockEntry_output'], $actualpurchase['estDateOfSale_output'], $actualpurchase['productName'], $actualpurchase['daysInStock']);
            $query = $db->update_query(self::TABLE_NAME, $actualpurchase, ''.self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
            if($query) {
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            }
        }
    }

    private function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('pid', 'quantity');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

    public function calculate_actualpurchasevalues($data = array()) {
        global $core;

        $plfields = array("productName", "pid", "packing", "quantity", "inputChecksum", "totalValue", "daysInStock");
        foreach($plfields as $field) {
            $actualpurchase[$field] = $data[$field];
        }
        $purchasetype = new PurchaseTypes($data['ptid']);
        $actualpurchase['transitTime'] = 20;
        $actualpurchase['clearanceTime'] = 1;
        $actualpurchase['dateOfStockEntry'] = '01-03-2015';

        $actualpurchase['dateOfStockEntry'] = strtotime($actualpurchase['dateOfStockEntry']);
        $transittime = '+'.$actualpurchase['transitTime'].' days';
        $clearance = '+'.$actualpurchase['clearanceTime'].' days';
        $actualpurchase['estDateOfStockEntry'] = strtotime($transittime, $actualpurchase['dateOfStockEntry']);
        $actualpurchase['estDateOfStockEntry'] = strtotime($clearance, $actualpurchase['estDateOfStockEntry']);
        $actualpurchase['estDateOfStockEntry_output'] = date($core->settings['dateformat'], $actualpurchase['estDateOfStockEntry']);
        $daysinstock = '+'.$actualpurchase['daysInStock'].' days';

        $actualpurchase['estDateOfSale'] = strtotime($daysinstock, $actualpurchase['estDateOfStockEntry']);
        $actualpurchase['estDateOfSale_output'] = date($core->settings['dateformat'], $actualpurchase['estDateOfSale']);
        $actualpurchase['shelfLife'] = 2;
        if($purchasetype->qtyIsNotStored == 1) {
            $actualpurchase['estDateOfSale'] = $actualpurchase['shelfLife'] = $actualpurchase['estDateOfStockEntry'] = '-';
        }
        unset($actualpurchase['transitTime'], $actualpurchase['clearanceTime'], $actualpurchase['dateOfStockEntry'], $actualpurchase['totalBuyingValue']);
        return $actualpurchase;
    }

}