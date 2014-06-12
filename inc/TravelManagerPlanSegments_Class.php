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

    private function read($id = '') {
        global $db;
        $this->data = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function create($segmentdata = array()) {
        global $db, $core;

//        if(is_empty($segmentdata['fromDate'], $segmentdata['toDate'], $segmentdata['originCity'], $segmentdata['destinationCity'])) {
//            $this->errorode = 2;
//            return false;
//        }
//        if(value_exists('travelmanager_plan_segments', 'createdBy', $core->user['uid'], "(fromDate = {$segmentdata['fromDate']}  OR toDate = {$segmentdata['toDate']}) AND sequence=".$segmentdata['sequence'])) {
//            $this->errorode = 4;
//            return false;
//        }

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

        $db->insert_query('travelmanager_plan_segments', $segmentdata_array);
        $this->data[self::PRIMARY_KEY] = $db->last_id();
        if(isset($segmentdata['tmtcid'])) {
            $transptdata['tmpsid'] = $this->data[self::PRIMARY_KEY];
            $transptdata['tmtcid'] = $segmentdata['tmtcid'];

            $transp_obj = new TravelManagerPlanTransps();
            /* Initialize the object */
            $transp_obj->set($transptdata);
            $transp_obj->save();
        }
        if(isset($segmentdata['tmhid'])) {
            $hoteltdata['tmpsid'] = $this->data[self::PRIMARY_KEY];
            $hoteltdata['tmhid'] = $segmentdata['tmhid'];

            $accod_obj = new TravelManagerPlanaccomodations();
            /**/
            $accod_obj->set($hoteltdata);
            $accod_obj->save();
            $this->errorode = 0;
        }
    }

    public function update($segmentdata = array()) {
        global $db;
        $tmpsid = $segmentdata['tmpsid'];
        unset($segmentdata['tmpsid']);
        $db->update_query(self::TABLE_NAME, $segmentdata, 'tmpsid='.$db->escape_string($tmpsid));
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
        $latestseg_objs = TravelManagerPlanSegments::get_segments(array('fromDate' => $this->data['fromDate'], 'toDate' => $this->data['toDate'], 'createdBy' => $core->user['uid']));
        if(is_array($latestseg_objs)) {
            foreach($latestseg_objs as $latestseg_obj) {
                $this->data = $latestseg_obj->get();
            }

            $this->update($this->data);
        }
        else {
            $this->create($data);
        }
    }

    public static function get_segment_byattr($attr, $value, $operator = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value, $operator)
        ;
    }

    public static function get_segments($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs)
        ;
    }

    public function get_plan() {
        return new TravelManagerPlan($this->data['tmpid'])
        ;
    }

    public function get_origincity() {
        return new Cities($this->data['originCity']);
    }

    public function get_destinationcity() {
        return new Cities($this->data['destinationCity'])
        ;
    }

    public function get() {
        return $this->data
        ;
    }

    public function get_createdBy() {
        return new Users($this->data['createdBy'])
        ;
    }

    public function get_errorcode() {
        return $this->errorode
        ;
    }

    public function get_modifiedBy() {
        return new Users($this->data['modifiedBy']);
    }

}