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
    private $data = array();

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
        $this->data = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public static function get_plan_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_plan($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_unplannedleaves() {
        global $core, $db;

        $query = $db->query('SELECT *  FROM '.Tprefix.' leaves WHERE  uid='.$core->user[uid].' AND NOT EXISTS(SELECT lid  FROM '.Tprefix.' travelmanager_plan WHERE lid='.$this->leave['lid'].' ) AND EXISTS (SELECT lid FROM leavesapproval WHERE lid='.$this->leave['lid'].' AND isApproved=1) ');
        if($db->num_rows($query) > 0) {
            while($rowsdata = $db->fetch_assoc($query)) {
                $udatanedleaves[$rowsdata['lid']] = new Leaves($rowsdata['lid']);
            }
            return $udatanedleaves;
        }
        return false;
    }

    public function get() {
        return $this->data;
    }

    public static function get_availablecitytransp($directiondata = array()) {
        global $core;

        if($directiondata['destcity']['departuretime'] < TIME_NOW) {
            $directiondata['destcity']['departuretime'] = TIME_NOW + 3600;
        }
        //key='.$core->settings['googleapikey'].'&
        $googledirection_api = 'http://maps.googleapis.com/maps/api/directions/json?origin='.$directiondata['origincity']['name'].',+'.$directiondata['origincity']['country'].'&destination='.$directiondata['destcity']['name'].',+'.$directiondata['destcity']['country'].'&sensor=false&mode='.$directiondata['destcity']['drivemode'].'&units=metric&departure_time='.$directiondata['destcity']['departuretime'];
        $json = file_get_contents($googledirection_api);
        $data = json_decode($json);
        return $data;
    }

    public static function parse_transportation($transmode, $sequence) {
        global $lang;
        /* The proposed transportation categories   are parsed accordingly with the possible available transportation methods proposed by Google */
        $transporcat_obj = TravelManagerTranspCategories::get_categories_byattr('apiVehicleTypes', $transmode['vehicleType'], array('operator' => 'like'));
        if(is_object($transporcat_obj)) {
            $transportaion_cat = $transporcat_obj->get();
            if(is_array($transportaion_cat)) {
                $categories = array($transportaion_cat['tmtcid'] => $transportaion_cat['title']);
                $transpcat['type'] = parse_checkboxes('segment['.$sequence.'][tmtcid]transp_'.$sequence.'_'.$transportaion_cat['name'].'', $categories, '', true, $transportaion_cat['description'], '&nbsp;&nbsp;');
                $transpcat['cateid'] = $transportaion_cat['tmtcid'];
                $transpcat['name'] = $transportaion_cat['name'];
                $transpcat['title'] = $transportaion_cat['title'];
            }

            return $transpcat;
        }
    }

    public static function parse_transportaionfields(array $category, $cityinfo = array(), $sequence) {
        global $lang;
        if(!empty($category['name'])) {
            switch($category['name']) {
                case 'taxi':
                    $transportaion_fields = '<div style="padding:3px; display: inline-block; width:50%;">'.$lang->approxfare.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][fare]', 'number', '').'</div>';
                    break;
                case 'bus':
                    $transportaion_fields = '<div style="padding:2px; display: inline-block; width:50%;">'.$lang->approxfare.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][fare]', 'number', '').'</div>';
                    break;
                case 'train':
                case 'lightrail':
                    $transportaion_fields = '<div style="padding:2px; display: inline-block; width:50%;">'.$lang->traino.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][vehicleNumber]', 'number', '').'</div>';
                    $transportaion_fields.=' <div style="padding:2px; display: inline-block; width:45%;">'.$lang->approxfare.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][fare]', 'number', '').'</div>';
                    break;
                case 'airplane':
                    $availabe_arilinersobjs = TravelManagerAirlines::get_airlines(array('contracted' => '1'));
                    if(is_array($availabe_arilinersobjs)) {
                        foreach($availabe_arilinersobjs as $availabe_arilinersobj) {
                            $permitted_ariliners[] = $availabe_arilinersobj->iatacode;
                        }
                    }
                    //$availabe_ariliners = $availabe_arilinersobj->get();
                    //$permitted_ariliners = array($availabe_ariliners['iatacode']);
                    //$arilinersroptions = parse_radiobutton('segment['.$sequence.'][aflid]', $ariliners, '', true, '&nbsp;&nbsp;');
                    //if(is_array($permitted_ariliners)) {
                    /* parse request array for the allowed airlines  and encode it as json array */

                    $flights = TravelManagerAirlines::get_flights(TravelManagerAirlines::build_flightrequestdata(array('origin' => $cityinfo['origincity']['unlocode'], 'destination' => $cityinfo['destcity']['unlocode'], 'maxStops' => 0, 'date' => $cityinfo['date'], 'permittedCarrier' => $permitted_ariliners)));
                    $transportaion_fields = TravelManagerAirlines::parse_bestflight($flights, array('name' => $category['name'], 'tmtcid' => $category['tmtcid']), $sequence);
                    //}
                    //$transportaion_fields .='<div style="display:block;width:100%;"> <div style="display:inline-block;" id="airlinesoptions"> '.$arilinersroptions.' </div>  </div>';
                    //}

                    /* Parse predefined airliners */
                    break;
                case 'car':
                    $transportaion_fields = '<div style="padding:2px; display: inline-block; width:30%;">'.$lang->agency.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][agencyName]', 'text', '').'</div>';
                    $transportaion_fields .= '<div style="padding:2px; display: inline-block; width:30%;">'.$lang->numberdays.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][numDays]', 'number', '').'</div>';
                    $transportaion_fields .= '<div style="padding:2px; display: inline-block; width:30%;">'.$lang->feeday.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][fare]', 'number', '').'</div>';
                    break;
                default:
                    $transportaion_fields = '<div style="padding:3px; display: inline-block; width:50%;">'.$lang->feeday.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][fare]', 'number', '').'</div>';
                    $transportaion_fields .= '<div style="padding:3px; display: inline-block; width:45%;">'.$lang->transptype.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][transpType]', 'text', '').'</div>';
                    break;
            }
            // $transportaion_fields .='<div style="display:inline-block;padding:5px;"  id="approximatefare"> Approximate Fare '.parse_textfield('segment['.$sequence.'][tmtcid][fare]', 'number', '').'</div>';

            return $transportaion_fields;
        }
    }

    private function check_isemptyfields($segmentdata = array()) {
        $this->requiredata = array('fromDate', 'toDate', 'originCity', 'destinationCity');
        foreach($this->requiredata as $value) {
            foreach($segmentdata as $segmentval) {
                if(empty($segmentval[$value])) {
                    return true;
                }
            }
        }
    }

    private function check_iteneraryconsistency($requiredata = array(), $leavetimeframe = array()) {
        if(is_array($requiredata)) {
            $this->segmentdata = $requiredata;

            $firstsegment_fromdate = strtotime($this->segmentdata[key($this->segmentdata)]['fromDate']);
            end($this->segmentdata);
            $lastsegment_todate = strtotime($this->segmentdata[key($this->segmentdata)]['toDate']);

            /* check if itinerary exceed leave time frame (leave from date != segment 1 from date   or leave to date != last segment to date) */
            $leavetimeframe ['leavefromdate'] = strtotime(date('Y-m-d 00:00:00', $leavetimeframe['leavefromdate']));
            $firstsegment_fromdate = strtotime(date('Y-m-d 00:00:00', $firstsegment_fromdate));

            $leavetimeframe['leavetodate'] = strtotime(date('Y-m-d 23:59:59', $leavetimeframe['leavetodate']));
            $lastsegment_todate = strtotime(date('Y-m-d 23:59:59', $lastsegment_todate));

            if(($leavetimeframe ['leavefromdate'] != $firstsegment_fromdate || $leavetimeframe['leavetodate'] != $lastsegment_todate)) {
                //echo '   leavefromdate '.$leavetimeframe['leavefromdate'].' firstsegfrom  '.$firstsegment_fromdate.' leavetodate '.$leavetimeframe['leavetodate'].' $lastsegment_todate '.$lastsegment_todate;
                $this->errorode = 7;
                //return false;
            }

            foreach($this->segmentdata as $sequence => $segmentdata) {
                /* if origin city = to "to city" of previous segment n for each segment */
                if(!empty($this->segmentdata [$sequence - 1]['destinationCity'])) {
                    if($this->segmentdata [$sequence - 1]['destinationCity'] != $segmentdata['originCity']) {
                        $this->errorode = 6;
                        return false;
                    }
                }
                $segmentdata['fromDate'] = strtotime($segmentdata['fromDate']);
                $segmentdata['toDate'] = strtotime($segmentdata['toDate']);
                /* if each segment's from and to are not opposite */
                if($segmentdata['fromDate'] > $segmentdata['toDate']) {
                    $this->errorode = 5;
                    return false;
                }
                if(strtotime($this->segmentdata [$sequence - 1]['toDate']) > $segmentdata['toDate']) {
                    $this->errorode = 5;
                    // return false;
                }
            }
        }
        return true;
    }

    public function create($data = array()) {
        global $db, $core;
        if(is_array($data)) {
            $this->data['lid'] = $data['lid'];
            $leave = new Leaves($this->data['lid']);
            unset($data['lid']);
            if($this->check_isemptyfields($data)) {
                $this->errorode = 2;
                return false;
            }

            $planleavedata['fromdate'] = $leave->get()['fromDate'];
            $planleavedata['todate'] = $leave->get()['toDate'];
            /* function to validate fields */

            if(!$this->check_iteneraryconsistency($data, array('leavefromdate' => $leave->get()['fromDate'], 'leavetodate' => $leave->get()['toDate']))) {
                return false;
            }

            /* Validate first segment and plann */
//            if(value_exists('travelmanager_plan', 'lid', $this->leaveid, 'uid='.$core->user['uid'])) {
//                $db->update_query(self::TABLE_NAME, array('lid' => $this->leaveid, createdOn => TIME_NOW), 'lid='.$db->escape_string($this->leaveid));
//                $this->errorode = 1;
//                return false;
//            }
            $plandata = array('identifier' => substr(md5(uniqid(microtime())), 1, 10),
                    'lid' => $leave->lid,
                    'uid' => $core->user['uid'],
                    'createdBy' => $core->user['uid'],
                    'createdOn' => TIME_NOW
            );
            $db->insert_query('travelmanager_plan', $plandata);
            $this->data[self::PRIMARY_KEY] = $db->last_id();
            $this->segmentdata = $data;
        }
        /* create segment */
        $segment_planobj = new TravelManagerPlanSegments();
        foreach($this->segmentdata as $sequence => $segmentdata) {
            $tmpsegment = new TravelManagerPlanSegments();
            $segmentdata['fromDate'] = strtotime($segmentdata['fromDate']);
            $segmentdata['toDate'] = strtotime($segmentdata['toDate']);
            $segmentdata[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
            $segmentdata['sequence'] = $sequence;
            $tmpsegment->set($segmentdata);
            $tmpsegment->save();
            $this->errorode = $tmpsegment->get_errorcode();
        }
    }

    public function save(array $data = array()) {
        global $core;
        if(empty($data)) {
            $data = $this->data;
        }
//get object of and the id and set data and save
        $latestsplan_obj = TravelManagerPlan::get_plan(array('lid' => $this->data['lid'], 'createdBy' => $core->user['uid']));
        if(is_object($latestsplan_obj)) {
            $this->data['tmpid'] = $latestsplan_obj->get()['tmpid'];
            $this->update($data);
        }
        else {
            $this->create($data);
        }
    }

    public function update($plandata = array()) {
        global $db;

        $segment_planobj = new TravelManagerPlanSegments();
        $this->segmentdata = $plandata;
        $tmpid = $plandata['tmpid'];
        unset($plandata['tmpid']);
        $db->update_query(self::TABLE_NAME, $plandata, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));

        foreach($this->segmentdata as $sequence => $segmentdata) {
            $segment_planobj = new TravelManagerPlanSegments();
            if(isset($segmentdata['lid'])) {
                unset($segmentdata['lid']);
            }
            if(isset($segmentdata['fromDate']) && isset($segmentdata['toDate']) && !empty($tmpid) && isset($segmentdata['sequence'])) {
                $segmentdata['fromDate'] = strtotime($segmentdata['fromDate']);
                $segmentdata['toDate'] = strtotime($segmentdata['toDate']);
            $segmentdata[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
                $segmentdata['sequence'] = $sequence;
            }

            $segment_planobj->set($segmentdata);
            $segment_planobj->save();
            // $segment_planobj->create($segmentdata);
            $this->errorode = $segment_planobj->get_errorcode();
        }
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

    /* segment toDate  between fromDate an toDate of  leave */
    public function isdate_exceededleave($plandata, $segmentdata) {
        $this->leave_datediff = abs($plandata ['todate'] - $plandata['fromdate']);
        $this->leave_days = floor($this->leave_datediff / (60 * 60 * 24 ));

        if(!empty($segmentdata['fromDate']) && !empty($segmentdata['toDate'])) {
            $this->segment_datediff = abs($segmentdata ['toDate'] - $segmentdata['fromDate']);
            $this->segment_days = floor($this->segment_datediff / (60 * 60 * 24));
        }

        /* no save if segment days greater than leave  dates interval */
        if($this->egment_days > $this->
                leave_days) {
            return true;
        }
    }

    public function get_errorcode() {
        return $this->errorode;
    }

    public function get_leave() {
        return new Leaves($this->data['lid'], false);
    }

    public function get_user() {
        return new Users($this->data['uid']);
    }

    public function get_createdBy() {

        return new Users($this->plan['createdBy']);
    }

    public function get_modifiedBy() {
        return new Users($this->data['modifiedBy']);
    }

}