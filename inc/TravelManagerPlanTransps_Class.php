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
class TravelManagerPlanTransps {
    private $data = array();

    const PRIMARY_KEY = 'tmpltid';
    const TABLE_NAME = 'travelmanager_plan_transps';

    public function __construct($id = '') {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    private function read($id) {
        global $db;
        $this->data = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public static function get_transpsegments_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_transpsegments($filters = null, array $configs = array()) {
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

    public function save(array $data = array()) {
        if(empty($data)) {
            $data = $this->data;
        }

        $tmptransp = TravelManagerPlanTransps::get_transpsegments(array(self::PRIMARY_KEY => $data[self::PRIMARY_KEY]));
        if(is_object($tmptransp)) {
            $tmptransp->update($data);
        }
        else {
            $tmptransp = TravelManagerPlanTransps::get_transpsegments(array(TravelManagerTranspCategories::PRIMARY_KEY => $data[TravelManagerTranspCategories::PRIMARY_KEY], TravelManagerPlan::PRIMARY_KEY => $data[TravelManagerPlan::PRIMARY_KEY]));
            if(is_object($tmptransp)) {
                $tmptransp->update($data);
            }
            $this->create($data);
        }
    }

    public function update(array $data) {
        global $db;

        /* Futher checks required here */
        $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
    }

    public function create($transportdata = array()) {
        global $db;

        $transp_details = base64_decode($transportdata['transpDetails'], true);
        if($transp_details != false) {
            $transportdata['flightDetails'] = $transp_details;
        }
        $tanspdata_array = array('tmpsid' => $transportdata['tmpsid'],
                'tmtcid' => $transportdata['tmtcid'],
                'fare' => $transportdata['fare'],
                'vehicleNumber' => $transportdata['vehicleNumber'],
                'flightNumber' => $transportdata['flightNumber'],
                'flightDetails' => $transportdata['flightDetails'],
                'transpType' => $transportdata['transpType'],
        );

        $db->insert_query(self::TABLE_NAME, $tanspdata_array);
        $this->data[self::PRIMARY_KEY] = $db->last_id();
    }

    public function get() {
        return $this->data;
    }

    public function get_segment() {
        return new TravelManagerPlanSegments($this->data['tmpsid']);
    }

    public function get_transpcategory() {
        return new TravelManagerTranspCategories($this->data['tmtcid']);
    }

}
?>