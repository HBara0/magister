<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Endproducttpes_class.php
 * Created:        @tony.assaad    Dec 4, 2013 | 12:29:46 PM
 * Last Update:    @tony.assaad    Dec 4, 2013 | 12:29:46 PM
 */

/**
 * Description of Endproducttpes_class
 *
 * @author tony.assaad
 */
class EndProducTypes extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'eptid';
    const TABLE_NAME = 'endproducttypes';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = 'eptid, name, title, psaid';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = null;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function update(array $data) {

    }

//
//    protected function create(array $data) {
//
////    }


    public function create(array $data) {
        global $db, $core, $log;
        if(empty($data['title'])) {
            $this->errorcode = 1;
            return false;
        }
        if(value_exists('endproducttypes', 'title', $data['title'])) {
            $this->errorcode = 2;
            return false;
        }

        $data['title'] = $core->sanitize_inputs($data['title'], array('removetags' => true));
        if(empty($data['name'])) {
            $data['name'] = strtolower($data['title']);
            $data['name'] = preg_replace('/\s+/', '', $data['name']);
        }

        $endproducttypes_data = array(
                'name' => $data['name'],
                'title' => $data['title'],
                'psaid' => $data['segapplications'],
                'createdBy' => $core->user['uid'],
                'createdOn' => TIME_NOW
        );
        $query = $db->insert_query('endproducttypes', $endproducttypes_data);
        $log->record('addendproducttypes');
    }

    public function get_application() {
        return new SegmentApplications($this->data['psaid']);
    }

    public function get_createdby() {
        return new Users($this->data['createdBy']);
    }

    public function get_modifiedby() {
        return new Users($this->data['modifiedBy']);
    }

    public static function get_producttype_byname($name) {
        global $db;
        if(!empty($name)) {
            $id = $db->fetch_field($db->query('SELECT eptid FROM '.Tprefix.'endproducttypes WHERE name="'.$db->escape_string($name).'"'), 'eptid');
            if(!empty($id)) {
                return new EndProducTypes($id);
            }
        }
        return false;
    }

    public static function get_endproductypes() {
        global $db, $core;
        $sort_query = ' ORDER BY title ASC';
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = $db->escape_string(' ORDER BY '.$core->input['sortby'].' '.$core->input['order']);
        }

        if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
            $core->settings['itemsperlist'] = intval($core->input['perpage']);
        }

        $limit_start = 0;
        if(isset($core->input['start'])) {
            $limit_start = intval($core->input['start']);
        }
        $query = $db->query("SELECT eptid FROM ".Tprefix."endproducttypes{$sort_query} LIMIT {$limit_start}, {$core->settings['itemsperlist']} ");
        if($db->num_rows($query) > 0) {
            while($producttype = $db->fetch_assoc($query)) {
                $producttypes[$producttype['eptid']] = new EndProducTypes($producttype['eptid']);
            }
            return $producttypes;
        }
        return false;
    }

    public function get_link() {
        global $core;
        return $core->settings['rootdir'].'/index.php?module=profiles/endproducttypeprofile&amp;eptid='.$this->data[self::PRIMARY_KEY];
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        if(is_array($attributes_param)) {
            foreach($attributes_param as $attr => $val) {
                $attributes .= $attr.'="'.$val.'"';
            }
        }
        return '<a href="'.$this->get_link().'" '.$attributes.'>'.$this->get_displayname().'</a>';
    }

    public function get_displayname() {
        return $this->data[self::DISPLAY_NAME];
    }

    public function get_primarykey() {
        return $this->data[self::PRIMARY_KEY];
    }

    public function get_errorcode() {
        return $this->errorcode;
    }

}
?>
