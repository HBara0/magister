<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: TravelManagerCityReviews_class.php
 * Created:        @tony.assaad    Jun 2, 2014 | 3:38:10 PM
 * Last Update:    @tony.assaad    Jun 2, 2014 | 3:38:10 PM
 */

/**
 * Description of TravelManagerCityReviews_class
 *
 * @author tony.assaad
 */
class TravelManagerCityReviews extends AbstractClass {
    protected $cityreviews = array();

    const PRIMARY_KEY = 'tmcrid';
    const TABLE_NAME = 'travelmanager_cityreviews';
    const CLASSNAME = __CLASS__;

    public function __construct($id) {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    protected function read($id) {
        global $db;

        $this->cityreviews = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function create(array $data) {
        ;
    }

    public function update(array $data) {
        ;
    }

    public static function get_cityreviews_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_cityreviews($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_createdBy() {
        return new Users($this->cityreviews['createdBy']);
    }

    public function get() {
        return $this->cityreviews;
    }

}