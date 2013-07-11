<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Users Class
 * $id: Users_class.php
 * Created:		@zaher.reda		June 07, 2012 | 12:11 PM
 * Last Update: @zaher.reda		July 26, 2012 | 01:08 PM
 */

class Users {
	private $user = array();
	private $errorcode = 0;

	public function __construct($uid = 0, $simple = true) {
		global $core, $db;

		if(empty($uid)) {
			$this->user = $core->user;
			$this->user['uid'] = $db->escape_string($this->user['uid']);
			$this->user['legalAffid'] = $db->fetch_field($db->query('SELECT legalAffid FROM '.Tprefix.'userhrinformation WHERE uid='.$this->user['uid']), 'legalAffid');
		}
		else {
			$this->read_user($uid, $simple);
		}
	}

	private function read_user($uid = '', $simple = true) {
		global $db;

		if(empty($uid)) {
			$uid = $this->user['uid'];
		}

		$query_select = 'uid, username, reportsTo, firstName, middleName, lastName, displayName, email';
		if($simple == false) {
			$query_select = '*';
		}

		$this->user = $db->fetch_assoc($db->query("SELECT ".$query_select."
												FROM ".Tprefix."users
												WHERE uid='".intval($uid)."'"));
		if(is_array($this->user) && !empty($this->user)) {
			return true;
		}
		$this->status = 2;
		return false;
	}

	private function read_mainaffiliate() {
		global $db;
		$this->user['mainaffiliate'] = $db->fetch_field($db->query("SELECT affid FROM ".Tprefix."affiliatedemployees WHERE uid='{$this->user['uid']}' AND isMain=1"), 'affid');
	}

	public function get_userbyemail($email) {
		global $db;
		$uidquery = $db->query("SELECT DISTINCT(uid), email FROM ".Tprefix."users WHERE email ='".$email."'");
		if($db->num_rows($uidquery) > 0) {
			while($useremail = $db->fetch_assoc($uidquery)) {
				$userbyemail = $useremail['uid'];
			}
			return $userbyemail;
		}
		else {
			return $email;
		}
	}

	public function get() {
		return $this->user;
	}

	public function get_reportsto() {
		return new Users($this->user['reportsTo']);
	}

	public function get_reportingto() {
		global $db;
		$reportsquery = $db->query("SELECT DISTINCT(uid), reportsTo, username, firstName, middleName, lastName, displayName FROM ".Tprefix."users
			 WHERE reportsTo={$this->user[uid]}");
		while($reporting = $db->fetch_assoc($reportsquery)) {
			$this->user['reportingTo'][] = $reporting;
		}
		return $this->user['reportingTo'];
	}

	public function get_additionaldays_byuser() {
		global $db;
		return $this->user['additionaldays'] = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."attendance_additionalleaves WHERE uid={$this->user[uid]}"));
	}

	public function can_hr($options = '') {
		global $db, $core, $user;
		if(!empty($options) && ($options == 'inaffiliate')) {
			if(is_array($core->user['hraffids'])) {
				$affiliate_where = 'AND affe.affid IN ('.implode(',', $core->user['hraffids']).')';
			}
			else {
				return false;
			}
		}

		$hrquery = $db->query("SELECT canHR
						FROM ".Tprefix."users u
						JOIN ".Tprefix."affiliatedemployees affe ON(u.uid=affe.uid)
						WHERE affe.canHr=1 {$affiliate_where} AND affe.uid={$this->user[uid]}");
		if($db->num_rows($hrquery) > 0) {
			return true;
		}
		else {
			return false;
		}
	}

	public function get_assistant() {
		return new Users($this->user['assistant']);
	}

	public function get_positions() {
		global $db, $lang;

		$query = $db->query("SELECT name, title FROM ".Tprefix."positions p JOIN ".Tprefix."userspositions up ON (up.posid=p.posid) WHERE uid={$this->user[uid]}");
		while($position = $db->fetch_assoc($query)) {
			if(!isset($lang->{$position['name']})) {
				$lang->{$position['name']} = $position['title'];
			}
			$this->user['positions'][] = $lang->{$position['name']};
		}
		return $this->user['positions'];
	}

	public function get_mainaffiliate() {
		if(!isset($this->user['mainaffiliate']) || empty($this->user['mainaffiliate'])) {
			$this->read_mainaffiliate();
		}
		return new Affiliates($this->user['mainaffiliate'], FALSE);
	}

	public function get_hrinfo($simple = true) {
		global $db;
		$query_select = '*';
		if($simple == true) {
			$query_select = 'employeeNum, joinDate, jobDescription';
		}

		$this->user['hrinfo'] = $db->fetch_assoc($db->query("SELECT ".$query_select."
										FROM ".Tprefix."userhrinformation
										WHERE uid='".$this->user['uid']."'"));
		if(is_array($this->user['hrinfo']) && !empty($this->user['hrinfo'])) {
			return $this->user['hrinfo'];
		}
		return false;
	}

	private function prepare_sign_info($seperate_lengend = false) {
		global $lang;
		$lang->load('profile');

		$mainaffiliate = $this->get_mainaffiliate();
		$this->user['mainaffiliate_details'] = $mainaffiliate->get();
		$this->user['mainaffiliate_details']['countryname'] = $mainaffiliate->get_country()->get()['name'];

		if(!empty($this->user['mainaffiliate_details']['addressLine1'])) {
			$info['address'] .= $this->user['mainaffiliate_details']['addressLine1'].', ';
		}

		if(!empty($this->user['mainaffiliate_details']['addressLine2'])) {
			$info['address'] .= $this->user['mainaffiliate_details']['addressLine2'].', ';
		}

		if(!empty($affiliate['postCode'])) {
			$info['address'] .= $this->user['mainaffiliate_details']['postCode'].'  ';
		}

		if(!empty($this->user['mainaffiliate_details']['city'])) {
			$info['address'] .= ucfirst($this->user['mainaffiliate_details']['city']).' - ';
		}

		$info['address'] .= ucfirst($this->user['mainaffiliate_details']['countryname']);
		$info['tel'] = '+'.$this->user['mainaffiliate_details']['phone1'];
		$info['ext'] = $this->user['internalExtension'];
		$info['fax'] = '+'.$this->user['mainaffiliate_details']['fax'];
		$info['website'] = 'www.orkila.com';
		//$info['bbpin'] = $this->user['bbPin'];
		$info['email'] = $this->user['email'];
		$info['skype'] = $this->user['skype'];

		if($this->user['mobileIsPrivate'] == 0 && !empty($this->user['mobile'])) {
			$info['mob'] = '+'.$this->user['mobile'];
		}

		if($this->user['mobile2IsPrivate'] == 0 && !empty($this->user['mobile2'])) {
			if(!empty($info['mob'])) {
				$info['mob'] .= '/';
			}
			$info['mob'] .= '+'.$this->user['mobile2'];
		}

		$info['mob'] = str_replace('-', ' ', $info['mob']);
		$info['tel'] = str_replace('-', ' ', $info['tel']);
		$info['fax'] = str_replace('-', ' ', $info['fax']);
		/* Get affiliate details - END */
		$required_values = array(1 => array('address'), 3 => array('tel', 'ext', 'fax'), 4 => array('mob', 'bbpin'), 5 => array('email', 'skype'), 6 => array('website'));
		$hidden_titles = array('address');

		foreach($required_values as $content) {
			$last_filled = false;
			foreach($content as $type) {
				if(!empty($info[$type])) {
					$last_filled = true;
					if(!isset($lang->{$type})) {
						$lang->{$type} = ucfirst($type);
					}

					if($seperate_lengend == false) {
						if(!in_array($type, $hidden_titles)) {
							$details['values'] .= $lang->{$type}.': ';
						}
					}

					$details['values'] .= $info[$type].'   ';

					$details['titles'] .= $lang->{$type}.":\n";
					if(strpos($this->user['mainaffiliate_details'][$type], "\n") || strpos($this->user[$type], "\n")) {
						$details['titles'] .= "\n";
					}
				}
			}

			if($last_filled == true) {
				$details['titles'] .= "\n";
				$details['values'] .= "\n";
			}
		}

		return $details;
	}

	public function generate_image_sign($saved = false, $width = 350, $height = 190, $is_compact = false) {
		$fonts['arial']['regular'] = './inc/fonts/arial.ttf';
		$fonts['arial']['bold'] = './inc/fonts/arialbd.ttf';
		$fonts['arial']['bolditalic'] = './inc/fonts/arialbi.ttf';

		if($is_compact == false) {
			$details = $this->prepare_sign_info();

			/* Check if addresses text is wider than specified width, and resize accordingly */
			$details['values_bbox'] = imagettfbbox(8.5, 0, $fonts['arial']['regular'], $details['values']);
			if($details['values_bbox'][4] > $width) {
				$width = $details['values_bbox'][4];
			}
		}
		else {
			$details['values_bbox'] = imagettfbbox(11, 0, $fonts['arial']['bold'], $this->user['displayName']);
			if(($details['values_bbox'][4] + 65) > $width) {
				$width = $details['values_bbox'][4] + 65;
			}

			$this->user['mainaffiliate_details'] = $this->get_mainaffiliate()->get();
		}

		$im = imagecreatetruecolor($width, $height);
		imagesavealpha($im, true);

		$colors['white'] = imagecolorexact($im, 255, 255, 255);
		$colors['salmon'] = imagecolorexact($im, 0xF4, 0x98, 0x7E);
		$colors['green'] = imagecolorexact($im, 0x7D, 0x9F, 0x3C);
		$colors['blue'] = imagecolorexact($im, 31, 73, 125);
		$colors['gray'] = imagecolorexact($im, 0x66, 0x66, 0x66);
		$colors['transparent'] = imagecolorallocatealpha($im, 0, 0, 0, 127);

		imagefill($im, 0, 0, $colors['white']);

		/* Parse Logo - Start */
		if($is_compact == false) {
			$logo = imagecreatefrompng(DOMAIN.'/images/signlogo.png');
			imagecopy($im, $logo, 1, 18, 0, 0, 98, 71);
		}
		else {
			$logo = imagecreatefrompng(DOMAIN.'/images/signlogo_min.png');
			imagecopy($im, $logo, 1, 4, 0, 0, 49, 36);
		}
		/* Parse Logo - End */

		imageline($im, 0, 0, 260, 0, $colors['black']);

		$this->user['displayName'] = explode(' ', $this->user['displayName']);
		$this->user['displayName'][count($this->user['displayName']) - 1] = strtoupper($this->user['displayName'][count($this->user['displayName']) - 1]);
		$this->user['displayName'] = implode(' ', $this->user['displayName']);

		if($is_compact == false) {
			imagefttext($im, 11, 0, 1, 16, $colors['green'], $fonts['arial']['bold'], $this->user['displayName']);
			$this->get_positions();
			imagefttext($im, 9, 0, 1, 98, $colors['salmon'], $fonts['arial']['bolditalic'], implode(', ', $this->user['positions']));
			imagefttext($im, 8.5, 0, 1, 130, $colors['gray'], $fonts['arial']['regular'], $details['values'], array('linespacing' => 1.1));

			if(empty($this->user['legalAffid'])) {
				$this->user['legalAffid'] = $this->user['mainaffiliate_details']['legalName'];
			}
			imagefttext($im, 10, 0, 1, 115, $colors['green'], $fonts['arial']['regular'], $this->user['legalAffid']);
		}
		else {
			imagefttext($im, 10, 0, 49 + 8, 36 / 1.8, $colors['green'], $fonts['arial']['bold'], $this->user['displayName']);
			if(!empty($this->user['internalExtension'])) {
				$this->user['internalExtension'] = ' ext: '.$this->user['internalExtension'];
			}
			$this->user['mainaffiliate_details']['phone1'] = str_replace('-', ' ', $this->user['mainaffiliate_details']['phone1']);
			imagefttext($im, 8, 0, 49 + 8, (36 / 1.8) + 13, $colors['salmon'], $fonts['arial']['regular'], '+'.$this->user['mainaffiliate_details']['phone1'].$this->user['internalExtension']);
		}

		if($saved == true) {
			$image = './tmp/'.substr(md5(uniqid(microtime())), 1, 5).'.png';
			imagepng($im, $image, 9, PNG_NO_FILTER);
			touch($image);
			imagedestroy($im);
			return $image;
		}
		else {
			header('Content-Type: image/png');
			imagepng($im, NULL, 9, PNG_NO_FILTER);
			imagedestroy($im);
		}
	}

	public function generate_text_sign($is_compact = false) {
		$signature = str_repeat('_', 35).'<br />';
		$this->user['displayName'] = explode(' ', $this->user['displayName']);
		$this->user['displayName'][count($this->user['displayName']) - 1] = strtoupper($this->user['displayName'][count($this->user['displayName']) - 1]);
		$this->user['displayName'] = implode(' ', $this->user['displayName']);
		$signature .= $this->user['displayName'].'<br />';

		if($is_compact == false) {
			$signature .= '<br />';
			$details = $this->prepare_sign_info();

			if(!isset($this->user['positions'])) {
				$this->get_positions();
			}

			if(empty($this->user['legalAffid'])) {
				$this->user['legalAffid'] = $this->user['mainaffiliate_details']['legalName'];
			}
			$signature .= implode(', ', $this->user['positions'])."<br />";
			$signature .= $this->user['legalAffid']."<br />";
			$signature .= preg_replace("/\n/i", '<br />', $details['values']);
		}
		else {
			if(!isset($this->user['mainaffiliate_details'])) {
				$this->user['mainaffiliate_details'] = $this->get_mainaffiliate()->get();
			}
			$signature .= '+'.$this->user['mainaffiliate_details']['phone1'].$this->user['internalExtension'];
		}
		return $signature;
	}

	public function get_errorcode() {
		return $this->errorcode;
	}

}
?>