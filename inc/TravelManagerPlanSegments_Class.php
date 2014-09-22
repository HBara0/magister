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
        if(!is_numeric($segmentdata['toDate'])) {
            $segmentdata['toDate'] = strtotime($segmentdata['toDate']);
            $segmentdata['fromDate'] = strtotime($segmentdata['fromDate']);
        }
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
                'reason' => $segmentdata['reason'],
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
                        if(!isset($transit['flightNumber'])) {
                            continue;
                        }
                        $transp_obj = new TravelManagerPlanTransps();
                        $transit[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
                        $transit['tmtcid'] = $category;
                        $transp_obj->set($transit);
                        $transp_obj->save();
                    }
                }
                else {
                    if(isset($data['transpType']) && empty($data['transpType'])) {
                        continue;
                    }
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

        if(!is_numeric($segmentdata['toDate'])) {
            $segmentdata['toDate'] = strtotime($segmentdata['toDate']);
            $segmentdata['fromDate'] = strtotime($segmentdata['fromDate']);
        }

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

    public function get_transportations() {
        return TravelManagerPlanTransps::get_data(array('tmpsid' => $this->data['tmpsid']));
    }

    public function get_transportationscat() {
        /* get the transportations categories of the transportations related to the segment object we call */
        return TravelManagerTranspCategories::get_data(array('tmtcid' => $this->get_transportations()->tmtcid));
    }

    private function get_allapidata() {
        return json_decode($this->apiFlightdata);
    }

    public function parse_segment() {
        global $template, $lang, $core, $db;
        $segmentdate = date('l F d, Y', $this->fromDate);
        $destination_cities = $this->get_origincity()->name.' - '.$this->get_destinationcity()->name;
        $transp_objs = TravelManagerPlanTransps::get_transpsegments(array('tmpsid' => $this->data[self::PRIMARY_KEY]), array('returnarray' => true));
        if(is_array($transp_objs)) {
            foreach($transp_objs as $transportation) {
                $transportation->transpType = $transportation->get_transpcategory()->title;

                if(!empty($transportation->transpDetails)) {
                    $transp_flightdetails = json_decode($transportation->transpDetails, true);
                    $flight_details = $this->parse_flightdetails($transp_flightdetails);
                }
                eval("\$segment_transpdetails .= \"".$template->get('travelmanager_viewplan_transpsegments')."\";");
                $flight_details = '';
            }
        }
        $accomd_objs = TravelManagerPlanaccomodations::get_planaccomodations(array('tmpsid' => $this->data[self::PRIMARY_KEY]));

        if(is_object($accomd_objs)) {
            $segment_hotel = '<div style="width:15%; display: inline-block;">'.$lang->checkin.' '.$accomd_objs->get_hotel()->get()['name'].' </div>'; // fix the html parse multiple hotl
            $segment_hotelprice = '<div style=" width:100%; display: block;">';
            $segment_hotelprice = '<div style=" width:15%; display: inline-block;">'.$lang->night.' '.$accomd_objs->numNights.' at $ '.$accomd_objs->priceNight.'/'.$lang->night.'</div>';
            $segment_hotelprice .=' <div style=" width:25%; margin-left:195px; display: inline-block;font-size:14px; font-weight:bold;"> $ '.($accomd_objs->numNights * $accomd_objs->priceNight).'</div> ';
            $segment_hotelprice .='</div>';
        }
        elseif(is_array($accomd_objs)) {
            foreach($accomd_objs as $accomdation) {
                $segment_hotel .= '<div style=" width:50%; display: inline-block;"> '.$lang->checkin.' '.$accomdation->get_hotel()->get()['name'].'<span style="margin:10px;"> '.$lang->night.' '.$accomdation->numNights.' at $ '.$accomdation->priceNight.' '.$lang->night.'</span></div>'; // fix the html parse multiple hotl
                //    $segment_hotel .= '<div style=" width:30%; display: inline-block;"> <span> '.$lang->night.' '.$accomdation->numNights.' at $ '.$accomdation->priceNight.' '.$lang->night.'</span></div>'; // fix the html parse multiple hotl
                $segment_hotel .= '<div style=" width:50%; display: inline-block;font-size:14px; font-weight:bold;"><span>$'.($accomdation->numNights * $accomdation->priceNight).'</span></div>'; // fix the html parse multiple hotl
                //   $segment_hotelprice .='<div style=" width:45%; display: block;"> Nights '.$accomdation->numNights.' at $ '.$accomdation->priceNight.'/Night</div>';
            }
        }
        $additional_expenses = $db->query("SELECT tmetid,actualAmt FROM ".Tprefix."travelmanager_expenses WHERE tmpsid=".($this->tmpsid));
        if($db->num_rows($additional_expenses) > 0) {
            while($additionalexp = $db->fetch_assoc($additional_expenses)) {
                $additionalexp_type = new TravelManager_Expenses_Types($additionalexp['tmetid']);
                $additional_expenses_details .= '<div style="display:block;padding:5px;">';
                $additional_expenses_details .= '<div style="width:50%;display:inline-block;">'.$additionalexp_type->title.'</div>';
                $additional_expenses_details .= '<div style="width:50%;display:inline-block;font-size:14px; font-weight:bold;">$'.$additionalexp['actualAmt'].'</div>';
                $additional_expenses_details .= '</div>';
            }
        }

        eval("\$segment_accomdetails  = \"".$template->get('travelmanager_viewplan_accomsegments')."\";");
        eval("\$segment_details .= \"".$template->get('travelmanager_viewplan_segments')."\";");

        return $segment_details;
    }

    public function parse_expensesummary() {
        global $template, $db;

        $query = $db->query("SELECT tmpltid, tmtcid, sum(fare) AS fare FROM ".Tprefix."travelmanager_plan_transps WHERE tmpsid IN (SELECT tmpsid FROM travelmanager_plan_segments WHERE tmpid =".intval($this->tmpid).") GROUP By tmtcid");
        if($db->num_rows($query) > 0) {
            while($transpexp = $db->fetch_assoc($query)) {
                $transpcat = new TravelManagerTranspCategories($transpexp['tmtcid']);
                $expenses_details .= '<div style="display:block;padding:5px;">';
                $expenses_details .= '<div style="width:20%;display:inline-block;">'.$transpcat->title.'</div>';
                $expenses_details .= '<div style="width:20%;display:inline-block;">$'.round($transpexp['fare'], 2).'</div>';
                $expenses_details .= '</div>';
                $expenses_total += $transpexp['fare'];
            }
            /* get hotel expences total night of each segment */

            $expenses['accomodation'] = $db->fetch_field($db->query("SELECT SUM(priceNight*numNights) AS total FROM ".Tprefix."travelmanager_plan_accomodations WHERE tmpsid IN (SELECT tmpsid FROM travelmanager_plan_segments WHERE tmpid=".intval($this->tmpid).")"), 'total');
            if(empty($expenses['accomodation'])) {
                $expenses['accomodation'] = 0;
            }
            $expenses_total += $expenses['accomodation'];
        }
        $additional_expenses = $db->query("SELECT tmetid,sum(actualAmt) AS actualAmt FROM ".Tprefix."travelmanager_expenses WHERE tmpsid IN (SELECT tmpsid FROM travelmanager_plan_segments WHERE tmpid =".intval($this->tmpid).") GROUP by tmetid");
        if($db->num_rows($additional_expenses) > 0) {
            while($additionalexp = $db->fetch_assoc($additional_expenses)) {
                $additionalexp_type = new TravelManager_Expenses_Types($additionalexp['tmetid']);
                $additional_expenses_details .= '<div style="display:block;padding:5px;">';
                $additional_expenses_details .= '<div style="width:20%;display:inline-block;">'.$additionalexp_type->title.'</div>';
                $additional_expenses_details .= '<div style="width:20%;display:inline-block;">$'.$additionalexp['actualAmt'].'</div>';
                $additional_expenses_details .= '</div>';
                $expenses['additional'] += $additionalexp['actualAmt'];
            }
            $expenses_total += $expenses['additional'];
        }

        $expenses_total = round($expenses_total, 2);
        eval("\$segment_expenses  = \"".$template->get('travelmanager_viewplan_expenses')."\";");
        return $segment_expenses;
    }

    private function parse_flightdetails($flightdata) {
        global $template, $core;
        $allapi_data = $this->get_allapidata();
        if(is_array($flightdata)) {
// parse flight name
            foreach($flightdata['slice'] as $slicenum => $slice) {
                foreach($slice['segment'] as $segmentnu => $segment) {
                    $flight[$segmentnu]['arrivaltime'] = date($core->settings['dateformat']." H:m", strtotime($segment[leg][0][arrivalTime]));
                    $flight[$segmentnu]['departuretime'] = date($core->settings['dateformat']." H:m", strtotime($segment[leg][0][departureTime]));
                    $flight[$segmentnu]['origin'] = $segment['leg'][0]['origin'];
                    $flight[$segmentnu]['destination'] = $segment['leg'][0]['destination'];
                    if(isset($segment['connectionDuration'])) {
                        $flight[$segmentnu]['connectionDuration'] = sprintf('%2dh %2dm', floor($segment['connectionDuration'] / 60), ($segment['connectionDuration'] % 60));
                        $connectionduration = '<div class="display:block; border_top border_bottom" style="padding: 10px; font-style: italic;">Connection: '.$flight[$segmentnu]['connectionDuration'].'</div>';
                    }
                    for($carriernum = 0; $carriernum < count($allapi_data->trips->data->carrier); $carriernum++) {

                        if($segment['flight'] ['carrier'] == $allapi_data->trips->data->carrier[$carriernum]->code) {
                            $flight['carrier'] = $allapi_data->trips->data->carrier[$carriernum]->name;
                            break;
                        }
                    }
                    $flight_details .='<div style=" width:40%; display:block;">'.$flight['carrier'].'</div>';
                    $flight_details .= '<div style=" width:55%; display:  block;">Departure '.$flight[$segmentnu]['departuretime'].' '.$flight[$segmentnu]['origin'].' Arrival  '.$flight[$segmentnu]['arrivaltime'].' '.$flight[$segmentnu]['destination'].'</div>';
                    $flight_details .= $connectionduration;
                    unset($connectionduration, $flight[$segmentnu]['connectionDuration']);
                }
            }
            return $flight_details;
        }
    }

    public function get_accomodations($config = array()) {
        return TravelManagerPlanaccomodations::get_planaccomodations(array('tmpsid' => $this->data[self::PRIMARY_KEY]), $config);
    }

}
?>