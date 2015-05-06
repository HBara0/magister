<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: ChemFunctionchemicals_class.php
 * Created:        @tony.assaad    Dec 4, 2013 | 11:17:50 AM
 * Last Update:    @tony.assaad    Dec 4, 2013 | 11:17:50 AM
 */

/**
 * Description of ChemFunctionchemicals_class
 *
 * @author tony.assaad
 */
class ChemFunctionChemicals extends AbstractClass {
    protected $data = array();
    private $segmentapplicationfunction = null;

    const PRIMARY_KEY = 'cfcid';
    const TABLE_NAME = 'chemfunctionchemcials';
    const CLASSNAME = __CLASS__;
    const SIMPLEQ_ATTRS = 'cfcid, safid, csid';

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    protected function create(array $data) {
        global $db;

        $db->insert_query(self::TABLE_NAME, $data);
    }

    protected function update(array $data) {
        global $db;

        $db->update_query(self::TABLE_NAME, $data, self::PRIMARY_KEY.'='.intval($this->data[self::PRIMARY_KEY]));
    }

    public function save(array $data = array()) {
        if(empty($data)) {
            $data = $this->data;
        }


        $object = self::get_data(array('safid' => $data['safid'], 'csid' => $data['csid']));
        if(is_object($object)) {
            $object->update($data);
        }
        else {
            $object = new self();
            $object->create($data);
        }
    }

    /* return segmentapplication object */
    public function get_segapplicationfunction() {
        $this->segmentapplicationfunction = new SegApplicationFunctions($this->data['safid']);  /* we store object in the var to avoid multiple instantiation thus will avoid multiple queries */
        return $this->segmentapplicationfunction;
    }

    public function get_segmentapplication() {
        if(is_object($this->segmentapplicationfunction)) {
            return $this->segmentapplicationfunction->get_application();
        }
        else {
            return $this->get_segapplicationfunction()->get_application();
        }
    }

    public function get_segment() {
        return $this->get_segmentapplication()->get_segment();
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
        return $this->get_chemical();
    }

    public function get_chemical() {
        return new Chemicalsubstances($this->data['csid'], false);
    }

    public function get_createdby() {
        return new Users($this->data['createdBy']);
    }

    public function get_modifiedby() {
        return new Users($this->data['modifiedBy']);
    }

}
?>
