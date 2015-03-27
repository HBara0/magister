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
    const UNIQUE_ATTRS = 'affid,purchaseType,effectiveFrom,effectiveTo';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core, $log;
        if(!$this->validate_requiredfields($data)) {
                $data['createdOn'] = TIME_NOW;
                $data['createdBy'] = $core->user['uid'];
                $query = $db->insert_query(self::TABLE_NAME, $data);
                if($query) {
                    $id = $db->last_id();
                    $log->record('aro_policies', $id);
                    return $this;
                }
        }
    }

    protected function update(array $data) {
        global $db, $core, $log;
        if(!$this->validate_requiredfields($data)) {
            $data['modifiedOn'] = TIME_NOW;

            $data['modifiedBy'] = $core->user['uid'];
            if(!isset($data['isActive'])) {
                $data['isActive'] = 0;
            }
            $query = $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
            if($query) {
                $id = $db->last_id();
                $log->record('aro_policies', $id);
                return $this;
            }
        }
    }

    private function validate_requiredfields(array $data = array()) {
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

}