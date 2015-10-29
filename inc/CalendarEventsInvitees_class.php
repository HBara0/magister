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

    /**
     * @description Deletes the invitation after sending a notification email to the invitee
     * @return bool
     */
    public function delete_invitation() {
        global $lang, $core;

        $user = $this->get_invitee();
        $event = $this->get_event();
        $email_subject = $lang->sprint($lang->eventcancelledsubject, $event->get_displayname());
        $email_message = $lang->sprint($lang->eventcancelledmessage, $event->get_displayname(), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $core->input['fromDate']), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $event->fromDate), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $event->toDate));
        $email_data = array(
                'from_email' => $core->settings['maileremail'],
                'from' => 'Orkila Mailer',
                'to' => $user->email,
                'subject' => $email_subject,
                'message' => $email_message,
        );
        $mail = new Mailer($email_data, 'php');
        $delete = $this->delete();
        return $delete;
    }

}
?>