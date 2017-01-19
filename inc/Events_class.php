<?php

/**
 * Description of Events
 *
 * @author H.B
 */
class Events extends AbstractClass {

    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'eid';
    const TABLE_NAME = 'events';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const REQUIRED_ATTRS = 'title,fromDate,toDate';
    const UNIQUE_ATTRS = 'alias,fromDate,toDate';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $log, $core, $errorhandler, $lang;
        if (!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        $data['createdOn'] = TIME_NOW;
        $data['createdBy'] = $core->user['uid'];
        $data['alias'] = generate_alias($data['title']);
        if (!is_empty($data['fromTime'], $data['fromDate'])) {
            $data['fromDate'] = strtotime($data['fromDate'] . ' ' . $data['fromTime']);
        }
        else {
            $this->errorcode = 2;
            return $this;
        }
        if (!is_empty($data['toTime'], $data['toDate'])) {
            $data['toDate'] = strtotime($data['toDate'] . ' ' . $data['toTime']);
        }
        else {
            $this->errorcode = 3;
            return $this;
        }
        unset($data['fromTime'], $data['toTime']);
        if (!$data['inputChecksum']) {
            $data['inputChecksum'] = generate_checksum();
        }
        if (is_array($data)) {
            $query = $db->insert_query(self::TABLE_NAME, $data);
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $log, $core, $errorhandler, $lang;
        if (!$this->validate_requiredfields($data)) {
            $this->errorcode = 1;
            return $this;
        }
        if (!is_empty($data['fromTime'], $data['fromDate'])) {
            $data['fromDate'] = strtotime($data['fromDate'] . ' ' . $data['fromTime']);
        }
        else {
            $this->errorcode = 2;
            return $this;
        }
        if (!is_empty($data['toTime'], $data['toDate'])) {
            $data['toDate'] = strtotime($data['toDate'] . ' ' . $data['toTime']);
        }
        else {
            $this->errorcode = 3;
            return $this;
        }
        unset($data['fromTime'], $data['toTime']);
        $data['modifiedOn'] = TIME_NOW;
        $data['modifiedBy'] = $core->user['uid'];
        $data['alias'] = generate_alias($data['title']);

        if (is_array($data)) {
            $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY . '=' . intval($this->data[self::PRIMARY_KEY]));
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
        }
        return $this;
    }

    /**
     *
     * @return \Users|boolean
     */
    public function get_createdBy() {
        return new Users(intval($this->data['createdBy']));
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {

        if (is_array($attributes_param)) {
            foreach ($attributes_param as $attr => $val) {
                $attributes .= $attr . '="' . $val . '"';
            }
        }
        return '<a href="' . $this->get_link() . '" ' . $attributes . '>' . $this->get_displayname() . '</a>';
    }

    public function get_link() {
        global $core;
        return $core->settings['rootdir'] . '/index.php?module=events/eventprofile&amp;id=' . $this->data[self::PRIMARY_KEY];
    }

    public function is_subscribed($uid) {
        $assignedevents = CalendarAssignments::get_data(array('uid' => intval($uid), 'eid' => $this->get_id(), 'isActive' => 1), array('returnarray' => true));
        if (is_array($assignedevents)) {
            return true;
        }
        return false;
    }

    public function canManageEvent() {
        global $core;
        if ($core->usergroup['canManageAllEvents']) {
            return true;
        }
        elseif ($this->data['createdBy'] == $core->user['uid']) {
            return true;
        }
        return false;
    }

    public function get_editlink() {
        global $core;
        return $core->settings['rootdir'] . '/index.php?module=events/manageevent&amp;id=' . $this->data[self::PRIMARY_KEY];
    }

    public function get_totalstudents() {
        $assignedstudents = CalendarAssignments::get_data(array('eid' => $this->get_id(), 'isActive' => 1), array('returnarray' => true));
        if (is_array($assignedstudents)) {
            return count($assignedstudents);
        }
        return 0;
    }

    public function is_past() {
        if ($this->data['toDate'] > TIME_NOW) {
            return true;
        }
        return false;
    }

    public function get_fromdate() {
        return $this->data['fromDate'];
    }

    /**
     *
     * @global type $core
     * @return type
     */
    public function get_todate() {
        global $core;
        if ($this->data['toDate']) {
            return $this->data['toDate'];
        }

        return $this->data['fromDate'] + $core->settings['lecturelength'];
    }

    public function get_subsribers() {
        $assignedevents = CalendarAssignments::get_data(array('eid' => $this->get_id(), 'isActive' => 1), array('returnarray' => true));
        if (is_array($assignedevents)) {
            foreach ($assignedevents as $assignedevent) {
                $subscribers[$assignedevent->uid] = $assignedevent->get_user();
            }
            return $subscribers;
        }
        return false;
    }

    public function get_fromdateoutput($format = 'd-m-Y') {
        return date($format, $this->get_fromdate());
    }

    public function get_fromtimeoutput($format = 'h:i A') {
        return date($format, $this->get_fromdate());
    }

    public function get_todateoutput($format = 'd-m-Y') {
        return date($format, $this->get_todate());
    }

    public function get_totimeoutput($format = 'h:i A') {
        return date($format, $this->get_todate());
    }

    /**
     *
     * @return string
     */
    public function parse_daterangeoutput() {
        $fromdate = $this->get_fromdateoutput('D, j M Y') . ' ' . $this->get_fromtimeoutput();
        $fromdate_class = 'success';
        if ($this->data['fromDate'] < TIME_NOW) {
            $fromdate_class = 'danger';
        }
        $fromdate = '<span class="label label-' . $fromdate_class . '">' . $fromdate . '</span>';

        $todate = $this->get_todateoutput('D, j M Y') . '  ' . $this->get_totimeoutput();
        $todate_class = 'success';
        if ($this->data['toDate'] < TIME_NOW) {
            $todate_class = 'danger';
        }
        $todate = '<span class="label label-' . $todate_class . '">' . $todate . '</span>';

        return $fromdate . ' TO ' . $todate;
    }

    /**
     *
     * @return boolean|\Recommendations
     */
    public function get_recommendation() {
        if (!intval($this->data['rid'])) {
            return false;
        }
        $recommednation_obj = new Recommendations(intval($this->data['rid']));
        if (!is_object($recommednation_obj)) {
            return false;
        }
        return $recommednation_obj;
    }

}
