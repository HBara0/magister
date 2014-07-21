<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: VisitReport_class.php
 * Created:        @tony.assaad    Jul 21, 2014 | 11:24:54 AM
 * Last Update:    @tony.assaad    Jul 21, 2014 | 11:24:54 AM
 */

/**
 * Description of VisitReport_class
 *
 * @author tony.assaad
 */
class VisitReport {
    const TABLE_NAME = 'visitreports';
    const PRIMARY_KEY = 'vrid';

    private $data = array();

    public function __construct($id, $simple = true) {
        if(!empty($id)) {
            $this->data = $this->read($id, $simple);
        }
    }

    private function read($id, $simple = true) {
        global $db;
        return $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public static function get_visitreports($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_customer() {
        return new Customers($this->data['cid']);
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        global $core;
        if(is_array($attributes_param)) {
            foreach($attributes_param as $attr => $val) {
                $attributes .= $attr.'="'.$val.'"';
            }
        }
        if(is_object($this->get_customer()) && !empty($this->data['date'])) {
            $this->visitdate = date($core->settings['dateformat'], $this->data['date']);
            return '<a href="index.php?module=crm/previewvisitreport&referrer=list&vrid='.$this->data[self::PRIMARY_KEY].'" '.$attributes.'>'.$this->get_customer()->get()['companyName'].' - '.$this->visitdate.'</a>';
        }
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

}