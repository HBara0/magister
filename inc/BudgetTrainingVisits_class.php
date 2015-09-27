<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetTrainingVisits.php
 * Created:        @tony.assaad    Nov 3, 2014 | 10:08:52 AM
 * Last Update:    @tony.assaad    Nov 3, 2014 | 10:08:52 AM
 */

/**
 * Description of BudgetTrainingVisits
 *
 * @author tony.assaad
 */
class BudgetTrainingVisits extends AbstractClass {
    protected $data = array();
    public $total = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'btvid';
    const TABLE_NAME = 'budgeting_trainingvisits';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = 'btvid,company, date';
    const UNIQUE_ATTRS = 'company,date,event';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $data['date'] = strtotime($data['date']);
            $data['createdOn'] = TIME_NOW;
            $data['createdBy'] = $core->user['uid'];
            $query = $db->insert_query(self::TABLE_NAME, $data);
        }
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $data['date'] = strtotime($data['date']);
            $data['modifiedOn'] = TIME_NOW;
            $data['modifiedBy'] = $core->user['uid'];
            $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        }
    }

//    public function save(array $data = array()) {
//
//        global $core;
//        if(empty($data)) {
//            $data = $this->data;
//        }
//        if(!$this->validate_requiredfields($data)) {
//            $trainingvisit = BudgetTrainingVisits::get_data(array('btvid' => $this->data[self::PRIMARY_KEY]));
//            if(is_object($trainingvisit)) {
//                $trainingvisit->update($data);
//            }
//            else {
//                $trainingvisit = BudgetTrainingVisits::get_data(array('bfbid' => $data['bfbid'], 'btvid' => $data['btvid']));
//                ;
//
//                if(is_object($trainingvisit)) {
//                    $trainingvisit->update($data);
//                }
//                else {
//                    $this->create($data);
//                }
//            }
//        }
//    }

    protected function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('date', 'purpose');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

}