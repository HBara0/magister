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

	public function __construct($id='', $simple = true) {
		if(isset($id)) {
			$this->read($id, $simple);
		}
	}

	private function read($id, $simple) {
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
		
		/* Need to put order, filter, and limit 
		 * Need to put order, filter, and limit 
		 * Need to put order, filter, and limit 
		 * Need to put order, filter, and limit 
		 */
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

	public function get_applications() {
		global $db;
		$query = $db->query('SELECT psaid  FROM '.Tprefix.'segmentapplications WHERE psid="'.$this->segment['psid'].'"');
		if($db->num_rows($query) > 0) {
			while($rowsegmentapp = $db->fetch_assoc($query)) {
				$segmentsapp[$rowsegmentapp['psaid']] = new Segmentapplications($rowsegmentapp['psaid']);
			}
			return $segmentsapp;
		}
		else {
			return false;
		}
	}

	public function get_coordinators() {
		global $db;
		$query = $db->query('SELECT pscid FROM '.Tprefix.'productsegmentcoordinators WHERE psid="'.$this->segment['psid'].'"');
		if($db->num_rows($query) > 0) {
			while($segmentcoordinator = $db->fetch_assoc($query)) {
				$segmentcoordinators[$segmentcoordinator['pscid']] = new ProdSegCoordinators($segmentcoordinator['pscid']);
			}
			return $segmentcoordinators;
		}
		else {
			return false;
		}
	}

	public function get_assignedemployees() {
		global $db;
		$query = $db->query('SELECT uid,emsid  FROM '.Tprefix.'employeessegments WHERE psid='.intval($this->segment['psid']).'');
		if($db->num_rows($query) > 0) {
			while($rowsegmentemployees = $db->fetch_assoc($query)) {
				$segmentsemployees[$rowsegmentemployees['emsid']] = new users($rowsegmentemployees['uid']);
			}
			return $segmentsemployees;
		}
		else {
			return false;
		}
	}

	public function get_entities() {
		global $db;

		$query = $db->query('SELECT e.eid  FROM '.Tprefix.'entities e JOIN '.Tprefix.'entitiessegments es  ON (es.eid=e.eid) 
							JOIN '.Tprefix.'productsegments p  ON (p.psid=es.psid) WHERE e.type="s" AND p.psid='.intval($this->segment['psid']).'');
		if($db->num_rows($query) > 0) {
			while($rowsegmentsuppliers = $db->fetch_assoc($query)) {
				$segmentsemployees[$rowsegmentsuppliers['esid']] = new Entities($rowsegmentsuppliers['eid']);
			}
			return $segmentsemployees;
		}
		else {
			return false;
		}
	}

	public function get_customers($filterpermission = '') {
		global $db;
		$query = $db->query('SELECT e.eid  FROM '.Tprefix.'entities e
							JOIN '.Tprefix.'entitiessegments es  ON (es.eid=e.eid) 
							JOIN '.Tprefix.'affiliatedentities a ON (e.eid=a.eid) 
							JOIN '.Tprefix.'affiliatedemployees ae ON (a.affid=ae.affid)
							JOIN '.Tprefix.'assignedemployees ase ON (ase.uid=ae.uid)			
							JOIN '.Tprefix.'productsegments p  ON (p.psid=es.psid) WHERE e.type="c" '.$filterpermission.' AND p.psid='.intval($this->segment['psid']).'');
		if($db->num_rows($query) > 0) {
			while($rowsegmentcustomers = $db->fetch_assoc($query)) {
				$segmentscustomers[$rowsegmentcustomers['eid']] = new Entities($rowsegmentcustomers['eid']);
			}
			return $segmentscustomers;
		}
		else {
			return false;
		}
	}

	public function get() {
		return $this->segment;
	}

}
?>
