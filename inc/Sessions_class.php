<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Sessions Class
 * $id: Sessions_class.php
 * Last Update: @zaher.reda 	August 30, 2012 | 12:27 PM
 */

class Sessions {
    public $sid, $uid, $ipaddress;

    public function __construct() {
        global $db, $core;
        $this->cleanup();
        $tokenpass = false;
        if($core->input['module'] == 'crm/addcalllog' && !empty($core->input['apiKey'])) {
            $user = Users::get_data(array('apiKey' => $core->input['apiKey']), array('returnarray' => false, 'simple' => false));
            if(is_object($user)) {
                $tokenpass = true;
                $uid = $user->uid;
            }
        }
        if(!isset($core->cookies['sid'])) {
            $this->create($uid);
        }
        else {
            $this->update();
        }

        if($this->uid != 0) {
            $core->user_obj = new Users($this->uid, FALSE);
            if($core->user_obj) {
                $core->user = $core->user_obj->get();
                unset($core->user['password'], $core->user['salt']);
                $core->user_obj->read_usergroupsperm(false, true);
                /* $auditing = $db->query("SELECT eid FROM ".Tprefix."suppliersaudits WHERE uid='".$this->uid."'");
                  if($db->num_rows($auditing) > 0) {
                  while($auditfor = $db->fetch_assoc($auditing)) {
                  $core->user['auditfor'][] = $auditfor['eid'];
                  }
                  }

                  if($core->usergroup['canViewAllAff'] == 0) {
                  $affiliates = get_specificdata("affiliatedemployees", "affid", "affid", "affid", '', 0, "uid='".$this->uid."'");
                  if(!is_array($affiliates)) {
                  $suppliers = array(0);
                  }
                  $core->user['affiliates'] = $affiliates;

                  if(is_array($core->user['auditfor'])) {
                  foreach($core->user['auditfor'] as $key => $val) {
                  $core->user['auditedaffiliates'][$val] = get_specificdata("affiliatedentities", "affid", "affid", "affid", '', 0, "eid='{$val}'");
                  }
                  }

                  }
                 */
                /* $suppliers = get_specificdata("assignedemployees", "eid", "eid", "eid", '', 0, "uid='".$this->uid."'");
                  if(!is_array($suppliers)) {
                  $suppliers = array(0);
                  }
                  $core->user['suppliers'] = $suppliers; */

                /* if($core->usergroup['canViewAllCust'] == 0 || $core->usergroup['canViewAllSupp'] == 0) {
                  $entities = $db->query("SELECT ae.eid, ae.affid, e.type FROM ".Tprefix."assignedemployees ae LEFT JOIN ".Tprefix."entities e ON (e.eid=ae.eid) WHERE ae.uid='".$this->uid."'");
                  if($db->num_rows($entities) > 0) {
                  while($entity = $db->fetch_assoc($entities)) {
                  if($entity['type'] == 's') {
                  $core->user['suppliers']['eid'][] = $entity['eid'];
                  $core->user['suppliers']['affid'][$entity['eid']][] = $entity['affid'];
                  }
                  elseif($entity['type'] == 'c') {
                  $core->user['customers'][] = $entity['eid'];
                  }
                  }
                  }
                  else
                  {
                  $core->user['suppliers'] = array(0);
                  $core->user['customers'] = array(0);
                  }
                  } */
                $core->user += get_user_business_assignments($this->uid); //parse_userentities_data($this->uid);

                if(!isset($core->user['mainaffiliate'])) {
                    $core->user['mainaffiliate'] = $core->user_obj->get_mainaffiliate()->get()['affid'];
                }
            }
        }
    }

    protected function cleanup() {
        global $db, $core;

        $limit = TIME_NOW - (60 * $core->settings['idletime']);
        $db->delete_query('sessions', "time<$limit");
    }

    protected function create($uid = '') {
        global $db, $core;

        $this->sid = md5(uniqid(microtime()));
        if(empty($uid)) {
            $uid = 0;
        }
        $this->uid = $uid;
        $this->ipaddress = userip();

        $this->create_dbsession();

        $this->create_cookie('sid', $this->sid, (TIME_NOW + (60 * $core->settings['idletime'])));
        $this->create_cookie('uid', $this->uid, (TIME_NOW + (60 * $core->settings['idletime'])));
    }

