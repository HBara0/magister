<?php

/**
 * Description of CalendarEventsRestrictions_class
 *
 * @author zaher.reda
 */
class CalendarEventsRestrictions extends AbstractClass {

    const PRIMARY_KEY = 'cerid';
    const TABLE_NAME = 'calendar_events_restrictions';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'affid,ceid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function update(array $data) {
        
    }

    /**
     *
     * @return \Events
     */
    public function get_event() {
        return new Events($this->{Events::PRIMARY_KEY});
    }

    /**
     *
     * @return \Affiliates
     */
    public function get_affiliate() {
        return new Affiliates($this->{Affiliates::PRIMARY_KEY});
    }

}

?>