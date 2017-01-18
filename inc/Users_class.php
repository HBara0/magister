<?php

class Users extends AbstractClass {

    protected $data = array();
    protected $errorcode = 0;
    protected $usergroup = array();

    const PRIMARY_KEY = 'uid';
    const TABLE_NAME = 'users';
    const DISPLAY_NAME = 'displayName';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        global $core;
        if (empty($id)) {
            $this->data = $core->user;
            $this->data['uid'] = intval($this->data['uid']);
        }
        else {
            parent::__construct($id, $simple);
        }
    }

    private function read_user($uid = '', $simple = true) {
        global $db;

        if (empty($uid)) {
            $uid = $this->data['uid'];
        }

        $query_select = 'uid, username, reportsTo, firstName, middleName, lastName, displayName, displayName AS name, email';
        if ($simple == false) {
            $query_select = '*';
        }

        $this->data = $db->fetch_assoc($db->query("SELECT " . $query_select . "
												FROM " . Tprefix . "users
												WHERE uid='" . intval($uid) . "'"));
        if (is_array($this->data) && !empty($this->data)) {
            return true;
        }
        $this->status = 2;
        return false;
    }

    public function read_usergroupsperm($mainonly = false, $replacecore = false) {
        global $db, $core;

        if ($mainonly == true) {
            $query_extrawhere = ' AND isMain=1';
        }
        $usergroup_obj = new UserGroups($core->user_obj->gid, false);
        if (!is_object($usergroup_obj) || !$usergroup_obj->get_id()) {
            error($lang->sectionnopermission);
        }
        $core->user['gid'] = $usergroup_obj->get_id();
        $usergroup = $usergroup_obj->get();
        foreach ($usergroup as $permission => $value) {
            $core->usergroup[$permission] = $value;
        }
    }

    public function get_usergroups($config = array()) {
        global $db;

        $query = $db->query('SELECT uug.gid, uug.isMain, ug.title
					FROM ' . Tprefix . 'users_usergroups uug
					JOIN ' . Tprefix . 'usergroups ug ON (ug.gid=uug.gid)
					WHERE uid=' . $this->data['uid'] . '
					ORDER BY isMain DESC');
        while ($usergroup = $db->fetch_assoc($query)) {
            if ($config['classified'] == true) {
                if ($usergroup['isMain'] == 1) {
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
        if (!is_object($this)) {
            return Users::get_user_byemail($email);
        }
        return $this->get_user_byemail($email);
    }

    public static function get_user_byemail($email) {
        global $db, $core;

        $email = $core->sanitize_email($email);
        if (!$core->validate_email($email)) {
            return false;
        }

        $query = $db->query("SELECT DISTINCT(u.uid)
							FROM " . Tprefix . "users u
							LEFT JOIN " . Tprefix . "usersemails ue ON (ue.uid=u.uid)
							WHERE u.email='" . $db->escape_string($email) . "' OR ue.email='" . $db->escape_string($email) . "'");
        if ($db->num_rows($query) > 0) {
            $uid = $db->fetch_field($query, 'uid');
            return new Users($uid);
        }
        else {
            return false;
        }
    }

    public static function get_user_byattr($attr, $value) {
        global $db;

        if (!is_empty($value, $attr)) {
            $id = $db->fetch_field($db->query('SELECT uid FROM ' . Tprefix . 'users WHERE ' . $db->escape_string($attr) . '="' . $db->escape_string($value) . '"'), 'uid');
            if (!empty($id)) {
                return new Users($id);
            }
        }
        return false;
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

        $allusers_query = $db->query("SELECT uid " . Tprefix . "FROM users WHERE gid!=7 ORDER BY displayName ASC");
        if ($db->num_rows($allusers_query) > 0) {
            while ($user = $db->fetch_assoc($allusers_query)) {
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
										FROM " . Tprefix . "userhrinformation
										WHERE uid='" . $this->data['uid'] . "'"));
        if (is_array($joindate) && !empty($joindate)) {
            return $joindate['joinDate'];
        }
        return false;
    }

    /**
     * Return all permissions that apply to this user taking into consideration all different assignment types
     * @global Cache    $cache
     * @return array    Related permissions
     */
    /**
     * Get the object of the user from the integration class
     * If no value is defined in the local users table, the system falls back to checking remote table by name
     * @global type $integration
     * @return \IntegrationOBUser|boolean
     */

    /**
     * this function only differs from its sister by the end output array which has another layer, the affid
     * @global type $core
     * @return boolean
     */
    public function generate_apikey() {
        return md5($this->data['uid'] . $this->data['username'] . $this->data['salt']);
    }

    public function save_apikey() {
        global $db;
        $key = $this->generate_apikey();
        if ($key) {
            $db->update_query(self::TABLE_NAME, array('apiKey' => $key), self::PRIMARY_KEY . '=' . intval($this->data[self::PRIMARY_KEY]));
        }
    }

    public function get_usergroup() {
        $usergroup = new UserGroups($this->data['gid']);
        return $usergroup;
    }

    /**
     * Get all users that are allowed to teach a course
     * @return boolean/array of User objs11
     */
    public function get_teachers() {
        //get all usergroups that are allowed to teach
        $teachusergroup_objs = UserGroups::get_column('gid', array('canManageLecture' => 1), array('returnarray' => true));
        if (!is_array($teachusergroup_objs)) {
            return false;
        }
        $teachuser_objs = Users::get_data("gid !=5 AND gid IN (" . implode(',', $teachusergroup_objs) . ")", array('returnarray' => true));
        if (!is_array($teachuser_objs)) {
            return false;
        }
        return $teachuser_objs;
    }

    /**
     * Get all students that are students
     * @return boolean
     */
    public function get_students() {
        //get all usergroups that are allowed to teach
        $studentgroup_objs = UserGroups::get_column('gid', array('canTakeLessons' => 1), array('returnarray' => true));
        if (!is_array($studentgroup_objs)) {
            return false;
        }
        $student_objs = Users::get_data("gid !=5 AND gid IN (" . implode(',', $studentgroup_objs) . ")", array('returnarray' => true));
        if (!is_array($student_objs)) {
            return false;
        }
        return $student_objs;
    }

    public function isActive() {
        if ($this->data['gid'] == 5) {
            return false;
        }
        return true;
    }

}

?>