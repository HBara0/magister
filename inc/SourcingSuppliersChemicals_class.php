<?php
/*-------Definiton-START--------*/
class SourcingSuppliersChemicals extends AbstractClass {
        protected $data = array();
        protected $errorcode = 0;
        const PRIMARY_KEY = 'sscid';
        const TABLE_NAME = 'sourcing_suppliers_chemicals';
        const SIMPLEQ_ATTRS = '*';
        const UNIQUE_ATTRS = 'sscid,ssid,csid';
        const CLASSNAME = __CLASS__;
        const DISPLAY_NAME = '';

                    /*-------Definiton-END--------*/
/*-------FUNCTIONS-START--------*/

public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
                }

public function create(array $data) {
        global $db,$core;
        $table_array = array(
 	'ssid' => $data['ssid'],
	'csid' => $data['csid'],
	'supplyType' => $data['supplyType'],

                );
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

protected function update(array $data) {
        global $db;
        if(is_array($data)) {
	$update_array['ssid']=$data['ssid'];
	$update_array['csid']=$data['csid'];
	$update_array['supplyType']=$data['supplyType'];

                    }
       $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
        }

/*-------FUNCTIONS-END--------*/

}