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

    public static function get_airports($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public static function get_airport_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public function get() {
        return $this->airport;
    }

}