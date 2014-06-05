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
    private $segment = array();

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
        $this->segment = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function create($segmentdata = array()) {
        global $db, $core;

        $segmentdata['fromDate'] = strtotime($segmentdata['fromDate']);
        $segmentdata['toDate'] = strtotime($segmentdata['toDate']);

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
        $segmentid = $db->last_id();
        if(isset($segmentdata['tmtcid'])) {
            $transptdata['tmpsid'] = $segmentid;
            $transptdata['tmtcid'] = $segmentdata['tmtcid'];

            $transp_obj = new TravelManagerPlanTransps();
            /* Initialize the object */
            $transp_obj->set($transptdata);
            $transp_obj->save();
        }
        if(isset($segmentdata['tmhid'])) {
            $hoteltdata['tmpsid'] = $segmentid;
            $hoteltdata['tmhid'] = $segmentdata['tmhid'];

            $accod_obj = new TravelManagerPlanaccomodations();
            /**/
            $accod_obj->set($hoteltdata);
            $accod_obj->save();
        }
        $this->errorode = 0;
    }

    public static function get_segment_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_segments($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_plan() {
        return new TravelManagerPlan($this->segment['tmpid']);
    }

    public function get_origincity() {
        return new Cities($this->segment['originCity']);
    }

    public function get_destinationcity() {
        return new Cities($this->segment['destinationCity']);
    }

    public function get() {
        return $this->segment;
    }

    public function get_createdBy() {
        return new Users($this->segment['createdBy']);
    }

    public function get_modifiedBy() {
        return new Users($this->segment['modifiedBy']);
    }

}