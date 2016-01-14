<?php

class Entities extends AbstractClass {
    protected $eid = 0;
    protected $status = false;
    protected $data = array();
    protected static $groupsup_names = array(1 => 'Solvay', 2 => 'Roquette', 3 => 'Wacker', 4 => 'Aditya Birla', 5 => 'Novacyl', 6 => 'Ametech', 7 => 'Vencorex', 8 => 'Meggle', 9 => 'AB Enzymes');

    const PRIMARY_KEY = 'eid';
    const TABLE_NAME = 'entities';
    const DISPLAY_NAME = 'companyName';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = '';

    public function __construct($data, $action = '', $simple = true) {
        if(is_array($data)) {
            $this->data = $data;
            switch($action) {
                case 'edit':
                    $this->modify();
                    break;
                case 'add_representative':
                    $this->create_representative();
                    break;
                case 'set_entitylogo':
                    $this->upload_logo();
                    break;
                default:
                    $this->create();
                    break;
            }
        }
        else {
            $this->data = $this->read($data, $simple);
        }
    }

    protected function update(array $data) {

    }

    public function save(array $data = array()) {

    }

    protected function create(array $data = array()) {
        global $db, $core, $lang;
        if(empty($this->data['companyName'])) {
            output_xml("<status>false</status><message>{$lang->specifycompanyname}</message>");
            $this->status = false;
            exit;
        }
        if(!$this->entity_exists($this->data['companyName'])) {
            if(empty($this->data['affid'])) {
                output_xml("<status>false</status><message>{$lang->specifyanaffiliate}</message>");
                $this->status = false;
                exit;
            }
            $noncurrentitytypes = array('pc', 'cs');
            if(!in_array($this->data['type'], $noncurrentitytypes)) {
                if(array_key_exists('repName', $this->data) || array_key_exists('repEmail', $this->data)) {
                    if($this->data['repName'] == 'na' || $this->data['repName'] == 'n/a') {
                        $representatives[0]['rpid'] = $db->fetch_field($db->query("SELECT rpid FROM ".Tprefix."representatives WHERE name='n/a'"), 'rpid');
                    }
                    else {
                        $this->create_representative();
                        $representatives[0]['rpid'] = $db->last_id();
                    }
                    unset($this->data['repName'], $this->data['repEmail']);
                }
                else {
                    $representatives = $this->data['representative']; //;$this->workout_representatives();
                    unset($this->data['representative'], $this->data['rep_numrows']);
                }
                if(is_array($representatives)) {
                    $representatives = array_filter(array_map('array_filter', $representatives));
                }
                if(empty($representatives)) {
                    output_xml("<status>false</status><message>{$lang->fillallrequiredfields}</message>");
                    $this->status = false;
                    exit;
                }
            }
            else {
                unset($this->data['representative'], $this->data['rep_numrows']);
            }
            $affiliates = $this->data['affid'];
            unset($this->data['affid']);

            if(isset($this->data['users']) && !empty($this->data['users'])) {
                $employees = $this->data['users'];
                unset($this->data['users'], $this->data['users_numrows']);
            }
            /* else
              {
              output_xml("<status>false</status><message>{$lang->specifyauser} Select a user</message>");
              $this->status = false;
              exit;
              } */

            if(isset($this->data['psid']) && !empty($this->data['psid'])) {
                $segments = $this->data['psid'];
                unset($this->data['psid']);
            }
            else {
                output_xml("<status>false</status><message>{$lang->specifyasegment}</message>");
                $this->status = false;
                exit;
            }

            if(!is_empty($this->data['telephone_intcode'], $this->data['telephone_areacode'], $this->data['telephone_number'])) {
                $this->data['phone1'] = $this->data['telephone_intcode'].'-'.$this->data['telephone_areacode'].'-'.$this->data['telephone_number'];
            }

            if(!is_empty($this->data['telephone2_intcode'], $this->data['telephone2_areacode'], $this->data['telephone2_number'])) {
                $this->data['phone2'] = $this->data['telephone2_intcode'].'-'.$this->data['telephone2_areacode'].'-'.$this->data['telephone2_number'];
            }

            if(!is_empty($this->data['fax_intcode'], $this->data['fax_areacode'], $this->data['fax_number'])) {
                $this->data['fax1'] = $this->data['fax_intcode'].'-'.$this->data['fax_areacode'].'-'.$this->data['fax_number'];
            }

            if(!is_empty($this->data['fax2_intcode'], $this->data['fax2_areacode'], $this->data['fax2_number'])) {
                $this->data['fax2'] = $this->data['fax2_intcode'].'-'.$this->data['fax2_areacode'].'-'.$this->data['fax2_number'];
            }

            $geolocation = $this->data['geoLocation']; //Temp solution

            unset($this->data['telephone_intcode'], $this->data['telephone_areacode'], $this->data['telephone_number']);
            unset($this->data['telephone2_intcode'], $this->data['telephone2_areacode'], $this->data['telephone2_number']);
            unset($this->data['fax_intcode'], $this->data['fax_areacode'], $this->data['fax_number']);
            unset($this->data['fax2_intcode'], $this->data['fax2_areacode'], $this->data['fax2_number'], $this->data['geoLocation']);

            if(isset($this->data['mainEmail']) && !empty($this->data['mainEmail'])) {
                if($core->validate_email($this->data['mainEmail'])) {
                    $this->data['mainEmail'] = $core->sanitize_email($this->data['mainEmail']);
                }
                else {
                    output_xml("<status>false</status><message>{$lang->invalidentityemail}</message>");
                    exit;
                }
            }

            $this->data['website'] = $core->sanitize_URL($this->data['website']);

            $this->data['contractFirstSigDate'] = $this->checkgenerate_date($this->data['contractFirstSigDate']);
            $this->data['contractExpiryDate'] = $this->checkgenerate_date($this->data['contractExpiryDate']);

            $this->data['createdOn'] = $this->data['dateAdded'] = TIME_NOW;
            $this->data['createdBy'] = $core->user['uid'];
            if(!isset($this->data['noQReportReq'])) {
                $this->data['noQReportReq'] = 1; //By default no QR is required
            }
            $coveredcountries = $this->data['coveredcountry'];
            unset($this->data['coveredcountry']);
            $query = $db->insert_query(self::TABLE_NAME, $this->data);
            if($query) {
                $this->data['eid'] = $this->eid = $db->last_id();
                /* Temp Solution */
                if(!empty($geolocation)) {
                    if(strstr($geolocation, ',')) {
                        $geolocation = str_replace(', ', ' ', $geolocation);
                    }
                    $db->query('UPDATE entities SET geoLocation=geomFromText("POINT('.$db->escape_string($geolocation).')") WHERE eid='.$this->eid);
                }
                $this->insert_affiliatedentities($affiliates);
                if(!in_array($this->data['type'], $noncurrentitytypes)) {
                    $this->insert_representatives($representatives);
                }
                if(is_array($segments)) {
                    $this->insert_entitysegments($segments);
                    if($this->data['type'] == 's' && $this->data['approved'] == 1) {
                        $this->send_creationnotification();
                    }
                }
//if($this->data['type'] == 'c') {
                if(!in_array($this->data['type'], $noncurrentitytypes)) {
                    if(IN_AREA == 'user') {
                        $this->insert_assignedemployee();
                    }
                    else {
                        $this->insert_assignedemployee($employees);
                    }
                }
//}
                if(is_array($coveredcountries)) {
                    foreach($coveredcountries as $coveredcountry) {
                        $coveredcountry['eid'] = $this->eid;
                        $countract_countryobj = new EntitiesContractCountries();
                        /* set the object by the core input data and save the same object */
                        $countract_countryobj->set($coveredcountry)->save();
                    }
                }
                $lang->entitycreated = $lang->sprint($lang->entitycreated, htmlspecialchars($this->data['companyName']));
                output_xml("<status>true</status><message>{$lang->entitycreated}</message>");
                $this->status = true;
            }
            else {
                output_xml("<status>false</status><message>{$lang->errorcreatingentity}</message>");
                $this->status = false;
            }
        }
        else {
            if($this->entity_type($this->data['companyName']) == 'c') {
                $existing_eid = $this->existing_eid($this->data['companyName']);
                $exists = $db->fetch_field($db->query("SELECT COUNT(*) AS counter FROM ".Tprefix."assignedemployees WHERE uid='".$db->escape_string($core->user['uid'])."' AND eid='{$existing_eid}'"), 'counter');
                if($exists == 0) {
                    $query = $db->insert_query('assignedemployees', array('eid' => $existing_eid, 'uid' => $core->user['uid']));
                    if($query) {
                        output_xml("<status>true</status><message>{$lang->joinedsuccessfully}</message>");
                        $this->status = true;
                    }
                    else {
                        output_xml("<status>false</status><message>{$lang->errorcreatingentity}</message>");
                        $this->status = false;
                    }
                }
                else {
                    output_xml("<status>false</status><message>{$lang->entityalreadyexists}</message>");
                    $this->status = false;
                }
            }
            else {
                output_xml("<status>false</status><message>{$lang->entityalreadyexists}</message>");
                $this->status = false;
            }
        }
    }

