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
            $arosegments = $this->save_productlines($data['productline'], $data['parmsfornetmargin']);
                    
            $this->save_linessupervision($data['actualpurchase'], $data['partiesinfo']['transitTime'], $data['partiesinfo']['clearanceTime'], $data['partiesinfo']['estDateOfShipment']);
            $this->save_currentstocklines($data['currentstock']);
             
            $fundsengaged_obj = new AroRequestsFundsEngaged();
            $data['totalfunds']['aorid'] = $this->data[self::PRIMARY_KEY];
            $fundsengaged_obj->set($data['totalfunds']);
            $fundsengaged_obj->save();
            
     //   $this->create_approvalchain();
       
        //////////////////////////////////////////////////
          //sending email section
            if($this->get_infromcoords()==1) {
                foreach($arosegments as $key => $value) {
                    $productsegment_obj = new ProductsSegments($value);
                    $coordinators_objs[$key] = $productsegment_obj->get_coordinators();
                    unset($productsegment_obj);
                }
                foreach($coordinators_objs as $key => $coord_objs) {
                    if(is_array($coord_objs)) {
                        foreach($coord_objs as $coord_obj) {
                            $users_objs[] = $coord_obj->get_coordinator();
                        }
                    }
                    else {
                        $users_objs[] = $coord_objs->get_coordinator();
                    }
                }
                foreach($users_objs as $user) {
                    $sendemail_to['coorinators'][] = $user->get_email();
                }
            }
            
            $sendemail_to['approvers']=$this->generate_approvalchain();
            $this->send_approvalemail($sendemail_to);
            
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
            //save product lines and return array of product segments involved
            $arosegments = $this->save_productlines($data['productline'], $data['parmsfornetmargin']);
            $this->save_linessupervision($data['actualpurchase'], $data['partiesinfo']['transitTime'], $data['partiesinfo']['clearanceTime'], $data['partiesinfo']['estDateOfShipment']);
            $this->save_currentstocklines($data['currentstock']);
            
            
            $fundsengaged_obj = new AroRequestsFundsEngaged();
            $data['totalfunds']['aorid'] = $this->data[self::PRIMARY_KEY];
            $fundsengaged_obj->set(  $data['totalfunds']);
            $fundsengaged_obj->save();
            
            /////////////////////////////////////////////
            // //sending email section
            if(true) {
                foreach($arosegments as $key => $value) {
                    $productsegment_obj = new ProductsSegments($value);
                    $coordinators_objs[$key] = $productsegment_obj->get_coordinators();
                    unset($productsegment_obj);
                }
                foreach($coordinators_objs as $key => $coord_objs) {
                    if(is_array($coord_objs)) {
                        foreach($coord_objs as $coord_obj) {
                            $users_objs[] = $coord_obj->get_coordinator();
                        }
                    }
                    else {
                        if(is_object($coord_objs)) {
                        $users_objs[] = $coord_objs->get_coordinator();
                        }
                    }
                }
                $sendemail_to_users=$users_objs;
            }
            $approvers=$this->get_approvers();
            if(is_array($approvers)){
                  foreach($approvers as $approver) {
                    $approvers_objs[] = new Users($approver->uid);
                }
            }
          //  $approvers = $this->generate_approvalchain();
            //get all user objects through the uid provided from generate_approvalchain() method
