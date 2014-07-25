<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: TravelManagerPlanSegments_Class.php
 * Created:        @tony.assaad    May 23, 2014 | 3:10:41 PM
 * Last Update:    @tony.assaad    May 23, 2014 | 3:10:41 PM
 */

/**
 * Description of TravelManagerPlanSegments_Class
 *
 * @author tony.assaad
 */
class TravelManagerPlanSegments {
    private $data = array();

    const PRIMARY_KEY = 'tmpsid';
    const TABLE_NAME = 'travelmanager_plan_segments';

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

    public function create($segmentdata = array()) {
        global $db, $core;

        if(is_empty($segmentdata['fromDate'], $segmentdata['toDate'], $segmentdata['originCity'], $segmentdata['destinationCity'])) {
            $this->errorode = 2;
            return false;
        }

        if(value_exists(self::TABLE_NAME, TravelManagerPlan::PRIMARY_KEY, $segmentdata[TravelManagerPlan::PRIMARY_KEY], "(fromDate = {$segmentdata['fromDate']}  OR toDate = {$segmentdata['toDate']}) AND sequence=".$segmentdata['sequence'])) {
            $this->errorode = 4;
            return false;
        }

        $sanitize_fields = array('fromDate', 'toDate', 'originCity', 'destinationCity');
        foreach($sanitize_fields as $val) {
            $this->supplier[$val] = $core->sanitize_inputs($this->supplier[$val], array('removetags' => true));
        }
        $segmentdata_array = array('tmpid' => $segmentdata['tmpid'],
                'name' => 'Segment_'.$segmentdata['sequence'],
                'fromDate' => $segmentdata['fromDate'],
                'toDate' => $segmentdata['toDate'],
                'originCity' => $segmentdata['originCity'],
                'destinationCity' => $segmentdata['destinationCity'],
                'createdBy' => $core->user['uid'],
                'sequence' => $segmentdata['sequence'],
                'createdOn' => TIME_NOW
        );

        $db->insert_query(self::TABLE_NAME, $segmentdata_array);
        $this->data[self::PRIMARY_KEY] = $db->last_id();

        // if(isset($segmentdata['tmtcid'])) {
        //  $transptdata['tmpsid'] = $this->data[self::PRIMARY_KEY];
        $transptdata = $segmentdata['tmtcid'];

        /* Initialize the object */
        if(is_array($transptdata)) {
            foreach($transptdata as $category => $data) {
                $chkdata = $data;
                rsort($chkdata);
                if(is_array($chkdata[0])) {
                    foreach($data as $id => $transit) {
                        $transp_obj = new TravelManagerPlanTransps();
                        $transit[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
                        $transit['tmtcid'] = $category;
                        $transp_obj->set($transit);
                        $transp_obj->save();
                    }
                }
                else {
                    $transp_obj = new TravelManagerPlanTransps();
                    $data['tmtcid'] = $category;
                    $data[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
                    $transp_obj->set($data);
                    $transp_obj->save();
                }
            }
            unset($chkdata);
        }

        if(isset($segmentdata['tmhid'])) {
            $hoteltdata['tmpsid'] = $this->data[self::PRIMARY_KEY];
            $hoteltdata['tmhid'] = $segmentdata['tmhid'];

            $accod_obj = new TravelManagerPlanaccomodations();

            $accod_obj->set($hoteltdata);
            $accod_obj->save();
            $this->errorode = 0;
        }
    }

    public function update(array $segmentdata) {
        global $db;

        $valid_fields = array('fromDate', 'toDate', 'originCity', 'destinationCity');
        /* Consider using array intersection */
        foreach($valid_fields as $attr) {
            $segmentnewdata[$attr] = $segmentdata[$attr];
        }

        $db->update_query(self::TABLE_NAME, $segmentnewdata, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
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
        global $core;
        if(empty($data)) {
            $data = $this->data;
        }//get object of and the id and set data and save
        $tmpsegment = TravelManagerPlanSegments::get_segments(array(TravelManagerPlan::PRIMARY_KEY => $data[TravelManagerPlan::PRIMARY_KEY], 'fromDate' => $data['fromDate'], 'toDate' => $data['toDate']));
        if(is_object($tmpsegment)) {
            $tmpsegment->update($data);
        }
        else {
            $this->create($data);
        }
    }

    public static function get_segment_byattr($attr, $value, $operator = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value, $operator);
    }

    public static function get_segments($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_plan() {
        return new TravelManagerPlan($this->data['tmpid']);
    }

    public function get_origincity() {
        return new Cities($this->data['originCity']);
    }

    public function get_destinationcity() {
        return new Cities($this->data['destinationCity']);
    }

    public function get() {
        return $this->data;
    }

    public function get_createdBy() {
        return new Users($this->data['createdBy']);
    }

    public function get_errorcode() {
        return $this->errorode;
    }

    public function get_modifiedBy() {
        return new Users($this->data['modifiedBy']);
    }

}
?>