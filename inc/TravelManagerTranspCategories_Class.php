<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: TravelManagerTranspCategories_Class.php
 * Created:        @tony.assaad    May 23, 2014 | 3:33:50 PM
 * Last Update:    @tony.assaad    May 23, 2014 | 3:33:50 PM
 */

/**
 * Description of TravelManagerTranspCategories_Class
 *
 * @author tony.assaad
 */
class TravelManagerTranspCategories {
    private $data = array();

    const PRIMARY_KEY = 'tmtcid';
    const TABLE_NAME = 'travelmanager_transpcategories';

    public function __construct($id) {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    private function read($id) {
        global $db;
        $this->data = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public static function get_categories_byattr($attr, $value, $options = null) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value, $options);
    }

    public static function get_categories($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

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

    public function get() {
        return $this->data;
    }

    public function get_apivehicle() {
        return $this->data['apiVehicleTypes'];
    }

    public function get_createdBy() {
        return new Users($this->data['createdBy']);
    }

    public function get_modifiedBy() {
        return new Users($this->data['modifiedBy']);
    }

}