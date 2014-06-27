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

    public static function parse_bestflight() {
        $json = '{
 "kind": "qpxExpress#tripsSearch",
 "trips": {
  "kind": "qpxexpress#tripOptions",
  "requestId": "T0zAmR1vDWqo5s08t0KAtB",
  "data": {
   "kind": "qpxexpress#data",
   "airport": [
    {
     "kind": "qpxexpress#airportData",
     "code": "BEY",
     "city": "BEY",
     "name": "Beirut Rafic Hariri International"
    },
    {
     "kind": "qpxexpress#airportData",
     "code": "CDG",
     "city": "PAR",
     "name": "Paris Charles de Gaulle"
    }
   ],
   "city": [
    {
     "kind": "qpxexpress#cityData",
     "code": "BEY",
     "name": "Beirut"
    },
    {
     "kind": "qpxexpress#cityData",
     "code": "PAR",
     "name": "Paris"
    }
   ],
   "aircraft": [
    {
     "kind": "qpxexpress#aircraftData",
     "code": "77W",
     "name": "Boeing 777"
    }
   ],
   "tax": [
    {
     "kind": "qpxexpress#taxData",
     "id": "LB",
     "name": "Lebanon Embarkation Tax"
    },
    {
     "kind": "qpxexpress#taxData",
     "id": "VL_001",
     "name": "Lebanon Departure Tax"
    },
    {
     "kind": "qpxexpress#taxData",
     "id": "YQ",
     "name": "ME YQ surcharge"
    }
   ],
   "carrier": [
    {
     "kind": "qpxexpress#carrierData",
     "code": "ME",
     "name": "Middle East Airlines AirLiban"
    }
   ]
  },
  "tripOption": [
   {
    "kind": "qpxexpress#tripOption",
    "saleTotal": "LBP790300",
    "id": "GWuENQQwmu7NwLq2debchG001",
    "slice": [
     {
      "kind": "qpxexpress#sliceInfo",
      "duration": 280,
      "segment": [
       {
        "kind": "qpxexpress#segmentInfo",
        "duration": 280,
        "flight": {
         "carrier": "ME",
         "number": "205"
        },
        "id": "G8qMbEDJGSJbvhPF",
        "cabin": "COACH",
        "bookingCode": "H",
        "bookingCodeCount": 9,
        "marriedSegmentGroup": "0",
        "leg": [
         {
          "kind": "qpxexpress#legInfo",
          "id": "Lj6dL4eDBM4S9DKW",
          "aircraft": "77W",
          "arrivalTime": "2014-06-26T19:40+02:00",
          "departureTime": "2014-06-26T16:00+03:00",
          "origin": "BEY",
          "destination": "CDG",
          "destinationTerminal": "2E",
          "duration": 280,
          "operatingDisclosure": "OPERATED BY AIR FRANCE",
          "mileage": 1979
         }
        ]
       }
      ]
     }
    ],
    "pricing": [
     {
      "kind": "qpxexpress#pricingInfo",
      "fare": [
       {
        "kind": "qpxexpress#fareInfo",
        "id": "AmlNcHNJUEnThAwwOJ6roEROStLCJATBiHvl+UhA",
        "carrier": "ME",
        "origin": "BEY",
        "destination": "PAR",
        "basisCode": "HOLB"
       }
      ],
      "segmentPricing": [
       {
        "kind": "qpxexpress#segmentPricing",
        "fareId": "AmlNcHNJUEnThAwwOJ6roEROStLCJATBiHvl+UhA",
        "segmentId": "G8qMbEDJGSJbvhPF",
        "freeBaggageOption": [
         {
          "kind": "qpxexpress#freeBaggageAllowance",
          "bagDescriptor": [
           {
            "kind": "qpxexpress#bagDescriptor",
            "commercialName": "UPTO50LB 23KG BAGGAGE",
            "count": 1,
            "description": [
             "Up to 50 lb/23 kg"
            ],
            "subcode": "0C3"
           }
          ],
          "pieces": 1
         }
        ]
       }
      ],
      "baseFareTotal": "USD402.00",
      "saleFareTotal": "LBP606900",
      "saleTaxTotal": "LBP183400",
      "saleTotal": "LBP790300",
      "passengers": {
       "kind": "qpxexpress#passengerCounts",
       "adultCount": 1
      },
      "tax": [
       {
        "kind": "qpxexpress#taxInfo",
        "id": "VL_001",
        "chargeType": "GOVERNMENT",
        "code": "VL",
        "country": "LB",
        "salePrice": "LBP5000"
       },
       {
        "kind": "qpxexpress#taxInfo",
        "id": "YQ",
        "chargeType": "CARRIER_SURCHARGE",
        "code": "YQ",
        "salePrice": "LBP128400"
       },
       {
        "kind": "qpxexpress#taxInfo",
        "id": "LB",
        "chargeType": "GOVERNMENT",
        "code": "LB",
        "country": "LB",
        "salePrice": "LBP50000"
       }
      ],
      "fareCalculation": "BEY ME PAR 402.00HOLB NUC 402.00 END ROE 1.00 FARE USD 402.00 EQU LBP 606900 XT 50000LB 5000VL 128400YQ",
      "latestTicketingTime": "2014-06-26T11:21-04:00",
      "ptc": "ADT"
     }
    ]
   }
  ]
 }
}';
        $response_flightdata = json_decode($json);
        //  print_r($response_flightdata);
        foreach($response_flightdata->trips as $trips) {
            print_r($response_flightdata->trips->tripOption[0]->id);
            if(!empty($trips->airport)) {
                $flight['airport'] = $trips->airport;
            }

            //   print_R($tripitem);
        }
        for($i = 0; $i < count($response_flightdata->kind[0]->slice[0]); $i++) {
            echo $i;
//            $ai = $response_flightdata->trips[$i]->data;
        }
    }

    public
            function get() {
        return $this->airlines;
    }

}