<?php
/*
 * Copyright © 2015 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: IntegrationMediationSalesOrders.php
 * Created:        @hussein.barakat    Jul 7, 2015 | 1:25:41 PM
 * Last Update:    @hussein.barakat    Jul 7, 2015 | 1:25:41 PM
 */

class IntegrationMediationSalesOrders extends IntegrationMediation {
    private $status = 0;
    protected $data = array();

    const PRIMARY_KEY = 'imsoid';
    const TABLE_NAME = 'integration_mediation_salesorders';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    public function __construct($id = null, $simple = true) {
        if(empty($id)) {
            return false;
        }
        $this->read($id, $simple);
    }

    protected function read($id, $simple) {
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

    public static function get_orders($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public static function get_column($column, $filters = '', array $configs = array()) {
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

    public function get_orderlines() {
        $orderlines = IntegrationMediationSalesOrderLines::get_orderlines(array('foreignOrderId' => $this->data['foreignId']), array('returnarray' => true));
        if(is_array($orderlines)) {
            return $orderlines;
        }
        return null;
    }

    public function get_currency() {
        if(!isset($this->data['currency']) || empty($this->data['currency'])) {
            $currency = new Currencies(840);
            return $currency;
        }
        $currency = Currencies::get_data(array('alphaCode' => $this->data['currency']), array('returnarray' => false));
        return $currency;
    }

}
?>