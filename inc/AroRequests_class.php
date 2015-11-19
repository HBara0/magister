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
    const SIMPLEQ_ATTRS = 'aorid,affid,orderType,identifier,isApproved,aroBusinessManager,revision';
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
        $orderrequest_fields = array('affid', 'orderType', 'orderReference', 'inspectionType', 'currency', 'exchangeRateToUSD', 'ReferenceNumber', 'aroBusinessManager', 'isFinalized');
        foreach($orderrequest_fields as $orderrequest_field) {
            $orderrequest_array[$orderrequest_field] = $data[$orderrequest_field];
        }
        $orderrequest_array['createdBy'] = $core->user['uid'];
        $orderrequest_array['createdOn'] = TIME_NOW;
        $orderrequest_array['identifier'] = substr(md5(uniqid(microtime())), 1, 10);
        $query = $db->insert_query(self::TABLE_NAME, $orderrequest_array);
        if($query) {
            $this->data['identifier'] = $orderrequest_array['identifier'];
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

            // $data['parmsfornetmargin']['fees'] = $data['partiesinfo']['totalfees'];
            $data['parmsfornetmargin']['commission'] = $data['partiesinfo']['commission'];


            $parms = $this->validate_productlines($data['productline'], $data['parmsfornetmargin']);
            if($this->errorcode != 0) {
                return $this->errorcode;
            }
            $data['parmsfornetmargin']['unitfees'] = $parms['unitfees'];
            $totalQtyperuom = $parms['totalQty'];
            //save product lines and return array of product segments involved
            $arosegments = $this->save_productlines($data['productline'], $data['parmsfornetmargin'], $totalQtyperuom);
            $this->save_linessupervision($data['actualpurchase'], $data['partiesinfo']['transitTime'], $data['partiesinfo']['clearanceTime'], $data['partiesinfo']['estDateOfShipment']);
            $this->save_currentstocklines($data['currentstock']);

            $fundsengaged_obj = new AroRequestsFundsEngaged();
            $data['totalfunds']['aorid'] = $this->data[self::PRIMARY_KEY];
            $fundsengaged_obj->set($data['totalfunds']);
            $fundsengaged_obj->save();

            $ordesummary_obj = new AroOrderSummary();
            $data['ordersummary']['aorid'] = $this->data[self::PRIMARY_KEY];
            if(is_array($data['ordersummary'])) {
                $ordesummary_obj->set($data['ordersummary']);
                $ordesummary_obj->save();
            }

            $data['approvalchain']['aroBusinessManager'] = $orderrequest_array['aroBusinessManager'];
            $this->create_approvalchain(null, $data['approvalchain']);
            //$sendemail_to['approvers'] = $this->generate_approvalchain();
            if($data['isFinalized'] == 1) {
                $this->send_approvalemail();
            }
            ///////////////////////////////
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
        $orderrequest_fields = array('affid', 'orderType', 'orderReference', 'inspectionType', 'currency', 'exchangeRateToUSD', 'ReferenceNumber', 'aroBusinessManager', 'isFinalized');
        foreach($orderrequest_fields as $orderrequest_field) {
            $orderrequest_array[$orderrequest_field] = $data[$orderrequest_field];
        }
        $orderrequest_array['avgLocalInvoiceDueDate'] = strtotime($data['avgeliduedate']);
        $orderrequest_array['modifiedBy'] = $core->user['uid'];
        $orderrequest_array['modifiedOn'] = TIME_NOW;
        if($data['isFinalized']) {
            $orderrequest_array['revision'] = $this->data['revision'] + 1;
        }
        $query = $db->update_query(self::TABLE_NAME, $orderrequest_array, ''.self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        if($query) {
            /* update the document conf with the next number */
            $arorequest = self::get_data(array('aorid' => $this->data[self::PRIMARY_KEY]));
            $this->data['identifier'] = $arorequest->identifier;
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

            //$data['parmsfornetmargin']['fees'] = $data['partiesinfo']['totalfees'];
            $data['parmsfornetmargin']['commission'] = $data['partiesinfo']['commission'];

            $parms = $this->validate_productlines($data['productline'], $data['parmsfornetmargin']);
            if($this->errorcode != 0) {
                return $this->errorcode;
            }
            $data['parmsfornetmargin']['unitfees'] = $parms['unitfees'];
            $totalQtyperuom = $parms['totalQty'];
            //save product lines and return array of product segments involved
            $arosegments = $this->save_productlines($data['productline'], $data['parmsfornetmargin'], $totalQtyperuom);
            $this->save_linessupervision($data['actualpurchase'], $data['partiesinfo']['transitTime'], $data['partiesinfo']['clearanceTime'], $data['partiesinfo']['estDateOfShipment']);
            $this->save_currentstocklines($data['currentstock']);


            $fundsengaged_obj = new AroRequestsFundsEngaged();
            $data['totalfunds']['aorid'] = $this->data[self::PRIMARY_KEY];
            $fundsengaged_obj->set($data['totalfunds']);
            $fundsengaged_obj->save();


            $ordesummary_obj = new AroOrderSummary();
            $data['ordersummary']['aorid'] = $this->data[self::PRIMARY_KEY];
            $ordesummary_obj->set($data['ordersummary']);
            $ordesummary_obj->save();

            $data['approvalchain']['aroBusinessManager'] = $orderrequest_array['aroBusinessManager'];
            $this->create_approvalchain(null, $data['approvalchain']);
            $approvers_objs = $this->get_approvers();
//            if(is_array($approvers_objs)) {
//                foreach($approvers_objs as $approver) {
//                    $approvers[] = $approver->uid;
//                }
//            }
            if($data['isFinalized'] == 1) {
                $this->send_approvalemail();
            }
            ////////////////////////////////////////////
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
                if(!empty($arorequestline['quantity'])) {
                    $parms['unitfees'] += $arorequestline['fees'] / $arorequestline['quantity'];
                }
                $parms['totalQty'][$arorequestline['uom']] +=$arorequestline['quantity'];
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
            $parms['unitfees'] = $parms['unitfees'] / $plrowid;
            return $parms;
        }
    }

    private function save_productlines($arorequestlines, $parmsfornetmargin, $totalQtyperuom) {  //$netmarginparms
        global $db;
        if(is_array($arorequestlines)) {
            foreach($arorequestlines as $arorequestline) {
                $arorequestline['aorid'] = $this->data[self::PRIMARY_KEY];
                if(empty($arorequestline['psid'])) {
                    $product = new Products($arorequestline['pid']);
                    $arorequestline['psid'] = $product->get_segment()['psid'];
                }
                $arosegments[$arorequestline['psid']] = $arorequestline['psid'];
                $arorequestline['exchangeRateToUSD'] = $this->data['exchangeRateToUSD'];
                $arorequestline['parmsfornetmargin'] = $parmsfornetmargin;
                $arorequestline['parmsfornetmargin']['totalQty'] = $totalQtyperuom[$arorequestline['uom']];
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
            return $arosegments;
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

    private function save_currentstocklines($currentstocklines) {
        if(is_array($currentstocklines)) {
            foreach($currentstocklines as $currentstockline) {
                $currentstockline['aorid'] = $this->data[self::PRIMARY_KEY];
                $currentstocksupervision_obj = new AroRequestsCurStkSupervision();
                $currentstocksupervision_obj->set($currentstockline);
                $currentstocksupervision_obj->save();
                $this->errorcode = $currentstocksupervision_obj->errorcode;
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
        global $lang;
        $where = 'warehouse='.$data['warehouse'].' AND ('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';
        $warehousepolicy = AroManageWarehousesPolicies::get_data($where);
        if(!is_object($warehousepolicy)) {
            return false;
        }
        $currency = new Currencies($warehousepolicy->currency);
        $uom = new Uom($warehousepolicy->rate_uom);
        if(is_object($warehousepolicy)) {
            $data['warehousingRate'] = $warehousepolicy->rate.'  '.$currency->alphaCode.'/'.$uom->get_displayname().'/'.$warehousepolicy->datePeriod.' Days';
            $data['warehousingPeriod'] = $warehousepolicy->datePeriod;
            $data['uom'] = $warehousepolicy->rate_uom;
            if($currency->alphaCode != 'USD') {
                $currencyobj = new Currencies('USD');
                $data['warehouseUsdExchangeRate'] = $currencyobj->get_latest_fxrate($currency->alphaCode, null);
                if(!empty($data['warehouseUsdExchangeRate'])) {
                    $data['warehousingRateUsd'] = ($warehousepolicy->rate * $data['warehouseUsdExchangeRate']).' USD/'.$uom->get_displayname().'/'.$warehousepolicy->datePeriod.' Days';
                }
            }
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

    public function generate_approvalchain($pickedapprovers = null, $options = null) {
        global $core;
        $filter = 'affid ='.$this->affid.' AND purchaseType = '.$this->orderType.' AND ('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';
        $aroapprovalchain_policies = AroApprovalChainPolicies::get_data($filter);
        if(is_object($aroapprovalchain_policies)) {
            $approvalchain = unserialize($aroapprovalchain_policies->approvalChain);
        }
        $localaffpolicy = AroPolicies::get_data($filter);

        $affiliate = new Affiliates($this->affid);
        if(is_array($approvalchain)) {
            foreach($approvalchain as $key => $val) {
                $sortedapprovalchain[$val['sequence']] = $val;
            }
            ksort($sortedapprovalchain); // Sort by sequece ASC
            foreach($sortedapprovalchain as $key => $val) {
                switch($val['approver']) {
                    case 'businessManager':
                        if(!empty($this->data['aroBusinessManager'])) {
                            $approvers['businessManager'] = $this->data['aroBusinessManager'];
                        }
                        else {
                            if(!empty($options['aroBusinessManager'])) {
                                $approvers['businessManager'] = $options['aroBusinessManager'];
                            }
                        }
//                        if(isset($pickedapprovers['businessManager']['uid']) && !empty($pickedapprovers['businessManager']['uid'])) {
//                            $approvers['businessManager'] = $pickedapprovers['businessManager']['uid'];
//                        }
//                        else {
//                            $approvers['businessManager'] = " <input style='padding :5px;' type='text' id='user_1_autocomplete'/>
//                                  <input type='hidden' id='user_1_id' name='approvalchain[businessManager][uid]'  />";
//                        }

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
                        $approvers['gfinancialManager'] = $affiliate->get_globalfinancialemanager()->uid; // $core->settings['gfinancialManager_id']; // 367;
                        break;
                    case 'cfo':
                        $approvers['cfo'] = $affiliate->get_cfo()->uid;
                        break;
                    case 'coo':
                        $approvers['coo'] = $affiliate->get_coo()->uid;
                        break;
                    case 'regionalSupervisor':
                        $approvers['regionalSupervisor'] = $affiliate->get_regionalsupervisor()->uid;
                        break;
                    case 'globalPurchaseManager':
                        $approvers['globalPurchaseManager'] = $affiliate->get_globalpurchasemanager()->uid;
                        break;
                    case 'user':
                        //  $user = new Users($val['uid']);
                        $approvers[$val['approver']] = $val['uid'];
                        break;
                    case 'reportsTo':
                        if(!empty($this->data['aroBusinessManager'])) {
                            $bm = $this->data['aroBusinessManager'];
                        }
                        else {
                            if(!empty($options['aroBusinessManager'])) {
                                $bm = $options['aroBusinessManager'];
                            }
                        }
                        $user = Users::get_data(array('uid' => $bm), array('simple' => false));
                        if(is_object($user) && !empty($user->reportsTo)) {
                            $approvers[$val['approver']] = $user->reportsTo;
                        }
                        unset($bm);
                    default:
                        if(is_int($val)) {
                            $approvers[$val] = $val;
                        }
                        break;
                }
            }
            /* Make list of approvers unique */
            if(is_array($approvers)) {
                $approvers = array_unique($approvers);
            }
            /* Remove the user himself from the approval chain */
            //    unset($approvers[array_search($core->user['uid'], $approvers)]);
            return $approvers;
        }
        return null;
    }

    public function create_approvalchain($approvers = null, $options = null) {
        global $core;

        if(empty($approvers)) {
            $approvers = $this->generate_approvalchain($options, $options['aroBusinessManager']);
        }
        //  $approve_immediately = $this->should_approveimmediately();
        $sequence = 1;
        if(is_array($approvers)) {
            foreach($approvers as $key => $val) {
                $approve_status = $timeapproved = 0;
                if($val == $core->user['uid'] && $approve_immediately == true) {
                    $approve_status = 1;
                    $timeapproved = TIME_NOW;
                }

                if(is_array($approvers)) {
                    $position = array_search($val, $approvers);
                }
                $approver = new AroRequestsApprovals();
                $approver->set(array('aorid' => $this->data[self::PRIMARY_KEY], 'uid' => $val, 'isApproved' => $approve_status, 'timeApproved' => $timeapproved, 'sequence' => $sequence, 'position' => $position, 'emailRecievedDate' => ''));
                $approver->save();
                $sequence++;
            }
        }
        return true;
    }

    private function check_infromcoords() {
        $filter = 'affid ='.$this->affid.' AND purchaseType = '.$this->orderType.' AND ('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';
        $aroapprovalchain_policies = AroApprovalChainPolicies::get_data($filter);
        if(is_object($aroapprovalchain_policies)) {
            return $aroapprovalchain_policies->informCoordinators;
        }
    }

    private function check_informglobalcfo() {
        $filter = 'affid ='.$this->affid.' AND purchaseType = '.$this->orderType.' AND ('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';
        $aroapprovalchain_policies = AroApprovalChainPolicies::get_data($filter);
        if(is_object($aroapprovalchain_policies)) {
            return $aroapprovalchain_policies->informGlobalCFO;
        }
    }

    private function check_informglobalpurchasemgr() {
        $filter = 'affid ='.$this->affid.' AND purchaseType = '.$this->orderType.' AND ('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';
        $aroapprovalchain_policies = AroApprovalChainPolicies::get_data($filter);
        if(is_object($aroapprovalchain_policies)) {
            return $aroapprovalchain_policies->informGlobalPurchaseMgr;
        }
    }

    private function get_segoordinators() {
        $arorequestlines = AroRequestLines::get_data(array('aorid' => $this->data['aorid']), array('returnarray' => true));
        if(is_array($arorequestlines)) {
            foreach($arorequestlines as $arorequestline) {
                $arosegments['psid'] = $arorequestline->psid;
            }
        }
        if(is_array($arosegments)) {
            foreach($arosegments as $key => $value) {
                $productsegment_obj = new ProductsSegments($value);
                $coordinators_objs[$value] = $productsegment_obj->get_coordinators();
                unset($productsegment_obj);
            }
            foreach($coordinators_objs as $key => $coord_objs) {
                if(is_array($coord_objs)) {
                    foreach($coord_objs as $coord_obj) {
                        $coordinators[] = $coord_obj->get_coordinator();
                    }
                }
                else if(is_object($coord_objs)) {
                    $coordinators[] = $coord_objs->get_coordinator();
                }
            }
        }
        return $coordinators;
    }

    public function get_approvers(array $config = array()) {
        if(empty($config)) {
            $config = array('returnarray' => true, 'simple' => false, 'order' => array('by' => 'sequence', 'sort' => 'ASC'));
        }
        return AroRequestsApprovals::get_data(array('aorid' => $this->data[self::PRIMARY_KEY]), $config);
    }

    public function get_firstapprover() {
        return $this->get_approvers(array('order' => array('sort' => 'ASC', 'by' => 'sequence'), 'limit' => '0, 1'));
    }

    public function send_approvalemail() {
        global $core, $db;
        $firstapprover = $this->get_firstapprover();
        if(!is_object($firstapprover) && empty($firstapprover)) {
            return false;
        }
        $to = $firstapprover->get_email();
        $approve_link = '<a href="'.$core->settings['rootdir']."/index.php?module=aro/managearodouments&referrer=toapprove&requestKey=".base64_encode($this->data['identifier'])."&id=".$this->data[self::PRIMARY_KEY].' "> View and Approve ARO </a>';
        $aroapprovalemail_subject = 'Aro Needs Approval !';
        $email_data = array(
                'from' => 'ocos@orkila.com',
                'to' => $to,
                'subject' => $aroapprovalemail_subject,
                'message' => "Aro Request Needs Approval:".$approve_link,
        );
        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();
        $mailer->set_type();
        $mailer->set_from($email_data['from']);
        $mailer->set_subject($email_data['subject']);
        $mailer->set_message($email_data['message']);
        $mailer->set_to($email_data['to']);
        // $x=$mailer->debug_info();  print_R($x); exit;
        $mailer->send();
        if($mailer->get_status() === true) {
            $data = array('emailRecievedDate' => TIME_NOW);
            $query = $db->update_query('aro_requests_approvals', $data, 'araid='.$firstapprover->araid);

            $toinform = $this->get_toinform();

            if($this->check_infromcoords() == 1) {
                $segcoords = $this->get_segoordinators();
                if(is_array($segcoords)) {
                    foreach($segcoords as $coord) {
                        $mailinglist[$coord->uid] = $coord->get_email();
                    }
                }
            }
            if(is_array($mailinglist)) {
                $mailinglist = array_unique($mailinglist);
                $email_data = array(
                        'from_email' => 'ocos@orkila.com',
                        'from' => 'ocos@orkila.com',
                        'to' => $mailinglist, //$toinform
                        'subject' => 'Aro '.$this->orderReference.' _Segments Coordinators Notification',
                        'message' => 'Aro '.$this->orderReference.' in progress'  // change message
                );
                $mail = new Mailer($email_data, 'php');
            }
        }
    }

    public function approve($user) {
        global $db;
        if($this->can_apporve($user)) {
            $query = $db->update_query('aro_requests_approvals', array('isApproved' => 1, 'timeApproved' => TIME_NOW), ''.self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]).' AND uid='.$user->uid);
            if($query) {
                return true;
            }
        }
        else {
            return false;
        }
    }

    public function get_nextapprover() {
        return AroRequestsApprovals::get_data(array('isApproved' => 0, 'aorid' => $this->data[self::PRIMARY_KEY]), array('order' => array('sort' => 'ASC', 'by' => 'sequence'), 'limit' => '0, 1'));
    }

    public function inform_nextapprover() {
        global $core, $db;
        $approval = $this->get_nextapprover();
        if(is_object($approval)) {
            $user = new Users($approval->uid);
            $approve_link = $core->settings['rootdir']."/index.php?module=aro/managearodouments&requestKey=".base64_encode($this->data['identifier'])."&id=".$this->data[self::PRIMARY_KEY]."&referrer=toapprove";
            $approve_link = '<a href="'.$approve_link.'">Approve Link</a>';
            $aroapprovalemail_subject = 'Aro Needs Approval';
            $email_data = array(
                    'from' => 'ocos@orkila.com',
                    'to' => $user->email,
                    'subject' => $aroapprovalemail_subject,
                    'message' => " Test Aro Request Needs Approval:".$approve_link,
            );
            $mailer = new Mailer();
            $mailer = $mailer->get_mailerobj();
            $mailer->set_type();
            $mailer->set_from($email_data['from']);
            $mailer->set_subject($email_data['subject']);
            $mailer->set_message($email_data['message']);
            $mailer->set_to($email_data['to']);
            $mailer->send();
            if($mailer->get_status() === true) {
                $data = array('emailRecievedDate' => TIME_NOW);
                $db->update_query('aro_requests_approvals', $data, 'araid='.$approval->araid);
            }
        }
    }

    public function can_apporve($user) {
        $approvers = $this->get_approvers();
        if(is_array($approvers)) {
            foreach($approvers as $approver) {
                if($approver->uid == $user->uid) {
                    return true;
                }
            }
            return false;
        }
    }

    public function update_arorequeststatus() {
        global $db;
        $db->update_query(self::TABLE_NAME, array('isApproved' => 1), self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return self::get_data(array(self::PRIMARY_KEY => $this->data[self::PRIMARY_KEY]));
    }

    public function get_approvals($isapproved = 1) {
        global $db;
        if($isapproved == 1) {
            $where_isapproved = ' WHERE isApproved=1';
        }
        else {
            $where_isapproved = ' WHERE isApproved=0';
        }
        $query = $db->query('SELECT * FROM '.Tprefix.'aro_requests_approvals '.$where_isapproved.' AND aorid='.$this->data[self::PRIMARY_KEY]);
        if($db->num_rows($query) > 0) {
            while($approver = $db->fetch_assoc($query)) {
                $approvers[$approver['uid']] = new Users($approver['uid']);
            }
            return $approvers;
        }
        return false;
    }

    public function is_approved() {
        $approvals = $this->get_approvals(0);
        if(count($approvals) == 0 || $approvals == false) {
            return true;
        }
        return false;
    }

    public function notifyapprove() {
        if($this->data['isApproved'] == 1) {
            $approvers = $this->get_approvers();
            if(is_array($approvers)) {
                foreach($approvers as $approver_obj) {
                    $approver = new Users($approver_obj->uid);
                    if(is_object($approver)) {
                        $mailinglist[$approver->uid] = $approver->get_email();
                    }
                }
            }
            if($this->check_infromcoords() == 1) {
                $segcoords = $this->get_segoordinators();
                if(is_array($segcoords)) {
                    foreach($segcoords as $coord) {
                        $mailinglist[$coord->uid] = $coord->get_email();
                    }
                }
            }
            if($this->check_informglobalcfo() == 1) {
                $affiliate = new Affiliates($this->data['affid']);
                $cfo = new Users($affiliate->cfo);
                $mailinglist[$cfo->uid] = $cfo->get_email();
            }
            if($this->check_informglobalpurchasemgr() == 1) {
                $affiliate = new Affiliates($this->data['affid']);
                $globalPurchaseMgr = new Users($affiliate->globalPurchaseManager);
                $mailinglist[$globalPurchaseMgr->uid] = $globalPurchaseMgr->get_email();
            }
            $informmoreusers = $this->check_informmoreusers();
            if(is_array($informmoreusers)) {
                foreach($informmoreusers as $useremail) {
                    $mailinglist[] = $useremail;
                }
            }

            $mailinglist = array_unique($mailinglist);
            $email_data = array(
                    'from_email' => 'ocos@orkila.com',
                    'from' => 'OCOS',
                    'to' => $mailinglist,
                    'subject' => 'Aro is approved',
                    'message' => "Aro is Approved"
            );
            $mail = new Mailer($email_data, 'php');
            if($mail->get_status() === true) {
                //
            }
        }
    }

    public function get_toinform() {
        if($this->check_infromcoords() == 1) {
            $segcoords = $this->get_segoordinators();
            if(is_array($segcoords)) {
                foreach($segcoords as $coord) {
                    $inform[$coord->uid] = $coord->get_email();
                }
            }
        }
        if($this->check_informglobalcfo() == 1) {
            $affiliate = new Affiliates($this->data['affid']);
            $cfo = new Users($affiliate->cfo);
            $inform[$cfo->uid] = $cfo->get_email();
        }
        if($this->check_informglobalpurchasemgr() == 1) {
            $affiliate = new Affiliates($this->data['affid']);
            $globalPurchaseMgr = new Users($affiliate->globalPurchaseManager);
            $inform[$globalPurchaseMgr->uid] = $globalPurchaseMgr->get_email();
        }
        $informmoreusers = $this->check_informmoreusers();
        if(is_array($informmoreusers)) {
            foreach($informmoreusers as $useremail) {
                $inform[] = $useremail;
            }
        }
        return $inform;
    }

    public function parse_messages(array $options = array()) {
        global $template, $core;
        $takeactionpage_conversation = null;

        $initialmsgs = AroRequestsMessages::get_data('aorid='.$this->data[self::PRIMARY_KEY].' AND inReplyTo=0', array('simple' => false, 'returnarray' => true));
        if(!is_array($initialmsgs)) {
            return false;
        }
        if(empty($options['uid'])) {
            $options['uid'] = $core->user['uid'];
        }

        foreach($initialmsgs as $initialmsg) {
            if(!is_object($initialmsg)) {
                continue;
            }
            //  Check if user is allowed to see the message /
            if(!$initialmsg->can_seemessage($options['uid'])) {
                continue;
            }
            $message = $initialmsg->get();
            $message['user'] = $initialmsg->get_user($message['uid'])->get();  //Get the user of  who set the message conversation /
            $message['message_date'] = date($core->settings['dateformat'], $message['createdOn']);

            if(isset($options['viewmode']) && ($options['viewmode'] == 'textonly')) {
                $takeactionpage_conversation .= '<span style="font-weight: bold;"> '.$message['user']['displayName'].'</span> <span style="font-size: 9px;">'.date($core->settings['dateformat'].' '.$core->settings['timeformat'], $message['createdOn']).'</span>:';
                $takeactionpage_conversation .= '<div>'.$message['message'].'</div><br />';
            }
            else {
                eval("\$takeactionpage_conversation .= \"".$template->get('aro_managearodocuments_takeaction_convmsg')."\";");
            }

            $replies_objs = $initialmsg->get_replies();
            if(is_array($replies_objs)) {
                $takeactionpage_conversation .= $this->parse_replies($replies_objs, 1, $options);
            }
        }
        return $takeactionpage_conversation;
    }

    private function parse_replies($replies, $depth = 1, array $options = array()) {
        global $template, $core;

        if(is_array($replies)) {
            foreach($replies as $reply) {
                if(!$reply->can_seemessage($options['uid'])) {
                    continue;
                }
                $bgcolor = alt_row($bgcolor);
                $inline_style = 'margin-left:'.($depth * 8).'px;';
                $message = $reply->get();
                $message['user'] = $reply->get_user($message['uid'])->get();
                $message['message_date'] = date($core->settings['dateformat'], $message['createdOn']);

                if(isset($options['viewmode']) && ($options['viewmode'] == 'textonly')) {
                    $takeactionpage_conversation .= '<span style="font-weight: bold;"> '.$message['user']['displayName'].'</span> <span style="font-size: 9px;">'.date($core->settings['dateformat'].' '.$core->settings['timeformat'], $message['createdOn']).'</span>:';
                    $takeactionpage_conversation .= '<div>'.$message['message'].'</div><br />';
                }
                else {
                    eval("\$takeactionpage_conversation .= \"".$template->get('aro_managearodocuments_takeaction_convmsg')."\";");
                }
                $reply_replies = $reply->get_replies();
                if(is_array($reply_replies)) {
                    $takeactionpage_conversation .= $this->parse_replies($reply_replies, $depth + 1, $options);
                }
            }
            return $takeactionpage_conversation;
        }
    }

    public function get_toapprove() {
        return $this->get_approvers();
    }

    public function get_approval_byappover($approver) {
        return AroRequestsApprovals::get_data('aorid='.$this->data[self::PRIMARY_KEY].' AND uid='.intval($approver));
    }

    public function check_informmoreusers() {
        $filter = 'affid ='.$this->affid.' AND purchaseType = '.$this->orderType.' AND ('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';
        $aroapprovalchain_policies = AroApprovalChainPolicies::get_data($filter);
        if(is_object($aroapprovalchain_policies)) {
            if(!empty($aroapprovalchain_policies->informExternalUsers)) {
                $informmore = unserialize(base64_decode($aroapprovalchain_policies->informExternalUsers));
            }
            $informmore = array_filter($informmore);
            if(!empty($aroapprovalchain_policies->informInternalUsers)) {
                $informinternalusers = unserialize(base64_decode($aroapprovalchain_policies->informInternalUsers));
                if(is_array($informinternalusers)) {
                    foreach($informinternalusers as $userid) {
                        $user = new Users($userid);
                        $informmore[] = $user->get_email();
                    }
                }
            }
        }
        return $informmore;
    }

    public function getif_approvedonce($aorid) {
        $approvals = AroRequestsApprovals::get_data(array(AroRequests::PRIMARY_KEY => intval($aorid)), array('returnarray' => true));
        if(is_array($approvals)) {
            foreach($approvals as $approval) {
                if($approval->isApproved == 1) {
                    return true;
                }
            }
        }
        return false;
    }

    public function delete_aro() {
        global $db;
        $todelete = $this->data[AroRequests::PRIMARY_KEY];
        $attributes = array(AroRequests::PRIMARY_KEY);
        if($this->data['isFinalized'] == 1 || $this->data['revision'] > 0) {
            $this->errorcode = 1;
            return $this;
        }
        foreach($attributes as $attribute) {
            $tables = $db->get_tables_havingcolumn($attribute, 'TABLE_NAME !="'.AroRequests::TABLE_NAME.'"');
            if(is_array($tables)) {
                foreach($tables as $table) {
                    $query = $db->query("SELECT * FROM ".Tprefix.$table." WHERE ".$attribute."=".intval($todelete)." ");
                    if($db->num_rows($query) > 0) {
                        if($table == AroApprovalChainPolicies::TABLE_NAME) {
                            while($approval = $db->fetch_assoc($query)) {
                                if($approval->isApproved == 1) {
                                    $this->errorcode = 1;
                                    return $this;
                                }
                            }
                        }
                        $deletequery = $db->query("DELETE FROM ".$table." WHERE ".AroRequests::PRIMARY_KEY." = ".$todelete);
                    }
                }
            }
        }
        $delete = $this->delete();
        if($delete) {
            $this->errorcode = 0;
            return $this;
        }
        $this->errorcode = 2;
        return $this;
    }

}