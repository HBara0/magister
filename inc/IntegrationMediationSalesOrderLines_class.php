v<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: IntegrationMediationOrderLines_class.php
 * Created:        @hussein.barakat    Jul 7, 2015 | 2:16:09 PM
 * Last Update:    @hussein.barakat    Jul 7, 2015 | 2:16:09 PM
 */

class IntegrationMediationSalesOrderLines extends IntegrationMediation {
    private $status = 0;
    private $data = array();

    const PRIMARY_KEY = 'imsolid';
    const TABLE_NAME = 'integration_mediation_salesorderlines';

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

    public function get_affiliate() {
        return new Affiliates($this->data['affid']);
    }

    public static function get_order_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_orderlines($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_column($column, $filters = '', array $configs = array()) {
        $data = new DataAccessLayer(static::CLASSNAME, static::TABLE_NAME, static::PRIMARY_KEY);
        return $data->get_column($column, $filters, $configs);
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

    public function get_order() {
        $order = new IntegrationMediationSalesOrders($this->data['foreignOrderId']);
        return $order;
    }

}
?>