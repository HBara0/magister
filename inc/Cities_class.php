<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Cities_class.php
 * Created:        @zaher.reda    Apr 29, 2014 | 2:45:11 PM
 * Last Update:    @zaher.reda    Apr 29, 2014 | 2:45:11 PM
 */

class Cities extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'ciid';
    const TABLE_NAME = 'cities';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = null;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    /**
     * Get the country of the city
     *
     * @return \Countries The onbject of the country
     */
    public function get_country() {
        return new Countries($this->data['coid']);
    }

    public static function get_city_byname($name) {
        return Cities::get_city_byattr('name', $name);
    }

    public static function get_city_byattr($attr, $value) {
        global $db;

        if(!empty($value) && !empty($attr)) {
            $query = $db->query('SELECT '.self::PRIMARY_KEY.' FROM '.Tprefix.self::TABLE_NAME.' WHERE '.$db->escape_string($attr).'="'.$db->escape_string($value).'"');
            if($db->num_rows($query) > 1) {
                $items = array();
                while($item = $db->fetch_assoc($query)) {
                    $items[$item[self::PRIMARY_KEY]] = new self($item[self::PRIMARY_KEY]);
                }
                $db->free_result($query);
                return $items;
            }
            else {
                if($db->num_rows($query) == 1) {
                    return new self($db->fetch_field($query, self::PRIMARY_KEY));
                }
                return false;
            }
        }
        return false;
    }

    public function get_defaultairport() {
        if(empty($this->data['defaultAirport'])) {
            return false;
        }
        return new TravelManagerAirports($this->data['defaultAirport']);
    }

    public function get_approvedhotels() {
        return TravelManagerHotels::get_data(array('city' => $this->data['ciid'], 'isApproved' => 1), array('returnarray' => true, 'order' => array('by' => array('-avgPrice', 'name'), 'sort' => array('DESC', 'ASC'))));
    }

    public function get_reviews() {
        global $db;

        $query = $db->query('SELECT tmcrid FROM '.Tprefix.'travelmanager_cityreviews  WHERE ciid ="'.$db->escape_string($this->data['ciid']).'"');
        if($db->num_rows($query) >= 1) {

            while($item = $db->fetch_assoc($query)) {
                $reviewitems[$item['tmcrid']] = new TravelManagerCityReviews($item['tmcrid']);
            }
        }
        return $reviewitems;
    }

    public function get_latestbriefing() {
        global $db;

        return TravelManagerCityBriefings::get_citybriefings('ciid='.intval($this->data['ciid']), array('ORDER' => array('by' => 'createdOn', 'sort' => 'DESC'), 'limit' => '0,1'));
    }

    public function get_unapprovedhotels() {
        return TravelManagerHotels::get_data(array('isApproved' => 0, 'city' => $this->data['ciid']));
    }

    public function parse_cityreviews() {
        global $lang, $core;
        $descity_reviewobjs = $this->get_reviews();
        if(is_array($descity_reviewobjs)) {
            $cityprofile_output = '<div> <strong>'.$lang->cityreview.'</strong></div>';
            foreach($descity_reviewobjs as $city_reviewsobj) {
                $destcityreview['review'] = $city_reviewsobj->get()[review];
                $destcityreview['user'] = $city_reviewsobj->get_createdBy()->get();
                $destcityreview['reviewdby'] = $destcityreview['user']['displayName'];
                $cityprofile_output .='<div style="display:block;padding:8px;">
                <div>'.$destcityreview['review'].'</div>
                    <div class="smalltext"><a href="'.$core->settings['rootdir'].'/users.php?action=profile&uid='.$destcityreview['user']['uid'].'"  target="_blank">'.$destcityreview['reviewdby'].'</a></div>
                        </div>';
            }
        }
        return $cityprofile_output;
    }

    public function parse_citybriefing() {
        global $lang, $core;
        $city_briefingsobj = $this->get_latestbriefing();
        if(is_object($city_briefingsobj)) {
            $citybriefings_output = ' <div><strong>'.$lang->citybrfg.'</strong></div>';
            $destcitybriefing['briefing'] = $city_briefingsobj->get()['briefing'];
            $destcitybriefing['user'] = $city_briefingsobj->get_createdBy()->get();
            $destcitybriefing['briefedby'] = $destcitybriefing['user']['displayName'];
            $citybriefings_output = '<div style="display:block;padding:8px;">
         <div>'.$destcitybriefing['briefing'].'</div>
         <div class="smalltext"><a href="'.$core->settings['rootdir'].'/users.php?action=profile&uid='.$destcitybriefing['user']['uid'].'" target="_blank">'.$destcitybriefing['briefedby'].'</a></div></div>';
        }

        return $citybriefings_output;
    }

    public static function get_cities($filters = '') {
        global $db;

        $cities = array();

        /* Filters to be improved */
        if(!empty($filters)) {
            if(is_array($filters)) {
                $andor = ' WHERE ';
                foreach($filters as $attr => $value) {
                    if(is_numeric($value)) {
                        $value = intval($value);
                    }
                    else {
                        $value = '"'.$db->escape_string($value).'"';
                    }
                    $filters_querystring.=$andor.$attr.'='.$value;
                    $andor = ' AND ';
                }
            }
            else {
                $filters_querystring = ' WHERE '.$db->escape_string($filters);
            }
        }
        $query = $db->query('SELECT '.self::PRIMARY_KEY.' FROM '.Tprefix.self::TABLE_NAME.$filters_querystring);
        if($db->num_rows($query) > 1) {
            while($city = $db->fetch_assoc($query)) {
                $cities[$city[self::PRIMARY_KEY]] = new Cities($city[self::PRIMARY_KEY]);
            }
            $db->free_result($query);
            return $cities;
        }
        else {
            if($db->num_rows($query) == 1) {
                return new self($db->fetch_field($query, self::PRIMARY_KEY));
            }
            return false;
        }

        return false;
    }

    public static function parse_transportations($transps, $transpdata = array(), $sequence, $source = '') {  //to be continued later
        global $template, $lang, $core;
        if($source == 'addmore') {
            $transpdata['inputChecksum'] = generate_checksum();
            $othertranspcategories = TravelManagerTranspCategories ::get_data('', array('returnarray' => true));
            $transp_category_fields = TravelManagerPlan::parse_transportaionfields($transps, array('inputChecksum' => $transpdata['inputChecksum'], 'transportationdetials' => $transpdata['transportationdetails'], 'name' => 'other', 'tmtcid' => $transp->tmtcid, 'othercategories' => $othertranspcategories), array('origincity' => $transpdata['origincity'], 'destcity' => $transpdata['destcity'], 'date' => $transpdata['transprequirements']['departuretime']), $sequence, $rowid);
            $mainaffobj = new Affiliates($core->user['mainaffiliate']);
            $destcity_obj = new Cities($transpdata['destcity']);
            $currencies[] = $destcity_obj->get_country()->get_maincurrency();
            $currencies[] = $mainaffobj->get_country()->get_maincurrency();
            $currencies[] = new Currencies(840, true);
            $currencies[] = new Currencies(978, true);
            $currencies = array_unique($currencies);
            $currencies_list = parse_selectlist('segment['.$sequence.'][tmtcid]['.$transpdata['inputChecksum'].'][currency]', 4, $currencies, '840');
            eval("\$transcategments_output = \"".$template->get('travelmanager_plantrip_segment_transtypefields')."\";");
            //  eval("\$transsegments_output .= \"".$template->get('travelmanager_plantrip_segment_transptype')."\";");
            return '<tr><td>'.$transcategments_output.'</td></tr>';
        }

        if(is_array($transps)) {
            foreach($transps as $transp) {
                $transpdetailsfields = array('fare', 'vehicleNumber', 'flightNumber', 'transpDetails', 'agencyName', 'numDays', 'transpType', 'paidBy', 'paidById', 'isMain');
                foreach($transpdetailsfields as $field) {
                    $transportation_details[$sequence][$transp->tmtcid][$field] = $transp->$field;
                }

                $transportation_details[$sequence][$transp->tmtcid]['display'] = "display:none;";
                if(isset($transp->paidById) && !empty($transp->paidById)) {
                    $transportation_details[$sequence][$transp->tmtcid]['display'] = "display:block;";
                }
            }
        }

        //   if(is_array($transpdata) && empty($transpdata['apiFlightdata'])) {
        /* Get proposed transits from API */
        $directionapi = TravelManagerPlan::get_availablecitytransp(array('origincity' => $transpdata['origincity'], 'destcity' => $transpdata['destcity'], 'departuretime' => $transpdata['transprequirements']['departuretime'], 'drivemode' => $transpdata['transprequirements']['drivemode']));  /*  Get available tranportaion mode for the city proposed by google API */
        //  }
        //  else {
        //   $directionapi = $transpdata['apiFlightdata'];
        // }

        /* Parse proposed transits - START */
        $valid_travelmodes = array('transit', 'driving');
        $used_transptype = array();

        if(is_array($directionapi->routes[0]->legs[0]->steps)) {
            $suggestedtransp['title'] .='<h2>'.$lang->suggestedtransportations.'</h2>';
            foreach($directionapi->routes[0]->legs[0]->steps as $step) {
                if(!in_array(strtolower($step->travel_mode), $valid_travelmodes)) {
                    continue;
                }

                if(in_array($step->transit_details->line->vehicle->type, $used_transptype)) {
                    continue;
                }

                if(isset($step->transit_details)) {
                    $used_transptype[] = $step->transit_details->line->vehicle->type;
                    $transitmode['vehicleType'] = $step->transit_details->line->vehicle->type;
                    $transitmode['url'] = $step->transit_details->line->url;
                    $transitmode['vehiclename'] = $step->transit_details->line->vehicle->name;
                    $transitmode['icon'] = $step->transit_details->line->vehicle->icon;
                }
                else {
                    if(strtolower($step->travel_mode) == 'driving') {
                        $transitmode['vehiclename'] = $transitmode['vehicleType'] = 'car';
                    }
                }

                $urldisplay = explode('/', $transitmode['url']);
                if(!empty($urldisplay [2])) {
                    $transitmode['url'] = '<a href="'.$transitmode[url].'" target="_blank" >'.$urldisplay[2].'</a>'; //temporary coded
                    $possible_transportation = '<div>'.$lang->reservation.'<span class="smalllinkgrey"> '.$transitmode['url'].'</span></div>';
                }
                if(is_array($usedcategories)) {
                    if(in_array($transitmode['vehiclename'], $usedcategories)) {
                        continue;
                    }
                }
                $usedcategories[$transitmode['vehiclename']] = $transitmode['vehiclename'];
                $drivingmode['transpcat'] = TravelManagerPlan::parse_transportation(array('selectedtransp' => $transpdata['transportationdetails'][$transpdata['segment']->tmpsid], 'vehicleType' => $transitmode['vehiclename']), $sequence);
                $transpdata['inputChecksum'] = generate_checksum();
                $transp = new TravelManagerPlanTransps();
                $transp_category_fields = TravelManagerPlan::parse_transportaionfields($transp, array('inputChecksum' => $transpdata['inputChecksum'], 'transportationdetials' => $transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']], 'name' => strtolower($drivingmode['transpcat']['name']), 'tmtcid' => $drivingmode['transpcat']['cateid']), array('origincity' => $transpdata['origincity'], 'destcity' => $transpdata['destcity'], 'date' => $transpdata['transprequirements']['departuretime']), $sequence);
                if(!empty($transp_category_fields)) {
                    if(empty($drivingmode['transpcat']['display'])) {
                        $drivingmode['transpcat']['display'] = 'display:none;';
                    }
                    if(empty($transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']]['display'])) {
                        $transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']]['display'] = "display:none;";
                    }
                    $transportation_details[$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']]['affid'] = $transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']]['paidById'];
                    if(is_object($transpdata['segment'])) {
                        $transportation_details[$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']]['affiliate'] = $transpdata['segment']->display_paidby($transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']]['paidBy'], $transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']]['paidById'])->name;
                    }
                    // $availabletransp[$drivingmode['transpcat']['cateid']] = $drivingmode['transpcat']['cateid'];
                    eval("\$suggestedtranscategments_output .= \"".$template->get('travelmanager_plantrip_segment_transtypefields')."\";");
                    eval("\$suggestedtranssegments_output .= \"".$template->get('travelmanager_plantrip_segment_transptype')."\";");
                };
            }
        }
        else if(($transpdata['transprequirements']['departuretime']) > strtotime(date("Y-m-d", strtotime(date("Y-m-d")))." +55 days")) {
            $suggestedtranssegments_output .='<div class="ui-state-highlight ui-corner-all" style="padding:10px; margin-bottom:10px;">system doesn\'t return possible transportations if the duration between now and the "from date" exceeds 55 days </div>';
        }
        /* Possible Reservation Resources */
        unset($possible_transportation);

        /* Parse proposed transits - END */

        /* Parse saved transits & flights - START */
        if(is_array($transps)) {
            $transsegments['title'] = '<h2>'.$lang->selectedtransportations.'</h2>';

            foreach($transps as $transp) {
                if($transp->get_transpcategory()->isAerial == 1 && $transp->isUserSuggested == 0) {
                    $aerialtransp = $transp;
                }
                else {
                    unset($drivingmode);
                    $availabletransp[0] = 0; /* Always exclude the airplan cateory when parsing other categories */
                    $transpdata['transportationdetails'] = $transp->get();
                    $transpdata['inputChecksum'] = $transp->inputChecksum;
                    $othertranspcategories = TravelManagerTranspCategories ::get_data('tmtcid NOT IN ('.implode(', ', $availabletransp).')', array('returnarray' => true));
                    $transp_category_fields = TravelManagerPlan::parse_transportaionfields($transp, array('inputChecksum' => $transpdata['inputChecksum'], 'transportationdetials' => $transpdata['transportationdetails'], 'name' => 'other', 'tmtcid' => $transp->tmtcid, 'othercategories' => $othertranspcategories), array('origincity' => $transpdata['origincity'], 'destcity' => $transpdata['destcity'], 'date' => $transpdata['transprequirements']['departuretime']), $sequence);
                    $transportation_details[$transpdata['segment']->tmpsid][$transpdata['inputChecksum']]['display'] = 'display:none;';
                    eval("\$transcategments_output .= \"".$template->get('travelmanager_plantrip_segment_transtypefields')."\";");
                    // eval("\$transsegments_output .= \"".$template->get('travelmanager_plantrip_segment_transptype')."\";");
                    $availabletransp[$transp->tmtcid] = $transp->tmtcid;
                }
            }
        }
        /* Always show one section Others type */
        if(empty($transsegments['title'])) {
            $transsegments['title'] = '<h2>'.$lang->selectothertransportations.'</h2>';
        }
        unset($drivingmode);
        $transpdata['inputChecksum'] = generate_checksum();
        $availabletransp[0] = 0;
        $othertranspcategories = TravelManagerTranspCategories ::get_data('tmtcid NOT IN ('.implode(', ', $availabletransp).')', array('returnarray' => true));
        $transp = new TravelManagerPlanTransps();
        $transp_category_fields = TravelManagerPlan::parse_transportaionfields($transp, array('inputChecksum' => $transpdata['inputChecksum'], 'transportationdetials' => $transpdata['transportationdetails'], 'name' => 'other', 'tmtcid' => $transp->tmtcid, 'othercategories' => $othertranspcategories), array('origincity' => $transpdata['origincity'], 'destcity' => $transpdata['destcity'], 'date' => $transpdata['transprequirements']['departuretime']), $sequence, $rowid);
        eval("\$transcategments_output .= \"".$template->get('travelmanager_plantrip_segment_transtypefields')."\";");
        // eval("\$transsegments_output .= \"".$template->get('travelmanager_plantrip_segment_transptype')."\";");

        /* Parse Flights */
        if($transpdata['origincity']['coid'] != $transpdata ['destcity']['coid']) {
            $transpdata['inputChecksum'] = generate_checksum();
            /* Parse save flight */
            if(!empty($aerialtransp)) {
                $transpdata['inputChecksum'] = $aerialtransp->inputChecksum;
                $transp_category_fields = TravelManagerPlan::parse_transportaionfields($aerialtransp, array('inputChecksum' => $transpdata['inputChecksum'], 'transportationdetials' => $transportation_details, 'name' => 'airplane', 'tmtcid' => $aerialtransp->tmtcid), array('origincity' => $transpdata['origincity'], 'destcity' => $transpdata['destcity'], 'date' => $transpdata['transprequirements']['departuretime']), $sequence);
                if(!empty($transp_category_fields)) {
                    eval("\$transcategments_output .= \"".$template->get('travelmanager_plantrip_segment_transtypefields')."\";");
                    //eval("\$transsegments_output .= \"".$template->get('travelmanager_plantrip_segment_transptype')."\";");
                }
            }
            /* Parse Available flights */
            $drivingmode ['transpcat'] = TravelManagerPlan::parse_transportation(array('vehicleType' => 'airplane'), $sequence);
            $aerialtransp = new TravelManagerPlanTransps();
            if(empty($suggestedtransp['title'])) {
                $suggestedtransp['title'] .='<h2>'.$lang->suggestedtransportations.'</h2>';
            }
            $transp_category_fields = TravelManagerPlan::parse_transportaionfields($aerialtransp, array('inputChecksum' => $transpdata['inputChecksum'], 'transportationdetials' => $transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']], 'name' => $drivingmode['transpcat']['name'], 'tmtcid' => $drivingmode['transpcat'] ['cateid']), array('origincity' => $transpdata['origincity'], 'destcity' => $transpdata['destcity'], 'date' => $transpdata['transprequirements']['departuretime'], 'arrivaldate' => $transpdata['transprequirements']['arrivaltime'], 'isOneway' => $transpdata['transprequirements']['oneway']), $sequence);
            if(!empty($transp_category_fields)) {
                $drivingmode['transpcat']['display'] = 'display:none;';
                eval("\$suggestedtranscategments_output .= \"".$template->get('travelmanager_plantrip_segment_transtypefields')."\";");
                eval("\$suggestedtranssegments_output .= \"".$template->get('travelmanager_plantrip_segment_transptype')."\";");
            }
        }
        /* Parse saved transits & flights - END */
        if(!empty($suggestedtranssegments_output)) {
            $transpmode_apimaplink = 'https://www.google.com/maps/dir/'.$transpdata['origincity']['name'].',+'.$transpdata['origincity']['country'].'/'.$transpdata['destcity']['name'].',+'.$transpdata['destcity']['country'].'/';
            $suggestedtranssegments_output = $suggestedtransp['title'].'<div class="ui-state-highlight ui-corner-all" style="padding: 6px; font-weight: bold;"><a href="'.$transpmode_apimaplink.'" target="_blank">Visualize Tranpostation Possibilities on Map</a></div>'.$suggestedtranssegments_output;
        }

        $transsegments_output = $transsegments['title'].$transsegments_output;

        $transp_ouptut['suggested'] = $suggestedtranssegments_output.$suggestedtranscategments_output;
        $transp_ouptut['othertransp'] = $transsegments_output.$transcategments_output;

        if(isset($transpdata['excludesuggestions']) && $transpdata['excludesuggestions'] == 1) {
            unset($transp_ouptut['suggested']);
        }
        if(isset($transpdata['transprequirements']['referrer']) && $transpdata['transprequirements'][referrer] == 'todate') {
            return $transp_ouptut['suggested'];
        }

        //return $suggestedtranssegments_output.$suggestedtranscategments_output.'<hr/>'.$transsegments_output.$transcategments_output;
        return $transp_ouptut;
    }

    protected function create(array $data) {

    }

    protected function update(array $data) {

    }

}