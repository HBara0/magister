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
class TravelManagerPlanaccomodation_Class {
    private $planaccomodations = array();

    const PRIMARY_KEY = 'tmpaid';
    const TABLE_NAME = 'travelmanager_plan_accomodations';

    public function __construct($id) {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    private function read($id) {
        global $db;
        $this->planaccomodations = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
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
        return new TravelManagerPlanSegments($this->planaccomodations['tmpsid']);
    }

    public function get_hotel() {
        return new TravelManagerHotels($this->planaccomodations['tmhid']);
    }

    public function get_accomodationtype() {
        return new TravelManagerPlanaccomodationtype_Class($this->planaccomodations['accomType']);
    }

    public function get() {
        return $this->planaccomodations;
    }

}