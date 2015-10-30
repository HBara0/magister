<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: TravelManagerAccomodationsReview.php
 * Created:        @tony.assaad    Jun 5, 2014 | 11:40:15 AM
 * Last Update:    @tony.assaad    Jun 5, 2014 | 11:40:15 AM
 */

/**
 * Description of TravelManagerAccomodationsReview
 *
 * @author tony.assaad
 */
class TravelManagerAccomodationsReview extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'tmhrid';
    const TABLE_NAME = 'travelmanager_accomreviews';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '') {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    protected function read($id = '') {
        global $db;

        $this->data = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function create(array $data) {

    }

    public function update(array $data) {

    }

    public static function get_accoreviews_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_accoreviews($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_createdBy() {
        return new Users($this->data['createdBy']);
    }

    public function get_hotel() {
        return new TravelManagerHotels($this->data['tmhid']);
    }

    public function get() {
        return $this->data;
    }

}