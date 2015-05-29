<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: vCard_class.php
 * Created:        @hussein.barakat    May 27, 2015 | 4:40:56 PM
 * Last Update:    @hussein.barakat    May 27, 2015 | 4:40:56 PM
 */

class vCard {
    private $vcardfile = array();
    private $vcard = array();
    private $types = array();

    public function __construct($contact = array()) {
        if(!empty($contact)) {
            $this->set_vcardfile($contact);
        }
    }

    public function set_vcardname($name = '') {
        if(!empty(trim($name))) {
            $this->vcard['name'] = $name;
        }
    }

    public function set_name($fname = '', $lname = '') {
        if(empty(trim($fname)) || empty(trim($lname))) {
            return;
        }
        $this->vcardfile .= 'N:'.$lname.';'.$fname.';;;'.PHP_EOL;
    }

    public function set_vcardfile($contact) {
        if(is_array($contact)) {
            $this->vcardfile = "BEGIN:VCARD\r\n";
            $this->vcardfile .= "VERSION:4.0\r\n";
            $this->set_name($contact['firstName'], $contact['lastName']);
            $this->set_fullname($contact['firstName'].' '.$contact['lastName']);
            $this->set_nickname($contact['displayName']);
            $this->set_organization($contact['organization']);
            $this->set_photo($contact['photo']);
            $this->set_phone($contact['phone']);
            $this->set_address($contact['address']);
            $this->set_email($contact['email']);
            $this->set_datestamp();
            $this->vcardfile .= 'END:VCARD'.PHP_EOL;
        }
        elseif(is_string($contact)) {
            $this->vcardfile = $contact;
        }
    }

    public function set_fullname($fullname = '') {
        if(empty(trim($fullname))) {
            return;
        }
        $this->vcardfile .= 'FN:'.$fullname.PHP_EOL;
        $this->vcard['name'] = $fullname;
    }

    public function set_email($email = '') {
        if(empty($email)) {
            return;
        }
        if(is_string($email) && !empty(trim($email))) {
            $this->vcardfile .= 'EMAIL:'.$email.PHP_EOL;
        }
        elseif(is_array($email)) {
            foreach($email as $e) {
                if(empty(trim($e))) {
                    continue;
                }
                $this->vcardfile .= 'EMAIL:'.$e.PHP_EOL;
            }
        }
    }

    public function parse_datestamp($timestamp, $utc = false) {
        if($utc == true) {
            $utc = 'Z';
        }
        return date('Ymd', $timestamp).'T'.date('His', $timestamp).$utc;
    }

    private function set_datestamp() {
        $this->vcardfile .= 'REV:'.$this->parse_datestamp(TIME_NOW).'Z'.PHP_EOL;
    }

    public function set_nickname($nickname = '') {
        if(empty(trim($nickname))) {
            return;
        }
        $this->vcardfile .= 'NICKNAME:'.$nickname.PHP_EOL;
    }

    public function set_organization($organization = '') {
        if(empty(trim($organization))) {
            return;
        }
        $this->vcardfile .= 'ORG:'.$organization.PHP_EOL;
    }

    public function download() {
        header('Content-Type: text/x-Vcard');
        header('Content-Disposition: inline; filename='.generate_alias($this->vcard['name']).'.vcf');
        output($this->get_vcard());
    }

    public function get_vcard() {
        return $this->vcardfile;
    }

    public function set_title($title = '') {
        if(empty(trim($title))) {
            return;
        }
        $this->vcardfile .= ' Title:'.$title.PHP_EOL;
    }

    public function set_photo($photo = array()) {
        if(empty($photo)) {
            return;
        }
        if(is_array($photo)) {
            foreach($photo as $type => $pic) {
                if(empty(trim($type)) || empty(trim($pic))) {
                    continue;
                }

                $this->vcardfile .= 'PHOTO;MEDIATYPE=image/'.$type.':base64,'.$pic.PHP_EOL;
            }
        }
    }

    public function set_address($address = array()) {
        if(is_empty($address)) {
            return;
        }
        foreach($address as $type => $address) {
            if(empty(trim($type)) || empty(trim($address))) {
                continue;
            }
            $this->vcardfile .= 'ADR;TYPE='.$type.';LABEL="'.$address.'":;;'.$address.PHP_EOL;
        }
    }

    public function set_phone($phone = array()) {
        if(is_empty($phone)) {
            return;
        }
        foreach($phone as $type => $number) {
            if(empty(trim($type)) || empty(trim($number))) {
                continue;
            }
            $this->vcardfile .= 'TEL;TYPE='.$type.',voice;VALUE =uri:tel:'.$number.PHP_EOL;
        }
    }

    public function get_userdata($uid) {
        if(empty($uid)) {
            return false;
        }
        $user = new Users($uid);
        $contact['firstName'] = $user->firstName;
        $contact['lastName'] = $user->lastName;
        $contact['displayName'] = $user->displayName;
        $contact['nickname'] = $user->displayName;
        $affiliatedemp = AffiliatedEmployees::get_data('uid='.$user->uid.' AND isMain=1');
        if(is_object($affiliatedemp)) {
            $affiliate = new Affiliates($affiliatedemp->affid);
            $contact['organization'] = $affiliate->get_displayname();
        }
        if(!empty($user->profilePicture)) {
            $type = substr($user->profilePicture, -3);
            $contact['photo'] = array('type' => $type, 'pic' => $user->profilePicture);
        }
        $contact['phone'] = array('work' => $user->telephone, 'work' => $user->telephone2, 'cell' => $user->mobile, 'cell' => $user->mobile2);
        $contact['address'] = array('work' => $user->addressLine1.' ', $user->addressLine2.' '.$user->building);
        $contact['email'] = $user->email;
        return $contact;
    }

}