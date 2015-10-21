<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: DataAccessLayer.php
 * Created:        @zaher.reda    May 15, 2014 | 10:56:42 AM
 * Last Update:    @zaher.reda    May 15, 2014 | 10:56:42 AM
 */

/**
 * Description of DataAccessLayer
 *
 * @author zaher.reda
 */
class DataAccessLayer {
    private $primary_key = null;
    private $table_name = null;
    private $class = null;
    private $data = array();
    public $num_rows = 0;

    public function __construct($class, $table, $primary_key) {
        $this->table_name = $table;
        $this->primary_key = $primary_key;
        $this->class = $class;
    }

    public function get_column($columnname, $filters = null, array $configs = array()) {
        global $db;
        if(!is_string($columnname)) {
            return false;
        }
        $items = array();
        $sql = 'SELECT '.$this->primary_key.','.$columnname.' FROM '.Tprefix.$this->table_name;

        $sql .= $this->construct_whereclause($filters, $configs['operators']);
        $sql .= $this->construct_groupclause($configs['group']);
        $sql .= $this->construct_orderclause($configs['order']);
        $sql .= $this->construct_limitclause($configs['limit']);
        //  echo $sql.'<br>';
        $query = $db->query($sql);
        $this->numrows = $db->num_rows($query);
        if($this->numrows > 0) {
            while($item = $db->fetch_assoc($query)) {
                $items[$item[$this->primary_key]] = $item[$columnname];
            }
            $db->free_result($query);
            return $items;
        }
        return false;
    }

    public function get_objects($filters = null, array $configs = array()) {
        global $db;

        if(!isset($configs['simple'])) {
            $configs['simple'] = true;
        }
        $items = array();
        $sql = 'SELECT '.$this->primary_key.' FROM '.Tprefix.$this->table_name;

        $sql .= $this->construct_whereclause($filters, $configs['operators']);
        $sql .= $this->construct_groupclause($configs['group']);
        $sql .= $this->construct_orderclause($configs['order']);
        $sql .= $this->construct_limitclause($configs['limit']);
        //  echo $sql.'<br>';
        $query = $db->query($sql);
        $this->numrows = $db->num_rows($query);
        if($this->numrows > 1) {
            while($item = $db->fetch_assoc($query)) {
                $items[$item[$this->primary_key]] = new $this->class($item[$this->primary_key], $configs['simple']);
            }
            $db->free_result($query);
            return $items;
        }
        else {
            if($this->numrows == 1 && $configs['returnarray'] == true) {
                $pk = $db->fetch_field($query, $this->primary_key);
                return array($pk => new $this->class($pk, $configs['simple']));
            }
            elseif($this->numrows == 1) {
                return new $this->class($db->fetch_field($query, $this->primary_key), $configs['simple']);
            }
            return false;
        }
    }

    public function get_objects_byattr($attr, $value, $options = array()) {
        global $db;

        if(is_empty($value, $attr)) {
            return false;
        }

        $sql = 'SELECT '.$this->primary_key.' FROM '.Tprefix.$this->table_name;

        $filters = array($attr => $value);
        $sql .= $this->construct_whereclause($filters, array($attr => $options['operator']));
        $query = $db->query($sql);
        if($db->num_rows($query) > 1) {
            $items = array();
            while($item = $db->fetch_assoc($query)) {
                $items[$item[$this->primary_key]] = new $this->class($item[$this->primary_key]);
            }
            $db->free_result($query);
            return $items;
        }
        else {
            if($db->num_rows($query) == 1) {
                return new $this->class($db->fetch_field($query, $this->primary_key));
            }
            return false;
        }

        return false;
    }

    private function construct_havingclause($having) {

    }

    private function construct_groupclause($group) {
        global $db;
        if(!is_array($group)) {
            if(!empty($group)) {
                return ' GROUP BY '.$db->escape_string($group);
            }
            return false;
        }

        if(is_array($group)) {
            $group = array_map($db->escape_string, $group);
            return implode(', ', $group);
        }
        return false;
    }

    private function construct_orderclause($order) {
        global $db;

        /* Improve to have multiple orders */
        if(!is_array($order)) {
            if(!empty($order)) {
                return ' ORDER BY '.$db->escape_string($order).' ASC';
            }
            return false;
        }

        if(is_array($order['by'])) {
            foreach($order['by'] as $seq => $by) {
                $sort = $order['sort'];
                if(is_array($order['sort'])) {
                    $sort = $order['sort'][$seq];
                    if(!isset($order['sort'][$seq]) || empty($order['sort'][$seq])) {
                        $sort = 'ASC';
                    }
                }

                $sortentries[] = $db->escape_string($by).' '.$db->escape_string($sort);
            }
            return ' ORDER BY '.implode(',', $sortentries);
        }
        else {
            if(!isset($order['sort']) || empty($order['sort'])) {
                $order['sort'] = 'ASC';
            }
            return ' ORDER BY '.$db->escape_string($order['by']).' '.$db->escape_string($order['sort']);
        }
        return false;
    }

