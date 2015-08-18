<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Chemicalsubstances_class.php
 * Created:        @tony.assaad    Dec 4, 2013 | 11:39:58 AM
 * Last Update:    @tony.assaad    Dec 4, 2013 | 11:39:58 AM
 */

/**
 * Description of Chemicalsubstances_class
 *
 * @author tony.assaad
 */
class Chemicalsubstances extends AbstractClass {
    protected $data = array();

    const PRIMARY_KEY = 'csid';
    const TABLE_NAME = 'chemicalsubstances';
    const DISPLAY_NAME = 'name';
    const CLASSNAME = __CLASS__;
    const SIMPLEQ_ATTRS = 'csid, casNum,name';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function update(array $data) {

    }

    public function create(array $data) {
        global $db, $core, $errorhandler, $lang;

        if(is_empty($data['casNum'], $data['name'])) {
            $this->error_code = 1;
            return false;
        }

        if(value_exists('chemicalsubstances', 'casNum', $data['casNum']) || value_exists('chemicalsubstances', 'name', $data['name'])) {
            $this->error_code = 2;
            if(value_exists('chemicalsubstances', 'casNum', $data['casNum'])) {
                $field = 'casNum';
            }
            if(value_exists('chemicalsubstances', 'name', $data['name'])) {
                $field = 'name';
            }
            if(value_exists('chemicalsubstances', 'casNum', $data['casNum']) && value_exists('chemicalsubstances', 'name', $data['name'])) {
                $field = 'casNum, name';
            }
            $errorhandler->record('entryexists', $field);
            return false;
        }
        $chemical_data = array(
                'casNum' => $core->sanitize_inputs($data['casNum'], array('removetags' => true)),
                'name' => $core->sanitize_inputs($data['name'], array('removetags' => true)),
                'synonyms' => $core->sanitize_inputs($data['synonyms'], array('removetags' => true))
        );
        $query = $db->insert_query('chemicalsubstances', $chemical_data);
        if($query) {
            $this->data[self::PRIMARY_KEY] = $db->last_id();
            $chemfnctionchemical = new ChemFunctionChemicals();
            $chem_data['safid'] = 0;
            $chem_data['csid'] = $this->data[self::PRIMARY_KEY];
            $chem_data['createdBy'] = $core->user['uid'];
            $chem_data['createdOn'] = TIME_NOW;
            $chemfnctionchemical->set($chem_data);
            $chemfnctionchemical->save();
            $this->status = 0;
            return true;
        }
    }

    public static function get_chemical_byname($chemname) { /* return object of chemi */
        global $db;
        if(!empty($chemname)) {
            $id = $db->fetch_field($db->query('SELECT csid FROM '.Tprefix.'chemicalsubstances WHERE name="'.$db->escape_string($name).'"'), 'csid');
            if(!empty($id)) {
                return new Chemicalsubstances($id);
            }
        }
        return false;
    }

    public static function get_chemicalsubstances() {
        global $db, $core;

        $sort_query = ' ORDER BY name ASC';
        if(isset($core->input['sortby'], $core->input['order'])) {
            $sort_query = ' ORDER BY '.$core->input['sortby'].' '.$core->input['order'];
        }

        if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
            $core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
        }

        $limit_start = 1;
        if(isset($core->input['start'])) {
            $limit_start = $db->escape_string($core->input['start']);
        }

        $query = $db->query("SELECT csid  FROM ".Tprefix."chemicalsubstances{$sort_query} LIMIT {$limit_start}");
        if($db->num_rows($query) > 0) {
            while($chemicalsubstance = $db->fetch_assoc($query)) {
                $chemicalsubstances[$chemicalsubstance['csid']] = new Chemicalsubstances($chemicalsubstance['csid']);
            }
            return $chemicalsubstances;
        }
        return false;
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

    public function get_status() {
        return $this->error_code;
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        if(is_array($attributes_param)) {
            foreach($attributes_param as $attr => $val) {
                $attributes .= $attr.'="'.$val.'"';
            }
        }
        return '<a href="'.$this->get_link().'" '.$attributes.'>'.$this->get_displayname().'</a>';
    }

    public function get_link() {
        global $core;
        return $core->settings['rootdir'].'/index.php?module=profiles/chemicalsubstanceprofile&amp;csid='.$this->data[self::PRIMARY_KEY];
    }

    public function get_chemfunctionchemicals() {
        global $db;
        $query = $db->query("SELECT cfcid FROM ".Tprefix."chemfunctionchemcials WHERE csid=".$db->escape_string($this->data['csid']));
        if($db->num_rows($query) > 0) {
            while($chemfunctionchemical = $db->fetch_assoc($query)) {
                $chemfunctionchemicals[$chemfunctionchemical['cfcid']] = new ChemFunctionChemicals($chemfunctionchemical['cfcid']);
            }
            return $chemfunctionchemicals;
        }
        return false;
    }

}
?>
