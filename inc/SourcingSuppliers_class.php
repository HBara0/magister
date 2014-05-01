<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: SourcingSuppliers_class.php
 * Created:        @zaher.reda    Apr 30, 2014 | 1:33:05 PM
 * Last Update:    @zaher.reda    Apr 30, 2014 | 1:33:05 PM
 */

/**
 * Description of SourcingSuppliers_class
 *
 * @author zaher.reda
 */
class SourcingSuppliers {
    private $supplier = array();

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
            $query_select = 'ssid, companyName, companyNameAbbr, type';
        }
        $this->supplier = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'sourcing_suppliers WHERE ssid='.intval($id)));
    }

    public function get_country() {
        return new Countries($this->supplier['country']);
    }

    public function get_city() {
        if(is_numeric($this->affiliate['city'])) {
            return new Cities($this->supplier['city']);
        }
        else {
            return Cities::get_city_byname($this->supplier['city']);
        }
    }

    public function get() {
        return $this->supplier;
    }

}