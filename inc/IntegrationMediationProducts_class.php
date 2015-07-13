<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: IntegrationMediationProducts_class.php
 * Created:        @zaher.reda    Jul 17, 2014 | 10:23:37 AM
 * Last Update:    @zaher.reda    Jul 17, 2014 | 10:23:37 AM
 */

/**
 * Description of IntegrationMediationProducts_class
 *
 * @author zaher.reda
 */
class IntegrationMediationProducts extends IntegrationMediation {
    private $status = 0;
    private $data = array();

    const PRIMARY_KEY = 'impid';
    const TABLE_NAME = 'integration_mediation_products';
    const DISPLAY_NAME = 'foreignName';

    public function __construct($id = null, $simple = true) {
        if(empty($id)) {
            return false;
        }
        $this->read($id, $simple);
    }

    private function read($id, $simple) {
        global $db;

        $query_select = '*';

        $this->data = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function get_localproduct() {
        return new Products($this->data['localId']);
    }

    public function get_foreignproduct() {

    }

    public function get_affiliate() {
        return new Affiliates($this->data['affid']);
    }

    public static function get_product_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_products($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
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

    public function get_localsupplier() {
        if(empty($this->data['localId']) && !isset($this->data['localId'])) {
            return null;
        }
        $localproduct = new Products($this->data['localId']);
        if(!is_object($localproduct) || empty($localproduct->pid)) {
            return null;
        }
        return $localproduct->get_supplier();
    }

}
?>