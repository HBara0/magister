<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: TravelManagerPlanaccomtypes_Class.php
 * Created:        @tony.assaad    May 23, 2014 | 4:17:45 PM
 * Last Update:    @tony.assaad    May 23, 2014 | 4:17:45 PM
 */

/**
 * Description of TravelManagerPlanaccomtypes_Class
 *
 * @author tony.assaad
 */
class TravelManagerPlanaccomtypes_Class {
    private $planaccomtypes = array();

    const PRIMARY_KEY = 'tmatid';
    const TABLE_NAME = 'travelmanager_accomtypes';

    public function __construct($id) {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    private function read($id) {
        global $db;
        $this->planaccomtypes = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public static function get_planaccomtypes_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_planaccomtypes($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get() {
        return $this->planaccomtypes;
    }

    public function get_createdBy() {
        return new Users($this->planaccomtypes['createdBy']);
    }

    public function get_modifiedBy() {
        return new Users($this->planaccomtypes['modifiedBy']);
    }

}