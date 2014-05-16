<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: TravelManagerAirlines.php
 * Created:        @tony.assaad    May 16, 2014 | 11:04:43 AM
 * Last Update:    @tony.assaad    May 16, 2014 | 11:04:43 AM
 */

/**
 * Description of TravelManagerAirlines
 *
 * @author tony.assaad
 */
class TravelManagerAirlines {
    private $airlines = array();

    const PRIMARY_KEY = 'alid';
    const TABLE_NAME = 'travelmanager_airlines';

    public function __construct($id) {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    private function read($id) {
        global $db;
        $this->airlines = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function get_country() {
        return new Countries($this->airlines['coid']);
    }

    public static function get_airlines_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_airlines($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get() {
        return $this->airlines;
    }

}