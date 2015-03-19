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
class AroRequests extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'aorid';
    const TABLE_NAME = 'aro_requests';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'aorid,affid,orderType';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'affid,orderType,orderReference';

    public function __construct($id = null, $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core, $log;

        $required_fields = array('affid', 'orderType', 'currency', 'orderReference');
        foreach($required_fields as $field) {
            $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
            if(is_empty($data[$field])) {
                $this->errorcode = 2;
                return $this->errorcode;
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
            if($this->errorcode != 0) {
                return $this->errorcode;
            }

            //Save parties Information data
            $partiesinformation_obj = new AroRequestsPartiesInformation();
            $data['partiesinfo']['aorid'] = $this->data[self::PRIMARY_KEY];
            $partiesinformation_obj->set($data['partiesinfo']);
            $partiesinformation_obj->save();
            $this->errorcode = $partiesinformation_obj->errorcode;
            if($this->errorcode != 0) {
                return $this->errorcode;
            }

            $netmargnparms_obj = new AroNetMarginParameters();
            $data['parmsfornetmargin']['aorid'] = $this->data[self::PRIMARY_KEY];
            $netmargnparms_obj->set($data['parmsfornetmargin']);
            $netmargnparms_obj->save();
            $this->errorcode = $netmargnparms_obj->get_errorcode();
            if($this->errorcode != 0) {
                return $this->errorcode;
            }

            $data['parmsfornetmargin']['fees'] = $data['partiesinfo']['totalfees'];
            $data['parmsfornetmargin']['commission'] = $data['partiesinfo']['commission'];

            $this->validate_productlines($data['productline'], $data['parmsfornetmargin']);
            if($this->errorcode != 0) {
                return $this->errorcode;
            }
            $this->save_productlines($data['productline'], $data['parmsfornetmargin']);

            $this->save_linessupervision($data['actualpurchase'], $data['partiesinfo']['transitTime'], $data['partiesinfo']['clearanceTime'], $data['partiesinfo']['estDateOfShipment']);
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
        $required_fields = array('affid', 'orderType', 'currency', 'orderReference');
        foreach($required_fields as $field) {
            $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
            if(is_empty($data[$field])) {
                $this->errorcode = 2;
                return $this->errorcode;
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
            /* update the document conf with the next number */
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            $this->save_ordercustomers($data['customeroder']);
            if($this->errorcode != 0) {
                return $this->errorcode;
            }

            //Save parties Information data
            $partiesinformation_obj = new AroRequestsPartiesInformation();
            $data['partiesinfo']['aorid'] = $this->data[self::PRIMARY_KEY];
            $partiesinformation_obj->set($data['partiesinfo']);
            $partiesinformation_obj->save();
            $this->errorcode = $partiesinformation_obj->errorcode;
            if($this->errorcode != 0) {
                return $this->errorcode;
            }


            $netmargnparms_obj = new AroNetMarginParameters();
            $data['parmsfornetmargin']['aorid'] = $this->data[self::PRIMARY_KEY];
            $netmargnparms_obj->set($data['parmsfornetmargin']);
            $netmargnparms_obj->save();
            $this->errorcode = $netmargnparms_obj->get_errorcode();
            if($netmargnparms_obj->get_errorcode() != 0) {
                return $this->errorcode;
            }

            $data['parmsfornetmargin']['fees'] = $data['partiesinfo']['totalfees'];
            $data['parmsfornetmargin']['commission'] = $data['partiesinfo']['commission'];

            $this->validate_productlines($data['productline'], $data['parmsfornetmargin']);
            $x = $this->errorcode;
            if($this->errorcode != 0) {
                return $this->errorcode;
            }
            $this->save_productlines($data['productline'], $data['parmsfornetmargin']);
            $this->save_linessupervision($data['actualpurchase'], $data['partiesinfo']['transitTime'], $data['partiesinfo']['clearanceTime'], $data['partiesinfo']['estDateOfShipment']);
        }
    }

    private function save_ordercustomers($customersdetails) {
        global $db;
        if(is_array($customersdetails)) {
            foreach($customersdetails as $order) {
                if(empty($order['cid']) && empty($order['ptid'])) {
                    continue;
                }
                $order['aorid'] = $this->data[self::PRIMARY_KEY];
                if(isset($order['todelete']) && !empty($order['todelete'])) {
                    $ordercustomer = AroOrderCustomers::get_data(array('inputChecksum' => $order['inputChecksum']));
                    if(is_object($ordercustomer)) {
                        $db->delete_query('aro_order_customers', 'aocid='.$ordercustomer->aocid.'');
                    }
                    continue;
                }
                $ordercust_obj = new AroOrderCustomers();
                $ordercust_obj->save($order);
                $this->errorcode = $ordercust_obj->get_errorcode();
                switch($this->get_errorcode()) {
                    case 0:
                        continue;
                    case 2:
                        return;
                }
            }
        }
    }

    //loop through product line for validation
    private function validate_productlines($arorequestlines, $parmsfornetmargin) {
        $plrowid = 0;
        if(is_array($arorequestlines)) {
            foreach($arorequestlines as $arorequestline) {
                $plrowid++;
                $arorequestline['aorid'] = $this->data[self::PRIMARY_KEY];
                $arorequestline['exchangeRateToUSD'] = $this->data['exchangeRateToUSD'];
                $arorequestline['parmsfornetmargin'] = $parmsfornetmargin;
                $requestline = new AroRequestLines();
                $requestline->set($arorequestline);
                $requestline->validate_requiredfields();
                $this->errorcode = $requestline->errorcode;
                switch($this->get_errorcode()) {
                    case 0:
                        continue;
                    case 2:
                        return;
                    case 3:
                        $this->errorid = $plrowid;
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
                    $actualpurchaseline = AroRequestLinesSupervision::get_data(array('inputChecksum' => $arorequestline['inputChecksum']));
                    if(is_object($actualpurchaseline)) {
                        $db->delete_query('aro_requests_linessupervision', 'arlsid='.$actualpurchaseline->arlsid.'');
                    }
                    continue;
                }
                $requestline = new AroRequestLines();
                $requestline->set($arorequestline);
                $requestline->save();
            }
        }
    }

    private function save_linessupervision($linessupervision, $transittime, $clearancetime, $dateOfStockEntry) {
        if(is_array($linessupervision)) {
            foreach($linessupervision as $linesupervision) {
                $linesupervision['aorid'] = $this->data[self::PRIMARY_KEY];
                $linesupervision['transitTime'] = $transittime;
                $linesupervision['clearanceTime'] = $clearancetime;
                $linesupervision['dateOfStockEntry'] = $dateOfStockEntry;
                $requestlinesupervision = new AroRequestLinesSupervision();
                $requestlinesupervision->set($linesupervision);
                $requestlinesupervision->save();
                $this->errorcode = $requestlinesupervision->errorcode;
                switch($this->get_errorcode()) {
                    case 0:
                        continue;
                    case 2:
                        return;
                }
            }
        }
    }

    public function calculate_netmaginparms($data = array()) {
        $where = 'warehouse='.$data['warehouse'].' AND ('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';
        $warehousepolicy = AroManageWarehousesPolicies::get_data($where);
        $currency = new Currencies($warehousepolicy->currency);
        $uom = new Uom($warehousepolicy->rate_uom);
        if(is_object($warehousepolicy)) {
            $data['warehousingRate'] = $warehousepolicy->rate.'  '.$currency->alphaCode.'/'.$uom->get_displayname().'/'.$warehousepolicy->datePeriod.' Days';
            $data['warehousingPeriod'] = $warehousepolicy->datePeriod;
        }
        if(!is_object($warehousepolicy)) {
            output($lang->nopolicy);
            exit;
        }
        return $data;
    }

    public function get_errorid() {
        return $this->errorid;
    }

    public function generate_approvalchain() {
        global $core;
        $filter = 'affid = '.$this->affid.' AND purchaseType = '.$this->orderType.' AND ('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';
        $aroapprovalchain_policies = AroApprovalChainPolicies::get_data($filter);
        if(is_object($aroapprovalchain_policies)) {
            $approvalchain = unserialize($aroapprovalchain_policies->approvalChain);
        }
        $affiliate = new Affiliate($this->affid);
        if(is_array($approvalchain)) {
            foreach($approvalchain as $key => $val) {
                switch($val) {
                    case 'businessManager':
                        // Aro request businessManager
                        $approvers['generalManager'] = $this->businessManager;
                        break;
                    case 'lolm':
                        $approvers['lolm'] = $affiliate->get_logisticsmanager()->uid;
                        break;
                    case 'lfinancialManager':
                        $approvers['lfinancialManager'] = $affiliate->get_financialemanager()->uid;
                        break;
                    case 'generalManager':
                        $approvers['generalManager'] = $affiliate->get_generalmanager()->uid;
                        break;
                    case 'gfinancialManager':
                        $aropartiesinfo = AroRequestsPartiesInformation::get_data(array('aorid' => $this->data[self::PRIMARY_KEY]));
                        $intermediaryAff = new Affiliates($aropartiesinfo->intermedAff);
                        $approvers['gfinancialManager'] = $intermediaryAff->get_financialemanager()->uid;
                        break;
                    case 'cfo':
                        $position = Positions::get_data(array('name' => 'cfo'));
                        $userposition = UsersPositions::get_data(array('posid' => $position->posid));
                        $approvers['cfo'] = $userposition->uid;
                        break;
                    case 'user':
                        break;
                    default:
                        if(is_int($val)) {
                            $approvers[$val] = $val;
                        }
                        break;
                }
            }
            /* Make list of approvers unique */
            $approvers = array_unique($approvers);

            /* Remove the user himself from the approval chain */
            unset($approvers[array_search($core->user['uid'], $approvers)]);

            return $approvers;
        }
        return null;
    }

    public function create_approvalchain($approvers = null) {
        global $core;

        if(empty($approvers)) {
            $approvers = $this->generate_approvalchain();
        }
        //  $approve_immediately = $this->should_approveimmediately();
        foreach($approvers as $key => $val) {
            $approve_status = $timeapproved = 0;
            if($val == $core->user['uid'] && $approve_immediately == true) {
                $approve_status = 1;
                $timeapproved = TIME_NOW;
            }
            $sequence = 1;
            if(is_array($approvers)) {
                $sequence = array_search($key, $approvers);
            }
            $approver = new AroRequestsApprovals();
            $approver->set(array('aorid' => $this->aorid, 'uid' => $val, 'isApproved' => $approve_status, 'timeApproved' => $timeapproved, 'sequence' => $sequence));
            $approver->save();
        }
        return true;
    }

}