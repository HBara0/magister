<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: SourcingSuppContactHist_class.php
 * Created:        @zaher.reda    Apr 30, 2014 | 1:49:01 PM
 * Last Update:    @zaher.reda    Apr 30, 2014 | 1:49:01 PM
 */

class SourcingSuppContactHist {
    private $contacthist = array();

    public function __construct($id, $simple = true) {
        if(empty($id)) {
            return false;
        }
        $this->read($id, $simple);
    }

    private function read($id, $simple) {
        global $db;

        $query_select = '*';
        if($simple == true) {
            $query_select = 'sschid, identifier, ssid, uid, affid, date, description, application';
        }
        $this->contacthist = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'sourcing_suppliers_contacthist WHERE sschid='.intval($id)));
    }
    
    public function get_user() {
        return new Users($this->contacthist['uid']);
    }
    
    public function get_affiliate() {
        return new Affiliates($this->contacthist['affid']);
    }

    public function get_supplier() {
        return new SourcingSuppliers($this->contacthist['ssid']);
    }
    
    public function get() {
        return $this->contacthist;
    }
}