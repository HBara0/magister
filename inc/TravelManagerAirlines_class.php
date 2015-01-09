<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: TravelManagerAirlines.php
 * Created:        @tony.assaad    May 16, 2014 | 11:04:43 AM
 * Last Update:    @tony.assaad    May 16, 2014 | 11:04:43 AM
 */

/**
 * Description of TravelManagerAirlines
 *
 * @author tony.assaad
 */
class TravelManagerAirlines {
    private $airlines = array();

    const PRIMARY_KEY = 'alid';
    const TABLE_NAME = 'travelmanager_airlines';

    public function __construct($id) {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    private function read($id) {
        global $db;
        $this->airlines = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function get_country() {
        return new Countries($this->airlines['coid']);
    }

    public static function get_airlines_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_airlines($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public static function build_flightrequestdata($requestdata) {

        if(!is_numeric($requestdata['date'])) {
            $requestdata['date'] = strtotime($requestdata['date']);
        }

        if($requestdata['date'] < TIME_NOW) {
            $requestdata['date'] = TIME_NOW + (3600);
        }

        $requestdata['date'] = date('Y-m-d', $requestdata['date']);

        $requestdata = json_encode(array('request' => array("passengers" => array("adultCount" => 1), "solutions" => 20, 'slice' => array(array('origin' => $requestdata['origin'], 'destination' => $requestdata['destination'], 'date' => $requestdata['date'], 'permittedCarrier' => $requestdata['permittedCarrier'])))));
//to send the reqeustdata to google api and return the response array.
        return $requestdata;
    }

    private function is_roundtrip($slices) {
        if(count($slices) > 1) {
            return true;
        }
        return false;
    }

    private function parse_responsefilghts($response_flightdata, $transpcat = array(), $sequence, $source = 'plan') {
        global $core, $template, $lang;
        foreach($response_flightdata->trips->tripOption as $tripoptnum => $tripoption) {
//for($tripoptnum = 0; $tripoptnum <= count($response_flightdata->trips->tripOption); $tripoptnum++) {
//$tripoption = $response_flightdata->trips->tripOption[$tripoptnum];
            $airportcount = count($trips->airport);
            if($airportcount >= 0) {
                for($i = 0; $i <= $airportcount; $i++) {
                    if(!empty($trips->airport[$i])) {
                        $flight[$i]['airport'] = $trips->airport[$i]->name;
                    }
                }
            }

            $is_roundtrip = self::is_roundtrip($tripoption->slice);
            foreach($tripoption->slice as $slicenum => $slice) {
// for($slicenum = 0; $slicenum < count($response_flightdata->trips->tripOption[$tripoptnum]->slice); $slicenum++) {
                foreach($slice->segment as $segmentnum => $segment) {
                    //  for($segmentnum = 0; $segmentnum < count($response_flightdata->trips->tripOption[$tripoptnum]->slice[$slicenum]->segment); $segmentnum++) {
                    $departuretime = strtotime($segment->leg[0]->departureTime);
                    $arrivaltime = strtotime($segment->leg[0]->arrivalTime);
                    $flight['departuredate'] = date($core->settings['dateformat'], $departuretime);
                    $flight['departuretime'] = date($core->settings['timeformat'], $departuretime);
                    $flight['arrivaldate'] = date($core->settings['dateformat'], $arrivaltime);
                    $flight['arrivaltime'] = date($core->settings['timeformat'], $arrivaltime);

                    $flight['origin'] = $segment->leg[0]->origin;
                    $flight['cabin'] = $segment->cabin;
                    $flight['destination'] = $segment->leg[0]->destination;

                    $flight['duration'] = sprintf('%2dh %2dm', floor($segment->leg[0]->duration / 60), ($segment->leg[0]->duration % 60));

                    if(isset($segment->connectionDuration)) {
                        $flight['connectionDuration'] = sprintf('%2dh %2dm', floor($segment->connectionDuration / 60), ($segment->connectionDuration % 60));
                        $connectionduration = '<div class="border_top border_bottom" style="padding: 10px; font-style: italic;">Connection: '.$flight['connectionDuration'].'</div>';
                    }

                    if(isset($tripoption->saleTotal) && !empty($tripoption->saleTotal)) {
                        $flight['currcode'] = substr($tripoption->saleTotal, 0, 3);
                    }

                    $flight['saleTotal'] = substr($tripoption->saleTotal, 3);
                    $currency_obj = new Currencies('USD');
                    $currency = $currency_obj->get_currency_by_alphacode($flight['currcode']);
                    $fxrates[$currency['alphaCode']] = $currency_obj->get_latest_fxrate($currency['alphaCode'], array('incDate' => 1));

                    for($carriernum = 0; $carriernum < count($response_flightdata->trips->data->carrier); $carriernum++) {
                        if($response_flightdata->trips->tripOption[$tripoptnum]->slice[$slicenum]->segment[$segmentnum]->flight->carrier == $flight['carriernum'][$carriernum] = $response_flightdata->trips->data->carrier[$carriernum]->code) {
                            $flight['carrier'] = $response_flightdata->trips->data->carrier[$carriernum]->name;
                            break;
                        }
                    }
                    $flight['flightnumber'] = $segment->flight->carrier.' '.$segment->flight->number;

                    $flight['flightid'] = $response_flightdata->trips->tripOption[$tripoptnum]->id;
                    $flight['pricing'] = round($flight['saleTotal'] / $fxrates[$currency['alphaCode']]['rate'], 2);
                    //  $flight['flightdetails'] = base64_encode(serialize($flight['flightnumber'].$flight['flightid']));

                    $flight['flightdetails'] = htmlspecialchars(json_encode($response_flightdata->trips->tripOption[$tripoptnum]));
                    if($is_roundtrip == true) {
                        $flight['triptype'] = 'Round Trip';
                        $flight['pricing'] += $flight['pricing'];

                        eval("\$flights_records_roundtripsegments_details .= \"".$template->get('travelmanager_plantrip_segment_catransportation_flightdetails_roundtrip_segments_details')."\";");

                        //eval("\$flights_records_roundtripsegments  = \"".$template->get('travelmanager_plantrip_segment_catransportation_flightdetails_roundtrip_segments')."\";");
                    }
                    // one way trip
                    else {
                        eval("\$flights_records_roundtripsegments_details .= \"".$template->get('travelmanager_plantrip_segment_catransportation_flightdetails_roundtrip_segments_details')."\";");
                    }

                    unset($connectionduration, $flight['connectionDuration']);
                    // }
                }
                if(!empty($source) && ($source == 'plan')) {
                    if($transpcat['selectedflight'] == $flight['flightnumber']) {
                        $checkbox['selctedflight'] = "checked='checked'";
                    }
                    $transpcatid = $transpcat['tmtcid'];
                    $flightnumber_checkbox = ' <input type="checkbox" name="segment['.$sequence.'][tmtcid]['.$transpcatid.']['.$flight[flightid].'][flightNumber]" value="'.$flight['flightnumber'].'"'.$checkbox['selctedflight'].'/>';
                    unset($checkbox['selctedflight']);
                }
            }
            eval("\$flights_records .= \"".$template->get('travelmanager_plantrip_segment_catransportation_flightdetails')."\";");
            $flights_records_segments = $flights_records_roundtripsegments = $flights_records_roundtripsegments_details = '';
            //}
        }
        return $flights_records;
    }

    /*
     * Parse JSON best flight  data  from google trips API
     * @param	int		$length		Length of the random string
     * @return  parsed Html	$output
     */
    public static function parse_bestflight($data, array $transpcat, $sequence, $source = 'plan') {
        $response_flightdata = json_decode($data);
        //$flights_records = '<div class = "subtitle" style = "width:100%;margin:10px; box-shadow: 0px 2px 1px rgba(0, 0, 0, 0.1), 0px 1px 1px rgba(0, 0, 0, 0.1); border: 1px  rgba(0, 0, 0, 0.1) solid;;">Best Flights</div>';

        return self::parse_responsefilghts($response_flightdata, $transpcat, $sequence, $source);
    }

    public static function get_flights($request, $apikey = null) {
//        $ch = curl_init('https://www.googleapis.com/qpxExpress/v1/trips/search?key=AIzaSyDXUgYSlAux8xlE8mA38T0-_HviEPiM5dU');
        // curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
//        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
//        $result = curl_exec($ch);
        //$result = file_get_contents('./modules/travelmanager/jsonflightdetails_roundtrip.txt');
        $result = file_get_contents('./modules/travelmanager/jsonflightdetailsPAR.txt');
        //      curl_close($ch);
        return $result;
    }

    public function __get($name) {
        if(array_key_exists($name, $this->airlines)) {
            return $this->airlines[$name];
        }
    }

    public function get() {
        return $this->airlines;
    }

}