    private function construct_limitclause($limit) {
        global $db;

        if(is_array($limit)) {
            if(isset($limit['offset'], $limit['row_count'])) {
                return ' LIMIT '.intval($limit['offset']).', '.$limit['row_count'];
            }
        }
        else {
            if(!empty($limit)) {
                return ' LIMIT '.$db->escape_string($limit);
            }
        }
        return false;
    }

    public function construct_whereclause_public($filters, $operators = array()) {
        return self::construct_whereclause($filters, $operators);
    }

    private function construct_whereclause($filters, $operators = array()) {
        global $db;

        if(is_array($filters) && !empty($filters)) {
            $andor = ' WHERE ';
            foreach($filters as $attr => $value) {
                if(!isset($operators[$attr]) || empty($operators[$attr])) {
                    $operators['attr'] = '=';
                }

                if(is_array($value)) {
                    if($operators[$attr] == 'like') {

                    }

                    if($operators[$attr] == 'BETWEEN') {
                        $filters_querystring .= $andor.$attr.' BETWEEN '.$value[0].' AND '.$value[1];
                    }
                    elseif($operators[$attr] == 'NOT IN') {
                        $value = array_map($db->escape_string, $value);
                        $filters_querystring .= $andor.$attr.' NOT IN ('.implode(',', $value).')';
                    }
                    else {
                        $value_numerichk = array_filter($value, 'is_numeric');
                        if($value_numerichk == $value) {
                            $value = array_map(intval, $value);
                            $filters_querystring .= $andor.$attr.' IN ('.implode(',', $value).')';
                        }
                        else {
                            $value = array_map($db->escape_string, $value);
                            $filters_querystring .= $andor.$attr.' IN ("'.implode('","', $value).'")';
                        }
                    }
                }
                else {
                    if(is_numeric($value)) {
                        if($operators[$attr] == 'grt') {
                            $operators[$attr] = ' > ';
                            $value = intval($value);
                        }
                        elseif($operators[$attr] == 'lt') {
                            $operators[$attr] = ' < ';
                            $value = intval($value);
                        }
                        elseif($operators[$attr] == 'IN') {
                            $value = '('.intval($value).')';
                        }
                        elseif($operators[$attr] == 'NOT IN') {
                            $value = '('.intval($value).')';
                        }
                        else {
                            $operators[$attr] = '=';
                            $value = intval($value);
                        }
                    }
                    else {
                        if($operators[$attr] == 'like') {
                            $value = '"%'.$db->escape_string($value).'%"';
                        }
                        elseif($operators[$attr] == 'IN') {
                            $value = '('.$value.')';
                        }
                        elseif($operators[$attr] == 'NOT IN') {
                            $value = '('.$value.')';
                        }
                        else if($operators[$attr] == 'CUSTOMSQL') {
                            $value = $db->escape_string($value);
                        }
                        else if($operators[$attr] == 'CUSTOMSQLSECURE') {
                            $value = $value;
                        }
                        else {
                            $operators[$attr] = '=';
                            $value = '"'.$db->escape_string($value).'"';
                        }
                    }
                    if($operators[$attr] == 'CUSTOMSQL') {
                        $filters_querystring .= $andor.' '.$value;
                    }
                    else if($operators[$attr] == 'CUSTOMSQLSECURE') {
                        $filters_querystring .= $andor.' '.$value;
                    }
                    else {
                        $filters_querystring .= $andor.$attr.' '.$operators[$attr].$value;
                    }
                    unset($value);
                }

                $andor = ' AND ';
            }
        }
        else {
            if(!empty($filters)) {
                $filters_querystring = ' WHERE '.$db->escape_string($filters);
                if($operators['filter'] == 'CUSTOMSQLSECURE') {
                    $filters_querystring = ' WHERE '.$filters;
                }
            }
        }
        return $filters_querystring;
    }

    public
            function fulltext_search($match, $against, $config = array()) {
        global $db;

        if(isset($config['modifier'])) {
            $config['modifier'] = ' '.$db->escape_string($config['modifier']);
        }

        if(!isset($configs['simple'])) {
            $configs['simple'] = true;
        }

        $items = array();

        $syntax = 'MATCH ('.$db->escape_string($match).') AGAINST ("'.$db->escape_string($against).'" '.$configs['modifier'].')';
        $sql = 'SELECT '.$this->primary_key.', '.$syntax.' AS relevance FROM '.Tprefix.$this->table_name;
        $sql .= ' WHERE '.$syntax;

        $order['by'][] = 'relevance';
        if(!is_array($configs['order'])) {
            if(!empty($configs['order'])) {
                $order['by'][] = $configs['order'];
            }
        }
        else {
            if(is_array($configs['order'])) {
                $order['by'] += $configs['order'];
            }
        }
        $order['sort'] = 'DESC';
        $sql .= $this->construct_orderclause($order);
        $sql .= $this->construct_limitclause($configs['limit']);

        $query = $db->query($sql);
        if($db->num_rows($query) > 0) {
            while($item = $db->fetch_assoc($query)) {
                $items[$item[$this->primary_key]] = new $this->class($item[$this->primary_key], $configs['simple']);
            }
            $db->free_result($query);
            return $items;
        }
        return false;
    }

    public function __get($name) {
        if(array_key_exists($name, $this->data)) {
            return $this->data[$name];
        }
    }

    public function get() {
        return $this->data;
    }

}
?>