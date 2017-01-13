<?php

class ModifyAccount extends Accounts {

    public $status = false;

    public function __construct($data) {
        if (is_array($data)) {
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
        global $core, $db;

        if (empty($uid)) {
            $uid = $this->data['uid']; /* to be implemented */
        }
        $db->insert_query('users_passwordarchive', array('uid' => $uid, 'password' => md5($password), 'archiveTime' => TIME_NOW));

        /* Maintain last X passwords - START */
        $query = $db->query('SELECT upaid FROM ' . Tprefix . 'users_passwordarchive WHERE uid=' . intval($uid) . ' ORDER BY archiveTime DESC LIMIT ' . $core->settings['passwordArchiveRetention'] . ', ' . ($core->settings['passwordArchiveRetention'] + 1));
        if ($db->num_rows($query) > 0) {
            while ($archived_password = $db->fetch_assoc($query)) {
                $existing_passwords[] = $archived_password['upaid'];
            }

            $db->delete_query('users_passwordarchive', 'upaid IN (' . implode(', ', $existing_passwords) . ')');
        }
        /* Maintain last X passwords - END */
    }

    private function perform_modify(array $data) {
        global $db, $core, $lang;

        if (empty($data['uid'])) {
            output_xml("<status>false</status><message>{$lang->wrongid}</message>");
            exit;
        }

        $uid = $db->escape_string($data['uid']);


        if (array_key_exists('username', $data)) {
            $username = $db->fetch_field($db->query("SELECT username FROM " . Tprefix . "users WHERE uid='{$uid}'"), 'username');
            if ($username != $data['username']) {
                if (parent::username_exists($data['username'])) {
                    output_xml("<status>false</status><message>{$lang->usernameexists}</message>");
                    exit;
                }
            }
        }

        if (array_key_exists('password', $data)) {
            if (!empty($data['password'])) {
                if (!parent::validate_password_complexity($data['password'])) {
                    output_xml("<status>false</status><message>{$lang->pwdpatternnomatch}</message>");
                    exit;
                }
                /* Check if password was used before */

                $data['salt'] = parent::create_salt();
                $data['password'] = parent::create_password($data['password'], $data['salt']);
                $data['loginKey'] = parent::create_loginkey();
            }
            else {
                unset($data['password']);
            }
        }

        if (isset($data['email'])) {
            if ($core->validate_email($data['email'])) {
                $data['email'] = $core->sanitize_email($data['email']);
            }
            else {
                output_xml("<status>false</status><message>{$lang->invalidemail}</message>");
                exit;
            }
        }
        if ($data['firstName'] && $data['lastName']) {
            $data['displayName'] = $data['firstName'] . ' ' . $data['lastName'];
        }



        $query = $db->update_query('users', $data, "uid='{$uid}'");
        if ($query) {
            $this->set_status(true);
        }
        else {
            $this->set_status(false);
        }
    }

}

?>