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

	public function __construct($id, $simple = true) {
		if(isset($id)) {
			$this->read($id, $simple);
		}
	}

	private function read($id, $simple) {
		global $db;

		$query_select = '*';
		if($simple == true) {
			$query_select = 'pid, name, spid,gpid';
		}
		$this->product = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'products WHERE pid='.intval($id)));
	}

	public function get_generic_product() {
		global $db;
		return $this->product['genericproduct'] = $db->fetch_assoc($db->query("SELECT gp.* 
								FROM ".Tprefix."genericproducts gp 
								JOIN ".Tprefix."products p ON (p.gpid=gp.gpid) 
								WHERE p.pid=".$this->product['pid'].""));
	}

	public function get_segment() {
		global $db;
		return $this->product['productsegment'] = $db->fetch_assoc($db->query("SELECT gp.psid, ps.title 
								FROM ".Tprefix."genericproducts gp 
								JOIN ".Tprefix."products p ON (p.gpid=gp.gpid)
								JOIN ".Tprefix."productsegments ps ON (gp.psid=ps.psid) 
								WHERE p.gpid=".$this->product['gpid'].""));
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

	public function get() {
		return $this->product;
	}

}
?>