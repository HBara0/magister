<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: AroRequestsMessages_class.php
 * Created:        @rasha.aboushakra    Apr 8, 2015 | 9:12:15 AM
 * Last Update:    @rasha.aboushakra    Apr 8, 2015 | 9:12:15 AM
 */

class AroRequestsMessages extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'armid';
    const TABLE_NAME = 'aro_requests_messages';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'armid,aorid,uid,msgId';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {

    }

    public function save(array $data = array()) {

    }

    protected function update(array $data) {

    }

    public function get_user() {
        return new Users($this->data['uid']);
    }

    public function get_replies() {
        global $db;
        $replies = self::get_data('inReplyTo='.$this->data[self::PRIMARY_KEY], array('simple' => false, 'returnarray' => true));
        if(is_array($replies)) {
            return $replies;
        }
        return false;
    }

    public function create_message(array $data, $aorid, array $config = array()) {
        global $db, $core;

        $valid_fields = array('uid', 'aorid', 'msgId', 'inReplyTo', 'inReplyToMsgId', 'message', 'viewPermission', 'createdOn');
        if(!empty($data)) {
            $this->data = $data;
        }
        else {
            $this->errorcode = 1;
            return false;
        }

        if(empty($this->data['message'])) {
            $this->errorcode = 2;
            return false;
        }
        if(value_exists('aro_requests_messages', 'message', $this->data['message'], ' uid='.$core->user['uid'].'')) { // Add date filter
            $this->errorcode = 3;
            return false;
        }
        if(preg_match("/Message-ID: (.*)/", $this->data['message'], $matches)) {
            preg_match("/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/", $matches[1], $messageid);
            $this->data['msgId'] = $messageid[1];
        }
        if(preg_match("/In-Reply-To: (.*)/", $this->data['message'], $matches)) {
            preg_match("/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/", $matches[1], $replyto);
            $this->data['inReplyToMsgId'] = $replyto[1];
        }

        if(isset($this->data['inReplyToMsgId'])) {
            $this->data['inReplyTo'] = self::get_message_byattr('msgId', $this->arorequestmessage_data['inReplyToMsgId'])->get()['lmid'];
        }

        if($config['source'] != 'emaillink') {
            $this->data['message'] = self::extract_message($data['message'], true);
        }
        $this->data['aorid'] = $aorid;
        $this->data['uid'] = $core->user['uid'];
        $this->data['createdOn'] = TIME_NOW;
        $this->data['viewPermission'] = 'public';
        foreach($this->data as $attr => $val) {
            if(!in_array($attr, $valid_fields)) {
                unset($this->data[$attr]);
            }
        }
        $query = $db->insert_query(self::TABLE_NAME, $this->data);
        if($query) {
            $this->data['armid'] = $db->last_id();
            $this->errorcode = 0;
            return true;
        }
    }

    public function send_message() {
        global $lang, $core;

        $lang->load('aro_meta');
        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();
        $mailer->set_from(array('name' => $core->user['displayName'], 'email' => $core->user['email']));

        $arorequest = AroRequests::get_data(array('aorid' => $this->data['aorid']), array('simple' => false));

        if(is_object($arorequest)) {
            $reply_links = DOMAIN.'/index.php?module=aro/managearodouments&action=takeactionpage&requestKey='.base64_encode($arorequest->get()['identifier']).'&inreplyTo='.$this->data['inReplyTo'].'&id='.base64_encode($arorequest->get()['aorid']);
            $view_link = DOMAIN."/index.php?module=aro/managearodouments&requestKey=".base64_encode($this->data['identifier'])."&id=".base64_encode($arorequest->get()['aorid'])."&referrer=toapprove";
        }
        $mailer->set_subject($lang->newrequestmsgsubject.' ['.$arorequest->orderReference.']');

        $emailreceivers = $this->get_emailreceivers();
        if(is_array($emailreceivers)) {
            foreach($emailreceivers as $uid => $emailreceiver) {
                $message = $lang->clicktoviewaro.' '.$view_link.'<br/>'.$this->data['message'].' | <a href="'.$reply_links.'">&#x21b6; '.$lang->reply.'</a><br/>';
                $message .= '<h1>'.$lang->conversation.'</h1>'.$arorequest->parse_messages(array('viewmode' => 'textonly', 'uid' => $uid));
                if(!empty($message)) {
                    $mailer->set_message($message);
                    $mailer->set_to($emailreceiver);
                    $mailer->send();
                }
                $message = '';
            }
        }

        $this->errorcode = 5;
        if($mailer->get_status() == true) {
            $this->errorcode = 0;
        }
        return $this;
    }

    public function can_seemessage($check_user = '') {
        global $core;

        if(empty($check_user)) {
            $check_user = $core->user['uid'];
        }
        if($this->data['uid'] == $check_user) {
            return true;
        }

        if($this->data['viewPermission'] == 'public') {
            return true;
        }

        switch($this->data['viewPermission']) {
            case'private':
                if($this->data['inReplyTo'] == 0 && $check_user == $this->data['uid']) {// check here
                    return true;
                }
                $inreply_obj = $this->get_inreplyto();
                if(is_object($inreply_obj)) {
                    $users_permission['inreplyto'] = $inreply_obj->get_user()->get()['uid'];
                }
                else {
                    return false;
                }

                if(in_array($check_user, array($users_permission['inreplyto'], $this->data['uid']))) {
                    return true;
                }
                return false;
                break;
            case 'limited':
                $aro_request_obj = new AroRequests($this->data['aorid']);
                $sender_approval_seq = $aro_request_obj->get_approval_byappover($this->data['uid'])->get()['sequence'];
                $user_approval_seq = $aro_request_obj->get_approval_byappover($check_user)->get()['sequence'];

                if($sender_approval_seq <= $user_approval_seq) {
                    return true;
                }
                return false;
                break;
        }
    }

    public function get_inreplyto() {
        if(empty($this->data['inReplyTo'])) {
            return false;
        }
        return new AroRequestsMessages($this->data['inReplyTo']);  /* Get the reply messaage id  of the current message object */
    }

    private function get_emailreceivers() {
        global $core;
        switch($this->data['viewPermission']) {
            case 'public':
                $arorequest_obj = new AroRequests($this->data['aorid']);
                $sender_approval_seq = $arorequest_obj->get_approval_byappover($this->data['uid'])->get()['sequence'];
                //$approvals_objs = $arorequest_obj->get_approvers();
                $config = array('returnarray' => true, 'simple' => false, 'order' => array('by' => 'sequence', 'sort' => 'ASC'));
                $approvals_objs = AroRequestsApprovals::get_data(array('aorid='.$this->data['aorid'].' AND sequence >='.intval($sender_approval_seq)), $config);
                if(is_array($approvals_objs)) {
                    foreach($approvals_objs as $approvals_obj) {
                        $user = new Users($approvals_obj->uid);
                        $users_receiver[$approvals_obj->uid] = $user->email;
                    }
                }
                else {
                    if(is_object(approvals_objs)) {
                        $user = new Users($approvals_objs->uid);
                        $users_receiver[$approvals_objs->uid] = $user->email;
                    }
                }
                $createdbyid = $arorequest_obj->createdBy;
                $createdby = new Users($createdbyid);
                $users_receiver[$createdby->get()['uid']] = $createdby->email;
                break;
            case 'private':
                $inreply_obj = $this->get_inreplyto();   /* Get the user whos in  the relplyTo this message */
                if(is_object($inreply_obj)) {
                    $users_receiver[$inreply_obj->get_user()->get()['uid']] = $inreply_obj->get_user()->get()['email'];
                }
                $createdbyid = $arorequest_obj->createdBy;
                $createdby = new Users($createdbyid);
                $users_receiver[$createdby->get()['uid']] = $createdby->email;
                break;
            case'limited':

                $arorequest_obj = new AroRequests($this->data['aorid']);
                $sender_approval_seq = $arorequest_obj->get_approval_byappover($this->data['uid'])->get()['sequence'];

                $sender_approvals_objs = AroRequestsApprovals::get_data('aorid='.$this->data['aorid'].' AND sequence >='.intval($sender_approval_seq));
                if(is_array($sender_approvals_objs)) {
                    foreach($sender_approvals_objs as $sender_approvals_obj) {
                        $user = new Users($sender_approvals_obj->uid);
                        $users_receiver[$user->uid] = $user->email;
                    }
                }
                break;
        }
        unset($users_receiver[$core->user['uid']]);   /* avoid send  threads  to the user who is setting the message thread */

        return $users_receiver;
    }

}