//            if(is_array($approvers['uid'])) {
//                foreach($approvers['uid'] as $uid) {
//                    $approvers_objs[] = new Users($app_obj);
//                }
//            }
//            
            //merge approvls and coordinators arrays
            IF(is_array($approvers_objs) && is_array($sendemail_to_users)){
            $sendmail_to_users = array_merge($approvers_objs, $sendemail_to_users);
            foreach($sendmail_to_users as $user) {
                $sendemail_to_emails[] = $user->get_email();
            }
            $this->send_approvalemail(array_unique($sendemail_to_emails));
            exit;
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
                if(empty($arorequestline['psid'])) {
                    $product = new Products($arorequestline['pid']);
                    $arorequestline['psid'] = $product->get_segment()['psid'];
                }
                $arosegments[$arorequestline['psid']] = $arorequestline['psid'];
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
                $currentstockline['transitTime'] = $transittime;
                $currentstockline['clearanceTime'] = $clearancetime;
                $currentstockline['dateOfStockEntry'] = $dateOfStockEntry;
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
        $filter = 'affid ='.$this->affid.' AND purchaseType = '.$this->orderType.' AND ('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';
        $aroapprovalchain_policies = AroApprovalChainPolicies::get_data($filter);
        if(is_object($aroapprovalchain_policies)) {
            $approvalchain = unserialize($aroapprovalchain_policies->approvalChain);
        }
        $affiliate = new Affiliates($this->affid);
        if(is_array($approvalchain)) {
            foreach($approvalchain as $key => $val) {
                $sortedapprovalchain[$val['sequence']]=$val;
            }
          ksort($sortedapprovalchain); // Sort by sequece ASC
         foreach($sortedapprovalchain as $key => $val) {
                switch($val['approver']) {
                    case 'businessManager':
                         $approvers['businessManager'] = 1;

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
        //    unset($approvers[array_search($core->user['uid'], $approvers)]);
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
        
        $sequence = 1;
        foreach($approvers as $key => $val) {
            $approve_status = $timeapproved = 0;
            if($val == $core->user['uid'] && $approve_immediately == true) {
//                 if($val == $core->user['uid']) {
//                    $approve_immediately = true;
//                }
                $approve_status = 1;
                $timeapproved = TIME_NOW;
            }
       
            if(is_array($approvers)) {
             $position = array_search($val, $approvers);
            }
            $approver = new AroRequestsApprovals();
            $approver->set(array('aorid' => $this->data[self::PRIMARY_KEY], 'uid' => $val, 'isApproved' => $approve_status, 'timeApproved' => $timeapproved, 'sequence' => $sequence, 'position' => $key));
            $approver->save();
            $sequence++;
        }
        return true;
    }
    
    public function send_approvalemail($to) {
        
        if(is_array($to['approvers'])){
            foreach($to['approvers'] as $approver_id){
                $approver=new Users($approver_id);
                $approvers_mailinglist[$approver_id]=$approver->get_email();  
            }
        }
        $approve_link="http://127.0.0.1/ocos/index.php?module=aro/managearodouments&id=".$this->data[self::PRIMARY_KEY]."&referrer=toapprove";
        $aroapprovalemail_subject='Aro Needs Approval !';
   
        $approval_email_data = array(
                'from' => 'ocos@orkila.com',
                'to' => $approvers_mailinglist,
                'subject' => $aronotiicationemail_subject ,
                'message' => "Aro Request Needs Verification:".$approve_link,
        );
        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();
        $mailer->set_type();
        $mailer->set_from($approval_email_data['from']);
        $mailer->set_subject($$approval_email_data['subject']);
        $mailer->set_message($approval_email_data['message']);
        $mailer->set_to($approval_email_data['to']);

        $x=$mailer->debug_info();
        print_R($x);
        exit;
        
       $aronotiicationemail_subject='Aro Notification';
        $notification_email_data = array(
                //'from_email' => '',
                'from' => 'ocos@orkila.com',
                'to' => $to['coorinators'],
                'subject' => $aronotiicationemail_subject ,
                'message' => "Aro Request Needs Verification ",
        );
        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();
        $mailer->set_type();
        $mailer->set_from($notification_email_data['from']);
        $mailer->set_subject($notification_email_data['subject']);
        $mailer->set_message($notification_email_data['message']);
        $mailer->set_to($notification_email_data['to']);

       
     //   $v = $mailer->debug_info();
     //   print_R($mailer->debug_info());
        $mailer->send();
//        if($mailer->get_status() == true) {
//            return true;
//        }
//        else {
//            return false;
//        }
//        $mail = new Mailer($email_data, 'php');
//        if($mail->get_status() === true) {
//
//        }
    }

    private function get_infromcoords() {
        $filter = 'affid ='.$this->affid.' AND purchaseType = '.$this->orderType.' AND ('.TIME_NOW.' BETWEEN effectiveFrom AND effectiveTo)';
        $aroapprovalchain_policies = AroApprovalChainPolicies::get_data($filter);
        if(is_object($aroapprovalchain_policies)) {
            return $aroapprovalchain_policies->informCoordinators;
        }
    }
    
    
    public function get_approvers() {
      $config= array('returnarray'=>true,'simple'=>false,'order'=>array('by'=>'sequence','sort'=>'ASC'));
      return  AroRequestsApprovals::get_data(array('aorid'=>$this->data[self::PRIMARY_KEY]),$config);
    }
    
    public function approve($from) {
        global $db;
        $id = $db->escape_string($id);
        if($this->can_apporve($from)) {
            $db->update_query(self::TABLE_NAME, array('isApproved' => 1, 'approvedOn' => TIME_NOW), ''.self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
            return true;
        }
        else {
            return false;
        }
   }
        
    public function can_apporve($from) {
        $approvers = $this->get_approvers();
        if(is_array($approvers)){
            foreach($approvers as $approver) {
                $can_approve[$approver->uid] = $approver->uid;
            }
        }
        else
            {
            $can_approve=$this->generate_approvalchain();
        }
        if(in_array($from,$can_approve)) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function notifyapprove() {
        global $lang, $log;
//        if($this->additionaldays['isApproved'] == 1) {
//            $user = new Users($this->additionaldays['uid']);
//            $requester_details = $user->get();
//            $lang->adddaysapprovedmessage = $lang->sprint($lang->adddaysapprovedmessage, $requester_details['displayName'], $this->additionaldays['numDays']);
//            $email_data = array(
//                    'from_email' => 'attendance@ocos.orkila.com',
//                    'from' => 'Orkila Attendance System',
//                    'to' => $requester_details['email'],
//                    'subject' => $lang->additionadaysapprovedsubject,
//                    'message' => $lang->adddaysapprovedmessage
//            );
//            $mail = new Mailer($email_data, 'php');
//            if($mail->get_status() === true) {
//                $log->record('notifyrequester', $user->get_reportsto()->get()['uid']);
//            }
//        }
    }

    public function update_requeststatus() {
        global $db, $log;

//        if($this->additionaldays['correspondToDate'] == 1) {
//            $period = $this->additionaldays['date'];
//        }
//        else {
//            $period = TIME_NOW;
//        }
//
//        $leavestats_query = $db->query("SELECT lsid, additionalDays 
//										FROM ".Tprefix."leavesstats 
//										WHERE uid={$this->additionaldays['uid']} AND ltid=1 AND {$period} BETWEEN periodStart AND periodEnd");
//        if($db->num_rows($leavestats_query) > 0) {
//            while($leavestat = $db->fetch_array($leavestats_query)) {
//                $additionalDays = $leavestat['additionalDays'];
//                $lsid = $leavestat['lsid'];
//            }
//            $additionalDays += $this->additionaldays['numDays'];
//            $db->update_query('leavesstats', array('additionalDays' => $additionalDays), "lsid={$lsid}");
//            $log->record('updateleavebalance', $this->additionaldays['adid']);
//        }
    }

    

}