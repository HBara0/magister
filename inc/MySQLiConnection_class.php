<?php

class MySQLiConnection {

    protected $link;
    private $db_encoding = 'utf8';
    private $db = array();

    public function __construct($db, $hostname = 'localhost', $username = 'root', $password = '', $prefix = '') {
        $this->db['hostname'] = $hostname;
        $this->db['username'] = $username;
        $this->db['password'] = $password;
        $this->db['db'] = $db;
        $this->db['prefix'] = $prefix;
        $this->connect();
        $this->select_db();
    }

    private function connect() {
        $this->link = @mysqli_connect($this->db['hostname'], $this->db['username'], $this->db['password']) or $this->mysqlerror();
    }

    private function select_db() {
        @mysqli_select_db($this->link, $this->db['db']) or $this->mysqlerror();
        $this->query("SET NAMES '{$this->db_encoding}'");
    }

    public function query($query_string) {
        $query = @mysqli_query($this->link, $query_string);

        if ($this->error_number()) {
            $this->mysqlerror($query_string);
        }
        return $query;
    }

    public function multi_query() {
        $query = @mysqli_multi_query($this->link, $query_string);

        if ($this->error_number()) {
            $this->mysqlerror($query_string);
        }
        return $query;
    }

    public function insert_query($table, $data, $options = '') {
        $comma = $index_string = $data_string = $keyphrase = '';
        if (is_array($data)) {
            $query_data = $this->prepare_insertstatement_data($data, $options);
            return $this->query('INSERT INTO ' . $this->db['prefix'] . $table . ' (' . $query_data['index'] . ') VALUES (' . $query_data['value'] . ')');
        }
        else {
            return false;
        }
    }

    public function multi_insert_query($table, array $data, $options = '') {
        if (is_array($data)) {
            foreach ($data as $entry => $entry_data) {
                $query_data = $this->prepare_insertstatement_data($entry_data, $options);
                $query_values .= $comma . '(' . $query_data['value'] . ')';
                $comma = ', ';
            }
            return $this->query('INSERT INTO ' . $this->db['prefix'] . $table . ' (' . $query_data['index'] . ') VALUES ' . $query_values);
        }
        else {
            return false;
        }
    }

    private function prepare_insertstatement_data(array $data, $options = '') {
        $comma = $keyphrase = '';
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                $statement['index'] .= $comma . $key;
                if (!empty($options['encrypt']) && is_array($options['encrypt']) && in_array($key, $options['encrypt'])) {
                    if (array_key_exists($key . 'Key', $data)) {
                        $keyphrase = $data[$key . 'Key'];
                    }
                    else {
                        $keyphrase = $key; //or later set a default key setting
                    }
                    $statement['value'] .= $comma . "AES_ENCRYPT('{$val}', '{$keyphrase}')";
                }
                elseif (!empty($options['geoLocation']) && is_array($options['geoLocation']) && in_array($key, $options['geoLocation'])) {
                    $statement['value'] .= $comma . 'geomFromText("POINT(' . $this->escape_string($val) . ')")';
                }
                else {
                    $statement['value'] .= $comma . "'" . $this->escape_string($val) . "'";
                }
                $comma = ', ';
            }