    public function send_creationnotification() {
        global $core, $lang;

        $lang->load('messages');
        $segments = $this->get_segments();
        if(is_array($segments)) {
            foreach($segments as $segment) {
                $segment_coordobjs = $segment->get_coordinators();
                if(is_array($segment_coordobjs)) {
                    foreach($segment_coordobjs as $coord) {
                        $email_data['to'][] = $coord->get_coordinator()->email;
                    }
                }
            }
        }
        $email_data['to'][] = 'sourcing@orkila.com';

        $email_data['to'] = array_unique($email_data['to']);
        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();
        $mailer->set_type();
        $mailer->set_from(array('name' => 'OCOS Mailer', 'email' => $core->settings['maileremail']));
        $mailer->set_subject($lang->addsuppliernotification_subject);
        $mailer->set_message($lang->sprint($lang->addsuppliernotification_message, $this->companyName, $this->get_link()));
        $mailer->set_to($email_data['to']);
        $mailer->send();
    }

    protected function modify() {
        global $core, $db, $lang;

        if(array_key_exists('eid', $this->data)) {
            $this->eid = $this->data['eid'];

            if(!empty($this->data['companyName'])) {
                $check_name = $db->query("SELECT eid FROM ".Tprefix."entities WHERE companyName='".$db->escape_string($this->data['companyName'])."'");
                if($db->num_rows($check_name) > 0) {
                    $existing = $db->fetch_array($check_name);
                    if($existing['eid'] != $this->data['eid']) {
                        output_xml("<status>false</status><message>{$lang->entityalreadyexists}</message>");
                        $this->status = false;
                        exit;
                    }
                }

                if(empty($this->data['affid'])) {
                    output_xml("<status>false</status><message>{$lang->specifyanaffiliate}</message>");
                    $this->status = false;
                    exit;
                }

                if(!is_empty($this->data['telephone_intcode'], $this->data['telephone_areacode'], $this->data['telephone_number'])) {
                    $this->data['phone1'] = $this->data['telephone_intcode'].'-'.$this->data['telephone_areacode'].'-'.$this->data['telephone_number'];
                }

                if(!is_empty($this->data['telephone2_intcode'], $this->data['telephone2_areacode'], $this->data['telephone2_number'])) {
                    $this->data['phone2'] = $this->data['telephone2_intcode'].'-'.$this->data['telephone2_areacode'].'-'.$this->data['telephone2_number'];
                }

                if(!is_empty($this->data['fax_intcode'], $this->data['fax_areacode'], $this->data['fax_number'])) {
                    $this->data['fax1'] = $this->data['fax_intcode'].'-'.$this->data['fax_areacode'].'-'.$this->data['fax_number'];
                }

                if(!is_empty($this->data['fax2_intcode'], $this->data['fax2_areacode'], $this->data['fax2_number'])) {
                    $this->data['fax2'] = $this->data['fax2_intcode'].'-'.$this->data['fax2_areacode'].'-'.$this->data['fax2_number'];
                }

                unset($this->data['telephone_intcode'], $this->data['telephone_areacode'], $this->data['telephone_number']);
                unset($this->data['telephone2_intcode'], $this->data['telephone2_areacode'], $this->data['telephone2_number']);
                unset($this->data['fax_intcode'], $this->data['fax_areacode'], $this->data['fax_number']);
                unset($this->data['fax2_intcode'], $this->data['fax2_areacode'], $this->data['fax2_number']);

                if(isset($this->data['mainEmail']) && !empty($this->data['mainEmail'])) {
                    if($core->validate_email($this->data['mainEmail'])) {
                        $this->data['mainEmail'] = $core->sanitize_email($this->data['mainEmail']);
                    }
                    else {
                        output_xml("<status>false</status><message>{$lang->invalidentityemail}</message>");
                        exit;
                    }
                }

                $representatives = $this->data['representative']; //;$this->workout_representatives();
                unset($this->data['representative'], $this->data['rep_numrows']);

                $affiliates = $this->data['affid'];
                unset($this->data['affid']);

                if(isset($this->data['users']) && !empty($this->data['users'])) {
                    $employees = $this->data['users'];
                    unset($this->data['users'], $this->data['users_numrows']);
                }
                else {
                    output_xml("<status>false</status><message>{$lang->specifyauser} Select a user</message>");
                    $this->status = false;
                    exit;
                }

                if(isset($this->data['psid']) && !empty($this->data['psid'])) {
                    $segments = $this->data['psid'];
                    unset($this->data['psid']);
                }
                else {
                    output_xml("<status>false</status><message>{$lang->specifyasegment} Select a segment</message>");
                    $this->status = false;
                    exit;
                }

                if(isset($this->data['logo']) && !empty($this->data['logo'])) {
                    $old_logo = $db->fetch_field($db->query("SELECT logo FROM ".Tprefix."entities WHERE eid=".$this->eid), 'logo');
                    if(!empty($old_logo)) {
                        if($old_logo != $this->data['logo']) {
                            unlink(ROOT.'/uploads/entitieslogos/'.$old_logo);
                        }
                    }
                }

                if(isset($this->data['contractFirstSigDate'])) {
                    $this->data['contractFirstSigDate'] = $this->checkgenerate_date($this->data['contractFirstSigDate']);
                }
                if(isset($this->data['contractExpiryDate'])) {
                    $this->data['contractExpiryDate'] = $this->checkgenerate_date($this->data['contractExpiryDate']);
                }

                /* Set value for unchecked checkboxes - START  */
                $checkboxes_tocheck = array('noQReportSend', 'noQReportReq');
                foreach($checkboxes_tocheck as $checkid) {
                    if(!isset($this->data[$checkid])) {
                        $this->data[$checkid] = 0;
                    }
                }
                /* Set value for unchecked checkboxes - END */
                $coveredcountries = $this->data['coveredcountry'];
                unset($this->data['coveredcountry']);

                $this->data['modifiedOn'] = TIME_NOW;
                $this->data['modifiedBy'] = $core->user['uid'];
                $query = $db->update_query('entities', $this->data, "eid='".$this->eid."'");

                if($query) {
                    $db->delete_query('affiliatedentities', "eid='".$this->eid."'");
                    $this->insert_affiliatedentities($affiliates);
                    $db->delete_query('entitiesrepresentatives', "eid='".$this->eid."'");
                    $this->insert_representatives($representatives);
                    if(is_array($segments)) {
                        $db->delete_query('entitiessegments', "eid='".$this->eid."'");
                        $this->insert_entitysegments($segments);
                    }
                    if(IN_AREA == 'admin') {
                        if(is_array($coveredcountries)) {
                            foreach($coveredcountries as $coveredcountry) {
                                if(empty($coveredcountry['coid'])) {
                                    continue;
                                }
                                $coveredcountries_keys[] = $coveredcountry['coid'];
                                $coveredcountry['eid'] = $this->eid;
                                $contract_countryobj = new EntitiesContractCountries();
                                /* set the object by the core input data and save the same object */
                                $contract_countryobj->set($coveredcountry)->save();
                            }

                            /* Delete removed entries */
                            if(is_array($coveredcountries_keys)) {
                                $coveredcountries_keys = array_map('intval', $coveredcountries_keys);
                                $covctryodelete = EntitiesContractCountries::get_contractcountries('eid='.$this->eid.' AND coid NOT IN ('.implode(', ', $coveredcountries_keys).')', array('returnarray' => true));

                                if(is_array($covctryodelete)) {
                                    foreach($covctryodelete as $covctry) {
                                        if(!is_object($covctry)) {
                                            continue;
                                        }
                                        $covctry->delete();
                                    }
                                }
                                unset($coveredcountries_keys, $covctryodelete);
                            }
                        }

                        /* $query = $db->query("SELECT uid FROM ".Tprefix."assignedemployees WHERE isValidator='1' AND eid='".$this->eid."'");
                          $validators = array();
                          while($validator = $db->fetch_assoc($query)) {
                          $validators[] = $validator['uid'];
                          }
                         */
                        $db->delete_query('assignedemployees', "eid='".$this->eid."'");
                        $db->delete_query('suppliersaudits', "eid='".$this->eid."'");
                        $this->insert_assignedemployee($employees);
                    }

                    $lang->entitymodified = $lang->sprint($lang->entitymodified, htmlspecialchars($this->data['companyName']));
                    output_xml("<status>true</status><message>{$lang->entitymodified}</message>");
                    $this->status = true;
                }
            }
            else {
                output_xml("<status>false</status><message>{$lang->specifyanaffiliate}</message>");
                $this->status = false;
                exit;
            }
        }
    }

