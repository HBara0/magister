<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: TravelManagerPlanTransps_Class.php
 * Created:        @tony.assaad    May 23, 2014 | 3:41:53 PM
 * Last Update:    @tony.assaad    May 23, 2014 | 3:41:53 PM
 */

/**
 * Description of TravelManagerPlanTransps_Class
 *
 * @author tony.assaad
 */
class TravelManagerPlanTransps extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'tmpltid';
    const TABLE_NAME = 'travelmanager_plan_transps';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'tmpsid,tmtcid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public static function get_transpsegments_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_data($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

//    public function save(array $data = array()) {
//        if(empty($data)) {
//            $data = $this->data;
//        }
//
//        $tmptransp = TravelManagerPlanTransps::get_data(array(self::PRIMARY_KEY => $data[self::PRIMARY_KEY]));
//        if(is_object($tmptransp)) {
//            $tmptransp->update($data);
//        }
//        else {
//            $tmptransp = TravelManagerPlanTransps::get_data(array(TravelManagerTranspCategories::PRIMARY_KEY => $data[TravelManagerTranspCategories::PRIMARY_KEY], TravelManagerPlanSegments::PRIMARY_KEY => $data[TravelManagerPlanSegments::PRIMARY_KEY]));
//            if(is_object($tmptransp)) {
//                $tmptransp->update($data);
//            }
//            else {
//                $this->create($data);
//            }
//        }
//    }

    protected function update(array $data) {
        global $db, $core;
        /* Specify transportation categories As isMain (if suggested by the system) */

        $valid_attrs = array('tmpsid', 'tmtcid', 'fare', 'vehicleNumber', 'companyName', 'flightNumber', 'transpDetails', 'paidBy', 'paidById', 'transpType', 'isUserSuggested', 'inputChecksum', 'currency', 'seatingDescription', 'isRoundTrip', 'stopDescription', 'class', 'isMinCost');
        $valid_attrs = array_combine($valid_attrs, $valid_attrs);
        $data = array_intersect_key($data, $valid_attrs);
        if(empty($data['isMinCost'])) {
            $data['isMinCost'] = 0;
        }
        if($data['paidBy'] != 'anotheraff') {
            unset($data['paidById']);
        }
        $data['modifiedOn'] = TIME_NOW;
        $data['modifiedBy'] = $core->user['uid'];
        $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
    }

    protected function create(array $transportdata = array()) {
        global $db, $core;

        $transp_details = base64_decode($transportdata['transpDetails'], true);

        if($transp_details != false) {
            $transportdata['transpDetails'] = $transp_details;
        }
        $tanspdata_array = array('tmpsid' => $transportdata['tmpsid'],
                'tmtcid' => $transportdata['tmtcid'],
                'fare' => $transportdata['fare'],
                'vehicleNumber' => $transportdata['vehicleNumber'],
                'flightNumber' => $transportdata['flightNumber'],
                'transpDetails' => $transportdata['transpDetails'],
                'paidBy' => $transportdata['paidBy'],
                'paidById' => $transportdata['paidById'],
                'transpType' => $transportdata['transpType'],
                'createdOn' => TIME_NOW,
                'createdBy' => $core->user['uid'],
                'isUserSuggested' => $transportdata['isUserSuggested'],
                'inputChecksum' => $transportdata['inputChecksum'],
                'currency' => $transportdata['currency'],
                'seatingDescription' => $transportdata['seatingDescription'],
                'isRoundTrip' => $transportdata['isRoundTrip'],
                'isMinCost' => $transportdata['isMinCost'],
                'companyName' => $transportdata['companyName'],
                'stopDescription' => $transportdata['stopDescription'],
                'class' => $transportdata['class']
        );

        if($tanspdata_array['paidBy'] != 'anotheraff') {
            unset($tanspdata_array['paidById']);
        }
        $db->insert_query(self::TABLE_NAME, $tanspdata_array);
        $this->data[self::PRIMARY_KEY] = $db->last_id();
    }

    public function get_segment() {
        return new TravelManagerPlanSegments($this->data['tmpsid']);
    }

    public function get_transpcategory() {
        return new TravelManagerTranspCategories($this->data['tmtcid']);
    }

    public function get_convertedamount(Currencies $tocurrency) {
        if($this->currency == $tocurrency->numCode) {
            return $this->fare;
        }
        $fromcurrency = new Currencies($this->currency);
        $exchangerate = $tocurrency->get_latest_fxrate($tocurrency->alphaCode, array(), $fromcurrency->alphaCode);

        if(empty($exchangerate)) {
            $reverserate = $tocurrency->get_latest_fxrate($fromcurrency->alphaCode, array(), $tocurrency->alphaCode);
            if(!empty($reverserate)) {
                $exchangerate = 1 / $reverserate;
                $tocurrency->set_fx_rate($fromcurrency->numCode, $tocurrency->numCode, $exchangerate);
            }
        }
        return $this->fare * $exchangerate;
    }

    public function get_traspclass() {
        return new TravelManagerPlanTranspClass($this->data['class']);
    }

}
?>