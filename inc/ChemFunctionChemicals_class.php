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
class ChemFunctionChemicals {
    private $chemfuntionchemical = array();
    private $segmentapplicationfunction = null;

    public function __construct($id, $simple = true) {
        if(isset($id)) {
            $this->read($id, $simple);
        }
    }

    private function read($id, $simple) {
        global $db;
        $query_select = '*';
        if($simple == true) {
            $query_select = 'cfcid, safid, csid';
        }
        $this->chemfuntionchemical = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'chemfunctionchemcials WHERE cfcid='.intval($id)));
    }

    /* return segmentapplication object */
    public function get_segapplicationfunction() {
        $this->segmentapplicationfunction = new SegApplicationFunctions($this->chemfuntionchemical['safid']);  /* we store object in the var to avoid multiple instantiation thus will avoid multiple queries */
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

    public function get_chemicalfunction() {
        if(is_object($this->segmentapplicationfunction)) {
            return $this->segmentapplicationfunction->get_function();
        }
        else {
            return $this->get_segapplicationfunction()->get_function();
        }
    }

    public function get_chemical() {
        return new Chemicalsubstances($this->chemfuntionchemical['csid']);
    }

    public function get_createdby() {
        return new Users($this->chemfuntionchemical['createdBy']);
    }

    public function get_modifiedby() {
        return new Users($this->chemfuntionchemical['modifiedBy']);
    }

    public function get() {
        return $this->chemfuntionchemical;
    }

}
?>
