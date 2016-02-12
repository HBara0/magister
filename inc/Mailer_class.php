<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Mailer Class
 * $id: Mailer_class.php
 * Last Update: @zaher.reda 	Septmeber 16, 2011 | 11:25 AM
 */

class Mailer {
    public $status = false;
    private $class_name = null;

    public function __construct($mail = null, $type = 'oophp', $only_send = true, array $smtp_options = array(), array $config = array()) {
        $this->class_name = 'Mailer_'.$type;

        if($this->class_name != 'Mailer_oophp') {
            $mailer = new $this->class_name($mail, $type, $only_send, null, $config);
            if($mailer->send($config)) {
                $this->set_status(true);
            }
            else {
                $this->set_status(false);
            }
        }
    }

    protected function set_status($status) {
        $this->status = $status;
    }

    public function get_mailerobj() {
        return new $this->class_name;
    }

    public function get_status() {
        return $this->status;
    }

}

class Mailer_functions {
    protected $only_send;
    protected $mail_data = array();

    protected function set_mail_data(array $data) {
        $this->mail_data = $data;
    }

    protected function check_missingdata(array $data) {
        foreach($data as $key => $val) {
            if(empty($val)) {
                return false;
            }
        }
        return true;
    }

    protected function validate_data($data) {
        if(is_array($data)) {
            foreach($data as $val) {
                if(is_array($val)) {
                    foreach($val as $v) {
                        $this->validate_data($v);
                    }
                }
                else {
                    $this->validate_data($val);
                }
            }
        }
        else {
            $data = strtolower($data);

            if((strpos($data, 'content-type:') !== false) || (strpos($data, 'bcc:') !== false) || (strpos($data, 'cc:') !== false)) {
                return false;
            }
            return true;
        }
        return true;
    }

    protected function isvalid_email($email) {
        if(strpos($email, ' ') !== false) {
            return false;
        }

        if(function_exists('filter_var')) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }
        else {
            return preg_match("/^[a-zA-Z0-9&*+\-_.{}~^\?=\/]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9.-]+$/si", $email);
        }
    }

    protected function clean_header($header) {
        $header = trim($header);
        $header = str_replace("\r", '', $header);
        $header = str_replace("\n", '', $header);
        return $header;
    }

    protected function fix_endofline($text) {
        $text = str_replace("\r\n", "\n", $text);
        $text = str_replace("\r", "\n", $text);
        return $text;
    }

}

class Mailer_oophp extends Mailer_functions {
    private $content_classes = array(
            'appointment' => 'urn:content-classes:appointment',
            'task' => 'urn:content-classes:task',
            'meetingrequest' => 'urn:content-classes:calendarmessage',
            'calendarmessage' => 'urn:content-classes:calendarmessage',
            'taskrequest' => 'urn:content-classes:calendarmessage',
            'note' => 'urn:content-classes:note',
            'item' => 'urn:content-classes:item'
    );
    protected $mail_data = array();
    private $boundaries = array();
    private $status = false;
    private $configs = array();

    public function __construct() {
        $this->boundaries['id'] = md5(uniqid(TIME_NOW));
        $this->boundaries[1] = 'b1_'.$this->boundaries['id'];
        $this->boundaries[2] = 'b2_'.$this->boundaries['id'];
        $this->mail_data['header'] = "X-Mailer: Orkila Mailer\n";
        $this->mail_data['header'] .= "MIME-version: 1.0\r\n";
        $this->set_type();
    }

    public function set_to($addresses) {
        $this->mail_data['to'] = $addresses;
        if(is_array($this->mail_data['to'])) {
            foreach($this->mail_data['to'] as $key => $val) {
                if(!$this->isvalid_email($val)) {
                    unset($this->mail_data['to'][$key]);
                }
            }

            $this->mail_data['to'] = implode(', ', $this->mail_data['to']);
        }
        else {
            if(!$this->isvalid_email($this->mail_data['to'])) {
                unset($this->mail_data['to']);
                return false;
            }
        }
    }