    public function get_products() {
        global $db;

        $query = $db->query('SELECT pid FROM '.Tprefix.'products WHERE spid = "'.$db->escape_string($this->data['eid']).'"');
        if($db->num_rows($query) > 0) {
            while($poduct = $db->fetch_assoc($query)) {
                $poducts[$poduct['pid']] = new Products($poduct['pid']);
            }
            return $poducts;
        }
        return false;
    }

    public function get_locations() {
        return EntityLocations::get_data(array('eid' => $this->data[self::PRIMARY_KEY]), array('simple' => false, 'returnarray' => true));
    }

    private function create_representative() {
        global $core, $db, $lang;

        if(!isset($this->data['repName'], $this->data['repEmail']) || (empty($this->data['repName']) || empty($this->data['repEmail']))) {
            output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
            exit;
        }

        $count = $db->fetch_field($db->query("SELECT COUNT(*) AS existing FROM ".Tprefix."representatives WHERE name='".$db->escape_string($this->data['repName'])."' AND email ='".$db->escape_string($this->data['repEmail'])."' "), "existing");
        if($count > 0) {
            output_xml("<status>false</status><message>{$lang->representativeexists}</message>");
            exit;
        }

        if($core->validate_email($this->data['repEmail'])) {
            $core->input['repEmail'] = $core->sanitize_email($this->data['repEmail']);
        }
        else {
            output_xml("<status>false</status><message>{$lang->invalidentityemail}</message>");
            exit;
        }

        if(isset($this->data['repTelephone']) && !empty($this->data['repTelephone'])) {
            if(!is_empty($this->data['repTelephone']['intcode'], $this->data['repTelephone']['areacode'], $this->data['repTelephone']['number'])) {
                $this->data['repTelephone'] = implode('-', $this->data['repTelephone']);
            }
            else {
                unset($this->data['repTelephone']);
            }
        }

        $query = $db->insert_query('representatives', array('name' => ucwords(strtolower($this->data['repName'])), 'email' => $this->data['repEmail'], 'phone' => $this->data['repTelephone'], 'isSupportive' => $this->data['isSupportive']));
        if($query) {
            $rpid = $db->last_id();
            if(isset($this->data['repcid'])) {
                $db->insert_query('entitiesrepresentatives', array('eid' => $this->data['repcid'], 'rpid' => $rpid));
            }

            if(isset($this->data['repspid'])) {
                $db->insert_query('entitiesrepresentatives', array('eid' => $this->data['repspid'], 'rpid' => $rpid));
            }

            if(isset($this->data['repPosition']) && !empty($this->data['repPosition'])) {
                $db->insert_query(RepresentativePositions::TABLE_NAME, array('rpid' => $rpid, 'posid' => $this->data['repPosition']));
            }
            $this->status = true;
        }
        else {
            $this->status = false;
        }
    }

