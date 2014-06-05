<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: TravelManagerPlan_class.php
 * Created:        @tony.assaad    May 23, 2014 | 3:01:16 PM
 * Last Update:    @tony.assaad    May 23, 2014 | 3:01:16 PM
 */

/**
 * Description of TravelManagerPlan_class
 *
 * @author tony.assaad
 */
class TravelManagerPlan {
    private $plan = array();

    const PRIMARY_KEY = 'tmpid';
    const TABLE_NAME = 'travelmanager_plan';

    public function __construct($id = '') {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    private function read($id = '') {
        global $db;
        $this->plan = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public static function get_plan_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_plans($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_unplannedleaves() {
        global $core, $db;

        $query = $db->query('SELECT *  FROM '.Tprefix.' leaves WHERE  uid='.$core->user[uid].' AND NOT EXISTS(SELECT lid  FROM '.Tprefix.' travelmanager_plan WHERE lid='.$this->leave['lid'].' ) AND EXISTS (SELECT lid FROM leavesapproval WHERE lid='.$this->leave['lid'].' AND isApproved=1) ');
        if($db->num_rows($query) > 0) {
            while($rowsdata = $db->fetch_assoc($query)) {
                $uplannedleaves[$rowsdata['lid']] = new Leaves($rowsdata['lid']);
            }
            return $uplannedleaves;
        }
        return false;
    }

    public function get() {
        return $this->plan;
    }

    public static function get_availablecitytransp($directiondata = array()) {
        $googledirection_api = 'http://maps.googleapis.com/maps/api/directions/json?origin='.$directiondata['origincity']['name'].',+'.$directiondata['origincity']['country'].'&destination='.$directiondata['destcity']['name'].'+District,+'.$directiondata['destcity']['country'].'&sensor=false&mode='.$directiondata['destcity']['drivemode'].'&units=metric&departure_time='.$directiondata['destcity']['departuretime'];
        $json = file_get_contents($googledirection_api);
        $data = json_decode($json);

        return $data;
    }

    public static function parse_transportation($transmode, $sequence) {
        /* The proposed transportation categories   are parsed accordingly with the possible available transportation methods proposed by Google */
        $transporcat_obj = TravelManagerTranspCategories::get_categories_byattr('apiVehicleTypes', $transmode['vehicletype'], array('operator' => 'like'));
        if(is_object($transporcat_obj)) {
            $transportaion_cat = $transporcat_obj->get();
            if(is_array($transportaion_cat)) {
                $categories = array($transportaion_cat['tmtcid'] => $transportaion_cat['name']);
                $transpcat = parse_checkboxes('segment['.$sequence.'][tmtcid]transp_'.$sequence.'_'.$transportaion_cat['name'].'', $categories, '', true, $transportaion_cat['apiVehicleTypes'], '&nbsp;&nbsp;');
            }
            return $transpcat;
        }
    }

    public static function parse_transportaionfields($category, $sequence) {
        if(!empty($category)) {
            switch($category) {
                case'car':
                    $transportaion_fields = 'Approxmita fare'.parse_textfield('segment['.$sequence.'][tmtcid]', 'text', '');
                    break;
                case'train':
                    $availabe_arilinersobjs = TravelManagerAirlines::get_airlines('', array('contracted' => '1'));
                    if(is_array($availabe_arilinersobjs)) {
                        foreach($availabe_arilinersobjs as $availabe_arilinersobj) {
                            $availabe_ariliners = $availabe_arilinersobj->get();
                            $ariliners = array($availabe_ariliners['alid'] => $availabe_ariliners['name']);
                            $arilinersroptions = parse_radiobutton('segment['.$sequence.'][aflid]', $ariliners, '', true, '&nbsp;&nbsp;');
                            $transportaion_fields .='<div style="display:block;width:100%;"> <div style="display:inline-block;" id="airlinesoptions"> '.$arilinersroptions.' </div>  </div>';
                        }
                    }
                    /* Parse predefined airliners */
                    break;
                case'car':
                    break;
                    $transportaion_fields = 'cars agencies';
            }
            return $transportaion_fields;
        }
    }

    public function create($data = array()) {
        global $db, $core;
        if(is_array($data)) {

            $this->leaveid = $data['lid'];
            unset($data['lid']);
            if(value_exists('travelmanager_plan', 'lid', $this->leaveid, 'uid='.$core->user['uid'])) {
                $this->errorode = 1;
                return false;
            }

            $plandata = array('identifier' => substr(md5(uniqid(microtime())), 1, 10),
                    'lid' => $this->leaveid,
                    'title' => $title,
                    'uid' => $core->user['uid'],
                    'createdBy' => $core->user['uid'],
                    'createdOn' => TIME_NOW
            );
            $db->insert_query('travelmanager_plan', $plandata);
            $planid = $db->last_id();
            /* create plan */
            $this->segmentdata = $data;
        }
        /* create segment */
        $segment_planobj = new TravelManagerPlanSegments();
        foreach($this->segmentdata as $sequence => $segmentdata) {
            $segmentdata['tmpid'] = $planid;
            $segmentdata['sequence'] = $sequence;
            $segment_planobj->create($segmentdata);
        }
    }

    public function get_errorcode() {
        return $this->errorode;
    }

    public function get_leave() {
        return new Leaves($this->plan['lid']);
    }

    public function get_user() {
        return new Users($this->plan['uid']);
    }

    public function get_createdBy() {
        return new Users($this->plan['createdBy']);
    }

    public function get_modifiedBy() {
        return new Users($this->plan['modifiedBy']);
    }

}