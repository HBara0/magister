<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Segmentapplications.php
 * Created:        @tony.assaad    Dec 3, 2013 | 3:58:53 PM
 * Last Update:    @tony.assaad    Dec 3, 2013 | 3:58:53 PM
 */

/**
 * Description of Segmentapplications
 *
 * @author tony.assaad
 */
class Segmentapplications {
	private $segmentapplication = array();

	public function __construct($id = '', $simple = true) {
		if(isset($id)) {
			$this->read($id, $simple);
		}
	}

	private function read($id, $simple = true) {
		global $db;
		$query_select = '*';
		if($simple == true) {
			$query_select = 'psaid, name, psid, title';
		}
		$this->segmentapplication = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'segmentapplications WHERE psaid='.intval($id)));
	}

	public function create($data = array()) {
		global $db, $core, $log;

		if(is_array($data)) {
			if(empty($data['title'])) {
				$this->errorcode = 1;
				return false;
			}

			if(value_exists('segmentapplications', 'title', $data['title'], 'psid='.$data['psid'])) {
				$this->errorcode = 2;
				return false;
			}

			if(empty($data['name']) && !isset($data['name'])) {
				$data['name'] = strtolower($data['title']);
				$data['name'] = preg_replace('/\s+/', '', $data['name']);
			}

			$data['title'] = $core->sanitize_inputs($data['title'], array('removetags' => true));
			$segapplication_data = array(
					'psid' => $data['psid'],
					'title' => $data['title'],
					'name' => $data['name'],
					'createdBy' => $core->user['uid'],
					'createdOn' => TIME_NOW
			);
			$query = $db->insert_query('segmentapplications', $segapplication_data);
			if($query) {
				$data['psaid'] = $db->last_id();
				if(!empty($data['segappfunctions']) && isset($data['segappfunctions'])) {
					foreach($data['segappfunctions'] as $cfid) {
						$segappfuncquery = $db->insert_query('segapplicationfunctions', array('cfid' => $cfid, 'psaid' => $data['psaid'], 'createdBy' => $core->user['uid'], 'createdOn' => TIME_NOW));
						if($segappfuncquery) {
							$data['safid'] = $db->last_id();
						}
					}
				}
				$log->record('createsegappfunctions', $data['psaid']);
				$this->errorcode = 0;
				return true;
			}
		}
	}

	public static function get_segmentsapplications() {
		global $db, $core;

		/* Need to put filter
		 * Need to put filter
		 * Need to put filter
		 * Need to put filter
		 */

		$sort_query = ' ORDER BY  title  ASC';
		if(isset($core->input['sortby'], $core->input['order'])) {
			$sort_query = ' ORDER BY '.$core->input['sortby'].' '.$core->input['order'];
		}

		if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
			$core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
		}

		$limit_start = 0;
		if(isset($core->input['start'])) {
			$limit_start = $db->escape_string($core->input['start']);
		}

		$query = $db->query("SELECT psaid FROM ".Tprefix."segmentapplications{$sort_query} LIMIT {$limit_start}, {$core->settings['itemsperlist']}");
		if($db->num_rows($query) > 0) {
			while($rowsegapp = $db->fetch_assoc($query)) {
				$segments_applications[$rowsegapp['psaid']] = new Segmentapplications($rowsegapp['psaid']);
			}
			return $segments_applications;
		}
		else {
			return false;
		}
	}

	public function get_segappfunctions() {
		global $db;
		$query = $db->query('SELECT cfid,safid  FROM '.Tprefix.'segapplicationfunctions WHERE psaid="'.intval($this->segmentapplication['psaid']).'"');
		if($db->num_rows($query) > 0) {
			while($rowsegmentappfunc = $db->fetch_assoc($query)) {
				$segmentsappfunc[$rowsegmentappfunc['safid']] = new Chemicalfunctions($rowsegmentappfunc['cfid']);
			}
			return $segmentsappfunc;
		}
		else {
			return false;
		}
	}

	public function get_endproduct() {
		global $db;

		$query = $db->query('SELECT eptid FROM '.Tprefix.'endproducttypes WHERE psaid="'.$this->segmentapplication['psaid'].'"');
		if($db->num_rows($query) > 0) {
			while($endproduct = $db->fetch_assoc($query)) {
				$endproducts[$endproduct['eptid']] = new Endproductypes($endproduct['eptid']);
			}
			return $endproducts;
		}
		else {
			return false;
		}
	}

	public function get_segment() {
		return new ProductsSegments($this->segmentapplication['psid']);
	}

	public function get() {
		return $this->segmentapplication;
	}

	public function get_errorcode() {
		if(is_object($this)) {
			return $this->errorcode;
		}
		else {
			return $errorcode;
		}
	}

}
?>