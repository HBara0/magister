<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroOrderIdentification_class.php
 * Created:        @tony.assaad    Feb 11, 2015 | 11:40:28 AM
 * Last Update:    @tony.assaad    Feb 11, 2015 | 11:40:28 AM
 */

/**
 * Description of AroOrderIdentification_class
 *
 * @author tony.assaad
 */
class AroOrderIdentification extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'aoiid';
    const TABLE_NAME = 'aro_order_indentification';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'aoiid,affid,orderType,orderReference';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'affid,orderType,orderReference';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core, $log;
        $required_fields = array('affid', 'orderType'); //warehsuoe
        foreach($required_fields as $field) {
            $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
            if(is_empty($data[$field])) {
                $this->errorcode = 2;
                return false;
            }
        }

        $policies_array = array('affid' => $data['affid'],
                'orderType' => $data['orderType'],
                'orderReference' => $data['orderReference'],
                'inspectionType' => $data['inspectionType'],
                'currency' => $data['currency'],
                'exchangeRateToUSD' => $data['exchangeRateToUSD'],
                'ReferenceNumber' => $data['ReferenceNumber'],
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

    protected function update(array $data) {
        ;
    }

}