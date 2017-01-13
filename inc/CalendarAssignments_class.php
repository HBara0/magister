<?php

/**
 * Description of CalendarAssignments
 *
 * @author H.B
 */
class CalendarAssignments extends AbstractClass {

    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'caid';
    const TABLE_NAME = 'calendarassignments';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const REQUIRED_ATTRS = 'uid';
    const UNIQUE_ATTRS = 'uid,did,eid';

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

        $data['modifiedOn'] = TIME_NOW;
        $data['modifiedBy'] = $core->user['uid'];
        if (is_array($data)) {
            $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY . '=' . intval($this->data[self::PRIMARY_KEY]));
            $log->record(self::TABLE_NAME, $this->data[self::PRIMARY_KEY]);
        }
        return $this;
    }

    /**
     * 
     * @return \Users
     */
    public function get_createdBy() {
        return new Users(intval($this->data['createdBy']));
    }

    /**
     *
     * @return boolean|string
     */
    public function get_type() {
        if (intval($this->data['did'])) {
            return 'deadline';
        }
        if (intval($this->data['eid'])) {
            return 'event';
        }

        return false;
    }

    public function get_displayname() {
        if (intval($this->data['did'])) {
            $deadline_obj = new Deadlines(intval($this->data['did']));
            return $deadline_obj->get_displayname();
        }

        if (intval($this->data['eid'])) {
            $event_obj = new Events(intval($this->data['eid']));
            return $event_obj->get_displayname();
        }

        return 'N/A';
    }

    /**
     *
     * @return \Deadlines|boolean\
     */
    public function get_deadline() {
        if (!$this->data['did']) {
            return false;
        }
        return new Deadlines(intval($this->data['did']));
    }

    /**
     *
     * @return boolean|\Events
     */
    public function get_event() {
        if (!$this->data['eid']) {
            return false;
        }
        return new Events(intval($this->data['eid']));
    }

}
