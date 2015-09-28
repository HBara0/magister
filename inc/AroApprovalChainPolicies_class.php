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
    const UNIQUE_ATTRS = 'affid,purchaseType,effectiveFrom,effectiveTo';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core, $log;
        $required_fields = array('effectiveFrom', 'effectiveTo');
        foreach($required_fields as $field) {
            $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
            if(is_empty($data[$field])) {
                $this->errorcode = 2;
                return false;
            }
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
                'createdOn' => TIME_NOW,
        );
        $query = $db->insert_query(self::TABLE_NAME, $policies_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
            $log->record('aro_manage_approvalchain_policies', $this->data[self::PRIMARY_KEY]);
            $this->errorcode = 0;
        }
    }

    protected function update(array $data) {
        global $db, $core, $log;
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
                            return false;
                        }
                        $policies_array['approvalChain'] = @serialize($approverfield);
                    }
                }
            }

            $policies_array = array('affid' => $data['affid'],
                    'effectiveFrom' => $data['effectiveFrom'],
                    'effectiveTo' => $data['effectiveTo'],
                    'approvalChain' => @serialize($data['approverchain']),
                    'modifiedBy' => $core->user['uid'],
                    'purchaseType' => $data['purchaseType'],
                    'informCoordinators' => $data['informCoordinators'],
                    'informGlobalCFO' => $data['informGlobalCFO'],
                    'informGlobalPurchaseMgr' => $data['informGlobalPurchaseMgr'],
                    'informExternalUsers' => base64_encode($data['informExternalUsers']),
                    'informInternalUsers' => base64_encode($data['informInternalUsers']),
                    'modifiedOn' => TIME_NOW,
            );
            unset($data['approvalChain']);
            $query = $db->update_query(self::TABLE_NAME, $policies_array, ''.self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
            if($query) {
                $log->record('aro_manage_approvalchain_policies', array('update'));
            }
        }
    }

    public function get_purchasetype() {
        return new PurchaseTypes($this->data['purchaseType']);
    }

}