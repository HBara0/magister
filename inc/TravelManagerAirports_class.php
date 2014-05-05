<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: TravelManagerAirports_class.php
 * Created:        @zaher.reda    Apr 29, 2014 | 3:01:50 PM
 * Last Update:    @zaher.reda    Apr 29, 2014 | 3:01:50 PM
 */

class TravelManagerAirports {
    private $airport = array();

    const PRIMARY_KEY = 'apid';
    const TABLE_NAME = 'travelmanager_airports';

    public function __construct($id) {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    private function read($id) {
        global $db;
        $this->airport = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function get_country() {
        return new Countries($this->airport['coid']);
    }

    public function get_citycountry() {
        return $this->get_city()->get_country();
    }

    public function get_city() {
        return new Cities($this->airport['ciid']);
    }

    public static function get_airport_byname($name) {
        return $this->get_airport_byattr('name', $name);
    }

    public static function get_airport_byattr($attr, $value) {
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

    public function get() {
        return $this->airport;
    }

}