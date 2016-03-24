<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: GadgetFxRates_class.php
 * Created:        @hussein.barakat    11-Mar-2016 | 14:30:02
 * Last Update:    @hussein.barakat    11-Mar-2016 | 14:30:02
 */

class GadgetCalendarToday extends SystemGadget {
    protected $data = array();
    protected $widget_id = '5';

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
        global $template, $lang;

        $current_date = getdate(TIME_NOW);
        $current_date['weekday'] = $lang->{strtolower($current_date['weekday'])};
        $current_date['monthname'] = $lang->{strtolower(date('F', TIME_NOW))};
        eval("\$output = \"".$template->get('gadget_calendartoday')."\";");
        return $output;
    }

}