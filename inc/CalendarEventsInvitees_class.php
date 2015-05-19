<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: CalendarEventsInvitees.php
 * Created:        @zaher.reda    May 16, 2015 | 5:46:02 PM
 * Last Update:    @zaher.reda    May 16, 2015 | 5:46:02 PM
 */

/**
 * Class for Calender Events Invitees
 *
 * @author zaher.reda
 */
class CalendarEventsInvitees extends AbstractClass {
    const PRIMARY_KEY = 'ceiid';
    const TABLE_NAME = 'calendar_events_invitees';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'ceid,uid';

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
     * @return \Users
     */
    public function get_invitee() {
        return new Users($this->{Users::PRIMARY_KEY});
    }

}
?>