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

    public function __construct($leavemessage = array(), $simple = true) {
        global $db;
        if(!is_array($leavemessage) && !empty($leavemessage)) {
            $this->leavemessage = $this->read($leavemessage, $simple);
        }
        else {
            if(isset($leavemessage['lmid']) && !empty($leavemessage['lmid'])) {
                $this->leavemessage = $this->read($leavemessage['lmid'], $simple);
            }
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

    public function get_viewpermissions() {

        switch($this->leavemessage['viewPermission']) {
            case'private':
                $inreply_obj = $this->Get_Inreplyto();
                if(is_object($inreply_obj)) {  //requester 
                    $users_permission['Inreplyto'] = $this->Get_Inreplyto()->get_user()[uid];
                }
                print_r($users_permission);
                $users_permission['requester'] = $this->leavemessage['uid'];
                if($core->user[uid] == $this->leavemessage['uid']) {
                    
                }
                //requester and the person setting the message can it

                break;
            case'public':
                break;
            case 'limited':
                break;
        }
    }

//if permsiion private  get uid  of sendgin mesage 
    public function create_message($data = array()) {
        global $db;
        print_R($data);
        //read emai header 
//		$message_data = array('lid' => $leaveid,
//		'uid' =>,
//		'inRepyTo' =>,
//		'inReplyToMsgId'=>
//		'message' =>,
//		'viewPermission' =>,
//		'createdOn' => TIME_NOW);

        echo 'create messaage';
        //$db->insert_query($table, $message_data, $options);
    }

    public function read_message() {
        return $this->leavemessage['message'];
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