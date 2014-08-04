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
class CrmVisitReports extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'vrid';
    const TABLE_NAME = 'visitreports';
    const DISPLAY_NAME = '';
    const CLASSNAME = __CLASS__;
    const SIMPLEQ_ATTRS = 'vrid, identifier, uid, cid, affid, date';

    public function __construct($id = '', $simple = true) {
        if(isset($id) && !empty($id)) {
            $this->data = $this->read($id, $simple);
        }
        return null;
    }

    protected function read($id, $simple = false) {
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

    public function get_customer() {
        return new Customers($this->data['cid']);
    }

    public function get_displayname() {
        global $core;

        return $this->get_customer()->get_displayname().' - '.date($core->settings['dateformat'], $this->data['date']);
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        global $core;
        if(is_array($attributes_param)) {
            foreach($attributes_param as $attr => $val) {
                $attributes .= $attr.'="'.$val.'"';
            }
        }

        return '<a href="index.php?module=crm/previewvisitreport&referrer=list&vrid='.$this->data[self::PRIMARY_KEY].'" '.$attributes.'>'.$this->get_displayname().'</a>';
    }

    protected function save(array $data = array()) {

    }

    protected function update(array $data) {

    }

    protected function create(array $data) {

    }

}
?>