<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Chemicalfunctionproduct.php
 * Created:        @tony.assaad    Dec 3, 2013 | 4:34:44 PM
 * Last Update:    @tony.assaad    Dec 3, 2013 | 4:34:44 PM
 */

/**
 * Description of Chemicalfunctionproduct
 *
 * @author tony.assaad
 */
class Chemicalfunctions {
    private $chemfunction = array();

    const PRIMARY_KEY = 'cfid';
    const TABLE_NAME = 'chemicalfunctions';

    public function __construct($id = '', $simple = true) {
        if(isset($id)) {
            $this->read($id, $simple);
        }
    }

    private function read($id, $simple) {
        global $db;
        $query_select = '*';
        if($simple == true) {
            $query_select = 'cfid, name, title';
        }
        $this->chemfunction = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'chemicalfunctions WHERE cfid='.intval($id)));
    }

    public function create($data = array()) {
        global $db, $core, $log;
        if(empty($data['title'])) {
            $this->errorcode = 1;
            return false;
        }

        if(is_array($data)) {
            if(value_exists('chemicalfunctions', 'title', $data['title'])) {
                $this->errorcode = 2;
                return false;
            }

            $data['title'] = $core->sanitize_inputs($data['title'], array('removetags' => true));
            if(empty($data['name']) && !isset($data['name'])) {
                $data['name'] = strtolower($data['title']);
                $data['name'] = preg_replace('/\s+/', '', $data['name']);
            }

            $chemicalfunctions_data = array(
                    'name' => $data['name'],
                    'title' => $data['title'],
                    'createdBy' => $core->user['uid'],
                    'createdOn' => TIME_NOW
            );
            $query = $db->insert_query('chemicalfunctions', $chemicalfunctions_data);
            if($query) {
                $this->chemfunction[self::PRIMARY_KEY] = $data['cfid'] = $db->last_id();
                if(!empty($data['segapplications']) && isset($data['segapplications'])) {
                    foreach($data['segapplications'] as $psaid) {
                        $segappfuncquery = $db->insert_query('segapplicationfunctions', array('cfid' => $data['cfid'], 'psaid' => $psaid, 'createdBy' => $core->user['uid'], 'createdOn' => TIME_NOW));
                        if($segappfuncquery) {
                            $data['safid'] = $db->last_id();
                        }
                    }
                }
                $log->record('createchemicalfunctions', $data['cfid']);
                $this->errorcode = 0;
                return true;
            }
        }
    }

    public static function get_chemfunction_byattr($attr, $value) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects_byattr($attr, $value);
    }

    public static function get_chemfunction_byname($name) {
        global $db;
        if(!empty($name)) {
            $id = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.'chemicalfunctions WHERE name="'.$db->escape_string($name).'"'), 'cfid');
            if(!empty($id)) {
                return new Chemicalfunctions($id);
            }
        }
        return false;
    }

    public static function get_functions($filters = '') {
        global $db, $core;

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
        $query = $db->query("SELECT cfid FROM ".Tprefix."chemicalfunctions{$sort_query} LIMIT {$limit_start}, {$core->settings['itemsperlist']}");
        if($db->num_rows($query) > 0) {
            while($chemicalfunction = $db->fetch_assoc($query)) {
                $chemicalfunctions[$chemicalfunction['cfid']] = new Chemicalfunctions($chemicalfunction['cfid']);
            }
            return $chemicalfunctions;
        }
        else {
            return false;
        }
    }

    /* return multilples Segmentapplications object for the current chemicalfunction */
    public function get_applications() {
        global $db;
        $query = $db->query('SELECT safid, psaid FROM '.Tprefix.'segapplicationfunctions WHERE cfid='.$this->chemfunction['cfid']);
        if($db->num_rows($query) > 0) {
            while($application = $db->fetch_assoc($query)) {
                $applications[$application['safid']] = new Segmentapplications($application['psaid']);
            }
            return $applications;
        }
        else {
            return false;
        }
    }

    public function get_createdby() {
        return new Users($this->chemfunction['createdBy']);
    }

    public function get_modifiedby() {
        return new Users($this->chemfunction['modifiedBy']);
    }

    public function get() {
        return $this->chemfunction;
    }

    public function save(array $data = array()) {
        if(value_exists(self::TABLE_NAME, self::PRIMARY_KEY, $this->segmentapplication[self::PRIMARY_KEY])) {
            //Update
        }
        else {
            if(empty($data)) {
                $data = $this->chemfunction;
            }
            $this->create($data);
        }
    }

    public function set(array $data) {
        foreach($data as $name => $value) {
            $this->chemfunction[$name] = $value;
        }
    }

    public function __set($name, $value) {
        $this->chemfunction[$name] = $value;
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
