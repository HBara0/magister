<?php

class vCard {

    private $vcardfile = array();
    private $vcard = array();
    private $types = array();

    public function __construct($contact = array()) {
        if (!empty($contact)) {
            $this->set_vcardfile($contact);
        }
    }

    public function set_vcardname($name = '') {
        $name = trim($name);
        if (!empty($name)) {
            $this->vcard['name'] = $name;
        }
    }

    public function set_name($fname = '', $lname = '') {
        $fname = trim($fname);
        $lname = trim($lname);
        if (empty($fname) || empty($lname)) {
            return;
        }
        $this->vcardfile .= 'N:' . $lname . ';' . $fname . ';;;' . "\r\n";
    }

    public function set_vcardfile($contact) {
        if (is_array($contact)) {
            $this->vcardfile = "BEGIN:VCARD\r\n";
            $this->vcardfile .= "VERSION:4.0\r\n";
            $this->set_name($contact['firstName'], $contact['lastName']);
            $this->set_fullname($contact['firstName'] . ' ' . $contact['lastName']);
            $this->set_nickname($contact['displayName']);
            $this->set_title($contact['title']);
            $this->set_organization($contact['organization']);
            $this->set_photo($contact['photo']);
            $this->set_phone($contact['phone']);
            $this->set_address($contact['address']);
            $this->set_email($contact['email']);
            $this->set_datestamp();
            $this->vcardfile .= 'END:VCARD' . "\r\n";
        }
        elseif (is_string($contact)) {
            $this->vcardfile = $contact;
        }
    }

    public function set_fullname($fullname = '') {
        $fullname = trim($fullname);
        if (empty($fullname)) {
            return;
        }
        $this->vcardfile .= 'FN:' . $fullname . "\r\n";
        $this->vcard['name'] = $fullname;
    }

    public function set_email($email = '') {
        if (empty($email)) {
            return;
        }

        $email = trim($email);
        if (is_string($email) && !empty($email)) {
            $this->vcardfile .= 'EMAIL:' . $email . "\r\n";
        }
        elseif (is_array($email)) {
            foreach ($email as $e) {
                $e = trim($e);
                if (empty($e)) {
                    continue;
                }
                $this->vcardfile .= 'EMAIL:' . $e . "\r\n";
            }
        }
    }

    public function parse_datestamp($timestamp, $utc = false) {
        if ($utc == true) {
            $utc = 'Z';
        }
        return date('Ymd', $timestamp) . 'T' . date('His', $timestamp) . $utc;
    }

    private function set_datestamp() {
        $this->vcardfile .= 'REV:' . $this->parse_datestamp(TIME_NOW) . 'Z' . "\r\n";
    }

    public function set_nickname($nickname = '') {
        $nickname = trim($nickname);
        if (empty($nickname)) {
            return;
        }
        $this->vcardfile .= 'NICKNAME:' . $nickname . "\r\n";
    }

    public function set_organization($organization = '') {
        $organization = trim($organization);
        if (empty($organization)) {
            return;
        }
        $this->vcardfile .= 'ORG:' . $organization . "\r\n";
    }

    public function download() {
        header('Content-Type: text/x-Vcard');
        header('Content-Disposition: inline; filename=' . generate_alias($this->vcard['name']) . '.vcf');
        output($this->get_vcard());
    }

    public function get_vcard() {
        return $this->vcardfile;
    }

    public function set_title($title = '') {
        $title = trim($title);
        if (empty($title)) {
            return;
        }
        $this->vcardfile .= 'TITLE:' . $title . "\r\n";
    }

    public function set_photo($photo = array()) {
        if (empty($photo)) {
            return;
        }
//        if(is_array($photo)) {
//            foreach($photo as $type => $pic) {
//                if(empty(trim($type)) || empty(trim($pic))) {
//                    continue;
//                }
//
//                $this->vcardfile .= 'PHOTO;MEDIATYPE=image/'.$type.':base64,'.$pic."\r\n";
//            }
//        }
    }

    public function set_address($address = array()) {
        if (is_empty($address)) {
            return;
        }
        foreach ($address as $type => $address) {
            $address = trim($address);
            $type = trim($type);
            if (empty($type) || empty($address)) {
                continue;
            }
            $this->vcardfile .= 'ADR;TYPE=' . $type . ';LABEL="' . $address . '":;;' . $address . "\r\n";
        }
    }

    public function set_phone($phone = array()) {
        if (is_empty($phone)) {
            return;
        }
        foreach ($phone as $type => $number) {
            $number = trim($number);
            $type = trim($type);
            if (empty($type) || empty($number)) {
                continue;
            }
            $this->vcardfile .= 'TEL;TYPE=' . $type . ',voice:' . $number . "\r\n";
        }
    }

    public function get_userdata($uid) {
        if (empty($uid)) {
            return false;
        }
        $user = new Users($uid, false);
        $contact['firstName'] = $user->firstName;
        $contact['lastName'] = $user->lastName;
        $contact['displayName'] = $user->displayName;
        $contact['nickname'] = $user->displayName;
        $affiliatedemp = AffiliatedEmployees::get_data('uid=' . $user->uid . ' AND isMain=1');
        if (is_object($affiliatedemp)) {
            $affiliate = new Affiliates($affiliatedemp->affid, false);
            $contact['organization'] = $affiliate->get_displayname();
        }

        $contact['title'] = implode(', ', $user->get_positions());

        if (!empty($user->profilePicture)) {
            $type = substr($user->profilePicture, -3);
            $contact['photo'] = array('type' => $type, 'pic' => $user->profilePicture);
        }

        if (!empty($affiliate->phone1)) {
            $contact['phone'] = array('work' => '+' . $affiliate->phone1);
            if (!empty($user->internalExtension)) {
                $contact['phone']['work'] .= ' x ' . $user->internalExtension;
            }
        }

        if ($contact['mobileIsPrivate'] != 1 || !empty($user->mobile)) {
            $contact['phone']['cell'] = '+' . $user->mobile;
        }


        $contact['address'] = array('work' => $affiliate->addressLine1 . ' ' . $affiliate->addressLine2);
        $contact['email'] = $user->email;
        return $contact;
    }

}
