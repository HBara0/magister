<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: GadgetFxRates_class.php
 * Created:        @hussein.barakat    22-Mar-2016 | 14:30:02
 * Last Update:    @hussein.barakat    22-Mar-2016 | 14:30:02
 */

class GadgetLeaveBalance extends SystemGadget {
    protected $data = array();
    protected $widget_id = '3';

    const CLASSNAME = __CLASS__;

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
//        if(!empty($instancedata['serializedConfig'])) {
//            $serialized_config = $instancedata['serializedConfig'];
//            $configs = unserialize($serialized_config);
//            if(is_array($configs)) {
//                $timezones_array = explode(',', $configs['options']['timezones']);
//            }
//        }
        return $this->parse_leavebalancegadget($instancedata);
    }

    /**
     * @global type $lang
     * @global type $core
     * @global type $template
     * @param type $instancedata
     * @return string html
     */
    public function parse_leavebalancegadget($instancedata = '') {
        global $lang, $core, $template;
        $divid = 'gauge'.TIME_NOW;
        if(is_array($instancedata) && !empty($instancedata['swgiid'])) {
            $divid = 'gauge'.$instancedata['swgiid'];
        }
        $current_leavestat = LeavesStats::get_data("uid={$core->user['uid']} ORDER BY periodStart DESC LIMIT 0,1");
        if(is_object($current_leavestat)) {
            $leavestat = $current_leavestat->get();
            $leavestat['minimum'] = 0;
            $leavestat['title'] = $lang->leavestatusfor.' '.date('Y', $leavestat['periodStart']);
        }
        else {
            $leavestat = array();
            $leavestat['title'] = $lang->noleavestats;
            $leavestat['minimum'] = $leavestat['canTake'] = $leavestat['daysTaken'] = 0;
        }
        eval("\$gauge = \"".$template->get('system_default_gauge')."\";");

        return $gauge;
    }

}