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
class AroOrderRequest extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'aorid';
    const TABLE_NAME = 'aro_order_requests';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'aorid,affid,orderType';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'affid,orderType';

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
        if(isset($data[nextnumid]) && !empty($data[nextnumid])) {
            print_R($data[nextnumid]);
            exit;
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

            /* update the docuent conf with the next number */

            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            $this->errorcode = 0;
        }
    }

    protected function update(array $data) {
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
        $query = $db->update_query(self::TABLE_NAME, $policies_array, ''.self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        if($query) {
            if(isset($data['nextnumid']) && !empty($data['nextnumid']['nextnum'])) {
                /* update nextnumber  in the document sequence based on affid and ptid */
                $documentseq_obj = AroDocumentsSequenceConf::get_data(array('affid' => $data['affid'], 'ptid' => $data['orderType']), array('returnarray' => false, 'simple' => false, 'operators' => array('affid' => 'in', 'ptid' => 'in')));
                if(is_object($documentseq_obj)) {
                    $nextNumber = $data['nextnumid']['nextnum'];
                    $documentseq_obj->set(array('nextNumber' => $nextNumber));
                    $documentseq_obj->save();
                }
            }

            $this->data[self::PRIMARY_KEY] = $db->last_id();
            /* update the docuent conf with the next number */
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            $this->errorcode = 0;
        }
    }

}