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
                'paidBy' => $data['paidBy'],
                'paidById' => $data['paidById'],
                'inputChecksum' => $data['inputChecksum'],
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
            $financedata['inputChecksum'] = $data['inputChecksum'];


            $db->update_query(self::TABLE_NAME, $financedata, 'tmpsid='.intval($data['tmpsid']));
        }
    }

}