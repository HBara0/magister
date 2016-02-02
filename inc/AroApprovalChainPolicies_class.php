<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroManageApprovalChainPolicies_class.php
 * Created:        @tony.assaad    Feb 4, 2015 | 12:37:40 PM
 * Last Update:    @tony.assaad    Feb 4, 2015 | 12:37:40 PM
 */

/**
 * Description of AroManageApprovalChainPolicies_class
 *
 * @author tony.assaad
 */
class AroApprovalChainPolicies extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'aapcid';
    const TABLE_NAME = 'aro_approvalchain_policies';
    const DISPLAY_NAME = '';
    const UNIQUE_ATTRS = 'affid,coid,purchaseType,effectiveFrom,effectiveTo';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core, $log;
        if(!$this->validate_requiredfields($data)) {
            if($this->co_exist()) {
                $this->errorcode = 3;
                return $this;
            }
            if(is_array($data['approverchain'])) {
                foreach($data['approverchain'] as $approverfield) {
                    if(empty($approverfield['approver']) || !isset($approverfield['approver'])) {
                        unset($approverfield);
                        // continue;
                    }
                    if(is_array($approverfield)) {
                        if(($approverfield['approver'] == 'user') && is_empty($approverfield['uid'])) {
                            $this->errorcode = 2;
                            return false;
                        }
                        $policies_array['approvalChain'] = @serialize($approverfield);
                    }
                }
            }
            $policies_array = array('affid' => $data['affid'],
                    'coid' => $data['coid'],
                    'effectiveFrom' => $data['effectiveFrom'],
                    'effectiveTo' => $data['effectiveTo'],
                    'approvalChain' => @serialize($data['approverchain']),
                    'createdBy' => $core->user['uid'],
                    'purchaseType' => $data['purchaseType'],
                    'informCoordinators' => $data['informCoordinators'],
                    'informGlobalCFO' => $data['informGlobalCFO'],
                    'informGlobalPurchaseMgr' => $data['informGlobalPurchaseMgr'],
                    'informExternalUsers' => base64_encode($data['informExternalUsers']),
                    'informInternalUsers' => base64_encode($data['informInternalUsers']),
                    'informGlobalCommercials' => $data['informGlobalCommercials'],
                    'createdOn' => TIME_NOW,
            );
            $query = $db->insert_query(self::TABLE_NAME, $policies_array);
            if($query) {
                $this->data[self::PRIMARY_KEY] = $db->last_id();
                $log->record('aro_manage_approvalchain_policies', $this->data[self::PRIMARY_KEY]);
                $this->errorcode = 0;
            }
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $core, $log;
        if(!$this->validate_requiredfields($data)) {

            if($this->co_exist('aapcid NOT IN ('.$this->data['aapcid'].')')) {
                $this->errorcode = 3;
                return $this;
            }

            if(is_array($data)) {
                if(is_array($data['approverchain'])) {
                    foreach($data['approverchain'] as $approverfield) {
                        if(empty($approverfield['approver']) || !isset($approverfield['approver'])) {
                            unset($approverfield);
                            // continue;
                        }
                        if(is_array($approverfield)) {
                            if($approverfield['approver'] == 'user' && is_empty($approverfield['uid'])) {
                                $this->errorcode = 2;
                                return $this;
                            }
                            $policies_array['approvalChain'] = @serialize($approverfield);
                        }
                    }
                }
                $policies_array = array('affid' => $data['affid'],
                        'coid' => $data['coid'],
                        'effectiveFrom' => $data['effectiveFrom'],
                        'effectiveTo' => $data['effectiveTo'],
                        'approvalChain' => @serialize($data['approverchain']),
                        'modifiedBy' => $core->user['uid'],
                        'purchaseType' => $data['purchaseType'],
                        'informCoordinators' => $data['informCoordinators'],
                        'informGlobalCFO' => $data['informGlobalCFO'],
                        'informGlobalPurchaseMgr' => $data['informGlobalPurchaseMgr'],
                        'informGlobalCommercials' => $data['informGlobalCommercials'],
                        'informExternalUsers' => base64_encode($data['informExternalUsers']),
                        'informInternalUsers' => base64_encode($data['informInternalUsers']),
                        'modifiedOn' => TIME_NOW,
                );
                unset($data['approvalChain']);
                $existing_chain = new AroApprovalChainPolicies($this->data[self::PRIMARY_KEY]);
                if(is_object($existing_chain)) {
                    if(strcmp($existing_chain->approvalChain, $policies_array['approvalChain']) != 0) {
                        if($this->is_policyused($this)) {
                            $this->errorcode = 4;
                            return $this;
                        }
                    }
                }
                $query = $db->update_query(self::TABLE_NAME, $policies_array, ''.self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
                if($query) {
                    $log->record('aro_manage_approvalchain_policies', array('update'));
                }
            }
        }
        else {
            $this->errorcode = 2;
        }
        return $this;
    }

    public function get_purchasetype() {
        return new PurchaseTypes($this->data['purchaseType']);
    }

    protected function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('affid', 'purchaseType', 'effectiveFrom', 'effectiveTo'); // add required fields
            foreach($required_fields as $field) {
                if(empty($data[$field])) {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

    public function co_exist($extra_where = '') {
        $where = 'purchaseType='.$this->data['purchaseType'].' AND affid='.$this->data['affid'].' AND coid='.$this->data['coid'].' AND ('
                .'((effectiveFrom BETWEEN '.$this->data['effectiveFrom'].' AND '.$this->data['effectiveTo'].') OR (effectiveTo BETWEEN '.$this->data['effectiveFrom'].' AND '.$this->data['effectiveTo'].'))'
                .' OR '.
                '(('.$this->data['effectiveFrom'].' BETWEEN effectiveFrom AND effectiveTo) AND ('.$this->data['effectiveTo'].' BETWEEN effectiveFrom AND effectiveTo))'
                .')';
        if(!empty($extra_where)) {
            $where .=' AND '.$extra_where;
        }
        $policy = self::get_data($where);
        if(is_object($policy)) {
            return true;
        }
        return false;
    }

    public function is_policyused($policyobj) {
        $aro_betweenpolicyeffect = AroRequests::get_data('affid = '.$policyobj->affid.' AND orderType='.$policyobj->purchaseType.' AND isFinalized = 1 AND createdOn BETWEEN '.$policyobj->effectiveFrom.' AND '.$policyobj->effectiveTo, array('returnarray' => true));
        if(is_array($aro_betweenpolicyeffect)) {
            foreach($aro_betweenpolicyeffect as $aro) {
                if($aro->getif_approvedonce($aro->aorid)) {
                    return true;
                }
            }
        }
        return false;
    }

}