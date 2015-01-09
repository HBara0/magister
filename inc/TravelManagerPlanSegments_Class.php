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
class TravelManagerPlanSegments extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'tmpsid';
    const TABLE_NAME = 'travelmanager_plan_segments';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'tmpid,sequence,fromDate';

    public function __construct($id = '') {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    protected function read($id = '') {
        global $db;
        $this->data = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function create(array $segmentdata) {
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
                'purpose' => $segmentdata['purpose'],
                'reason' => $segmentdata['reason'],
                'destinationCity' => $segmentdata['destinationCity'],
                'createdBy' => $core->user['uid'],
                'sequence' => $segmentdata['sequence'],
                'createdOn' => TIME_NOW
        );

        $db->insert_query(self::TABLE_NAME, $segmentdata_array);
        $this->data[self::PRIMARY_KEY] = $db->last_id();

        if(isset($segmentdata['tmtcid'])) {
            $transptdata['tmpsid'] = $this->data[self::PRIMARY_KEY];

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
                            $transit['paidBy'] = $data['paidBy'];
                            $transit['paidById'] = $data['paidById'];
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
        }

        if(isset($segmentdata['tmhid'])) {
//            if(isset($segmentdata['tmhid']['otherhotel'])) {
//
//                foreach($segmentdata['tmhid']['otherhotel'] as $column => $otherhotel) {
//                    $otherhoteldata['tmhid'] = $segmentdata['tmhid']['otherhotel'];
//                    $otherhoteldata['tmpsid'] = $this->data[self::PRIMARY_KEY];
//                    $otherhoteldata['priceNight'] = $segmentdata['tmhid']['otherhotel']['priceNight'];
//                    $otherhoteldata['numNights'] = $segmentdata['tmhid']['otherhotel']['numNights'];
//                    $otherhoteldata['paidBy'] = $segmentdata['tmhid']['otherhotel']['entites'];
//                    $otherhoteldata['paidById'] = $segmentdata['tmhid']['otherhotel']['paidBy'];
//                    $otherhoteldata = array($column => $otherhotel);
//                    $accod_obj = new TravelManagerPlanaccomodations();
//                    $accod_obj->set($otherhoteldata);
//                }
//            }
            print_r($segmentdata['tmhid']);

            foreach($segmentdata['tmhid'] as $tmhid => $hotel) {
                $hotelacc = TravelManagerPlanaccomodations::get_data('tmhid='.$tmhid);
                /* if hotel not exist in segment accomodation & is not selected Skip! */

                if(!is_object($hotelacc) && empty($hotel[$tmhid])) {
                    continue;
                }
                $hoteldata['tmhid'] = $tmhid;
                $hoteldata['tmpsid'] = $this->data[self::PRIMARY_KEY];
                $hoteldata['priceNight'] = $hotel['priceNight'];
                $hoteldata['currency'] = $hotel['currency'];
                $hoteldata['numNights'] = $hotel['numNights'];
                $hoteldata['paidBy'] = $hotel['entites'];
                $hoteldata['paidById'] = $hotel['paidBy'];
                $accod_obj = new TravelManagerPlanaccomodations();
                $accod_obj->set($hoteldata);
                $accod_obj->save();
            }
        }

        $additionalexpenses = $segmentdata['expenses'];
        if(is_array($additionalexpenses)) {
            foreach($additionalexpenses as $expense) {
                $expensestdata['tmpsid'] = $this->data[self::PRIMARY_KEY];
                $expensestdata['createdBy'] = $core->user['uid'];
                $expensestdata['tmetid'] = $expense['tmetid'];
                $expensestdata['expectedAmt'] = $expense['expectedAmt'];
                $expensestdata['currency'] = $expense['currency'];
                $expensestdata['actualAmt'] = $expense['actualAmt'];
                $expensestdata['description'] = $expense['description'];
                $expensestdata['paidBy'] = $expense['paidBy'];
                $expensestdata['paidById'] = $expense['paidById'];
                $expenses_obj = new Travelmanager_Expenses();
                $expenses_obj->set($expensestdata);
                $expenses_obj->save();
                $this->errorode = 0;
            }
        }
    }

    public function update(array $segmentdata) {
        global $db, $core;
        if(!is_numeric($segmentdata['toDate'])) {
            $segmentdata['toDate'] = strtotime($segmentdata['toDate']);
            $segmentdata['fromDate'] = strtotime($segmentdata['fromDate']);
        }

        $valid_fields = array('fromDate', 'toDate', 'originCity', 'destinationCity', 'reason');
        /* Consider using array intersection */
        foreach($valid_fields as $attr) {
            $segmentnewdata[$attr] = $segmentdata[$attr];
        }

        $segmentnewdata['modifiedBy'] = $core->user['uid'];
        $segmentnewdata['modifiedOn'] = TIME_NOW;

        $db->update_query(self::TABLE_NAME, $segmentnewdata, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));

        $transptdata = $segmentdata['tmtcid'];
        if(is_array($transptdata)) {
            foreach($transptdata as $category => $data) {
                $chkdata = $data;
                rsort($chkdata);
                if(is_array($chkdata[0])) {
                    foreach($data as $id => $transit) {
                        if(!isset($transit['flightNumber'])) {
                            continue;
                        }
                        $transit['paidBy'] = $data['paidBy'];
                        $transit['paidById'] = $data['paidById'];
                        $transp_obj = new TravelManagerPlanTransps();
                        $transit[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
                        $transit['tmtcid'] = $category;

                        $transp_obj->set($transit);
                        $transp_obj->save();
                    }
                    ///// Delete Flight if not checked on modify segment //////
                    $transpseg = TravelManagerPlanTransps::get_data(array('tmtcid' => $category, 'tmpsid' => $this->data[self::PRIMARY_KEY]));
                    if(is_object($transpseg)) {
                        foreach($data as $id => $transit) {
                            if(isset($transit['flightNumber']) && $transpseg->flightNumber == $transit['flightNumber']) {
                                $flights[$id] = $transit['flightNumber'];
                            }
                        }
                        if(!is_array($flights)) {
                            $db->delete_query('travelmanager_plan_transps', 'tmtcid='.$category.' AND tmpsid ='.$this->data['tmpsid'].'');
                        }
                    }
                    ///////////////////////////////////////////////////////
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


        if(is_array($segmentdata['tmhid'])) {
            $segment_hotels['tmhid'] = $segmentdata['tmhid'];

            foreach($segment_hotels['tmhid'] as $tmhid => $hotel) {
                $hotelacc = TravelManagerPlanaccomodations::get_data('tmhid='.$tmhid);
                if(is_object($hotelacc) && (!in_array($hotelacc->tmhid, array_keys($hotel)))) {
                    $db->delete_query('travelmanager_plan_accomodations', 'tmhid='.$tmhid.' AND tmpsid ='.$this->data['tmpsid'].'');
                    continue;
                }
                /* if hotel not exist in segment accomodation & is not selected Skip! */
                if(!is_object($hotelacc) && empty($hotel[$tmhid])) {
                    continue;
                }
                $hoteldata['tmhid'] = $tmhid;
                $hoteldata['tmpsid'] = $this->data[self::PRIMARY_KEY];
                $hoteldata['priceNight'] = $hotel['priceNight'];
                $hoteldata['numNights'] = $hotel['numNights'];
                $hoteldata['paidBy'] = $hotel['entites'];
                $hoteldata['paidById'] = $hotel['paidBy'];
                $accod_obj = new TravelManagerPlanaccomodations();
                $accod_obj->set($hoteldata);
                $accod_obj->save();
            }
        }

        $additionalexpenses = $segmentdata['expenses'];
        if(is_array($additionalexpenses)) {
            foreach($additionalexpenses as $expense) {
                $expensestdata['tmpsid'] = $this->data[self::PRIMARY_KEY];
                $expensestdata['createdBy'] = $core->user['uid'];
                $expensestdata['tmetid'] = $expense['tmetid'];
                $expensestdata['tmeid'] = $expense['tmeid'];
                $expensestdata['expectedAmt'] = $expense['expectedAmt'];
                $expensestdata['currency'] = $expense['currency'];
                $expensestdata['actualAmt'] = $expense['actualAmt'];
                $expensestdata['description'] = $expense['description'];
                $expensestdata['paidBy'] = $expense['paidBy'];
                $expensestdata['paidById'] = $expense['paidById'];
                $expenses_obj = new Travelmanager_Expenses();
                $expenses_obj->set($expensestdata);
                $expenses_obj->save();
                $this->errorode = 0;
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

//    public function save(array $data = array()) {
//        global $core;
//
//        if(empty($data)) {
//            $data = $this->data;
//        }//get object of and the id and set data and save
//        $tmpsegment = TravelManagerPlanSegments::get_segments(array(TravelManagerPlan::PRIMARY_KEY => $data[TravelManagerPlan::PRIMARY_KEY], 'fromDate' => $data['fromDate'], 'toDate' => $data['toDate']));
//        if(is_object($tmpsegment)) {
//            $this->data['tmpsid'] = $tmpsegment->tmpsid;
//            $tmpsegment->update($data);
//        }
//        else {
//            $this->create($data);
//        }
//    }

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
        return TravelManagerPlanTransps::get_data(array('tmpsid' => $this->data[self::PRIMARY_KEY]), array('returnarray' => true));
    }

//    public function get_transportationscat() {
//        /* get the transportations categories of the transportations related to the segment object we call */
//        return TravelManagerTranspCategories::get_data(array('tmtcid' => $this->get_transportations()->tmtcid));
//    }

    public function get_expenses($config = array()) {
        return Travelmanager_Expenses::get_data(array('tmpsid' => $this->data[self::PRIMARY_KEY]), $config);
    }

    private function get_allapidata() {
        return json_decode($this->apiFlightdata);
    }

    public function display_paidby($paidby, $paidbyid) {
        global $core;
        switch($paidby) {
            case "myaffiliate":
                $object = new Affiliates($core->user['mainaffiliate']);
//$paidby = $affiliate->name;
                break;
            case "anotheraff":
                $object = new Affiliates($paidbyid);
// $paidby = $affiliate->name;
                break;
            default:
                $object = $paidby;
        }
        return $object;
    }

    public function parse_segment() {
        global $template, $lang, $core, $db;
        $segmentdate = date('l F d, Y', $this->fromDate);
        $numfmt = new NumberFormatter($lang->settings['locale'], NumberFormatter::CURRENCY);

        $destination_cities = $this->get_origincity()->name.' - '.$this->get_destinationcity()->name;
        $transp_objs = TravelManagerPlanTransps::get_data(array('tmpsid' => $this->data[self::PRIMARY_KEY]), array('returnarray' => true));
        if(is_array($transp_objs)) {
            foreach($transp_objs as $transportation) {
                $transportation->transpType = $transportation->get_transpcategory()->title;
                $paidby = $this->display_paidby($transportation->paidBy, $transportation->paidById);
                if(is_object($paidby)) {
                    $paidby = $paidby->get_displayname();
                }
                if(!empty($transportation->transpDetails)) {
                    $transp_flightdetails = json_decode($transportation->transpDetails, true);
                    $flight_details = $this->parse_flightdetails($transp_flightdetails);
                }
                eval("\$segment_transpdetails .= \"".$template->get('travelmanager_viewplan_transpsegments')."\";");
                $flight_details = '';
            }
        }
        $accomd_objs = TravelManagerPlanaccomodations::get_data(array('tmpsid' => $this->data[self::PRIMARY_KEY]), array('returnarray' => true));

        if(is_array($accomd_objs)) {
            foreach($accomd_objs as $accomdation) {
                $paidby = $this->display_paidby($accomdation->paidBy, $accomdation->paidById);
                if(is_object($paidby)) {
                    $paidby = $paidby->get_displayname();
                }
                $segment_hotel .= '<div style = " width:70%; display: inline-block;"> '.$lang->checkin.' '.$accomdation->get_hotel()->get()['name'].'<span style = "margin:10px;"> '.$lang->night.' '.$accomdation->numNights.' at $ '.$accomdation->priceNight.' '.$lang->night.'</span></div>'; // fix the html parse multiple hotl
//    $segment_hotel .= '<div style = " width:30%; display: inline-block;"> <span> '.$lang->night.' '.$accomdation->numNights.' at $ '.$accomdation->priceNight.' '.$lang->night.'</span></div>'; // fix the html parse multiple hotl
                $segment_hotel .= '<div style = " width:25%; display: inline-block;font-size:14px; font-weight:bold;text-align:right;margin-left:5px;"><span>  '.$numfmt->formatCurrency(($accomdation->numNights * $accomdation->priceNight), "USD").'</span> <br/> <small style="font-weight:normal;">[paid by: '.$paidby.' ]</small></div>'; // fix the html parse multiple hotl
//   $segment_hotelprice .='<div style = " width:45%; display: block;"> Nights '.$accomdation->numNights.' at $ '.$accomdation->priceNight.'/Night</div>';
            }
        }
        $additional_expenses = Travelmanager_Expenses::get_data(array('tmpsid' => $this->tmpsid), array('simple' => false, 'returnarray' => true));
        if(is_array($additional_expenses)) {
            foreach($additional_expenses as $additionalexp) {
                $additionalexp_type = new TravelManager_Expenses_Types($additionalexp->tmetid);
                $additional_expenses_details .= '<div style = "display:block;padding:5px 0px 5px 0px;">';
                $paidby = $this->display_paidby($additionalexp->paidBy, $additionalexp->paidById);
                if(is_object($paidby)) {
                    $paidby = $paidby->get_displayname();
                }
                if($additionalexp_type->title == 'Other') {
                    $additionalexp_type->title = $additionalexp->description;
                }
                $additional_expenses_details .= '<div style = "width:70%;display:inline-block;">'.$additionalexp_type->title.'</div>';
                $additional_expenses_details .= '<div style = "width:25%;display:inline-block;font-size:14px;font-weight:bold;text-align:right;">'.$numfmt->formatCurrency($additionalexp->expectedAmt, "USD").'<br/><small style="font-weight:normal;">[paid by: '.$paidby.' ] </small> </div>';
                $additional_expenses_details .= '</div>';
            }
        }
        eval("\$segment_accomdetails  = \"".$template->get('travelmanager_viewplan_accomsegments')."\";");
        eval("\$segment_details .= \"".$template->get('travelmanager_viewplan_segments')."\";");
        return $segment_details;
    }

    public function parse_expensesummary() {
        global $template, $db, $lang;

        $numfmt = new NumberFormatter($lang->settings['locale'], NumberFormatter::CURRENCY);

        $query = $db->query("SELECT tmpltid, tmtcid, sum(fare) AS fare FROM ".Tprefix."travelmanager_plan_transps WHERE tmpsid IN (SELECT tmpsid FROM travelmanager_plan_segments WHERE tmpid =".intval($this->tmpid).") GROUP By tmtcid");
        if($db->num_rows($query) > 0) {
            while($transpexp = $db->fetch_assoc($query)) {
                $transpcat = new TravelManagerTranspCategories($transpexp['tmtcid']);
                $expenses_details .= '<div style = "display:block;padding:5px 0px 5px 0px;">';
                $expenses_details .= '<div style = "width:85%;display:inline-block;">'.$transpcat->title.'</div>';
                $expenses_details .= '<div style = "width:10%;display:inline-block;text-align:right;">$'.round($transpexp['fare'], 2).'</div>';
                $expenses_details .= '</div>';
                $expenses_total += $transpexp['fare'];
            }
            /* get hotel expences total night of each segment */
        }
        $expenses['accomodation'] = $db->fetch_field($db->query("SELECT SUM(priceNight*numNights) AS total FROM ".Tprefix."travelmanager_plan_accomodations WHERE tmpsid IN (SELECT tmpsid FROM travelmanager_plan_segments WHERE tmpid=".intval($this->tmpid).")"), 'total');
        if(empty($expenses['accomodation'])) {
            $expenses['accomodation'] = 0;
        }
        $expenses_total += $expenses['accomodation'];
        $expenses_subtotal = $numfmt->formatCurrency($expenses_total, "USD");


        $additional_expenses = $db->query("SELECT tmetid,sum(expectedAmt) AS expectedAmt,description FROM ".Tprefix."travelmanager_expenses WHERE tmpsid IN (SELECT tmpsid FROM travelmanager_plan_segments WHERE tmpid =".intval($this->tmpid).") GROUP by tmetid");
        if($db->num_rows($additional_expenses) > 0) {
            $additional_expenses_details = '<div style="display:block;padding:5px 0px 5px 0px;width:15%;" class="subtitle">'.$lang->addexp.'</div>';
            while($additionalexp = $db->fetch_assoc($additional_expenses)) {
                $additionalexp_type = new TravelManager_Expenses_Types($additionalexp['tmetid']);
                $additional_expenses_details .= '<div style = "display:block;padding:5px 0px 5px 0px;">';
                $additional_expenses_details .= '<div style = "width:85%;display:inline-block;">'.$additionalexp_type->title.'</div>';
                $additional_expenses_details .= '<div style = "width:10%;display:inline-block;text-align:right;">'.$numfmt->formatCurrency($additionalexp['expectedAmt'], "USD").'</div>';
                $additional_expenses_details .= '</div>';
                $expenses['additional'] += $additionalexp['expectedAmt'];
            }
            $additional_expenses_details .='<div style="display:block;padding:5px 0px 5px 0px;">';
            $additional_expenses_details .='<div style="display:inline-block;width:85%;">'.$lang->additionalexpensestotal.'</div><div style="width:10%; display:inline-block;text-align:right;font-weight:bold;">  '.$numfmt->formatCurrency($expenses['additional'], "USD").'</div></div>';
            $expenses_total += $expenses['additional'];
        }
        $expenses_total = $numfmt->formatCurrency($expenses_total, "USD");
// $expenses_total = round($expenses_total, 2);
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
                        $connectionduration = '<div class = "display:block; border_top border_bottom" style = "padding: 10px; font-style: italic;">Connection: '.$flight[$segmentnu]['connectionDuration'].'</div>';
                    }
                    for($carriernum = 0; $carriernum < count($allapi_data->trips->data->carrier); $carriernum++) {

                        if($segment['flight'] ['carrier'] == $allapi_data->trips->data->carrier[$carriernum]->code) {
                            $flight['carrier'] = $allapi_data->trips->data->carrier[$carriernum]->name;
                            break;
                        }
                    }
                    $flight_details .='<div style = "width:40%; display:block;">'.$flight['carrier'].'</div>';
                    $flight_details .= '<div style = "width:55%; display:  block;">Departure '.$flight[$segmentnu]['departuretime'].' '.$flight[$segmentnu]['origin'].' Arrival '.$flight[$segmentnu]['arrivaltime'].' '.$flight[$segmentnu]['destination'].'</div>';
                    $flight_details .= $connectionduration;
                    unset($connectionduration, $flight[$segmentnu]['connectionDuration']);
                }
            }
            return $flight_details;
        }
    }

    public function get_accomodations($config = array()) {
        return TravelManagerPlanaccomodations::get_data(array('tmpsid' => $this->data[self::PRIMARY_KEY]), $config);
    }

//
//    public function display_paidby($paidby, $paidbyid) {
//        global $core;
//        switch($paidby) {
//            case "myaffiliate":
//                $object = new Affiliates($core->user['mainaffiliate']);
//                //$paidby = $affiliate->name;
//                break;
//            case "anotheraff":
//                $object = new Affiliates($paidbyid);
//                // $paidby = $affiliate->name;
//                break;
//            default:
//                $object = $paidby;
//        }
//        return $object;
//    }
}
?>