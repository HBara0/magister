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

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core, $log;
        $required_fields = array('effectiveFrom', 'effectiveTo'); //warehsuoe
        foreach($required_fields as $field) {
            $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
            if(is_empty($data[$field])) {
                $this->errorcode = 2;
                return false;
            }
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
        }
    }

    protected function update(array $data) {
        global $db, $core, $log;
        if(is_array($data)) {
            $data['modifiedBy'] = $core->user['uid'];
            $data['modifiedOn'] = TIME_NOW;
            $query = $db->update_query(self::TABLE_NAME, $data, 'awpid ='.intval($this->data['awpid']));
            if($query) {
                $log->record(self::TABLE_NAME, array('update'));
            }
        }
    }

    public function co_exist() {

    }

}