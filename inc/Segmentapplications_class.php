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

	public function __construct($id, $simple = true) {
		if(isset($id)) {
			$this->read($id, $simple);
		}
	}

	private function read($id, $simple = true) {
		global $db;
		$query_select = '*';
		if($simple == true) {
			$query_select = 'psaid, name,psid, title';
		}
		$this->segmentapplication = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'segmentapplications WHERE psaid='.intval($id)));
	}

	public static function create($data = array()) {
		global $db, $core, $log;
		if(empty($data['title'])) {
			$errorcode = 1;
			return false;
		}

		if(is_array($data)) {
			if(value_exists('segmentapplications', 'title', $data['title'], 'psid='.$data['psid'].'')) {
				$errorcode = 2;
				return false;
			}
			$data['title'] = $core->sanitize_inputs($data['title'], array('removetags' => true));
			$segapplication_data = array('psid' => $data['psid'],
					'title' => $data['title']
			);
			$query = $db->insert_query('segmentapplications', $segapplication_data);
			if($query) {
				$data['psaid'] = $db->last_id();
				if(!empty($data['segappfunctions']) && isset($data['segappfunctions'])) {
					foreach($data['segappfunctions'] as $cfid) {
						$segappfuncquery = $db->insert_query('segapplicationfunctions', array('cfid' => $cfid, 'psaid' => $data['psaid'], 'createdBy' => $core->user['uid']));
						if($segappfuncquery) {
							$data['safid'] = $db->last_id();
						}
					}
				}
			}
		}
	}

	public static function get_segmentsapplications() {
		global $db, $core;

		$sort_query = 'ORDER BY  title  ASC';
		if(isset($core->input['sortby'], $core->input['order'])) {
			$sort_query = 'ORDER BY '.$core->input['sortby'].' '.$core->input['order'];
		}

		if(isset($core->input['perpage']) && !empty($core->input['perpage'])) {
			$core->settings['itemsperlist'] = $db->escape_string($core->input['perpage']);
		}

		$limit_start = 0;
		if(isset($core->input['start'])) {
			$limit_start = $db->escape_string($core->input['start']);
		}
		$query = $db->query("SELECT psaid  FROM ".Tprefix."segmentapplications  {$sort_query} LIMIT  {$limit_start},{$core->settings['itemsperlist']}");
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

		$query = $db->query('SELECT  eptid  FROM '.Tprefix.'endproducttypes WHERE psaid="'.$this->segmentapplication['psaid'].'"');
		if($db->num_rows($query) > 0) {
			while($rowsegmentendproducts = $db->fetch_assoc($query)) {
				$segmentsendproduct[$rowsegmentendproducts['eptid']] = new Endproductypes($rowsegmentendproducts['eptid']);
			}
			return $segmentsendproduct;
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

}
?>
