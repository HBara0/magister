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
        $replies = self::get_data('inReplyToMsgId='.$this->data[self::PRIMARY_KEY], array('simple' => false, 'returnarray' => true));
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
            return $this;
        }

        if(empty($this->data['message'])) {
            $this->errorcode = 2;
            return $this;
        }
        if(value_exists('aro_requests_messages', 'message', $this->data['message'], ' uid='.$core->user['uid'].'')) { // Add date filter
            $this->errorcode = 3;
            return $this;
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
            $inreplyto_obj = self::get_data(array('armid' => $this->data['inReplyToMsgId']), array('simple' => false));
            if(is_object($inreplyto_obj)) {
                $this->data['inReplyToMsgId'] = $inreplyto_obj->get()['armid'];
                $this->data['inReplyTo'] = $inreplyto_obj->get()['uid'];
            }
        }

        if($config['source'] != 'emaillink') {
            $this->data['message'] = self::extract_message($data['message'], true);
        }
        $this->data['aorid'] = $aorid;
        $this->data['uid'] = $core->user['uid'];
        $this->data['createdOn'] = TIME_NOW;
        $this->data['viewPermission'] = 'limited';
        if($data['viewPermission'] == 1 || (is_object($inreplyto_obj) && $inreplyto_obj->viewPermission == 'public')) {
            $this->data['viewPermission'] = 'public';
        }
        foreach($this->data as $attr => $val) {
            if(!in_array($attr, $valid_fields)) {
                unset($this->data[$attr]);
            }
        }
        $query = $db->insert_query(self::TABLE_NAME, $this->data);
        if($query) {
            $this->data['armid'] = $db->last_id();
            $this->errorcode = 0;
            return $this;
        }
    }

    public function send_message($options = array()) {
        global $lang, $core;
        if(empty($this->data['aorid'])) {
            $this->data['aorid'] = $core->input['aorid'];
        }
        $lang->load('aro_meta');
        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();
        $mailer->set_from(array('name' => $core->user['displayName'], 'email' => $core->settings['maileremail']));

        $arorequest = AroRequests::get_data(array('aorid' => $this->data['aorid']), array('simple' => false));

        if(is_object($arorequest)) {
            // $reply_links = DOMAIN.'/index.php?module=aro/managearodouments&action=takeactionpage&requestKey='.base64_encode($arorequest->get()['identifier']).'&inreplyTo='.$this->data['inReplyTo'].'&id='.base64_encode($arorequest->get()['aorid']);
            $reply_links = DOMAIN."/index.php?module=aro/managearodouments&referrer=toapprove&id=".$arorequest->get()['aorid'].'#message';
            $view_link = DOMAIN."/index.php?module=aro/managearodouments&referrer=toapprove&id=".$arorequest->get()['aorid'];
        }
        if($options['msgtype'] == 'rejection') {
            $aroaffiliate_obj = Affiliates::get_affiliates(array('affid' => $arorequest->affid));
            $purchasteype_obj = PurchaseTypes::get_data(array('ptid' => $arorequest->orderType));
            $mailer->set_subject('REJECTED Aro Request ['.$arorequest->orderReference.']/'.$aroaffiliate_obj->get_displayname().'/'.$purchasteype_obj->get_displayname());
        }
        else {
            $mailer->set_subject($lang->newrequestmsgsubject.' ['.$arorequest->orderReference.'] ['.$arorequest->inputChecksum.']');
        }

        $emailreceivers = $this->get_emailreceivers();
        if(is_array($emailreceivers)) {
            foreach($emailreceivers as $uid => $emailreceiver) {
                $message = 'Time elapsed since finalization '.$arorequest->get_timelapsed().'<br/>';
                $message .= '<h1>'.$lang->conversation.'</h1>'.$arorequest->parse_messages(array('viewmode' => 'textonly', 'uid' => $uid));
                if($options['msgtype'] == 'rejection') {
                    $rejectedby = Users::get_data(array('uid' => $options['rejectedBy']));
                    $message = 'Aro Request ['.$arorequest->orderReference.']/'.$aroaffiliate_obj->get_displayname().'/'.$purchasteype_obj->get_displayname().' was rejected by '.$rejectedby.
                            '<br/>'.$message;
                }
                //message will be already parsed do removing the duplication of this message
//                else {
//                    $message .= '<a href="'.$view_link.'">'.$lang->clicktoviewaro.'</a><br/>'.$this->data['message'].' | <a href="'.$reply_links.'">&#x21b6; '.$lang->reply.'</a><br/>';
//                }
                if(!empty($message)) {
                    $emailformatter = new EmailFormatting();
                    $emailformatter->set_message(array('title' => $lang->aroconversation, 'message' => $message));
                    $message = $emailformatter->get_message();
                    $mailer->set_message('__ARO NOTIFICATION__<br/>'.$message);
                    $mailer->set_to($emailreceiver);
//                    $x = $mailer->debug_info();
//                    print_R($x);
//                    exit;
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
                return true;

//                $aro_request_obj = new AroRequests($this->data['aorid']);
//                $sender_approval_seq = $aro_request_obj->get_approval_byappover($this->data['uid'])->get()['sequence'];
//                $user_approval_seq = $aro_request_obj->get_approval_byappover($check_user)->get()['sequence'];
//                if($sender_approval_seq >= $user_approval_seq) {
//                    //  if($sender_approval_seq <= $user_approval_seq) {
//                    return true;
//                }
//                return false;
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
                $config = array('returnarray' => true, 'simple' => false, 'order' => array('by' => 'sequence', 'sort' => 'ASC'));
                $approvals_objs = AroRequestsApprovals::get_data('aorid='.$this->data['aorid'], $config);
                if(is_array($approvals_objs)) {
                    foreach($approvals_objs as $approvals_obj) {
                        $user = new Users($approvals_obj->uid);
                        $users_receiver[$approvals_obj->uid] = $user->email;
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
                $lastapproval = $arorequest_obj->get_lastnotified(); //get_lastapproval
                $sender_approval_seq = 1;
                if(is_object($lastapproval)) {
                    $sender_approval_seq = $lastapproval->sequence;
                }
                $config = array('returnarray' => true, 'simple' => false, 'order' => array('by' => 'sequence', 'sort' => 'ASC'));
                $approvals_objs = AroRequestsApprovals::get_data('aorid='.$this->data['aorid'].' AND sequence <='.intval($sender_approval_seq), $config);
                if(is_array($approvals_objs)) {
                    foreach($approvals_objs as $approvals_obj) {
                        $user = new Users($approvals_obj->uid);
                        $users_receiver[$approvals_obj->uid] = $user->email;
                    }
                }
                $createdbyid = $arorequest_obj->createdBy;
                $createdby = new Users($createdbyid);
                $users_receiver[$createdby->get()['uid']] = $createdby->email;
                break;
        }
        unset($users_receiver[$core->user['uid']]);   /* avoid send  threads  to the user who is setting the message thread */

        return $users_receiver;
    }

}