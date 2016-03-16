<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: HrJobOpportunities.php
 * Created:        @rasha.aboushakra    Nov 3, 2015 | 12:42:56 PM
 * Last Update:    @rasha.aboushakra    Nov 3, 2015 | 12:42:56 PM
 */

/**
 * Description of HrJobOpportunities
 *
 * @author rasha.aboushakra
 */
class HrJobOpportunities extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'joid';
    const TABLE_NAME = 'hr_jobopprtunities';
    const DISPLAY_NAME = 'reference';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const REQUIRED_ATTRS = 'affid,employmentType,title,workLocation,responsibilities,shortDesc,unpublishOn,publishOn';
    const UNIQUE_ATTRS = 'affid,alias,reference';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $log, $core, $errorhandler, $lang;
        if(!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        $dates = array('approxJoinDate', 'publishOn', 'unpublishOn');
        foreach($dates as $date) {
            if(isset($data[$date]) && !empty($data[$date]) && !is_numeric($data[$date])) {
                $data[$date] = strtotime($data[$date]);
            }
        }
        $data['createdOn'] = TIME_NOW;
        $data['createdBy'] = $core->user['uid'];
        $data['alias'] = strtolower($data['reference'].'-'.generate_alias($data['title']).'-'.$data['affid']);
        /* ---SANITIZE INPUTS---START */
        $sanitize_fields = array('reference', 'title', /* 'shortDesc', 'responsibilities', 'minQualifications', 'prefQualifications' */);
        foreach($sanitize_fields as $val) {
            $data[$val] = $core->sanitize_inputs($data[$val], array('removetags' => true));
        }
        /* ---SANITIZE INPUTS---END */


        /* Verify if user can HR this affiliate Server side --START */
        if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
            if(!in_array($data['affid'], $core->user['hraffids'])) {
                return $this;
            }
        }
        if($data['unpublishOn'] < $data['publishOn']) {
            $this->errorcode = 5;
            return $this;
        }
        /* Verify if user can HR this affiliate Server side --END */