    public function set_cc($addresses) {
        if(isset($addresses)) {
            $this->mail_data['cc'] = $addresses;
            if(is_array($this->mail_data['cc'])) {
                foreach($this->mail_data['cc'] as $key => $val) {
                    if(!$this->isvalid_email($val)) {
                        unset($this->mail_data['cc'][$key]);
                    }
                }

                $this->mail_data['cc'] = implode(', ', $this->mail_data['cc']);
            }

            $this->mail_data['header'] .= "CC:".$this->clean_header($this->mail_data['cc'])."\n";
        }
    }

    public function set_bcc($addresses) {
        if(isset($addresses)) {
            $this->mail_data['bcc'] = $addresses;

            if(is_array($this->mail_data['bcc'])) {
                foreach($this->mail_data['bcc'] as $key => $val) {
                    if(!$this->isvalid_email($val)) {
                        unset($this->mail_data['bcc'][$key]);
                    }
                }

                $this->mail_data['bcc'] = implode(', ', $this->mail_data['bcc']);
            }

            $this->mail_data['header'] .= 'BCC:'.$this->clean_header($this->mail_data['bcc'])."\n";
        }
    }

    public function set_from($sender) {
        if(is_array($sender)) {
            $this->mail_data['from_email'] = $sender['email'];
            $this->mail_data['from'] = $sender['name'];
        }
        else {
            $this->mail_data['from_email'] = $sender;
        }
        if(function_exists('isvalid_email')) {
            $is_valid = isvalid_email($this->mail_data['from_email']);
        }
        else {
            $is_valid = $this->isvalid_email($this->mail_data['from_email']);
        }

        if($is_valid === false) {
            output_xml('<status>false</status><message>Invalid email</message>');
            exit;
        }

        $this->mail_data['header'] .= 'FROM:'.$this->clean_header($this->mail_data['from']).'<'.$this->clean_header($this->mail_data['from_email']).">\n";
        $this->mail_data['header'] .= 'Reply-To: <'.$this->clean_header($this->mail_data['from_email']).">\n";
        $this->mail_data['header'] .= 'Return-Path: <'.$this->clean_header($this->mail_data['from_email']).">\n";
        $this->mail_data['add_param'] = '-f'.$this->clean_header($this->mail_data['from_email']).' -r'.$this->clean_header($this->mail_data['from_email']);
    }

    public function set_flag($flag) {
        if(isset($flag) && !empty($flag)) {
            $this->mail_data['flag'] = $flag;
            $this->mail_data['header'] .= "X-Message-Flag:".$this->clean_header($this->mail_data['flag'])."\n";
        }
    }

    public function set_replyby($date) {
        if(isset($date) && !empty($date)) {
            $this->mail_data['replyby'] = $date;
            $this->mail_data['header'] .= "Reply-By:".date($core->settings['dateformat'].' '.$core->settings['timeformat'], $this->mail_data['replyby'])."\n";
        }
    }

    public function set_headeritem($header, $value) {
        if(!empty($header) && !empty($value)) {
            $this->mail_data['header'] .= $this->clean_header($header).':'.$this->clean_header($value)."\n";
        }
    }

    public function set_type($type = 'normal', $config = array()) {
        $this->mail_data['type'] = $type;
        $this->mail_data['type_config'] = $config;
        $this->mail_data['type_header'] = $this->parse_headertype($type, $config);
    }

