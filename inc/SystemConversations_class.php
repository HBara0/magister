<?php
/* -------Definiton-START-------- */

class SystemConversations extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'scid';
    const TABLE_NAME = 'system_conversations';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = '';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = 'title';
    const REQUIRED_ATTRS = 'title';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $fields = array('title', 'module', 'tableName', 'recordId', 'alternativeId', 'lastReplyTime', 'lastReplyUid', 'scmtid', 'inputChecksum');
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(!is_null($data[$field])) {
                    $table_array[$field] = $data[$field];
                }
            }
        }
        $table_array['alias'] = generate_alias($table_array['title']);
        $table_array['createdBy'] = $core->user['uid'];
        $table_array['createdOn'] = TIME_NOW;
        $this->errorcode = 3;
        if($this->validate_requiredfields($table_array)) {
            if(is_array($table_array)) {
                $query = $db->insert_query(self::TABLE_NAME, $table_array);
                if($query) {
                    $this->errorcode = 0;
                    $this->data[self::PRIMARY_KEY] = $db->last_id();
                }
            }
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $core;
        $fields = array('title', 'module', 'tableName', 'recordId', 'alternativeId', 'lastReplyTime', 'lastReplyUid', 'scmtid', 'inputChecksum');
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(!is_null($data[$field])) {
                    $table_array[$field] = $data[$field];
                }
            }
        }
        $table_array['alias'] = generate_alias($table_array['title']);
        $table_array['modifiedBy'] = $core->user['uid'];
        $table_array['modifiedOn'] = TIME_NOW;

        if($this->validate_requiredfields($table_array)) {
            if(is_array($table_array)) {
                $db->update_query(self::TABLE_NAME, $table_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
                $this->errorcode = 0;
            }
        }
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    /**
     * responsible for main initiation of a conversation and send first message
     * @param type $data
     * @param type $message
     * @return \SystemConversations
     */
    public function initiate_conversation($data = array(), $message = array(), $participants = array()) {
        global $errorhandler, $lang;
        if(!is_array($message) || empty($message)) {
            $this->errorcode = 2;
            return $this;
        }
        $this->set($data);
        $result = $this->save();
        //if saved succesfully save recipients and send the message
        if($result->get_errorcode() == 0) {
            $message[$result::PRIMARY_KEY] = $result->data[$result::PRIMARY_KEY];
            //save recipients
            if(is_array($participants)) {
                foreach($participants as $participant) {
                    $conversationrecipient_obj = new SystemConvesationsParticipants();
                    $conversationrecipient_obj->set(array('uid' => $participant, 'scid' => $result->data[$result::PRIMARY_KEY]));
                    $conversationrecipient_obj = $conversationrecipient_obj->save();
                    if($conversationrecipient_obj->get_errorcode() != 0) {
                        $result->errorcode = $conversationrecipient_obj->get_errorcode();
                        break;
                    }
                }
            }
            else {
                $errorhandler->record('requiredfields', $lang->participants);
                $result->errorcode = 3;
            }
            if($result->get_errorcode() == 0) {
                //save and send message
                $conversationmessage_obj = new SystemConversationsMessages();
                $conversationmessage_obj = $conversationmessage_obj->create_message($message, $result->data[self::PRIMARY_KEY]);
                if($conversationmessage_obj->get_errorcode() != 0) {
                    $result->errorcode = $conversationmessage_obj->get_errorcode();
                }
            }
        }
        return $result;
    }

    /**
     * get all replies of convesation ordered by the creation date of the message
     * @global type $db
     * @param type $order
     * @return boolean or array
     */
    public function get_replies($order = 'ASC') {
        global $db;
        $replies = SystemConversationsMessages::get_data(array(self::PRIMARY_KEY => intval($this->data[self::PRIMARY_KEY])), array('simple' => false, 'order' => array('by' => 'createdOn', 'sort' => $order), 'returnarray' => true));
        if(is_array($replies)) {
            return $replies;
        }
        return false;
    }

    /**
     * function responsible for returning all recipients for the conversation
     * @global type $core
     * @return type
     */
    public function get_recipients() {
        global $core;
        $users = array();
        switch($this->data['type']) {
            default:
                $recipients = SystemConvesationsParticipants::get_data(array(self::PRIMARY_KEY => $this->data[self::PRIMARY_KEY]), array('returnarray' => true));
                if(is_array($recipients)) {
                    foreach($recipients as $recipient) {
                        $user_obj = new Users(intval($recipient->uid));
                        if(is_object($user_obj) && !empty($user_obj->email)) {
                            $users[$user_obj->uid] = $user_obj->email;
                        }
                    }
                }
                break;
//            case 'private':
//                $inreply_obj = $this->get_inreplyto();   /* Get the user whos in  the relplyTo this message */
//                if(is_object($inreply_obj)) {
//                    $users_receiver[$inreply_obj->get_user()->get()['uid']] = $inreply_obj->get_user()->get()['email'];
//                }
//                $createdbyid = $arorequest_obj->createdBy;
//                $createdby = new Users($createdbyid);
//                $users_receiver[$createdby->get()['uid']] = $createdby->email;
//                break;
//            case'limited':
//                $arorequest_obj = new AroRequests($this->data['aorid']);
//                $lastapproval = $arorequest_obj->get_lastnotified(); //get_lastapproval
//                $sender_approval_seq = 1;
//                if(is_object($lastapproval)) {
//                    $sender_approval_seq = $lastapproval->sequence;
//                }
//                $config = array('returnarray' => true, 'simple' => false, 'order' => array('by' => 'sequence', 'sort' => 'ASC'));
//                $approvals_objs = AroRequestsApprovals::get_data('aorid='.$this->data['aorid'].' AND sequence <='.intval($sender_approval_seq), $config);
//                if(is_array($approvals_objs)) {
//                    foreach($approvals_objs as $approvals_obj) {
//                        $user = new Users($approvals_obj->uid);
//                        $users_receiver[$approvals_obj->uid] = $user->email;
//                    }
//                }
//                $createdbyid = $arorequest_obj->createdBy;
//                $createdby = new Users($createdbyid);
//                $users_receiver[$createdby->get()['uid']] = $createdby->email;
//                break;
        }
        if(is_array($users)) {
            return $users;
        }
        return false;
    }

    /**
     * custome validation
     * @global type $errorhandler
     * @param type $data
     * @return boolean
     */
    protected function validate_requiredfields($data) {
        global $errorhandler;
        $required_fields = static::REQUIRED_ATTRS;
        if(!empty($required_fields)) {
            $required_fields = explode(',', $required_fields);
            if(is_array($required_fields) && is_array($data)) {
                foreach($required_fields as $field) {
                    if(!isset($data[$field]) || empty($data[$field])) {
                        $errorhandler->record('requiredfields', $field);
                        return false;
                    }
                }
            }
        }

        if(is_empty($data['tableName'], $data['recordId']) && empty($data['alternativeId'])) {
            return false;
        }
        return true;
    }

    /**
     *
     * main function to parse all conversation history
     * @global type $template
     * @global type $core
     * @param array $options
     * @return boolean
     */
    public function parse_messages(array $options = array()) {
        global $template, $core;
        $takeactionpage_conversation = null;

        $initialmsgs = SystemConversationsMessages::get_data(self::PRIMARY_KEY.'='.$this->data[self::PRIMARY_KEY].' AND replyTo=0', array('simple' => false, 'returnarray' => true));
        if(!is_array($initialmsgs)) {
            return false;
        }
        if(empty($options['uid'])) {
            $options['uid'] = $core->user['uid'];
        }

        foreach($initialmsgs as $initialmsg) {
            if(!is_object($initialmsg)) {
                continue;
            }
            //  Check if user is allowed to see the message /
//            if(!$initialmsg->can_seemessage($options['uid'])) {
//                continue;
//            }
            $takeactionpage_conversation .= $initialmsg->parse_single_message($options);

            $replies_objs = $initialmsg->get_replies();
            if(is_array($replies_objs)) {
                $takeactionpage_conversation .= $this->parse_replies($replies_objs, 1, $options);
            }
        }
        return $takeactionpage_conversation;
    }

    /**
     *
     * function responsible to parse all replies for current message
     * @global type $template
     * @global type $core
     * @param type $replies
     * @param type $depth
     * @param array $options
     * @return type
     */
    private function parse_replies($replies, $depth = 1, array $options = array()) {
        global $template, $core;

        if(is_array($replies)) {
            foreach($replies as $reply) {
//                if(!$reply->can_seemessage($options['uid'])) {
//                    continue;
//                }
                $takeactionpage_conversation.=$reply->parse_single_message($options);

                $reply_replies = $reply->get_replies();
                if(is_array($reply_replies)) {
                    $takeactionpage_conversation .= $this->parse_replies($reply_replies, $depth + 1, $options);
                }
            }
            return $takeactionpage_conversation;
        }
    }

    /**
     * responsibl for parsing the whole conversation
     * @param type $configs
     * @return type string
     */
    public function parse_conversation($configs = array()) {
        global $lang;
        $conv_url = $this->get_conversation_url(array('scid' => $this->data[self::PRIMARY_KEY]));
        $conversation_output = '<hr><h3>'.$this->get_displayname().' '.$lang->conversation.'</h3>';
        $conversation_output.=$this->parse_messages();
        $conversation_output .= '<hr><div><a target="_blank" href="'.$conv_url.'"><button type="button" class="btn btn-success">'.$lang->goto.' '.$lang->conversation.'</button></a></div>';
        return $conversation_output;
    }

}