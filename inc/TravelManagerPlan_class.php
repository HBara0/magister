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
        if($directiondata['departuretime'] < TIME_NOW) {
            $directiondata['departuretime'] = TIME_NOW + 3600;
        }
//key = '.$core->settings['googleapikey'].' &

        $googledirection_api = 'http://maps.googleapis.com/maps/api/directions/json?origin='.$directiondata['origincity']['name'].',+'.$directiondata['origincity']['country'].'&destination='.$directiondata['destcity']['name'].',+'.$directiondata['destcity']['country'].'&sensor=false&mode='.$directiondata['drivemode'].'&units=metric&departure_time='.$directiondata['departuretime'];
        //  $json = file_get_contents($googledirection_api);
        //   $data = json_decode($json);
        return $data;
    }

    public static function parse_transportation($transmode, $sequence) {
        global $lang;
        /* The proposed transportation categories   are parsed accordingly with the possible available transportation methods proposed by Google */
        $transporcat_obj = TravelManagerTranspCategories::get_categories_byattr('apiVehicleTypes', $transmode['vehicleType'], array('operator' => 'like'));

        if(is_object($transporcat_obj)) {
            $transportaion_cat = $transporcat_obj->get();
            if(is_array($transportaion_cat)) {
                $selectedtransp = array();
                $categories = array($transportaion_cat['tmtcid'] => $transportaion_cat['title']);
                if(isset($transmode['selectedtransp'][$transportaion_cat['tmtcid']]) && !empty($transmode['selectedtransp'][$transportaion_cat['tmtcid']])) {
                    $selectedtransp['tmtcid'] = $transportaion_cat['tmtcid'];
                    $transpcat['display'] = 'display:block';
                }

                $transpcat['type'] = parse_checkboxes('segment['.$sequence.'][tmtcid]transp_'.$sequence.'_'.$transportaion_cat['name'].'', $categories, $selectedtransp, true, $transportaion_cat['description'], '&nbsp;&nbsp;');
                $transpcat['cateid'] = $transportaion_cat['tmtcid'];
                $transpcat['name'] = $transportaion_cat['name'];
                $transpcat['title'] = $transportaion_cat['title'];
            }

            return $transpcat;
        }
    }

    public function parse_paidby($sequence, $checksum, $selectedoptions = '', $rowid = '') {
        global $lang;
        $paidby_entities = array(
                'myaffiliate' => $lang->myaffiliate,
                'supplier' => $lang->supplier,
                'client' => $lang->client,
                'myself' => $lang->myself,
                'anotheraff' => $lang->anotheraff
        );
        return '<div style="display:inline-block;padding:10px;width:25%;" id="paidby_transp_'.$sequence.'_'.$checksum.'">'.$lang->paidby.'</div><div style="display:inline-block;width:25%;">'.parse_selectlist('segment['.$sequence.'][tmtcid]['.$checksum.'][paidBy]', '', $paidby_entities, $selectedoptions, '', 'if($(this).find(":selected").val()=="anotheraff"){$("#"+$(this).find(":selected").val()+ "_transp_'.$checksum.'_'.$sequence.'").effect("highlight",{ color: "#D6EAAC"}, 1500).find("input").first().focus().val("");}else{$("#anotheraff_transp_'.$checksum.'_'.$sequence.'").hide();}', array('id' => 'paidbylist_transp_'.$sequence.'_'.$checksum.'', 'width' => '100%')).'</div>';
    }

    public static function parse_transportaionfields(TravelManagerPlanTransps $transportation, array $category, $cityinfo = array(), $sequence, $rowid = '') {
        global $lang, $template, $core;

        $mainaffobj = new Affiliates($core->user['mainaffiliate']);
        if(!empty($cityinfo['destcity']['ciid'])) {
            $destcity = $cityinfo['destcity']['ciid'];
        }
        else {
            $destcity = $cityinfo['destcity'];
        }

        $destcity_obj = new Cities($destcity);
        $currencies[] = $destcity_obj->get_country()->get_maincurrency();
        $currencies[] = $mainaffobj->get_country()->get_maincurrency();
        $currencies[] = new Currencies(840, true);
        $currencies[] = new Currencies(978, true);
        foreach($currencies as $currency) {
            if(is_object($currency)) {
                $val_currencies[] = $currency->validate_currency();
            }
        }
        $currencies = array_filter(array_unique($val_currencies));
        $currencies_list = parse_selectlist('segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][currency]', '', $currencies, $transportation->currency, '', '', array('width' => '100%', 'id' => 'currency_'.$sequence.'_'.$category['inputChecksum'].'_list'));


        if(!empty($category['name'])) {
            $paidby_entities = array(
                    'myaffiliate' => $lang->myaffiliate,
                    'supplier' => $lang->supplier,
                    'client' => $lang->client,
                    'myself' => $lang->myself,
                    'anotheraff' => $lang->anotheraff
            );

            //    if($transportation->isRoundTrip == '1') {
            //      $checkroundtrip = 'checked="checked"';
            $rountrip_radiobuttons = parse_radiobutton("segment[".$sequence."][tmtcid][".$category['inputChecksum']."][isRoundTrip]", array('1' => 'Yes', '0' => 'No'), $transportation->isRoundTrip);
            $transportaion_fields_output .='<div><div style="display:inline-block;padding:10px;width:25%;">'.$lang->roundtrip.'</div><div style = "display:inline-block;width:25%;">'
                    .$rountrip_radiobuttons.'</div></div>';
            //  }


            switch($category['name']) {
                case 'taxi'://taxi
                    $transportaion_fields .= '<div style="padding:3px; display: inline-block; width:50%;">'.$lang->approxfare.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][fare]', 'segment_'.$sequence.'_tmtcid_'.$category['tmtcid'].'_fare', 'number', $category['transportationdetials']['fare']).'</div>';
                    $transportaion_fields .='<div><div style="display:inline-block;padding:10px;width:25%;">'.$lang->seatingdescription.'</div><div style = "display:inline-block;width:25%;"><textarea name="segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][seatingDescription]" id="segment_'.$sequence.'_tmtcid_'.$category['tmtcid'].'_seatingdescription"  style="width:100%;>'.$transportation->seatingDescription.'</textarea></div></div>';
                    $selectlists['paidby'] = self::parse_paidby($sequence, $category['tmtcid'], $category['transportationdetials']['paidBy']);
                    $transportaion_fields .= '<input type="hidden" name="segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][tmtcid]" value="'.$category['tmtcid'].'"/>';
//   $transportaion_fields .= '<div style = "display:inline-block;padding:10px;width:15%;">'.$lang->currency.'</div><div style = "display:inline-block;width:20%;">'.$currencies_list.'</div></div>';

                    break;
                case 'bus':
                    $transportaion_fields .= '<div><div style="padding:10px; display: inline-block; width:25%;">'.$lang->approxfare.'</div><div style="display:inline-block;width:25%;">'.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][fare]', 'segment_'.$sequence.'_tmtcid_'.$category['tmtcid'].'_fare', 'number', $category['transportationdetials']['fare']).'</div></div>';
                    $transportaion_fields .='<div><div style="display:inline-block;padding:10px;width:25%;">'.$lang->seatingdescription.'</div><div style = "display:inline-block;width:25%;"><textarea name="segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][seatingDescription]" id="segment_'.$sequence.'_tmtcid_'.$category['tmtcid'].'_seatingdescription" style="width:100%;">'.$transportation->seatingDescription.'</textarea></div></div>';
                    $selectlists['paidby'] = self::parse_paidby($sequence, $category['inputChecksum'], $category['transportationdetials']['paidBy']);
                    $transportaion_fields .= '<input type="hidden" name="segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][tmtcid]" value="'.$category['tmtcid'].'"/>';
//   $transportaion_fields .= '<div style = "display:inline-block;padding:10px;width:15%;">'.$lang->currency.'</div><div style = "display:inline-block;width:20%;">'.$currencies_list.'</div></div>';

                    break;
                case 'train':
                case 'lightrail':
                    $transportaion_fields .= '<div style="padding:2px; display: inline-block; width:25%;padding:10px;">'.$lang->vehiclenumber.'</div><div style="display:inline-block;width:25%;">'.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][vehicleNumber]', 'segment_'.$sequence.'_tmtcid_'.$category['tmtcid'].'_vehicleNumber', 'text', $category['transportationdetials']['vehicleNumber']).'</div>';
                    $transportaion_fields.=' <div style="padding:2px; display: inline-block; width:15%;">'.$lang->approxfare.'</div><div style="display:inline-block;width:25%;">'.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][fare]', 'segment_'.$sequence.'_tmtcid_'.$category['tmtcid'].'_fare', 'number', $category['transportationdetials']['fare']).'</div>';
