<?php

class CreateAccount extends Accounts {
    private $user = array();

    public function __construct($data) {
        if(is_array($data)) {
            $this->perform_registration($data);
        }
    }

    private function perform_registration(array $data) {
        global $db, $core, $lang;

        if(empty($data['username'])) {
            output_xml("<status>false</status><message>{$lang->specifyusername}</message>");
            exit;
        }

        if(!parent::username_exists($data['username'])) {
            $db_encrypt_fields = '';

            if(!parent::validate_password_complexity($data['password'])) {
                output_xml("<status>false</status><message>{$lang->pwdpatternnomatch}</message>");
                exit;
            }

            $data['salt'] = parent::create_salt();
            $data['password'] = parent::create_password($data['password'], $data['salt']);
            $data['loginKey'] = parent::create_loginkey();

            if($core->validate_email($data['email'])) {
                $data['email'] = $core->sanitize_email($data['email']);
            }
            else {
                output_xml("<status>false</status><message>{$lang->invalidemail}</message>");
                exit;
            }

            if(empty($data['affiliates']['mainaffid'])) {
                output_xml("<status>false</status><message>{$lang->selectaffiliate}</message>");
                exit;
            }
            /* if(count($data['affid']) == 0) {
              output_xml("<status>false</status><message>{$lang->selectaffiliate}</message>");
              exit;
              } */

            if(empty($data['reportsTo'])) {
                output_xml("<status>false</status><message>{$lang->selectreportto}Select a report to person</message>");
                exit;
            }

            if(count($data['posid']) == 0) {
                output_xml("<status>false</status><message>{$lang->selectposition}Select at least one position</message>");
                exit;
            }

            /* if(count($data['spid']) == 0) {
              output_xml("<status>false</status><message>{$lang->selectsupplier}</message>");
              exit;
              } */

            /* if(count($data['cid']) == 0) {
              output_xml("<status>false</status><message>{$lang->selectcustomer}</message>");
              exit;
              } */

            if(empty($data['firstName']) || empty($data['lastName'])) {
                output_xml("<status>false</status><message>{$lang->fillinfirstlastname}</message>");
                exit;
            }
            $data['firstName'] = ucfirst($data['firstName']);
            $data['lastName'] = ucfirst($data['lastName']);
            /* if(empty($data['city'])) {
              output_xml("<status>false</status><message>{$lang->fillcity}</message>");
              exit;
              }

              if(empty($data['addressLine1'])) {
              output_xml("<status>false</status><message>{$lang->filloneaddress}</message>");
              exit;
              } */

            if(!empty($data['telephone_intcode']) || !empty($data['telephone_areacode']) || !empty($data['telephone_number'])) {
                $data['telephone1'] = $data['telephone_intcode']."-".$data['telephone_areacode']."-".$data['telephone_number'];
            }

            /* if(empty($data['telephone_number']) || empty($data['telephone_areacode']) || empty($data['telephone_intcode'])) {
              output_xml("<status>false</status><message>{$lang->completephone1}</message>");
              exit;
              } */

            if(!empty($data['telephone2_intcode']) || !empty($data['telephone2_areacode']) || !empty($data['telephone2_number'])) {
                $data['telephone2'] = $data['telephone2_intcode'].'-'.$data['telephone2_areacode'].'-'.$data['telephone2_number'];
            }

            unset($data['telephone_intcode'], $data['telephone2_intcode'], $data['telephone_areacode'], $data['telephone2_areacode'], $data['telephone_number'], $data['telephone2_number']);
            $data['dateAdded'] = time();

            /* if(empty($data['poBox'])) {
              output_xml("<status>false</status><message>{$lang->fillpobox}</message>");
              exit;
              } */
            //$main_affiliate = $data['mainaffid'];
            $affiliates = $data['affiliates'];
            $suppliers = $data['supplier'];
            $customers = $data['cid'];
            $positions = $data['posid'];
            $segments = $data['psid'];
            $usergroups['main'] = $data['gid'] = $data['maingid'];
            if(is_array($data['addgids'])) {
                $usergroups['additional'] = $data['addgids'];
                array_push($usergroups['additional'], $data['maingid']);
            }
            else {
                $usergroups['additional'][] = $usergroups['main'];
            }
            unset($data['affiliates'], $data['cid'], $data['supplier'], $data['posid'], $data['supp_numrows'], $data['psid'], $data['maingid'], $data['addgids']);

            $query = $db->insert_query('users', $data);
            $uid = $db->last_id();
            if($query) {
                $this->user['uid'] = $uid;
                /* Set Usergroups - START */
                $usergroups['additional'] = array_unique($usergroups['additional']);
                foreach($usergroups['additional'] as $group) {
                    $newuser_group = array(
                            'uid' => $this->user['uid'],
                            'gid' => $group
                    );
                    if($group == $usergroups['main']) {
                        $newuser_group['isMain'] = 1;
                    }
                    $db->insert_query('users_usergroups', $newuser_group);
                }
                unset($usergroups);
                /* Set Usergroups - END */
                //$main_affiliate_found = false;
                $affiliates['affids'][$affiliates['mainaffid']] = $affiliates['mainaffid'];
                if(is_array($affiliates['affids'])) {
                    foreach($affiliates['affids'] as $key => $val) {
                        $new_affiliatedemployees = array('affid' => $val, 'uid' => $uid);
                        //$isMain = 0;
                        foreach($affiliates as $attr => $values) {
                            if($attr != 'affids') {
                                if(is_array($values)) {
                                    if(in_array($val, $values)) {
                                        $new_affiliatedemployees[$attr] = 1;
                                    }
                                }
                                else {
                                    if($attr == 'mainaffid') {
                                        if($val == $values) {
                                            //$main_affiliate_found = true;
                                            $new_affiliatedemployees['isMain'] = 1;
                                            if(isset($affiliates['affids']['canAudit'][$val])) {
                                                $new_affiliatedemployees['canAudit'] = 1;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        $db->insert_query('affiliatedemployees', $new_affiliatedemployees);
                    }
                }

                $this->set_employeenum($this->generate_employeenumber($affiliates['mainaffid']));
                /* 				if($main_affiliate_found == false) {
                  $main_affiliate_audit = 0;
                  if(isset($affiliates['affids']['canAudit'][$affiliates['mainaffid']])) {
                  $main_affiliate_audit = 1;
                  }

                  $db->insert_query('affiliatedemployees', array('affid' => $affiliates['mainaffid'], 'uid' => $uid, 'isMain' => 1, 'canAudit' => $main_affiliate_audit));
                  } */

                if(isset($suppliers) && !empty($suppliers)) {
                    foreach($suppliers as $key => $val) {
                        if(empty($val['eid']) || !isset($val['eid'])) {
                            continue;
                        }
                        if(isset($val['isValidator']) && $val['isValidator'] == 'on') {
                            $db->insert_query('suppliersaudits', array('eid' => $val['eid'], 'uid' => $uid));
                        }
                        foreach($val['affiliates'] as $value) {
                            $db->insert_query('assignedemployees', array('eid' => $val['eid'], 'uid' => $uid, 'affid' => $value));
                        }
                    }
                }
                if(isset($positions) && !empty($positions)) {
                    foreach($positions as $key => $val) {
                        $db->insert_query('userspositions', array('posid' => $val, 'uid' => $uid));
                    }
                }

                if(isset($segments) && !empty($segments)) {
                    foreach($segments as $key => $val) {
                        $db->insert_query('employeessegments', array('psid' => $val, 'uid' => $uid));
                    }
                }

                if(isset($customers) && !empty($customers)) {
                    foreach($customers as $key => $val) {
                        $db->insert_query('assignedemployees', array('eid' => $val, 'uid' => $uid));
                    }
                }

                /* Set workshift - Start */
                $db->insert_query('employeesshifts', array('uid' => $uid, 'wsid' => $db->fetch_field($db->query("SELECT defaultWorkshift FROM ".Tprefix."affiliates WHERE affid={$affiliates[mainaffid]}"), 'defaultWorkshift'), 'fromDate' => TIME_NOW, 'toDate' => TIME_NOW + (60 * 60 * 24 * 356 * 4)));
                /* Set workshift - END */

                /* Set File Sharing Module permissions - Start */
                $folders_query = $db->query("SELECT ffid, noWritePermissionsLater, noReadPermissionsLater FROM ".Tprefix."filesfolder WHERE noWritePermissionsLater=1 OR noReadPermissionsLater=1");
                if($db->num_rows($folders_query) > 0) {
                    while($folder = $db->fetch_assoc($folders_query)) {
                        $db->insert_query('filesfolder_viewrestriction', array('uid' => $uid, 'ffid' => $folder['ffid'], 'noRead' => $folder['noReadPermissionsLater'], 'noWrite' => $folder['noWritePermissionsLater']));
                    }
                }


                /* Set File Sharing Module permissions - END */

                $lang->useradded = $lang->sprint($lang->useradded, $data['username']);
                output_xml("<status>true</status><message>{$lang->useradded}</message>");
            }
        }
        else {
            output_xml("<status>false</status><message>{$lang->usernameexists}</message>");
        }
    }

    private function set_employeenum($number) {
        global $db;

        if(empty($number)) {
            return false;
        }

        $db->insert_query('userhrinformation', array('employeeNum' => $number, 'uid' => $this->user['uid']));
    }

}
?>