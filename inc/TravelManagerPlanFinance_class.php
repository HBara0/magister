<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: TravelManagerPlanFinance_class.php
 * Created:        @hussein.barakat    Mar 17, 2015 | 1:41:36 PM
 * Last Update:    @hussein.barakat    Mar 17, 2015 | 1:41:36 PM
 */

class TravelManagerPlanFinance extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'tmpfid';
    const TABLE_NAME = 'travelmanager_plan_finance';
    const SIMPLEQ_ATTRS = 'tmpsid,amount,currency,paidBy,paidById';
    const UNIQUE_ATTRS = 'tmpfid';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '') {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    protected function read($id = '') {
        global $db;
        $this->data = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function create(array $data) {
        global $db;

        $tanspdata_array = array(
                'tmpsid' => $data['tmpsid'],
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'paidBy' => $data['paidBy'],
                'paidById' => $data['paidById'],
        );

        $db->insert_query(self::TABLE_NAME, $data);
        $this->data[self::PRIMARY_KEY] = $db->last_id();
    }

    protected function update(array $data) {
        global $db, $core;
        if(is_array($data)) {
            $financedata['tmpsid'] = $data['tmpsid'];
            $financedata['amount'] = $data['amount'];
            $financedata['paidBy'] = $data['paidBy'];
            $financedata['paidById'] = $data['paidById'];
            $financedata['currency'] = $data['currency'];

            $db->update_query(self::TABLE_NAME, $financedata, 'tmpsid='.intval($data['tmpsid']));
        }
    }

    public static function get_planfina_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_data($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_segment() {
        return new TravelManagerPlanSegments($this->data['tmpsid']);
    }

    public function get_currency() {
        return new Currencies($this->data['currency']);
    }

    public function parse_paidby() {
        global $lang;

        $paidby_entities = array(
                'myaffiliate' => $lang->myaffiliate,
                'supplier' => $lang->supplier,
                'client' => $lang->client,
                'myself' => $lang->myself,
                'anotheraff' => $lang->anotheraff
        );
        foreach($paidby_entities as $val => $paidby) {
            $selected = '';
            if($this->data['paidBy'] === $val) {
                $selected = ' selected="selected"';
            }

            $paid_options.="<option value=".$val." {$selected}>{$paidby}</option>";
        }

        return $paid_options;
    }

    public function get() {
        return $this->data;
    }

    public function get_convertedamount(Currencies $tocurrency) {
        if($this->currency == $tocurrency->numCode) {
            return $this->amount;
        }
        $fromcurrency = new Currencies($this->currency);
        $exchangerate = $tocurrency->get_latest_fxrate($tocurrency->alphaCode, array(), $fromcurrency->alphaCode);

        if(empty($exchangerate)) {
            $reverserate = $tocurrency->get_latest_fxrate($fromcurrency->alphaCode, array(), $tocurrency->alphaCode);
            if(!empty($reverserate)) {
                $exchangerate = 1 / $reverserate;
                $tocurrency->set_fx_rate($fromcurrency->numCode, $tocurrency->numCode, $exchangerate);
            }
        }
        return $this->priceNight * $exchangerate;
    }

}