// $transportaion_fields .= '<input type="hidden" name="segment['.$sequence.'][tmtcid]['.$category['tmtcid'].'][transpType]" value="'.$category['name'].'" />';
                    $transportaion_fields .= '<div><div style = "display:inline-block;padding:10px;width:25%;">'.$lang->currency.'</div><div style = "display:inline-block;width:20%;">'.$currencies_list.'</div></div>';
                    $transportaion_fields .='<div><div style="display:inline-block;padding:10px;width:25%;">'.$lang->seatingdescription.'</div><div style = "display:inline-block;width:25%;"><textarea name="segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][seatingDescription]" id="segment_'.$sequence.'_tmtcid_'.$category['tmtcid'].'_seatingdescription" style="width:100%;">'.$transportation->seatingDescription.'</textarea></div></div>';
                    $selectlists['paidby'] = self::parse_paidby($sequence, $category['inputChecksum'], $category['transportationdetials']['paidBy']);
                    $transportaion_fields .= '<input type="hidden" name="segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][tmtcid]" value="'.$category['tmtcid'].'"/>';

                    break;
                case 'airplane':

                    if(is_array($availabe_arilinersobjs)) {
                        foreach($availabe_arilinersobjs as $availabe_arilinersobj) {
                            $permitted_ariliners[] = $availabe_arilinersobj->iatacode;
                        }
                    }

                    /* parse request array for the allowed airlines  and encode it as json array */

                    if(!empty($transportation->transpDetails)) {
                        $transportaion_fields .= '<h2><small>Selected Flight</small></h2>';
                        $transportaion_fields .= TravelManagerAirlines::parse_bestflight($transportation->transpDetails, array('transportationdetails' => $category['transportationdetials'], 'selectedflight' => $transportation->flightNumber, 'name' => $category['name'], 'tmtcid' => $category['tmtcid'], 'inputChecksum' => $category['inputChecksum']), $sequence, 'plan', array('isMinCost' => $transportation->isMinCost));
                        $transportaion_fields .= '<br /><hr/><br/>';
                    }
                    else {
                        $button = '<button type="button" id="airflights_button_'.$sequence.'" style="float: right" class="button">'.$lang->minimizemaximize.'</button>';
                        $transportaion_fields .= '<h2><small>Possible Flights</small></h2>'.$button.'<br/>';
                        $flights = TravelManagerAirlines::get_flights(TravelManagerAirlines::build_flightrequestdata(array('origin' => $cityinfo['origincity']['unlocode'], 'destination' => $cityinfo['destcity']['unlocode'], 'date' => $cityinfo['date'], 'arrivaldate' => $cityinfo['arrivaldate'], 'isOneway' => $cityinfo['isOneway'], 'permittedCarrier' => $permitted_ariliners)));
                        $transportaion_fields .= '<input name = "segment['.$sequence.'][apiFlightdata]" id = "segment_'.$sequence.'apiFlightdata" type = "hidden" value = \''.$flights.'\' />';
                        $transportaion_fields .= TravelManagerAirlines::parse_bestflight($flights, array('transportationdetails' => $category['transportationdetials'], 'selectedflight' => $category['transportationdetials']['flightNumber'], 'name' => $category['name'], 'tmtcid' => $category['tmtcid']), $sequence);
                    }
