<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: TravelManagerPlanHotels_Class.php
 * Created:        @tony.assaad    May 23, 2014 | 4:04:55 PM
 * Last Update:    @tony.assaad    May 23, 2014 | 4:04:55 PM
 */

/**
 * Description of TravelManagerPlanHotels_Class
 *
 * @author tony.assaad
 */
class TravelManagerPlanaccomodations {
    private $data = array();

    const PRIMARY_KEY = 'tmpaid';
    const TABLE_NAME = 'travelmanager_plan_accomodations';

    public function __construct($id = '') {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    private function read($id = '') {
        global $db;
        $this->data = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function create($data = array()) {
        global $db, $core;

        $tanspdata_array = array('tmpsid' => $data['tmpsid'],
                'tmhid' => $data['tmhid']
        );
        $db->insert_query('travelmanager_plan_accomodations', $tanspdata_array);
        $this->data[self::PRIMARY_KEY] = $db->last_id();
    }

    public static function get_planacco_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_planaccomodations($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_segment() {
        return new TravelManagerPlanSegments($this->data['tmpsid']);
    }

    /**/
    public function set(array $data) {
        foreach($data as $name => $value) {
            $this->data[$name] = $value;
        }
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    /* call the Magical function  get to acces the private attributes */
    public function __get($name) {
        if(array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
    }

    public function save(array $data = array()) {
        if(value_exists(self::TABLE_NAME, self::PRIMARY_KEY, $this->data[self::PRIMARY_KEY])) {
            //Update
        }
        else {
            if(empty($data)) {
                $data = $this->data;
            }

            $this->create($data);
        }
    }

    public function get_hotel() {
        return new TravelManagerHotels($this->data['tmhid']);
    }

    public function get_accomodationtype() {
        return new TravelManagerPlanaccomodationtype($this->data['accomType']);
    }

    public function get() {
        return $this->data;
    }

}