<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: TravelManagerPlanTransps_Class.php
 * Created:        @tony.assaad    May 23, 2014 | 3:41:53 PM
 * Last Update:    @tony.assaad    May 23, 2014 | 3:41:53 PM
 */

/**
 * Description of TravelManagerPlanTransps_Class
 *
 * @author tony.assaad
 */
class TravelManagerPlanTransps_Class {
    private $transpsegments = array();

    const PRIMARY_KEY = 'tmpltid';
    const TABLE_NAME = 'travelmanager_plan_transps';

    public function __construct($id) {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    private function read($id) {
        global $db;
        $this->transpsegments = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public static function get_transpsegments_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_transpsegments($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get() {
        return $this->transpsegments;
    }

    public function get_segment() {
        return new TravelManagerPlanSegments($this->transpsegments['tmpsid']);
    }

    public function get_transpcategory() {
        return new TravelManagerTranspCategories($this->transpsegments['tmtcid']);
    }

}