    private function workout_representatives() {
        global $core, $lang;

        for($i = 1; $i <= $this->data ['rep_numrows']; $i++) {
            if(empty($this->data['representative_'.$i])) {
                unset($this->data['representative_'.$i]);
                continue;
            }
            else {
                $found_once = true;
                $representatives[$i]['rpid'] = $this->data['representative_'.$i];
            }
            unset($this->data['representative_'.$i]);
        }

        unset($this->data['rep_numrows']);

        if($found_once !== true) {
            output_xml("<status>false</status><message>{$lang->specifyrepresentative}</message>");
            $this->status = false;
            exit;
        }
        return $representatives;
    }

    private function insert_representatives(array $representatives) {
        global $db;

        foreach($representatives as $key => $val) {
            if(empty($val['rpid'])) {
                continue;
            }
            $representative = array(
                    'rpid' => $val['rpid'],
                    'eid' => $this->eid
            );
            $db->insert_query('entitiesrepresentatives', $representative);
        }
    }

    private function insert_affiliatedentities(array $affiliates) {
        global $db;

        foreach($affiliates as $key => $val) {

            $affentity = array(
                    'affid' => $val,
                    'eid' => $this->eid,
            );
            $db->insert_query('affiliatedentities', $affentity);
        }
    }

    private function insert_assignedemployee($employees = '', array $validators = array()) {
        global $db, $core;

        if(isset($employees) && !empty($employees)) {
            foreach($employees as $key => $val) {
                if(empty($val['uid']) || !isset($val['uid'])) {
                    continue;
                }
                if(isset($val['isValidator']) && $val['isValidator'] == 'on') {
                    $db->insert_query('suppliersaudits', array('eid' => $this->eid, 'uid' => $val['uid']));
                }
                if(!is_array($val['affiliates'])) {
                    continue;
                }
                foreach($val['affiliates'] as $value) {
                    $db->insert_query('assignedemployees', array('eid' => $this->eid, 'uid' => $val['uid'], 'affid' => $value));
                }
            }
        }
        else {
            $main_affiliate = $db->fetch_field($db->query("SELECT affid FROM ".Tprefix."affiliatedemployees WHERE isMain='1' AND uid='".$core->user['uid']."'"), 'affid');
            $db->insert_query('assignedemployees', array('eid' => $this->eid, 'uid' => $core->user['uid'], 'affid' => $main_affiliate));
        }
        /* if(empty($employees)) {
          $db->insert_query('assignedemployees', array('eid'=> $this->eid, 'uid'=> $core->user['uid']));
          }
          else
          {
          if(is_array($employees)) {
          foreach($employees as $key => $val) {
          $assignemployee = array(
          'uid'	  => $val,
          'eid'	  => $this->eid,
          );
          if(in_array($val, $validators)) {
          $assignemployee['isValidator'] = 1;
          }
          $db->insert_query('assignedemployees', $assignemployee);
          }
          }
          } */
    }

