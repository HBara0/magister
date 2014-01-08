<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: Entitiesbrands_class.php
 * Created:        @tony.assaad    Dec 4, 2013 | 1:09:49 PM
 * Last Update:    @tony.assaad    Dec 4, 2013 | 1:09:49 PM
 */

/**
 * Description of Entitiesbrands_class
 *
 * @author tony.assaad
 */
class Entbrands {
	private $entitiesbrands = array();

	public function __construct($id = '', $simple = false) {
		if(isset($id)) {
			$this->read($id, $simple);
		}
	}

	private function read($id, $simple) {
		global $db;
		$query_select = '*';
		if($simple == true) {
			$query_select = 'ebid,name';
		}
		$this->entitiesbrands = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'entitiesbrands WHERE ebid='.intval($id)));
	}

	public function create($data = array()) {
		global $db, $core;
		if(is_array($data)) {
			$this->data = $data;
			if(empty($this->data['title'])) {
				$this->errorcode = 1;
				return false;
			}
			if(value_exists('entitiesbrands', 'name', $this->data['title'])) {
				$this->errorcode = 2;
				return false;
			}

			/* insert entity Brands */

			$enttitbrand_data = array('name' => $this->data['title'],
					'eid' => $this->data['eid'],
					'createdBy' => $core->user['uid'],
					'createdOn' => TIME_NOW
			);
			print_r($enttitbrand_data);
			$query = $db->insert_query('entitiesbrands', $enttitbrand_data);
			if($query) {
				$this->ebid = $db->last_id();
				if(is_array($this->data['endproducttypes'])) {
					foreach($this->data['endproducttypes'] as $eptid) {
						$entitiesbrandsproducts_data = array('ebid' => $this->ebid,
								'eptid' => $eptid,
								'createdBy' => $core->user['uid'],
								'createdOn' => TIME_NOW
						);
						$query = $db->insert_query('entitiesbrandsproducts', $entitiesbrandsproducts_data);
					}
				}
				$this->errorcode = 0;
				return true;
			}
		}
	}

	public function get_entity() {
		return new Entities($this->entitiesbrands['eid']);
	}

	public function get_createdby() {
		return new Users($this->entitiesbrands['createdBy']);
	}

	public function get_modifiedby() {
		return new Users($this->entitiesbrands['modifiedBy']);
	}

	public static function get_entitybrand_byid($id) {
		global $db;

		if(!empty($id)) {
			$query = $db->query('SELECT ebid  FROM '.Tprefix.'entitiesbrands  WHERE eid="'.$db->escape_string($id).'"');
			while($ebidrow = $db->fetch_assoc($query)) {
				$entiy_brands[$ebidrow['ebid']] = new Entbrands($ebidrow['ebid']);
			}
			return $entiy_brands;
		}
		return false;
	}

	public static function get_entitybrands() {
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
		$query = $db->query('SELECT ebid  FROM '.Tprefix.'entitiesbrands');
		if($db->num_rows($query) > 0) {
			while($entitybrow = $db->fetch_assoc($query)) {
				$entiy_brands[$entitybrow['ebid']] = new Entbrands($entitybrow['ebid']);
			}
			return $entiy_brands;
		}
		else {
			return false;
		}
	}

	public function get_errorcode() {
		return $this->errorcode;
	}

	public function get() {
		return $this->entitiesbrands;
	}

}
?>
