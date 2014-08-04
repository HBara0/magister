<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: IntegrationMediationEntities_class.php
 * Created:        @zaher.reda    Jul 17, 2014 | 2:47:01 PM
 * Last Update:    @zaher.reda    Jul 17, 2014 | 2:47:01 PM
 */

/**
 * Description of IntegrationMediationEntities_class
 *
 * @author zaher.reda
 */
class IntegrationMediationEntities {
    private $status = 0;
    private $data = array();

    const PRIMARY_KEY = 'imspid';
    const TABLE_NAME = 'integration_mediation_entities';
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

    public function get_localentity() {
        return new Entities($this->data['localId']);
    }

    public function get_foreignentity() {

    }

    public function get_affiliate() {
        return new Affiliates($this->data['affid']);
    }

    public static function get_entity_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_entities($filters = null, array $configs = array()) {
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

}