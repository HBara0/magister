<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Customers_class.php
 * Created:        @zaher.reda    Jun 11, 2014 | 11:21:38 AM
 * Last Update:    @zaher.reda    Jun 11, 2014 | 11:21:38 AM
 */

/**
 * Description of Customers_class
 *
 * @author zaher.reda
 */
class Customers extends Entities {
    //put your code here

    const PRIMARY_KEY = 'eid';
    const TABLE_NAME = 'entities';
    const DISPLAY_NAME = 'companyName';

    public function __construct($id, $action = '', $simple = true) {
        $this->data = $this->read($id, $simple);
        $this->data[self::PRIMARY_KEY] = $this->data[parent::PRIMARY_KEY];
    }

    public static function get_customers($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function set(array $data) {
        foreach($data as $name => $value) {
            $this->data[$name] = $value;
        }
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    /* call the Magical function  get to acces the private attributes */
    public function get() {
        return $this->data;
    }

}