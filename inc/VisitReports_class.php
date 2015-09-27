<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: VisitReports.php
 * Created:        @hussein.barakat    Apr 15, 2015 | 11:21:34 AM
 * Last Update:    @hussein.barakat    Apr 15, 2015 | 11:21:34 AM
 */

class VisitReports extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'vrid';
    const TABLE_NAME = 'visitreports';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'vrid,identifier,uid,cid,rpid,affid,date';
    const UNIQUE_ATTRS = 'identifier,uid,cid';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        ;
    }

    public function update(array $data) {
        global $db;
        if(is_array($data)) {
            $fields = array('identifier', 'uid', 'cid', 'rpid', 'affid', 'date', 'type', 'purpose', 'hasSupplier', 'lid', 'supplyStatus', 'availabilityIssues', 'currentMktShare', 'isLocked', 'finishDate', 'isDraft');
            foreach($fields as $field) {
                $update_array[$field] = $data[$field];
            }
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

}