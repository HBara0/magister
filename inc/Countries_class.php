<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Countries Class
 * $id: Countries_class.php
 * Created:        @zaher.reda    Mar 8, 2013 | 4:56:25 PM
 * Last Update:    @zaher.reda    Mar 8, 2013 | 4:56:25 PM
 */

class Countries {
    private $country = array();

    public function __construct($id) {
        if(empty($id)) {
            return false;
        }
        $this->read($id);
    }

    private function read($id) {
        global $db;
        $this->country = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.'countries WHERE coid='.intval($id)));
    }

    public function get_maincurrency() {
        return new Currencies($this->country['mainCurrency']);
    }

    public function get_affiliate() {
        return new Affiliates($this->country['affid']);
    }

    public function get_capitalcity() {
        if(!is_empty($this->country['capitalCity'])) {
            return new Cities($this->country['capitalCity']);
        }
        return false;
    }

    public static function get_country_byname($name) {
        global $db;

        if(!empty($name)) {
            $id = $db->fetch_field($db->query('SELECT coid FROM '.Tprefix.'countries WHERE name="'.$db->escape_string($name).'"'), 'coid');
            if(!empty($id)) {
                return new Countries($id);
            }
        }
        return false;
    }

    public static function get_countries($filters = '') {
        global $db;

        $items = array();

        if(!empty($filters)) {
            $filters = ' WHERE '.$db->escape_string($filters);
        }
        $query = $db->query('SELECT coid FROM '.Tprefix.'countries'.$filters);
        while($item = $db->fetch_assoc($query)) {
            $items[$item['coid']] = new self($item['coid']);
        }
        $db->free_result($query);
        return $items;
    }

    public function __set($name, $value) {
        $this->country[$name] = $value;
    }

    public function __get($name) {
        if(array_key_exists($name, $this->country)) {
            return $this->country[$name];
        }
    }

    public function save() {
        global $db;
        /* Add validations */
        $db->update_query('countries', $this->country, 'coid='.intval($this->country['coid']));
    }

    public function get() {
        return $this->country;
    }

}
?>
