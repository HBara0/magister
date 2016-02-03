<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroPolicies.php
 * Created:        @rasha.aboushakra    Feb 4, 2015 | 10:40:13 AM
 * Last Update:    @rasha.aboushakra    Feb 4, 2015 | 10:40:13 AM
 */

class AroPolicies extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'apid';
    const TABLE_NAME = 'aro_policies';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'affid,coid,purchaseType,effectiveFrom,effectiveTo';

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
            $data['createdOn'] = TIME_NOW;
            $data['createdBy'] = $core->user['uid'];
            $query = $db->insert_query(self::TABLE_NAME, $data);
            if($query) {
                $this->data[self::PRIMARY_KEY] = $id = $db->last_id();
                $log->record('aro_policies', $id);
            }
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $core, $log;
        if(!$this->validate_requiredfields($data)) {
            if($this->co_exist('apid NOT IN ('.$this->data['apid'].')')) {
                $this->errorcode = 3;
                return $this;
            }
            $data['modifiedOn'] = TIME_NOW;
            $data['modifiedBy'] = $core->user['uid'];
            if(!isset($data['isActive'])) {
                $data['isActive'] = 0;
            }
            if($this->is_policyused($this)) {
                $this->errorcode = 4;
                return $this;
            }
            $query = $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
            if($query) {
                // $id = $db->last_id();
                $log->record('aro_policies', $this->data[self::PRIMARY_KEY]);
            }
        }
        return $this;
    }

    protected function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('affid', 'effectiveFrom', 'effectiveTo'); // add required fields
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