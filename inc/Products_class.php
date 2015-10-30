<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Products Class
 * $id: Products_class.php
 * Created:        @tony.assaad    Mar 11, 2013 | 2:12:19 PM
 * Last Update:    @tony.assaad    Mar 11, 2013 | 2:12:19 PM
 */

class Products extends AbstractClass {
    protected $data = array();

    const UNIQUE_ATTRS = '';
    const PRIMARY_KEY = 'pid';
    const TABLE_NAME = 'products';
    const DISPLAY_NAME = 'name';
    const SIMPLEQ_ATTRS = 'pid, name, spid, gpid, defaultFunction';
    const CLASSNAME = __CLASS__;

    public function __construct($id = '', $simple = true) {
        parent::__construct($id, $simple);
    }

    public function get_genericproduct_legacy() {
        global $db;
        return $this->data['genericproduct'] = $db->fetch_assoc($db->query("SELECT gp.*
								FROM ".Tprefix."genericproducts gp
								JOIN ".Tprefix."products p ON (p.gpid=gp.gpid)
								WHERE p.pid=".$this->data['pid'].""));
    }

    public function get_genericproduct() {
        return new GenericProducts($this->data['gpid']);
    }

    public function get_segment() {
        global $db;
        return $this->data['productsegment'] = $db->fetch_assoc($db->query("SELECT gp.psid, ps.title, ps.titleAbbr
								FROM ".Tprefix."genericproducts gp
								JOIN ".Tprefix."products p ON (p.gpid=gp.gpid)
								JOIN ".Tprefix."productsegments ps ON (gp.psid=ps.psid)
								WHERE p.gpid=".$this->data['gpid'].""));
    }

    public function get_productsegment() {
        if(!empty($this->data['defaultFunction'])) {
            return $this->get_defaultchemfunction()->get_segment();
        }
        else {
            return $this->get_genericproduct()->get_segment();
        }
    }

    public function get_supplier() {
        return new Entities($this->data['spid'], '', true);
    }

    public static function get_product_byname($name) {
        global $db;
        if(!empty($name)) {
            $id = $db->fetch_field($db->query('SELECT pid FROM '.Tprefix.'products WHERE name="'.$db->escape_string($name).'"'), 'pid');
            if(!empty($id)) {
                return new Products($id);
            }
        }
        return false;
    }

    public static function get_products($filters = '', $configs = array()) {
        $data = new DataAccessLayer(__CLASS__, self::TABLE_NAME, self::PRIMARY_KEY);
        return $data->get_objects($filters, $configs);
    }

    public function get_chemfunctionproducts() {
        global $db;
        $query = $db->query("SELECT cfpid FROM ".Tprefix."chemfunctionproducts WHERE pid=".$db->escape_string($this->data['pid']));
        if($db->num_rows($query) > 0) {
            while($chemfunctionproduct = $db->fetch_assoc($query)) {
                $chemfunctionproducts[$chemfunctionproduct['cfpid']] = new ChemFunctionProducts($chemfunctionproduct['cfpid']);
            }
            return $chemfunctionproducts;
        }
        return false;
    }

    public function get_chemicalsubstance() {
        global $db;
        if(empty($this->data['pid'])) {
            return false;
        }
        $query = $db->query("SELECT csid FROM ".Tprefix."productschemsubstances WHERE pid=".$db->escape_string($this->data['pid']));
        if($db->num_rows($query) > 0) {
            while($rowprodchemsubstance = $db->fetch_assoc($query)) {
                $productschemsubstances[$rowprodchemsubstance['csid']] = new Chemicalsubstances($rowprodchemsubstance['csid']);
            }
            return $productschemsubstances;
        }
        return false;
    }

    public function get_defaultchemfunction() {
//        if(empty($this->product['defaultFunction'])) {
//            return false;
//        }
        return new ChemFunctionProducts($this->data['defaultFunction']);
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        if(!empty($this->data['companyNameAbbr'])) {
            $this->data['companyName'] .= ' ('.$this->data['companyNameAbbr'].')';
        }

        if(is_array($attributes_param)) {
            foreach($attributes_param as $attr => $val) {
                $attributes .= $attr.'="'.$val.'"';
            }
        }
        return '<a href="'.$this->get_link().'" '.$attributes.'>'.$this->get_displayname().'</a>';
    }

    public function get_link() {
        global $core;
        return $core->settings['rootdir'].'/index.php?module=profiles/products&amp;pid='.$this->data[self::PRIMARY_KEY];
    }

    protected function update(array $data) {

    }

}
?>