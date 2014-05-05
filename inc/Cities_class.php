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

    const PRIMARY_KEY = 'ciid';
    const TABLE_NAME = 'cities';

    public function __construct($id) {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    private function read($id) {
        global $db;
        $this->city = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function get_country() {
        return new Countries($this->city['coid']);
    }

    public static function get_city_byname($name) {
        return Cities::get_city_byattr('name', $name);
    }

    public static function get_city_byattr($attr, $value) {
        global $db;

        if(!empty($value) && !empty($attr)) {
            $query = $db->query('SELECT '.self::PRIMARY_KEY.' FROM '.Tprefix.self::TABLE_NAME.' WHERE '.$db->escape_string($attr).'="'.$db->escape_string($value).'"');
            if($db->num_rows($query) > 1) {
                $items = array();
                while($item = $db->fetch_assoc($query)) {
                    $items[$item[self::PRIMARY_KEY]] = new self($item[self::PRIMARY_KEY]);
                }
                $db->free_result($query);
                return $items;
            }
            else {
                if($db->num_rows($query) == 1) {
                    return new self($db->fetch_field($query, self::PRIMARY_KEY));
                }
                return false;
            }
        }
        return false;
    }

    public function get_defaultairport() {
        if(empty($this->city['defaultAirport'])) {
            return false;
        }
        return new TravelManagerAirports($this->city['defaultAirport']);
    }

    public static function get_cities($filters = '') {
        global $db;

        $cities = array();

        if(!empty($filters)) {
            $filters = ' WHERE '.$db->escape_string($filters);
        }
        $query = $db->query('SELECT '.self::PRIMARY_KEY.' FROM '.Tprefix.self::TABLE_NAME.$filters);
        while($city = $db->fetch_assoc($query)) {
            $cities[$city[self::PRIMARY_KEY]] = new Cities($city[self::PRIMARY_KEY]);
        }
        $db->free_result($query);
        return $cities;
    }

    public function get() {
        return $this->city;
    }

}