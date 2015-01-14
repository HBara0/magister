<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Cities_class.php
 * Created:        @zaher.reda    Apr 29, 2014 | 2:45:11 PM
 * Last Update:    @zaher.reda    Apr 29, 2014 | 2:45:11 PM
 */

class Cities {
    private $data = array();

    const PRIMARY_KEY = 'ciid';
    const TABLE_NAME = 'cities';

    public function __construct($id) {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    private function read($id) {
        global $db;
        $this->data = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

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

    public function get_approvedhotels($filter = '') {
        global $db;
        if(!empty($filter) && $filter == 'approved') {
            $filterwhere = '  WHERE  isApproved=1 ';
            $filterwhereand = ' AND city ="'.$db->escape_string($this->data['ciid']).'"';
        }
        else {
            $filterwhere = ' WHERE city ="'.$db->escape_string($this->data['ciid']).'"';
            $filterwhereand = '';
        }
        $query = $db->query('SELECT tmhid FROM '.Tprefix.'travelmanager_hotels '.$filterwhere.$filterwhereand);
        if($db->num_rows($query) >= 1) {
            while($item = $db->fetch_assoc($query)) {
                $items[$item['tmhid']] = new TravelManagerHotels($item['tmhid']);
            }
        }
        return $items;
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

        return TravelManagerCityBriefings::get_citybriefings('ciid='.$db->escape_string($this->data['ciid']), array('ORDER' => array('by' => 'createdOn', 'sort' => 'DESC'), 'limit' => '0,1'));
    }

    public function get_unapprovedhotels() {
        return TravelManagerHotels::get_data(array('isApproved' => 0, 'city' => $this->data['ciid']));
    }

    public function parse_approvedhotels($sequence, $destcity = '', $selectedhotel = array(), $source) {
        global $template, $lang, $core;
        $approved_hotelsobjs = $this->get_approvedhotels();
        if(is_array($selectedhotel) && !empty($selectedhotel)) {
            $segid = key($selectedhotel);
        }

        if(is_array($approved_hotelsobjs)) {
            //   $hotelssegments_output = '<div class="subtitle">'.$lang->approvedhotels.'</div>';
            foreach($approved_hotelsobjs as $approved_hotelsobj) {
                $approved_hotels = $approved_hotelsobj->get();
                if($source == 'modify') {
                    $approvedhotel_checksum = $approved_hotels['tmhid'];
                }
                else {
                    $approvedhotel_checksum = generate_checksum('accomodation');
                }
                if(is_array($selectedhotel) && !empty($selectedhotel)) {
                    // $approvedhotel_id = key($selectedhotel[key($selectedhotel)]);
                    $approvedhotel_id = $selectedhotel[$segid][$approved_hotels['tmhid']]['selectedhotel'];
                }

                if($approved_hotels['tmhid'] == $approvedhotel_id) {
                    $segment[$sequence]['tmhid'][$approvedhotel_checksum]['checked'] = " checked='checked'";
                }
                $hotelname = array($approved_hotels['tmhid'] => $approved_hotels['name']);
                $review_tools .= '<a href="#'.$approved_hotels['tmhid'].'" id="hotelreview_'.$approved_hotels['tmhid'].'_travelmanager/plantrip_loadpopupbyid" rel="hotelreview_'.$approved_hotels['tmhid'].'" title="'.$lang->hotelreview.'"><img src="'.$core->settings['rootdir'].'/images/icons/reviewicon.png" title="'.$lang->readhotelreview.'" alt="'.$lang->readhotelreview.'" border="0" width="16" height="16"></a>';


                // $checkbox_hotel = parse_checkboxes('segment['.$sequence.']['.$approvedhotel_checksum.'][tmhid]', $hotelname, $selectedhotel[$segid][$approved_hotels['tmhid']], true, '&nbsp;&nbsp;');
                $checkbox_hotel = '<input aria-describedby="ui-tooltip-155" title="" '.$segment[$sequence][tmhid][$approvedhotel_checksum]['checked'].'  name="segment['.$sequence.'][tmhid]['.$approvedhotel_checksum.'][tmhid]" id="segment['.$sequence.']['.$approvedhotel_checksum.'][tmhid]" value="'.$approved_hotels['tmhid'].'" type="checkbox">'.$approved_hotels['name'];

                $paidby_details.=$this->parse_paidby($sequence, '', $segid, $approvedhotel_checksum, array('tmhid' => $approved_hotels['tmhid'], 'accomodations' => $selectedhotel[$segid][$approvedhotel_id]));
                if(empty($selectedhotel[$segid][$approved_hotels['tmhid']]['display'])) {
                    $selectedhotel[$segid][$approved_hotels['tmhid']]['display'] = "display:none;";
                }
                $mainaffobj = new Affiliates($core->user['mainaffiliate']);
                /* ffilter the currency  either get the curreny of the destination city or  the currencies of the country of the main affiliate */
                $currency['filter']['numCode'] = 'SELECT mainCurrency FROM countries where capitalCity='.$destcity['ciid'].' OR numCode IN(SELECT mainCurrency FROM countries where coid='.$mainaffobj->get_country()->coid.')';
                $curr_objs = Currencies::get_data($currency['filter'], array('returnarray' => true, 'operators' => array('numCode' => 'IN')));
                $curr_objs[840] = new Currencies(840);
                $currencies_list .= parse_selectlist('segment['.$sequence.'][tmhid]['.$approved_hotels['tmhid'].'][currency]', 4, $curr_objs, '840', '', '', array('width' => '100%'));
                $currencies_list .= parse_selectlist('segment['.$sequence.'][tmhid]['.$approvedhotel_checksum.'][currency]', 4, $currencies, '840');

                eval("\$hotelssegments_output  .= \"".$template->get('travelmanager_plantrip_segment_hotels')."\";");
                $review_tools = $paidby_details = $currencies_list = $checkbox_hotel = '';
            }
        }
        else {
            /* Parse others */
        }
        return $hotelssegments_output;
    }

    public function parse_paidby($sequence, $rowid, $segid, $approvedhotel_checksum, $selectedoptions = array()) {
        global $lang;
        if(empty($rowid)) {
            $rowid = $selectedoptions['tmhid'];
        }
        $paidby_entities = array(
                'myaffiliate' => $lang->myaffiliate,
                'supplier' => $lang->supplier,
                'client' => $lang->client,
                'myself' => $lang->myself,
                'anotheraff' => $lang->anotheraff
        );
        foreach($paidby_entities as $val => $paidby) {
            if(!empty($selectedoptions['accomodations']['paidby'])) {
                $selected = '';
                if($selectedoptions['accomodations']['paidby'] === $val) {
                    $selected = ' selected="selected"';
                }
            }
            $paid_options.="<option value=".$val." {$selected}> {$paidby} </option>";
        }

        $onchange_actions = 'if($(this).find(":selected").val()=="anotheraff"){$("#"+$(this).find(":selected").val()+"_accomodations_'.$sequence.'_'.$rowid.'").show().find("input").first().focus().val("");}else{$("#anotheraff_accomodations_'.$sequence.'_'.$rowid.'").hide();}';
        // $onchange_actions = 'onchange="$(\"#"+$(this).find(":selected").val()+"_"+'.$sequence.').effect("highlight", {color: "#D6EAAC"}, 1500).find("input").first().focus();\"';
        return 'Paid By <select id="paidbylist_accomodations_'.$sequence.'_'.$rowid.'" name="segment['.$sequence.'][tmhid]['.$approvedhotel_checksum.'][entites]" onchange='.$onchange_actions.'>'.$paid_options.'</select> ';
        //   return '<div style="display:block;padding:8px;"  id="paidby"> Paid By '.parse_selectlist('segment['.$sequence.'][tmhid]['.$selectedoptions['tmhid'].'][entites]', 6, $paidby_entities, $selected_paidby[$segid], '', '$("#"+$(this).find(":selected").val()+ "_"+'.$sequence.'+"_"+'.$rowid.').effect("highlight", {color: "#D6EAAC"}, 1500).find("input").first().focus();;', array('id' => 'paidby')).'</div>';
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

    public static function parse_transportations($transpdata = array(), $sequence) {  //to be continued later
        global $template, $lang;
        $rowid = 0;
        if(is_array($transpdata) && empty($transpdata['apiFlightdata'])) {
            $directionapi = TravelManagerPlan::get_availablecitytransp(array('origincity' => $transpdata['origincity'], 'destcity' => $transpdata['destcity'], 'departuretime' => $transpdata['transprequirements']['departuretime'], 'drivemode' => $transpdata['transprequirements']['drivemode']));  /*  Get available tranportaion mode for the city proposed by google API */
        }
        else {
            $directionapi = $transpdata['apiFlightdata'];
        }
        $valid_travelmodes = array('transit', 'driving');
        $used_transptype = array();
        if(is_array($directionapi->routes[0]->legs[0]->steps)) {
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

                $drivingmode['transpcat'] = TravelManagerPlan::parse_transportation(array('selectedtransp' => $transpdata['transportationdetails'][$transpdata['segment']->tmpsid], 'vehicleType' => $transitmode['vehiclename']), $sequence);
                $transp_category_fields = TravelManagerPlan::parse_transportaionfields(array('transportationdetials' => $transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']], 'name' => strtolower($drivingmode['transpcat']['name']), 'tmtcid' => $drivingmode['transpcat']['cateid']), array('origincity' => $transpdata['origincity'], 'destcity' => $transpdata['destcity'], 'date' => $transpdata['transprequirements']['departuretime']), $sequence);
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
                    $todelete[$drivingmode[transpcat][cateid]] = $lang->delete.' <input type = "checkbox" title = "'.$lang->todelete.'" value = "1" id = "segment_'.$sequence.'_tmtcid_'.$drivingmode[transpcat][cateid].'_todelete" name = "segment['.$sequence.'][tmtcid]['.$drivingmode[transpcat][cateid].'][todelete]" />';
                    // $transpfield['display'] = 'display:inline-block;';

                    $availabletransp[$drivingmode['transpcat']['cateid']] = $drivingmode['transpcat']['cateid'];
                    eval("\$transcategments_output .= \"".$template->get('travelmanager_plantrip_segment_transtypefields')."\";");
                    eval("\$transsegments_output .= \"".$template->get('travelmanager_plantrip_segment_transptype')."\";");
                };
            }
        }
        $types = array('bus', 'train', 'taxi', 'heavy_rail', 'lightrail');
        foreach($types as $type) {
            $drivingmode['transpcat'] = TravelManagerPlan::parse_transportation(array('selectedtransp' => $transpdata['transportationdetails'][$transpdata['segment']->tmpsid], 'vehicleType' => $type), $sequence);
            if(empty($transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']])) {
                unset($drivingmode['transpcat']);
                continue;
            }
            $transp_category_fields = TravelManagerPlan::parse_transportaionfields(array('transportationdetials' => $transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']], 'name' => strtolower($drivingmode['transpcat']['name']), 'tmtcid' => $drivingmode['transpcat']['cateid']), array('origincity' => $transpdata['origincity'], 'destcity' => $transpdata['destcity'], 'date' => $transpdata['transprequirements']['departuretime']), $sequence);
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
                $todelete[$drivingmode[transpcat][cateid]] = $lang->delete.' <input type = "checkbox" title = "'.$lang->todelete.'" value = "1" id = "segment_'.$sequence.'_tmtcid_'.$drivingmode[transpcat][cateid].'_todelete" name = "segment['.$sequence.'][tmtcid]['.$drivingmode[transpcat][cateid].'][todelete]" />';

                $availabletransp[$drivingmode['transpcat']['cateid']] = $drivingmode['transpcat']['cateid'];
                eval("\$transcategments_output .= \"".$template->get('travelmanager_plantrip_segment_transtypefields')."\";");
                eval("\$transsegments_output .= \"".$template->get('travelmanager_plantrip_segment_transptype')."\";");
            }
        }

        if($transpdata['origincity']['coid'] != $transpdata['destcity']['coid']) {
            $drivingmode ['transpcat'] = TravelManagerPlan::parse_transportation(array('selectedtransp' => $transpdata['transportationdetails'][$transpdata['segment']->tmpsid], 'vehicleType' => 'airplane'), $sequence);
            $transp_category_fields = TravelManagerPlan::parse_transportaionfields(array('transportationdetials' => $transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$drivingmode['transpcat']['cateid']], 'name' => $drivingmode['transpcat']['name'], 'tmtcid' => $drivingmode['transpcat'] ['cateid']), array('origincity' => $transpdata['origincity'], 'destcity' => $transpdata['destcity'], 'date' => $transpdata['transprequirements']['departuretime']), $sequence);
            if(!empty($transp_category_fields)) {
                unset($possible_transportation);
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
                $availabletransp[$drivingmode['transpcat']['cateid']] = $drivingmode['transpcat']['cateid'];
                eval("\$transcategments_output .= \"".$template->get('travelmanager_plantrip_segment_transtypefields')."\";");
                eval("\$transsegments_output .= \"".$template->get('travelmanager_plantrip_segment_transptype')."\";");
            }
        }

        /* Always have the Others type */
        unset($drivingmode);
        $drivingmode[transpcat][cateid] = 0;
        //  $drivingmode['transpcat'] = TravelManagerPlan::parse_transportation(array('selectedtransp' => $transpdata['transportationdetails'][$transpdata['segment']->tmpsid], 'vehicleType' => 'other'), $sequence);
        $availabletransp[1] = 1; /* Always exclude the airplan cateory when parsing other categories */
        $othertranspcategories = TravelManagerTranspCategories ::get_data('tmtcid NOT IN ('.implode(', ', $availabletransp).')', array('returnarray' => true));
        $drivingmode['transpcat']['display'] = 'display:block;';
        $transpdata['transportationdetails'][$transpdata['segment']->tmpsid][0]['display'] = 'display:none;';
        $drivingmode['transpcat']['title'] = 'Other';
        if(empty($transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$$drivingmode[transpcat][cateid]]['display'])) {
            $transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$$drivingmode[transpcat][cateid]]['display'] = "display:none;";
        }
        $transportation_details[$transpdata['segment']->tmpsid][$$drivingmode[transpcat][cateid]]['affid'] = $transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$$drivingmode[transpcat][cateid]]['paidById'];
        if(is_object($transpdata['segment'])) {
            $transportation_details[$transpdata['segment']->tmpsid][$$drivingmode[transpcat][cateid]]['affiliate'] = $transpdata['segment']->display_paidby($transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$$drivingmode[transpcat][cateid]]['paidBy'], $transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$$drivingmode[transpcat][cateid]]['paidById'])->name;
        }
        $transp_category_fields = TravelManagerPlan::parse_transportaionfields(array('transportationdetials' => $transpdata['transportationdetails'][$transpdata['segment']->tmpsid][$drivingmode[transpcat][cateid]], 'name' => 'other', 'tmtcid' => $drivingmode[transpcat][cateid], 'othercategories' => $othertranspcategories), array('origincity' => $transpdata['origincity'], 'destcity' => $transpdata['destcity'], 'date' => $transpdata['transprequirements']['departuretime']), $sequence, $rowid);
        $todelete[$drivingmode[transpcat][cateid]] = $lang->delete.' <input type = "checkbox" title = "'.$lang->todelete.'" value = "1" id = "segment_'.$sequence.'_tmtcid_'.$drivingmode[transpcat][cateid].'_todelete" name = "segment['.$sequence.'][tmtcid]['.$drivingmode[transpcat][cateid].'][todelete]" />';
        $row_id = 'id="'.$sequence.'_'.$rowid.'"';
        eval("\$transcategments_output .= \"".$template->get('travelmanager_plantrip_segment_transtypefields')."\";");
        eval("\$transsegments_output .= \"".$template->get('travelmanager_plantrip_segment_transptype')."\";");
        $rowid++;
        unset($row_id);
        return $transsegments_output.$transcategments_output;
    }

    public function __set($name, $value) {
        $this->
                data[$name] = $value;
    }

    /* call the Magical function  get to acces the private attributes */
    public function __get($name) {
        if(array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
    }

    public function get() {
        return $this->data;
    }

}