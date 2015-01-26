<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Productsegcoord_class.php
 * Created:        @tony.assaad    Dec 4, 2013 | 11:53:25 AM
 * Last Update:    @tony.assaad    Dec 4, 2013 | 11:53:25 AM
 */

/**
 * Description of Productsegcoord_class
 *
 * @author tony.assaad
 */
class ProdSegCoordinators extends AbstractClass {
    const PRIMARY_KEY = 'pscid';
    const TABLE_NAME = 'productsegmentcoordinators';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = '*';
    const CLASSNAME = __CLASS__;

    protected $data = array();

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

//    private function read($id, $simple) {
//        global $db;
//        $query_select = '*';
//        if($simple == true) {
//            $query_select = 'pscid, psid, uid';
//        }
//        $this->prodsegcoordinator = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'productsegmentcoordinators WHERE pscid='.intval($id)));
//    }

    public function get_segment() {
        return new ProductsSegments($this->data['psid']);
    }

    public function get_coordinator() {
        return new Users($this->data['uid']);
    }

    public function get_createdby() {
        return new Users($this->data['createdBy']);
    }

    public function get_modifiedby() {
        return new Users($this->data['modifiedBy']);
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
