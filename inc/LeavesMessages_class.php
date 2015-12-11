<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of LeavesMessages_class
 *
 * @author tony.assaad
 */
class LeavesMessages {
    private $errorcode = 0;
    private $leavemessage = array();
    private $leave = null;

    const PRIMARY_KEY = 'lmid';
    const TABLE_NAME = 'leaves_messages';

    public function __construct($id = '', $simple = true) {
        if(isset($id) && !empty($id)) {
            $this->read($id, $simple);
        }
    }

    private function read($id, $simple = true) {
        global $db;

        if(empty($id)) {
            return false;
        }

        $query_select = '*';
        if($simple == true) {
            $query_select = 'lmid, lid, uid, inReplyto, message, viewPermission';
        }
        $this->leavemessage = $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function get_inreplyto() {
        if(empty($this->leavemessage['inReplyTo'])) {
            return false;
        }
        return new LeavesMessages($this->leavemessage['inReplyTo']);  /* Get the reply messaage id  of the current message object */
    }

    public function get_replies() {
        global $db;

        $replies = LeavesMessages::get_messages('inReplyTo='.$this->leavemessage['lmid'], array('simple', false, 'returnarray' => true));
        if(is_array($replies)) {
            return $replies;
        }
        return false;
    }

    public function can_seemessage($check_user = '') {
        global $core;

        if(empty($check_user)) {
            $check_user = $core->user['uid'];
        }
        if($this->leavemessage['uid'] == $check_user) {
            return true;
        }

        if($this->leavemessage['viewPermission'] == 'public') {
            return true;
        }

        switch($this->leavemessage['viewPermission']) {
            case'private':
                if($this->leavemessage['inReplyTo'] == 0 && $check_user == $this->get_leave()->get_requester()->get()['uid']) {
                    return true;
                }
                $inreply_obj = $this->get_inreplyto();
                if(is_object($inreply_obj)) {
                    $users_permission['inreplyto'] = $inreply_obj->get_user()->get()['uid'];
                }
                else {
                    return false;
                }

                if(in_array($check_user, array($users_permission['inreplyto'], $this->leavemessage['uid']))) {
                    return true;
                }
                return false;
                break;
            case 'limited':
                $leave_obj = new Leaves($this->leavemessage['lid']);
                $sender_approval_seq = $leave_obj->get_approval_byappover($this->leavemessage['uid'])->get()['sequence'];
                $user_approval_seq = $leave_obj->get_approval_byappover($check_user)->get()['sequence'];

                if($sender_approval_seq <= $user_approval_seq) {
                    return true;
                }
                return false;
                break;
        }
    }

    public static function extract_message($message, $removecommand = false) {
        $commands = array('#approve', '#revoke', '#public', '#message', '#private', '#limited');
        foreach($commands as $command) {
            $position = strpos($message, $command);
            if($position != false) {
                break;
            }
        }
        if($position == false) {
            return $message;
        }

        $message = substr($message, $position);

        $message = str_replace(array("\r\n\r\n", "\n\r\n\r", "\r\\n\r\\n", "\\n\r\\n\r"), '------@@NEWSECTION@@------', $message);
        $position = strpos($message, '------@@NEWSECTION@@------');

        if($position != false) {
            $message = substr($message, 0, $position);
        }
        if($removecommand == true) {
            $message = str_replace($commands, '', $message);
        }
        $message = trim($message);

        return $message;
    }

    public function create_message(array $data, $lid, array $config = array()) {
        global $db, $core;

        $valid_fields = array('uid', 'lid', 'msgId', 'inReplyTo', 'inReplyToMsgId', 'message', 'viewPermission', 'createdOn');
        if(!empty($data)) {
            $this->leavemessage = $data;
        }
        else {
            $this->errorcode = 1;
            return false;
        }

        if(empty($this->leavemessage['message'])) {
            $this->errorcode = 2;
            return false;
        }
        if(value_exists('leaves_messages', 'message', $this->leavemessage['message'], ' uid='.$core->user['uid'].'')) { // Add date filter
            $this->errorcode = 3;
            return false;
        }
        if(preg_match("/Message-ID: (.*)/", $this->leavemessage['message'], $matches)) {
            preg_match("/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/", $matches[1], $messageid);
            $this->leavemessage['msgId'] = $messageid[1];
        }
        if(preg_match("/In-Reply-To: (.*)/", $this->leavemessage['message'], $matches)) {
            preg_match("/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/", $matches[1], $replyto);
            $this->leavemessage['inReplyToMsgId'] = $replyto[1];
        }

        if(isset($this->leavemessage['inReplyToMsgId'])) {
            $this->leavemessage['inReplyTo'] = self::get_message_byattr('msgId', $this->leavemessage_data['inReplyToMsgId'])->get()['lmid'];
        }

        if($config['source'] != 'emaillink') {
            $this->leavemessage['message'] = self::extract_message($data['message'], true);
        }


        $this->leavemessage['lid'] = $lid;
        $this->leavemessage['uid'] = $core->user['uid'];
        $this->leavemessage['createdOn'] = TIME_NOW;
        $this->leavemessage['viewPermission'] = 'public'; //Temporary overwrite as per management request
        foreach($this->leavemessage as $attr => $val) {
            if(!in_array($attr, $valid_fields)) {
                unset($this->leavemessage[$attr]);
            }
        }

        $query = $db->insert_query(self::TABLE_NAME, $this->leavemessage);
        $this->leavemessage['lmid'] = $db->last_id();
        $this->errorcode = 0;
        return true;
    }

//    public function read_message() {
//        global $db;
//        $lastmessage = $db->fetch_field($db->query("SELECT message FROM ".Tprefix."leaves_messages ORDER BY lmid DESC"), 'message');
//
//        return $lastmessage;
//    }

    public function send_message() {
        global $lang, $core;

        $lang->load('attendance_messages');
        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();
        $mailer->set_from(array('name' => $core->user['displayName'], 'email' => $core->user['email']));

        $leave = $this->get_leave(false);
        $leavetype = $leave->get_type(false);

        $leave->details_crumb = parse_additionaldata($leave->get(), $leavetype->additionalFields);

        if(is_array($leave->details_crumb) && !empty($leave->details_crumb)) {
            $leave->details_crumb = ' - '.implode(' ', $leave->details_crumb);
        }

        $leave_details['fromDate'] = date($core->settings['dateformat'], $leave->get()['fromDate']);
        $leave_details['toDate'] = date($core->settings['dateformat'], $leave->get()['toDate']);

        $leave_details['requester'] = $leave->get_requester()->get()['displayName'];
        $reply_links = DOMAIN.'/index.php?module=attendance/listleaves&action=takeactionpage&requestKey='.base64_encode($leave->requestKey).'&inreplyTo='.$this->leavemessage['inReplyTo'].'&id='.base64_encode($leave->get()['lid']);

        $leave->reason .= $leave->parse_expenses();
        $approvals = $leave->parse_approvalsapprovers();

        $mailer->set_subject($lang->newleavemsgsubject.' ['.$leave->requestKey.']');
        $leave_details = $lang->sprint($lang->requestleavemessagesupervisor, $leave_details['requester'], strtolower($leavetype->title).$leave->details_crumb, date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave->fromDate), date($core->settings['dateformat'].' '.$core->settings['timeformat'], $leave->toDate), $leave->reason, $approvals, $reply_links);

        $emailreceivers = $this->get_emailreceivers();
        foreach($emailreceivers as $uid => $emailreceiver) {
            $message = '<p>'.$this->leavemessage['message'].' | <a href="'.$reply_links.'">&#x21b6; '.$lang->reply.'</a></p>';
            $message .= '<h1>'.$lang->conversation.'</h1>'.$leave->parse_messages(array('viewmode' => 'textonly', 'uid' => $uid));
            $message .= '<div><br />'.$leave_details.'</div>';
            if(!empty($message)) {
                $mailer->set_message($message);
                $mailer->set_to($emailreceiver);
                $mailer->send();
            }
            $message = '';
        }

        $this->errorcode = 5;
        if($mailer->get_status() == true) {
            $this->errorcode = 0;
        }
    }

