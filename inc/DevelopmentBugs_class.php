<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * Development Bugs Class
 * $id: DevelopmentBugs_class.php
 * Created:        @zaher.reda    Jun 14, 2014 | 10:42:15 PM
 * Last Update:    @zaher.reda    Jun 14, 2014 | 10:42:15 PM
 */

/**
 * Description of DevelopmentBugs_class
 *
 * @author zaher.reda
 */
class DevelopmentBugs {
    private $data = null;
    private $sendcriterion = 'createonly';
    private $lastoperation = null;

    const PRIMARY_KEY = 'dbid';
    const TABLE_NAME = 'development_bugs';
    const DISPLAY_NAME = 'summary';

    public function __construct($id = null, $simple = true) {
        if(empty($id)) {
            return false;
        }
        $this->read($id, $simple);
    }

    private function read($id, $simple) {
        global $db;

        $query_select = '*';
        if($simple == true) {
            $query_select = self::PRIMARY_KEY.', '.self::DISPLAY_NAME.', module, affectedVersion';
        }
        $this->data = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function update(array $data = array()) {
        global $core, $db;

        $this->lastoperation = 'update';
        //$static_fields = array('');
        //unset($data[self::PRIMARY_KEY]);
//        /$data['modifiedOn'] = TIME_NOW;
        //$db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.'='.$this->data[self::PRIMARY_KEY]);

        return $this;
    }

    public function create(array $data = array()) {
        global $core, $db;

        $this->lastoperation = 'create';

        $module = explode('/', $this->detect_module());
        $defaults = array('affectedVersion' => SYSTEMVERSION, 'status' => 'open', 'priority' => 'normal', 'severity' => 'normal', 'module' => $module[0], 'moduleFile' => $module[1], 'reportedOn' => TIME_NOW, 'sessionUser' => $core->user_obj->uid);

        if(!isset($data['sessionUser'])) {
            $data['sessionUser'] = $defaults['sessionUser'];
        }

        if(!isset($data['reportedOn'])) {
            $data['reportedOn'] = TIME_NOW;
        }

        foreach($defaults as $attr => $default_val) {
            if(!isset($data[$attr]) || empty($data[$attr])) {
                $data[$attr] = $default_val;
            }
        }

        if(!isset($data['summary'])) {
            $data['summary'] = $this->generate_summary();
        }

        if(is_array($data['description'])) {
            $data['description'] = serialize($data['description']);
        }
        $query = $db->insert_query(self::TABLE_NAME, $data);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
            return $this;
        }
        return false;
    }

    public function save(array $data = array()) {
        if(empty($data)) {
            $data = $this->data;
        }

        if(!isset($data['summary']) || empty($data['summary'])) {
            $this->data['summary'] = $data['summary'] = $this->generate_summary();
        }

        if(value_exists(self::TABLE_NAME, self::PRIMARY_KEY, $this->data[self::PRIMARY_KEY])) {
            return $this->update($data);
        }
        else {
            $bug = self::get_bug_byattr('summary', $data['summary']);
            if(is_object($bug)) {
                $this->{self::PRIMARY_KEY} = $bug->{self::PRIMARY_KEY};
                unset($bug);
                return $this->update($data);
            }
            return $this->create($data);
        }
    }

    public static function get_bug_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public function send() {
        global $lang, $core;

        if($this->lastoperation == 'update' && $this->sendcriterion == 'createonly') {
            return;
        }

        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();

        $msg_content = array('summary', 'description', 'line', 'file', 'stackTrace');
        $mailer->set_from(array('name' => 'OCOS Bugs Reporter', 'email' => 'bugs@ocos.orkila.com'));
        $mailer->set_subject('[Bug #'.$this->data[self::PRIMARY_KEY].'] '.$this->data[self::DISPLAY_NAME]);

        foreach($msg_content as $item) {
            if(!isset($lang->{$item})) {
                $lang->{$item} = ucfirst($item);
            }

            if($item == 'stackTrace') {
                $this->data[$item] = $this->parse_stack(unserialize($this->data[$item]));
            }
            $message .= $lang->{$item}.': '.$this->data[$item]."\r\n<br />";
        }

        $message .= $lang->username.': '.$this->get_user()->displayName;
        $mailer->set_message($message);
        $mailer->set_to(explode(';', $core->settings['bugnotificationcontacts']));

        $mailer->send();
    }

    private function generate_summary() {
        return '['.$this->detect_module().'][Version: '.SYSTEMVERSION.'] Error Line: '.$this->data['line'];
    }

    private function detect_module() {
        global $core;
        $module = $core->input['module'];

        if(!isset($module) || empty($module)) {
            $module = 'general/'.basename(basename($_SERVER['PHP_SELF']), '.php');
        }

        return $module;
    }

    private function parse_stack($stack = null) {
        if(empty($stack)) {
            $stack = $this->data['stackTrace'];
        }

        if(!is_array($stack)) {
            $stack = unserialize($stack);
        }
        if(is_array($stack)) {
            foreach($stack as $step => $data) {
                $stack_output .= '<br />Stack: '.$step.'<br />';
                foreach($data as $item => $value) {
                    $stack_output .= ' '.$item.': '.$value.'<br />';
                }
            }
            return $stack_output;
        }
        return false;
    }

    public function set(array $data) {
        foreach($data as $name => $value) {
            $this->data[$name] = $value;
        }
        return $this;
    }

    public function get_user() {
        return new Users($this->data['user']);
    }

    public function __get($name) {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }
        return false;
    }

    public function get() {
        return $this->data;
    }

}