    private function parse_headertype($type = 'normal', $config = array()) {
        if($type == 'ical') {
            if((isset($config['content-class']) && !empty($config['content-class'])) && isset($this->content_classes[$config['content-class']])) {
                $header = "Content-class: ".$this->content_classes[$config['content-class']]."\r\n";

                if(!isset($config['method'])) {
                    $config['method'] = 'PUBLISH';
                }
                $header .= "Content-type: text/calendar; charset=UTF-8; method={$config[method]}; name=\"{$config[filename]}\"\r\n"; //method=REQUEST;
                $header .= "Content-Transfer-Encoding: 8bit\n\n";
            }
        }
        elseif($type == 'mixed') {
            if(!isset($config['boundary'])) {
                $config['boundary'] = $this->boundaries[1];
            }

            $header = "Content-Type: multipart/mixed;\n\tboundary=\"".$config['boundary']."\"\n";
            $header .= "Content-Transfer-Encoding: 8bit\n\n";
        }
        elseif($type == 'plain') {
            $header = "Content-type: text/plain; charset=utf-8;\n";
            $header .= "Content-Transfer-Encoding: 8bit\n\n";
        }
        else {
            $header = "Content-type: text/html; charset=utf-8;\n";
            $header .= "Content-Transfer-Encoding: 8bit\n\n";
        }
        return $header;
    }

    public function add_attachment($attachment, $type = '', $config = array()) {
        if(!isset($this->mail_data['attachments'])) {
            $this->mail_data['attachments'] = array();
        }
        $attachment_id = md5(uniqid(TIME_NOW));
        $attachment_size = filesize($attachment);
        $handle = fopen($attachment, 'r');
        $attachment_content = fread($handle, $attachment_size);
        fclose($handle);

        $attachment_content = chunk_split(base64_encode($attachment_content));
        if(isset($config['filename']) && !empty($config['filename'])) {
            $filename = $config['filename'];
        }
        else {
            $filename = basename($attachment);
        }

        $this->mail_data['attachments'][$attachment_id] = "--".$this->boundaries[1]."\n";
        if(isset($type) && !empty($type)) {
            $this->mail_data['attachments'][$attachment_id] .= "Content-Type: ".$type."; charset=utf-8; name=\"".$filename."\"\n";
        }
        else {
            $this->mail_data['attachments'][$attachment_id] .= "Content-Type: application/octet-stream; charset=utf-8; name=\"".$filename."\"\n";
        }

        $content_id = $attachment_id;
        if(isset($config['contentid']) && !empty($config['contentid'])) {
            $content_id = $config['contentid'];
        }
        $this->mail_data['attachments'][$attachment_id] .= "Content-Id: <".$content_id.">\n";
        unset($content_id);
        $this->mail_data['attachments'][$attachment_id] .= "Content-Transfer-Encoding: base64\n";
        $this->mail_data['attachments'][$attachment_id] .= "Content-Disposition: attachment; filename=\"".$filename."\"\n\n";
        $this->mail_data['attachments'][$attachment_id] .= $attachment_content."\n";

        if($this->mail_data['multiparted'] == false) {
            $this->set_multiparted(true);
            $this->set_message($this->mail_data['originalmessage']);
            $this->set_type('mixed');
        }
    }

    public function set_subject($subject) {
        $this->mail_data['subject'] = wordwrap(htmlspecialchars_decode($subject), 70);
    }

    public function set_message($message) {
        $this->mail_data['originalmessage'] = $message;
        if($this->mail_data['multiparted'] == true) {
            $content_types = array('plain', 'html');
            if(!empty($this->configs['requiredcontenttypes']) && is_array($this->configs['requiredcontenttypes'])) {
                $content_types = $this->configs['requiredcontenttypes'];
            }
            if($this->mail_data['type'] == 'ical') {
                $content_types = array('ical');
                $content_types_config['ical'] = $this->mail_data['type_config'];
            }

            $this->mail_data['message'] = "Content-Type: multipart/alternative; boundary=\"".$this->boundaries[2]."\"\n\n";
            foreach($content_types as $type) {
                $this->mail_data['message'] .= $this->parse_message_part($message, $type, $content_types_config[$type]);
            }

            $this->mail_data['message'] .= "\n--".$this->boundaries[2]."--\n";
        }
        else {
            $this->mail_data['message'] = $this->fix_endofline($message);
        }
    }