    private function get_emailreceivers() {
        global $core;
        switch($this->leavemessage['viewPermission']) {
            case 'public':
                $leave_obj = new Leaves($this->leavemessage['lid']);
                $sender_approvals_objs = $leave_obj->get_toapprove();
                if(is_array($sender_approvals_objs)) {
                    foreach($sender_approvals_objs as $sender_approvals_obj) {
                        $users_receiver[$sender_approvals_obj->get_user()->get()['uid']] = $sender_approvals_obj->get_user()->get()['email'];
                    }
                }
                else {
                    if(is_object($sender_approvals_objs)) {
                        $users_receiver[$sender_approvals_objs->get_user()->get()['uid']] = $sender_approvals_objs->get_user()->get()['email'];
                    }
                }
                $requester = $this->get_leave()->get_requester();
                $users_receiver[$requester->get()['uid']] = $requester->get()['email'];
                break;
            case 'private':
                $inreply_obj = $this->get_inreplyto();   /* Get the user whos in  the relplyTo this message */
                if(is_object($inreply_obj)) {
                    $users_receiver[$inreply_obj->get_user()->get()['uid']] = $inreply_obj->get_user()->get()['email'];
                }
                $requester = $this->get_leave()->get_requester();
                $users_receiver[$requester->get()['uid']] = $requester->get()['email'];
                break;
            case'limited':
                /* users in the approval chain from the person putting the message and higher
                 * Get the leave object and then get their  approvers chain
                 * foreach of the approvers get their user related email
                 *   */

                $leave_obj = new Leaves($this->leavemessage['lid']);
                $sender_approval_seq = $leave_obj->get_approval_byappover($this->leavemessage['uid'])->get()['sequence'];

                $sender_approvals_objs = AttLeavesApproval::get_approvals('lid='.$this->leavemessage['lid'].' AND sequence >='.intval($sender_approval_seq));
                if(is_array($sender_approvals_objs)) {
                    foreach($sender_approvals_objs as $sender_approvals_obj) {
                        $users_receiver[$sender_approvals_obj->get_user()->get()['uid']] = $sender_approvals_obj->get_user()->get()['email'];
                    }
                }
                break;
        }
        unset($users_receiver[$core->user['uid']]);   /* avoid send  threads  to the user who is setting the message thread */
        return $users_receiver;
    }

    public static function get_message_byattr($attr, $value, $simple = true) {
        global $db;

        if(!empty($value) && !empty($attr)) {
            $query = $db->query('SELECT '.self::PRIMARY_KEY.' FROM '.Tprefix.self::TABLE_NAME.' WHERE '.$db->escape_string($attr).'="'.$db->escape_string($value).'"');
            if($db->num_rows($query) > 1) {
                $messages = array();
                while($message = $db->fetch_assoc($query)) {
                    $messages[$message[self::PRIMARY_KEY]] = new self($message[self::PRIMARY_KEY], $simple);
                }
                $db->free_result($query);
                return $messages;
            }
            else {
                if($db->num_rows($query) == 1) {
                    return new self($db->fetch_field($query, self::PRIMARY_KEY), $simple);
                }
                return false;
            }
        } return false;
    }

    public static function get_messages($filters = '', $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_leave($simple = true) {
        return new Leaves($this->leavemessage['lid'], $simple);
    }

    public function get_user() {
        return new Users($this->leavemessage['uid']);
    }

    public function get_errorcode() {
        return $this->errorcode;
    }

    public function get() {
        return $this->leavemessage;
    }

}