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
    const PRIMARY_KEY = null;
    const TABLE_NAME = null;

    private $class = null;
    private $data = array();

    public function __construct($class, $table, $primary_key) {
        DEFINE(self::PRIMARY_KEY, $primary_key);
        DEFINE(self::TABLE_NAME, $table);

        $this->class = $class;
    }

    public static function get_objects($filters = null, array $configs = array()) {
        global $db;

        $items = array();
        $sql = 'SELECT '.self::PRIMARY_KEY.' FROM '.Tprefix.self::TABLE_NAME;

        $sql .= $this->construct_whereclause($filters);
        $sql .= $this->construct_orderclause($configs['order']);
        $sql .= $this->construct_limitclause($configs['limit']);

        $query = $db->query($sql);
        if($db->num_rows($query) > 1) {
            while($item = $db->fetch_assoc($query)) {
                $items[$item[self::PRIMARY_KEY]] = new $this->class($item[self::PRIMARY_KEY]);
            }
            $db->free_result($query);
            return $items;
        }
        else {
            if($db->num_rows($query) == 1) {
                return new $this->class($db->fetch_field($query, self::PRIMARY_KEY));
            }
            return false;
        }
    }

    private function construct_havingclause($having) {

    }

    private function construct_orderclause($order) {
        global $db;
        /* Improve to have multiple orders */
        if(is_array($order)) {
            if(!isset($order['sort']) || empty($order['sort'])) {
                $order['sort'] = 'ASC';
            }
            $query_order = 'ORDER BY '.$db->escape_string($order['by']).' '.$db->escape_string($order['sort']);
        }
        else {
            if(!empty($order)) {
                $query_order = 'ORDER BY '.$db->escape_string($order).' ASC';
            }
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

    private function construct_whereclause($filters) {
        global $db;

        if(is_array($filters)) {
            $andor = ' WHERE ';
            foreach($filters as $attr => $value) {
                if(is_numeric($value)) {
                    $value = intval($value);
                }
                else {
                    $value = '"'.$db->escape_string($value).'"';
                }
                $filters_querystring .= $andor.$attr.'='.$value;
                $andor = ' AND ';
            }
        }
        else {
            $filters_querystring = ' WHERE '.$db->escape_string($filters);
        }
        return $filters_querystring;
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