    private function insert_entitysegments(array $segments) {
        global $db;
        if(is_array($segments)) {
            foreach($segments as $key => $val) {
                $db->insert_query('entitiessegments', array('psid' => $val, 'eid' => $this->eid));
            }
        }
    }

    private function checkgenerate_date($date) {
        if(!empty($date)) {
            $date_details = explode('-', $date);
            if(checkdate($date_details[1], $date_details[0], $date_details[2])) {
                return mktime(0, 0, 0, $date_details[1], $date_details[0], $date_details[2]);
            }
            else {
                output_xml("<status>false</status><message>{$lang->invalidfromdate}</message>");
                exit;
            }
        }
    }

    private function upload_logo() {
        global $core;
        $core->settings['logosdir'] = ROOT.'/uploads/entitieslogos';
        $upload = new Uploader($this->data['fieldname'], $this->data['file'], array('image/jpeg', 'image/gif'), 'putfile', 300000, 0, 1);
        $upload->set_upload_path($core->settings['logosdir']);
        $upload->process_file();
        $upload->resize();

        $this->logofilename = $upload->get_filename();
    }

    public function get_uploaded_logo() {
        return $this->logofilename;
    }

    public function get_eid() {
        return $this->data['eid'];
    }

    public function get_status() {
        return $this->status;
    }

    public function entity_exists($name) {
        global $db;

        if(function_exists('value_exists')) {
            return value_exists('entities', 'companyName', $name);
        }
        else {
            $query = $db->query("SELECT companyName FROM ".Tprefix."entities WHERE companyName='".$db->escape_string($name)."'");
            if($db->num_rows($query) > 0) {
                return true;
            }
            else {
                return false;
            }
        }
    }

    public function get_country() {
        return new Countries($this->data['country']);
    }

