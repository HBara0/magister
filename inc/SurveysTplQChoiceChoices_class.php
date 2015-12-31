<?php
/*-------Definiton-START--------*/
class SurveysTplQChoiceChoices extends AbstractClass {
        protected $data = array();
        protected $errorcode = 0;
        const PRIMARY_KEY = 'stqccid';
        const TABLE_NAME = 'surveys_templates_questionschoices_choices';
        const SIMPLEQ_ATTRS = '*';
        const UNIQUE_ATTRS = '';
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
 	'stqcid' => $data['stqcid'],
	'choice' => $data['choice'],
	'value' => $data['value'],

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
	$update_array['stqcid']=$data['stqcid'];
	$update_array['choice']=$data['choice'];
	$update_array['value']=$data['value'];

                    }
       $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
        }

/*-------FUNCTIONS-END--------*/

}