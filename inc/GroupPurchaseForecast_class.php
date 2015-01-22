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
        global $db, $core;
        if(is_array($data)) {
            if(!$this->validate_requiredfields($data)) {
                $forecastdata['gpfid'] = self::PRIMARY_KEY;
                $forecastdata['affid'] = $data['affid'];
                $forecastdata['year'] = $data['year'];
                $forecastdata['spid'] = $data['spid'];
                $forecastdata['createdOn'] = TIME_NOW;
                $forecastdata['createdBy'] = $core->user['uid'];
                $query = $db->insert_query(self::TABLE_NAME, $forecastdata);
                $this->data[self::PRIMARY_KEY] = $db->last_id();

                if(!$query) {
                    return;
                }
                $forecastlines = $data['forecastline'];
                if(is_array($forecastlines)) {
                    foreach($forecastlines as $forecastline) {
                        $forecastline['gpfid'] = $this->data[self::PRIMARY_KEY];
                        $forecastline['businessMgr'] = $core->user['uid'];
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
                $query = $db->update_query(self::TABLE_NAME, $forecastdata, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
                if(!query) {
                    $this->errorcode = 601;
                    return;
                }
                $forecastlines = $data['forecastline'];
                if(is_array($forecastlines)) {
                    foreach($forecastlines as $forecastline) {
                        $forecastline['gpfid'] = $this->data[self::PRIMARY_KEY];
                        $forecastline['businessMgr'] = $core->user['uid'];
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

    public function get_forecastlines() {
        global $core;
        return GroupPurchaseForecastLines::get_data(array('gpfid' => $this->data[self::PRIMARY_KEY], 'businessMgr' => $core->user['uid']), array('returnarray' => true, 'simple' => false));
    }

    private function validate_requiredfields(array $data = array()) {
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

    public static function get_grouppurchaseforecast($groupdata) {
        global $core;
        $core->usergroup['grouppurchasing_canViewAllForecasts'] = 0;
        if(empty($groupdata['suppliers'])) {
            $groupdata['suppliers'] = $core->user['suppliers']['eid'];
        }
        $filter_where['suppliers'] = $groupdata['suppliers'];

        if(empty($groupdata['affiliates'])) {
            $groupdata['affiliates'] = $core->user['affiliates'];
        }

        if($core->usergroup['grouppurchasing_canViewAllForecasts'] == 1) {
            //$filter_where['suppliers'] = $groupdata['suppliers'];
            $filter_where['affid'] = $groupdata['affiliates'];
        }
        else {

            $affiliates = Affiliates::get_affiliates(array('affid' => $groupdata['affiliates']), array('returnarray' => true, 'simple' => false, 'operators' => array('affid' => 'in')));
            foreach($affiliates as $affiliate) {
                if($affiliate->generalManager == $core->user['uid']) {
                    $filter_where['affid'][$affiliate->affid] = $affiliate->affid;
                }
                if($affiliate->supervisor == $core->user['uid']) {
                    $filter_where['affid'][$affiliate->affid] = $affiliate->affid;
                }
                else {
                    //  $filter_where['affid'] = $core->user['affiliates'];
                }


                /* User is an audit of the affiliate => show all affiliate data */
                $affemployess_objs = AffiliatedEmployees::get_data(array('affid' => $affiliate->affid, 'uid' => $core->user['uid'], 'canAudit' => 1), array('returnarray' => true));
                if(is_array($affemployess_objs)) {
                    foreach($affemployess_objs as $affemployess_obj) {
                        if($affemployess_obj->uid == $core->user['uid']) {
                            $filter_where['affid'] = $affemployess_obj->affid;
                        }
                    }
                }
            }
        }
        /* User is coordinator of the segment of a given product => show data from all affiliates of of products related to specific segment */
        $dal_config = array(
                'operators' => array('affid' => 'in', 'spid' => 'in', 'year' => '='),
                'simple' => false,
                'returnarray' => true
        );
        $purchase_forcastobjs = GroupPurchaseForecast::get_data(array('affid' => $filter_where['affid'], 'year' => $groupdata['years'], 'spid' => $filter_where['suppliers']), $dal_config);

        /* security to show data */
        return $purchase_forcastobjs;
    }

}