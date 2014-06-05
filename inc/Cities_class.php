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

    public function get_approvedhotels() {
        global $db;

        $query = $db->query('SELECT tmhid FROM '.Tprefix.'travelmanager_hotels  WHERE  isApproved=1 AND city ="'.$db->escape_string($this->city['ciid']).'"');
        if($db->num_rows($query) >= 1) {
            while($item = $db->fetch_assoc($query)) {
                $items[$item['tmhid']] = new TravelManagerHotels($item['tmhid']);
            }
        }
        return $items;
    }

    public function get_reviews() {
        global $db;

        $query = $db->query('SELECT tmcrid FROM '.Tprefix.'travelmanager_cityreviews  WHERE ciid ="'.$db->escape_string($this->city['ciid']).'"');
        if($db->num_rows($query) >= 1) {

            while($item = $db->fetch_assoc($query)) {
                $reviewitems[$item['tmcrid']] = new TravelManagerCityReviews($item['tmcrid']);
            }
        }
        return $reviewitems;
    }

    public function get_latestbriefing() {
        global $db;

        return TravelManagerCityBriefings::get_citybriefings('ciid='.$db->escape_string($this->city['ciid']), array('ORDER' => array('by' => 'createdOn', 'sort' => 'DESC'), 'limit' => '0,1'));
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
                    $filters_querystring .= $andor.$attr.'='.$value;
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

    public function get() {
        return $this->city;
    }

}