            return $statement;
        }
        return false;
    }

    public function update_query($table, $data, $where = '', $options = '') {
        $comma = $query_string = '';
        if (is_array($data)) {
            foreach ($data as $key => $val) {
                if (is_null($val)) {
                    continue;
                }
                if (!empty($options['encrypt']) && is_array($options['encrypt']) && in_array($key, $options['encrypt'])) {
                    if (array_key_exists($key . 'Key', $data)) {
                        $keyphrase = $data[$key . 'Key'];
                    }
                    else {
                        $keyphrase = $key; //or later set a default key setting
                    }
                    $query_string .= $comma . "{$key}=AES_ENCRYPT('{$val}', '{$keyphrase}')";
                }
                elseif (!empty($options['geoLocation']) && is_array($options['geoLocation']) && in_array($key, $options['geoLocation'])) {
                    $statement['value'] .= $comma . $key . '=geomFromText("POINT(' . $this->escape_string($val) . ')")';
                }
                else {
                    $query_string .= "{$comma}{$key}='" . $this->escape_string($val) . "'";
                }
                $comma = ', ';
            }
            if (!empty($where)) {
                $where = ' WHERE ' . $where;
            }

            return $this->query("UPDATE {$this->db['prefix']}{$table} SET {$query_string}{$where}");
        }
        else {
            return false;
        }
    }

    public function delete_query($table, $where = '') {
        $where_query = '';
        if (!empty($where)) {
            $where_query = ' WHERE ' . $where;
        }

        return $this->query("DELETE FROM {$this->db['prefix']}{$table}{$where_query}");
    }

    public function fetch_array($query, $type = MYSQLI_BOTH) {
        return mysqli_fetch_array($query, $type);
    }

    public function fetch_assoc($query) {
        return mysqli_fetch_assoc($query);
    }

    public function fetch_field($query, $field, $row = false) {
        if ($row === false) {
            $fetch = $this->fetch_array($query);
        }
        else {
            mysqli_data_seek($query, $row);
            $fetch = mysqli_fetch_field($query);
        }
        return $fetch[$field];
    }

    public function last_id() {
        return mysqli_insert_id($this->link);
    }

    public function free_result($query) {
        return mysqli_free_result($query);
    }

    public function close() {
        @mysqli_close($this->link);
    }

    public function num_fields($query) {
        return mysqli_num_fields($query);
    }

    public function num_rows($query) {
        return mysqli_num_rows($query);
    }

    public function affected_rows() {
        return mysqli_affected_rows($this->link);
    }

    public function escape_string($string) {
        if (function_exists('mysqli_real_escape_string') && $this->link) {
            return mysqli_real_escape_string($this->link, $string);
        }
        elseif (function_exists('mysqli_escape_string')) {
            return mysqli_escape_string($this->link, $string);
        }
        else {
            return addslashes($string);
        }
    }

    protected function error_number() {
        if ($this->link) {
            return mysqli_errno($this->link);
        }
        else {
            return mysqli_errno();
        }
    }

    protected function error() {
        if ($this->link) {
            return mysqli_error($this->link);
        }
        else {
            return mysqli_error();
        }
    }

    public function set_charset($charset = '') {
        if (empty($charset)) {
            $charset = $this->db_encoding;
        }

        mysqli_set_charset($this->link, $charset);
    }

    protected function mysqlerror($string = '') {
        global $errorhandler;

        if (!is_object($errorhandler)) {
            $errorhandler = new errorHandler();
        }

        $error = array(
            'error_no' => $this->error_number(),
            'error' => $this->error(),
            'query' => $string
        );
        $errorhandler->trigger($error, '', SQL_ERROR);
        /* echo 'MySQL Error:'.$this->error_number();
          echo "<br />".$this->error();
          echo "<br />Query:".$string;
          exit; */
    }

    public function optimize_table($table) {
        $this->query("OPTIMIZE TABLE {$this->db['prefix']}{$table}");
    }

    public function analyze_table($table) {
        $this->query("ANALYZE TABLE {$this->db['prefix']}{$table}");
    }

    public function show_create_table($table) {
        $query = $this->query("SHOW CREATE TABLE {$this->db['prefix']}{$table}");
        $structure = $this->fetch_array($query);
        return $structure['Create Table'];
    }

    public function show_fields_from($table, $type = MYSQLI_BOTH) {
        $query = $this->query("SHOW FIELDS FROM {$this->db['prefix']}{$table}");
        while ($field = $this->fetch_array($query, $type)) {
            $field_info[] = $field;
        }
        return $field_info;
    }

    public function get_tables_havingcolumn($column, $filter = '') {
        if (!empty($filter)) {
            $filter = ' AND ' . $filter;
        }
        $query = $this->query('SELECT DISTINCT TABLE_NAME
                        FROM INFORMATION_SCHEMA.COLUMNS
                        WHERE COLUMN_NAME IN ("' . $this->escape_string($column) . '")
                        AND TABLE_SCHEMA="' . $this->db['db'] . '"' . $filter);

        if ($this->num_rows($query) > 0) {
            while ($table = $this->fetch_array($query)) {
                $tables[] = $table['TABLE_NAME'];
            }
            return $tables;
        }
        return null;
    }

    public function field_name($result, $index) {
        return mysqli_fetch_field_direct($result, $index);
    }

    public function table_status($table = '') {
        if (!empty($table)) {
            $query = $this->query("SHOW TABLE STATUS LIKE '" . $this->db['prefix'] . $table . "'");
        }
        else {
            $query = $this->query("SHOW TABLE STATUS");
        }
        $total = 0;
        while ($table = $this->fetch_array($query)) {
            $total += $table['Data_length'] + $table['Index_length'];
        }
        return $total;
    }

    /**
     * Checks if a MySQL function exists
     * @param string $functionname
     * @return boolean
     */
    public function function_exists($functionname) {
        $query = $this->query('SHOW FUNCTION STATUS WHERE name="' . $this->escape_string($functionname) . '"');
        if ($this->num_rows($query) > 0) {
            return true;
        }
        return false;
    }

    public function __sleep() {
        return array('hostname', 'username', 'password', 'db');
    }

    public function __wakeup() {
        $this->connect();
    }

}

?>