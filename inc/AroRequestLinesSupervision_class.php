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

    // const UNIQUE_ATTRS = 'aorid,pid,packing';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $log;
        if(!$this->validate_requiredfields($data)) {
            $aorid = $data['aorid'];
            $actualpurchase = $this->calculate_actualpurchasevalues($data);
            $actualpurchase['aorid'] = $aorid;
            $actualpurchase['shelfLife'] = $data['shelfLife'];
            unset($actualpurchase['estDateOfStockEntry_formatted'], $actualpurchase['estDateOfSale_formatted'], $actualpurchase['estDateOfStockEntry_output'], $actualpurchase['estDateOfSale_output'], $actualpurchase['productName'], $actualpurchase['daysInStock']);
            $query = $db->insert_query(self::TABLE_NAME, $actualpurchase);
            if($query) {
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            }
        }
    }

    protected function update(array $data) {
        global $db, $log;
        if(!$this->validate_requiredfields($data)) {
            $actualpurchase = $this->calculate_actualpurchasevalues($data);
            $actualpurchase['aorid'] = $data['aorid'];
            $actualpurchase['shelfLife'] = $data['shelfLife'];
            unset($actualpurchase['estDateOfStockEntry_formatted'], $actualpurchase['estDateOfSale_formatted'], $actualpurchase['estDateOfStockEntry_output'], $actualpurchase['estDateOfSale_output'], $actualpurchase['productName'], $actualpurchase['daysInStock']);
            $query = $db->update_query(self::TABLE_NAME, $actualpurchase, ''.self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
            if($query) {
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            }
        }
    }

    protected function validate_requiredfields(array $data = array()) {
        global $errorhandler, $lang;
        if(is_array($data)) {
            $required_fields = array('pid', 'quantity', 'packing');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $errorhandler->record('Required fields', $lang->$field);
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

    public function calculate_actualpurchasevalues($data = array()) {
        global $core;

        $plfields = array("productName", "pid", "packing", "quantity", "inputChecksum", "totalValue", "daysInStock", "transitTime", "clearanceTime", "dateOfStockEntry", "estDateOfStockEntry", "estDateOfSale");
        foreach($plfields as $field) {
            $actualpurchase[$field] = $data[$field];
        }
        $purchasetype = new PurchaseTypes($data['ptid']);

        $actualpurchase['dateOfStockEntry'] = strtotime($actualpurchase['dateOfStockEntry']);

        if(isset($actualpurchase['estDateOfStockEntry']) && !empty($actualpurchase['estDateOfStockEntry'])) {
            $actualpurchase['estDateOfStockEntry'] = strtotime($actualpurchase['estDateOfStockEntry']);
            $actualpurchase['estDateOfSale'] = strtotime($actualpurchase['estDateOfSale']);
        }
        else {
            $estDateOfStockEntry = $this->get_stockentryestdate($actualpurchase);
            $actualpurchase['estDateOfStockEntry'] = $estDateOfStockEntry['output'];
            $actualpurchase['estDateOfStockEntry_output'] = $estDateOfStockEntry['formatted'];
            //  $transittime = '+'.$actualpurchase['transitTime'].' days';
            //  $clearance = '+'.$actualpurchase['clearanceTime'].' days';
            //  $actualpurchase['estDateOfStockEntry'] = strtotime($transittime, $actualpurchase['dateOfStockEntry']);
            //  $actualpurchase['estDateOfStockEntry'] = strtotime($clearance, $actualpurchase['estDateOfStockEntry']);
            $actualpurchase['estDateOfSale'] = strtotime($actualpurchase['estDateOfSale']);
            if(isset($actualpurchase['daysInStock']) && !empty($actualpurchase['daysInStock'])) {
                $daysinstock = '+'.$actualpurchase['daysInStock'].' days';
                $actualpurchase['estDateOfSale'] = strtotime($daysinstock, $actualpurchase['estDateOfStockEntry']);
            }
        }
        if(!empty($actualpurchase['estDateOfSale']) && $actualpurchase['estDateOfSale'] != false) {
            $actualpurchase['estDateOfSale_output'] = date($core->settings['dateformat'], $actualpurchase['estDateOfSale']);
            $actualpurchase['estDateOfSale_formatted'] = date('d-m-Y', $actualpurchase['estDateOfSale']);
        }
        $actualpurchase['estDateOfStockEntry_formatted'] = date('d-m-Y', $actualpurchase['estDateOfStockEntry']);

        if($purchasetype->qtyIsNotStored == 1) {
            $actualpurchase['estDateOfSale'] = $actualpurchase['shelfLife'] = $actualpurchase['estDateOfStockEntry'] = '-';
        }
        unset($actualpurchase['transitTime'], $actualpurchase['clearanceTime'], $actualpurchase['dateOfStockEntry'], $actualpurchase['totalBuyingValue'], $actualpurchase['daysInStock']);
        if(isset($data['diffStockSalesdates']) && !is_empty($data['diffStockSalesdates'])) {
            $actualpurchase['diffStockSalesdates'] = $data['diffStockSalesdates'];
        }
        return $actualpurchase;
    }

    public function get_stockentryestdate($data = array()) {
        global $core;
        $transittime = '+'.$data['transitTime'].' days';
        $clearance = '+'.$data['clearanceTime'].' days';
        if($data['transitTime'] != 0) {
            $data['estDateOfStockEntry'] = strtotime($transittime, $data['dateOfStockEntry']);
        }
        if($data['clearanceTime'] != 0) {
            $data['estDateOfStockEntry'] = strtotime($clearance, $data['estDateOfStockEntry']);
        }
        if(!empty($data['estDateOfStockEntry'])) {
            $estDateOfStockEntry['output'] = $data['estDateOfStockEntry'];
            $estDateOfStockEntry['formatted'] = date($core->settings['dateformat'], $data['estDateOfStockEntry']);
        }
        return $estDateOfStockEntry;
    }

}