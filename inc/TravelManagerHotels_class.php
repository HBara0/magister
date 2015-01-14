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
class TravelManagerHotels extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'tmhid';
    const TABLE_NAME = 'travelmanager_hotels';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'country,city';
    const SIMPLEQ_ATTRS = 'tmhid, name, alias,city';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

//    protected function read($id) {
//        global $db;
//        $this->hotels = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
//    }

    public function create(array $data) {
        global $db, $core;
        if(is_empty($data['name'], $data['city'])) {
            $this->errorode = 2;
            return false;
        }
        $data['alias'] = trim($data['name']);
        $db->insert_query(self::TABLE_NAME, $data);
        $this->data[self::PRIMARY_KEY] = $db->last_id();
        $this->errorode = 0;
    }

    public function update(array $data) {

    }

    public function get_country() {
        return new Countries($this->data['country']);
    }

    public function get_city() {
        return new Cities($this->data['city']);
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
        return TravelManagerAccomodationsReview::get_accoreviews('tmhid='.$db->escape_string($this->data['tmhid']), array('ORDER' => array('by' => 'createdOn', 'sort' => 'DESC'), 'limit' => '0,1'));
    }

}