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
    const UNIQUE_ATTRS = 'affid,orderType,orderReference';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core, $log;

        $required_fields = array('affid', 'orderType', 'currency');
        foreach($required_fields as $field) {
            $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
            if(is_empty($data[$field])) {
                $this->errorcode = 2;
                return false;
            }
        }
        $orderrequest_fields = array('affid', 'orderType', 'orderReference', 'inspectionType', 'currency', 'exchangeRateToUSD', 'ReferenceNumber');
        foreach($orderrequest_fields as $orderrequest_field) {
            $orderrequest_array[$orderrequest_field] = $data[$orderrequest_field];
        }
        $orderrequest_array['createdBy'] = $core->user['uid'];
        $orderrequest_array['createdOn'] = TIME_NOW;

        $query = $db->insert_query(self::TABLE_NAME, $orderrequest_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
            if(isset($data['nextnumid']) && !empty($data['nextnumid']['nextnum'])) {
                /* update nextnumber  in the document sequence based on affid and ptid */
                $this->set_documentsequencenumber($data);
            }
            /* update the docuent conf with the next number */
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            $this->save_ordercustomers($data['customeroder']);

            $netmargnparms_obj = new AroNetMarginParameters();
            $data['parmsfornetmargin']['aorid'] = $this->data[self::PRIMARY_KEY];
            $netmargnparms_obj->set($data['parmsfornetmargin']);
            $netmargnparms_obj->save();

            // $data['productline']['parmsfornetmargin'] = $data['parmsfornetmargin'];
            // $this->save_productlines($data['productline']);
            $this->save_productlines($data['productline'], $data['parmsfornetmargin']);

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
        $required_fields = array('affid', 'orderType', 'currency');
        foreach($required_fields as $field) {
            $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
            if(is_empty($data[$field])) {
                $this->errorcode = 2;
                return false;
            }
        }
        $orderrequest_fields = array('affid', 'orderType', 'orderReference', 'inspectionType', 'currency', 'exchangeRateToUSD', 'ReferenceNumber');
        foreach($orderrequest_fields as $orderrequest_field) {
            $orderrequest_array[$orderrequest_field] = $data[$orderrequest_field];
        }
        $orderrequest_array['modifiedBy'] = $core->user['uid'];
        $orderrequest_array['modifiedOn'] = TIME_NOW;
        $query = $db->update_query(self::TABLE_NAME, $orderrequest_array, ''.self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        if($query) {
            $this->errorcode = 0; // need to check error code
            /* update the docuent conf with the next number */
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            $this->save_ordercustomers($data['customeroder']);

            $netmargnparms_obj = new AroNetMarginParameters();
            $data['parmsfornetmargin']['aorid'] = $this->data[self::PRIMARY_KEY];
            $netmargnparms_obj->set($data['parmsfornetmargin']);
            $netmargnparms_obj->save();
            //    $data['productline']['parmsfornetmargin'] = $data['parmsfornetmargin'];
            $this->save_productlines($data['productline'], $data['parmsfornetmargin']);
        }
    }

    private function save_ordercustomers($customersdetails) {
        if(is_array($customersdetails)) {
            foreach($customersdetails as $order) {
                $order['aorid'] = $this->data[self::PRIMARY_KEY];
                $ordercust_obj = new AroOrderCustomers();
                $ordercust_obj->set($order);
                $ordercust_obj->save();
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

    private function save_productlines($arorequestlines, $parmsfornetmargin) {  //$netmarginparms
        global $db;
        if(is_array($arorequestlines)) {
            foreach($arorequestlines as $arorequestline) {
                $arorequestline['aorid'] = $this->data[self::PRIMARY_KEY];
                $arorequestline['exchangeRateToUSD'] = $this->data['exchangeRateToUSD'];
                $arorequestline['parmsfornetmargin'] = $parmsfornetmargin;
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
                    case 3:
                        return;
                }
            }
        }
    }

    public function calculate_netmaginparms($data = array()) {
        $parmsfornetmargin = array('estimatedLocalPayment' => 200, // Add default dates and strtotime()
                'estimatedImtermedPayment' => 100, //
                'estimatedManufacturerPayment' => 100 //
        );

        $where = 'warehouse='.$data['warehouse'].' AND '.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo';
        $warehousepolicy = AroManageWarehousesPolicies::get_data($where);
        $currency = new Currencies($warehousepolicy->currency);
        $uom = new Uom($warehousepolicy->rate_uom);
        $data['warehousingRate'] = $warehousepolicy->rate.'  '.$currency->alphaCode.'/'.$uom->get_displayname().'/'.$warehousepolicy->datePeriod.' Days';
        $data['warehousingPeriod'] = $warehousepolicy->datePeriod;

        $purchasetype = new PurchaseTypes($data['ptid']);
        $data['intermedPeriodOfInterest'] = $data['localPeriodOfInterest'] = 0;
        $data['intermedPeriodOfInterest'] = max($parmsfornetmargin['estimatedImtermedPayment'] - $parmsfornetmargin['estimatedManufacturerPayment'], 0);
        $data['localPeriodOfInterest'] = max($parmsfornetmargin['estimatedLocalPayment'] - $parmsfornetmargin['estimatedManufacturerPayment'], 0);
        if($purchasetype->isPurchasedByEndUser == 1) {
            $data['localPeriodOfInterest'] = max($parmsfornetmargin['estimatedLocalPayment'] - $parmsfornetmargin['estimatedImtermedPayment'], 0);
        }

        return $data;
    }

}