<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: CrmVisitReports.php
 * Created:        @zaher.reda    Jul 1, 2014 | 3:51:59 PM
 * Last Update:    @zaher.reda    Jul 1, 2014 | 3:51:59 PM
 */

/**
 * Description of CrmVisitReports
 *
 * @author zaher.reda
 */
class CrmVisitReports {
    private $data = array();
    private $errorcode = 0;

    const PRIMARY_KEY = 'vrid';
    const TABLE_NAME = 'visitreports';
    const DISPLAY_NAME = '';

    public function __construct($id, $simple = true) {
        if(isset($id) && !empty($id)) {
            $this->data = $this->read($id, $simple);
        }
    }

    private function read($id, $simple = false) {
        global $db;
        $query_select = '*';
        if($simple == true) {
            $query_select = 'vrid, identifier, uid, cid, affid, date';
        }

        return $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public static function get_visitreports($filters = null, array $configs = array()) {
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

    public function get() {
        return $this->data;
    }

    public function get_displayname() {
        return $this->data[self::DISPLAY_NAME];
    }

    public function get_errorcode() {
        return $this->errorcode;
    }

}
?>