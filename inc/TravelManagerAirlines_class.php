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
        $requestdata = json_encode(array('request' => array('slice' => array(array('origin' => $requestdata['origin'], 'destination' => $requestdata['destination'], 'date' => $requestdata['date'], 'permittedCarrier' => $requestdata['permittedCarrier'])))));
        //to send the reqeustdata to google api and return the response array.
        return $requestdata;
    }

    private function is_roundtrip($slices) {
        if($slices > 1) {
            return 1;
        }
        else {
            return;
        }
    }

    private function parse_responsefilghts($response_flightdata, $transpcatid, $sequence) {
        global $core, $template;
        for($tripoptnum = 0; $tripoptnum <= count($response_flightdata->trips->tripOption); $tripoptnum++) {
            $airportcount = count($trips->airport);
            if($airportcount >= 0) {
                for($i = 0; $i <= $airportcount; $i++) {
                    if(!empty($trips->airport[$i])) {
                        $flight[$i]['airport'] = $trips->airport[$i]->name;
                    }
                }
            } print_R($transpcatid);
            for($slicenum = 0; $slicenum < count($response_flightdata->trips->tripOption[$tripoptnum]->slice); $slicenum++) {
                $slices = count($response_flightdata->trips->tripOption[$tripoptnum]->slice);

                $triptype = self::is_roundtrip(count($response_flightdata->trips->tripOption[$tripoptnum]->slice));

                for($segmentnum = 0; $segmentnum < count($response_flightdata->trips->tripOption[$tripoptnum]->slice[$slicenum]->segment); $segmentnum++) {
                    $departuretime = strtotime($response_flightdata->trips->tripOption[$tripoptnum]->slice[$slicenum]->segment[$segmentnum]->leg[0]->departureTime);
                    $arrivaltime = strtotime($response_flightdata->trips->tripOption[$tripoptnum]->slice[$slicenum]->segment[$segmentnum]->leg[0]->arrivalTime);
                    $flight['departuretime'] = date($core->settings['timeformat'], $departuretime);
                    $flight['arrivaltime'] = date('h:i A', $arrivaltime);

                    $hours = floor($response_flightdata->trips->tripOption[$tripoptnum]->slice[$slicenum]->segment[$segmentnum]->leg[0]->duration / 60);
                    $minutes = ($response_flightdata->trips->tripOption[$tripoptnum]->slice[$slicenum]->segment[$segmentnum]->leg[0]->duration / 60);
                    $flight['duration'] = sprintf('%2dh %2dm', $hours, $minutes);

                    if(isset($response_flightdata->trips->tripOption[$tripoptnum]->saleTotal) && !empty($response_flightdata->trips->tripOption[$tripoptnum]->saleTotal)) {
                        $flight['currcode'] = substr($response_flightdata->trips->tripOption[$tripoptnum]->saleTotal, 0, 3);
                    }

                    $flight['saleTotal'] = substr($response_flightdata->trips->tripOption[$tripoptnum]->saleTotal, 3);
                    $currency_obj = new Currencies('USD');
                    $currency = $currency_obj->get_currency_by_alphacode($flight['currcode']);
                    $fxrates[$currency['alphaCode']] = $currency_obj->get_latest_fxrate($currency['alphaCode'], array('incDate' => 1));

                    for($carriernum = 0; $carriernum < count($response_flightdata->trips->data->carrier); $carriernum++) {
                        if($response_flightdata->trips->tripOption[$tripoptnum]->slice[$slicenum]->segment[$segmentnum]->flight->carrier == $flight['carriernum'][$carriernum] = $response_flightdata->trips->data->carrier[$carriernum]->code) {
                            $flight['carrier'] = $response_flightdata->trips->data->carrier[$carriernum]->name;
                            break;
                        }
                    }
                    $flight['flightnumber'] = $response_flightdata->trips->tripOption[$tripoptnum]->slice[$slicenum]->segment[$segmentnum]->flight->carrier.' '.$response_flightdata->trips->tripOption[$tripoptnum]->slice[$slicenum]->segment[$segmentnum]->flight->number;

                    $flight['flightid'] = $response_flightdata->trips->tripOption[$tripoptnum]->id;
                    $flight['pricing'] = round($flight['saleTotal'] / $fxrates[$currency['alphaCode']]['rate'], 2);

                    $flight['flightdetails'] = serialize($flight['flightnumber'].$flight['flightid']);

                    if($triptype == 1) {
                        $flight['triptype'] = 'roundtrip';
                        $flight['pricing']+= $flight['pricing'];

                        eval("\$flights_records_roundtripsegments_details .= \"".$template->get('travelmanager_plantrip_segment_catransportation_flightdetails_roundtrip_segments_details')."\";");

                        //eval("\$flights_records_roundtripsegments  = \"".$template->get('travelmanager_plantrip_segment_catransportation_flightdetails_roundtrip_segments')."\";");
                    }
                    // one way trip
                    else {
                        eval("\$flights_records_roundtripsegments_details .= \"".$template->get('travelmanager_plantrip_segment_catransportation_flightdetails_roundtrip_segments_details')."\";");
                    }
                }
                eval("\$flights_records .= \"".$template->get('travelmanager_plantrip_segment_catransportation_flightdetails')."\";");
                $flights_records_segments = $flights_records_roundtripsegments = $flights_records_roundtripsegments_details = '';
            }
        }
        return $flights_records;
    }

    /*
     * Parse JSON best flight  data  from google trips API
     * @param	int		$length		Length of the random string
     * @return  parsed Html	$output
     */
    public static function parse_bestflight(array $transpcat, $sequence) {
        global $core, $template;

        $json = file_get_contents('./modules/travelmanager/jsonflightdetails_onetrip.txt');

        $response_flightdata = json_decode($json);
        $flights_records = '<div class="subtitle" style="width:100%;margin:10px; box-shadow: 0px 2px 1px rgba(0, 0, 0, 0.1), 0px 1px 1px rgba(0, 0, 0, 0.1); border: 1px  rgba(0, 0, 0, 0.1) solid;;">Best Flights</div>';

        return self::parse_responsefilghts($response_flightdata, $transpcat['tmtcid'], $sequence);
    }

    public function get() {
        return $this->airlines;
    }

}