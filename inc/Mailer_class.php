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
	
	public function __construct(array $mail, $type, $only_send = true, array $smtp_options = array()) {	
		$class_name = 'Mailer_'.$type;
		$mailer = new $class_name($mail, $type, $only_send);
		if($mailer->send()) {
			$this->set_status(true);
		}
		else
		{
			$this->set_status(false);
		}
	}
			
	protected function set_status($status) {
		$this->status = $status;
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
				else
				{
					$this->validate_data($val);
				}	
			}
		}
		else
		{
			$data = strtolower($data);
				
			if((strpos($data,'content-type:') !== false) || (strpos($data,'bcc:') !== false) || (strpos($data,'cc:') !== false)) {
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
		else
		{
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

class Mailer_php extends Mailer_functions {	
	public function __construct(array $mail, $type, $only_send = true) {
		$this->only_send = $only_send;
		$this->set_mail_data($mail);		
	}
	
	public function send() {
		global $core;
		
		if($this->only_send == false) {
			if($this->check_missingdata($this->mail_data) == false) {
				output_xml('<status>false</status><message>Please fill all fields</message>');
				exit;
			}
			if(function_exists('isvalid_email')) {
				$valid_email = isvalid_email($this->mail_data['from_email']);
			}
			else
			{
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
		
		@ini_set("sendmail_from", $this->mail_data['from_email']);

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
			$boundary_id = md5(uniqid(time()));
			$boundary[1] = 'b1_'.$boundary_id;
			$boundary[2] = 'b2_'.$boundary_id;
			
			$this->mail_data['header'] .= "MIME-Version: 1.0\n";
    		$this->mail_data['header'] .= "Content-Type: multipart/related;\n\ttype=\"text/html\";\n\tboundary=\"".$boundary[1]."\"\n"; 

			$this->mail_data['att_message'] .= "--".$boundary[1]."\n";
			
			$this->mail_data['att_message'] .= "Content-Type: multipart/alternative;\n boundary=\"".$boundary[2]."\"\n\n";
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
				
				$this->mail_data['att_message'] .= "Content-Type: application/octet-stream; name=\"".$filename."\"\n";
				$this->mail_data['att_message'] .= "Content-Transfer-Encoding: base64\n";
				$this->mail_data['att_message'] .= "Content-Disposition: attachment; filename=\"".$filename."\"\n\n";
				$this->mail_data['att_message'] .= $attachment_content."\n";
			}
			
			$this->mail_data['att_message'] .= "--".$boundary[1]."--\n";
			$this->mail_data['message'] = $this->mail_data['att_message'];
		}
		else
		{
			$this->mail_data['header'] .= "Content-type: text/html; charset=utf-8;\n";
		}
		
		$this->mail_data['message'] = $this->fix_endofline($this->mail_data['message']);
		if($includes_attachment === true) {
			$send = @mail($this->mail_data['to'], wordwrap($this->clean_header($this->mail_data['subject']), 70), $this->mail_data['message'], $this->mail_data['header'], $this->mail_data['add_param']);
		}
		else
		{
			if(function_exists('mb_send_mail'))
			{
				$send = @mb_send_mail($this->mail_data['to'], wordwrap($this->mail_data['subject'], 70), $this->mail_data['message'], $this->mail_data['header'], $this->mail_data['add_param']);
			}
			else
			{
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