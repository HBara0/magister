<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Endproducttpes_class.php
 * Created:        @tony.assaad    Dec 4, 2013 | 12:29:46 PM
 * Last Update:    @tony.assaad    Dec 4, 2013 | 12:29:46 PM
 */

/**
 * Description of Endproducttpes_class
 *
 * @author tony.assaad
 */
class Endproductypes {
	private $endproduct = array();

	public function __construct($id = '', $simple = false) {
		if(isset($id)) {
			$this->read($id, $simple);
		}
	}

	private function read($id, $simple) {
		global $db;
		$query_select = '*';
		if($simple == true) {
			$query_select = 'eptid,name,title';
		}
		$this->endproduct = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'endproducttypes WHERE eptid='.intval($id)));
	}

	public function create($data = array()) {
		global $db, $core, $log;
		if(empty($data['title'])) {
			$this->errorcode = 1;
			return false;
		}
		if(value_exists('endproducttypes', 'title', $data['title'])) {
			$this->errorcode = 2;
			return false;
		}
		$data['title'] = $core->sanitize_inputs($data['title'], array('removetags' => true));
		if(empty($data['name'])) {
			$data['name'] = $data['title'];
		}

		$endproducttypes_data = array('name' => $data['name'],
				'title' => $data['title'],
				'psaid' => $data['segapplications'],
				'createdBy' => $core->user['uid'],
				'createdOn' => TIME_NOW
		);
		$query = $db->insert_query('endproducttypes', $endproducttypes_data);
		$log->record('endproducttypes');
	}

	public function get_application() {
		return new Segmentapplications($this->endproduct['psaid']);
	}

	public function get_createdby() {
		return new Users($this->endproduct['createdBy']);
	}

	public function get_modifiedby() {
		return new Users($this->endproduct['modifiedBy']);
	}

	public static function get_producttype_byname($name) {
		global $db;
		if(!empty($name)) {
			$id = $db->fetch_field($db->query('SELECT eptid FROM '.Tprefix.'endproducttypes WHERE name="'.$db->escape_string($name).'"'), 'eptid');
			if(!empty($id)) {
				return new Endproductypes($id);
			}
		}
		return false;
	}

	public static function get_endproductypes() {
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
		$query = $db->query("SELECT eptid  FROM ".Tprefix."endproducttypes {$sort_query} LIMIT  {$limit_start},{$core->settings['itemsperlist']} ");
		if($db->num_rows($query) > 0) {
			while($productypes = $db->fetch_assoc($query)) {
				$entiy_productypes[$productypes['eptid']] = new Endproductypes($productypes['eptid']);
			}
			return $entiy_productypes;
		}
		else {
			return false;
		}
	}

	public function get() {
		return $this->endproduct;
	}

	public function get_errorcode() {
		return $this->errorcode;
	}

}
?>
