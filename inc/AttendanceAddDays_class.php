<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Additional Days Class
 * $id: AttendanceAddDays_class.php
 * Created:        @tony.assaad    Apr 23, 2013 | 2:18:23 PM
 * Last Update:    @tony.assaad    Apr 24, 2013 | 2:18:23 PM
 */

/**
 * Description of AttendanceAddDays_class
 *
 * @author tony.assaad
 */
class AttendanceAddDays Extends Attendance {
    private $status = 0; //0=No errors;1=Subject missing;2=Entry exists;3=Error saving;4=validation violation
    private $additionaldays = array();

    const PRIMARY_KEY = 'adid';
    const TABLE_NAME = 'attendance_additionalleaves';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($attedadddays_data = array()) {
        if(!empty($attedadddays_data['adid']) && isset($attedadddays_data['adid'])) {
            $this->read($attedadddays_data['adid'], '');
            return true;
        }
        elseif(!empty($attedadddays_data['identifier']) && isset($attedadddays_data['identifier'])) {
            $this->read('', $attedadddays_data['identifier']);
            return true;
        }
        else {
            if(is_numeric($attedadddays_data)) {
                $this->read($attedadddays_data, '');
                return true;
            }
        }
    }

    public function approve($fromemail) {
        global $db;
        $id = intval($id);
        if($this->can_apporve($fromemail)) {
            $db->update_query('attendance_additionalleaves', array('isApproved' => 1, 'approvedOn' => TIME_NOW), 'identifier="'.$this->additionaldays['identifier'].'" AND isApproved="0"');

            return true;
        }
        else {
            return false;
        }
    }

    public function approve_user($uid) {
        global $db;
        $uid = intval($uid);
        if($this->can_approve_user($uid)) {
            $db->update_query('attendance_additionalleaves', array('isApproved' => 1, 'approvedOn' => TIME_NOW), 'identifier="'.$this->additionaldays['identifier'].'" AND isApproved="0"');

            return true;
        }
        else {
            return false;
        }
    }

    public function request($uid, $data = array()) {
        global $db, $core, $log;

        unset($data['module'], $data['action'], $data['uid']);
        $this->data = $data;

        if(is_empty($this->data['date'], $this->data['numDays'], $this->data['remark'])) {
            $this->status = 1;
            return false;
        }

        $this->data['date'] = strtotime($this->data['date']);
        if($this->data['date'] == false || $this->data['date'] == -1) {
            output_xml("<status>false</status><message>{$lang->invalidtodate}</message>");
            exit;
        }

        $additional_leavesdata = array(
                'identifier' => $identifier = substr(md5(uniqid(microtime())), 1, 10),
                'numDays' => $core->sanitize_inputs($this->data['numDays']),
                'date' => $core->sanitize_inputs($this->data['date']),
                'addedBy' => $core->user['uid'],
                'isApproved' => $this->data['isApproved'],
                'remark' => $core->sanitize_inputs($this->data['remark']),
                'correspondToDate' => intval($this->data['correspondToDate']),
                'uid' => $uid,
                'requestedOn' => TIME_NOW
        );

        if(is_array($additional_leavesdata)) {
            if(!$this->check_existingrequest($uid, $additional_leavesdata['date'])) { /* check if users have exisintg additionalleaves in the same date they are requesting */
                $query = $db->insert_query('attendance_additionalleaves', $additional_leavesdata);
                if($query) {
                    $aadid = $db->last_id();
                    $this->status = 0;
                    $log->record($aadid);
                    return $aadid;
                }
            }
            else {
                $this->status = 2;
                return false;
            }
        }
    }

