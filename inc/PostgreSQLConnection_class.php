<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * PostgreSQL Connection Class
 * $id: PostgreSQLConnection_class.php
 * Created:        @zaher.reda    Feb 18, 2013 | 12:11:30 PM
 * Last Update:    @zaher.reda    Feb 18, 2013 | 12:11:30 PM
 */

class PostgreSQLConnection {
    protected $link;
    private $db_encoding = 'utf8';
    private $db = array('hostname' => 'localhost');

    public function __construct($db, $hostname = 'localhost', $username = 'root', $password = '', $prefix = '') {
        if(!empty($hostname)) {
            $this->db['hostname'] = $hostname;
        }
        $this->db['username'] = $username;
        $this->db['password'] = $password;
        $this->db['db'] = $db;
        $this->db['prefix'] = $prefix;
        $this->connect();
    }

    private function connect() {
        $this->link = pg_connect('host='.$this->db['hostname'].' port=5432 dbname='.$this->db['db'].' user='.$this->db['username'].' password='.$this->db['password']) or $this->pgerror();
        $this->query('SET NAMES \''.$this->db_encoding.'\'');
    }

    public function query($query_string) {
        $query = @pg_query($this->link, $query_string);

        if($this->error()) {
            $this->pgerror($query_string);
        }
        return $query;
    }

    public function insert_query($table, $data, $options = '') {
        if(is_array($data)) {
            $query_data = $this->prepare_insertstatement_data($data, $options);

            return $this->query('INSERT INTO '.$this->db['prefix'].$table.' ('.$query_data['index'].') VALUES ('.$query_data['value'].')');
        }
        else {
            return false;
        }
    }

    private function prepare_insertstatement_data(array $data, $options = null) {
        $comma = '';
        if(!empty($data)) {
            foreach($data as $key => $val) {
                $statement['index'] .= $comma.$key;

                if($options['isfunction'][$key] == true) {
                    $statement['value'] .= $comma.$this->escape_string($val);
                }
                else {
                    $statement['value'] .= $comma."'".$this->escape_string($val)."'";
                }
                $comma = ', ';
            }
            return $statement;
        }
        return false;
    }

    public function update_query($table, $data, $where = '') {
        $comma = $query_string = '';
        if(is_array($data)) {
            foreach($data as $key => $val) {
                $query_string .= "{$comma}{$key}='".$this->escape_string($val)."'";
                $comma = ', ';
            }

            if(!empty($where)) {
                $where = ' WHERE '.$where;
            }

            return $this->query("UPDATE {$this->db['prefix']}{$table} SET {$query_string}{$where}");
        }
        else {
            return false;
        }
    }

    public function fetch_array($query, $type = PGSQL_BOTH) {
        return pg_fetch_array($query, $type);
    }

    public function fetch_assoc($query) {
        return pg_fetch_assoc($query);
    }

    public function fetch_field($query, $field, $row = NULL) {
        return pg_fetch_result($query, $row, $field);
    }

    public function free_result($query) {
        return pg_free_result($query);
    }

    public function close() {
        @pg_close($this->link);
    }

    public function num_rows($query) {
        return pg_num_rows($query);
    }

    protected function error() {
        if($this->link) {
            return pg_last_error($this->link);
        }
        else {
            return pg_last_error();
        }
    }

    public function set_charset($charset = '') {
        if(empty($charset)) {
            $charset = $this->db_encoding;
        }

        pg_set_client_encoding($this->link, $charset);
    }

    public function escape_string($string) {
        if(function_exists('pg_escape_string') && $this->link) {
            return pg_escape_string($this->link, $string);
        }
        else {
            return addslashes($string);
        }
    }

    protected function pgerror($string = '') {
        global $errorhandler;

        if(!is_object($errorhandler)) {
            $errorhandler = new errorHandler();
        }

        $error = array(
                'error' => $this->error(),
                'query' => $string
        );
        $errorhandler->trigger($error, '', SQL_ERROR);
    }

    public function __sleep() {
        return array('hostname', 'username', 'password', 'db');
    }

    public function __wakeup() {
        $this->connect();
    }

}
?>
