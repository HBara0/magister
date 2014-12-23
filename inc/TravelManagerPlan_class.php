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

    public function parse_paidby($sequence, $cat) {
        global $lang;
        $paidby_entities = array(
                'myaffiliate' => $lang->myaffiliate,
                'supplier' => $lang->supplier,
                'client' => $lang->client,
                'myself' => $lang->myself,
                'anotheraff' => $lang->anotheraff
        );
        return '<div style="display:inline-block;padding:5px;"  id="paidby"> Paid By '.parse_selectlist('segment['.$sequence.'][tmtcid]['.$cat.'][entities]', 6, $paidby_entities, $paidby_entities[$paidby_entities['myaffiliate']], '', '$("#"+$(this).find(":selected").val()+ "_"+'.$sequence.').effect("highlight", {color: "#D6EAAC"}, 1500).find("input").first().focus();', array('id' => 'paidby')).'</div>';
    }

    public static function parse_transportaionfields(array $category, $cityinfo = array(), $sequence) {
        global $lang, $core;

        if(!empty($category['name'])) {
            $paidby_entities = array(
                    'myaffiliate' => $lang->myaffiliate,
                    'supplier' => $lang->supplier,
                    'client' => $lang->client,
                    'myself' => $lang->myself,
                    'anotheraff' => $lang->anotheraff
            );
            switch($category['name']) {
                case 'taxi'://taxi
                    $transportaion_fields = '<div style="padding:3px; display: inline-block; width:50%;">'.$lang->approxfare.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][fare]', 'number', '').'</div>';
                    $transportaion_fields .=self::parse_paidby($sequence, $category['tmtcid']);

                    break;
                case 'bus':
                    $transportaion_fields = '<div style="padding:2px; display: inline-block; width:50%;">'.$lang->approxfare.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][fare]', 'number', '').'</div>';
                    $transportaion_fields .=self::parse_paidby($sequence, $category['tmtcid']);
                    break;
                case 'train':
                case 'lightrail':
                    $transportaion_fields = '<div style="padding:2px; display: inline-block; width:50%;">'.$lang->traino.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][vehicleNumber]', 'number', '').'</div>';
                    $transportaion_fields.=' <div style="padding:2px; display: inline-block; width:45%;">'.$lang->approxfare.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][fare]', 'number', '').'</div>';
                    $transportaion_fields .=self::parse_paidby($sequence, $category['tmtcid']);
                    break;
                case 'airplane':
                    // $availabe_arilinersobjs = TravelManagerAirlines::get_airlines(array('contracted' => '1'));
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
                    if(!is_array($cityinfo['flight']) && empty($cityinfo['flight'])) {
                        $flights = TravelManagerAirlines::get_flights(TravelManagerAirlines::build_flightrequestdata(array('origin' => $cityinfo['origincity']['unlocode'], 'destination' => $cityinfo['destcity']['unlocode'], 'maxStops' => 0, 'date' => $cityinfo['date'], 'permittedCarrier' => $permitted_ariliners)));

                        $transportaion_fields = TravelManagerAirlines::parse_bestflight($flights, array('name' => $category['name'], 'tmtcid' => $category['tmtcid']), $sequence);
                    }
                    //$transportaion_fields .='<div style="display:block;width:100%;"> <div style="display:inline-block;" id="airlinesoptions"> '.$arilinersroptions.' </div>  </div>';
                    //}
                    $transportaion_fields .=self::parse_paidby($sequence, $category['tmtcid']);
                    /* Parse predefined airliners */
                    break;
                case 'car':
                    $transportaion_fields = '<div style="padding:2px; display: inline-block; width:30%;">'.$lang->agency.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][agencyName]', 'text', '').'</div>';
                    $transportaion_fields .= '<div style="padding:2px; display: inline-block; width:30%;">'.$lang->numberdays.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][numDays]', 'number', '').'</div>';
                    $transportaion_fields .= '<div style="padding:2px; display: inline-block; width:30%;">'.$lang->feeday.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][fare]', 'number', '').'</div>';
                    $transportaion_fields .=self::parse_paidby($sequence, $category['tmtcid']);
                    break;
                default:
                    $transportaion_fields = '<div style="padding:3px; display: inline-block; width:45%;">'.$lang->transptype.' '.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][transpType]', 'text', '').'</div>';
                    $transportaion_fields .= '<div style="padding:3px; display: inline-block; width:50%;">'.$lang->feeday.' '.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][fare]', 'number', '').'</div>';
                    break;
            }
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

            if($this->check_isemptyfields($data['segment'])) {
                $this->errorode = 2;
                return false;
            }

            $planleavedata['fromdate'] = $leave->get()['fromDate'];
            $planleavedata['todate'] = $leave->get()['toDate'];
            /* function to validate fields */

            if(!$this->check_iteneraryconsistency($data['segment'], array('leavefromdate' => $leave->get()['fromDate'], 'leavetodate' => $leave->get()['toDate']))) {
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
        }
        /* create segment */
        foreach($data['segment'] as $sequence => $segmentdata) {
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
        unset($data['module'], $data['action'], $data['sequence'], $data['todate'], $data['prevdestcity']);
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

        $segments = $plandata['segment'];
        $valid_attrs = array('uid', 'title', 'createBy', 'createdOn', 'modifiedBy', 'modifiedOn', 'isFinalized');
        $valid_attrs = array_combine($valid_attrs, $valid_attrs);
        $plandata = array_intersect_key($plandata, $valid_attrs);
        if(!empty($plandata)) {
            $db->update_query(self::TABLE_NAME, $plandata, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        }
        if(is_array($segments)) {
            foreach($segments as $sequence => $segmentdata) {
                $segment_planobj = new TravelManagerPlanSegments();
                if(isset($segmentdata['fromDate']) && isset($segmentdata['toDate'])) {
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

    private function parse_leavetypetitle() {
        global $core;
        $leave_obj = $this->get_leave();
        $leave = $leave_obj->get();
        $leave['fromDate_output'] = date($core->settings['dateformat'], $leave['fromDate']);
        $leave['toDate_output'] = date($core->settings['dateformat'], $leave['toDate']);
        $leave['type_output'] = $leave_obj->get_leavetype()->get()['title'];
        $leave_ouput = '  <div class="ui-state-highlight ui-corner-all" style="padding: 5px; font-style: italic;">'.$leave['type_output'].' - '.$leave['fromDate_output'].' - '.$leave['toDate_output'].'</div>';
        return $leave_ouput;
    }

    public function parse_existingsegments() {
        global $lang, $template, $core, $header, $headerinc, $menu;
        $segmentplan_objs = TravelManagerPlanSegments::get_segments(array('tmpid' => $this->tmpid), array('order' => array('by' => 'sequence', 'sort' => 'ASC')));
        $segid = 1;
        $disabled = 'disabled="true"';
        $leave_ouput = $this->parse_leavetypetitle();
        $leaveid = $this->get_leave()->lid;
        foreach($segmentplan_objs as $segmentid => $segmentobj) {
            $segmentstabs .= '<li><a href="#segmentstabs-'.$segid.'">Segment '.$segid.'</a></li>  ';
            $sequence = $segmentobj->sequence;
            $segment[$sequence]['toDate_output'] = date($core->settings['dateformat'], ( $segmentobj->toDate));
            $segment[$sequence]['toDate_formatted'] = date('d-m-Y', ( $segmentobj->toDate));
            $segment[$sequence]['fromDate_output'] = date($core->settings['dateformat'], $segmentobj->fromDate);
            $segment[$sequence]['fromDate_formatted'] = date('d-m-Y', ($segmentobj->fromDate));

            $segment[$sequence]['origincity']['name'] = $segmentobj->get_origincity()->name;
            $segment[$sequence]['origincity']['ciid'] = $segmentobj->get_origincity()->ciid;
            $segment[$sequence]['destinationcity']['name'] = $segmentobj->get_destinationcity()->name;
            $segment[$sequence]['destinationcity']['ciid'] = $segmentobj->get_destinationcity()->ciid;
            $segment[$sequence]['reason'] = $segmentobj->reason;

            //get transp cat send to  parse_transportaionfields
            $transportation_obj = $segmentobj->get_transportationscat();
            $categery['name'] = $transportation_obj->name;
            //$transsegments_output = $this->parse_transportaionfields(array('name' => $transportation_obj->name), array('flight' => $segmentobj->apiFlightdata), $sequence);

            /* parse transportations types --START */
            $seg_transppbj = $segmentobj->get_transportations();
            if(is_array($seg_transppbj)) {
                foreach($seg_transppbj as $transp) {
                    $selectedtransp[] = $transp->tmtcid;

                    //$drivingmode[transpcat][type] = Cities::parse_transportations(array('apiFlightdata' => $segmentobj->apiFlightdata), $sequence);
                    //     $transsegments_output .= Cities::parse_transportations(array('origincity' => $segmentobj->get_origincity()->get(), 'destcity' => $segmentobj->get_destinationcity()->get(), 'departuretime' => $segmentobj->fromDate), $sequence);
                }
            }
            /* parse transportations types --END */

            $cityprofile_output = $segmentobj->get_destinationcity()->parse_cityreviews();
            $citybriefings_output = $segmentobj->get_destinationcity()->parse_citybriefing();


            /* parse hotel --START */
            $hotelssegments_objs = $segmentobj->get_accomodations(array('returnarray' => true));
            if(is_array($hotelssegments_objs)) {

                foreach($hotelssegments_objs as $segmentacc) {
                    $accomodation[$segmentid][$segmentacc->tmhid]['priceNight'] = $segmentacc->priceNight;
                    $accomodation[$segmentid][$segmentacc->tmhid]['numNights'] = $segmentacc->numNights;
                    $accomodation[$segmentid][$segmentacc->tmhid]['paidbyid'] = $segmentacc->paidById;

                    $accomodation[$segmentid][$segmentacc->tmhid]['display'] = "display:none;";
                    $accomodation[$segmentid][$segmentacc->tmhid]['paidby'] = $segmentacc->paidBy;
                    if(isset($accomodation[$segmentid][$segmentacc->tmhid]['paidbyid']) && !empty($accomodation[$segmentid][$segmentacc->tmhid]['paidbyid'])) {
                        $accomodation[$segmentid][$segmentacc->tmhid]['display'] = "display:block;";
                    }

                    $accomodation[$segmentid][$segmentacc->tmhid]['affid'] = $segmentacc->paidById;
                    $accomodation[$segmentid][$segmentacc->tmhid]['affiliate'] = $segmentobj->display_paidby($segmentacc->paidBy, $segmentacc->paidById)->name;

                    $accomodation[$segmentid][$segmentacc->tmhid]['total'] = ($accomodation[$segmentid][$segmentacc->tmhid]['priceNight']) * ($accomodation [$segmentid][$segmentacc->tmhid]['numNights']);
                    $accomodation[$segmentid][$segmentacc->tmhid]['selectedhotel'][] = $segmentacc->tmhid;
                }
            }
            $city_obj = new Cities($segmentobj->get_destinationcity()->ciid);
            $hotelssegments_output = $city_obj->parse_approvedhotels($sequence, $accomodation);
            /* parse hotel --END */

            /* parse expenses --START */
            $segexpenses_ojbs = $segmentobj->get_expenses(array('simple' => false, 'returnarray' => true));
            if(is_array($segexpenses_ojbs)) {

                foreach($segexpenses_ojbs as $rowid => $expenses) {
                    $expensesoptions = Travelmanager_Expenses_Types::get_data();
                    $expensestype[$segmentid][$rowid]['expectedAmt'] = $expenses->expectedAmt;
                    $expensestype[$segmentid][$rowid]['selectedtype'][] = $expenses->tmetid;

                    if(!empty($expenses->paidBy)) {
                        $expensestype[$segmentid][$rowid]['paidby'] = $expenses->paidBy;
                    }
                    if(!empty($expenses->paidById)) {
                        $expensestype[$segmentid][$rowid]['paidbyid'] = $expenses->paidById;
                    }
                    $expensestype[$segmentid][$rowid]['display'] = "display:none;";
                    if(isset($expensestype[$segmentid][$rowid]['paidbyid']) && !empty($expensestype[$segmentid][$rowid]['paidbyid'])) {
                        $expensestype[$segmentid][$rowid]['display'] = "display:block;";
                    }

                    $expensestype[$segmentid][$rowid]['affid'] = $expenses->paidById;
                    $affid = $segmentobj->display_paidby($expenses->paidBy, $expenses->paidById)->affid;
                    $expensestype[$segmentid][$rowid]['affiliate'] = $segmentobj->display_paidby($expenses->paidBy, $expenses->paidById)->name;
                    if(!empty($expenses->description)) {
                        $expensestype[$segmentid][$rowid]['otherdesc'] = $expenses->description;
                    }
                    $segments_expenses_output .= $expenses->get_types()->parse_expensesfield($expensesoptions, $sequence, $rowid, $expensestype);
                }
            }

            /* parse expenses --END */

            eval("\$transsegments_output .= \"".$template->get('travelmanager_plantrip_segment_transptype')."\";");
            eval("\$plansegmentscontent_output = \"".$template->get('travelmanager_plantrip_segmentcontents')."\";");
            unset($segments_expenses_output, $expensestype, $transsegments_output, $accomodation, $selectedhotel);
            eval("\$plantrip_createsegment   = \"".$template->get('travelmanager_plantrip_createsegment')."\";");
            $segments_output .= '<div id="segmentstabs-'.$segid.'">'.$plantrip_createsegment.'</div>';
            $segid++;
        }

        eval("\$plantript_segmentstabs= \"".$template->get('travelmanager_plantrip_segmentstabs')."\";");

        eval("\$plantrip = \"".$template->get('travelmanager_plantrip')."\";");

        return $plantrip;
    }

}