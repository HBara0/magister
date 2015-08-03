<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Integration class to manage integration with 3rd party applications
 * $id: integration.php
 * Created:        @zaher.reda    Feb 18, 2013 | 12:03:10 PM
 * Last Update:    @zaher.reda    Feb 18, 2013 | 12:03:10 PM
 */

class Integration {
    protected $period = array('from' => '30 minutes ago', 'to' => 'tomorrow');
    protected $affiliates_index = array();
    protected $f_db;
    protected $foreign_system;
    private $status = 0;

    public function __construct($foreign_system, array $database_info) {
        if(!isset($database_info['engine'])) {
            $this->status = 702;
            return false;
        }

        if(!empty($foreign_system)) {
            $this->set_foreign_system($foreign_system);
        }
        else {
            $this->status = 100601;
            return false;
        }

        return $this->connect($database_info);
    }

    private function connect(array $database_info) {
        switch($database_info['engine']) {
            case 'pg':
            case 'pgsql':
            case 'postgre':
            case 'postgreSQL':
                $this->f_db = new PostgreSQLConnection($database_info['database'], $database_info['hostname'], $database_info['username'], $database_info['password']);
                break;
            case 'mySQL':
                $this->f_db = new MySQLConnection($database_info['database'], $database_info['hostname'], $database_info['username'], $database_info['password']);
                break;
            case 'mySQLi':
                $this->f_db = new MySQLiConnection($database_info['database'], $database_info['hostname'], $database_info['username'], $database_info['password']);
                break;
            default: $this->status = 702;
                return false;
        }

        return true;
    }

    public function set_sync_interval(array $sync_perid) {
        if(!empty($sync_perid['from'])) {
            $this->period['from'] = $sync_perid['from'];
        }

        if(!empty($sync_perid['to'])) {
            $this->period['to'] = $sync_perid['to'];
        }
        return true;
    }

    private function set_foreign_system($foreign_system) {
        $this->foreign_system = intval($foreign_system);
    }

    public function match_affiliates_ids(array $ids) {
        foreach($ids as $fkey => $key) {
            if(empty($key)) {
                continue;
            }
            $this->affiliates_index[$fkey] = $key;
        }
        return true;
    }

}

class IntegrationDataAccessLayer {
    private $primary_key = null;
    private $table_name = null;
    private $class = null;
    private $data = array();
    protected $f_db = null;

    public function __construct($class, $table, $primary_key, $f_db = null) {
        global $intgdb;
        $this->table_name = $table;
        $this->primary_key = $primary_key;
        $this->class = $class;

        $this->f_db = $intgdb;
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
    }

