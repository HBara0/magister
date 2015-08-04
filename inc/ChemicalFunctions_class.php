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
class ChemicalFunctions extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'cfid';
    const TABLE_NAME = 'chemicalfunctions';
    const DISPLAY_NAME = 'title';
    const SIMPLEQ_ATTRS = 'cfid, name, title, description,publishOnWebsite';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = 'name';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data = array()) {
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

            $data['title'] = $core->sanitize_inputs($data['title'], array('removetags' => true, 'method' => 'striponly'));
            if(empty($data['name'])) {
                $data['name'] = strtolower($data['title']);
                $data['name'] = preg_replace('/\s+/', '', $data['name']);
            }
            $chemicalfunctions_data = array(
                    'name' => $data['name'],
                    'publishOnWebsite' => 0,
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'createdBy' => $core->user['uid'],
                    'createdOn' => TIME_NOW
            );
            if(isset($data['publishOnWebsite']) && !empty($data['publishOnWebsite'])) {
                if($data['publishOnWebsite'] == 1) {
                    $chemicalfunctions_data['publishOnWebsite'] = 1;
                }
            }
            $query = $db->insert_query('chemicalfunctions', $chemicalfunctions_data);
            if($query) {
                $this->data[self::PRIMARY_KEY] = $db->last_id();
                if(!empty($data['segapplications']) && isset($data['segapplications']) && !empty($this->data[self::PRIMARY_KEY])) {
                    foreach($data['segapplications'] as $psaid) {
                        $segappfuncquery = $db->insert_query('segapplicationfunctions', array('cfid' => $this->data['cfid'], 'publishOnWebsite' => $chemicalfunctions_data['publishOnWebsite'], 'psaid' => $psaid, 'description' => $data['description'], 'createdBy' => $core->user['uid'], 'createdOn' => TIME_NOW));
                        if($segappfuncquery) {
                            $data['safid'] = $db->last_id();
                        }
                    }
                }
                $log->record('createchemicalfunctions', $data['cfid']);
                $this->errorcode = 0;
                return $this;
            }
        }
    }

    protected function update(array $data = array()) {
        global $db, $core;
        if(!isset($data['cfid']) && empty($data['cfid'])) {
            $this->errorcode = 2;
            return $this;
        }
        $segapplications = $data['segapplications'];
        if($data['publishOnWebsite'] != 1) {
            $data['publishOnWebsite'] = 0;
        }
        unset($data['segapplications']);
        $newalias = generate_alias($data['title']);
        if(!is_object(ChemicalFunctions::get_data(array('name' => $newalias, self::PRIMARY_KEY => $this->data[self::PRIMARY_KEY]), array('operators' => array(self::PRIMARY_KEY => 'NOT IN'))))) {
            $data['name'] = $newalias;
        }
        $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
        $segapfunctions_existingobjs = SegApplicationFunctions::get_data(array(self::PRIMARY_KEY => $this->data[self::PRIMARY_KEY]), array('returnarray' => true));
        if(is_array($segapfunctions_existingobjs)) {
            foreach($segapfunctions_existingobjs as $segapfunction_obj) {
                if($data['publishOnWebsite'] == 0) {
                    $db->update_query(segapplicationfunctions, array('publishOnWebsite' => "0"), 'safid ='.$segapfunction_obj->safid);
                }
            }
        }
        if(!empty($segapplications) && isset($segapplications)) {
            foreach($segapplications as $psaid) {
                if(!SegApplicationFunctions::get_data(array(self::PRIMARY_KEY => $this->data[self::PRIMARY_KEY], 'psaid' => $psaid))) {
                    $db->insert_query('segapplicationfunctions', array(self::PRIMARY_KEY => $data['cfid'], 'publishOnWebsite' => $data['publishOnWebsite'], 'psaid' => $psaid, 'description' => $data['description'], 'createdBy' => $core->user['uid'], 'createdOn' => TIME_NOW));
                }
            }
        }

        return $this;
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
                return new self($id);
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
                $chemicalfunctions[$chemicalfunction['cfid']] = new self($chemicalfunction['cfid']);
            }
            return $chemicalfunctions;
        }
        else {
            return false;
        }
    }

    /* return multilples SegmentApplications object for the current chemicalfunction */
    public function get_applications() {
        global $db;
        $query = $db->query('SELECT safid, psaid FROM '.Tprefix.'segapplicationfunctions WHERE cfid='.$this->data['cfid']);
        if($db->num_rows($query) > 0) {
            while($application = $db->fetch_assoc($query)) {
                $applications[$application['safid']] = new SegmentApplications($application['psaid']);
            }
            return $applications;
        }
        else {
            return false;
        }
    }

    public function get_segmentapplicationfunction() {
        global $db;
        $query = $db->query('SELECT safid FROM '.Tprefix.'segapplicationfunctions WHERE cfid='.$this->data['cfid']);
        if($db->num_rows($query) > 0) {
            while($applicationfunction = $db->fetch_assoc($query)) {
                $applicationfunctions[$applicationfunction['safid']] = new SegApplicationFunctions($applicationfunction['safid']);
            }
            return $applicationfunctions;
        }
        else {
            return false;
        }
    }

}
?>
