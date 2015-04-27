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
    protected $errorcode = 0;

    const PRIMARY_KEY = 'tmpfid';
    const TABLE_NAME = 'travelmanager_plan_finance';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = 'tmpsid,currency';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db;
        $tanspdata_array = array(
                'tmpsid' => $data['tmpsid'],
                'amount' => $data['amount'],
                'currency' => $data['currency'],
                'inputChecksum' => $data['inputChecksum'],
        );
        $query = $db->insert_query(self::TABLE_NAME, $tanspdata_array);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
        }
        return $this;
    }

    protected function update(array $data) {
        global $db;
        if(is_array($data)) {
            $financedata['tmpsid'] = $data['tmpsid'];
            $financedata['amount'] = $data['amount'];
            $financedata['currency'] = $data['currency'];
            $financedata['inputChecksum'] = $data['inputChecksum'];
        }
        $db->update_query(self::TABLE_NAME, $financedata, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
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
        return $this->amount * $exchangerate;
    }

}