<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroRequestsApprovals_class.php
 * Created:        @rasha.aboushakra    Feb 13, 2015 | 2:38:35 PM
 * Last Update:    @rasha.aboushakra    Feb 13, 2015 | 2:38:35 PM
 */

class AroRequestsApprovals extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'araid';
    const TABLE_NAME = 'aro_requests_approvals';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'aorid,uid';
    const REQUIRED_ATTRS = 'aorid,position,uid,sequence';  //timesApproved

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $log, $errorhandler, $lang;
        if($this->validate_requiredfields($data)) {
            $query = $db->insert_query(self::TABLE_NAME, $data);
            if($query) {
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            }
        }
        else {
            $this->errorcode = 5;
            $errorhandler->record($lang->requiredfields.' for ', $lang->$data['position']);
            return $this;
        }
    }

    protected function update(array $data) {
        global $db, $log, $errorhandler, $lang;
        if($this->validate_requiredfields($data)) {

            $query = $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
            if($query) {
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            }
        }
        else {
            $this->errorcode = 5;
            $errorhandler->record($lang->requiredfields.' for ', $lang->$data['position']);

            return $this;
        }
    }

    public function get_user() {
        return new Users($this->data['uid']);
    }

    public function get_email() {
        $user = $this->get_user();
        return $user->email;
    }

    public function is_apporved() {
        if($this->data['isApproved'] == 1) {
            return true;
        }
        return false;
    }

}