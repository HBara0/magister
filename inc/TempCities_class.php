<?php
/*-------Definiton-START--------*/
class TempCities extends AbstractClass {
        protected $data = array();
        protected $errorcode = 0;
        const PRIMARY_KEY = 'tempcitid';
        const TABLE_NAME = 'temp_cities';
        const SIMPLEQ_ATTRS = '*';
        const UNIQUE_ATTRS = 'tempcitid';
        const CLASSNAME = __CLASS__;
        const DISPLAY_NAME = 'cityName';

                    /*-------Definiton-END--------*/
/*-------FUNCTIONS-START--------*/

public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
                }

public function create(array $data) {
        global $db,$core;
        $table_array = array(
 	'operation' => $data['operation'],
	'countryCode' => $data['countryCode'],
	'cityCode' => $data['cityCode'],
	'cityName' => $data['cityName'],
	'cityName2' => $data['cityName2'],

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
	$update_array['operation']=$data['operation'];
	$update_array['countryCode']=$data['countryCode'];
	$update_array['cityCode']=$data['cityCode'];
	$update_array['cityName']=$data['cityName'];
	$update_array['cityName2']=$data['cityName2'];

                    }
       $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
        }

/*-------FUNCTIONS-END--------*/

}