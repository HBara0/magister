<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * Products Segments Class
 * $id: ProductsSegments.php
 * Created:        @zaher.reda    Oct 7, 2013 | 3:47:19 PM
 * Last Update:    @zaher.reda    Oct 7, 2013 | 3:47:19 PM
 */

class ProductsSegments {
	private $segment = array();

	public function __construct($id, $simple = true) {
		if(isset($id)) {
			$this->read($id, $simple);
		}
	}

	private function read($id, $simple ) {
		global $db;

		$query_select = '*';
		if($simple == true) {
			$query_select = 'psid, title';
		}

		$this->segment = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'productsegments WHERE psid='.intval($id)));
	}

	public static function get_segment_byname($name) {
		global $db;

		if(!empty($name)) {
			$id = $db->fetch_field($db->query('SELECT psid FROM '.Tprefix.'segment WHERE title="'.$db->escape_string($name).'"'), 'psid');
			if(!empty($id)) {
				return new ProductsSegments($id);
			}
		}
		return false;
	}

	public static function get_segments() {
		global $db;
		$query = $db->query('SELECT psid  FROM '.Tprefix.'productsegments');
		if($db->num_rows($query) > 0) {
			while($rowsegment = $db->fetch_assoc($query)) {
				$segments[$rowsegment['psid']] = new ProductsSegments($rowsegment['psid']);
			}
			return $segments;
		}
		else {
			return false;
		}
	}

//get coordinators as obj
	public function get() {
		return $this->segment;
	}

}
?>