    public function check_existingrequest($uid, $date) {
        global $db;
        $query = $db->query("SELECT uid FROM ".Tprefix."attendance_additionalleaves WHERE uid='".intval($uid)."' AND date='".$db->escape_string($date)."'");
        if($db->num_rows($query) > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    public function can_apporve($reporttofromemail) {
        /* if  from email= email of reportto to this user */
        $user = new Users($this->additionaldays['uid']);
        $reporttsto = $user->get_reportsto()->get();
        if($reporttsto['email'] == $reporttofromemail) {
            return true;
        }
        else {
            return false;
        }
    }

    public function can_approve_user($uid) {
        $user = new Users($this->additionaldays['uid']);
        $reporttsto_obj = $user->get_reportsto();
        if(is_object($reporttsto_obj)) {
            $reporttsto = $reporttsto_obj->get();
        }
        if($reporttsto['uid'] == $uid) {
            return true;
        }
        else {
            return false;
        }
    }

    public function notify_request() {
        global $log, $core, $lang;

        if(isset($this->additionaldays['identifier'])) {
            $user = new Users($this->additionaldays['uid']);
            $requester = $user->get();
            $reportsto = $user->get_reportsto()->get();

            if(is_array($reportsto)) {
                $this->additionaldays['date_output'] = date($core->settings['dateformat'], $this->additionaldays['date']);

                $email_data = array(
                        'from_email' => 'approve_requestadddays@ocos.orkila.com',
                        'from' => 'Orkila Attendance System',
                        'to' => $reportsto['email'],
                        'subject' => $lang->sprint($lang->adddaysnotificationsubject, $requester['displayName'], $this->additionaldays['identifier']),
                        'message' => $lang->sprint($lang->adddaysrequestapproval, $requester['displayName'], $this->additionaldays['numDays'], $this->additionaldays['date_output'], $this->additionaldays['remark'], '<a  style="font: bold 11px Arial;
    text-decoration: none;
    background-color: #EEEEEE;
    color: #333333;
    padding: 2px 6px 2px 6px;
    border-top: 1px solid #CCCCCC;
    border-right: 1px solid #333333;
    border-bottom: 1px solid #333333;
    border-left: 1px solid #CCCCCC;" href="'.$core->settings['rootdir'].'ocos/index.php?module=attendance/listaddleavedays" target="_blank">Approve</a></br>'.$lang->replytoemailtoaprove)
                );

                $mail = new Mailer($email_data, 'php');
                if($mail->get_status() === true) {
                    $log->record('notifysupervisors', $reportsto);
                }
            }
        }
    }

    public function notifyapprove() {
        global $lang, $log;
        if($this->additionaldays['isApproved'] == 1) {
            $user = new Users($this->additionaldays['uid']);
            $requester_details = $user->get();
            $lang->adddaysapprovedmessage = $lang->sprint($lang->adddaysapprovedmessage, $requester_details['displayName'], $this->additionaldays['numDays']);
            $email_data = array(
                    'from_email' => 'attendance@ocos.orkila.com',
                    'from' => 'Orkila Attendance System',
                    'to' => $requester_details['email'],
                    'subject' => $lang->additionadaysapprovedsubject,
                    'message' => $lang->adddaysapprovedmessage
            );
            $mail = new Mailer($email_data, 'php');
            if($mail->get_status() === true) {
                $log->record('notifyrequester', $user->get_reportsto()->get()['uid']);
            }
        }
    }

    public function update_leavestats() {
        global $db, $log;

        if($this->additionaldays['correspondToDate'] == 1) {
            $period = $this->additionaldays['date'];
        }
        else {
            $period = TIME_NOW;
        }

        $leavestats_query = $db->query("SELECT lsid, additionalDays
                                        FROM ".Tprefix."leavesstats
                                        WHERE uid={$this->additionaldays['uid']} AND ltid=1 AND ({$period} BETWEEN periodStart AND periodEnd)");
        if($db->num_rows($leavestats_query) > 0) {
            while($leavestat = $db->fetch_array($leavestats_query)) {
                $additionalDays = $leavestat['additionalDays'];
                $lsid = $leavestat['lsid'];
            }
            $additionalDays += $this->additionaldays['numDays'];

            $db->update_query('leavesstats', array('additionalDays' => $additionalDays), "lsid={$lsid}");
            if($db->affected_rows() > 0) {
                $db->update_query(self::TABLE_NAME, array('isCounted' => 1), self::PRIMARY_KEY.'='.$this->additionaldays[self::PRIMARY_KEY]);
            }
            $log->record('updateleavebalance', $this->additionaldays['adid']);
        }
    }

    private function read($id = '', $identifier = '', $simple = true) {
        global $db;
        if(empty($id) && !empty($identifier)) {
            $where_statement = ' WHERE identifier="'.$db->escape_string($identifier).'"';
        }
        elseif(!empty($id)) {
            $where_statement = ' WHERE adid='.$db->escape_string($id);
        }
        $this->additionaldays = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."attendance_additionalleaves {$where_statement}"));

        if(is_array($this->additionaldays) && !empty($this->additionaldays)) {
            return true;
        }
        return false;
    }

    public static function get_data($filters = '', $configs = array()) {
        $data = new DataAccessLayer(self::CLASSNAME, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get() {
        return $this->additionaldays;
    }

    protected function get_affilisateduser() {
        $user_attendance = new Users($id);
        return $this->userattendance = $user_attendance->get();
    }

    public function get_status() {
        return $this->status;
    }

}
?>
