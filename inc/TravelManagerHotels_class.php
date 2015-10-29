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
class TravelManagerHotels extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'tmhid';
    const TABLE_NAME = 'travelmanager_hotels';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'name';
    const UNIQUE_ATTRS = 'country,city,alias';
    const SIMPLEQ_ATTRS = 'tmhid, name,alias,city,country,isApproved,avgPrice,stars,isContracted,currency,addressLine1';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

//    protected function read($id) {
//        global $db;
//        $this->hotels = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
//    }

    public function create(array $data) {
        global $db, $template, $core, $lang;

        if(is_empty($data['name'], $data['city'], $data['telephone_intcode'], $data['telephone_number'], $data['addressLine1'])) {//$data['telephone_areacode'],
            $this->errorcode = 1;
            return false;
        }
        $data['phone'] = $data['telephone_intcode'].'-'.$data['telephone_number']; //.'-'.$data['telephone_areacode']
        unset($data['telephone_intcode'], $data['telephone_number']); //, $data['telephone_areacode'],
        $city = new Cities($data['city']);
        if(is_object($city)) {
            $data['country'] = $city->coid;
        }
        if(!empty($data['contactEmail']) && !filter_var($data['contactEmail'], FILTER_VALIDATE_EMAIL)) {
            $this->errorcode = 2;
            return $this;
        }
        $regex_web = '#((https?://|www\.)([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)#';
        if(!empty($data['website']) && !preg_match($regex_web, $data['website'])) {
            $this->errorcode = 2;
            return $this;
        }
        $data['alias'] = generate_alias($data['name']);
        $db->insert_query(self::TABLE_NAME, $data);
        $this->data[self::PRIMARY_KEY] = $db->last_id();
        $this->errorcode = 0;
        //sending the approve email process
        $newhotel = new TravelManagerHotels($db->last_id(), false);
        if(is_object($newhotel)) {
            $hotel = $newhotel->get();
            $hotel['city'] = $newhotel->get_city()->get_displayname();
            $hotel['country'] = $newhotel->get_country()->get_displayname();
            if($newhotel->isContracted == 1) {
                $hotel['iscontracted'] = '<img src="'.$core->settings['rootdir'].'\images\icons\completed.png">';
            }
            else {
                $hotel['iscontracted'] = '<img src="'.$core->settings['rootdir'].'\images\invalid.gif">';
            }
            //getting hotels in the same country details
            $hotelsinsamecountry = TravelManagerHotels::get_data(array('country' => $newhotel), array('returnarray' => true, 'simple' => false));
            if(is_array($hotelsinsamecountry)) {
                foreach($hotelsinsamecountry as $hotelincountry) {
                    $otherhotel = $hotelincountry->get();
                    if($newhotel->isApproved == 1) {
                        $otherhotel['isapproved'] = '<img src="'.$core->settings['rootdir'].'\images\icons\completed.png">';
                    }
                    else {
                        $otherhotel['isapproved'] = '<img src="'.$core->settings['rootdir'].'\images\invalid.gif">';
                    }
                    if(empty($otherhotel['avgPrice'])) {
                        $otherhotel['avgPrice'] = '-';
                    }
                    eval("\$hotelsinsamecountrysection .= \"".$template->get('approvehotel_email_hotelsinsamecountry')."\";");
                    unset($otherhotel);
                }
            }
            eval("\$emailmessage = \"".$template->get('approvehotel_email')."\";");
            $mailer = new Mailer();
            $mailer = $mailer->get_mailerobj();
            $mailer->set_to('zaher.reda@orkila.com');
            $mailer->set_from('mailer@ocos.orkila.com');
            $mailer->set_subject('New Hotel to Approve');
            $mailer->set_message($emailmessage);
            $mailer->send();
//            if($mailer->get_status() === true) {
//                $this->errorcode = 0;
//            }
//            else {
//                $this->errorcode = 1;
//            }
            $this->errorcode = 0;
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if((is_empty($data['name'], $data['city'], $data['addressLine1'])) || (is_empty($data['telephone_intcode'], $data['telephone_number']) && empty($data['phone']))) {// $data['telephone_areacode'],
            $this->errorcode = 2;
            return false;
        }
        if(is_array($data)) {
            $regex_web = '#((https?://|www\.)([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)#';
            if(!empty($data['website']) && !preg_match($regex_web, $data['website'])) {
                $this->errorcode = 2;
                return $this;
            }
            $update_array['name'] = $data['name'];
            $update_array['country'] = $data['country'];
            $update_array['city'] = $data['city'];
            $update_array['addressLine1'] = $data['addressLine1'];
            $update_array['addressLine2'] = $data['addressLine2'];
            $update_array['postCode'] = $data['postCode'];
            $update_array['poBox'] = $data['poBox'];
            $update_array['fax'] = $data['fax'];
            $update_array['website'] = $data['website'];
            $update_array['mainEmail'] = $data['mainEmail'];
            $update_array['stars'] = $data['stars'];
            $update_array['mgmtReview'] = $data['mgmtReview'];
            $update_array['isContracted'] = $data['isContracted'];
            $update_array['isApproved'] = $data['isApproved'];
            $update_array['avgPrice'] = $data['avgPrice'];
            $update_array['currency'] = $data['currency'];
            $update_array['contactPerson'] = $data['contactPerson'];
            $update_array['contactEmail'] = $data['contactEmail'];
            $update_array['distance'] = $data['distance'];
        }
        $update_array['alias'] = generate_alias($data['name']);
        if(isset($data['phone']) && !empty($data['phone'])) {
            $update_array['phone'] = $data['phone'];
        }
        else {
            $update_array['phone'] = $data['telephone_intcode'].'-'.$data['telephone_number']; //'-'.$data['telephone_areacode']
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    public function get_country() {
        return new Countries($this->data['country']);
    }

    public function get_city() {
        return new Cities($this->data['city']);
    }

    public static function get_hotels_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_hotels($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_review() {
        return TravelManagerAccomodationsReview::get_accoreviews('tmhid='.intval($this->data['tmhid']), array('ORDER' => array('by' => 'createdOn', 'sort' => 'DESC'), 'limit' => '0,1'));
    }

    public function get_editlink() {
        global $core;
        return $core->settings['rootdir'].'/index.php?module=travelmanager/edithotel&id='.$this->data[self::PRIMARY_KEY];
    }

    public function approve_hotel() {
        global $db;
        $approve['isApproved'] = '1';
        $query = $db->update_query(self::TABLE_NAME, $approve, 'tmhid ='.$this->tmhid);
        if($query) {
            return true;
        }
        else {
            return false;
        }
    }

    public function get_warning($data) {
        global $lang;
        if($data['avgprice'] != 'N/A' && !empty($data['avgprice']) && !empty($data['pricepernight'])) {
            $tocurrency = new Currencies('USD');
            $fromcurrency = new Currencies($data['currency']);
            $exchangerate = $tocurrency->get_latest_fxrate($tocurrency->alphaCode, array(), $fromcurrency->alphaCode);
            $pricepernight_usd = $data['pricepernight'] * $exchangerate;
            if($pricepernight_usd > (((10 * $data['avgprice']) / 100) + $data['avgprice'])) {
                return '<p style="color:red">'.$lang->hotelpricewarning.'</p>';
            }
        }
    }

}