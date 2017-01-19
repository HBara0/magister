<?php

/**
 * Description of CalendarTaskShare_class
 *
 * @author tony.assaad
 */
class CalendarTaskShares extends AbstractClass {

    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'ctsid';
    const TABLE_NAME = 'calendar_tasks_shares';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'ctsid, ctid, uid';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db, $core;
        if (is_array($data)) {
            if (is_empty($data['ctid'])) {
                return;
            }
            $task_data['ctid'] = $data['ctid'];
            unset($data['ctid']);

            $task_data['createdBy'] = $core->user['uid'];
            $task_data['createdOn'] = TIME_NOW;
            $task_data['uid'] = intval($data['uid']);

            $db->insert_query(self::TABLE_NAME, $task_data);
        }
    }

    public function save(array $data = array()) {
        if (empty($data)) {
            $data = $this->data;
        }
        if (isset($this->data[self::PRIMARY_KEY]) && !empty($this->data[self::PRIMARY_KEY])) {
            $this->update($data);
        }
        else {
            $existing_share = CalendarTaskShares::get_data(array('uid' => $data['uid'], 'ctid' => $data['ctid']));
            if (is_object($existing_share)) {
                $this->update($data);
            }
            else {
                $this->create($data);
            }
        }

        $this->errorode = 0;
    }

    protected function update(array $data) {
        
    }

//    protected function delete() {
//        global $db;
//        $query = $db->delete_query(self::TABLE_NAME, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
//        if($query) {
//            return true;
//        }
//        return false;
//    }

    public function get_user() {
        return new Users($this->data['uid']);
    }

    public function get_task() {
        return new Tasks($this->data['ctid']);
    }

    public function get_tasks_byuser($userid) {
        $tasks_ids = CalendarTaskShares::get_column('ctid', array('uid' => $userid), array('order' => array('sort' => array('DESC'), 'by' => array('createdOn')), 'returnarray' => true));
        if (is_array($tasks_ids)) {
            foreach ($tasks_ids as $taskid) {
                $tasks[] = new Tasks($taskid);
            }
            return $tasks;
        }
        return null;
    }

}
