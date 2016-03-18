<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: GadgetFxRates_class.php
 * Created:        @hussein.barakat    11-Mar-2016 | 14:30:02
 * Last Update:    @hussein.barakat    11-Mar-2016 | 14:30:02
 */

class GadgetTimezones extends SystemGadget {
    protected $data = array();
    protected $widget_id = '2';

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
     * @return type
     */
    public function parse(array $instancedata) {
        if(!empty($instancedata['serializedConfig'])) {
            $serialized_config = $instancedata['serializedConfig'];
            $configs = unserialize($serialized_config);
            if(is_array($configs)) {
                $timezones_array = explode(',', $configs['options']['timezones']);
            }
        }
        return $this->parse_timezones_list($timezones_array);
    }

    /**
     * Return Timezeone list as fed, else generate it for default set of timezones
     * @global type $lang
     * @global type $core
     * @return string
     */
    public function parse_timezones_list($timezones = '') {
        global $lang, $core;
        if(!is_array($timezones) || is_empty($timezones)) {
            $timezones = array('UTC', 'Africa/Casablanca', 'Africa/Dakar', 'Africa/Abidjan', 'Europe/Paris', 'Africa/Lagos', 'Africa/Algiers', 'Africa/Tunis', 'Africa/Cairo', 'Asia/Beirut', 'Asia/Amman', 'Africa/Nairobi', 'Asia/Riyadh', 'Asia/Tehran', 'Asia/Dubai', 'Asia/Hong_Kong');
        }
        $gmttime = gmmktime(gmdate('H'), gmdate('i'), gmdate('s'), gmdate('n'), gmdate('d'), gmdate('Y'));
        $timezones_list.='<ul>';
        foreach($timezones as $timezone) {
            $timezone_obj = new DateTimeZone($timezone);
            $time_obj = new DateTime('now', $timezone_obj);
            $timezone_city = str_replace('_', ' ', explode('/', $timezone));
            if(empty($timezone_city[1])) {
                $timezone_city[1] = $timezone_city[0];
            }
            $timezones_list .= '<li>'.$lang->sprint($lang->timecity, date('H:i', $gmttime + $timezone_obj->getOffset($time_obj)), ucwords($timezone_city[1])).'</li>';
        }
        $timezones_list.='</ul>';
        return $timezones_list;
    }

}