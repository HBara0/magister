<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: MeetingsMOMActions_class.php
 * Created:        @rasha.aboushakra    May 20, 2015 | 11:31:35 PM
 * Last Update:    @rasha.aboushakra    May 20, 2015 | 11:31:35 PM
 */

class MeetingsMOMActions extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'momaid';
    const TABLE_NAME = 'meetings_mom_actions';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core, $lang;
        if(!$this->validate_requiredfields($data)) {
            $actions['date'] = strtotime($data['date']);
            $actions['what'] = $data['what'];
            $actions['isTask'] = $data['isTask'];
            $actions['momid'] = $data['momid'];
            $actions['inputChecksum'] = $data['inputChecksum'];

            $query = $db->insert_query(self::TABLE_NAME, $actions);
            if($query) {
                $this->data[self::PRIMARY_KEY] = $db->last_id();
                if(!is_array($data['users']) && !is_array($data['representatives'])) {
                    $this->errorcode = 1;
                    return $this;
                }
                if(is_array($data['users'])) {
                    foreach($data['users'] as $user) {
                        if(!empty($user['uid'])) {
                            $user[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
                            $user['repid'] = 0;
                            $action_assignees = new MeetingsMOMActionAssignees();
                            $action_assignees->set($user);
                            $action_assignees->save();
                            $users[$user['uid']] = $user['uid'];
                        }
                    }
                }
                if(is_array($data['representatives'])) {
                    foreach($data['representatives'] as $representative) {
                        if(!empty($representative['repid'])) {
                            $representative[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
                            $representative['uid'] = 0;
                            $action_assignees = new MeetingsMOMActionAssignees();
                            $action_assignees->set($representative);
                            $action_assignees->save();
                        }
                    }
                }
                /* Create task - START */
                if($actions['isTask'] == 1) {
                    $task_share = false;
                    foreach($users as $user) {
                        $new_task = array(
                                'uid' => $user,
                                'subject' => $data['what'],
                                'priority' => 1,
                                'dueDate' => $data['date'],
                                'description' => $lang->specificfollowactions,
                                'reminderInterval' => 604800, //Every 2 days
                                'reminderStart' => $data['date'],
                                'createdBy' => $core->user['uid']
                        );
                        if(!$task_share) {
                            $new_task['share'] = array($core->user['uid']);
                        }
                        $task = new Tasks();
                        $createtask = $task->create_task($new_task);
                        $task_share = true;
                    }
                    if($createtask) {
                        $task->notify_task();
                    }
                }
                /* Create task - END */
            }
        }
    }

    protected function update(array $data) {
        global $db;
        if(!$this->validate_requiredfields($data)) {
            $actions['date'] = strtotime($data['date']);
            $actions['what'] = $data['what'];
            $actions['isTask'] = $data['isTask'];
            $actions['momid'] = $data['momid'];
            $actions['inputChecksum'] = $data['inputChecksum'];

            $query = $db->update_query(self::TABLE_NAME, $actions, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
            if($query) {
                if(!is_array($data['users']) && !is_array($data['representatives'])) {
                    $this->errorcode = 1;
                    return $this;
                }
                if(is_array($data['users'])) {
                    foreach($data['users'] as $user) {
                        if(!empty($user['uid'])) {
                            $user[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
                            $user['repid'] = 0;
                            $action_assignees = new MeetingsMOMActionAssignees();
                            $action_assignees->set($user);
                            $action_assignees->save();
                        }
                    }
                }
                if(is_array($data['representatives'])) {
                    foreach($data['representatives'] as $representative) {
                        if(!empty($representative['repid'])) {
                            $representative[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
                            $representative['uid'] = 0;
                            $action_assignees = new MeetingsMOMActionAssignees();
                            $action_assignees->set($representative);
                            $action_assignees->save();
                        }
                    }
                }
            }
            return $this;
        }
    }

    protected function validate_requiredfields(array $data = array()) {
        if(is_array($data)) {
            $required_fields = array('momid', 'what');
            foreach($required_fields as $field) {
                if(empty($data[$field]) && $data[$field] != '0') {
                    $this->errorcode = 2;
                    return true;
                }
            }
        }
    }

}