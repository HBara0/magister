<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * Products Class
 * $id: Products_class.php
 * Created:        @tony.assaad    Mar 11, 2013 | 2:12:19 PM
 * Last Update:    @tony.assaad    Mar 11, 2013 | 2:12:19 PM
 */

class Products {
    private $product = array();

    const UNIQUE_ATTRS = '';
    const PRIMARY_KEY = 'pid';
    const TABLE_NAME = 'products';
    const DISPLAY_NAME = 'name';

    public function __construct($id, $simple = true) {
        if(isset($id)) {
            $this->read($id, $simple);
        }
        return null;
    }

    private function read($id, $simple) {
        global $db;

        $query_select = '*';
        if($simple == true) {
            $query_select = 'pid, name, spid, gpid, defaultFunction';
        }

        $this->product = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'products WHERE pid='.intval($id)));
    }

    public function get_genericproduct_legacy() {
        global $db;
        return $this->product['genericproduct'] = $db->fetch_assoc($db->query("SELECT gp.*
								FROM ".Tprefix."genericproducts gp
								JOIN ".Tprefix."products p ON (p.gpid=gp.gpid)
								WHERE p.pid=".$this->product['pid'].""));
    }

    public function get_genericproduct() {
        return new GenericProducts($this->product['gpid']);
    }

    public function get_segment() {
        global $db;
        return $this->product['productsegment'] = $db->fetch_assoc($db->query("SELECT gp.psid, ps.title, ps.titleAbbr
								FROM ".Tprefix."genericproducts gp
								JOIN ".Tprefix."products p ON (p.gpid=gp.gpid)
								JOIN ".Tprefix."productsegments ps ON (gp.psid=ps.psid)
								WHERE p.gpid=".$this->product['gpid'].""));
    }

    public function get_productsegment() {
        if(!empty($this->product['defaultFunction'])) {
            return $this->get_defaultchemfunction()->get_segment();
        }
        else {
            return $this->get_genericproduct()->get_segment();
        }
    }

    public function get_supplier() {
        return new Entities($this->product['spid'], '', true);
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
        $query = $db->query("SELECT cfpid FROM ".Tprefix."chemfunctionproducts WHERE pid=".$db->escape_string($this->product['pid']));
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
        $query = $db->query("SELECT csid FROM ".Tprefix."productschemsubstances WHERE pid=".$db->escape_string($this->product['pid']));
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
        return new ChemFunctionProducts($this->product['defaultFunction']);
    }

    public function get() {
        return $this->product;
    }

    /**
     *
     * @return Srtring
     */
    public function get_displayname() {
        return $this->product[self::DISPLAY_NAME];
    }

    public function __get($attr) {
        if(isset($this->product[$attr])) {
            return $this->product[$attr];
        }
        return false;
    }

    public function __isset($name) {
        return isset($this->product[$name]);
    }

    public function parse_link($attributes_param = array('target' => '_blank')) {
        if(!empty($this->product['companyNameAbbr'])) {
            $this->product['companyName'] .= ' ('.$this->product['companyNameAbbr'].')';
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
        return $core->settings['rootdir'].'/index.php?module=profiles/products&amp;pid='.$this->product[self::PRIMARY_KEY];
    }

}
?>