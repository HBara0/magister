<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroPartiesInformation_class.php
 * Created:        @rasha.aboushakra    Mar 10, 2015 | 9:43:04 AM
 * Last Update:    @rasha.aboushakra    Mar 10, 2015 | 9:43:04 AM
 */

class AroRequestsPartiesInformation extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'apiid';
    const TABLE_NAME = 'aro_requests_partiesinformation';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'aorid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $log;
        $partiesinfo_data = $this->calculate_partiesinfofields($data);
        if(is_array($partiesinfo_data)) {
            $fields = array('intermedPTIsThroughBank', 'vendorPTIsThroughBank', 'forwarder', 'forwarderPT', 'shipmentPort', 'isConsolidation', 'consolidationWarehouse');
            foreach($fields as $field) {
                $partiesinfo_data[$field] = $data[$field];
            }
            $query = $db->insert_query(self::TABLE_NAME, $partiesinfo_data);
            if($query) {
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            }
        }
    }

    protected function update(array $data) {
        global $db, $log, $core;
        $partiesinfo_data = $this->calculate_partiesinfofields($data);
        if(is_array($partiesinfo_data)) {
            $fields = array('intermedPTIsThroughBank', 'vendorPTIsThroughBank', 'forwarder', 'forwarderPT', 'shipmentPort', 'isConsolidation', 'consolidationWarehouse');
            foreach($fields as $field) {
                $partiesinfo_data[$field] = $data[$field];
            }
            $query = $db->update_query(self::TABLE_NAME, $partiesinfo_data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
            if($query) {
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            }
        }
    }

    protected function validate_requiredfields(array $data = array()) {
        global $core, $errorhandler;
        if(is_array($data)) {
            $required_fields = array('estDateOfShipment', 'shipmentCountry', 'originCountry', 'vendorIncoterms', 'vendorIncotermsDesc', 'vendorPaymentTerm', 'vendorPaymentTermDesc', 'commission');
            $purchtype = new PurchaseTypes($core->input['cpurchasetype']);
            if($purchtype->needsIntermediary == 1) {
                $additionalfields = array('vendorEid', 'intermedAff', 'intermedIncoterms', 'intermedIncotermsDesc', 'intermedPaymentTerm', 'intermedPaymentTermDesc');
            }
            else if($data['vendorIsAff'] == 0 && $purchtype->needsIntermediary != 0) {
                $additionalfields = array('vendorEid', 'intermedAff', 'intermedIncoterms', 'intermedIncotermsDesc', 'intermedPaymentTerm', 'intermedPaymentTermDesc');
            }
            else if($data['vendorIsAff'] == 1) {
                $additionalfields = array('vendorAff');
            }
            if(is_array($additionalfields) && !empty($additionalfields)) {
                $required_fields = array_merge($required_fields, $additionalfields);
            }
            if(!empty($data['intermedIncoterms']) && !empty($data['vendorIncoterms']) && $data['vendorIncoterms'] != $data['intermedIncoterms']) {
                $required_fields[] = 'freight';
            }
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != 0) {
                    $errorhandler->record('Required fields', $field);
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

    private function calculate_partiesinfofields(array $data = array()) {
        if(!$this->validate_requiredfields($data)) {
            $fields = array('aorid', 'transitTime', 'clearanceTime', 'shipmentCountry', 'originCountry', 'freight', 'bankFees', 'insurance', 'legalization', 'courier', 'otherFees',
                    'vendorEid', 'vendorIsAff', 'vendorAff', 'vendorIncoterms', 'vendorIncotermsDesc', 'vendorPaymentTerm', 'vendorPaymentTermDesc', 'commission', 'totalDiscount');
            foreach($fields as $field) {
                $partiesinfo_data[$field] = $data[$field];
            }
            $partiesinfo_data['estDateOfShipment'] = strtotime($data['estDateOfShipment']);
            $partiesinfo_data['vendorEstDateOfPayment'] = $this->get_vendordates($data);
            if($data['vendorIsAff'] !== 1) {
                $additionalfields = array('intermedAff', 'intermedIncoterms', 'intermedIncotermsDesc', 'intermedPaymentTerm', 'intermedPaymentTermDesc', 'ptAcceptableMargin');
                foreach($additionalfields as $field) {
                    $partiesinfo_data[$field] = $data[$field];
                }
                $dates = $this->get_intermediarydates($data);
                $partiesinfo_data['intermedEstDateOfPayment'] = $dates['intermedEstDateOfPayment'];
                $partiesinfo_data['promiseOfPayment'] = $dates['promiseOfPayment'];
                $partiesinfo_data['commFromIntermed'] = $data['commFromIntermed'];
            }
        }
        return $partiesinfo_data;
    }

    public function get_intermediarydates(array $data = array()) {
        if(empty($data)) {
            $data = $this->data;
        }
        $partiesinfo_data['estDateOfShipment'] = strtotime($data['estDateOfShipment']);
        $intermed_paymentterm = PaymentTerms::get_data(array('ptid' => $data['intermedPaymentTerm']));
        if(is_object($intermed_paymentterm)) {
            $intermediarydates['intermedEstDateOfPayment'] = strtotime('+'.$intermed_paymentterm->overduePaymentDays.' days', $partiesinfo_data['estDateOfShipment']);
        }
        if(!empty($intermediarydates['intermedEstDateOfPayment'])) {
            $intermediarydates['promiseOfPayment'] = strtotime('+'.$data['ptAcceptableMargin'].' days', $intermediarydates['intermedEstDateOfPayment']);
        }
        return $intermediarydates;
    }

    public function get_vendordates(array $data = array()) {
        $partiesinfo_data['estDateOfShipment'] = strtotime($data['estDateOfShipment']);
        $vendor_paymentterm = PaymentTerms::get_data(array('ptid' => $data['vendorPaymentTerm']));
        if(is_object($vendor_paymentterm)) {
            $partiesinfo_data['vendorEstDateOfPayment'] = strtotime('+'.$vendor_paymentterm->overduePaymentDays.' days', $partiesinfo_data['estDateOfShipment']);
        }
        //  $partiesinfo_data['vendorEstDateOfPayment_formatted'] = date('d-m-Y', $partiesinfo_data['vendorEstDateOfPayment']);
        return $partiesinfo_data['vendorEstDateOfPayment'];
    }

}