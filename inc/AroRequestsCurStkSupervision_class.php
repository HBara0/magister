<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroRequestsCurStkSupervision.php
 * Created:        @rasha.aboushakra    Feb 13, 2015 | 1:35:30 PM
 * Last Update:    @rasha.aboushakra    Feb 13, 2015 | 1:35:30 PM
 */

class AroRequestsCurStkSupervision extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'arcssid';
    const TABLE_NAME = 'aro_requests_curstksupervision';
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
            //$currentstock['aorid'] = $data['aorid'];
            unset($data['packingTitle']);
            $dates = array('dateOfStockEntry', 'expiryDate', 'estDateOfSale');
            foreach($dates as $date) {
                if(isset($data[$date]) && !empty($data[$date])) {
                    $data[$date] = strtotime($data[$date]);
                }
            }
            $query = $db->insert_query(self::TABLE_NAME, $data);
            if($query) {
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
                return $this;
            }
        }
    }

    protected function update(array $data) {
        global $db, $log;
        if(!$this->validate_requiredfields($data)) {
            unset($data['packingTitle']);
            $dates = array('dateOfStockEntry', 'expiryDate', 'estDateOfSale');
            foreach($dates as $date) {
                if(isset($data[$date]) && !empty($data[$date])) {
                    $data[$date] = strtotime($data[$date]);
                }
            }
            $query = $db->update_query(self::TABLE_NAME, $data, ''.self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
            if($query) {
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
                return $this;
            }
        }
    }

    protected function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('pid', 'quantity', 'packing');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

}