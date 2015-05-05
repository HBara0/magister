<?php
/*-------Definiton-START--------*/
class SystemTablesColumns extends AbstractClass {
        protected $data = array();
        protected $errorcode = 0;
        const PRIMARY_KEY = 'stcid';
        const TABLE_NAME = 'system_tables_columns';
        const SIMPLEQ_ATTRS = '*';
        const UNIQUE_ATTRS = 'stcid';
        const CLASSNAME = __CLASS__;
        const DISPLAY_NAME = 'columnTitle';

                    /*-------Definiton-END--------*/
/*-------FUNCTIONS-START--------*/

public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
                }

public function create(array $data) {
        global $db,$core;
        $table_array = array(
 	'relatedTo' => $data['relatedTo'],
	'columnDbName' => $data['columnDbName'],
	'columnSystemName' => $data['columnSystemName'],
	'columnTitle' => $data['columnTitle'],
	'stid' => $data['stid'],
	'columnDefault' => $data['columnDefault'],
	'isNull' => $data['isNull'],
	'dataType' => $data['dataType'],
	'length' => $data['length'],
	'extra' => $data['extra'],
	'isPrimaryKey' => $data['isPrimaryKey'],
	'isRequired' => $data['isRequired'],
	'isUnique' => $data['isUnique'],
	'isSimple' => $data['isSimple'],
	'isDisplayName' => $data['isDisplayName'],

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
	$update_array['relatedTo']=$data['relatedTo'];
	$update_array['columnDbName']=$data['columnDbName'];
	$update_array['columnSystemName']=$data['columnSystemName'];
	$update_array['columnTitle']=$data['columnTitle'];
	$update_array['stid']=$data['stid'];
	$update_array['columnDefault']=$data['columnDefault'];
	$update_array['isNull']=$data['isNull'];
	$update_array['dataType']=$data['dataType'];
	$update_array['length']=$data['length'];
	$update_array['extra']=$data['extra'];
	$update_array['isPrimaryKey']=$data['isPrimaryKey'];
	$update_array['isRequired']=$data['isRequired'];
	$update_array['isUnique']=$data['isUnique'];
	$update_array['isSimple']=$data['isSimple'];
	$update_array['isDisplayName']=$data['isDisplayName'];

                    }
       $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
        }

/*-------FUNCTIONS-END--------*/
/*-------GETTER FUNCTIONS-START--------*/

public function get_stid(){
    return new SystemTabless($this->data['stid']);

   }
/*-------GETTER FUNCTIONS-END--------*/
}