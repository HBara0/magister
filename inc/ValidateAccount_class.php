<?php

class ValidateAccount extends Accounts {
	private $result = false;
	protected $user_details;
	protected $error_message = '';


	public function __construct(array $data = array()) {
		if(!empty($data)) {
			if(parent::username_exists($data['username'])) {
				$this->user_details = $this->get_user_by_username($data['username']);

				if($this->validate_password($data['password'], $this->user_details)) {
					$this->result = true;
				}
				else {
					$this->result = false;
				}
			}
			else {
				$this->result = false;
			}
		}
	}

	protected function validate_password($password, $user_data) {
		if(parent::create_password($password, $user_data['salt']) == $user_data['password']) {
			return true;
		}
		else {
			return false;
		}
	}

	protected function validate_crypt_password($password, $user_data) {
		if(parent::create_password($password, $user_data['salt']) == $user_data['password']) {
			return true;
		}
		else {
			return false;
		}
	}

	public function can_attemptlogin($username = '') {
		global $db, $core, $lang;

		if(parent::username_exists($username)) {
			if(!empty($username)) {
				$this->user_details = $this->get_user_by_username($username);
			}
			else {
				if(empty($this->user_details)) {
					return false;
				}
			}

			if($this->user_details['failedLoginAttempts'] >= $core->settings['loginattempts']) {
				if(time() - $this->user_details['lastAttemptTime'] > ($core->settings['failedlogintime'] * 60)) {
					$db->update_query('users', array('failedLoginAttempts' => 0, 'lastAttemptTime' => 0), "uid='".$this->user_details['uid']."'");
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return true;
			}
		}
		else {
			$this->error_message = $lang->invalidlogin;
			return false;
		}
	}

	public function get_real_failed_attempts() {
		global $db;

		return $db->fetch_field($db->query("SELECT failedLoginAttempts FROM ".Tprefix."users WHERE uid='".$this->user_details['uid']."'"), 'failedLoginAttempts');
	}

	public function get_user_by_username($username) {
		global $db;

		return $db->fetch_array($db->query("SELECT * FROM ".Tprefix."users WHERE username='".$db->escape_string($username)."'"));
	}

	public function get_userdetails() {
		return $this->user_details;
	}

	public function get_validation_result() {
		return $this->result;
	}

	public function get_error_message() {
		if(!empty($this->error_message)) {
			return $this->error_message;
		}
		return false;
	}


}
?>