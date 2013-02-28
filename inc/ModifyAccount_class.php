<?php
class ModifyAccount extends Accounts {
	public $status = false;
	
	public function __construct($data) {
		if(is_array($data)) {
			$this->perform_modify($data);
		}
	}
	
	private function set_status($new_status) { 
		$this->status = $new_status;
	}
	
	public function get_status() {
		return $this->status;
	} 
	
	public function archive_password($password, $uid = '') {
		global $db;
		
		if(empty($uid)) {
			$uid = $this->data['uid']; /* to be implemented */
		}
		$db->insert_query('users_passwordarchive', array('uid' => $uid, 'password' =>  md5($password), 'archiveTime' => TIME_NOW));
	
		/* Maintain last X passwords */
		$db->delete_query('users_passwordarchive', 'upaid IN (SELECT upaid FROM '.Tprefix.'users_passwordarchive WHERE uid='.intval($uid).' ORDER BY archiveTime ASC LIMIT '.$core->settings['users_passwordarchive'].', '.$core->settings['users_passwordarchive']);
	}
	
	private function perform_modify(array $data) {
		global $db, $core, $lang;
		
		if(empty($data['uid'])) {
			output_xml("<status>false</status><message>{$lang->wrongid}</message>");
			exit;
		}
		
		$uid = $db->escape_string($data['uid']);
		
		if(isset($data['referrer'])) {
			if($data['referrer'] == 'hr') {
				$hr_fields = $db->show_fields_from('userhrinformation');
	
				foreach($hr_fields as $val) {
					if($val['Field'] != 'uhrid') {
						$hr_data[$val['Field']] = $data[$val['Field']];
						if($val['Field'] != 'uid') {
							unset($data[$val['Field']]);
						}
					}
				}
			}
			unset($data['referrer']);
		}
		
		if(array_key_exists('username', $data)) {
			$username = $db->fetch_field($db->query("SELECT username FROM ".Tprefix."users WHERE uid='{$uid}'"), 'username');
			if($username != $data['username']) {
				if(parent::username_exists($data['username'])) {
					output_xml("<status>false</status><message>{$lang->usernameexists}</message>");
					exit;
				}
			}
		}
		
		if(array_key_exists('password', $data)) {
			if(!empty($data['password'])) {
				if(!parent::validate_password_complexity($data['password'])) {
					output_xml("<status>false</status><message>{$lang->pwdpatternnomatch}</message>");
					exit;
				}
				/* Check if password was used before */
				if(parent::in_passwordarhive($data['password'], $uid)) {
					output_xml("<status>false</status><message>{$lang->passwordalreadyused}</message>");
					exit;
				}
				$data['salt']  = parent::create_salt(); 
				$data['password'] = parent::create_password($data['password'], $data['salt']);
				$data['loginKey']  = parent::create_loginkey();
			}
			else
			{
				unset($data['password']);
			}
		}
		
		if(isset($data['email'])) {
			if($core->validate_email($data['email'])) {
				$data['email'] = $core->sanitize_email($data['email']);
			}
			else
			{
				output_xml("<status>false</status><message>{$lang->invalidemail}</message>");
				exit;
			}
		}
		
		$phones_index = array('mobile', 'mobile2', 'telephone', 'telephone2');
		foreach($phones_index as $val) {
			if(isset($data[$val.'_intcode'], $data[$val.'_areacode'], $data[$val.'_number'])) {
				if(!empty($data[$val.'_intcode']) || !empty($data[$val.'_areacode']) || !empty($data[$val.'_number'])) {
					$data[$val] = $data[$val.'_intcode'].'-'.$data[$val.'_areacode'].'-'.$data[$val.'_number'];
				}
				else
				{
					$data[$val] = '';
				}
				unset($data[$val.'_intcode'], $data[$val.'_areacode'], $data[$val.'_number']);
			}
		}
		
		if(!isset($data['mobileIsPrivate'])) {
			$data['mobileIsPrivate'] = 0;	
		}
		
		if(!isset($data['mobile2IsPrivate'])) {
			$data['mobile2IsPrivate'] = 0;	
		}
		$secondary_data = array(
						//'mainaffid' => 'main_affiliate',
						'affiliates'	=> 'affiliates',
						'supplier'	=> 'suppliers',
						'posid'	=> 'positions',
						'cid'	=> 'customers',
						'psid'	=> 'segments',
						'experience' => 'experience',
						'certificate' => 'certificates'
						);
								
		foreach($secondary_data as $key => $val) {
			if(isset($data[$key])) {
				if(!empty($data[$key])) {
					${$val} = $data[$key];
				}
				unset($data[$key]);
			}
		}
		/*if(isset($data['mainaffid'])) {
			if(!empty($data['mainaffid'])) {
				$main_affiliate = $data['mainaffid'];
			}
			unset($data['mainaffid']);
		}
	
		if(isset($data['affids'])) {
			$affiliates = $data['affids'];
			unset($data['affids']);	
		}
		
		if(isset($data['supplier'])) {
			$suppliers = $data['supplier'];
			unset($data['supplier'], $data['supp_numrows']);
		}
		
		if(isset($data['cid'])) {
			$customers = $data['cid'];
			unset($data['cid']);
		}
		
		if(isset($data['posid'])) {
			$positions = $data['posid'];
			unset($data['posid']);
		}

		if(isset($data['psid'])) {
			$segments = $data['psid'];
			unset($data['psid']);
		}*/
	
		$query = $db->update_query('users', $data, "uid='{$uid}'");
		if($query) {
			//$main_affiliate_found = false;
			if(!empty($affiliates)) {
				$affiliates['affids'][$affiliates['mainaffid']] = $affiliates['mainaffid']; 
				$db->delete_query('affiliatedemployees', "uid='{$uid}'");
				if(is_array($affiliates['affids'])) {
					foreach($affiliates['affids'] as $key => $val) { 
						$new_affiliatedemployees = array('affid' => $val, 'uid' => $uid);
						//$isMain = 0;
						foreach($affiliates as $attr => $values) {
							if($attr != 'affids') {
								if(is_array($values)) {
									if(in_array($val, $values)) {
										$new_affiliatedemployees[$attr] = 1;
									}
								}
								else
								{
									if($attr == 'mainaffid') {
										if($val == $values) {
											$main_affiliate_found = true;
											$new_affiliatedemployees['isMain'] = 1;
											if(isset($affiliates['affids']['canAudit'][$val])) {
												$new_affiliatedemployees['canAudit'] = 1;
											}
										}	
									}
								}
							}
						}
	
						$db->insert_query('affiliatedemployees', $new_affiliatedemployees);
					}
				}
			}
			
/*			if(!empty($affiliates['mainaffid'])) {
				if(empty($affiliates['affids'])) {
					$db->delete_query('affiliatedemployees', "uid='{$uid}'");
				}
				if($main_affiliate_found == false) {
					$main_affiliate_audit = 0;
					if(isset($affiliates['affids']['canAudit'][$affiliates['mainaffid']])) {
						$main_affiliate_audit = 1;
					}
					$db->insert_query('affiliatedemployees', array('affid' => $affiliates['mainaffid'], 'uid' => $uid, 'isMain' => 1, 'canAudit' => $main_affiliate_audit));
				}
			}*/
			
			if(!empty($suppliers)) {
				$db->delete_query('assignedemployees', "uid='{$uid}'");
				$db->delete_query('suppliersaudits', "uid='{$uid}'");
				foreach($suppliers as $key => $val) {
					if(empty($val['eid']) || !isset($val['eid']) || empty($val['affiliates'])) {
						continue;
					}
					
					if(isset($val['isValidator']) && $val['isValidator'] == 'on') {
						$db->insert_query('suppliersaudits', array('eid' => $val['eid'], 'uid' => $uid));
					}
					foreach($val['affiliates'] as $value) {
						$db->insert_query('assignedemployees', array('eid' => $val['eid'], 'uid' => $uid, 'affid' => $value));
					}
				}
			}
			
			if(!empty($customers)) {
				if(empty($suppliers)) {
					$db->delete_query('assignedemployees', "uid='{$uid}'");
				}
				
				foreach($customers as $key => $val) {
					$db->insert_query('assignedemployees', array('eid' => $val, 'uid' => $uid));
				}
			}
			
			if(!empty($positions)) {
				$db->delete_query('userspositions', "uid='{$uid}'");

				foreach($positions as $key => $val) {
					$db->insert_query('userspositions', array('posid' => $val, 'uid' => $uid));
				}
			}
			
			if(isset($segments) && !empty($segments)) {
				$db->delete_query('employeessegments', "uid='{$uid}'");
				foreach($segments as $key => $val) {
					$db->insert_query('employeessegments', array('psid' => $val, 'uid' => $uid));
				}
			}
			
			if(!empty($experience)) {
				$db->delete_query('employeesexperience', "uid='{$uid}'");
				foreach($experience as $key => $val) {
					if(empty($val['company'])) { continue; }
					
					$val['uid'] = $uid;
					$db->insert_query('employeesexperience', $val);
				}
			}
			if(!empty($certificates)) {
				$db->delete_query('employeeseducationcert', "uid='{$uid}'");
				foreach($certificates as $key => $val) {
					if(empty($val['name'])) { continue; }
					
					$val['uid'] = $uid;
					$db->insert_query('employeeseducationcert', $val);
				}
			}
			
			if(!empty($hr_data)) {
				$date_fields = array('joinDate', 'leaveDate', 'birthDate', 'firstJobDate');
				foreach($date_fields as $val) {
/*					$date_tocheck = explode('-', $hr_data[$val]);
					if(checkdate(intval($date_tocheck[1]), intval($date_tocheck[0]), intval($date_tocheck[2]))) {
						$hr_data[$val] =  mktime(2, 0, 0, $date_tocheck[1], $date_tocheck[0], $date_tocheck[2]);
					}
					else
					{
						unset($hr_data[$val]);
					}*/
					$hr_data[$val] = strtotime($hr_data[$val]);
				}
				
				if(!empty($hr_data['salary'])) {
					$hr_data['salary'] = intval($hr_data['salary']);
					$hr_data['salaryKey'] =  parent::random_string(10);
					$db_encrypt_fields = array('salary');
				}
				
				if(value_exists('userhrinformation', 'uid', $uid)) {
					$db->update_query('userhrinformation', $hr_data, "uid='{$uid}'", array('encrypt' => $db_encrypt_fields));
				}
				else
				{
					$db->insert_query('userhrinformation', $hr_data, array('encrypt' => $db_encrypt_fields));
				}
			}
			
			$this->set_status(true);
		}
		else
		{
			$this->set_status(false);
		}
	}
}
?>