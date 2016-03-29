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

        if(is_empty($data['name'], $data['city'], $data['telephone_intcode'], $data['telephone_number'], $data['addressLine1'], $data['avgPrice'])) {//$data['telephone_areacode'],
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
            $this->errorcode = 3;
            return $this;
        }
        $data['alias'] = generate_alias($data['name']);
        $data['createdBy'] = $core->user['uid'];
        $data['createdOn'] = TIME_NOW;
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
                $path = $core->settings['rootdir'].'/images/icons/completed.png';
                $alt = 'Yes';
            }
            else {
                $path = $core->settings['rootdir'].'/images/invalid.gif';
                $alt = 'No';
            }
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            $base64 = 'data:image/'.$type.';base64,'.base64_encode($data);
            $hotel['iscontracted'] = '<img src="'.$base64.'" alt="'.$alt.'"></>';
            //parse newly created hotel in bottom table
            $otherhotel = $hotel;
            if($newhotel->isApproved == 1) {
                $otherhotel['isapproved'] = 'Yes';
            }
            else {
                $otherhotel['isapproved'] = 'No';
            }
            if(empty($otherhotel['avgPrice'])) {
                $otherhotel['avgPrice'] = '-';
            }
            $otherhotel['city'] = $newhotel->get_city()->get_displayname();
            eval("\$newlycreatedhotel_tow = \"".$template->get('approvehotel_email_hotelsinsamecountry')."\";");
            //getting hotels in the same country details
            $hotelsinsamecountry = TravelManagerHotels::get_data(array('country' => $newhotel->country), array('returnarray' => true, 'order' => 'isApproved', 'simple' => false));
            if(is_array($hotelsinsamecountry)) {
                $hotelsinsamecountrysection = '<tr><th colspan="4" style="text-align: center;background-color: #D9D9C2">'.$lang->hotelsinsamecountry.'</th></tr>';
                foreach($hotelsinsamecountry as $hotelincountry) {
                    /**
                     * Skip the same hotel
                     */
                    if($newhotel->get_id() == $hotelincountry->get_id()) {
                        continue;
                    }
                    $otherhotel = $hotelincountry->get();
                    if($newhotel->isApproved == 1) {
                        $otherhotel['isapproved'] = 'Yes';
                    }
                    else {
                        $otherhotel['isapproved'] = 'No';
                    }
                    if(empty($otherhotel['avgPrice'])) {
                        $otherhotel['avgPrice'] = '-';
                    }
                    $otherhotel['city'] = $hotelincountry->get_city()->get_displayname();
                    eval("\$hotelsinsamecountrysection .= \"".$template->get('approvehotel_email_hotelsinsamecountry')."\";");
                    unset($otherhotel, $otherhotel['avgPrice']);
                }
            }

            $to[] = 'audrey.sacy@orkila.com';
            $to[] = 'pamela.carnaby@orkila.com';

            $affiliates = Affiliates::get_affiliates(array('country' => $newhotel->country), array('returnarray' => true));
            if(is_array($affiliates)) {
                foreach($affiliates as $affiliate_obj) {
                    $gm = Users::get_data(array('uid' => $affiliate_obj->generalManager), array('simple' => false));
                    if(is_object($gm)) {
                        $to[] = $gm->get_email();
                    }
                }
            }
            $createdby = $core->user['displayName'];
            eval("\$emailmessage = \"".$template->get('approvehotel_email')."\";");
            $mailer = new Mailer();
            $mailer = $mailer->get_mailerobj();
            $mailer->set_to(array_unique($to));
            $mailer->set_from('mailer@ocos.orkila.com');
            $mailer->set_cc('zaher.reda@orkila.com');
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
        if((is_empty($data['name'], $data['city'], $data['addressLine1'], $data['avgPrice'])) || (is_empty($data['telephone_intcode'], $data['telephone_number']) && empty($data['phone']))) {// $data['telephone_areacode'],
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
        global $db, $core;
        $approve['isApproved'] = '1';
        $approve['approvedBy'] = $core->user['uid'];
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

    public function get_currency() {
        if(!empty($this->data['currency'])) {
            return new Currencies(intval($this->data['currency']));
        }
        return false;
    }

    /**
     * Feed the function two hotel ids, and replace all occurences where hotel to be replaced shows up by the new hotel
     * @global type $db
     * @param type $replaced
     * @param type $replace_by
     * @return boolean
     */
    public function replace_hotel($replaced, $replace_by) {
        global $db;
        if($replace_by) {
            //replace existing accomodations hotel ids from old to new one
            $existing_accomodations = TravelManagerPlanaccomodations::get_data(array('tmhid' => intval($replaced)), array('returnarray' => true));
            if(is_array($existing_accomodations)) {
                $db->update_query(TravelManagerPlanaccomodations::TABLE_NAME, array('tmhid' => intval($replace_by)), 'tmhid='.intval($replaced));
            }
            //replace existing accomodations REVIEWS hotel ids from old to new one
            $existing_accomodations_revs = TravelManagerAccomodationsReview::get_data(array('tmhid' => intval($replaced)), array('returnarray' => true));
            if(is_array($existing_accomodations_revs)) {
                $db->update_query(TravelManagerAccomodationsReview::TABLE_NAME, array('tmhid' => intval($replace_by)), 'tmhid='.intval($replaced));
            }
        }
        return true;
    }

}