//
//        if(value_exists(self::TABLE_NAME, 'affid', $data['affid'], '(('.TIME_NOW.' BETWEEN '.$data['publishOn'].' AND '.$data['unpublishOn'].') AND title="'.$data['title'].'" )')) {
//            $this->errorcode = 4;
//            return $this;
//        }

        $requiredlangs = $data['requiredlang'];
        unset($data['requiredlang']);
        $requiredcareerlevel = $data['careerLevel'];
        unset($data['careerLevel']);
        $requirededucationlevel = $data['educationLevel'];
        unset($data['educationLevel']);
        if(is_array($data)) {
            $query = $db->insert_query(self::TABLE_NAME, $data);
            if($query) {
                $this->data[self::PRIMARY_KEY] = $db->last_id();
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
                if(!empty($requiredlangs) && is_array($requiredlangs)) {
                    $langdata[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
                    $langdata['type'] = 'requirement';
                    foreach($requiredlangs as $langid) {
                        $hrjoblang = new HrJobOpportunitiesLanguage();
                        $langdata['language'] = $langid;
                        $hrjoblang->set($langdata);
                        $hrjoblang->save();
                    }
                }
                if(!empty($requiredcareerlevel) && is_array($requiredcareerlevel)) {
                    $leveldata[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
                    $leveldata['type'] = 'requirement';
                    foreach($requiredcareerlevel as $levelid) {
                        if($levelid == 0) {
                            continue;
                        }
                        $hrjobcareerlevel = new HrJobOpportunitiesSelectedCareerLevel();
                        $leveldata['joclid'] = $levelid;
                        $hrjobcareerlevel->set($leveldata);
                        $hrjobcareerlevel->save();
                    }
                }
                if(!empty($requirededucationlevel) && is_array($requirededucationlevel)) {
                    $leveldata[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
                    $leveldata['type'] = 'requirement';
                    foreach($requirededucationlevel as $levelid) {
                        if($levelid == 0) {
                            continue;
                        }
                        $hrjobeducationlevel = new HrJobOpportunitiesSelectedEducationLevel();
                        $leveldata['joelid'] = $levelid;
                        $hrjobeducationlevel->set($leveldata);
                        $hrjobeducationlevel->save();
                    }
                }
            }
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $log, $core, $errorhandler, $lang;
        if(!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        $dates = array('approxJoinDate', 'publishOn', 'unpublishOn');
        foreach($dates as $date) {
            if(isset($data[$date]) && !empty($data[$date]) && !is_numeric($data[$date])) {
                $data[$date] = strtotime($data[$date]);
            }
        }
        $data['modifiedOn'] = TIME_NOW;
        $data['modifiedBy'] = $core->user['uid'];
        $data['alias'] = strtolower($data['reference'].'-'.generate_alias($data['title']).'-'.$data['affid']);

        /* ---SANITIZE INPUTS---START */
        $sanitize_fields = array('reference', 'title', /* 'shortDesc', 'responsibilities', 'minQualifications', 'prefQualifications' */);
        foreach($sanitize_fields as $val) {
            $data[$val] = $core->sanitize_inputs($data[$val], array('removetags' => true));
        }
        /* ---SANITIZE INPUTS---END */


        /* Verify if user can HR this affiliate Server side --START */
        if($core->usergroup['hr_canHrAllAffiliates'] == 0) {
            if(!in_array($data['affid'], $core->user['hraffids'])) {
                return $this;
            }
        }

        if($data['unpublishOn'] < $data['publishOn']) {
            $this->errorcode = 5;
            return $this;
        }
        $requiredlangs = $data['requiredlang'];
        unset($data['requiredlang']);
        $requiredcareerlevel = $data['careerLevel'];
        unset($data['careerLevel']);
        $requirededucationlevel = $data['educationLevel'];
        unset($data['educationLevel']);
        if(is_array($data)) {
            $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
            $query = $db->delete_query(HrJobOpportunitiesLanguage::TABLE_NAME, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
            if(!empty($requiredlangs) && is_array($requiredlangs)) {
                $langdata[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
                $langdata['type'] = 'requirement';
                foreach($requiredlangs as $langid) {
                    $hrjoblang = new HrJobOpportunitiesLanguage();
                    $langdata['language'] = $langid;
                    $hrjoblang->set($langdata);
                    $hrjoblang->save();
                }
            }
            $query1 = $db->delete_query(HrJobOpportunitiesSelectedCareerLevel::TABLE_NAME, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]).' AND type="requirement"');
            if(!empty($requiredcareerlevel) && is_array($requiredcareerlevel)) {
                $leveldata[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
                $leveldata['type'] = 'requirement';
                foreach($requiredcareerlevel as $levelid) {
                    if($levelid == 0) {
                        continue;
                    }
                    $hrjobcareerlevel = new HrJobOpportunitiesSelectedCareerLevel();
                    $leveldata['joclid'] = $levelid;
                    $hrjobcareerlevel->set($leveldata);
                    $hrjobcareerlevel->save();
                }
            }
            $query2 = $db->delete_query(HrJobOpportunitiesSelectedEducationLevel::TABLE_NAME, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]).' AND type="requirement"');
            if(!empty($requirededucationlevel) && is_array($requirededucationlevel)) {
                $leveldata[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
                $leveldata['type'] = 'requirement';
                foreach($requirededucationlevel as $levelid) {
                    if($levelid == 0) {
                        continue;
                    }
                    $hrjobeducationlevel = new HrJobOpportunitiesSelectedEducationLevel();
                    $leveldata['joelid'] = $levelid;
                    $hrjobeducationlevel->set($leveldata);
                    $hrjobeducationlevel->save();
                }
            }
        }
        return $this;
    }

    /**
     * get all applicants that are still active for current job opportunity
     * @return boolean
     */
    public function get_active_applicants() {
        $applicants = HrJobApplicants::get_data(array('joid' => $this->data[self::PRIMARY_KEY], 'isActive' => 1), array('returnarray' => true));
        if(is_array($applicants)) {
            return $applicants;
        }
        return false;
    }

    /**
     * returns affiliate obj of current vacancy
     * @return \Affiliates
     */
    public function get_affiliate() {
        return new Affiliates(intval($this->data[Affiliates::PRIMARY_KEY]));
    }

}