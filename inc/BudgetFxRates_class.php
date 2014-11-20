<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: BudgetFxRates_class.php
 * Created:        @tony.assaad    Oct 8, 2014 | 2:10:08 PM
 * Last Update:    @tony.assaad    Oct 8, 2014 | 2:10:08 PM
 */

/**
 * Description of BudgetFxRates_class
 *
 * @author tony.assaad
 */
class BudgetFxRates extends AbstractClass {
    const PRIMARY_KEY = 'bfxid';
    const TABLE_NAME = 'budgeting_fxrates';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'bfxid, affid, year, fromCurrency, toCurrency, rate';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db;
        if(empty($data[rate])) {
            $this->errorcode = 1;
            return false;
        }
        if(isset($data['createreverserate']) && $data['createreverserate'] == 1) {
            $this->create_reverserate($data);
        }
        unset($data['createreverserate']);
        $db->insert_query(self::TABLE_NAME, $data);
    }

    private function create_reverserate($data) {
        global $db;
        $affid = $data['affid'];
        $year = $data['year'];
        $rate = $data['rate'];
        unset($data['createreverserate'], $reversed_data['createreverserate'], $data['rate'], $data['affid'], $data['year']);
        /* swap the 2 arrays */
        $reversed_data = array_combine(array_keys($data), array_reverse(array_values($data)));
        unset($reversed_data['createreverserate']);
        $reversed_data['affid'] = $affid;
        $reversed_data['year'] = $year;
        $reversed_data['rate'] = 1 / $rate;
        if(!is_empty($data['affid'], $data['year'], $data['rate'])) {
            $db->insert_query(self::TABLE_NAME, $data);
        }
        $db->insert_query(self::TABLE_NAME, $reversed_data);
    }

    protected function update(array $data) {
        global $db;

        if(is_array($data)) {
            unset($data['createreverserate']);
            $query = $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
            if(!query) {
                $this->errorcode = 601;
                return;
            }
        }
    }

    public function save(array $data = array()) {
        if(empty($data)) {
            $data = $this->data;
        }
        $existing_rate = BudgetFxRates::get_data(array('affid' => $data['affid'], 'year' => $data['year'], 'fromCurrency' => $data['fromCurrency'], 'toCurrency' => $data['toCurrency']));
        if(!is_object($existing_rate)) {
            if(isset($this->data)) {
                $this->create($this->data);
            }
        }
        else {

            $existing_rate->update($data);
        }
    }

    public function get_formCurrency() {
        return new Currencies($this->data['fromCurrency']);
    }

    public function get_toCurrency() {

        return new Currencies($this->data['toCurrency']);
    }

}