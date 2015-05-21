<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: MeetingsMom_ActionsAssignees_class.php
 * Created:        @rasha.aboushakra    May 21, 2015 | 12:07:50 AM
 * Last Update:    @rasha.aboushakra    May 21, 2015 | 12:07:50 AM
 */

class MeetingsMOMActionAssignees extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'momaaid';
    const TABLE_NAME = 'meetings_mom_action_assignees';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'momaid,repid,uid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $query = $db->insert_query(self::TABLE_NAME, $data);
        if($query) {
            return $this;
        }
    }

    protected function update(array $data) {
        global $db;
        $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        return $this;
    }

}