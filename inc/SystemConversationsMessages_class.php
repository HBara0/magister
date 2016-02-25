<?php
/* -------Definiton-START-------- */

class SystemConversationsMessages extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'scmid';
    const TABLE_NAME = 'system_conversations_messages';
    const SIMPLEQ_ATTRS = '*';
    const UNIQUE_ATTRS = '';
    const CLASSNAME = __CLASS__;
    const DISPLAY_NAME = '';
    const REQUIRED_ATTRS = 'scid,message,uid';

    /* -------Definiton-END-------- */
    /* -------FUNCTIONS-START-------- */
    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function create(array $data) {
        global $db, $core;
        $fields = array('scid', 'message', 'uid', 'replyTo', 'replyToEmail', 'type', 'inputChecksum');
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(!is_null($data[$field])) {
                    $table_array[$field] = $data[$field];
                }
            }
        }
        $table_array['createdOn'] = TIME_NOW;
        $table_array['createdBy'] = $core->user['uid'];
        if($this->validate_requiredfields($table_array)) {
            if(is_array($table_array)) {
                $query = $db->insert_query(self::TABLE_NAME, $table_array);
                if($query) {
                    $this->errorcode = 0;
                    $this->data[self::PRIMARY_KEY] = $db->last_id();
                    //update last reply time and uid for conversation object
                    $conversation_obj = $this->get_conversation();
                    $conversation = $conversation_obj->get();
                    $conversation['lastReplyTime'] = $this->data['createdOn'];
                    $conversation['lastReplyUid'] = $this->data['uid'];
                    $conversation_obj->set($conversation);
                    $conversation_obj->save();
                }
            }
        }
        else {
            $this->errorcode = 3;
        }
        return $this;
    }

    protected function update(array $data) {
        global $db, $core;
        $fields = array('scid', 'message', 'uid', 'replyTo', 'replyToEmail', 'type', 'inputChecksum');
        if(is_array($fields)) {
            foreach($fields as $field) {
                if(!is_null($data[$field])) {
                    $table_array[$field] = $data[$field];
                }
            }
        }
        $table_array['modifiedOn'] = TIME_NOW;
        $table_array['modifiedBy'] = $core->user['uid'];
        if($this->validate_requiredfields($table_array)) {
            if(is_array($table_array)) {
                $db->update_query(self::TABLE_NAME, $table_array, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
                $this->errorcode = 0;
                //update last reply time and uid for conversation object
                $conversation_obj = $this->get_conversation();
                $conversation = $conversation_obj->get();
                $conversation['lastReplyTime'] = $this->data['modifiedOn'];
                $conversation['lastReplyUid'] = $this->data['uid'];
                $conversation_obj->set($conversation);
                $conversation_obj->save();
            }
        }
        else {
            $this->errorcode = 3;
        }
        return $this;
    }

    /* -------FUNCTIONS-END-------- */
    /* -------GETTER FUNCTIONS-START-------- */
    /**
     * return user obj of current message
     * @return \Users
     */
    public function get_user() {
        return new Users(intval($this->data['uid']));
    }

    /**
     *
     * @return \SystemConversations
     */
    public function get_conversation() {
        return new SystemConversations($this->data['scid']);
    }

    /* -------GETTER FUNCTIONS-END-------- */
    /**
     * Create a message in the conversation followed by all related actions
     * @global type $db
     * @global type $core
     * @param array $data
     * @param type $aorid
     * @param array $config
     * @return \SystemConversations
     */
    public function create_message(array $data, $scmid, array $config = array()) {
        global $db, $core;

        $messagedata = array();

        //check if message contains an email message id and save it as reply to email id
        if(preg_match("/Message-ID: (.*)/", $data['message'], $matches)) {
            preg_match("/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/", $matches[1], $messageid);
            $messagedata['replyToEmail'] = $messageid[1];
        }
        //check if this message is a reply to a specific message and save it in reply to field
        if(preg_match("/In-Reply-To: (.*)/", $data['message'], $matches)) {
            preg_match("/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/", $matches[1], $replyto);
            $messagedata['replyTo'] = $replyto[1];
        }

        if($config['source'] != 'emaillink') {
            $messagedata['message'] = LeavesMessages::extract_message($data['message'], true);
        }

        $messagedata['scid'] = $scmid;
        $messagedata['uid'] = $core->user['uid'];
        $messagedata['createdOn'] = TIME_NOW;
        $messagedata['type'] = $data['type'];
        $messagedata['inputChecksum'] = $data['inputChecksum'];

        //save message after the data has been set
        $this->set($messagedata);
        $this->save();
        //if saving of message was completed normally, proceed with additional steps
        if($this->get_errorcode() == 0) {
            $conversation_obj = $this->get_conversation();
            //get conversation recipients
            $recipients = $conversation_obj->get_recipients();
            if(is_array($recipients)) {
                //send email for all recipients
                $this->send_message($recipients, $conversation_obj);
            }
            else {
                $this->errorcode = 5;
            }
        }
        return $this;
    }

    /**
     * this function is solely responsible for sending the message through emails after being fed the recipients
     *
     * @global type $lang
     * @global type $core
     * @param type $recipients array of recipients containing recipient emails
     * @param type $options array containing additional options for the sent email
     * @return \SystemConversationsMessages
     */
    public function send_message($recipients, $conversation_obj, $options = array()) {
        global $lang, $core;

        $lang->load('aro_meta');
        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();
        $mailer->set_from(array('name' => $core->user['displayName'], 'email' => $core->settings['maileremail']));

//        $reply_links = DOMAIN."/index.php?module=aro/managearodouments&referrer=toapprove&id=".$arorequest->get()['aorid'].'#message';
//        $view_link = DOMAIN."/index.php?module=aro/managearodouments&referrer=toapprove&id=".$arorequest->get()['aorid'];
//        $mailer->set_subject($lang->newrequestmsgsubject.' ['.$arorequest->orderReference.'] ['.$arorequest->inputChecksum.']');

        if(is_array($recipients)) {
            foreach($recipients as $emailreceiver) {
                $message = $this->data['message'];
                if(!empty($message)) {
                    $emailformatter = new EmailFormatting();
                    $emailformatter->set_message(array('title' => $conversation_obj->get_displayname(), 'message' => $message));
                    $message = $emailformatter->get_message();
                    $mailer->set_message('__CONVERSATION NOTIFICATION__<br/>'.$message);
                    $mailer->set_to($emailreceiver);
                    $mailer->send();
                }
            }
        }

        if($mailer->get_status() != true) {
            $this->errorcode = 6;
        }
        return $this;
    }

    /**
     * get the replyto message id if it exists
     * @return boolean|\self
     */
    public function get_replyto() {
        if(empty($this->data['replyTo'])) {
            return false;
        }
        /* Get the reply messaage id  of the current message object */
        return new self($this->data['replyTo']);
    }

    /**
     * get all replies for current message
     * @global type $db
     * @return boolean
     */
    public function get_replies() {
        global $db;
        $replies = self::get_data('replyTo='.$this->data[self::PRIMARY_KEY], array('simple' => false, 'returnarray' => true));
        if(is_array($replies)) {
            return $replies;
        }
        return false;
    }

    /**
     * responsible for parsing a single message
     * @param type $viewmode
     * @return string
     */
    public function parse_single_message($options = array()) {
        global $core, $template;
        $bgcolor = alt_row($bgcolor);
        $inline_style = 'margin-left:'.($depth * 8).'px;';
        $message = $this->get();
        $message['user'] = $this->get_user()->get();
        $message['message_date'] = date($core->settings['dateformat'], $message['createdOn']);
        $show_replyicon = 'display:none';
        if(is_array($options)) {
            if(isset($options['showreply']) && $options['showreply'] == true) {
                $show_replyicon = '';
            }
        }
        if(isset($options['viewmode']) && ($options['viewmode'] == 'textonly')) {
            $takeactionpage_conversation .= '<span style="font-weight: bold;"> '.$message['user']['displayName'].'</span> <span style="font-size: 9px;">'.date($core->settings['dateformat'].' '.$core->settings['timeformat'], $message['createdOn']).'</span>:';
            $takeactionpage_conversation .= '<div>'.$message['message'].'</div><br />';
        }
        else {
            eval("\$takeactionpage_conversation = \"".$template->get('general_conversation_messagehistory_entry')."\";");
        }
        return $takeactionpage_conversation;
    }

}