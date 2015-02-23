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

        $required_fields = array('affid', 'orderType', 'currency'); //warehsuoe
        foreach($required_fields as $field) {
            $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
            if(is_empty($data[$field])) {
                $this->errorcode = 2;
                return false;
            }
        }


        //
        $orderrequest_array = array('affid' => $data['affid'],
                'orderType' => $data['orderType'],
                'orderReference' => $data['orderReference'],
                'inspectionType' => $data['inspectionType'],
                'currency' => $data['currency'],
                'exchangeRateToUSD' => $data['exchangeRateToUSD'],
                'ReferenceNumber' => $data['ReferenceNumber'],
                'createdBy' => $core->user['uid'],
                'createdOn' => TIME_NOW,
        );
        $query = $db->insert_query(self::TABLE_NAME, $orderrequest_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
            if(isset($data['nextnumid']) && !empty($data['nextnumid']['nextnum'])) {
                /* update nextnumber  in the document sequence based on affid and ptid */
                $this->set_documentsequencenumber($data);
            }
            /* update the docuent conf with the next number */
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);

            //$netmarginparms=$data['netmarginparms];
            //$this->save_productlines($data['productline'],$netmarginparms);

            $this->save_productlines($data['productline']);
            $this->save_ordercustomers($data['customeroder']);
            $this->errorcode = 0;
        }
    }

    private function set_documentsequencenumber($doc_sequencedata) {
        $documentseq_obj = AroDocumentsSequenceConf::get_data(array('affid' => $doc_sequencedata['affid'], 'ptid' => $doc_sequencedata['orderType']), array('returnarray' => false, 'simple' => false, 'operators' => array('affid' => 'in', 'ptid' => 'in')));
        if(is_object($documentseq_obj)) {
            $nextNumber = $doc_sequencedata['nextnumid']['nextnum'];
            $documentseq_obj->set(array('nextNumber' => $nextNumber));
            $documentseq_obj->save();
        }
    }

    protected function update(array $data) {
        global $db, $core, $log;
        $required_fields = array('affid', 'orderType', 'currency'); //warehsuoe
        foreach($required_fields as $field) {
            $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
            if(is_empty($data[$field])) {
                $this->errorcode = 2;
                return false;
            }
        }
        $orderrequest_array = array('affid' => $data['affid'],
                'orderType' => $data['orderType'],
                'orderReference' => $data['orderReference'],
                'inspectionType' => $data['inspectionType'],
                'currency' => $data['currency'],
                'exchangeRateToUSD' => $data['exchangeRateToUSD'],
                'ReferenceNumber' => $data['ReferenceNumber'],
                'modifiedBy' => $core->user['uid'],
                'modifiedOn' => TIME_NOW,
        );
        $query = $db->update_query(self::TABLE_NAME, $orderrequest_array, ''.self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
            /* update the docuent conf with the next number */
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            $this->save_productlines($data['productline']);
            $this->save_ordercustomers($data['customeroder']);
            $this->errorcode = 0;
        }
    }

    private function save_ordercustomers($customersdetails) {
        foreach($customersdetails as $cusomeroder) {
            foreach($cusomeroder as $order) {
                $order[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
                // if(isset($order['cid']) && !empty($order['cid'])) {
                $ordercust_obj = new AroOrderCustomers();
                $ordercust_obj->set($order);
                $ordercust_obj->save();
                // }
                $this->errorcode = $ordercust_obj->errorcode;
                switch($this->get_errorcode()) {
                    case 0:
                        continue;
                    case 2:
                        return;
                }
            }
        }
    }

    private function save_productlines($arorequestlines) {  //$netmarginparms
        global $db;
        if(is_array($arorequestlines)) {
            foreach($arorequestlines as $arorequestline) {
                $arorequestline['aorid'] = $this->data[self::PRIMARY_KEY];
                $arorequestline['exchangeRateToUSD'] = $this->data['exchangeRateToUSD'];
                //$arorequestline['parmsfornetmargin']=$netmarginparms;
                if(isset($arorequestline['todelete']) && !empty($arorequestline['todelete'])) {
                    $requestline = AroRequestLines::get_data(array('inputChecksum' => $arorequestline['inputChecksum']));
                    if(is_object($requestline)) {
                        $db->delete_query('aro_requests_lines', 'arlid='.$requestline->arlid.'');
                    }
                    continue;
                }
                $requestline = new AroRequestLines();
                $requestline->set($arorequestline);
                $requestline->save();
                $this->errorcode = $requestline->errorcode;
                switch($this->get_errorcode()) {
                    case 0:
                        continue;
                    case 2:
                        return;
                }
            }
        }
    }

}