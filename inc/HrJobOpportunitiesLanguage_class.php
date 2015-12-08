<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: HrJobOpportunitiesLanguage.php
 * Created:        @hussein.barakat    08-Dec-2015 | 16:14:34
 * Last Update:    @hussein.barakat    08-Dec-2015 | 16:14:34
 */

class HrJobOpportunitiesLanguage extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'hrjolid';
    const TABLE_NAME = 'hr_jobopportunities_language';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const REQUIRED_ATTRS = 'joid,language';
    const UNIQUE_ATTRS = 'joid,language';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $fields = array('joid', 'language');
            foreach($fields as $field) {
                if(empty($data[$field])) {
                    $this->errorcode = 1;
                    return $this;
                }
                $tabledata[$field] = $data[$field];
            }
            $db->insert_query(self::TABLE_NAME, $tabledata);
        }
        else {
            $this->errorcode = 1;
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $fields = array('joid', 'language');
            foreach($fields as $field) {
                if(empty($data[$field])) {
                    $this->errorcode = 1;
                    return $this;
                }
                $tabledata[$field] = $data[$field];
            }
            $db->update_query(self::TABLE_NAME, $tabledata, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        }
        else {
            $this->errorcode = 1;
        }
        return $this;
    }

}