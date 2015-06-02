<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Meeting Minutes of Meeting Class
 * $id: MeetingsMOM_class.php
 * Created:        @zaher.reda    Nov 15, 2013 | 12:54:20 PM
 * Last Update:    @zaher.reda    Nov 15, 2013 | 12:54:20 PM
 */

class MeetingsMOM extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'momid';
    const TABLE_NAME = 'meetings_minsofmeeting';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'mtid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core, $log;

        if(empty($data['mtid']) || empty($data['meetingDetails'])) {
            $this->errorcode = 1;
            return false;
        }
        $data['meetingDetails'] = $core->sanitize_inputs($data['meetingDetails'], array('method' => 'striponly', 'allowable_tags' => '<span><div><a><br><p><b><i><del><strike><img><video><audio><embed><param><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><figure><figcaption><strong><em><table><tr><td><th><tbody><thead><tfoot><h1><h2><h3><h4><h5><h6>', 'removetags' => true));
        $data['followup'] = $core->sanitize_inputs($data['followup'], array('method' => 'striponly', 'allowable_tags' => '<span><div><a><br><p><b><i><del><strike><img><video><audio><embed><param><blockquote><mark><cite><small><ul><ol><li><hr><dl><dt><dd><sup><sub><big><pre><figure><figcaption><strong><em><table><tr><td><th><tbody><thead><tfoot><h1><h2><h3><h4><h5><h6>', 'removetags' => true));

        if(!value_exists('meetings_minsofmeeting', 'mtid', $data['mtid'])) {
            $query = $db->insert_query('meetings_minsofmeeting', array('mtid' => $data['mtid'], 'meetingDetails' => $data['meetingDetails'], 'followup' => $data['followup'], 'createdBy' => $core->user['uid'], 'createdOn' => TIME_NOW));
            $this->momid = $db->last_id();
            if($query) {
                $db->update_query('meetings', array('hasMOM' => 1), 'mtid='.$data['mtid']);
                $this->errorcode = 0;
                $log->record('addedmom', $data['mtid']);

                if(is_array($data['actions'])) {
                    foreach($data['actions'] as $action_data) {
                        $momactions = new MeetingsMOMActions();
                        $action_data['momid'] = $this->momid;
                        $momactions->set($action_data);
                        $momactions->save();
                    }
                }
            }
        }
        else {
            $mom_obj = MeetingsMOM::get_mom_bymeeting($data['mtid']);
            $data['momid'] = $mom_obj->get()['momid'];
            $mom_obj->update($data);
        }
    }

    public function update(array $data) {
        global $db, $core, $log;
        $data['modifiedBy'] = $core->user['uid'];
        $data['modifiedOn'] = TIME_NOW;
        $query = $db->update_query('meetings_minsofmeeting', array('meetingDetails' => $data['meetingDetails'], 'followup' => $data['followup'], 'modifiedBy' => $data['modifiedBy'], 'modifiedOn' => $data['modifiedOn']), 'momid='.$this->momid.'');

        if($query) {
            $this->errorcode = 0;
            $log->record('updatedmom', $data['mtid']);
            if(is_array($data['actions'])) {
                foreach($data['actions'] as $action_data) {
                    $momactions = new MeetingsMOMActions();
                    $action_data['momid'] = $this->momid;
                    $momactions->set($action_data);
                    $momactions->save();
                }
            }
        }
    }

    public static function get_mom_bymeeting($mtid) {
        global $db;

        $momid = $db->fetch_field($db->query('SELECT momid FROM '.Tprefix.'meetings_minsofmeeting WHERE mtid='.intval($mtid)), 'momid');
        if(!empty($momid)) {
            return new MeetingsMOM($momid);
        }
        return false;
    }

    public function get_errorcode() {
        return $this->errorcode;
    }

//    public function get() {
//        return $this->data;
//    }

    /*
     * Parse Specific actions follow up 
     */
    public function parse_actions($source = '', $momactions = array()) {
        global $template, $core, $lang;
        if(isset($core->input['mtid']) && !empty($core->input['mtid'])) {
            $meetingmom = MeetingsMOM::get_mom_bymeeting($core->input['mtid']);
            $momactions = MeetingsMOMActions::get_data(array('momid' => $meetingmom->momid), array('returnarray' => true));
        }
        if(is_array($momactions)) {
            $arowid = 0;
            foreach($momactions as $actions) {
                $actions_data = $actions->get();
                if($actions_data['date'] != 0) {
                    $actions_data['date_otput'] = date($core->settings['dateformat'], $actions_data['date']);
                    $actions_data['date_formatted'] = date($core->settings['dateformat'], $actions_data['date']);
                }
                if($actions_data['isTask'] == 1) {
                    $istask = $lang->markedasatask;
                    if($source == 'QR') {
                        $task = Tasks::get_tasks(array('description' => 'Specific follow up actions', 'subject' => $actions_data['what']), array('simple' => false));
                        if(is_object($task)) {
                            if($task->isDone == 1) {
                                $istask .=' (<img src="./images/valid.gif" alt="'.$lang->valid.'" border="0" /><small>completed</small>)';
                            }
                        }
                    }
                }
                $momactionsassignees = MeetingsMOMActionAssignees::get_data(array('momaid' => $actions->momaid), array('returnarray' => true));
                $userrowid = 0;
                $reprowid = 0;
                if(is_array($momactionsassignees)) {
                    foreach($momactionsassignees as $assignee) {
                        $assignee_data = $assignee->get();
                        if(isset($assignee->uid) && !empty($assignee->uid)) {
                            $user = new Users($assignee->uid);
                            if(is_object(($user))) {
                                $assignee_data['username'] = $user->get_displayname();
                            }
                            $checksum['users'] = $assignee->inputChecksum;

                            $actions_users .= '<tr id="'.$userrowid.'" width="100%"><td><span>'.$assignee_data[username].'</span><td></tr>';
                            $userrowid++;
                        }
                        if(isset($assignee->repid) && !empty($assignee->repid)) {
                            $representative = new Representatives($assignee->repid);
                            if(is_object(($representative))) {
                                $assignee_data['repname'] = $representative->get_displayname();
                            }
                            $checksum['representatives'] = $assignee->inputChecksum;

                            $actions_representatives .='<tr><td><span>'.$assignee_data[repname].'</span></td></tr>';
                            $reprowid++;
                        }
                    }
                }
                eval("\$actions_rows .= \"".$template->get('meetings_mom_actions_rowsdisplay')."\";");

                unset($istask, $actions_users, $actions_representatives);
                $arowid++;
            }
            $actions_rows = '<table>'.$actions_rows.'</table>';
        }
        return $actions_rows;
    }

}
?>
