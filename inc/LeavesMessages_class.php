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

        $this->leavemessage = $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix.'leaves_messages WHERE lmid='.intval($id)));
    }

    public function get_inreplyto() {
        if(empty($this->leavemessage['inReplyTo'])) {
            return false;
        }
        return new LeavesMessages($this->leavemessage['inReplyTo']);  /* Get the reply messaage id  of the current message object */
    }

    public function can_seemessage($check_user = '') {
        global $core;

        if(!isset($check_user)) {
            $check_user = $core->user['uid'];
        }

        if($this->leavemessage['uid'] == $check_user) {
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
            case 'public':
                return true;
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

    public function create_message(array $data = array(), $leaveid) {
        global $db, $core;

        if(!empty($data)) {
            $this->messagedata = $data;
        }
        else {
            $this->errorcode = 1;
            return false;
        }
        if(preg_match("/Message-ID: (.*)/", $this->messagedata['message'], $matches)) {
            preg_match("/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/", $matches[1], $messageid);
            $this->leavemessage_data['msgId'] = $messageid[1];
        }
        if(preg_match("/In-Reply-To: (.*)/", $this->messagedata['message'], $matches)) {
            preg_match("/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/", $matches[1], $replyto);
            $this->leavemessage_data['inReplyToMsgId'] = $replyto[1];
        }

        if(isset($this->leavemessage_data['inReplyToMsgId'])) {
            $this->leavemessage_data['inReplyTo'] = LeavesMessages::get_message_byattr('msgId', $this->leavemessage_data['inReplyToMsgId']);
        }
        $this->leavemessage_data['message'] = 'message body  ';

        $message_data = array('lid' => $leaveid,
                'uid' => $core->user['uid'],
                'msgId' => $this->leavemessage_data['msgId'],
                'inReplyTo' => $this->leavemessage_data['lmid'],
                'inReplyToMsgId' => $this->leavemessage_data['inReplyTo'],
                'message' => $this->leavemessage_data['message'],
                'viewPermission' => $this->messagedata['permission'],
                'createdOn' => TIME_NOW);

        $query = $db->insert_query('leaves_messages', $message_data);
        // $this->send_message();
        $this->errorcode = 0;
        return true;
    }

//    public function read_message() {
//        global $db;
//        $lastmessage = $db->fetch_field($db->query("SELECT message FROM ".Tprefix."leaves_messages ORDER BY lmid DESC"), 'message');
//
//        return $lastmessage;
//    }

    public function send_message(LeavesMessages $message) {
        global $core;
        //$this->leavemessage['thread'] = $this->read_message();

        /* We need to show the full conversation below */

        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();
        $mailer->set_from(array('name' => $core->user['displayName'], 'email' => $core->user['email']));
        $mailer->set_subject('Conversation message');
        $mailer->set_message($message->get()['message']);
        $mailer->set_to('tony.assaad@orkila.com');

        //$mailer->send();
    }

    public static function get_message_byattr($attr, $value) {
        global $db;

        if(!empty($value) && !empty($attr)) {
            $query = $db->query('SELECT lmid FROM '.Tprefix.'leaves_messages WHERE '.$db->escape_string($attr).'="'.$db->escape_string($value).'"');
            if($db->num_rows($query) > 1) {
                $messages = array();
                while($message = $db->fetch_assoc($query)) {
                    $messages[$message['lmid']] = new LeavesMessages($message['lmid']);
                }
                $db->free_result($query);
                return $messages;
            }
            else {
                if($db->num_rows($query) == 1) {
                    return new LeavesMessages($db->fetch_field($query, 'lmid'));
                }
                return false;
            }
        }
        return false;
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