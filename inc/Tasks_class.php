<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Logs Class
 * $id: Tasks_class.php
 * Created:		@zaher.reda		April 20, 2012 | 10:53 AM
 * Last Update: @zaher.reda		May 18, 2012 | 09:53 AM
 */

class Tasks {
    private $task = array();
    private $status = 0; //0=No errors;1=Subject missing;2=Entry exists;3=Error saving
    private $date_vars = array('dueDate', 'timeDone');

    const PRIMARY_KEY = 'ctid';
    const TABLE_NAME = 'calendar_tasks';
    const DISPLAY_NAME = 'subject';

    public function __construct($id = '', $simple = false) {
        global $core;

        if(isset($id) && !empty($id)) {
            $this->task = $this->read_task($id, $simple);
            if($simple == false) {
                foreach($this->date_vars as $attr) {
                    if(isset($this->task[$attr]) && !empty($this->task[$attr])) {
                        $this->task[$attr.'_output'] = date($core->settings['dateformat'], $this->task[$attr]);
                    }
                }
            }
        }
    }

    private function read_task($id, $simple = false) {
        global $db;

        if(empty($id)) {
            return false;
        }

        $query_select = 'ct.*, u.displayName AS assignedTo';
        if($simple == true) {
            $query_select = 'ctid, subject, pimAppId';
        }
        return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."calendar_tasks ct JOIN ".Tprefix."users u ON (u.uid=ct.uid) WHERE ctid=".$db->escape_string($id)));
    }

    /* Creates the task in the DB
     * @param  	Array			$data 		Array containing the input
     * @return  Boolean						0=No errors;1=Subject missing;2=Entry exists
     */
    public function parsestatus() {
        switch($this->task['percCompleted']) {
            case 0:
                $task_status = 'pending';
                break;
            case $this->task['percCompleted'] >= 1 && $this->task['percCompleted'] <= 99:
                $task_status = 'inprogress';
                break;
            default :
                $task_status = 'completed';
        }
        return $task_status;
    }

    public function create_task(array $data) {
        global $db, $log, $core;
        if(is_empty($data['subject'])) {
            $this->status = 1;
            return false;
        }
        if(!isset($data['dueDate']) || empty($data['dueDate'])) {
            if(isset($data['altDueDate']) && !empty($data['altDueDate'])) {
                $data['dueDate'] = strtotime($data['altDueDate']);
            }
            else {
                $data['dueDate'] = TIME_NOW + 604800;
            }
        }
        else {
            $data['dueDate'] = strtotime($data['dueDate']);
        }
        if(value_exists('calendar_tasks', 'subject', $data['subject'], 'dueDate='.$data['dueDate'].' AND uid='.$db->escape_string($data['uid']))) {
            $this->status = 2;
            return false;
        }

        $data['description'] = $core->sanitize_inputs($data['description'], array('method' => 'striponly', 'removetags' => true, 'allowable_tags' => '<blockquote><b><strong><em><ul><ol><li><p><br><strike><del><pre><dl><dt><dd><sup><sub><i><cite><small>'));
        if(isset($data['altDueDate']) && !empty($data['altDueDate'])) {
            $data['dueDate'] = strtotime($data['altDueDate']);
        }
        $new_task = array(
                'uid' => $data['uid'],
                'identifier' => substr(md5(uniqid(microtime())), 1, 10),
                'subject' => ucwords(strtolower($core->sanitize_inputs($data['subject']))),
                'priority' => $data['priority'],
                'percCompleted' => $data['percCompleted'],
                'description' => ucfirst(strtolower($data['description'])),
                'reminderInterval' => $data['reminderInterval'],
                'reminderStart' => strtotime($data['reminderStart']),
                'createdBy' => $core->user['uid'],
                'dueDate' => $data['dueDate'],
                'prerequisitTask' => $data['prerequisitTask']
        );

        if(empty($new_task['reminderStart'])) {
            unset($new_task['reminderInterval'], $new_task['reminderStart']);
        }

        $this->task = $new_task;

        /* get exist users for the current task */
        $query = $db->insert_query('calendar_tasks', $new_task);
        if($query) {
            $this->task['ctid'] = $db->last_id();
            $log->record($this->task['ctid']);
            /* share task  - START */
            if(is_array($data['share'])) {
                foreach($data['share'] as $uid) {
                    $calendarshare_obj = new CalendarTaskShares();
                    $calendarshare_obj->set(array('uid' => $uid, self::PRIMARY_KEY => $this->task[self::PRIMARY_KEY]));
                    $calendarshare_obj->save();
                }
            }
            $this->status = 0;
            return true;
        }
        else {
            $this->status = 3;
            return false;
        }
    }

    public function get_shared_users() {
        $shares = $this->get_shares();
        if(is_array($shares)) {
            foreach($shares as $share) {
                $users[$share->uid] = $share->get_user();
            }

            return $users;
        }
        return null;
    }

    public function get_shares() {
        return CalendarTaskShares::get_data(array(self::PRIMARY_KEY => $this->task[self::PRIMARY_KEY]), array('returnarray' => true));
    }

    public function notify_task() {
        global $core, $db, $lang;

        $lang->load('calendar_messages');
        if($this->task['uid'] != $core->user['uid']) {
            fix_newline($this->task['description']);

            /* prepare send  that in icalender format - START */
            $ical_obj = new iCalendar(array('identifier' => $this->task['identifier'], 'uidtimestamp' => $this->task['createdOn'], 'component' => 'task'));  /* pass identifer to outlook to avoid creation of multiple file with the same date */
            $ical_obj->set_summary($this->task['subject']);
            $ical_obj->set_name();
            $ical_obj->set_description($this->task['description']);
            $ical_obj->set_duedate($this->task['dueDate']);
            $ical_obj->set_priority($this->task['priority']);
//$ical_obj->set_icalattendees($this->task['uid']);
            $ical_obj->sentby();
            $ical_obj->set_percentcomplete($this->task['percCompleted']);
//$ical_obj->set_categories('CalendarTask');
            $ical_obj->endical();
            $ical_obj->save();

            $email_data['to'] = $db->fetch_field($db->query("SELECT email FROM ".Tprefix."users WHERE uid=".$db->escape_string($this->task['uid']).""), 'email');
            $mailer = new Mailer();
            $mailer = $mailer->get_mailerobj();

            $mailer->set_type('ical', array('content-class' => 'task', 'method' => 'REQUEST'));
            $mailer->set_from(array('name' => $core->user['displayName'], 'email' => $core->user['email']));
            $mailer->set_subject($lang->task.': '.$this->task['subject']);
            $mailer->set_message($lang->sprint($lang->assigntaskmessage, $this->parse_status(), date($core->settings['dateformat'], $this->task['dueDate']), $this->task['description']));
            $mailer->set_to($email_data['to']);
            $mailer->set_replyby($this->task['dueDate']);
            $mailer->add_attachment($ical_obj->get_filepath(), 'text/calendar', array('filename' => $this->task['subject']));
            $mailer->send();
            $ical_obj->delete();
            if($mailer->get_status() === false) {
                return false; //output_xml("<status>false</status><message>{$lang->errorsendingemail}</message>");
            }
        }
    }

    public function set_pimid($pimid) {
        global $db, $log;

        $db->update_query('calendar_tasks', array('pimAppId' => $pimid), 'ctid='.$this->task['ctid']);
    }

    public function change_status($new_status) {
        global $db, $log;

        $new_perc = 0;
        if($new_status == 1) {
            $new_perc = 100;
        }
        $query = $db->update_query('calendar_tasks', array('isDone' => $new_status, 'timeDone' => TIME_NOW, 'percCompleted' => $new_perc), 'ctid='.$db->escape_string($this->task['ctid']));
        if($db->affected_rows() > 0) {
            try {
                if(isset($this->task['pimAppId'])) {
                    $ol = new COM('Outlook.Application');
                    $mapi = $ol->GetNamespace('MAPI');
                    $pimtask = $mapi->GetItemFromID($this->task['pimAppId']);

                    if($new_status == 0) {
                        $pimtask->complete = false;
                        $pimtask->PercentComplete = 0;
                        $pimtask->Status = 0;
                        $pimtask->Save();
                    }
                    else {
                        $pimtask->MarkComplete();
                    }
                }
            }
            catch(Exception $e) {

            }

            $this->status = 0;
            return true;
        }
        else {
            $this->status = 3;
            return false;
        }
        $log->record($this->task['ctid'], $new_status);
    }

    public function update_task($completed) {
        global $db, $core, $log;

        if(!isset($completed)) {
            return false;
        }

        $new_status = 0;
        if($completed == '100') {
            $new_status = 1;
        }
        $taskdata['percCompleted'] = $completed;
        $taskdata['isDone'] = $new_status;

        if($this->task['percCompleted'] == 0 && $this->task['percCompleted'] < $completed) {
            $taskdata['timestarted'] = TIME_NOW;
        }

        $query = $db->update_query('calendar_tasks', $taskdata, 'ctid='.$this->task['ctid']);

        if($query) {
            $log->record($this->task['ctid']);
            $this->status = 0;
            return true;
        }
        else {
            $this->status = 3;
            return true;
        }
    }

    public function save_note($note) {
        global $db, $core, $log;

        if(empty($note)) {
            $this->status = 1;
            return false;
        }
        /* Check if task note with same subject created by the same user exists */
        if(value_exists('calendar_tasks_notes', 'note', $note, 'uid='.$core->user['uid'].' AND ctid='.$this->task['ctid'])) {
            $this->status = 2;
            return false;
        }
        else {
            $task_notes_details = array(
                    'ctid' => $this->task['ctid'],
                    'uid' => $core->user['uid'],
                    'note' => $note,
                    'dateAdded' => TIME_NOW
            );

            $query = $db->insert_query('calendar_tasks_notes', $task_notes_details);
            if($query) {
                $log->record($db->last_id(), $this->task['ctid']);
                $this->status = 0;
                return true;
            }
            else {
                $this->status = 3;
                return false;
            }
        }
    }

    public function get_notes() {
        return TasksNotes::get_data(array('ctid' => $this->task['ctid']), array('simple' => false, 'returnarray' => true, 'order' => array('by' => 'dateAdded', 'sort' => 'DESC')));
    }

    public function get_status() {
        return $this->status;
    }

    public function get_id() {
        return $this->task['ctid'];
    }

    public function get_task() {
        fix_newline($this->task['description']);
        return $this->task;
    }

    public function get_user() {
        return new Users($this->task['uid']);
    }

    public static function get_tasks($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_displayname() {
        return $this->task[self::DISPLAY_NAME];
    }

    public function get() {
        return $this->task;
    }

    public function __get($name) {
        if(isset($this->task[$name])) {
            return $this->task[$name];
        }
        return false;
    }

    public function __isset($name) {
        return isset($this->task[$name]);
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        if(is_array($attributes_param)) {
            foreach($attributes_param as $attr => $val) {
                $attributes .= $attr.' "'.$val.'"';
            }
        }

        return '<a href="index.php?module=calendar/task&id='.$this->task[self::PRIMARY_KEY].'" '.$attributes.'>'.$this->task[self::DISPLAY_NAME].'</a>';
    }

    public function parse_status() {
        global $lang;

        switch($this->task['priority']) {
            case '0': return $lang->prioritylow;
                break;
            case '1': return $lang->prioritynormal;
                break;
            case '2': return $lang->priorityhigh;
                break;
            default: return false;
        }
    }

    public function is_sharedwithuser() {
        global $core;
        if(value_exists(CalendarTaskShares::TABLE_NAME, 'uid', $core->user['uid'], self::PRIMARY_KEY.'='.intval($this->task[self::PRIMARY_KEY]))) {
            return true;
        }
        return false;
    }

}
?>