<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: TravelManagerCityBriefings_class.php
 * Created:        @tony.assaad    Jun 3, 2014 | 10:10:47 AM
 * Last Update:    @tony.assaad    Jun 3, 2014 | 10:10:47 AM
 */

/**
 * Description of TravelManagerCityBriefings_class
 *
 * @author tony.assaad
 */
class TravelManagerCityBriefings extends AbstractClass {
    protected $citybriefings = array();

    const PRIMARY_KEY = 'tmcbid';
    const TABLE_NAME = 'travelmanager_citybriefings';

    public function __construct($id) {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    protected function read($id) {
        global $db;

        $this->citybriefings = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function create(array $data) {
        ;
    }

    public function update(array $data) {
        ;
    }

    public static function get_citybriefings_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_citybriefings($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_createdBy() {
        return new Users($this->citybriefings['createdBy']);
    }

    public function get() {
        return $this->citybriefings;
    }

}