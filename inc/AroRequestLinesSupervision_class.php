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
            unset($actualpurchase['estDateOfStockEntry_output']);
            $query = $db->insert_query(self::TABLE_NAME, $data);
            if($query) {
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            }
        }
    }

    protected function update(array $data) {
        global $db, $log;
        if(!$this->validate_requiredfields($data)) {
            unset($actualpurchase['estDateOfStockEntry_output']);
            $query = $db->update_query(self::TABLE_NAME, $data, ''.self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
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
        $rowid = $data['rowid'];
        unset($data['action'], $data['module']); //can  move to a function
        $plfields = array("productName", "pid", "packing", "quantity", "inputChecksum", "totalBuyingValue", "daysInStock");
        foreach($plfields as $field) {
            $actualpurchase[$field] = $data[$field];
        }
        $packaging = Packaging::get_data('name IS NOT NULL');
        $packaging_list = parse_selectlist('actualpurchase['.$rowid.'][packing]', '', $packaging, $actualpurchase [packing], '', '', array('id' => "actualpurchase_".$plrowid."_packing", 'blankstart' => 1));
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
        $actualpurchase['estDateOfSale'] = strtotime('+'.$actualpurchase['daysInStock'].' days', $actualpurchase['estDateOfStockEntry']);

        $actualpurchase['shelfLife'] = 2;
        if($purchasetype->qtyIsNotStored == 1) {
            $actualpurchase['estDateOfSale'] = $actualpurchase['shelfLife'] = $actualpurchase['estDateOfStockEntry'] = '-';
        }
        return $actualpurchase;
    }

}