//$transportaion_fields .='<div style="display:block;width:100%;"> <div style="display:inline-block;" id="airlinesoptions"> '.$arilinersroptions.' </div>  </div>';
//}
                    /* Parse predefined airliners */
                    break;
                case 'car':
                    $transportaion_fields .= '<div style="padding:10px; display: inline-block; width:25%;">'.$lang->agency.'</div><div style="display:inline-block;width:25%;">'.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][agencyName]', 'segment_'.$sequence.'_tmtcid_'.$category['tmtcid'].'_agencyName', 'text', $category['transportationdetials']['agencyName'], array('style' => 'width:100%;', 'tabindex' => '2;')).'</div>';
                    $transportaion_fields .= '<div style="padding:10px; display: inline-block; width:15%;">'.$lang->numberdays.'</div><div style="display:inline-block;width:20%;">'.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][numDays]', 'segment_'.$sequence.'_tmtcid_'.$category['tmtcid'].'_numDays', 'number', $category['transportationdetials']['transpType'], array('tabindex' => '2;')).'</div>';
                    $transportaion_fields .= '<div><div style = "display:inline-block;padding:10px;width:25%;">'.$lang->feeday.'</div><div style = "display:inline-block;width:25%;">'.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][fare]', 'segment_'.$sequence.'_tmtcid_'.$category['tmtcid'].'_fare', 'number', $category['transportationdetials']['fare'], array('style' => 'width:100%;', 'tabindex' => '2;')).'</div>';
                    $transportaion_fields .= '<div style = "display:inline-block;padding:10px;width:15%;">'.$lang->currency.'</div><div style = "display:inline-block;width:20%;">'.$currencies_list.'</div></div>';
                    $transportaion_fields .='<div><div style="display:inline-block;padding:10px;width:25%;">'.$lang->seatingdescription.'</div><div style = "display:inline-block;width:25%;"><textarea name="segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][seatingDescription]" id="segment_'.$sequence.'_tmtcid_'.$category['tmtcid'].'_seatingdescription"  style="width:100%;>'.$transportation->seatingDescription.'</textarea></div></div>';
                    $selectlists['paidby'] = self::parse_paidby($sequence, $category['inputChecksum'], $category['transportationdetials']['paidBy']);
                    $transportaion_fields .= '<input type="hidden" name="segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][tmtcid]" value="'.$category['tmtcid'].'"/>';
                    break;
                default:
                    $transportaion_fields.='<hr>';
                    $transportaion_fields .= '<div><div style="display:inline-block;padding:10px;width:25%;">'.$lang->transptype.'</div><div style="display:inline-block;width:25%;">'.parse_selectlist('segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][tmtcid]', '2', $category['othercategories'], $transportation->tmtcid, '', '', array('width' => '100%', 'id' => 'segment_'.$sequence.'_tmtcid_'.$category['inputChecksum'].'_othercategory', 'blankstart' => true, 'data_attribute' => 'data-reqparent="children-segment_'.$sequence.'_tmtcid_'.$category['tmtcid'].'_fare" ')).'</div>';
                    $transportaion_fields .= '<div style = "display:inline-block;padding:10px;width:15%;">'.$lang->vehiclenumber.'</div><div style = "display:inline-block;width:20%;">'.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][vehicleNumber]', 'segment_'.$sequence.'_tmtcid_'.$category['tmtcid'].'_vehicleNumber', 'text', $transportation->vehicleNumber, array('style' => 'width:100%;', 'tabindex' => '2;')).'</div></div>';
                    $transportaion_fields .= '<div><div style = "display:inline-block;padding:10px;width:25%;">'.$lang->feeday.'</div><div style = "display:inline-block;width:25%;">'.parse_textfield('segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][fare]', 'segment_'.$sequence.'_tmtcid_'.$category['tmtcid'].'_fare', 'number', $transportation->fare, array('style' => 'width:100%;', 'tabindex' => '2;')).'</div>';
                    $transportaion_fields .= '<div style = "display:inline-block;padding:10px;width:15%;">'.$lang->currency.'</div><div style = "display:inline-block;width:20%;">'.$currencies_list.'</div></div>';
                    $classes = TravelManagerPlanTranspClass::get_data('name is not null', array('returnarray' => true));
                    $transportaion_fields .='<div><div style="display:inline-block;padding:10px;width:25%;">'.$lang->class.'</div><div style="display:inline-block;width:25%;">'.parse_selectlist('segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][class]', '2', $classes, $transportation->class, '', '', array('width' => '100%', 'id' => 'segment_'.$sequence.'_tmtcid_'.$category['inputChecksum'].'_class')).'</div></div>';
                    $transportaion_fields.=$transportaion_fields_output;
                    $transportaion_fields .='<div><div style="display:inline-block;padding:10px;width:25%;">'.$lang->seatingdescription.'</div><div style = "display:inline-block;width:25%;"><textarea tabindex= 2 name="segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][seatingDescription]" id="segment_'.$sequence.'_tmtcid_'.$category['tmtcid'].'_seatingdescription" style="width:100%;">'.$transportation->seatingDescription.'</textarea></div>';
                    $transportaion_fields .='<div style="display:inline-block;padding:10px;width:35%;margin-left:10px;" id="transpclass_warning_'.$sequence.'_'.$category['inputChecksum'].'"></div></div>';
                    $transportaion_fields .='<div><div style="display:inline-block;padding:10px;width:25%;">'.$lang->stopdescription.'</div><div style = "display:inline-block;width:25%;"><textarea tabindex=2 name="segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][stopDescription]" id="segment_'.$sequence.'_tmtcid_'.$category['tmtcid'].'_stopDescription" style="width:100%;">'.$transportation->stopDescription.'</textarea></div></div>';
                    $selectlists['paidby'] = self::parse_paidby($sequence, $category['inputChecksum'], $transportation->paidBy);
                    $transportaion_fields .= '<input type = "hidden" name = "segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][isUserSuggested]" value = "1"/>';
                    break;
            }


            if($category['name'] != 'airplane') {

                $transportaion_fields .= '<div style = "position: absolute; top: 1px; right: 1px;"><input type = "checkbox" label = "'.$lang->delete.'" class = "deletecheckbox" title = "'.$lang->deletecheckboxnote.'" value = "1" id = "segment_'.$sequence.'_tmtcid_'.$category['inputChecksum'].'_todelete" name = "segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][todelete]" /><label for = "segment_'.$sequence.'_tmtcid_'.$category['inputChecksum'].'_todelete">&nbsp;
                    </label></div>';
                $transportaion_fields .= '<input type = "hidden" name = "segment['.$sequence.'][tmtcid]['.$category['inputChecksum'].'][inputChecksum]" value = "'.$category['inputChecksum'].'"/>';
            }
            if(!empty($selectlists['paidby'])) {
                $transportation_details[$sequence][$category['inputChecksum']]['display'] = 'display:none;';

                if(!empty($transportation->paidById)) {

                    $transportation_details[$sequence][$category['inputChecksum']]['display'] = 'display:block;';
                    $transpseg = new TravelManagerPlanSegments();
                    $transportation_details[$sequence][$category['inputChecksum']]['affiliate'] = $transpseg->display_paidby($transportation->paidBy, $transportation->paidById)->name;
                }
                eval("\$transportaion_fields .= \"".$template->get('travelmanager_plantrip_segment_paidbyfields')."\";");
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

    private function check_iteneraryconsistency($requiredata = array(), $leavetimeframe = array(), $finalize = 0) {
        if(is_array($requiredata)) {
            $this->segmentdata = $requiredata;
            $firstsegment_fromdate = strtotime($this->segmentdata[key($this->segmentdata)]['fromDate']);
//  end($this->segmentdata);
            $lastsegment_todate = strtotime($this->segmentdata[end(array_keys($this->segmentdata))]['toDate']);

            /* check if itinerary exceed leave time frame (leave from date != segment 1 from date   or leave to date != last segment to date) */
            $leavetimeframe ['leavefromdate'] = strtotime(date('Y-m-d 00:00:00', $leavetimeframe['leavefromdate']));
            $firstsegment_fromdate = strtotime(date('Y-m-d 00:00:00', $firstsegment_fromdate));

            $leavetimeframe['leavetodate'] = strtotime(date('Y-m-d 23:59:59', $leavetimeframe['leavetodate']));
            $lastsegment_todate = strtotime(date('Y-m-d 23:59:59', $lastsegment_todate));
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
                if((strtotime($this->segmentdata[$sequence - 1]['toDate']) > $segmentdata['toDate'])) {
                    $this->errorode = 5;
                    return false;
                }
            }
            if($leavetimeframe['leavefromdate'] != $firstsegment_fromdate || $leavetimeframe['leavetodate'] != $lastsegment_todate) {
//  echo ' leavefromdate '.$leavetimeframe['leavefromdate'].' firstsegfrom '.$firstsegment_fromdate.' leavetodate '.$leavetimeframe['leavetodate'].' $lastsegment_todate '.$lastsegment_todate;
                if($leavetimeframe['leavetodate'] < $lastsegment_todate) {
                    $this->errorode = 7;
                    return false;
                }
                if($leavetimeframe['leavetodate'] > $lastsegment_todate) {
                    if(strlen($this->saveaddseg) < 1 || $this->saveaddseg != $this->sequence) {
                        $this->errorode = 8;
                        if($finalize == 1) {
                            return false;
                        }
                    }
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
        global $db, $core;
        $this->data['lid'] = $plandata['lid'];
        $leave = new Leaves($this->data['lid']);
        if(!$this->check_iteneraryconsistency($plandata['segment'], array('leavefromdate' => $leave->get()['fromDate'], 'leavetodate' => $leave->get()['toDate']), $this->data['finalizeplan'])) {
            return false;
        }
        $segments = $plandata['segment'];
        $valid_attrs = array('uid', 'title', 'modifiedBy', 'modifiedOn', 'isFinalized');
        $valid_attrs = array_combine($valid_attrs, $valid_attrs);
        $plandata = array_intersect_key($plandata, $valid_attrs);
        $plandata['modifiedBy'] = $core->user['uid'];
        $plandata['modifiedOn'] = TIME_NOW;
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
                $segment_planobj = $segment_planobj->save();
// $segment_planobj->create($segmentdata);
                $this->errorode = $segment_planobj->get_errorcode();
                if($this->errorode != 0) {
                    return $this;
                }
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
        if(!is_array($this->data)) {
            return false;
        }
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
        $planid = $this->tmpid;
        $segmentplan_objs = TravelManagerPlanSegments::get_segments(array('tmpid' => $this->tmpid), array('returnarray' => true, 'order' => array('by' => 'sequence', 'sort' => 'ASC')));
        $segid = 1;
// $disabled = 'disabled="true"';
        $section = '';
        $leave_ouput = $this->parse_leavetypetitle();
        $leaveid = $this->get_leave()->lid;
        $leave_purposes = LeaveTypesPurposes::get_data('', array('returnarray' => true));
        if(!is_array($segmentplan_objs)) {
            redirect('index.php?module=travelmanager/listplans');
        }
        foreach($segmentplan_objs as $segmentid => $segmentobj) {
            $sequence = $segmentobj->sequence;
            $delete_tabicon = '<span class="ui-icon ui-icon-close" id="deleteseg_'.$segmentid.'"role="presentation" title="Close">Remove Tab</span>';

            if($sequence == 1) {
                $disabledfields[$sequence]['destinationcity'] = ' disabled="disabled" ';
                $delete_tabicon = '';
            }

            $segmentstabs .= '<li><a href="#segmentstabs-'.$segid.'">segment_'.$sequence.'</a>'.$delete_tabicon.'</li>  ';

            $segment[$sequence]['toDate_output'] = date($core->settings['dateformat'], ( $segmentobj->toDate));
            $segment[$sequence]['toDate_formatted'] = date('d-m-Y', ( $segmentobj->toDate));
            $segment[$sequence]['fromDate_output'] = date($core->settings['dateformat'], $segmentobj->fromDate);
            $segment[$sequence]['fromDate_formatted'] = date('d-m-Y', ($segmentobj->fromDate));
            $segment[$sequence]['origincity']['name'] = $segmentobj->get_origincity()->name;
            $segment[$sequence]['origincity']['ciid'] = $segmentobj->get_origincity()->ciid;
            $segment[$sequence]['destinationcity']['name'] = $segmentobj->get_destinationcity()->name;
            $segment[$sequence]['destinationcity']['ciid'] = $segmentobj->get_destinationcity()->ciid;
            $segment[$sequence]['reason_output'] = $segmentobj->reason;
            if(!empty($segmentobj->affid) || !empty($segmentobj->eid)) {
                $checked['specifyentcheck'] = 'checked="checked"';
            }
            if(!empty($segmentobj->eid)) {
                $ent = new Entities($segmentobj->eid);
                $segment[$sequence]['entity']['name'] = $ent->get_displayname();
                $segment[$sequence]['entity']['eid'] = $segmentobj->eid;
            }

            unset($checked['isNoneBusiness']);
            if(isset($segmentobj->isNoneBusiness) && !empty($segmentobj->isNoneBusiness)) {
                $checked['isNoneBusiness'] = ' checked = "checked"';
            }
            $display_external = $display_internal = 'style="display:none"';
            $segmentpurposes = TravelManagerPlanSegPurposes::get_data(array('tmpsid' => $segmentobj->tmpsid), array('returnarray' => true, 'simple' => false));
            if(is_array($segmentpurposes)) {
                foreach($segmentpurposes as $segmentpurpose) {
                    $selectedpurpose[$segmentpurpose->purpose] = $segmentpurpose->purpose;
                    $purpose = new LeaveTypesPurposes($segmentpurpose->purpose, false);
                    if($purpose->category == 'internal') {
                        $display_internal = '';
                    }
                    elseif($purpose->category == 'external') {
                        $display_external = '';
                    }
                    unset($purpose);
                }

                $affient_objs = TravelManagerPlanAffient::get_data(array('tmpsid' => $segmentobj->tmpsid), array('returnarray' => true));
                if(is_array($affient_objs)) {
                    $affrowid = $entrowid = 1;
                    foreach($affient_objs as $affient_obj) {
                        if($affient_obj->get_type() == 'affiliate') {
                            $affiliates = Affiliates::get_affiliates(array('isActive' => '1'));
                            $afent_checksum = $affient_obj->inputChecksum;
                            $affilate_list = parse_selectlist('segment['.$sequence.'][assign][affid]['.$afent_checksum.']', '1', $affiliates, $affient_obj->primaryId, '', '', array('blankstart' => true));
                            eval("\$affiliates_output .= \"".$template->get('travelmanager_plantrip_createsegment_affiliates')."\";");
                            $affrowid++;
                            unset($afent_checksum);
                        }
                        elseif($affient_obj->get_type() == 'entity') {
                            $entityid = $affient_obj->primaryId;
                            $afent_checksum = $affient_obj->inputChecksum;
                            $entityname = $affient_obj->get_entity()->get_displayname();
                            eval("\$entities .= \"".$template->get('travelmanager_plantrip_createsegment_entities')."\";");
                            $entrowid++;
                            unset($entityid, $entityname, $afent_checksum);
                        }
                        elseif($affient_obj->get_type() == 'event') {
                            $ltpurpose = 'event';
                            $eventid = $affient_obj->primaryId;
                            $afent_checksum = $affient_obj->inputChecksum;
                            $calevents = Events::get_data(array('isPublic' => 1), array('returnarray' => true));
                            $events = '<td '.$display_external.'>'.$lang->selectevent.'</td><td '.$display_external.'>';
                            $events .= parse_selectlist("segment[{$sequence}][assign][ceid][".$afent_checksum."]", $tabindex, $calevents, $eventid, '', '', array('width' => '200px'));
                            $events .='</td>';
                            // eval("\$entities .= \"".$template->get('travelmanager_plantrip_createsegment_entities')."\";");
                            $entrowid++;
                            unset($entityid, $entityname, $afent_checksum);
                        }
                    }
                }
            }
            if(is_array($leave_purposes)) {
                foreach($leave_purposes as $leave_purpose) {
                    if($leave_purpose->category == 'internal') {
                        $interperp[$leave_purpose->ltpid] = $leave_purpose->title;
                    }
                    elseif($leave_purpose->category == 'external') {
                        $extpurps[$leave_purpose->ltpid] = $leave_purpose->title;
                    }
                }
                if(is_array($extpurps)) {
                    $extpurposes_checks = parse_checkboxes('segment['.$sequence.'][purpose]', $extpurps, $selectedpurpose, '', 'external purposes', '<br>', 'purposes_checks_external_'.$sequence.'', 1);
                }
                if(is_array($interperp)) {
                    $internalpurposes_checks = parse_checkboxes('segment['.$sequence.'][purpose]', $interperp, $selectedpurpose, '', 'internal purposes', '<br>', 'purposes_checks_internal_'.$sequence.'', 1);
                }
            }
            $affiliates = Affiliates::get_affiliates(array('isActive' => '1'));
            $afent_checksum = generate_checksum();
            $affilate_list = parse_selectlist('segment['.$sequence.'][assign][affid]['.$afent_checksum.']', '1', $affiliates, '', '', '', array('blankstart' => true));
            $affrowid = $entrowid = 0;
            eval("\$affiliates_output .= \"".$template->get('travelmanager_plantrip_createsegment_affiliates')."\";");
            $afent_checksum = generate_checksum();
            //   if($ltpurpose != 'event') {
            eval("\$entities.= \"".$template->get('travelmanager_plantrip_createsegment_entities')."\";");
            //   }
            if($ltpurpose == 'event') {
                $calevents = Events::get_data(array('isPublic' => 1), array('returnarray' => true));
                $events .= '<tr id="'.$entrowid.'"><td '.$display_external.' data-purposes="external_'.$sequence.'">Select Event</td><td '.$display_external.' data-purposes="external_'.$sequence.'">';
                $events .= parse_selectlist("test", $tabindex, $calevents, $selected_options, '', '', array('width' => '200px', 'blankstart' => true));
                $events .='</td></tr>';
            }
            unset($afent_checksum, $selectedpurpose);
            //get transp cat send to  parse_transportaionfields
//            $transportation_obj = $segmentobj->get_transportationscat();
//            $categery['name'] = $transportation_obj->name;
//            $transp = new TravelManagerPlanTransps();
//            $transsegments_output = $this->parse_transportaionfields($transp, array('flight' => $segmentobj->apiFlightdata), $sequence);

            /* parse transportations types --START */
            $seg_transppbj = $segmentobj->get_transportations();
            $destcity = $segmentobj->get_destinationcity()->get();
            $destcityid = $destcity['ciid'];
            $transp_requirements['drivemode'] = 'transit';
            $transp_requirements['departuretime'] = $segmentobj->fromDate;
            $transsegments_output = Cities::parse_transportations($seg_transppbj, array('transportationdetails' => $transportation_details, 'segment' => $segmentobj, 'origincity' => $segmentobj->get_origincity()->get(), 'destcity' => $destcity, 'transprequirements' => $transp_requirements, 'excludesuggestions' => 1), $sequence);

            unset($transp_requirements);
            /* parse transportations types --END */

            $cityprofile_output = $segmentobj->get_destinationcity()->parse_cityreviews();
            $citybriefings_output = $segmentobj->get_destinationcity()->parse_citybriefing();

            /* parse hotel --START */
            $hotelssegments_objs = $segmentobj->get_accomodations(array('returnarray' => true));

            // $date1 = date_create(($this->get_leave()->fromDate));
            // $date2 = date_create(($this->get_leave()->toDate));
            // $leavedays = date_diff($date1, $date2);
//            if(is_array($hotelssegments_objs)) {
//                foreach($hotelssegments_objs as $segmentacc) {
//                    $accomodation[$segmentid][$segmentacc->tmhid]['hotelsection'] = $section.' '.$lang->hotel;
//                    $accomodation[$segmentid][$segmentacc->tmhid]['hotelsectioncolor'] = "background-color:".$bgcolor."; ";
//                    $accomodation[$segmentid][$segmentacc->tmhid]['priceNight'] = $segmentacc->priceNight;
//                    $accomodation[$segmentid][$segmentacc->tmhid]['numNights'] = $segmentacc->numNights;
//                    $accomodation[$segmentid][$segmentacc->tmhid]['inputChecksum'] = $segmentacc->inputChecksum;
//                    $accomodation[$segmentid][$segmentacc->tmhid]['paidbyid'] = $segmentacc->paidById;
//                    $accomodation[$segmentid][$segmentacc->tmhid]['display'] = "display:none;";
//                    $accomodation[$segmentid][$segmentacc->tmhid]['paidby'] = $segmentacc->paidBy;
//                    $acc_currobj = new Currencies($segmentacc->currency);
//                    $accomodation[$segmentid][$segmentacc->tmhid]['currency'] = $acc_currobj->numCode;
//
//                    if(isset($accomodation[$segmentid][$segmentacc->tmhid]['paidbyid']) && !empty($accomodation[$segmentid][$segmentacc->tmhid]['paidbyid'])) {
//                        $accomodation[$segmentid][$segmentacc->tmhid]['display'] = "display:block;";
//                    }
//                    $accomodation[$segmentid][$segmentacc->tmhid]['affid'] = $segmentacc->paidById;
//                    $accomodation[$segmentid][$segmentacc->tmhid]['affiliate'] = $segmentobj->display_paidby($segmentacc->paidBy, $segmentacc->paidById)->name;
//                    $accomodation[$segmentid][$segmentacc->tmhid]['total'] = ($accomodation[$segmentid][$segmentacc->tmhid]['priceNight']) * ($accomodation [$segmentid][$segmentacc->tmhid]['numNights']);
////                    $accomodation[$segmentid][$segmentacc->tmhid]['selectedhotel'] = $segmentacc->tmhid;
//                }
//            }

            $city_obj = new Cities($segmentobj->get_destinationcity()->ciid);
            $counrty_obj = $city_obj->get_country();

            $approvedhotels = $city_obj->get_approvedhotels();
            if(is_array($approvedhotels)) {
                $hotelssegments_output = '<h2><small>Approved Hotels</small></h2>';
            }
            if(empty($approvedhotels)) {
                $approvedhotels = array();
            }
            if(is_object($counrty_obj) && !empty($counrty_obj->coid)) {
                $otherapprovedhotels = TravelManagerHotels::get_data('country='.$counrty_obj->coid.' AND city != '.$city_obj->ciid.' AND isApproved=1', array('returnarray' => true));
            }
            $leavedays = abs($segmentobj->toDate - $segmentobj->fromDate);
            $leavedays = floor($leavedays / (60 * 60 * 24));
            $hotelssegments_output .= $segmentobj->parse_hotels($sequence, $approvedhotels, $leavedays);
            if(is_array($otherapprovedhotels)) {
                $hotelssegments_output.='<br /><a nohref="nohref" style="cursor:pointer;" id="countryhotels_'.$sequence.'_check"><div style="display:inline-block"><button type="button" class="button">Lookup Hotels In The Same Country</button></div></a>';
                $hotelssegments_output.='<div id=countryhotels_'.$sequence.'_view style="display:none">';
                $hotelssegments_output.=$segmentobj->parse_hotels($sequence, $otherapprovedhotels, $leavedays);
                $hotelssegments_output.='</div>';
            }
            $hotelssegments_output .= '<br /><h2><small>Other Possible Hotels</small></h2>';
            $otherhotels = TravelManagerHotels::get_data(array('country' => $counrty_obj->coid, 'tmhid' => '(SELECT tmhid FROM '.TravelManagerPlanaccomodations::TABLE_NAME.' WHERE '.TravelManagerPlanSegments::PRIMARY_KEY.' = '.$segmentobj->get_id().')', 'isApproved' => 0), array('returnarray' => true, 'operators' => array('tmhid' => 'IN')));
            if(is_array($otherhotels)) {
                $hotelssegments_output .= $segmentobj->parse_hotels($sequence, $otherhotels, $leavedays);
            }
            /* parse hotel --END */
            $otherhotel_checksum = generate_checksum('accomodation');
            $paidby_entities = array(
                    'myaffiliate' => $lang->myaffiliate,
                    'supplier' => $lang->supplier,
                    'client' => $lang->client,
                    'myself' => $lang->myself,
                    'anotheraff' => $lang->anotheraff
            );
            $otherhotel['displaystatus'] = "display:none;";
            $paidby_onchangeactions = 'if($(this).find(":selected").val()=="anotheraff"){$("#"+$(this).find(":selected").val()+"_otheraccomodations_'.$sequence.'_'.$otherhotel_checksum.'").effect("highlight", { color: "#D6EAAC"}, 1500).find("input").first().focus().val("");
}
else {
$("#anotheraff_otheraccomodations_'.$sequence.'_'.$otherhotel_checksum.'").hide();
}';
            $paidbyoptions = parse_selectlist('segment['.$sequence.'][tmhid]['.$otherhotel_checksum.'][entites]', '3', $paidby_entities, $selectedhotel->paidBy, 0, $paidby_onchangeactions);
            $mainaffobj = new Affiliates($core->user['mainaffiliate']);
            $destcity_obj = new Cities($destcity['ciid']);
            $dest_country = $destcity_obj->get_country();
            $destcountry_id = $dest_country->coid;
            $currencies[] = $dest_country->get_maincurrency();
            $currencies[] = $mainaffobj->get_country()->get_maincurrency();
            $currencies[] = new Currencies(840, true);
            $currencies[] = new Currencies(978, true);
            foreach($currencies as $currency) {
                if(is_object($currency)) {
                    $val_currencies[] = $currency->validate_currency();
                }
            }
            $currencies = array_filter(array_unique($val_currencies));
            $currencies_list = parse_selectlist('segment['.$sequence.'][tmhid]['.$otherhotel_checksum.'][currency]', '3', $currencies, '840', '', '', array('id' => 'currency_'.$sequence.'_'.$otherhotel_checksum.'_list'));
            $leavedays = abs($segmentobj->toDate - $segmentobj->fromDate);
            $leavedays = floor($leavedays / (60 * 60 * 24));
            eval("\$otherhotels_output = \"".$template->get('travelmanager_plantrip_segment_otherhotels')."\";");
            /* parse expenses --START */
            $segexpenses_ojbs = $segmentobj->get_expenses(array('simple' => false, 'returnarray' => true, 'order' => array('by' => 'tmeid', 'sort' => 'ASC')));
            $rowid = 0;
            if(is_array($segexpenses_ojbs)) {
                foreach($segexpenses_ojbs as $rowid => $expenses) {
                    $expensesoptions = Travelmanager_Expenses_Types::get_data();
                    $expensestype[$segmentid][$rowid]['tmeid'] = $expenses->tmeid;
                    $expensestype[$segmentid][$rowid]['expectedAmt'] = $expenses->expectedAmt;
                    $expensestype[$segmentid][$rowid]['selectedtype'][] = $expenses->tmetid;
                    $expensestype[$segmentid][$rowid]['currency'][] = $expenses->currency;

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
                    if(!empty($expenses->description) && $expenses->tmetid == '4') {
                        $expensestype[$segmentid][$rowid]['otherdesc'] = $expenses->description;
                    }
                    $segments_expenses_output .= $expenses->get_types()->parse_expensesfield($expensesoptions, $sequence, $rowid, $expensestype, array('destcity' => $destcity_obj), $segmentid);
                }
            }
            else {
                $rowid = 1;
                $expensestype[$sequence][$rowid]['display'] = "display:none;";
                $expensestype_obj = new Travelmanager_Expenses_Types();
                $segments_expenses_output = $expensestype_obj->parse_expensesfield('', $sequence, $rowid, $expensestype, array('destcity' => $destcity_obj));
            }

            /* parse expenses --END */
            //parse finances-START
            $finance = $segmentobj->get_finance(array('returnarray' => true));
            $mainaffobj = new Affiliates($core->user['mainaffiliate']);
            $currencies_f[] = $destcity_obj->get_country()->get_maincurrency();
            $currencies_f[] = $mainaffobj->get_country()->get_maincurrency();
            $currencies_f[] = new Currencies(840, true);
            $currencies_f[] = new Currencies(978, true);
            foreach($currencies_f as $currency) {
                if(is_object($currency)) {
                    $val_currencies[] = $currency->validate_currency();
                }
            }
            $currencies_f = array_filter(array_unique($val_currencies));
            if(is_array($finance)) {
                $frowid = 0;
                foreach($finance as $financerow) {
                    $frowid++;
                    $amount_value = $financerow->amount;
                    $finance_checksum = $financerow->inputChecksum;
                    $currencies_listf = parse_selectlist('segment['.$sequence.'][tmpfid]['.$frowid.'][currency]', '8', $currencies_f, $financerow->currency, '', '', array("id" => 'segment_'.$sequence.'_tmpfid_'.$frowid.'_currency'));
                    $segments_financess_output = $currencies_listf;
                    eval("\$finance_output .= \"".$template->get('travelmanager_plantrip_segmentfinance')."\";");
                }
            }
            else {
                $frowid = 1;
                $currencies_listf = parse_selectlist('segment['.$sequence.'][tmpfid]['.$frowid.'][currency]', '5', $currencies_f, $finance->currency, '', '', array("id" => 'segment_'.$sequence.'_tmpfid_'.$frowid.'_currency'));
                $finance_checksum = generate_checksum('finance');
                eval("\$finance_output = \"".$template->get('travelmanager_plantrip_segmentfinance')."\";");
            }
            //parse finnance--end
            $checkedaccomodation = '';
            if($segmentobj->noAccomodation == '1') {
                $checkedaccomodation = 'checked = "checked"';
            }
            //parse finances-END
            //  eval("\$transsegments_output.= \"".$template->get('travelmanager_plantrip_segment_transptype')."\";");
            $display['othertranspssection'] = "display:none";


            if(is_array($seg_transppbj)) {
                $checked['othertranspssection'] = 'checked="checked"';
                $display['othertranspssection'] = "display:block";
            }
            eval("\$plansegmentscontent_output = \"".$template->get('travelmanager_plantrip_segmentcontents')."\";");
            unset($segments_expenses_output, $expensestype, $transsegments_output, $hotelssegments_output, $accomodation, $selectedhotel);
            eval("\$plantrip_createsegment = \"".$template->get('travelmanager_plantrip_createsegment')."\";");
            $segments_output .= '<div id = "segmentstabs-'.$segid.'">'.$plantrip_createsegment.'</div>';
            $segid++;
            unset($transsegments_output, $checkedaccomodation, $finance_output, $checked, $affiliates_output, $entities);
        }

        eval("\$plantript_segmentstabs= \"".$template->get('travelmanager_plantrip_segmentstabs')."\";");
        $planid = $this->tmpid;


        $helptour = new HelpTour();
        $helptour->set_id('travelmanager_helptour');
        $helptour->set_cookiename('travelmanager_helptour');
        $touritems = $this->get_helptouritems();

        $helptour->set_items($touritems);
        $helptour = $helptour->parse();

        eval("\$plantrip = \"".$template->get('travelmanager_plantrip')."\";");
        return $plantrip;
    }

    public function is_finalized() {
        if($this->data['isFinalized'] == 1) {
            return true;
        }
    }

    public function get_helptouritems() {
        global $lang, $core;
        $touritems = array(
                'start' => array('ignoreid' => true, 'text' => $lang->starttmtour),
                'createtab' => array('options' => 'tipLocation:top;', 'text' => $lang->helptour_createsegment.'<br/><br/>'.$lang->starttmtourexample),
                'cities_1_cache_autocomplete' => array('text' => $lang->helptour_firstcity),
                'pickDate_from_1' => array('text' => $lang->helptour_fromdate),
                'destinationcity_1_cache_autocomplete' => array('options' => 'tipLocation:top;', 'text' => $lang->helptour_firstdestcity),
                'pickDate_to_1' => array('options' => 'tipLocation:top;', 'text' => $lang->helptour_todate),
                'purposes_row_1' => array('options' => 'tipLocation:top;', 'text' => $lang->helptour_purposes),
                'considerleisure_1' => array('options' => 'tipLocation:top;', 'text' => $lang->helptour_considerleisure),
                'segreason_1' => array('options' => 'tipLocation:top;', 'text' => $lang->helptour_segreason),
                'save_section_1_1' => array('options' => 'tipLocation:right;', 'text' => $lang->helptour_savesec1),
                'transpsetionheader_1' => array('text' => $lang->helptour_transpsetionheader),
                'lookuptransps_1' => array('text' => $lang->helptour_lookuptransps),
                'checkbox_show_othertransps_1' => array('text' => $lang->helptour_chooseothertransps),
                'save_section_1_2' => array('options' => 'tipLocation:right;', 'text' => $lang->helptour_savesec2),
                'accomsectionheader_1' => array('text' => $lang->helptour_accomsectionheader),
                'travelmanager/hotelslist' => array('text' => $lang->helptour_checkhotelslist.'</br></br> As an example, <a href="'.$core->settings['rootdir'].'/index.php?module=travelmanager/hotelslist" target="_blank" style="color:#83C41A;">click here</a> before proceeding with your accomodations expenses.'),
                'noAccomodation_1' => array('text' => $lang->helptour_noaccomodation),
                'countryhotels_1_check' => array('options' => 'tipLocation:top;', 'text' => $lang->helptour_countryhotels),
                'hotels_1_cache_hotel_autocomplete' => array('options' => 'tipLocation:top;', 'text' => $lang->helptour_hotelsautocomplete),
                'addnewhotel_1_travelmanager/plantrip_loadpopupbyid' => array('text' => $lang->helptour_addnewhotel),
                'save_section_1_3' => array('options' => 'tipLocation:right;', 'text' => $lang->helptour_savesec3),
                'addexpensessetionheader_1' => array('text' => $lang->helptour_addexpensessetionheader),
                'segment_expensestype_1_1' => array('text' => $lang->helptour_expensestype),
                'expenses_amtcurr_1_1' => array('text' => $lang->helptour_expenses_amtcurr),
                'segment_paidby_1_1' => array('text' => $lang->helptour_expensepaidby),
                'ajaxaddmore_travelmanager/plantrip_expenses_1' => array('options' => 'tipLocation:top;', 'text' => $lang->helptour_addexpense),
                'addexpensessetionheader_1' => array('text' => $lang->helptour_addexpensessetionheader),
                'save_section_1_4' => array('options' => 'tipLocation:right;', 'text' => $lang->helptour_savesec4),
                'financesetionheader_1' => array('options' => 'tipLocation:top;', 'text' => $lang->helptour_financesetionheader),
                'save_section_1_5' => array('options' => 'tipLocation:right;', 'text' => $lang->helptour_savesec5),
                'preview' => array('text' => $lang->saveandpreview_helptour),
                'save_addsegment' => array('text' => $lang->saveandcreatenewseg_helptour),
                'finalize' => array('text' => $lang->saveandfinalize_helptour.'</br></br><a href="#" style="height:20px;color:#83C41A">Back to top</a>'),
        );

        return $touritems;
    }

    public function get_secondseghelptouritems() {
        $touritems = array(
                'ui-id-2' => array('text' => 'Now proceed with your next segment as before'),
                'destinationcity_2_cache_autocomplete' => array('text' => 'Select the destination city of that specific segment (Mandatory).<br/> Press outside the field so that the rest of the page load'),
                'pickDate_to_2' => array('text' => 'Select the segment end date.<br/> If this is your last segment keep the date unchanged'),
        );
        return $touritems;
    }

    public function delete() {
        global $db;
        $segments = TravelManagerPlanSegments::get_data(array('tmpid' => $this->data['tmpid']), array('returnarray' => true));
        if(empty($this->data[static::PRIMARY_KEY]) && empty($this->data['identifier'])) {
            return false;
        }
        elseif(empty($this->data[static::PRIMARY_KEY]) && !empty($this->data['identifier'])) {
            $query = $db->delete_query(static::TABLE_NAME, 'identifier="'.$db->escape_string($this->data['identifier']).'"');
        }
        else {
            $query = $db->delete_query(static::TABLE_NAME, static::PRIMARY_KEY.'='.intval($this->data[static::PRIMARY_KEY]));
        }
        if($segments) {
            $plan_classes = array('TravelManagerPlanSegments', 'TravelManagerPlanTransps', 'TravelManagerPlanaccomodations', 'Travelmanager_Expenses', 'TravelManagerCityReviews', 'TravelManagerPlanAffient', 'TravelManagerPlanSegPurposes', 'TravelManagerPlanFinance');
            foreach($segments as $segment) {
                $segmentid = $segment->tmpsid;
                if(!empty($segmentid)) {
                    if(is_array($plan_classes)) {
                        foreach($plan_classes as $object) {
                            $data = $object::get_data('tmpsid = '.$segmentid.'', array('returnarray' => true));
                            if(is_array($data)) {
                                foreach($data as $object_todelete) {
                                    $object_todelete->delete();
                                }
                            }
                        }
                    }
                }
            }
        }

        if($query) {
            return true;
        }
        return false;
    }

    public function email_finance($message, $subject) {
        global $core;
        $affiliate = $this->get_user()->get_mainaffiliate();
        if(is_object($affiliate) && !empty($affiliate->financeEmail)) {
            $financeemail = $affiliate->financeEmail;
        }
        elseif(!empty($affiliate->finManager)) {
            $finmanager = new Users($affiliate->finManager);
            $financeemail = $finmanager->email;
        }
        if(empty($financeemail)) {
            return;
        }
        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();
        $mailer->set_type();
        $mailer->set_from(array('name' => 'Orkila Attendance System', 'email' => 'attendance@ocos.orkila.com'));
        $mailer->set_subject($subject);
        $mailer->set_message($message);
        $mailer->set_to($financeemail);
        $mailer->send();
    }

}