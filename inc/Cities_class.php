<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: Cities_class.php
 * Created:        @zaher.reda    Apr 29, 2014 | 2:45:11 PM
 * Last Update:    @zaher.reda    Apr 29, 2014 | 2:45:11 PM
 */

class Cities {
    private $city = array();

    public function __construct($id) {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    private function read($id) {
        global $db;
        $this->city = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.'cities WHERE ciid='.intval($id)));
    }

    public function get_country() {
        return new Countries($this->city['coid']);
    }
    
    public static function get_city_byname($name) {
        global $db;

        if(!empty($name)) {
            $id = $db->fetch_field($db->query('SELECT ciid FROM '.Tprefix.'cities WHERE name="'.$db->escape_string($name).'"'), 'ciid');
            if(!empty($id)) {
                return new Cities($id);
            }
        }
        return false;
    }

    public function get_defaultairport() {
        
    }
    
    public static function get_cities($filters = '') {
        global $db;
        
        $cities = array();
        
        if(!empty($filters)) {
            $filters = ' WHERE '.$db->escape_string($filters);
        }
        $query = $db->query('SELECT ciid FROM '.Tprefix.'cities'.$filters);
        while($city = $db->fetch_assoc($query)) {
            $cities[$city['ciid']] = new Cities($city['ciid']);
        }
        $db->free_result($query);
        return $cities;
    }
    
    public function get() {
        return $this->city;
    }

}