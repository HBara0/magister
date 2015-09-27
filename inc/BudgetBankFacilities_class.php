<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetBankFacilities_class.php
 * Created:        @rasha.aboushakra    Nov 5, 2014 | 2:34:52 PM
 * Last Update:    @rasha.aboushakra    Nov 5, 2014 | 2:34:52 PM
 */

class BudgetBankFacilities extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'bbfid';
    const TABLE_NAME = 'budgeting_bankfacilities';
    const DISPLAY_NAME = '';
    const UNIQUE_ATTRS = 'bnkid,bfbid';
    const SIMPLEQ_ATTRS = 'bbfid,inputChecksum, bnkid, bfbid, hasFacilities, overDraft, loan, forexForward, billsDiscount, othersGuarantees';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(!$this->validate_requiredfields($data)) {
            if(is_array($data)) {
                $fields = array('bnkid', 'inputChecksum', 'bfbid', 'facilityCurrency', 'interestRate', 'premiumCommission', 'totalAmount', 'endquarterAmount', 'comfortLetter', 'LastIssuanceDate', 'LastRenewalDate');
                $facilitiesfields = array('overDraft', 'loan', 'forexForward', 'billsDiscount', 'othersGuarantees');
                foreach($facilitiesfields as $facilityfield) {
                    $facilities_total +=$data[$facilityfield];
                }
                if($facilities_total > 0) {
                    $fields = array_merge($fields, $facilitiesfields);
                    $banks_data['hasFacilities'] = 1;
                }
                foreach($fields as $field) {
                    $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                    $data[$field] = $db->escape_string($data[$field]);
                    $banks_data[$field] = $data[$field];
                }
                $banks_data['LastIssuanceDate'] = strtotime($banks_data['LastIssuanceDate']);
                $banks_data['LastRenewalDate'] = strtotime($banks_data['LastRenewalDate']);
                $banks_data['createdOn'] = TIME_NOW;
                $banks_data['createdBy'] = $core->user['uid'];
                $query = $db->insert_query(self::TABLE_NAME, $banks_data);
            }
        }
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $fields = array('bnkid', 'bfbid', 'facilityCurrency', 'interestRate', 'premiumCommission', 'totalAmount', 'endquarterAmount', 'comfortLetter', 'LastIssuanceDate', 'LastRenewalDate');
            $facilitiesfields = array('overDraft', 'loan', 'forexForward', 'billsDiscount', 'othersGuarantees');
            foreach($facilitiesfields as $facilityfield) {
                $facilities_total +=$data[$facilityfield];
            }
            if($facilities_total > 0) {
                $fields = array_merge($fields, $facilitiesfields);
                $banks_data['hasFacilities'] = 1;
            }
            foreach($fields as $field) {
                $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                $data[$field] = $db->escape_string($data[$field]);
                $banks_data[$field] = $data[$field];
            }
            $banks_data['LastIssuanceDate'] = strtotime($banks_data['LastIssuanceDate']);
            $banks_data['LastRenewalDate'] = strtotime($banks_data['LastRenewalDate']);
            $banks_data['modifiedOn'] = TIME_NOW;
            $banks_data['modifiedBy'] = $core->user['uid'];
            $db->update_query(self::TABLE_NAME, $banks_data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
        }
    }

//    public function save(array $data = array()) {
//        if(empty($data)) {
//            $data = $this->data;
//        }
//        if(!$this->validate_requiredfields($data)) {
//            $bankfacilities = self::get_data(array('bbfid' => $this->data[self::PRIMARY_KEY]));
//            if(is_object($bankfacilities)) {
//                $bankfacilities->update($data);
//            }
//            else {
//                $bankfacilities = self::get_data(array('bnkid' => $data['bnkid'], 'bfbid' => $data['bfbid']));
//                if(is_object($bankfacilities)) {
//                    $bankfacilities->update($data);
//                }
//                else {
//                    $this->create($data);
//                }
//            }
//        }
//    }

    protected function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('bnkid', 'interestRate', 'premiumCommission', 'totalAmount', 'endquarterAmount', 'comfortLetter');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

    public function delete_bankfacility() {
        global $db, $core;
        if(empty($data)) {
            $data = $this->data;
        }
        if(isset($data[self::PRIMARY_KEY]) && !empty($data[self::PRIMARY_KEY])) {
            $bank = self::get_data(array('bbfid' => $data[self::PRIMARY_KEY]));
            $bank->delete();
        }
        if(isset($data['inputChecksum']) && !empty($data['inputChecksum'])) {
            $bank = self::get_data(array('inputChecksum' => $data['inputChecksum']));
            if(is_object($bank)) {
                $bank->delete();
            }
        }
    }

}
?>