<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroOrderCustomers_class.php
 * Created:        @tony.assaad    Feb 13, 2015 | 2:23:37 PM
 * Last Update:    @tony.assaad    Feb 13, 2015 | 2:23:37 PM
 */

/**
 * Description of AroOrderCustomers_class
 *
 * @author tony.assaad
 */
class AroOrderCustomers extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'aocid';
    const TABLE_NAME = 'aro_order_customers';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'aocid,ptid';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'cid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function update(array $data) {
        global $db, $core, $log;
        if(!$this->validate_requiredfields($data)) {

            $query = $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
            if($query) {
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            }
        }
    }

    public function create(array $data) {
        global $db, $core, $log;
        $required_fields = array('paymentTermBaseDate', 'cid'); //warehsuoe
        foreach($required_fields as $field) {
            $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
            if(is_empty($data[$field])) {
                $this->errorcode = 2;
                return false;
            }
        }
        if(isset($data['altcid']) && !empty($data['altcid'])) {
            $data['altcid'] = $data['altcid'];
            $data['cid'] = 0;
        }

        $data['paymentTermBaseDate'] = strtotime($data['paymentTermBaseDate']);
        $policies_array = array('cid' => $data['cid'],
                'ptid' => $data['ptid'],
                'inputChecksum' => $data['ptid'],
                'aorid' => $data['aorid'],
                'altCid' => $data['altcid'],
                'paymentTermDesc' => $data['paymentTermDesc'],
                'paymentTermBaseDate' => $data['paymentTermBaseDate'],
                'createdBy' => $core->user['uid'],
                'createdOn' => TIME_NOW,
        );
        $query = $db->insert_query(self::TABLE_NAME, $policies_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            $this->errorcode = 0;
        }
    }

    public static function get_average($arraydates) {

        foreach($arraydates as $key) {

            $newTimeAdd = new DateTime($key["timeAdded"]);
            $newTimeRead = new DateTime($key["timeRead"]);
            $interval = $newTimeAdd->diff($newTimeRead);
            $intervals[] = $interval->days; //get days
        }
        if(!empty($intervals)) {
            return array_sum($intervals / count($intervals));
        }
    }

}