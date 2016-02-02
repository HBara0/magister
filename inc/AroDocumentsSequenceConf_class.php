<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroDocumentsSequenceConf.php
 * Created:        @tony.assaad    Feb 10, 2015 | 4:00:23 PM
 * Last Update:    @tony.assaad    Feb 10, 2015 | 4:00:23 PM
 */

/**
 * Description of AroDocumentsSequenceConf
 *
 * @author tony.assaad
 */
class AroDocumentsSequenceConf extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'adsid';
    const TABLE_NAME = 'aro_documentsequences';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'adsid,affid,ptid,effectiveFrom,effectiveTo';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'affid,coid,ptid,effectiveFrom,effectiveTo';

    protected function update(array $data) {
        global $db, $core, $log;
        if(!$this->validate_requiredfields($data)) {
            $documentsequence_array = array('affid' => $data['affid'],
                    'coid' => $data['coid'],
                    'effectiveFrom' => $data['effectiveFrom'],
                    'effectiveTo' => $data['effectiveTo'],
                    'prefix' => $data['prefix'],
                    'incrementBy' => $data['incrementBy'],
                    'nextNumber' => $data['nextNumber'],
                    'suffix' => $data['suffix'],
                    'modifiedBy' => $core->user['uid'],
                    'ptid' => $data['ptid'],
                    'modifiedOn' => TIME_NOW,
            );
            $query = $db->update_query(self::TABLE_NAME, $documentsequence_array, ''.self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
            if($this->is_policyused($this)) {
                $this->errorcode = 4;
                return $this;
            }
            if($query) {
                $this->data[self::PRIMARY_KEY] = $db->last_id();
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
                $this->errorcode = 0;
            }
        }
        return $this;
    }

    protected function create(array $data) {
        global $db, $core, $log;
        if(!$this->validate_requiredfields($data)) {
            $documentsequence_array = array('affid' => $data['affid'],
                    'coid' => $data['coid'],
                    'effectiveFrom' => $data['effectiveFrom'],
                    'effectiveTo' => $data['effectiveTo'],
                    'prefix' => $data['prefix'],
                    'incrementBy' => $data['incrementBy'],
                    'nextNumber' => $data['nextNumber'],
                    'suffix' => $data['suffix'],
                    'createdBy' => $core->user['uid'],
                    'ptid' => $data['ptid'],
                    'createdOn' => TIME_NOW,
            );
            $query = $db->insert_query(self::TABLE_NAME, $documentsequence_array);
            if($query) {
                $this->data[self::PRIMARY_KEY] = $db->last_id();
                $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
                $this->errorcode = 0;
            }
        }
        return $this;
    }

    public function get_intersecting_sequenceconf($confdata = array()) {
        if(empty($confdata)) {
            $confdata = $this->data;
        }
        if(!$this->validate_requiredfields($confdata)) {
            if(!isset($confdata['coid']) || empty($confdata['coid'])) {
                $confdata['coid'] = 0;
            }
            $where = "affid=".$confdata['affid']." AND ptid=".$confdata['ptid']." AND coid=".$confdata['coid']." AND ((effectiveFrom BETWEEN ".$confdata['effectiveFrom']." AND ".$confdata['effectiveTo']." ) OR (effectiveTo BETWEEN ".$confdata['effectiveFrom']." AND ".$confdata['effectiveTo'].")"
                    ."OR (effectiveFrom < ".$confdata['effectiveFrom']." AND effectiveTo > ".$confdata['effectiveTo']."))";
            $docsequenceconf = self::get_data($where);
            if($docsequenceconf->adsid == $confdata['adsid']) {
                return;
            }
            return $docsequenceconf;
        }
        return;
    }

    protected function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('affid', 'ptid', 'effectiveFrom', 'effectiveTo');
            foreach($required_fields as $field) {
                if(empty($data[$field])) {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

    public function get_nextaro_identification() {
        return $this->data['nextNumber'] + $this->data['incrementBy'];
    }

    public function is_policyused($policyobj) {
        $aro_betweenpolicyeffect = AroRequests::get_data('affid = '.$policyobj->affid.' AND orderType='.$policyobj->ptid.' AND isFinalized = 1 AND createdOn BETWEEN '.$policyobj->effectiveFrom.' AND '.$policyobj->effectiveTo, array('returnarray' => true));
        if(is_array($aro_betweenpolicyeffect)) {
            foreach($aro_betweenpolicyeffect as $aro) {
                if($aro->getif_approvedonce($aro->aorid)) {
                    return true;
                }
            }
        }
        return false;
    }

}