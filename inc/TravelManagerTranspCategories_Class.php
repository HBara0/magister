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
    private $transpcategories = array();

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
        $this->transpcategories = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public static function get_categories_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_categories($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get() {
        return $this->transpcategories;
    }

    public function get_apivehicle() {
        return $this->transpcategories['apiVehicleTypes'];
    }

    public function get_createdBy() {
        return new Users($this->transpcategories['createdBy']);
    }

    public function get_modifiedBy() {
        return new Users($this->transpcategories['modifiedBy']);
    }

}