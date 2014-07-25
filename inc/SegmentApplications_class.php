<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Segmentapplications.php
 * Created:        @tony.assaad    Dec 3, 2013 | 3:58:53 PM
 * Last Update:    @tony.assaad    Dec 3, 2013 | 3:58:53 PM
 */

/**
 * Description of Segmentapplications
 *
 * @author tony.assaad
 */
class SegmentApplications {
    private $data = array();

    const PRIMARY_KEY = 'psaid';
    const TABLE_NAME = 'segmentapplications';
    const DISPLAY_NAME = 'title';

    public function __construct($id = '', $simple = true) {
        if(isset($id)) {
            $this->read($id, $simple);
        }
    }

    private function read($id, $simple = true) {
        global $db;
        $query_select = '*';
        if($simple == true) {
            $query_select = 'psaid, name, psid, title';
        }
        $this->data = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public function create($data = array()) {
        global $db, $core, $log;

        if(is_array($data)) {
            if(empty($data['title'])) {
                $this->errorcode = 1;
                return false;
            }

            if(value_exists(self::TABLE_NAME, 'title', $data['title'], 'psid='.$data['psid'])) {
                $this->errorcode = 2;
                return false;
            }

            if(empty($data['name']) && !isset($data['name'])) {
                $data['name'] = strtolower($data['title']);
                $data['name'] = preg_replace('/\s+/', '', $data['name']);
            }

            $data['title'] = $core->sanitize_inputs($data['title'], array('removetags' => true, 'method' => 'striponly'));
            $segapplication_data = array(
                    'psid' => $data['psid'],
                    'title' => $data['title'],
                    'name' => $data['name'],
                    'createdBy' => $core->user['uid'],
                    'createdOn' => TIME_NOW
            );
            $query = $db->insert_query(self::TABLE_NAME, $segapplication_data);
            if($query) {
                $this->data[self::PRIMARY_KEY] = $db->last_id();
                if(!empty($data['segappfunctions']) && isset($data['segappfunctions'])) {
                    foreach($data['segappfunctions'] as $cfid) {
                        $segappfuncquery = $db->insert_query('segapplicationfunctions', array('cfid' => $cfid, 'psaid' => $this->data[self::PRIMARY_KEY], 'createdBy' => $core->user['uid'], 'createdOn' => TIME_NOW));
                        if($segappfuncquery) {
                            $data['safid'] = $db->last_id();
                        }
                    }
                }

                $log->record('createsegappfunctions', $this->data[self::PRIMARY_KEY]);
                $this->errorcode = 0;
                return true;
            }
        }
    }

    public static function get_segmentsapplications_legacy() {
        global $db, $core;

        /* Need to put filter
         * Need to put filter
         * Need to put filter
         * Need to put filter
         */

        $sort_query = ' ORDER BY  title  ASC';
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = ' ORDER BY '.$core->input['sortby'].' '.$core->input['order'];
        }

        if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
            $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
        }

        $limit_start = 0;
        if(isset($core->input['start'])) {
            $limit_start = $db->escape_string($core->input['start']);
        }

        $query = $db->query("SELECT psaid FROM ".Tprefix.self::TABLE_NAME."{$sort_query} LIMIT {$limit_start}, {$core->settings['itemsperlist']}");
        if($db->num_rows($query) > 0) {
            while($rowsegapp = $db->fetch_assoc($query)) {
                $segments_applications[$rowsegapp['psaid']] = new self($rowsegapp['psaid']);
            }
            return $segments_applications;
        }
        else {
            return false;
        }
    }

    public static function get_segmentsapplications($filters = '', $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_segappfunctions() {
        global $db;
        $query = $db->query('SELECT cfid, safid FROM '.Tprefix.'segapplicationfunctions WHERE psaid="'.intval($this->data['psaid']).'"');
        if($db->num_rows($query) > 0) {
            while($rowsegmentappfunc = $db->fetch_assoc($query)) {
                $segmentsappfunc[$rowsegmentappfunc['safid']] = new ChemicalFunctions($rowsegmentappfunc['cfid']);
            }
            return $segmentsappfunc;
        }
        else {
            return false;
        }
    }

    public function get_endproduct() {
        global $db;

        $query = $db->query('SELECT eptid FROM '.Tprefix.'endproducttypes WHERE psaid="'.$this->data['psaid'].'"');
        if($db->num_rows($query) > 0) {
            while($endproduct = $db->fetch_assoc($query)) {
                $endproducts[$endproduct['eptid']] = new EndProducTypes($endproduct['eptid']);
            }
            return $endproducts;
        }
        else {
            return false;
        }
    }

    public function get_segment() {
        return new ProductsSegments($this->data['psid']);
    }

    public static function get_application_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public function save(array $data = array()) {
        if(value_exists(self::TABLE_NAME, self::PRIMARY_KEY, $this->data[self::PRIMARY_KEY])) {
            //Update
        }
        else {
            if(empty($data)) {
                $data = $this->data;
            }
            $this->create($data);
        }
    }

    public function __get($attr) {
        if(isset($this->data[$attr])) {
            return $this->data[$attr];
        }
        return false;
    }

    public function __isset($name) {
        return isset($this->data[$name]);
    }

    public function get() {
        return $this->data;
    }

    public function get_displayname() {
        return $this->data[self::DISPLAY_NAME];
    }

    public function set(array $data) {
        foreach($data as $name => $value) {
            $this->data[$name] = $value;
        }
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function get_errorcode() {
        if(is_object($this)) {
            return $this->errorcode;
        }
        else {
            return $errorcode;
        }
    }

}
?>
