<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Chemfunctionproducts_class.php
 * Created:        @tony.assaad    Dec 3, 2013 | 5:09:33 PM
 * Last Update:    @tony.assaad    Dec 3, 2013 | 5:09:33 PM
 */

/**
 * Description of Chemfunctionproducts_class
 *
 * @author tony.assaad
 */
class ChemFunctionProducts extends AbstractClass {
    protected $data = array();
    private $segmentapplicationfunction = null;

    const PRIMARY_KEY = 'cfpid';
    const TABLE_NAME = 'chemfunctionproducts';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;
    const UNIQUE_ATTRS = null;

    protected $errorcode = 0;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

//    private function read($id, $simple) {
//        global $db;
//        $query_select = '*';
//        if($simple == true) {
//            $query_select = 'cfpid, pid, safid';
//        }
//        $this->data = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'chemfunctionproducts WHERE cfpid='.intval($id)));
//    }

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

    public function get_chemicalfunction() {
        if(is_object($this->segmentapplicationfunction)) {
            return $this->segmentapplicationfunction->get_function();
        }
        else {
            return $this->get_segapplicationfunction()->get_function();
        }
    }

    public function get_segment() {
        return $this->get_segmentapplication()->get_segment();
    }

    public function get_produt() {
        return new Products($this->data['pid']);
    }

    public function get_createdby() {
        return new Users($this->data['createdBy']);
    }

    public function get_modifiedby() {
        return new Users($this->data['modifiedBy']);
    }

    public function __get($attr) {
        if(isset($this->data[$attr])) {
            return $this->data[$attr];
        }
        return false;
    }

    public function get() {
        return $this->data;
    }

    protected function create(array $data) {

    }

    protected function update(array $data) {

    }

}
?>