    protected function update() {
        global $db, $core;
        $this->cleanup();

        $this->sid = $db->escape_string($core->cookies['sid']);
        $this->uid = $db->escape_string($core->cookies['uid']);
        $this->ipaddress = userip();

        $this->authenticate_cookie();

        $session_data = array(
                'uid' => $this->uid,
                'time' => TIME_NOW
        );

        $session_information = $db->fetch_assoc($db->query("SELECT uid, time, ip FROM ".Tprefix."sessions WHERE sid='".$this->sid."' ORDER BY time DESC LIMIT 0,1"));
        if($session_information['uid'] > 0) {
            /* if($session_information['ip'] != $this->ipaddress) {
              unset($core->user);
              $db->delete_query('sessions', "sid='".$this->sid."'");
              $this->create_cookie('sid', '', (TIME_NOW - 3600));
              $this->create_cookie('uid', '', (TIME_NOW - 3600));
              $this->create_cookie('loginKey', '', (TIME_NOW - 3600));
              $this->uid = 0;

              return false;
              } */

            if((TIME_NOW - $session_information['time']) > (60 * 2)) {
                $db->update_query('users', array('lastVisit' => TIME_NOW), "uid='".$this->uid."'");
                $db->update_query('sessions', array('time' => TIME_NOW), "sid='".$this->sid."'");
                $this->create_cookie('sid', $this->sid, (TIME_NOW + (60 * $core->settings['idletime']))); //60*SESSION_EXPIRE
                $this->create_cookie('uid', $this->uid, (TIME_NOW + (60 * $core->settings['idletime'])));
            }
        }

        if($this->uid != $session_information['uid']) {
            $query = $db->update_query('sessions', $session_data, "sid='".$this->sid."'");
            if($db->affected_rows() == 0) {
                $this->create_dbsession();
            }
        }
    }

    private function authenticate_cookie() {
        global $core, $db;
        if(empty($core->cookies['loginKey'])) {
            unset($core->user);
            $this->uid = 0;
            //$this->create();
            return false;
        }
        else {
            if(empty($this->uid) || $core->cookies['loginKey'] != $db->fetch_field($db->query("SELECT loginKey FROM ".Tprefix."users WHERE uid={$this->uid}"), 'loginKey')) {
                unset($core->user);
                $this->uid = 0;
                return false;
            }
        }
    }

    private function create_dbsession() {
        global $db;

        $session_data = array(
                'sid' => $this->sid,
                'uid' => $this->uid,
                'time' => TIME_NOW,
                'ip' => $this->ipaddress
        );
        $db->insert_query('sessions', $session_data);
    }

    public function name_phpsession($name = '') {
        if(empty($name)) {
            $name = COOKIE_PREFIX.'session_'.substr(md5(uniqid(microtime())), 1, 10);
        }
        return session_name($name);
    }

    public function id_phpsession($id = '') {
        return session_id($id);
    }

    public function regenerate_id_phpsession($delete_old = false) {
        return session_regenerate_id($delete_old);
    }

    public function start_phpsession($ttl = '') {
        global $core;

        if(empty($ttl)) {
            $ttl = $core->settings['idletime'];
        }

        session_set_cookie_params((60 * $ttl), COOKIE_PATH, COOKIE_DOMAIN);
        session_start();
    }

    public function set_phpsession(array $values) {
        if(is_array($values)) {
            foreach($values as $key => $val) {
                $_SESSION[$key] = $val;
            }
        }
    }

    public function get_phpsession($index) {
        return $_SESSION[$index];
    }

    public function isset_phpsession($index) {
        return isset($_SESSION[$index]);
    }

    public function destroy_phpsession($fully = true) {
        if($fully === true) {
            if(isset($core->cookies[$this->name_phpsession()])) {
                $this->create_cookie($this->name_phpsession(), '', TIME_NOW - 42000);
            }
        }
        session_destroy();
    }

    public function generate_token() {
        $this->token = md5(uniqid(microtime(), true));
        return $this->token;
    }

    public function is_validtoken() {
        global $core;

        if(empty($this->token)) {
            $this->token = $this->get_phpsession('token');
        }

        if(empty($this->token) || $core->input['token'] != $this->token) {
            return false;
        }

        return true;
    }

    protected function create_cookie($name, $value = '', $expire = NULL, $secure = false, $httponly = false) {
        if(!defined(COOKIE_PATH)) {
            define(COOKIE_PATH, '/');
        }

        if($expire == -1 || $expire === NULL) {
            $expire = 0;
        }
        else {
            $expire += TIME_NOW;
        }

        setcookie(COOKIE_PREFIX.$name, $value, $expire, COOKIE_PATH, COOKIE_DOMAIN, $secure, $httponly);
    }

}
?>