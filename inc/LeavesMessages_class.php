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
            $query_select = 'lmid, lid, uid, inReplyto, message';
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

        $replies = LeavesMessages::get_messages('inReplyTo='.$this->leavemessage['lmid'], false);
        if(is_array($replies)) {
            return $replies;
        }
        return false;
    }

    public function can_seemessage($check_user = '') {
        global $core;

        if(!isset($check_user)) {
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

                if($sender_approval_seq < $user_approval_seq) {
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

        if(empty($this->leavemessage['message'])) {
            $this->errorcode = 1;
            return false;
        }
        $this->leavemessage['lid'] = $lid;
        $this->leavemessage['uid'] = $core->user['uid'];
        $this->leavemessage['createdOn'] = TIME_NOW;

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

    public function send() {
        global $core;

        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();
        $mailer->set_from(array('name' => $core->user['displayName'], 'email' => $core->user['email']));

        /* SET TO LAND VAR */
        $mailer->set_subject('New message to leave request.'.$this->get_leave()->get()['requestKey']);

        /* ATTENTION
         * SHOW LEAVE DETAILS TWO SPACES AFTER THE REPLY MESSAGE
         * SHOW THE FULL CONVERSATION ALONG TO THE DETAILS
         * NEED TO BE DEVELOPED
         */
        $mailer->set_message($this->leavemessage['message']);

        /* NEED TO SET PROPER TO DEPENDING ON PERMISSION */
        $mailer->set_to('tony.assaad@orkila.com');
        $mailer->send();
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
        }
        return false;
    }

    public static function get_messages($filters = '', $simple = true) {
        global $db;

        $items = array();

        if(!empty($filters)) {
            $filters = ' WHERE '.$db->escape_string($filters);
        }
        $query = $db->query('SELECT '.self::PRIMARY_KEY.' FROM '.Tprefix.self::TABLE_NAME.$filters);
        while($item = $db->fetch_assoc($query)) {
            $items[$item[self::PRIMARY_KEY]] = new self($item[self::PRIMARY_KEY], $simple);
        }
        $db->free_result($query);
        return $items;
    }

    public function get_leave() {
        return new Leaves($this->leavemessage['lid']);
    }

    public function get_user() {
        return new Users($this->leavemessage['uid']);
    }

    public function get() {
        return $this->leavemessage;
    }

}