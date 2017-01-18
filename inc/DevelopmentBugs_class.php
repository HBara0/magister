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
    private $errorcode = 0;

    const PRIMARY_KEY = 'dbid';
    const TABLE_NAME = 'development_bugs';
    const DISPLAY_NAME = 'summary';

    public function __construct($id = null, $simple = true) {
        if (empty($id)) {
            return false;
        }
        $this->read($id, $simple);
    }

    private function read($id, $simple) {
        global $db;

        $query_select = '*';
        if ($simple == true) {
            $query_select = self::PRIMARY_KEY . ', ' . self::DISPLAY_NAME . ', module, moduleFile, affectedVersion, severity, priority, reportedOn, status, isFixed';
        }
        elseif (is_string($simple) && !empty($simple)) {
            $query_select = $db->escape_string($simple);
        }
        $this->data = $db->fetch_assoc($db->query('SELECT ' . $query_select . ' FROM ' . Tprefix . self::TABLE_NAME . ' WHERE ' . self::PRIMARY_KEY . '=' . intval($id)));
    }

    public function update(array $data = array()) {
        global $core, $db;

        $this->lastoperation = 'update';

        if ($data['isFixed'] == 1) {
            $data['status'] = 'resolved';
        }

        $data['modifiedOn'] = TIME_NOW;
        $data['modifiedBy'] = $core->user['uid'];
        if (is_array($data['description'])) {
            $data['description'] = serialize($data['description']);
        }

        $query = $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY . '=' . intval($this->data[self::PRIMARY_KEY]));
        if ($query) {
            return $this;
        }
        $this->errorcode = 601;
        return false;
    }

    public function create(array $data = array()) {
        global $core, $db;

        $this->lastoperation = 'create';

        $module = explode('/', $this->detect_module());
        $defaults = array('affectedVersion' => SYSTEMVERSION, 'status' => 'open', 'priority' => 'normal', 'severity' => 'normal', 'module' => $module[0], 'moduleFile' => $module[1], 'reportedOn' => TIME_NOW, 'sessionUser' => $core->user_obj->uid);

        if (!isset($data['sessionUser'])) {
            $data['sessionUser'] = $defaults['sessionUser'];
        }

        if (!isset($data['reportedBy'])) {
            $data['reportedBy'] = $defaults['sessionUser'];
        }

        if (!isset($data['reportedOn'])) {
            $data['reportedOn'] = TIME_NOW;
        }

        foreach ($defaults as $attr => $default_val) {
            if (!isset($data[$attr]) || empty($data[$attr])) {
                $data[$attr] = $default_val;
            }
        }

        if (!isset($data['summary'])) {
            $data['summary'] = $this->generate_summary();
        }

        if (is_array($data['description'])) {
            $data['description'] = serialize($data['description']);
        }
        $query = $db->insert_query(self::TABLE_NAME, $data);
        if ($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
            return $this;
        }
        return false;
    }

    public function save(array $data = array()) {
        if (empty($data)) {
            $data = $this->data;
        }

        if (value_exists(self::TABLE_NAME, self::PRIMARY_KEY, $this->data[self::PRIMARY_KEY])) {
            return $this->update($data);
        }
        else {
            if (!isset($data['summary']) || empty($data['summary'])) {
                $this->data['summary'] = $data['summary'] = $this->generate_summary();
            }

            $bug = self::get_bug_byattr('summary', $data['summary']);
            if (is_object($bug)) {
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

    public static function get_bugs($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function send() {
        global $lang, $core;

        if ($this->lastoperation == 'update' && $this->sendcriterion == 'createonly') {
            return;
        }
//
//        $mailer = new Mailer();
//        $mailer = $mailer->get_mailerobj();
//
//        $msg_content = array('summary', 'description', 'line', 'file', 'stackTrace');
//        $mailer->set_from(array('name' => 'Magister Bugs Reporter', 'email' => 'bugs@magister.esiee.com'));
//        $mailer->set_subject('[Bug #' . $this->data[self::PRIMARY_KEY] . '] ' . $this->data[self::DISPLAY_NAME]);
//        foreach ($msg_content as $item) {
//            if (!isset($lang->{$item})) {
//                $lang->{$item} = ucfirst($item);
//            }
//
//            if ($item == 'stackTrace') {
//                $this->data[$item] = $this->parse_stack(unserialize($this->data[$item]));
//            }
//            $message .= $lang->{$item} . ': ' . $this->data[$item] . "\r\n<br />";
//        }
//
//        $message .= $lang->username . ': ' . $this->get_user()->displayName . "\r\n<br />";
//        $message .= 'Link: ' . $this->parse_link();
//        $mailer->set_message($message);
//        $mailer->set_to(explode(';', $core->settings['bugnotificationcontacts']));
//
//        $mailer->send();
    }

    private function generate_summary() {
        return '[' . $this->detect_module() . '][Version: ' . SYSTEMVERSION . '] Error Line: ' . $this->data['line'];
    }

    private function detect_module() {
        global $core;
        $module = $core->input['module'];

        if (!isset($module) || empty($module)) {
            $module = 'general/' . basename(basename($_SERVER['PHP_SELF']), '.php');
        }

        return $module;
    }

    private function parse_stack($stack = null) {
        if (empty($stack)) {
            $stack = $this->data['stackTrace'];
        }

        if (!is_array($stack)) {
            $stack = unserialize($stack);
        }
        if (is_array($stack)) {
            foreach ($stack as $step => $data) {
                $stack_output .= '<br />Stack: ' . $step . '<br />';
                foreach ($data as $item => $value) {
                    $stack_output .= ' ' . $item . ': ' . $value . '<br />';
                }
            }
            return $stack_output;
        }
        return false;
    }

    public function set(array $data) {
        foreach ($data as $name => $value) {
            $this->data[$name] = $value;
        }
        return $this;
    }

    public function get_relatedreq() {
        return new Requirements($this->data['relatedRequirement']);
    }

    public function get_user() {
        return new Users($this->data['sessionUser']);
    }

    public function get_assigneduser() {
        return new Users($this->data['assignedTo']);
    }

    public function __get($name) {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        return false;
    }

    public function __isset($name) {
        return isset($this->data[$name]);
    }

    public function get() {
        return $this->data;
    }

    public function get_errorcode() {
        return $this->errorcode;
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        if (is_array($attributes_param)) {
            foreach ($attributes_param as $attr => $val) {
                $attributes .= $attr . '="' . $val . '"';
            }
        }
        return '<a href="index.php?module=development/viewbug&id=' . $this->data[self::PRIMARY_KEY] . '" ' . $attributes . '>' . $this->data[self::DISPLAY_NAME] . '</a>';
    }

}

?>