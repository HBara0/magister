<?php
/*
 * Copyright © 2013 Orkila International Offshore, All Rights Reserved
 * 
 * [Provide Short Descption Here]
 * $id: Chemicalfunctionproduct.php
 * Created:        @tony.assaad    Dec 3, 2013 | 4:34:44 PM
 * Last Update:    @tony.assaad    Dec 3, 2013 | 4:34:44 PM
 */

/**
 * Description of Chemicalfunctionproduct
 *
 * @author tony.assaad
 */
class Chemicalfunctions {
	private $chemfunction = array();

	public function __construct($id = '', $simple = true) {
		if(isset($id)) {
			$this->read($id, $simple);
		}
	}

	private function read($id, $simple) {
		global $db;
		$query_select = '*';
		if($simple == true) {
			$query_select = 'cfid, name,title';
		}
		$this->chemfunction = $db->fetch_assoc($db->query('SELECT '.$query_select.' FROM '.Tprefix.'chemicalfunctions WHERE cfid='.intval($id)));
	}

	public static function create($data = array()) {
		global $db, $core, $log;
		if(empty($data['title'])) {
			$errorcode = 1;
			return false;
		}

		if(is_array($data)) {
			print_r($data);
			if(value_exists('chemicalfunctions', 'title', $data['title'])) {
				$errorcode = 2;
				return false;
			}
			$data['title'] = $core->sanitize_inputs($data['title'], array('removetags' => true));
			if(empty($data['name'])) {
				$data['name'] = $data['title'];
			}
			$chemicalfunctions_data = array('name' => $data['name'],
					'title' => $data['title'],
					'createdBy' => $core->user['uid']
			);
			$query = $db->insert_query('chemicalfunctions', $chemicalfunctions_data);
			if($query) {
				$data['cfid'] = $db->last_id();
				if(!empty($data['segapplications']) && isset($data['segapplications'])) {
					foreach($data['segapplications'] as $psaid) {
						$segappfuncquery = $db->insert_query('segapplicationfunctions', array('cfid' => $data['cfid'], 'psaid' => $psaid, 'createdBy' => $core->user['uid']));
						if($segappfuncquery) {
							$data['safid'] = $db->last_id();
						}
					}
				}
				$log->record('createchemicalfunctions', $data['cfid']);
				$errorcode = 0;
				return true;
			}
		}
	}

	public static function get_chemfunction_byname($name) {
		global $db;
		if(!empty($name)) {
			return $db->fetch_assoc($db->query('SELECT * FROM '.Tprefix.'chemicalfunctions WHERE name="'.$name.'"'));
		}
	}

	public static function get_functions() {
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
		$query = $db->query('SELECT cfid  FROM '.Tprefix.'chemicalfunctions');
		if($db->num_rows($query) > 0) {
			while($rowchecmical = $db->fetch_assoc($query)) {
				$checmical_functions[$rowchecmical['cfid']] = new Chemicalfunctions($rowchecmical['cfid']);
			}
			return $checmical_functions;
		}
		else {
			return false;
		}
	}

	/* return multilples Segmentapplications object for the current chemicalfunction */
	public function get_applications() {
		global $db;
		$query = $db->query('SELECT safid ,psaid FROM '.Tprefix.'segapplicationfunctions WHERE cfid='.$this->chemfunction['cfid'].'');
		if($db->num_rows($query) > 0) {
			while($rowchecmapplications = $db->fetch_assoc($query)) {
				$checm_applicationsfunctions[$rowchecmapplications['safid']] = new Segmentapplications($rowchecmapplications['psaid']);
			}
			return $checm_applicationsfunctions;
		}
		else {
			return false;
		}
	}

	public function get_createdby() {
		return new Users($this->chemfunction['createdBy']);
	}

	public function get_modifiedby() {
		return new Users($this->chemfunction['modifiedBy']);
	}

	public function get() {
		return $this->chemfunction;
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
