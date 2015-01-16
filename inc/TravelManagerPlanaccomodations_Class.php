<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: TravelManagerPlanHotels_Class.php
 * Created:        @tony.assaad    May 23, 2014 | 4:04:55 PM
 * Last Update:    @tony.assaad    May 23, 2014 | 4:04:55 PM
 */

/**
 * Description of TravelManagerPlanHotels_Class
 *
 * @author tony.assaad
 */
class TravelManagerPlanaccomodations extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'tmpaid';
    const TABLE_NAME = 'travelmanager_plan_accomodations';
    const SIMPLEQ_ATTRS = 'tmpsid,tmhid,tmpaid,priceNight,numNights';
    const UNIQUE_ATTRS = 'tmpsid,tmhid';
    const CLASSNAME = __CLASS__;

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

    public function create(array $data) {
        global $db;

        $tanspdata_array = array('tmpsid' => $data['tmpsid'],
                'tmhid' => $data['tmhid'],
                'priceNight' => $data['priceNight'],
                'inputChecksum' => $data['inputChecksum'],
                'numNights' => $data['numNights'],
                'currency' => $data['currency'],
                'paidBy' => $data['paidBy'],
                'paidById' => $data['paidById'],
        );

        $db->insert_query(self::TABLE_NAME, $data);
        $this->data[self::PRIMARY_KEY] = $db->last_id();
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $hoteldata['priceNight'] = $data['priceNight'];
            $hoteldata['numNights'] = $data['numNights'];
            $hoteldata['inputChecksum'] = $data['inputChecksum'];
            $hoteldata['paidBy'] = $data['paidBy'];
            $hoteldata['paidById'] = $data['paidById'];
            $hoteldata['currency'] = $data['currency'];
            $hoteldata['modifiedBy'] = $core->user['uid'];
            $hoteldata['modifiedOn'] = TIME_NOW;

            $db->update_query(self::TABLE_NAME, $hoteldata, ' tmhid='.intval($this->data['tmhid']).' AND tmpsid='.intval($this->data['tmpsid']));
        }
    }

    public static function get_planacco_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_data($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_segment() {
        return new TravelManagerPlanSegments($this->data['tmpsid']);
    }

    public function get_currency() {
        return new Currencies($this->data['currency']);
    }

    public function parse_paidby() {
        global $lang;

        $paidby_entities = array(
                'myaffiliate' => $lang->myaffiliate,
                'supplier' => $lang->supplier,
                'client' => $lang->client,
                'myself' => $lang->myself,
                'anotheraff' => $lang->anotheraff
        );
        foreach($paidby_entities as $val => $paidby) {
            $selected = '';
            if($this->data['paidBy'] === $val) {
                $selected = ' selected="selected"';
            }

            $paid_options.="<option value=".$val." {$selected}>{$paidby}</option>";
        }

        return $paid_options;
    }

    public function get_hotel() {
        return new TravelManagerHotels($this->data['tmhid']);
    }

    public function get_accomodationtype() {
        return new TravelManagerPlanaccomodationtype($this->data['accomType']);
    }

    public function get() {
        return $this->data;
    }

    public function get_convertedamount($fromcurrency, $tocurrency) {
        $exchagerate = $fromcurrency->get_latest_fxrate($tocurrency->alphaCode, array(), $fromcurrency->alphaCode);
        return $this->priceNight * $exchagerate;
    }

}