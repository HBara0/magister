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

}
