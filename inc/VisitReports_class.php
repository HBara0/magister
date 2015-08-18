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
            $update_array['identifier'] = $data['identifier'];
            $update_array['uid'] = $data['uid'];
            $update_array['cid'] = $data['cid'];
            $update_array['rpid'] = $data['rpid'];
            $update_array['affid'] = $data['affid'];
            $update_array['date'] = $data['date'];
            $update_array['type'] = $data['type'];
            $update_array['purpose'] = $data['purpose'];
            $update_array['hasSupplier'] = $data['hasSupplier'];
            $update_array['supplyStatus'] = $data['supplyStatus'];
            $update_array['availabilityIssues'] = $data['availabilityIssues'];
            $update_array['currentMktShare'] = $data['currentMktShare'];
            $update_array['isLocked'] = $data['isLocked'];
            $update_array['finishDate'] = $data['finishDate'];
            $update_array['isDraft'] = $data['isDraft'];
            $update_array['lid'] = $data['lid'];
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

}