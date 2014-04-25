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
    //put your code here

    private $errorcode = 0; //0=No errors;1=Subject missing;2=Entry exists;3=Error saving;4=validation violation
    private $leavemessage = array();

    public function __construct($id = '', $simple = true) {
        global $db;
        if(isset($id) && !empty($id)) {
            $this->leavemessage = $this->read($id, $simple);
        }
    }

    private function read($id, $simple = true) {
        global $db;

        if(empty($id)) {
            return false;
        }
        $query_select = '*';
        if($simple == true) {
            $query_select = 'lmid, lid,uid,inReplyto,message';
        }

        return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."leaves_messages WHERE lmid=".$db->escape_string($id)));
    }

    public function Get_Inreplyto() {
        return new LeavesMessages($this->leavemessage['inReplyto']);  /* Get the reply messaage id  of the current message object */
    }

    public function can_seemessage() {

        switch($this->leavemessage['viewPermission']) {
            case'private':
                $inreply_obj = $this->Get_Inreplyto();
                if(is_object($inreply_obj)) {  //requester
                    $users_permission['Inreplyto'] = $this->Get_Inreplyto()->get_user()[uid];
                }
                //print_r($users_permission);
                $users_permission['requester'] = $this->leavemessage['uid'];
                if($core->user[uid] == $users_permission['Inreplyto']) {
                    return true;
                }
                //requester and the person setting the message can it

                break;
            case'public':
                break;

            case 'limited':
                $leave_obj = new Leaves(array('lid' => $this->leavemessage['lid']));
                $approvers_objs = $leave_obj->get_approvers();
                foreach($approvers_objs as $approvers_user) {
                    $approvers = $approvers_user->get();
                }

                $query3 = $db->query("SELECT l.* FROM ".Tprefix."leavesapproval l
				WHERE l.isApproved='0' AND l.lid='{$this->leavemessage['lid']}' AND sequence >= (SELECT sequence FROM ".Tprefix."leavesapproval WHERE uid='{$approvers[uid]}' AND lid='{$this->leavemessage['lid']}' AND isApproved=1)
                                ORDER BY sequence ASC
                                LIMIT 0, 1");
                //starting user > =2 ( to sequence >= audery )


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

        $position = strpos($message, "\n");
        if($position != false) {
            $message = substr($message, 0, $position);
        }
        if($removecommand == true) {
            $message = str_replace($commands, '', $message);
        }
        $message = trim($message);

        return $message;
    }

//if permsiion private  get uid  of sendgin message
    public function create_message(array $data = array(), $leaveid) {
        global $db, $core;

        $this->can_seemessage();
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
            $this->leavemessage_data['inReplyTo'] = $db->fetch_field($db->query("SELECT  lmid  FROM ".Tprefix."leaves_messages WHERE msgId =".$db->escape_string($this->leavemessage_data['inReplyToMsgId'])." "), 'lmid');
        }
        $this->leavemessage_data['message'] = 'message body  ';
        // $this->leavemessage_data['persmission'] = 'limited';
        $message_data = array('lid' => $leaveid,
                'uid' => $core->user['uid'],
                'msgId' => $this->leavemessage_data['msgId'],
                'inReplyTo' => $this->leavemessage_data['lmid'],
                'inReplyToMsgId' => $this->leavemessage_data['inReplyTo'],
                'message' => $this->leavemessage_data['message'],
                'viewPermission' => $this->messagedata['permission'],
                'createdOn' => TIME_NOW);
        print_R($message_data);
        $db->insert_query('leaves_messages', $message_data);
        $this->send_message();
        $this->errorcode = 0;
        return true;
    }

    public function read_message() {
        global $db;
        $lastmessage = $db->fetch_field($db->query("SELECT message  FROM ".Tprefix."leaves_messages ORDER BY lmid DESC "), 'message');

        return $lastmessage;
    }

    public function send_message() {
        global $core;
        $this->leavemessage['thread'] = $this->read_message();
        $email_data['to'] = '';
        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();
        $mailer->set_from(array('name' => $core->user['displayName'], 'email' => $core->user['email']));
        // $mailer->set_subject($this->get_leave()->get());
        $mailer->set_subject('Conversation message');
        $mailer->set_message($this->leavemessage['thread']);
        $mailer->set_to('tony.assaad@orkila.com');
        //   print_r($mailer->debug_info());
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