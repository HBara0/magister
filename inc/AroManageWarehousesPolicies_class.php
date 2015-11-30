<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroManageWarehousesPolicies_class.php
 * Created:        @tony.assaad    Feb 3, 2015 | 11:32:39 AM
 * Last Update:    @tony.assaad    Feb 3, 2015 | 11:32:39 AM
 */

/**
 * Description of AroManageWarehousesPolicies_class
 *
 * @author tony.assaad
 */
class AroManageWarehousesPolicies extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'awpid';
    const TABLE_NAME = 'aro_wareshouses_policies';
    const DISPLAY_NAME = '';
    const UNIQUE_ATTRS = 'warehouse,effectiveFrom,effectiveTo';
    const SIMPLEQ_ATTRS = 'awpid,warehouse,effectiveFrom,effectiveTo,rate,currency,datePeriod,rate_uom';
    const CLASSNAME = __CLASS__;
    const REQUIRED_ATTRS = 'effectiveFrom,effectiveTo,warehouse';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core, $log;
        if($this->validate_requiredfields($data)) {
            if($this->co_exist()) {
                $this->errorcode = 3;
                return $this;
            }
            $policies_array = array('warehouse' => $data['warehouse'],
                    'effectiveFrom' => $data['effectiveFrom'],
                    'effectiveTo' => $data['effectiveTo'],
                    'rate' => $data['rate'],
                    'currency' => $data['currency'],
                    'rate_uom' => $data['rate_uom'],
                    'datePeriod' => $data['datePeriod'],
                    'createdBy' => $core->user['uid'],
                    'createdOn' => TIME_NOW,
            );
            $query = $db->insert_query(self::TABLE_NAME, $policies_array);
            if($query) {
                $this->data[self::PRIMARY_KEY] = $db->last_id();
                $log->record('aro_managewareshouses_policies', $this->data[self::PRIMARY_KEY]);
                $this->errorcode = 0;
                return $this;
            }
        }
        else {
            $this->errorcode = 2;
            return $this;
        }
    }

    protected function update(array $data) {
        global $db, $core, $log;
        if($this->validate_requiredfields($data)) {
            if($this->co_exist('awpid NOT IN ('.$this->data['awpid'].')')) {
                $this->errorcode = 3;
                return $this;
            }
            if(is_array($data)) {
                $data['modifiedBy'] = $core->user['uid'];
                $data['modifiedOn'] = TIME_NOW;
                $query = $db->update_query(self::TABLE_NAME, $data, 'awpid ='.intval($this->data['awpid']));
                if($query) {
                    $log->record(self::TABLE_NAME, array('update'));
                    $this->errorcode = 0;
                    return $this;
                }
            }
        }
        else {
            $this->errorcode = 2;
            return $this;
        }
    }

    public function co_exist($extra_where = '') {
        $where = 'warehouse='.$this->data['warehouse'].' AND ('
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

}