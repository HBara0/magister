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
class AroManageApprovalChainPolicies extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'aapcid';
    const TABLE_NAME = 'aro_manage_approvalchain_policies';
    const DISPLAY_NAME = '';
    const UNIQUE_ATTRS = 'affid,purchaseType';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    protected function create(array $data) {
        global $db, $core, $log;
        $required_fields = array('effectiveFrom', 'effectiveTo'); //warehsuoe
        foreach($required_fields as $field) {
            $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
            if(is_empty($data[$field])) {
                $this->errorcode = 2;
                return false;
            }
        }

        if(is_array($data['approverchain'])) {
            print_r($data['approverchain']);
        }
        $policies_array = array('affid' => $data['affid'],
                'effectiveFrom' => $data['effectiveFrom'],
                'effectiveTo' => $data['effectiveTo'],
                'approvalChain' => @serialize($data['approverchain']),
                'createdBy' => $core->user['uid'],
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

    }

    public function get_purchasetype() {
        return new purchaseType($this->data['purchaseType']);
    }

}