    public function get_objects($filters = null, array $configs = array()) {
        global $integration;

        if(!isset($configs['simple'])) {
            $configs['simple'] = true;
        }
        $items = array();
        $sql = 'SELECT '.$this->primary_key.' FROM '.Tprefix.$this->table_name;

//        if(is_array($filters) && !empty($filters)) {
//            $andor = ' WHERE ';
//            foreach($filters as $attr => $value) {
//                if(!isset($configs['operators'][$attr]) || empty($configs['operators'][$attr])) {
//                    $configs['operators'][$attr] = '=';
//                }
//
//                if(is_array($value)) {
//                    if($configs['operators'][$attr] == 'like') {
//
//                    }
//                    if($configs['operators'][$attr] == 'BETWEEN') {
//                        $sql .= $andor.$attr.' BETWEEN '.$value[0].' AND '.$value[1];
//                    }
//                    else {
//                        $value_numerichk = array_filter($value, 'is_numeric');
//                        if($value_numerichk == $value) {
//                            $value = array_map(intval, $value);
//                            $sql .= $andor.$attr.' IN ('.implode(',', $value).')';
//                        }
//                        else {
//                            $value = array_map($this->f_db->escape_string, $value);
//                            $sql .= $andor.$attr.' IN ("'.implode('","', $value).'")';
//                        }
//                    }
//                }
//                else {
//                    if(is_numeric($value)) {
//                        if($configs['operators'][$attr] == 'grt') {
//                            $configs['operators'][$attr] = ' > ';
//                            $value = intval($value);
//                        }
//                        elseif($configs['operators'][$attr] == 'lt') {
//                            $configs['operators'][$attr] = ' < ';
//                            $value = intval($value);
//                        }
//                        elseif($configs['operators'][$attr] == 'IN') {
//                            $value = '('.intval($value).')';
//                        }
//                        elseif($configs['operators'][$attr] == 'NOT IN') {
//                            $value = '('.intval($value).')';
//                        }
//                        else {
//                            $configs['operators'][$attr] = '=';
//                            $value = intval($value);
//                        }
//                    }
//                    else {
//                        if($configs['operators'][$attr] == 'like') {
//                            $value = '"%'.$this->f_db->escape_string($value).'%"';
//                        }
//                        elseif($configs['operators'][$attr] == 'IN') {
//                            $value = '('.$value.')';
//                        }
//                        elseif($configs['operators'][$attr] == 'NOT IN') {
//                            $value = '('.$value.')';
//                        }
//                        else if($configs['operators'][$attr] == 'CUSTOMSQL') {
//                            $value = $this->f_db->escape_string($value);
//                        }
//                        else if($configs['operators'][$attr] == 'CUSTOMSQLSECURE') {
//                            $value = $value;
//                        }
//                        else {
//                            $configs['operators'][$attr] = '=';
//                            $value = '"'.$this->f_db->escape_string($value).'"';
//                        }
//                    }
//                    if($configs['operators'][$attr] == 'CUSTOMSQL') {
//                        $sql .= $andor.' '.$value;
//                    }
//                    else if($configs['operators'][$attr] == 'CUSTOMSQLSECURE') {
//                        $sql .= $andor.' '.$value;
//                    }
//                    else {
//                        $sql .= $andor.$attr.' '.$configs['operators'][$attr].$value;
//                    }
//                    unset($value);
//                }
//
//                $andor = ' AND ';
//            }
//        }
        // else
        if(!empty($filters)) {
            $sql .= ' WHERE '.$filters; //SQL where statement; to be improved
        }

        if(!isset($this->f_db)) {
            $this->f_db = $integration->get_dbconn();
        }
        $query = $this->f_db->query($sql);
        $numrows = $this->f_db->num_rows($query);
        if($numrows > 1) {
            while($item = $this->f_db->fetch_assoc($query)) {
                $items[$item[$this->primary_key]] = new $this->class($item[$this->primary_key], $this->f_db);
            }
            $this->f_db->free_result($query);
            return $items;
        }
        else {
            if($numrows == 1 && $configs['returnarray'] == true) {
                $pk = $this->f_db->fetch_field($query, $this->primary_key);
                return array($pk => new $this->class($pk, $this->f_db));
            }
            elseif($numrows == 1) {
                return new $this->class($this->f_db->fetch_field($query, $this->primary_key), $this->f_db);
            }
            return false;
        }
    }

}

Abstract class IntegrationAbstractClass {
    protected $data;
    protected $f_db;

    const PRIMARY_KEY = '';
    const TABLE_NAME = '';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;

    public function __construct($id, $f_db = NULL) {
        global $intgdb;
        if(!empty($f_db)) {
            $this->f_db = $f_db;
        }
        else {
            $this->f_db = $intgdb;
        }
        $this->read($id);
    }

    private function read($id) {
        $this->data = $this->f_db->fetch_assoc($this->f_db->query("SELECT *
						FROM ".static::TABLE_NAME."
						WHERE ".static::PRIMARY_KEY."='".$this->f_db->escape_string($id)."'"));
    }

    public function get_id() {
        return $this->data[static::PRIMARY_KEY];
    }

    public function __get($name) {
        if(isset($this->data[$name])) {
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

    public static function get_data($filters = '', $configs = array()) {
        $data = new IntegrationDataAccessLayer(static::CLASSNAME, static::TABLE_NAME, static::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_displayname() {
        return $this->data[static::DISPLAY_NAME];
    }

}
?>
