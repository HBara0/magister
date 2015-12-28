<?php
/* -------Definiton-START-------- */

class HrJobOpportunitiesFilter extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'hrjofid';
    const TABLE_NAME = 'hr_jobopportunities_filters';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'joid';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = '';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $table_array = array(
                'joid' => $data['joid'],
                'filterType' => $data['filterType'],
                'minExpYears' => $data['minExpYears'],
                'maxExpYears' => $data['maxExpYears'],
                'minAge' => $data['minAge'],
                'maxAge' => $data['maxAge'],
                'gender' => $data['gender'],
                'residence' => $data['residence'],
        );
        if(!empty($data['educationLevel']) && is_array($data['educationLevel'])) {
            $leveldata['joid'] = $data['joid'];
            $leveldata['type'] = 'filter';
            foreach($data['educationLevel'] as $levelid) {
                if($levelid == 0) {
                    continue;
                }
                $hrjobeducationlevel = new HrJobOpportunitiesSelectedEducationLevel();
                $leveldata['joelid'] = $levelid;
                $hrjobeducationlevel->set($leveldata);
                $hrjobeducationlevel->save();
            }
        }
        if(!empty($data['careerLevel']) && is_array($data['careerLevel'])) {
            $leveldata['joid'] = $update_array['joid'];
            $leveldata['type'] = 'filter';
            foreach($data['careerLevel'] as $levelid) {
                if($levelid == 0) {
                    continue;
                }
                $hrjobcareerlevel = new HrJobOpportunitiesSelectedCareerLevel();
                $leveldata['joclid'] = $levelid;
                $hrjobcareerlevel->set($leveldata);
                $hrjobcareerlevel->save();
            }
        }
        $query = $db->insert_query(self::TABLE_NAME, $table_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(is_array($data)) {
            $update_array['joid'] = $data['joid'];
            $update_array['filterType'] = $data['filterType'];
            $update_array['minExpYears'] = $data['minExpYears'];
            $update_array['maxExpYears'] = $data['maxExpYears'];
            $update_array['minAge'] = $data['minAge'];
            $update_array['maxAge'] = $data['maxAge'];
            $update_array['gender'] = $data['gender'];
            $update_array['residence'] = $data['residence'];
        }
        $query = $db->delete_query(HrJobOpportunitiesSelectedEducationLevel::TABLE_NAME, 'joid='.intval($update_array['joid']).' AND type="filter"');
        if(!empty($data['educationLevel']) && is_array($data['educationLevel'])) {
            $leveldata['joid'] = $update_array['joid'];
            $leveldata['type'] = 'filter';
            foreach($data['educationLevel'] as $levelid) {
                if($levelid == 0) {
                    continue;
                }
                $hrjobeducationlevel = new HrJobOpportunitiesSelectedEducationLevel();
                $leveldata['joelid'] = $levelid;
                $hrjobeducationlevel->set($leveldata);
                $hrjobeducationlevel->save();
            }
        }
        $query1 = $db->delete_query(HrJobOpportunitiesSelectedCareerLevel::TABLE_NAME, 'joid='.intval($update_array['joid']).' AND type="filter"');
        if(!empty($data['careerLevel']) && is_array($data['careerLevel'])) {
            $leveldata['joid'] = $update_array['joid'];
            $leveldata['type'] = 'filter';
            foreach($data['careerLevel'] as $levelid) {
                if($levelid == 0) {
                    continue;
                }
                $hrjobcareerlevel = new HrJobOpportunitiesSelectedCareerLevel();
                $leveldata['joclid'] = $levelid;
                $hrjobcareerlevel->set($leveldata);
                $hrjobcareerlevel->save();
            }
        }
        $db->update_query(self::TABLE_NAME, $update_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
}