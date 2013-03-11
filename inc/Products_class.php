<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: Products_class.php
 * Created:        @tony.assaad    Mar 11, 2013 | 2:12:19 PM
 * Last Update:    @tony.assaad    Mar 11, 2013 | 2:12:19 PM
 */

/**
 * Description of Products_class
 *
 * @author tony.assaad
 */
class Products {
	private $product = array();

	public function __construct($id, $simple = true) {
		if(isset($id)) {
			$this->read($id);
		}
	}

	private function read($id) {
		global $db;
		$this->product = $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.'products WHERE pid='.$id));
	}

	public function get_generic_product() {
		global $db;
		return $this->product['genericproduct'] = $db->fetch_assoc($db->query("SELECT gp.psid,gp.title,gp.description FROM ".Tprefix." genericproducts  gp 
								JOIN  ".Tprefix."products p ON(p.gpid=gp.gpid) WHERE p.pid=".$this->product['pid'].""));
	}

	public function get_product_segment() {
		global $db;
		return $this->product['productsegment'] = $db->fetch_assoc($db->query("SELECT gp.psid,ps.title FROM ".Tprefix."genericproducts  gp 
								JOIN  ".Tprefix."products p ON(p.gpid=gp.gpid) JOIN  ".Tprefix."productsegments  ps ON(gp.psid=ps.psid) 
								WHERE p.gpid=".$this->product['gpid'].""));
	}

	public function get_product_supplier() {
		$entitie = new Entities($this->product['spid']);
		return $this->product['supplier'] = $entitie->get();
	}

	public function get() {
		return $this->product;
	}

}
?>