    public function __get($name) {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }
        return false;
    }

    public function __isset($name) {
        return isset($this->data[$name]);
    }

    public function get() {
        return $this->data;
    }

    protected function read($id, $simple) {
        global $db;
        if(!empty($id)) {
            $query_select = '*';
            if($simple == true) {
                $query_select = 'eid, companyName, companyName AS name, companyNameAbbr, companyNameShort, logo, country, type,supplierType,isActive';
            }
            return $db->fetch_assoc($db->query("SELECT ".$query_select." FROM ".Tprefix."entities WHERE eid='".$db->escape_string($id)."'"));
        }
        return false;
    }

    protected function existing_eid($name) {
        global $db;
        return $db->fetch_field($db->query("SELECT eid FROM ".Tprefix."entities WHERE companyName='".$db->escape_string($name)."'"), 'eid');
    }

    public function entity_type($name) {
        global $db;
        return $db->fetch_field($db->query("SELECT type FROM ".Tprefix."entities WHERE companyName='".$db->escape_string($name)."'"), 'type');
    }

    public function auto_assignsegment($gpid) {
        global $db;
        /* Get segment of generic product */
        $psid = $db->fetch_field($db->query("SELECT psid FROM ".Tprefix."genericproducts WHERE gpid='".$db->escape_string($gpid)."'"), 'psid');

        if(!value_exists('entitiessegments', 'psid', $psid, 'eid='.$this->data['eid'].'')) {
            $db->insert_query('entitiessegments', array('psid' => $psid, 'eid' => $this->data['eid']));
        }
    }

    public static function get_entity_byname($name) {
        global $db;

        if(!empty($name)) {
            $sql = 'SELECT eid FROM '.Tprefix.'entities WHERE companyName="'.$db->escape_string($name).'"';
            $id = $db->fetch_field($db->query($sql), 'eid');
            if(!empty($id)) {
                return new Entities($id);
            }
        }
        return false;
    }

    public function get_assignedusers(array $affiliates = array()) {
        global $db;

        if(!empty($affiliates)) {
            $query_extrawhere .= ' AND affid IN ('.implode(', ', $affiliates).')';
        }

        $query = $db->query('SELECT *
                        FROM '.Tprefix.'assignedemployees
                        WHERE eid='.$this->data['eid'].' AND uid NOT IN (SELECT uid FROM '.Tprefix.'users WHERE gid=7)'.$query_extrawhere);
        if($db->num_rows($query) > 0) {
            while($assigned = $db->fetch_assoc($query)) {
                $assigns[] = new Users($assigned['uid']);
            }
            return $assigns;
        }
        return false;
    }

    public function has_assignedusers(array $affiliates = array()) {
        global $db;

        if(!empty($affiliates)) {
            $query_extrawhere .= ' AND affid IN ('.implode(', ', $affiliates).')';
        }

        $query = $db->query('SELECT *
                        FROM '.Tprefix.'assignedemployees
                        WHERE eid='.$this->data['eid'].' AND uid NOT IN (SELECT uid FROM '.Tprefix.'users WHERE gid=7)'.$query_extrawhere);
        if($db->num_rows($query) > 0) {
            return true;
        }
        return false;
    }

    public function requires_qr() {
        global $db;
        if(!isset($this->data['noQReportReq'])) {
            $this->data['noQReportReq'] = $db->fetch_field($db->query('SELECT noQReportReq FROM '.Tprefix.'entities WHERE eid='.intval($this->data['eid'])), 'noQReportReq');
        }

        if($this->data['noQReportReq'] == 0) {
            return true;
        }
        return false;
    }

    public function get_meetings() {
        global $db, $core;

        $filters = ' AND idAttr="spid"';
        if($this->data['type'] == 'c') {
            $filters = ' AND idAttr="cid"';
        }

        if($core->usergroup['meetings_canViewAllMeetings'] == 0) {
            $meetings_sharedwith = Meetings::get_meetingsshares_byuser();
            $filters .= ' AND (mtid IN (SELECT mtid FROM '.Tprefix.'meetings WHERE isPublic=1 OR createdBy='.$core->user['uid'].')';
            if(is_array($meetings_sharedwith)) {
                $filters .= ' OR (mtid IN ('.implode(', ', array_keys($meetings_sharedwith)).'))';
            }
            $filters .= ')';
        }

        $query = $db->query("SELECT mtid
							FROM ".Tprefix."meetings_associations
							WHERE id='".$db->escape_string($this->data['eid'])."'".$filters);
        if($db->num_rows($query) > 0) {
            while($meeting = $db->fetch_assoc($query)) {
                $meetings[$meeting['mtid']] = new Meetings($meeting['mtid']);
            }
            return $meetings;
        }
        return false;
    }

    public function get_parent() {
        if(isset($this->data['parent']) && !empty($this->data['parent'])) {
            return new Entities($this->data['parent']);
        }
        return false;
    }

    public function get_brands() {
        global $db;

        $query = $db->query('SELECT ebid FROM '.Tprefix.'entitiesbrands WHERE eid="'.intval($this->data['eid']).'"');
        if($db->num_rows($query) > 0) {
            while($brand = $db->fetch_assoc($query)) {
                $brands[$brand['ebid']] = new EntitiesBrands($brand['ebid']);
            }
            return $brands;
        }
        return false;
    }

    public function get_brandsproducts() {
        $data = new DataAccessLayer(EntBrandsProducts, 'entitiesbrandsproducts', 'ebpid');
        $brands = $this->get_brands();
        if(is_array($brands)) {
            return $data->get_objects(array('ebid' => array_keys($brands)), array('returnarray' => true));
        }
        return false;
    }

    public function get_segments() {
        global $db;
        if(empty($this->data['eid'])) {
            $this->data['eid'] = $this->eid;
        }

        $query = $db->query('SELECT psid FROM '.Tprefix.'entitiessegments WHERE eid='.intval($this->data['eid']));
        if($db->num_rows($query) > 0) {
            while($segment = $db->fetch_assoc($query)) {
                $segments[$segment['psid']] = new ProductsSegments($segment['psid']);
            }
            return $segments;
        }
        return false;
    }

    public function get_contractedcountires() { /* for this supplier */
        return EntitiesContractCountries::get_contractcountries('eid='.intval($this->data['eid']));
    }

    public function get_displayname() {
        return $this->data[self::DISPLAY_NAME];
    }

    public function get_link() {
        global $core;
        return $core->settings['rootdir'].'/index.php?module=profiles/entityprofile&amp;eid='.$this->data['eid'];
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        if(!empty($this->data['companyNameAbbr'])) {
            $this->data['companyName'] .= ' ('.$this->data['companyNameAbbr'].')';
        }

        if(is_array($attributes_param)) {
            foreach($attributes_param as $attr => $val) {
                $attributes .= $attr.'="'.$val.'"';
            }
        }
        return '<a href="'.$this->get_link().'" '.$attributes.'>'.$this->data['companyName'].'</a>';
    }

    public function get_shortdisplayname() {
        if(!empty($this->companyNameAbbr)) {
            return $this->companyNameAbbr;
        }
        elseif(!empty($this->companyNameShort)) {
            return $this->companyNameShort;
        }
        else {
            return $this->get_displayname();
        }
    }

    public function mergeanddelete($oldid, $newid) {
        global $db;
        global $log;
//        $checkattrs = array(
//                'affiliatedentities' => 'affid',
//                'entitiessegments' => 'psid',
//                'entitiesrepresentatives' => 'rpid',
//                'assignedemployees' => 'uid',
//        );
        $type = $this->type;
        if($type == 'c') {
            $entity_columns = array('eid', 'cid');
        }
        elseif($type == 's') {
            $entity_columns = array('eid', 'spid', 'vendorEid');
        }
        $old_entity_details = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."entities WHERE eid={$oldid}"));
        $new_entity_details = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."entities WHERE eid={$newid}"));

        foreach($entity_columns as $column) {
            $tables[$column] = $db->get_tables_havingcolumn($column);
        }
        if(is_array($tables)) {
            foreach($tables as $key => $columntables) {
                if($columntables == 'entities') {
                    continue;
                }
                if(is_array($columntables)) {
                    $entity_tables[$key] = array_fill_keys(array_values($columntables), $key);
                }
            }
        }
        if(!empty($newid)) {
            foreach($new_entity_details as $nkey => $nval) {
                if(empty($new_entity_details[$nkey]) && !empty($old_entity_details[$nkey])) {
                    $update_details[$nkey] = $old_entity_details[$nkey];
                }
            }

            if(is_array($update_details)) {
                $update_details_query = $db->update_query('entities', $update_details, "eid='{$newid}'");
            }

            foreach($entity_tables as $tables) {
                if(is_array($tables)) {
                    foreach($tables as $table => $attr) {
                        $classname = get_classname_bytable($table);
                        if($classname != false && !empty($classname)) {
                            $unique_attrs = $classname::UNIQUE_ATTRS;
                            if(!empty($unique_attrs) && !is_null($unique_attrs)) {
                                $unique_attrib_arr = explode(',', $unique_attrs);
                                if(!empty($unique_attrib_arr) && is_array($unique_attrib_arr)) {
                                    foreach($unique_attrib_arr as $attrvalue) {
                                        if($attrvalue == $attr) {
                                            continue;
                                        }
                                        $unique_attribs_arr[] = $attrvalue;
                                    }
                                    if(is_array($unique_attribs_arr) && !empty($unique_attribs_arr)) {
                                        $unique_attrs = implode(',', $unique_attribs_arr);
                                        $sql_select = "SELECT ".$unique_attrs." FROM ".Tprefix."".$table." WHERE ".$attr."=".$newid."";

                                        foreach($unique_attribs_arr as $attrs) {
                                            $sql_where_in .= " AND ".$attrs." IN (SELECT ".$attrs." FROM ".Tprefix."".$table." WHERE ".$attr."=".$oldid.")";
                                            $sql_where_notin .= " AND ".$attrs." NOT IN (SELECT ".$attrs." FROM ".Tprefix."".$table." WHERE ".$attr."=".$oldid.")";
                                        }
                                    }
                                }
                                $query_intersect = $sql_select.$sql_where_in;
                                $query_difference = $sql_select.$sql_where_notin;
                                $rows_intersect = $db->query($query_intersect);
                                if($db->num_rows($rows_intersect) > 0) {
                                    while($rowsdata_int = $db->fetch_assoc($rows_intersect)) {
                                        foreach($rowsdata_int as $col => $vals) {
                                            $sql_update_in.=" AND ".$col." =".$vals."";
                                        }
                                        $update_query = $db->delete_query($table, "{$attr} = {$oldid} {$sql_update_in}");
                                        $results .= $table.': '.$db->affected_rows().'<br />';
                                    }
                                }
                                $rows_diff = $db->query($query_difference);
                                if($db->num_rows($rows_diff) > 0) {
                                    while($rowsdata_diff = $db->fetch_assoc($rows_diff)) {
                                        foreach($rowsdata_diff as $col => $vals) {
                                            $sql_update_notin.=" AND ".$col." =".$vals."";
                                        }
                                        $update_query = $db->update_query($table, array($attr => $newid), "{$attr} = {$oldid} {$sql_update_notin}");
                                        $results .= $table.': '.$db->affected_rows().'<br />';
                                    }
                                }
                            }
                            else {
                                if($table != 'entities') {
                                    $db->update_query($table, array($attr => $newid), $attr.'='.$oldid);
                                    $results .= $table.': '.$db->affected_rows().'<br />';
                                }
                            }
                            unset($unique_attribs_arr, $sql_where_in, $sql_where_notin, $query_intersect, $query_difference, $query_difference, $sql_update_in, $sql_update_notin);
                        }
                        else {
                            if($table != 'entities') {

                                $db->update_query($table, array($attr => $newid), $attr.'='.$oldid);
                                $results .= $table.': '.$db->affected_rows().'<br />';
                            }
                        }
                    }
                }
            }
        }
        $query = $db->delete_query('entities', "eid = '{$oldid}'");
        if($query) {
            $log->record($oldid, $newid);
//            output_xml("<status>true</status><message>{$lang->successdeletemerge}<![ CDATA[<br / >{$results}]]></message>");

            return true;
        }
        else {
            return false;
        }
    }

    public function get_type() {
        if($this->type == 's') {
            return 'Supplier';
        }
        if($this->type == 'c') {
            return 'Customer';
        }
        if($this->type == 'pc') {
            return 'Producer Customer';
        }
        if($this->type == 'ps') {
            return 'Producer Supplier';
        }
        if($this->type == 't') {
            return 'Trader';
        }
        if($this->type == 'p') {
            return 'Producer';
        }
        if($this->type == 'cs') {
            return 'Competitor supplier';
        }
        return '-';
    }

    public function get_suptype() {
        if($this->supplierType == 's') {
            return 'Supplier';
        }
        if($this->supplierType == 'c') {
            return 'Customer';
        }
        if($this->supplierType == 'pc') {
            return 'Producer Customer';
        }
        if($this->supplierType == 'ps') {
            return 'Producer Supplier';
        }
        if($this->supplierType == 't') {
            return 'Trader';
        }
        if($this->supplierType == 'p') {
            return 'Producer';
        }
        if($this->supplierType == 'cs') {
            return 'Competitor supplier';
        }
        return '-';
    }

    public function get_principalsuppliegroups($returntype = 'object') {
        foreach(self::$groupsup_names as $groupid => $companyname) {
            $entities = Entities::get_data(array('companyName like "%'.$companyname.'%"'), array('returnarray' => true, 'operators' => array('CUSTOMSQLSECURE')));
            if(!is_array($entities)) {
                continue;
            }
            if($returntype == 'object') {
                $groupsuppliers[$groupid] = $entities;
            }
            elseif($returntype == 'id') {
                foreach($entities as $entity) {
                    $groupsuppliers[$entity->data[eid]] = $groupid;
                }
            }
        }
        if(!is_array($groupsuppliers)) {
            return false;
        }
        return $groupsuppliers;
    }

    public function get_suppliergroupname($number) {
        if(array_key_exists($number, self::$groupsup_names)) {
            return self::$groupsup_names[$number];
        }
        return false;
    }

    public function get_supgrouparray() {
        return self::$groupsup_names;
    }

    public function get_representatives() {
        $entitiesreps = EntitiesRepresentatives::get_data(array('eid' => $this->data['eid']), array('returnarray' => true));
        if(is_array($entitiesreps) && !empty($entitiesreps)) {
            $reps = [];
            foreach($entitiesreps as $entrep) {
                $reps = array_filter(array_merger($reps, $entrep->get_representative()));
            }
            return $reps;
        }
        return null;
    }

    public function get_representatives_ids() {
        global $db;
        $query = 'SELECT rpid FROM entitiesrepresentatives WHERE eid ='.$this->data['eid'];
        $entrepquery = $db->query($query);
        if($db->num_rows($entrepquery) > 0) {
            $reps = array();
            while($news = $db->fetch_assoc($entrepquery)) {
                if($news['rpid'] == 0 || $news['rpid'] == 1) {
                    continue;
                }
                $reps[] = $news['rpid'];
            }
            return $reps;
        }
        return null;
    }

    public function get_segment_names() {
        global $db;
        if(empty($this->data['eid'])) {
            $this->data['eid'] = $this->eid;
        }

        $query = $db->query('SELECT es.psid,seg.title FROM '.Tprefix.'entitiessegments AS es LEFT JOIN productsegments as seg ON (es.psid=seg.psid)WHERE eid='.intval($this->data['eid']));
        if($db->num_rows($query) > 0) {
            while($segment = $db->fetch_assoc($query)) {
                $segments[$segment['psid']] = $segment['title'];
            }
            return $segments;
        }
        return false;
    }

    public function is_supplier() {
        if($this->type == 's' || $this->type == 'cs') {
            return true;
        }
        return false;
    }

    public function get_affiliate() {
        return new Affiliates($this->data['affid']);
    }

    public function is_auditor($uid) {
        $assignedemps = AssignedEmployees::get_data(array('uid' => $uid, 'eid' => $this->data['eid'], 'isValidator' => true), array('returnarray' => true));
        if(is_array($assignedemps)) {
            return true;
        }
        return false;
    }

}
?>