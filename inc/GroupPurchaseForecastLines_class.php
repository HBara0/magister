<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: GorupPurchaseForecastLines_class.php
 * Created:        @rasha.aboushakra    Dec 15, 2014 | 11:49:45 AM
 * Last Update:    @rasha.aboushakra    Dec 15, 2014 | 11:49:45 AM
 */

class GroupPurchaseForecastLines extends AbstractClass {
    protected $data = array();
    public $errorcode = 0;

    const PRIMARY_KEY = 'gpflid';
    const TABLE_NAME = 'grouppurchase_forecastlines';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'gpflid, gpfid, inputChecksum, pid, psid, saleType, businessMgr';
    const UNIQUE_ATTRS = 'gpfid,businessMgr,pid,psid,saleType';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if(is_array($data)) {
            if(!$this->validate_requiredfields($data)) {
                $fields = array('gpfid', 'inputChecksum', 'pid', 'psid', 'saleType', 'businessMgr', 'month1', 'month2', 'month3', 'month4', 'month5', 'month6', 'month7', 'month8', 'month9', 'month10', 'month11', 'month12');
                foreach($fields as $field) {
                    if(isset($data[$field])) {
                        $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                        $data[$field] = $db->escape_string($data[$field]);
                        $forecastline_data[$field] = $data[$field];
                    }
                }
                $forecastline_data['createdOn'] = TIME_NOW;
                $forecastline_data['createdBy'] = $core->user['uid'];
                $query = $db->insert_query(self::TABLE_NAME, $forecastline_data);
            }
        }
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            if(!$this->validate_requiredfields($data)) {
                $fields = array('pid', 'psid', 'saleType', 'month1', 'month2', 'month3', 'month4', 'month5', 'month6', 'month7', 'month8', 'month9', 'month10', 'month11', 'month12');
                foreach($fields as $field) {
                    if(isset($data[$field])) {
                        $data[$field] = $core->sanitize_inputs($data[$field], array('removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
                        $data[$field] = $db->escape_string($data[$field]);
                        $forecastline_data[$field] = $data[$field];
                    }
                }
                $forecastline_data['modifiedOn'] = TIME_NOW;
                $forecastline_data['modifiedBy'] = $core->user['uid'];
                $db->update_query(self::TABLE_NAME, $forecastline_data, self::PRIMARY_KEY.' = '.intval($this->data[self::PRIMARY_KEY]));
            }
        }
    }

    protected function validate_requiredfields(array $data = array()) {
        global $core, $db;
        if(is_array($data)) {
            $required_fields = array('pid', 'saleType', 'month1', 'month2', 'month3', 'month4', 'month5', 'month6', 'month7', 'month8', 'month9', 'month10', 'month11', 'month12');
            foreach($required_fields as $field) {
                if(isset($data[$field])) {
                    if(empty($data[$field]) && $data[$field] != '0') {
                        $this->errorcode = 2;
                        return true;
                    }
                }
            }
        }
    }

//    public function filter_securityview() {
//        global $core;
//        $forcastobj = GroupPurchaseForecast::get_data(array('gpfid' => $this->data['gpfid']));
//        $affiliate = new Affiliates($forcastobj->affid, false);
//        /* Data is created by user => show all data of the given user */
//        if(($this->data['businessMgr'] == $core->user['uid']) || $core->usergroup['grouppurchasing_canViewAllForecasts'] == 1) {
//            $filter_where = '';
//        }
//        /* User is GM of the affiliate => show all affiliate data */
//        if($affiliate->generalManager == $core->user['uid']) {
//            $filter_where = ' AND gpfid IN (SELECT  gpfid FROM '.GroupPurchaseForecast::TABLE_NAME.' WHERE affid IN('.$forcastobj->affid.'))';
//        }
//        /* User is supervisor of the affiliate => show all affiliate data */
//        if($affiliate->supervisor == $core->user['uid']) {
//            $filter_where = ' AND gpfid IN (SELECT  gpfid FROM '.GroupPurchaseForecast::TABLE_NAME.' WHERE affid IN('.$forcastobj->affid.'))';
//        }
//        /* User is an audit of the affiliate => show all affiliate data */
//
//
//
////        else {
////            $filter_where = ' AND gpfid IN (SELECT  gpfid FROM '.GroupPurchaseForecast::TABLE_NAME.' WHERE affid IN(2))';
////        }
//
//        return $filter_where;
//    }

    public static function get_forecastlinespermisiions(GroupPurchaseForecast $gpforecast) {
        global $core;

        if($core->usergroup['grouppurchase_canViewAllForecasts'] == 1) {
            return null;
        }

        $productseg_coordinators = ProdSegCoordinators::get_data(array('uid' => $core->user['uid']), array('returnarray' => true));
        if(is_array($productseg_coordinators)) {
            foreach($productseg_coordinators as $productseg_coordinator) {
                $segments[] = $productseg_coordinator->psid;
            }
        }

        $affiliates_where = '(affid IN ('.implode(',', $core->user['affiliates']).') AND(generalManager='.$core->user['uid'].' OR supervisor='.$core->user['uid'].'))';
        if(is_array($core->user['auditedaffids'])) {
            $affiliates_where .= ' OR (affid IN ('.implode(',', $core->user['auditedaffids']).'))';
        }

        $affiliates_filter = Affiliates::get_affiliates(array('affid' => $affiliates_where), array('returnarray' => true, 'simple' => false, 'operators' => array('affid' => 'CUSTOMSQL')));
        if(is_array($affiliates_filter)) {
            if(in_array($gpforecast->affid, array_keys($affiliates_filter))) {
                return;
            }
        }

        if(is_array($segments)) {
            $filter['psid'] = $segments;
        }
        else {
            $filter['businessMgr'] = $core->user['uid'];
        }
        return $filter;
    }

    public function get_gpforecast() {
        return new GroupPurchaseForecast($this->data[GroupPurchaseForecast::PRIMARY_KEY]);
    }

}