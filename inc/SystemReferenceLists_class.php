<?php
/*-------Definiton-START--------*/
class SystemReferenceLists extends AbstractClass {
        protected $data = array();
        protected $errorcode = 0;
        const PRIMARY_KEY = 'srlid';
        const TABLE_NAME = 'system_referencelists';
        const SIMPLEQ_ATTRS = '*';
        const UNIQUE_ATTRS = 'srlid,referenceType';
        const CLASSNAME = __CLASS__;
        const DISPLAY_NAME = 'name';

                    /*-------Definiton-END--------*/
/*-------FUNCTIONS-START--------*/

public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
                }

public function create(array $data) {
        global $db,$core;
        $table_array = array(
 	'name' => $data['name'],
	'referenceType' => $data['referenceType'],
	'selectorType' => $data['selectorType'],

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
	$update_array['name']=$data['name'];
	$update_array['referenceType']=$data['referenceType'];
	$update_array['selectorType']=$data['selectorType'];

                    }
       $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
        }

/*-------FUNCTIONS-END--------*/

}