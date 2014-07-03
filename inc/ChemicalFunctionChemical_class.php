<?php
/*
 * Copyright © 2014 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: ChemicalFunctionChemical_class.php
 * Created:        @tony.assaad    Jul 2, 2014 | 1:38:36 PM
 * Last Update:    @tony.assaad    Jul 2, 2014 | 1:38:36 PM
 */

/**
 * Description of ChemicalFunctionChemical_class
 *
 * @author tony.assaad
 */
class ChemicalFunctionChemical {
    private $data = array();

    const PRIMARY_KEY = 'cfcid';
    const TABLE_NAME = 'chemfunctionchemcials';

    public function __construct($id, $simple = true) {
        if(!empty($id)) {
            $this->data = $this->read($id, $simple);
        }
    }

    private function read($id, $simple = true) {
        global $db;
        return $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.self::TABLE_NAME.' WHERE '.self::PRIMARY_KEY.'='.intval($id)));
    }

    public static function get_checmicalfunction($filters = null, array $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_segapplicationfunction() {
        $this->segmentapplicationfunction = new SegApplicationFunctions($this->data['safid']);  /* we store object in the var to avoid multiple instantiation thus will avoid multiple queries */
        return $this->segmentapplicationfunction;
    }

    public function get_chemicalfunction() {
        if(is_object($this->segmentapplicationfunction)) {
            return $this->segmentapplicationfunction->get_function();
        }
        else {
            return $this->get_segapplicationfunction()->get_function();
        }
    }

    public function get_chemicalsubstance() {
        return new Chemicalsubstances($this->data['csid']);
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

}