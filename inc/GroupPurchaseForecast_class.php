<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: GorupPurchaseForecast_class.php
 * Created:        @rasha.aboushakra    Dec 15, 2014 | 11:46:25 AM
 * Last Update:    @rasha.aboushakra    Dec 15, 2014 | 11:46:25 AM
 */

class GroupPurchaseForecast extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'gpfid';
    const TABLE_NAME = 'grouppurchase_forecast';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'gpfid, affid, year, spid';
    const UNIQUE_ATTRS = 'affid,year,spid';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core, $log;
        if(is_array($data)) {
            if(!$this->validate_requiredfields($data)) {
                $forecastdata['gpfid'] = self::PRIMARY_KEY;
                $forecastdata['affid'] = $data['affid'];
                $forecastdata['year'] = $data['year'];
                $forecastdata['spid'] = $data['spid'];
                $forecastdata['createdOn'] = TIME_NOW;
                $forecastdata['createdBy'] = $core->user['uid'];
                if($data['uid'] != 0) {
                    $forecastdata['createdBy'] = $data['uid'];
                }
                $query = $db->insert_query(self::TABLE_NAME, $forecastdata);
                $this->data[self::PRIMARY_KEY] = $db->last_id();

                if(!$query) {
                    return;
                }
                $log->record('creategpforcast', $this->data[self::PRIMARY_KEY]);
                $forecastlines = $data['forecastline'];
                if(is_array($forecastlines)) {
                    foreach($forecastlines as $forecastline) {
                        $forecastline['gpfid'] = $this->data[self::PRIMARY_KEY];
                        $forecastline['businessMgr'] = $core->user['uid'];
                        if($data['uid'] != 0) {
                            $forecastline['businessMgr'] = $data['uid'];
                        }
                        $gpforecastline = new GroupPurchaseForecastLines($forecastline[GroupPurchaseForecastLines::PRIMARY_KEY]);
                        if(empty($forecastline['psid'])) {
                            $product = new Products($forecastline['pid']);
                            $forecastline['psid'] = $product->get_genericproduct()->get_segment()->psid;
                        }
                        $gpforecastline->set($forecastline);
                        if(isset($forecastline['todelete']) && $forecastline['todelete'] == 1) {
                            $gpforecastline->delete();
                            unset($forecastline);
                        }
                        else {
                            $gpforecastline->save();
                        }
                        $this->errorcode = $gpforecastline->errorcode;
                        switch($this->get_errorcode()) {
                            case 0:
                                continue;
                            case 2:
                                return;
                        }
                    }
                }
            }
        }
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            if(!$this->validate_requiredfields($data)) {
                $forecastdata['affid'] = $data['affid'];
                $forecastdata['year'] = $data['year'];
                $forecastdata['spid'] = $data['spid'];
                $forecastdata['modifiedOn'] = TIME_NOW;
                $forecastdata['modifiedBy'] = $core->user['uid'];
                if($data['uid'] != 0) {
                    $forecastdata['modifiedBy'] = $data['uid'];
                }
                $query = $db->update_query(self::TABLE_NAME, $forecastdata, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
                if(!query) {
                    $this->errorcode = 601;
                    return;
                }
                $log->record('updategpforcast', $this->data[self::PRIMARY_KEY]);
                $forecastlines = $data['forecastline'];
                if(is_array($forecastlines)) {
                    foreach($forecastlines as $forecastline) {
                        $forecastline['gpfid'] = $this->data[self::PRIMARY_KEY];
                        $forecastline['businessMgr'] = $core->user['uid'];
                        if($data['uid'] != 0) {
                            $forecastline['businessMgr'] = $data['uid'];
                        }
                        $gpforecastline = new GroupPurchaseForecastLines($forecastline[GroupPurchaseForecastLines::PRIMARY_KEY]);
                        if(empty($forecastline['psid'])) {
                            $product = new Products($forecastline['pid']);
                            $forecastline['psid'] = $product->get_genericproduct()->get_segment()->psid;
                        }
                        $gpforecastline->set($forecastline);
                        if(isset($forecastline['todelete']) && $forecastline['todelete'] == 1) {
                            $gpforecastline->delete();
                            unset($forecastline);
                        }
                        else {
                            $gpforecastline->save();
                        }
                        $this->errorcode = $gpforecastline->errorcode;
                        switch($this->get_errorcode()) {
                            case 0:
                                continue;
                            case 2:
                                return;
                        }
                    }
                }
            }
        }
    }

    public function get_forecastlines($uid = null) {
        global $core;
        $businessmgr = $core->user['uid'];
        if(isset($uid) && !empty($uid)) {
            $businessmgr = $uid;
        }
        return GroupPurchaseForecastLines::get_data(array('gpfid' => $this->data[self::PRIMARY_KEY], 'businessMgr' => $businessmgr), array('returnarray' => true, 'simple' => false));
    }

    protected function validate_requiredfields(array $data = array()) {
        global $core, $db;
        if(is_array($data)) {
            $required_fields = array('affid', 'year', 'spid');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

    public static function get_grppurchpermissions($groupdata) {
        global $core;

        if($core->usergroup['grouppurchase_canViewAllForecasts'] == 1) {
            $filter_where['affid'] = $groupdata['affiliates'];
            $filter_where['spid'] = $groupdata['suppliers'];
            return $filter_where;
        }
        if(empty($groupdata['suppliers'])) {
            $groupdata['suppliers'] = $core->user['suppliers']['eid'];
        }
        if(is_array($core->user['suppliers']['eid'])) {
            $filter_where['spid'] = array_intersect($groupdata['suppliers'], $core->user['suppliers']['eid']);
        }
        $affiliates_where = '(affid IN ('.implode(',', $core->user['affiliates']).') AND(generalManager='.$core->user['uid'].' OR supervisor='.$core->user['uid'].'))';
        $affiliates_filter = Affiliates::get_affiliates(array('affid' => $affiliates_where), array('returnarray' => true, 'simple' => false, 'operators' => array('affid' => 'CUSTOMSQL')));
        if(is_array($affiliates_filter) && is_array($groupdata['affiliates'])) {
            $intersection = array_intersect(array_keys($affiliates_filter), $groupdata['affiliates']);
        }
        if(!empty($intersection)) {
            $filter_where['affid'] = array_intersect(array_keys($affiliates_filter), $groupdata['affiliates']);
        }
        return $filter_where;
    }

    public function get_supplier() {
        return new Entities($this->spid);
    }

    public function get_affiliate() {
        return new Affiliates($this->affid);
    }

    public function get_displayname() {
        return $this->year.' - '.$this->get_affiliate()->name.' - '.$this->get_supplier()->get_displayname();
    }

}