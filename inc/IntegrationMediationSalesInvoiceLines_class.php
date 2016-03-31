<?php
/*
 * Copyright © 2016 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: IntegrationMediationSalesInvoiceLines_class.php
 * Created:        @rasha.aboushakra    Mar 15, 2016 | 10:41:48 AM
 * Last Update:    @rasha.aboushakra    Mar 15, 2016 | 10:41:48 AM
 */

/**
 * Description of IntegrationMediationSalesInvoiceLines_class
 *
 * @author rasha.aboushakra
 */
class IntegrationMediationSalesInvoiceLines extends IntegrationMediation {
    private $status = 0;
    protected $data = array();
    private $order_currency = '';
    private $order_salesrep = '';
    private $order_customer = '';

    const PRIMARY_KEY = 'imsiid';
    const TABLE_NAME = 'integration_mediation_salesinvoicelines';

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

    public static function get_invoicelines($filters = null, array $configs = array()) {
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

    public function get_invoice() {
        $order = IntegrationMediationSalesInvoices::get_invoices(array('foreignId' => $this->data['foreignInvoiceId']), array('returnarray' => false));
        if(!is_object($order)) {
            return null;
        }
        return $order;
    }

//    public function get_salesrep() {
//        return $this->order_salesrep;
//    }
//
//    public function set_salesrep($order_salesrep) {
//        if(empty($order_salesrep)) {
//            return false;
//        }
//        $this->order_salesrep = $order_salesrep;
//    }
//
//    public function get_customer() {
//        return $this->order_customer;
//    }
//
//    public function set_customer($order_cust) {
//        if(empty($order_cust)) {
//            return false;
//        }
//        $this->order_customer = $order_cust;
//    }
//
//    public function get_salesrep_object() {
//        $user = Users::get_data(array('displayName' => $this->order_salesrep));
//        if(!is_object($user) || empty($user->uid)) {
//            return false;
//        }
//        return $user;
//    }
}
?>