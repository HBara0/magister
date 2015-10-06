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

class Users extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;
    protected $usergroup = array();

    const PRIMARY_KEY = 'uid';
    const TABLE_NAME = 'users';
    const DISPLAY_NAME = 'displayName';
    const SIMPLEQ_ATTRS = 'uid, username, reportsTo, firstName, middleName, lastName, displayName, displayName AS name, email,gid';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        global $core;
        if(empty($id)) {
            $this->data = $core->user;
            $this->data['uid'] = intval($this->data['uid']);
        }
        else {
            parent::__construct($id, $simple);
        }
    }

    private function read_user($uid = '', $simple = true) {
        global $db;

        if(empty($uid)) {
            $uid = $this->data['uid'];
        }

        $query_select = 'uid, username, reportsTo, firstName, middleName, lastName, displayName, displayName AS name, email';
        if($simple == false) {
            $query_select = '*';
        }

        $this->data = $db->fetch_assoc($db->query("SELECT ".$query_select."
												FROM ".Tprefix."users
												WHERE uid='".intval($uid)."'"));
        if(is_array($this->data) && !empty($this->data)) {
            return true;
        }
        $this->status = 2;
        return false;
    }

    private function read_mainaffiliate() {
        global $db;
        $this->data['mainaffiliate'] = $db->fetch_field($db->query("SELECT affid FROM ".Tprefix."affiliatedemployees WHERE uid='{$this->data['uid']}' AND isMain=1"), 'affid');
    }

    public function read_usergroupsperm($mainonly = false, $replacecore = false) {
        global $db, $core;

        if($mainonly == true) {
            $query_extrawhere = ' AND isMain=1';
        }

        $query = $db->query('SELECT *
                            FROM '.Tprefix.'users_usergroups uug
                            JOIN '.Tprefix.'usergroups ug ON (ug.gid=uug.gid)
                            WHERE uid='.$this->data['uid'].$query_extrawhere.'
                            ORDER BY isMain DESC');
        while($usergroup = $db->fetch_assoc($query)) {
            if($usergroup['isMain'] != 1) {
                unset($usergroup['title'], $usergroup['gid'], $usergroup['defaultModule']);
            }
            else {
                if($replacecore == true) {
                    $core->usergroup = $usergroup;
                    $core->user['gid'] = $usergroup['gid'];
                }
            }

            foreach($usergroup as $permission => $value) {
                if($replacecore == true) {
                    if($core->usergroup[$permission] == 0 && $value == 1) {
                        $core->usergroup[$permission] = 1;
                    }
                }
                if($this->usergroup[$permission] == 0 && $value == 1) {
                    $this->usergroup[$permission] = 1;
                }
            }
        }
    }

    public function get_usergroups($config = array()) {
        global $db;

        $query = $db->query('SELECT uug.gid, uug.isMain, ug.title
					FROM '.Tprefix.'users_usergroups uug
					JOIN '.Tprefix.'usergroups ug ON (ug.gid=uug.gid)
					WHERE uid='.$this->data['uid'].'
					ORDER BY isMain DESC');
        while($usergroup = $db->fetch_assoc($query)) {
            if($config['classified'] == true) {
                if($usergroup['isMain'] == 1) {
                    $usergroups['main'] = $usergroup;
                }
                else {
                    $usergroups['additional'][$usergroup['gid']] = $usergroup;
                }
            }
            else {
                $usergroups[$usergroup['gid']] = $usergroup;
            }
        }

        return $usergroups;
    }

    /* Backward compatibility */
    public static function get_userbyemail($email) {
        if(!is_object($this)) {
            return Users::get_user_byemail($email);
        }
        return $this->get_user_byemail($email);
    }

    public static function get_user_byemail($email) {
        global $db, $core;

        $email = $core->sanitize_email($email);
        if(!$core->validate_email($email)) {
            return false;
        }

        $query = $db->query("SELECT DISTINCT(u.uid)
							FROM ".Tprefix."users u
							LEFT JOIN ".Tprefix."usersemails ue ON (ue.uid=u.uid)
							WHERE u.email='".$db->escape_string($email)."' OR ue.email='".$db->escape_string($email)."'");
        if($db->num_rows($query) > 0) {
            $uid = $db->fetch_field($query, 'uid');
            return new Users($uid);
        }
        else {
            return false;
        }
    }

    public static function get_user_byattr($attr, $value) {
        global $db;

        if(!is_empty($value, $attr)) {
            $id = $db->fetch_field($db->query('SELECT uid FROM '.Tprefix.'users WHERE '.$db->escape_string($attr).'="'.$db->escape_string($value).'"'), 'uid');
            if(!empty($id)) {
                return new Users($id);
            }
        }
        return false;
    }

    public function get_reportsto() {
        if(empty($this->data['reportsTo'])) {
            return false;
        }
        return new Users($this->data['reportsTo']);
    }

    public function get_reportingto() {
        global $db;
        $reportsquery = $db->query("SELECT DISTINCT(uid), reportsTo, username, firstName, middleName, lastName, displayName FROM ".Tprefix."users
			 WHERE reportsTo={$this->data[uid]}");
        while($reporting = $db->fetch_assoc($reportsquery)) {
            $this->data['reportingTo'][] = $reporting;
        }
        return $this->data['reportingTo'];
    }

    public function get_additionaldays_byuser() {
        global $db;
        return $this->data['additionaldays'] = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."attendance_additionalleaves WHERE uid={$this->data[uid]}"));
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
						WHERE affe.canHr=1 {$affiliate_where} AND affe.uid={$this->data[uid]}");
        if($db->num_rows($hrquery) > 0) {
            return true;
        }
        else {
            return false;
        }
    }

    public function get_assistant() {
        return new Users($this->data['assistant']);
    }

// The below has to be in the Affiliates Class
//	public function get_affiliateuser() {
//		global $db;
//		$affemployee_query = $db->query("SELECT affe.aeid,u.displayName,u.uid,u.username FROM affiliatedemployees affe
//										JOIN ".Tprefix."users u ON (u.uid=affe.uid)
//										JOIN ".Tprefix."affiliates aff ON(aff.affid=affe.affid) WHERE affe.affid in('".$this->get_mainaffiliate()->get()['affid']."')");
//		if($db->num_rows($affemployee_query) > 0) {
//			while($affiliate_user = $db->fetch_assoc($affemployee_query)) {
//				$affiliate_users[$affiliate_user['uid']] = $affiliate_user;
//			}
//			return $affiliate_users;
//		}
//	}

    public function get_positions() {
        global $db, $lang;

        $query = $db->query("SELECT name, title FROM ".Tprefix."positions p JOIN ".Tprefix."userspositions up ON (up.posid=p.posid) WHERE uid={$this->data[uid]}");
        while($position = $db->fetch_assoc($query)) {
            if(!isset($lang->{$position['name']})) {
                $lang->{$position['name']} = $position['title'];
            }
            $this->data['positions'][] = $lang->{$position['name']};
        }
        return $this->data['positions'];
    }

    public function get_auditedaffiliates() {
        global $db;

        $query = $db->query('SELECT * FROM '.Tprefix.'affiliatedemployees WHERE uid='.$this->data['uid'].' AND canAudit=1');
        if($db->num_rows($query) > 0) {
            while($affiliate = $db->fetch_assoc($query)) {
                $affiliates[$affiliate['affid']] = new Affiliates($affiliate['affid']);
            }
            return $affiliates;
        }
        return false;
    }

    public function get_leaves() {
        global $db;
        $query = $db->query("SELECT l.lid,l.uid FROM ".Tprefix."leaves l
							JOIN ".Tprefix."leavetypes lt ON(lt.ltid=l.type)
							JOIN ".Tprefix."leavesapproval lap ON(l.lid=lap.lid) WHERE lap.isApproved=1
							AND lt.isBusiness=1 AND l.uid={$this->data[uid]}");
        while($leaves = $db->fetch_assoc($query)) {
            $leav_obj = new Leaves(array('lid' => $leaves['lid']), false);
            $user_leaves[$leaves['lid']] = $leav_obj->get_leavetype()->get();
            $this->data['leaves'] = $user_leaves;
        }
        return $this->data['leaves'];
    }

    public function get_mainaffiliate() {
        global $cache;
        if(!isset($this->data['mainaffiliate']) || empty($this->data['mainaffiliate'])) {
            $this->read_mainaffiliate();
        }

        if(!$cache->iscached('affiliate', $this->data['mainaffiliate'])) {
            $affiliate = new Affiliates($this->data['mainaffiliate'], FALSE);
            $cache->add('affiliate', $affiliate, $affiliate->get_id());
        }
        else {
            $affiliate = $cache->get_cachedval('affiliate', $this->data['mainaffiliate']);
        }

        return $affiliate;
    }

    /* CORRECTIONS NEEDED:
     *  The below should return objects of Segments
     *  There is no need for the join being made
     * Should return false if nothing
     */
    public function get_segments() {
        global $db;

        $query = $db->query("SELECT psid FROM employeessegments WHERE uid=".$this->data['uid']);
        if($db->num_rows($query) > 0) {
            while($segment = $db->fetch_assoc($query)) {
                $segments[$segment['psid']] = new ProductsSegments($segment['psid']);
            }
            return $segments;
        }
    }

    /* CORRECTIONS NEEDED:
     * The below should return objects of Segments
     * There is no need for the join being made
     * Should be renamed to get_coordinatedsegments
     * Should return false if nothing
     */
    public function get_coordinatesegments() {
        global $db;

        $segment_query = $db->query("SELECT psc.pscid,ps.psid,ps.title FROM  ".Tprefix."productsegmentcoordinators psc
									JOIN ".Tprefix."users u on u.uid=psc.uid
									JOIN ".Tprefix."productsegments ps ON (ps.psid=psc.psid) WHERE u.uid=".$this->data['uid']);
        if($db->num_rows($segment_query) > 0) {
            while($segmentcoord = $db->fetch_assoc($segment_query)) {
                $segmentcoords[$segmentcoord['pscid']] = $segmentcoord;
            }
            return $segmentcoords;
        }
    }

    public function get_hrinfo($simple = true) {
        global $db;
        $query_select = '*';
        if($simple == true) {
            $query_select = 'employeeNum, joinDate, jobDescription, firstJobDate';
        }

        $this->data['hrinfo'] = $db->fetch_assoc($db->query("SELECT ".$query_select."
										FROM ".Tprefix."userhrinformation
										WHERE uid='".$this->data['uid']."'"));
        if(is_array($this->data['hrinfo']) && !empty($this->data['hrinfo'])) {
            return $this->data['hrinfo'];
        }
        return false;
    }

    private function prepare_sign_info($seperate_lengend = false) {
        global $lang;
        $lang->load('profile');

        $mainaffiliate = $this->get_mainaffiliate();
        $this->data['mainaffiliate_details'] = $mainaffiliate->get();
        $this->data['mainaffiliate_details']['countryname'] = $mainaffiliate->get_country()->get()['name'];

        if(!empty($this->data['mainaffiliate_details']['addressLine1'])) {
            $info['address'] .= $this->data['mainaffiliate_details']['addressLine1'].', ';
        }

        if(!empty($this->data['mainaffiliate_details']['addressLine2'])) {
            $info['address'] .= $this->data['mainaffiliate_details']['addressLine2'].', ';
        }

        if(!empty($affiliate['postCode'])) {
            $info['address'] .= $this->data['mainaffiliate_details']['postCode'].'  ';
        }

        if(!empty($this->data['mainaffiliate_details']['city'])) {
            $info['address'] .= $mainaffiliate->get_city()->get()['name'].' - '; //ucfirst($this->user['mainaffiliate_details']['city']).' - ';
        }

        $info['address'] .= ucfirst($this->data['mainaffiliate_details']['countryname']);
        $info['tel'] = '+'.$this->data['mainaffiliate_details']['phone1'];
        $info['ext'] = $this->data['internalExtension'];
        $info['fax'] = '+'.$this->data['mainaffiliate_details']['fax'];
        $info['website'] = 'www.orkila.com';
        //$info['bbpin'] = $this->user['bbPin'];
        $info['email'] = $this->data['email'];
        $info['skype'] = $this->data['skype'];

        if($this->data['mobileIsPrivate'] == 0 && !empty($this->data['mobile'])) {
            $info['mob'] = '+'.$this->data['mobile'];
        }

        if($this->data['mobile2IsPrivate'] == 0 && !empty($this->data['mobile2'])) {
            if(!empty($info['mob'])) {
                $info['mob'] .= '/';
            }
            $info['mob'] .= '+'.$this->data['mobile2'];
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
                    if(strpos($this->data['mainaffiliate_details'][$type], "\n") || strpos($this->data[$type], "\n")) {
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
        global $core;
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

            /* Check if banners are to be added */
            $banners_dir_path = './images/signaturebanners/'.$this->get_mainaffiliate()->get()['affid'].'/';
            if(file_exists($banners_dir_path)) {
                $banners_dir = opendir($core->sanitize_path($banners_dir_path));
                while(false !== ($file = readdir($banners_dir))) {
                    $bannerfile_info = pathinfo($banners_dir_path.$file);
                    if($file != '.' && $file != '..' && in_array($bannerfile_info['extension'], array('jpg', 'png'))) {
                        list($bannerwidth, $bannerheight) = getimagesize($banners_dir_path.$file);
                        $height += $bannerheight;
                        $total_bannersheight += $bannerheight;
                        if($width < $bannerwidth) {
                            $width += ($bannerwidth - $width);
                        }
                    }
                }
            }
        }
        else {
            $details['values_bbox'] = imagettfbbox(11, 0, $fonts['arial']['bold'], $this->data['displayName']);
            if(($details['values_bbox'][4] + 65) > $width) {
                $width = $details['values_bbox'][4] + 65;
            }

            $this->data['mainaffiliate_details'] = $this->get_mainaffiliate()->get();
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
            $logo = imagecreatefrompng('./images/signlogo.png');
            imagecopy($im, $logo, 1, 18, 0, 0, 98, 71);
        }
        else {
            $logo = imagecreatefrompng('./images/signlogo_min.png');
            imagecopy($im, $logo, 1, 4, 0, 0, 49, 36);
        }
        /* Parse Logo - End */

        imageline($im, 0, 0, 260, 0, $colors['black']);

        $this->data['displayName'] = explode(' ', $this->data['displayName']);
        $this->data['displayName'][count($this->data['displayName']) - 1] = strtoupper($this->data['displayName'][count($this->data['displayName']) - 1]);
        $this->data['displayName'] = implode(' ', $this->data['displayName']);

        if($is_compact == false) {
            imagefttext($im, 11, 0, 1, 16, $colors['green'], $fonts['arial']['bold'], $this->data['displayName']);
            $this->get_positions();
            imagefttext($im, 9, 0, 1, 98, $colors['salmon'], $fonts['arial']['bolditalic'], implode(', ', $this->data['positions']));
            imagefttext($im, 8.5, 0, 1, 130, $colors['gray'], $fonts['arial']['regular'], $details['values'], array('linespacing' => 1.1));

            if(empty($this->data['legalAffid'])) {
                $this->data['legalAffid'] = $this->data['mainaffiliate_details']['legalName'];
            }
            imagefttext($im, 10, 0, 1, 115, $colors['green'], $fonts['arial']['regular'], $this->data['legalAffid']);
        }
        else {
            imagefttext($im, 10, 0, 49 + 8, 36 / 1.8, $colors['green'], $fonts['arial']['bold'], $this->data['displayName']);
            if(!empty($this->data['internalExtension'])) {
                $this->data['internalExtension'] = ' ext: '.$this->data['internalExtension'];
            }
            else {
                $this->data['internalExtension'] = '';
            }

            $this->data['mainaffiliate_details']['phone1'] = str_replace('-', ' ', $this->data['mainaffiliate_details']['phone1']);
            imagefttext($im, 8, 0, 49 + 8, (36 / 1.8) + 13, $colors['salmon'], $fonts['arial']['regular'], '+'.$this->data['mainaffiliate_details']['phone1'].$this->data['internalExtension']);
        }

        /* Check if banners exist & add them */
        if($is_compact == false) {
            $banners_dir_path = './images/signaturebanners/'.$this->get_mainaffiliate()->get()['affid'].'/';
            if(file_exists($banners_dir_path)) {
                $banners_dir = opendir($core->sanitize_path($banners_dir_path));
                $prevbannerheight = 0;
                while(false !== ($file = readdir($banners_dir))) {
                    $langfile_info = pathinfo($banners_dir_path.$file);
                    if($file != '.' && $file != '..' && in_array($langfile_info['extension'], array('jpg', 'png'))) {
                        list($bannerwidth, $bannerheight) = getimagesize($banners_dir_path.$file);
                        $banner = imagecreatefromjpeg(ROOT.$banners_dir_path.$file);
                        imagecopy($im, $banner, 1, $height - $total_bannersheight + $prevbannerheight, 0, 0, $bannerwidth, $bannerheight);
                        $prevbannerheight = $bannerheight;
                    }
                }
                unset($banner);
            }
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
        $this->data['displayName'] = explode(' ', $this->data['displayName']);
        $this->data['displayName'][count($this->data['displayName']) - 1] = strtoupper($this->data['displayName'][count($this->data['displayName']) - 1]);
        $this->data['displayName'] = implode(' ', $this->data['displayName']);
        $signature .= $this->data['displayName'].'<br />';

        if($is_compact == false) {
            $signature .= '<br />';
            $details = $this->prepare_sign_info();

            if(!isset($this->data['positions'])) {
                $this->get_positions();
            }

            if(empty($this->data['legalAffid'])) {
                $this->data['legalAffid'] = $this->data['mainaffiliate_details']['legalName'];
            }
            $signature .= implode(', ', $this->data['positions'])."<br />";
            $signature .= $this->data['legalAffid']."<br />";
            $signature .= preg_replace("/\n/i", '<br />', $details['values']);
        }
        else {
            if(!isset($this->data['mainaffiliate_details'])) {
                $this->data['mainaffiliate_details'] = $this->get_mainaffiliate()->get();
            }
            $signature .= '+'.$this->data['mainaffiliate_details']['phone1'].$this->data['internalExtension'];
        }
        return $signature;
    }

    public static function get_users($filters = null, $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_email() {
        return $this->email;
    }

    public static function get_activeusers() {
        global $db;

        $allusers_query = $db->query("SELECT uid ".Tprefix."FROM users WHERE gid!=7 ORDER BY displayName ASC");
        if($db->num_rows($allusers_query) > 0) {
            while($user = $db->fetch_assoc($allusers_query)) {
                $users[$user['uid']] = new Users($user['uid']);
            }
            return $users;
        }
        return false;
    }

    public static function get_allusers() {
        return Users::get_activeusers();
    }

    public function get_errorcode() {
        return $this->errorcode;
    }

    public function parse_link($attributes_param = array('target' => '_blank'), $options = array()) {
        if(is_array($attributes_param)) {
            foreach($attributes_param as $attr => $val) {
                $attributes .= $attr.'="'.$val.'"';
            }
        }

        if(!isset($options['outputvar'])) {
            $options['outputvar'] = 'displayName';
        }

        return '<a href="users.php?action=profile&amp;uid='.$this->data['uid'].'" '.$attributes.'>'.$this->data[$options['outputvar']].'</a>';
    }

    public function get_link() {
        global $core;
        return $core->settings['rootdir'].'/users.php?action=profile&amp;uid='.$this->data['uid'];
    }

    protected function create(array $data) {

    }

    protected function update(array $data) {

    }

    public function save(array $data = array()) {

    }

    public function get_joindate() {
        global $db;
        $query_select = '*';
        $joindate = $db->fetch_assoc($db->query("SELECT joinDate
										FROM ".Tprefix."userhrinformation
										WHERE uid='".$this->data['uid']."'"));
        if(is_array($joindate) && !empty($joindate)) {
            return $joindate['joinDate'];
        }
        return false;
    }

    /**
     * Return all permissions that apply to this user taking into consideration all different assignment types
     * @global Cache    $cache
     * @return array    Related permissions
     */
    public function get_businesspermissions() {
        global $cache;
        $this->read_usergroupsperm();

        $affemployees = AffiliatedEmployees::get_data(array('uid' => $this->uid), array('returnarray' => true));
        $assignedemployees = AssignedEmployees::get_data(array('uid' => $this->uid), array('returnarray' => true));
        $segmentscoord = ProdSegCoordinators::get_data(array('uid' => $this->uid), array('returnarray' => true));
        $supplieraudits = SupplierAudits::get_data(array('uid' => $this->uid), array('returnarray' => true));
        $reportingusers = Users::get_users(array('reportsTo' => $this->uid), array('returnarray' => true));

        /**
         * Default set of permissions
         * Empty arrays are left on purpose
         */
        $permissions = array('spid' => array(), 'cid' => array(), 'eid' => array(), 'psid' => array(), 'pid' => array(), 'uid' => array($this->uid), 'affid' => array($this->get_mainaffiliate()->get_id()));

        foreach($affemployees as $affemployee) {
            $affiliate = $affemployee->get_affiliate();
            if($affemployee->canAudit == 1) {
                $affentities = AffiliatedEntities::get_data(array('affid' => $affiliate->get_id()), array('returnarray' => true));
                if(is_array($affentities)) {
                    foreach($affentities as $affentity) {
                        $entity = $affentity->get_entity();
                        $cache->add('entity', $entity, $entity->get_id());

                        $permissions['eid'][] = $entity->get_id();
                        if($entity->is_supplier()) {
                            $permissions['spid'][] = $entity->get_id();
                            $products = Products::get_data(array('spid' => $entity->get_id()), array('returnarray' => true));
                            $cache->add('entityproducts', $products, $entity->get_id());
                            if(is_array($entity)) {
                                $permissions['pid'] = array_keys($products);
                            }
                        }
                        else {
                            $permissions['cid'][] = $entity->get_id();
                        }
                    }
                }

                $affiliateemployees = AssignedEmployees::get_data(array('affid' => $affiliate->get_id()), array('returnarray' => true));
                if(is_array($affiliateemployees)) {
                    foreach($affiliateemployees as $affiliateemployee) {
                        if(!$cache->iscached('user', $affiliateemployee->{Users::PRIMARY_KEY})) {
                            $employee = $affiliateemployee->get_user();
                            $cache->add('user', $employee, $employee->get_id());
                        }
                        else {
                            $employee = $cache->get_cachedval('user', $affiliateemployee->{Users::PRIMARY_KEY});
                        }

                        $permissions['uid'][] = $employee->get_id();
                    }
                }
                unset($affentities);
            }
            else {
                $permissions['affid'][] = $affiliate->get_id();
            }
        }

        if(is_array($supplieraudits)) {
            foreach($supplieraudits as $audit) {
                if(!$cache->iscached('entity', $audit->{Entities::PRIMARY_KEY})) {
                    $entity = $audit->get_entity();
                }
                else {
                    $entity = $cache->get_cachedval('entity', $audit->{Entities::PRIMARY_KEY});
                }

                $permissions['eid'][] = $entity->get_id();
                $permissions['spid'][] = $entity->get_id();

                if(!$cache->iscached('entity', $audit->{Entities::PRIMARY_KEY})) {
                    $products = Products::get_data(array('spid' => $entity->get_id()), array('returnarray' => true));
                    $cache->add('entityproducts', $products, $entity->get_id());
                }
                else {
                    $products = $cache->get_cachedval('entityproducts', $audit->{Entities::PRIMARY_KEY});
                }

                if(is_array($products)) {
                    $permissions['pid'] = array_merge($permissions['pid'], array_keys($products));
                }
            }
        }

        if(is_array($segmentscoord)) {
            foreach($segmentscoord as $segmentcoord) {
                $segment = $segmentcoord->get_segment();
                $permissions['psid'][] = $segment->get_id();

                $generics = GenericProducts::get_data(array('psid' => $segment->get_id()), array('returnarray' => true));
                if(is_array($generics)) {
                    $products = Products::get_data(array('gpid' => array_keys($generics)), array('returnarray' => true));
                    if(is_array($products)) {
                        $permissions['pid'] = array_merge($permissions['pid'], array_keys($products));
                    }
                }
                /**
                 * Get products using applications
                 */
                $applications = SegmentApplications::get_data(array('psid' => $segment->get_id()), array('returnarray' => true));
                if(!is_array($applications)) {
                    continue;
                }
                foreach($applications as $application) {
                    $cache->add('application', $application, $application->get_id());

                    $segappfuncs = $application->get_segappfunctionsobjs();
                    if(!is_array($segappfuncs)) {
                        continue;
                    }
                    $chemfunprods = ChemFunctionProducts::get_data(array('safid' => array_keys($segappfuncs)), array('returnarray' => true));
                    if(!is_array($chemfunprods)) {
                        continue;
                    }
                    foreach($chemfunprods as $chemfunprod) {
                        $permissions['pid'][] = $chemfunprod->{Products::PRIMARY_KEY};
                    }
                }
            }

            $employeesegments = EmployeeSegments::get_data(array('psid' => $segment->get_id()), array('returnarray' => true));
            if(is_array($employeesegments)) {
                foreach($employeesegments as $employeesegment) {
                    if(!$cache->iscached('user', $employeesegment->{Users::PRIMARY_KEY})) {
                        $employee = $employeesegment->get_user();
                        $cache->add('user', $employee, $employee->get_id());
                    }
                    else {
                        $employee = $cache->get_cachedval('user', $employeesegment->{Users::PRIMARY_KEY});
                    }

                    $permissions['uid'][] = $employee->get_id();
                    $affiliate = $employee->get_mainaffiliate();
                    $permissions['affid'][] = $affiliate->get_id();
                }
            }
            unset($employeesegments);

            $entitiesegments = EntitiesSegments::get_data(array('psid' => $segment->get_id()), array('returnarray' => true));
            if(is_array($entitiesegments)) {
                foreach($entitiesegments as $entitysegment) {
                    if(!$cache->iscached('entity', $entitysegment->{Entities::PRIMARY_KEY})) {
                        $entity = $entitysegment->get_entity();
                        $cache->add('entity', $entity, $entity->get_id());
                    }
                    else {
                        $entity = $cache->get_cachedval('entity', $entitysegment->{Entities::PRIMARY_KEY});
                    }
                    $permissions['eid'][] = $entity->get_id();
                    if($entity->is_supplier()) {
                        $permissions['spid'][] = $entity->get_id();
                    }
                    else {
                        $permissions['cid'][] = $entity->get_id();
                    }
                }
            }
        }

        if(is_array($assignedemployees)) {
            foreach($assignedemployees as $assignedemployee) {
                if(is_array($permissions['eid'])) {
                    if(in_array($assignedemployee->{Entities::PRIMARY_KEY}, $permissions['eid'])) {
                        continue;
                    }
                }
                if(!$cache->iscached('entity', $assignedemployee->{Entities::PRIMARY_KEY})) {
                    $entity = $assignedemployee->get_entity();
                    $cache->add('entity', $entity, $entity->get_id());
                }
                else {
                    $entity = $cache->get_cachedval('entity', $assignedemployee->{Entities::PRIMARY_KEY});
                }
                $permissions['eid'][] = $entity->get_id();
                if($entity->is_supplier()) {
                    $permissions['spid'][] = $entity->get_id();
                }
                else {
                    $permissions['cid'][] = $entity->get_id();
                }
            }
        }

        /* Set users who report to the user */
        if(is_array($reportingusers)) {
            $permissions['uid'] = array_merge($permissions['uid'], array_keys($reportingusers));
        }

        /* Unique the values */
        foreach($permissions as $type => $values) {
            if(is_array($values)) {
                $permissions[$type] = array_unique($values);
            }
        }

        /**
         * If a permission has no value then it means the user has not been assigned to any
         * system therefore, zero out the value to avoid it being consider as having all permissions
         */
        foreach($permissions as $key => $perm) {
            if(empty($permissions[$key])) {
                $permissions[$key] = array(0);
            }
        }

        /**
         * If user has global permission, remove the permissions values
         */
        if($this->usergroup['canViewAllAff'] == 1) {
            unset($permissions['affid']);
        }

        if($this->usergroup['canViewAllSupp'] == 1) {
            unset($permissions['spid']);
        }
        if($this->usergroup['canViewAllCust'] == 1) {
            unset($permissions['cid']);
        }

        return $permissions;
    }

    /**
     * Get the object of the user from the integration class
     * If no value is defined in the local users table, the system falls back to checking remote table by name
     * @global type $integration
     * @return \IntegrationOBUser|boolean
     */
    public function get_integrationObUser() {
        global $integration;
        if(class_exists('IntegrationOB', true)) {
            if(!empty($this->integrationOBId)) {
                return new IntegrationOBUser($this->integrationOBId, $integration->get_dbconn());
            }

            /**
             * Attempt to get by display name
             */
            $intuser = IntegrationOBUser::get_data('name=\''.$this->displayName.'\' OR (firstname=\''.$this->firstName.'\' AND lastname=\''.$this->lastName.'\')');
            if(is_object($intuser)) {
                return $intuser;
            }
            else {
                if(is_array($intuser)) {
                    return current($intuser);
                }
            }

            return false;
        }
    }

}
?>