    private function parse_message_part($message, $type, $config = array()) {
        global $core;
        $message_part = "--".$this->boundaries[2]."\n";
        $message_part .= $this->parse_headertype($type, $config);
        if($type == 'plain') {
            $message = $core->sanitize_inputs(str_replace('<br />', "\n", $message), array('removetags' => true));
        }

        $message_part .= $this->fix_endofline($message)."\n";
        return $message_part;
    }

    private function set_multiparted($is_multiparted = false) {
        $this->mail_data['multiparted'] = $is_multiparted;
    }

    public function set_required_contenttypes(array $requiredcontenttypes = array('plain', 'html')) {
        $this->configs['requiredcontenttypes'] = $requiredcontenttypes;
    }

    public function send() {
        if(!$this->validate_data($this->mail_data)) {
            output_xml('<status>false</status><message>Security violation detected</message>');
            exit;
        }

        @ini_set('sendmail_from', $this->mail_data['from_email']);
        if(!strstr($this->mail_data['header'], 'Content-type:')) {
            $this->mail_data['header'] .= $this->mail_data['type_header'];
        }

        if(isset($this->mail_data['attachments']) && !empty($this->mail_data['attachments'])) {
            $this->mail_data['message'] = "--".$this->boundaries[1]."\n".$this->mail_data['message'];

            foreach($this->mail_data['attachments'] as $id => $content) {
                $this->mail_data['message'] .= $content;
            }
            $this->mail_data['message'] .= '--'.$this->boundaries[1]."--\n";
            $send = @mail($this->mail_data['to'], $this->clean_header($this->mail_data['subject']), $this->mail_data['message'], $this->mail_data['header'], $this->mail_data['add_param']);
        }
        else {
            if(function_exists('mb_send_mail')) {
                $send = @mb_send_mail($this->mail_data['to'], $this->mail_data['subject'], $this->mail_data['message'], $this->mail_data['header'], $this->mail_data['add_param']);
            }
            else {
                $send = @mail($this->mail_data['to'], $this->clean_header($this->mail_data['subject']), $this->mail_data['message'], $this->mail_data['header'], $this->mail_data['add_param']);
            }
        }

        if($send) {
            $this->set_status(true);
            return true;
        }
        $this->set_status(false);
        return false;
    }

    private function set_status($status) {
        $this->status = $status;
    }

    public function get_status() {
        return $this->status;
    }

    public function debug_info() {
        return $this->mail_data;
    }

}

class Mailer_php extends Mailer_functions {
    private $content_classes = array(
            'appointment' => 'urn:content-classes:appointment',
            'task' => 'urn:content-classes:task',
            'meetingrequest' => 'urn:content-classes:calendarmessage',
            'calendarmessage' => 'urn:content-classes:calendarmessage',
            'taskrequest' => 'urn:content-classes:calendarmessage',
            'note' => 'urn:content-classes:note',
            'item' => 'urn:content-classes:item'
    );

    public function __construct(array $mail, $type, $only_send = true) {
        $this->only_send = $only_send;
        $this->set_mail_data($mail);
    }

