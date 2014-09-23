<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Entbrandsproducts_class.php
 * Created:        @tony.assaad    Dec 4, 2013 | 1:19:43 PM
 * Last Update:    @tony.assaad    Dec 4, 2013 | 1:19:43 PM
 */

/**
 * Description of Entbrandsproducts_class
 *
 * @author tony.assaad
 */
class EntBrandsProducts extends AbstractClass {
    protected $data = array();
    protected $errorcode = 0;

    const PRIMARY_KEY = 'ebpid';
    const TABLE_NAME = 'entitiesbrandsproducts';
    const DISPLAY_NAME = '';
    const SIMPLEQ_ATTRS = 'ebpid, ebid, eptid';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public static function get_producttypes_bybrand($id) {
        global $db;

        if(!empty($id)) {
            $query = $db->query('SELECT eptid FROM '.Tprefix.'entitiesbrandsproducts WHERE ebid="'.$db->escape_string($id).'"');
            while($endproduct = $db->fetch_assoc($query)) {
                $endproducts[$endproduct['eptid']] = new EndProducTypes($endproduct['eptid']);
            }
            return $endproducts;
        }
        return false;
    }

    public static function get_entitiesbrandsproducts_bybrand($id) {
        global $db;

        if(!empty($id)) {
            $query = $db->query('SELECT ebpid FROM '.Tprefix.'entitiesbrandsproducts WHERE ebid="'.$db->escape_string($id).'"');
            while($brandproduct = $db->fetch_assoc($query)) {
                $brandproducts[$brandproduct['ebpid']] = new Entbrandsproducts($brandproduct['ebpid']);
            }
            return $brandproducts;
        }
        return false;
    }

    public static function get_entbrandsproducts($filter_where = '') {
        global $db;

        /* Need to put order, filter, and limit
         * Need to put order, filter, and limit
         * Need to put order, filter, and limit
         * Need to put order, filter, and limit
         */

        $query = $db->query('SELECT * FROM '.Tprefix.'entitiesbrandsproducts '.$filter_where.'');
        while($rows = $db->fetch_assoc($query)) {
            $entbrandsproducts[$rows['ebpid']] = new Entbrandsproducts($rows['ebpid']);
        }
        return $entbrandsproducts;
    }

    public function get_entitybrand() {
        return new EntitiesBrands($this->data['ebid']);
    }

    public function get_endproduct() {
        if($this->data['eptid'] == 0) {
            return;
        }
        return new EndProducTypes($this->data['eptid']);
    }

    public function get_createdby() {
        return new Users($this->data['createdBy']);
    }

    public function get_modifiedby() {
        return new Users($this->data['modifiedBy']);
    }

    protected function create(array $data) {

    }

    protected function update(array $data) {

    }

    public function save(array $data = array()) {

    }

}
?>
