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
class TravelManagerHotels {
    private $hotels = array();

    const PRIMARY_KEY = 'tmhid';
    const TABLE_NAME = 'travelmanager_hotels';

    public function __construct($id) {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    private function read($id) {
        global $db;
        $this->hotels = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function get_country() {
        return new Countries($this->hotels['coid']);
    }

    public function get_city() {
        return new Cities($this->hotels['ciid']);
    }

    public static function get_hotels_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_hotels($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_review() {
        global $db;
        return TravelManagerAccomodationsReview::get_accoreviews('tmhid='.$db->escape_string($this->hotels['tmhid']), array('ORDER' => array('by' => 'createdOn', 'sort' => 'DESC'), 'limit' => '0,1'));
    }

    public function get() {
        return $this->hotels;
    }

}