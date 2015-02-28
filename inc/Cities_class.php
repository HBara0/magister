<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Cities_class.php
 * Created:        @zaher.reda    Apr 29, 2014 | 2:45:11 PM
 * Last Update:    @zaher.reda    Apr 29, 2014 | 2:45:11 PM
 */

class Cities extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'ciid';
    const TABLE_NAME = 'cities';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = null;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function get_country() {
        return new Countries($this->data['coid']);
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
        if(empty($this->data['defaultAirport'])) {
            return false;
        }
        return new TravelManagerAirports($this->data['defaultAirport']);
    }

    public static function get_cities($filters = '') {
        global $db;

        $cities = array();

        /* Filters to be improved */
        if(!empty($filters)) {
            if(is_array($filters)) {
                $andor = ' WHERE ';
                foreach($filters as $attr => $value) {
                    if(is_numeric($value)) {
                        $value = intval($value);
                    }
                    else {
                        $value = '"'.$db->escape_string($value).'"';
                    }
                    $filters_querystring.=$andor.$attr.'='.$value;
                    $andor = ' AND ';
                }
            }
            else {
                $filters_querystring = ' WHERE '.$db->escape_string($filters);
            }
        }
        $query = $db->query('SELECT '.self::PRIMARY_KEY.' FROM '.Tprefix.self::TABLE_NAME.$filters_querystring);
        if($db->num_rows($query) > 1) {
            while($city = $db->fetch_assoc($query)) {
                $cities[$city[self::PRIMARY_KEY]] = new Cities($city[self::PRIMARY_KEY]);
            }
            $db->free_result($query);
            return $cities;
        }
        else {
            if($db->num_rows($query) == 1) {
                return new self($db->fetch_field($query, self::PRIMARY_KEY));
            }
            return false;
        }

        return false;
    }

    protected function create(array $data) {

    }

    protected
            function update(array $data) {

    }

}