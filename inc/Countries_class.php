<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Countries Class
 * $id: Countries_class.php
 * Created:        @zaher.reda    Mar 8, 2013 | 4:56:25 PM
 * Last Update:    @zaher.reda    Mar 8, 2013 | 4:56:25 PM
 */

class Countries extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'coid';
    const TABLE_NAME = 'countries';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function get_maincurrency() {
        return new Currencies($this->data['mainCurrency']);
    }

    public static function get_coveredcountries() {
        return self::get_countries('affid !=0');
    }

    public static function get_countries($filters = '', $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_capitalcity() {
        if(!is_empty($this->data['capitalCity'])) {
            return new Cities($this->data['capitalCity']);
        }
        return false;
    }

    public function get_affiliate() {
        return new Affiliates($this->data['affid']);
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

    public function save(array $data = array()) {
        global $db;
        /* Add validations */
        $db->update_query('countries', $this->data, 'coid='.intval($this->data['coid']));
    }

    protected function create(array $data) {

    }

    protected function update(array $data) {

    }

}
?>
