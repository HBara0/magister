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
    private $order_currency = '';
    private $order_salesrep = '';

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
        $order = IntegrationMediationSalesOrders::get_orders(array('foreignId' => $this->data['foreignOrderId']), array('returnarray' => false));
        if(!is_object($order)) {
            return null;
        }
        return $order;
    }

    public function set_ordercur($currency) {
        if(empty($currency)) {
            return false;
        }
        $this->order_currency = $currency;
    }

    public function get_ordercurr() {
        return $this->order_currency;
    }

    public function get_ordercurr_object() {
        $currency = Currencies::get_data(array('alphaCode' => $this->get_ordercurr()), array('returnarray' => false));
        if(!is_object($currency) || empty($currency->alphaCode)) {
            return false;
        }
        return $currency;
    }

    public function get_salesrep() {
        return $this->order_salesrep;
    }

    public function set_salesrep($order_salesrep) {
        if(empty($order_salesrep)) {
            return false;
        }
        $this->order_salesrep = $order_salesrep;
    }

    public function get_salesrep_object() {
        $user = Users::get_data(array('displayName' => $this->order_salesrep));
        if(!is_object($user) || empty($user->uid)) {
            return false;
        }
        return $user;
    }

}
?>