    public function send($config) {
        global $core;

        if($this->only_send == false) {
            if($this->check_missingdata($this->mail_data) == false) {
                output_xml('<status>false</status><message>Please fill all fields</message>');
                exit;
            }
            if(function_exists('isvalid_email')) {
                $valid_email = isvalid_email($this->mail_data['from_email']);
            }
            else {
                $valid_email = $this->isvalid_email($this->mail_data['from_email']);
            }

            if($valid_email === false) {
                output_xml('<status>false</status><message>Invalid email</message>');
                exit;
            }
        }

        if(is_array($this->mail_data['to'])) {
            if($this->only_send == false) {
                foreach($this->mail_data['to'] as $key => $val) {
                    if(!$this->isvalid_email($val)) {
                        unset($this->mail_data['to'][$key]);
                    }
                }
            }

            $this->mail_data['to'] = implode(', ', $this->mail_data['to']);
        }

        if(!$this->validate_data($this->mail_data)) {
            output_xml('<status>false</status><message>Security violation detected</message>');
            exit;
        }

        @ini_set('sendmail_from', $this->mail_data['from_email']);

        $this->mail_data['header'] = 'FROM:'.$this->clean_header($this->mail_data['from']).'<'.$this->clean_header($this->mail_data['from_email']).">\n";
        $this->mail_data['header'] .= 'Reply-To: <'.$this->clean_header($this->mail_data['from_email']).">\n";
        $this->mail_data['header'] .= 'Return-Path: <'.$this->clean_header($this->mail_data['from_email']).">\n";
        $this->mail_data['header'] .= "X-Mailer: PHP\n";

        $this->mail_data['add_param'] = '-f'.$this->clean_header($this->mail_data['from_email']).' -r'.$this->clean_header($this->mail_data['from_email']);

        /* Parse CC - Start */
        if(isset($this->mail_data['cc'])) {
            if(is_array($this->mail_data['cc'])) {
                if($this->only_send == false) {
                    foreach($this->mail_data['cc'] as $key => $val) {
                        if(!$this->isvalid_email($val)) {
                            unset($this->mail_data['cc'][$key]);
                        }
                    }
                }
                $this->mail_data['cc'] = implode(', ', $this->mail_data['cc']);
            }

            $this->mail_data['header'] .= "CC:".$this->clean_header($this->mail_data['cc'])."\n";
        }
        /* Parse CC - END */

        /* Parse BCC - Start */
        if(isset($this->mail_data['bcc'])) {
            if(is_array($this->mail_data['bcc'])) {
                if($this->only_send == false) {
                    foreach($this->mail_data['bcc'] as $key => $val) {
                        if(!$this->isvalid_email($val)) {
                            unset($this->mail_data['bcc'][$key]);
                        }
                    }
                }
                $this->mail_data['bcc'] = implode(', ', $this->mail_data['bcc']);
            }

            $this->mail_data['header'] .= "BCC:".$this->clean_header($this->mail_data['bcc'])."\n";
        }
        /* Parse BCC - END */

        /* Parse Flag - START */
        if(isset($this->mail_data['flag'])) {
            $this->mail_data['header'] .= "X-Message-Flag:".$this->clean_header($this->mail_data['flag'])."\n";
        }
        if(isset($this->mail_data['replyby'])) {
            $this->mail_data['header'] .= "Reply-By:".date($core->settings['dateformat'].' '.$core->settings['timeformat'], $this->mail_data['replyby'])."\n";
        }
        /* Parse Flag - END */

        if(isset($this->mail_data['attachments']) && is_array($this->mail_data['attachments'])) {
            $includes_attachment = true;
            $boundary_id = md5(uniqid(TIME_NOW));
            $boundary[1] = 'b1_'.$boundary_id;
            $boundary[2] = 'b2_'.$boundary_id;

            $this->mail_data['header'] .= "MIME-Version: 1.0\n";
            $this->mail_data['header'] .= "Content-Type: multipart/mixed;\n\tboundary=\"".$boundary[1]."\"\n"; //\n\ttype=\"text/html\";

            $this->mail_data['att_message'] .= "--".$boundary[1]."\n";

            $this->mail_data['att_message'] .= "Content-Type: multipart/alternative; boundary=\"".$boundary[2]."\"\n\n";
            $this->mail_data['att_message'] .= "--".$boundary[2]."\n";
            $this->mail_data['att_message'] .= "Content-type: text/plain; charset=\"utf-8\"\n";
            $this->mail_data['att_message'] .= "Content-Transfer-Encoding: 8bit\n\n";

            $this->mail_data['att_message'] .= $this->mail_data['message']."\n\n";

            $this->mail_data['att_message'] .= "--".$boundary[2]."\n";
            $this->mail_data['att_message'] .= "Content-type: text/html; charset=\"utf-8\"\n";
            $this->mail_data['att_message'] .= "Content-Transfer-Encoding: 8bit\n\n";

            $this->mail_data['att_message'] .= $this->mail_data['message']."\n\n";

            $this->mail_data['att_message'] .= "\n--".$boundary[2]."--\n";

            foreach($this->mail_data['attachments'] as $key => $attachment) {
                $this->mail_data['att_message'] .= "--".$boundary[1]."\n";

                $attachment_size = filesize($attachment);
                $handle = fopen($attachment, 'r');
                $attachment_content = fread($handle, $attachment_size);
                fclose($handle);

                $attachment_content = chunk_split(base64_encode($attachment_content));
                $filename = basename($attachment);

                if(isset($this->mail_data['attachments_types'][$key])) {
                    $this->mail_data['att_message'] .= "Content-Type: ".$this->mail_data['attachments_types'][$key]."; name=\"".$filename."\"\n";
                }
                else {
                    $this->mail_data['att_message'] .= "Content-Type: application/octet-stream; name=\"".$filename."\"\n";
                }
                $this->mail_data['att_message'] .= "Content-Transfer-Encoding: base64\n";
                $this->mail_data['att_message'] .= "Content-Disposition: attachment; filename=\"".$filename."\"\n\n";
                $this->mail_data['att_message'] .= $attachment_content."\n";
            }

            $this->mail_data['att_message'] .= '--'.$boundary[1]."--\n";
            $this->mail_data['message'] = $this->mail_data['att_message'];
        }
        else {
            if((isset($config['content-class']) && !empty($config['content-class'])) && isset($this->content_classes[$config['content-class']])) {
                $this->mail_data['header'] .= "MIME-version: 1.0\r\n";
                $this->mail_data['header'] .= "Content-class: ".$this->content_classes[$config['content-class']]."\r\n";

                if(!isset($config['method'])) {
                    $config['method'] = 'PUBLISH';
                }
                $this->mail_data['header'] .= "Content-type: text/calendar; charset=UTF-8; method={$config[method]}; name=\"{$config[filename]}\"\r\n"; //method=REQUEST;
                $this->mail_data['header'] .= "Content-Transfer-Encoding: 8bit\n\n";
            }
            elseif(isset($config['mixedcontent']) && $config['mixedcontent'] == true) {
                $this->mail_data['header'] .= "MIME-version: 1.0\r\n";
                $this->mail_data['header'] .= "Content-Type: multipart/mixed;\n\tboundary=\"".$config['boundary']."\"\n";
                $this->mail_data['header'] .= "Content-Transfer-Encoding: 8bit\n\n";
            }
            else {
                $this->mail_data['header'] .= "Content-type: text/html; charset=utf-8;\n";
            }
        }

        $this->mail_data['message'] = $this->fix_endofline($this->mail_data['message']);
        if($includes_attachment === true) {
            $send = @mail($this->mail_data['to'], wordwrap($this->clean_header($this->mail_data['subject']), 70), $this->mail_data['message'], $this->mail_data['header'], $this->mail_data['add_param']);
        }
        else {
            if(function_exists('mb_send_mail')) {
                $send = @mb_send_mail($this->mail_data['to'], wordwrap($this->mail_data['subject'], 70), $this->mail_data['message'], $this->mail_data['header'], $this->mail_data['add_param']);
            }
            else {
                $send = @mail($this->mail_data['to'], wordwrap($this->clean_header($this->mail_data['subject']), 70), $this->mail_data['message'], $this->mail_data['header'], $this->mail_data['add_param']);
            }
        }

        if(!$send) {
            return false;
        }
        return true;
    }

}
?>