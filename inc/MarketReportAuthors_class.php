<?php
/*-------Definiton-START--------*/
class MarketReportAuthors extends AbstractClass {
        protected $data = array();
        protected $errorcode = 0;
        const PRIMARY_KEY = 'mkra';
        const TABLE_NAME = 'marketreport_authors';
        const SIMPLEQ_ATTRS = '*';
        const UNIQUE_ATTRS = 'uid,mrid';
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
 	'uid' => $data['uid'],
	'mrid' => $data['mrid'],

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
	$update_array['uid']=$data['uid'];
	$update_array['mrid']=$data['mrid'];

                    }
       $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
        }

/*-------FUNCTIONS-END--------*/
/*-------GETTER FUNCTIONS-START--------*/

public function get_mrid(){
    return new MarketReport($this->data['mrid']);

   }
/*-------GETTER FUNCTIONS-END--------*/
}