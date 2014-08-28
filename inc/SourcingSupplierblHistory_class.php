<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: SourcingSupplierblHistry_class.php
 * Created:        @tony.assaad    Jul 30, 2014 | 10:12:29 AM
 * Last Update:    @tony.assaad    Jul 30, 2014 | 10:12:29 AM
 */

/**
 * Description of SourcingSupplierblHistry_class
 *
 * @author tony.assaad
 */
class SourcingSupplierblHistory {
    const PRIMARY_KEY = 'ssbid';
    const TABLE_NAME = 'sourcing_suppliers_blhistory';

    private $data = array();

    public function __construct($id = '') {
        $this->data = $this->read($id, $simple);
        $this->data[self::PRIMARY_KEY] = $this->data[self::PRIMARY_KEY];
    }

    public function read($id, $simple) {
        global $db;
        if(!empty($id)) {
            return $db->fetch_assoc($db->query("SELECT  * FROM ".Tprefix.self::TABLE_NAME." WHERE ".self::PRIMARY_KEY."='".$db->escape_string($id)."'"));
        }
        return false;
    }

    public function create($data) {
        global $db, $log, $core;

        if(empty($data['reason'])) {
            $this->status = 1;
            return false;
        }

        $supplier = new Sourcing($data['ssid']);
        if($supplier->is_blacklisted()) {
            $this->status = 2;
            return false;
        }
        $blacklist_history = array(
                'ssid' => $data['ssid'],
                'reason' => $core->sanitize_inputs($data['reason'], array('removetags' => true)),
                'requestedOn' => strtotime($data['requestedOn']),
                'requestedBy' => $data['requestedBy'],
                'createdOn' => TIME_NOW
        );
        $query = $db->insert_query(self::TABLE_NAME, $blacklist_history);
        if($query) {
            $query = $db->update_query('sourcing_suppliers', array('isBlacklisted' => 1), 'ssid='.intval($data['ssid']).'');
            $log->record($data['ssid']);
            /* Notify coordinators */
            $this->sendblnotification();
            $this->status = 0;
        }
    }

    public function update($data) {
        global $db, $core;

        $data['modifiedBy'] = $core->user['uid'];

        $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
    }

    public function sendblnotification(Sourcing $supplier = null, array $options = array()) {
        global $core, $lang;

        if(!is_object($supplier)) {
            $supplier = new Sourcing($this->data['ssid']);
        }

        $lang->load('messages');
        $message_body = $lang->sourcing_notifyblacklist_message;
        $message_subject = $lang->sourcing_notifyblacklist_subject;
        if(isset($options['status']) && $options['status'] == 'remove') {
            $message_body = $lang->sourcing_removeblacklist_message;
            $message_subject = $lang->sourcing_removeblacklist_subject;
        }

        $supplier_segments = $supplier->get_supplier_segments(); /* get product segment of the potential supplier */

        $email_data['cc'] = array('sourcing@orkila.com');
        foreach($supplier_segments as $psid => $supsegment) {
            $segment_objs = new ProductsSegments($psid);
            $segment_coordobjs = $segment_objs->get_coordinators();
            if(is_array($segment_coordobjs)) {
                foreach($segment_coordobjs as $coord) {
                    $email_data['to'][] = $coord->get_coordinator()->email;
                }
            }
            $email_data['to'] = array_unique($email_data['to']);
        }
        $mailer = new Mailer();
        $mailer = $mailer->get_mailerobj();
        $mailer->set_type();
        $mailer->set_from(array('name' => $core->settings['mailfrom'], 'email' => $core->settings['maileremail']));
        $mailer->set_subject($message_subject);
        $mailer->set_message($lang->sprint($message_body, $supplier->get_supplier()['companyName']));
        $mailer->set_to($email_data['to']);
        $mailer->set_cc($email_data['cc']);
        $mailer->send();
    }

    public static function get_histories($filters = null, array $configs = array()) {
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

    public function __get($name) {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }
        return false;
    }

    public function get_potntialsupplier() {
        return new Sourcing($this->data['ssid']);
    }

    public function get_requestedby() {
        return new Users($this->data['requestedBy']);
    }

    public function get() {
        return $this->data;
    }

    public function get_status() {
        return $this->status;
    }

}