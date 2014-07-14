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

    public function get_approvedhotels() {
        global $db;

        $query = $db->query('SELECT tmhid FROM '.Tprefix.'travelmanager_hotels  WHERE  isApproved=1 AND city ="'.$db->escape_string($this->data['ciid']).'"');
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

    public function parse_approvedhotels($sequence) {
        $approved_hotelsobjs = $this->get_approvedhotels();

        if(is_array($approved_hotelsobjs)) {
            $hotelssegments_output = '<div class="subtitle">Approved Hotels</div>';
            foreach($approved_hotelsobjs as $approved_hotelsobj) {
                $approved_hotels = $approved_hotelsobj->get();
                $hotelname = array($approved_hotels['tmhid'] => $approved_hotels['name']);
                $review_tools .= ' <a href="#'.$approved_hotels['tmhid'].'" id="hotelreview_'.$approved_hotels['tmhid'].'_travelmanager/plantrip_loadpopupbyid" rel="hotelreview_'.$approved_hotels['tmhid'].'" title="'.$lang->sharewith.'"><img src="'.$core->settings['rootdir'].'./images/icons/reviewicon.png" title="'.$lang->readhotelreview.'" alt="'.$lang->readhotelreview.'" border="0" width="16" height="16"></a>';
                $hotelssegments_output .= ' <div style="display:block;">'.parse_checkboxes('segment['.$sequence.'][tmhid]', $hotelname, '', true, '&nbsp;&nbsp;').'<span> '.$review_tools.' </span></div>';

//eval("\$hotelssegments_output  .= \"".$template->get('travelmanager_plantrip_segment_hotels')."\";");
                $review_tools = '';
            }
        }
        return $hotelssegments_output;
    }

    public function parse_cityreviews() {
        global $lang, $core;
        $descity_reviewobjs = $this->get_reviews();
        if(is_array($descity_reviewobjs)) {
            $cityprofile_output = '<div> <strong>'.$lang->cityreview.'</strong></div>';
            foreach($descity_reviewobjs as $city_reviewsobj) {
                $destcityreview['review'] = $city_reviewsobj->get()['review'];
                $destcityreview['user'] = $city_reviewsobj->get_createdBy()->get();
                $destcityreview['reviewdby'] = $destcityreview['user']['displayName'];
                $cityprofile_outparse_transportationput .='<div style="display:block;padding:8px;">
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
                    $filters_querystring .= $andor.$attr.'='.$value;
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

    public static function parse_tranaportations($transpdata = array(), $sequence) {  //to be continued later
        global $template, $lang;

        $directionapi = TravelManagerPlan::get_availablecitytransp(array('origincity' => $transpdata['origincity'], 'destcity' => $transpdata['destcity'], 'departuretime' => $transpdata['departuretime']));  /* Get available tranportaion mode for the city proposed by google API */
        $transpmode_googledirections = ' https://www.google.com/maps/dir/'.$transpdata['origincity']['name'].',+'.$transpdata['origincity']['country'].'/'.$transpdata['destcity']['name'].',+'.$transpdata['destcity']['country'].'/';

        for($i = 0; $i < count($directionapi->routes[0]->legs[0]->steps); $i++) {
            if(!empty($directionapi->routes[0]->legs[0]->steps[$i]->transit_details->line->url)) {
                $transitmode['url'] = $directionapi->routes[0]->legs[0]->steps[$i]->transit_details->line->url;
            }
            if(!empty($directionapi->routes[0]->legs[0]->steps[$i]->transit_details->line->vehicle->name)) {
                $transitmode['vehiclename'] = $directionapi->routes[0]->legs[0]->steps[$i]->transit_details->line->vehicle->name;
            }
            if(!empty($directionapi->routes[0]->legs[0]->steps[$i]->transit_details->line->vehicle->type)) {
                $transitmode['vehicletype'] = $directionapi->routes[0]->legs[0]->steps[$i]->transit_details->line->vehicle->type;
            }
            $urldisplay = (explode('/', $transitmode[url]));
            $transitmode['url'] = '<a href="'.$transitmode[url].'" target="_blank" >'.$urldisplay[2].'</a>'; //temporary coded
            $drivingmode['transpcat'] = TravelManagerPlan::parse_transportation(array('vehicleType' => $transitmode['vehiclename']), array('origincity' => $transpdata['origincity']['name'], 'destcity' => $transpdata['destcity']['name']), $sequence);
            $transptitle = '<div class="subtitle">Possible Transportations</div>';

            $transp_category_fields = TravelManagerPlan::parse_transportaionfields(array('name' => $drivingmode['transpcat']['name'], 'tmtcid' => $drivingmode['transpcat']['cateid']), array('origincity' => $transpdata['origincity'], 'destcity' => $transpdata['destcity'], 'date' => $transpdata['departuretime']), $sequence);

            eval("\$transcategments_output .= \"".$template->get('travelmanager_plantrip_segment_catransportation')."\";");
            eval("\$transsegments_output .= \"".$template->get('travelmanager_plantrip_segment_transportation')."\";");

            unset($transitmode);
        }
        //temporary coded  loaded from google
        $drivingmode['transpcat'] = TravelManagerPlan::parse_transportation(array('vehicleType' => 'airplane'), array('origincity' => $transpdata['origincity']['name'], 'destcity' => $transpdata['destcity']['name']), $sequence);
        $transptitle = '<div class="subtitle">Possible Transportations</div>';

        $transp_category_fields = TravelManagerPlan::parse_transportaionfields(array('name' => $drivingmode['transpcat']['name'], 'tmtcid' => $drivingmode['transpcat']['cateid']), array('origincity' => $transpdata['origincity'], 'destcity' => $transpdata['destcity'], 'date' => $transpdata['departuretime']), $sequence);
        if(!empty($transp_category_fields)) {
            eval("\$transcategments_output .= \"".$template->get('travelmanager_plantrip_segment_catransportation')."\";");
            eval("\$transsegments_output .= \"".$template->get('travelmanager_plantrip_segment_transportation')."\";");
        }
        return $transsegments_output.$transcategments_output;
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

    public function get() {
        return $this->data;
    }

}