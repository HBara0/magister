<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: GadgetFxRates_class.php
 * Created:        @hussein.barakat    11-Mar-2016 | 14:30:02
 * Last Update:    @hussein.barakat    11-Mar-2016 | 14:30:02
 */

class GadgetFxRates extends SystemGadget {
    protected $data = array();
    protected $widget_id = '1';

    public function __construct() {
        parent::__construct();
    }

    public function create(array $data) {

    }

    public function update(array $data) {

    }

    /**
     *
     * @param array $instancedata
     * @return string
     */
    public function parse(array $instancedata) {
        if(empty($instancedata['serializedConfig'])) {
            return 'Error Parsing : missing configs';
        }
        $serialized_config = $instancedata['serializedConfig'];
        $configs = unserialize($serialized_config);
        if(!is_array($configs)) {
            return 'Error Parsing : missing configs';
        }
        if(is_empty($configs['required']['currency'])) {
            return 'Error Parsing : missing currencies';
        }
        $currency_arrays = explode(',', $configs['required']['currency']);
        if(!is_array($currency_arrays)) {
            return 'Error Parsing : missing currencies';
        }
        return $this->parse_rates_list($currency_arrays);
    }

    /**
     * Return list items of rates list
     * @global type $lang
     * @param type $currency_arrays
     * @param type $tocurrency_id
     * @return string
     */
    public function parse_rates_list($currency_arrays, $tocurrency_id = 840) {
        global $lang, $core;
        $currencysrates_list = '<ul class="list-group">';
        $tocurrency_obj = new Currencies($tocurrency_id);
        $tocurrency_output = $lang->basecurrency.' '.$tocurrency_obj->get_displayname().'<br>';
        foreach($currency_arrays as $curid) {
            $currency_obj = new Currencies($curid);
            $currency = $currency_obj->get();
            $fxrates[$currency['alphaCode']] = $tocurrency_obj->get_latest_fxrate($currency['alphaCode'], array('incDate' => 1));
        }
        if(is_array($fxrates)) {
            foreach($fxrates as $alpha => $fxrate) {
                if(is_array($fxrate)) {
                    $currencysrates_list .= '<li class="list-group-item"><span title="'.round(1 / $fxrate['rate'], 4).'">'.round($fxrate['rate'], 4).' '.$alpha.'</span> <span class="smalltext" style="color:#CCC;">'.date($core->settings['dateformat'], $fxrate['date']).'</span></li>';
                }
            }
        }
        $currencysrates_list .='</ul>';
        return $tocurrency_output.$currencysrates_list;
    }

    /**
     * get string of currency ids seperated by comma and return them in an array
     * @param type $existingcurrencies
     * @return boolean on failure
     * @return array of currency ids
     */
    public function fix_settingsarray($existingcurrencies) {
        $curids = explode(',', $existingcurrencies);
        if(is_array($curids)) {
            return $curids;
        }
        return false;
    }

}