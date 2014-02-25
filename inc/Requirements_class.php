<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 * 
 * Requirements Class
 * $id: Requirements_class.php
 * Created By: 		@zaher.reda			May 21, 2012 | 09:38 PM
 * Last Update: 	@zaher.reda			May 21, 2012 | 09:38 PM
 */

class Requirements {
	private $requirement = array();

	public function __construct($id = '', $simple = false) {
		if(isset($id) && !empty($id)) {
			$this->requirement = $this->read_requirement($id, $simple);
		}
	}

	public function read_requirement($id, $simple = false) {
		global $db, $core;

		$text_fields = array('performance', 'userInterface', 'security', 'description');

		$query_select = 'dr1.*, dr2.title AS parentTitle';
		if($simple == true) {
			$query_select = 'dr1.drid, dr1.title';
		}

		$query = $db->query("SELECT {$query_select} 
		FROM ".Tprefix."development_requirements  dr1
		LEFT JOIN ".Tprefix."development_requirements  dr2 ON (dr1.parent=dr2.drid)
		WHERE dr1.drid=".$db->escape_string($id));

		if($db->num_rows($query) > 0) {
			while($requirement = $db->fetch_assoc($query)) {
				$level = 'parent';
				if($requirement['parent'] == $id) {
					$level = 'children';
				}
				if($simple == false) {
					$requirement['dateCreated_output'] = date($core->settings['dateformat'], $requirement['dateCreated']);

					foreach($text_fields as $field) {
						$requirement[$field] = htmlentities($requirement[$field]);
						fix_newline($requirement[$field]);
						parse_ocode($requirement[$field]);
					}
				}
				$requirements = $requirement;
				$requirements['children'] = $this->read_requirement_children($requirement['drid'], $simple);
			}
		}

		return $requirements;
	}

	private function read_requirement_children($id, $simple = false) {
		global $db;

		$query_select = '*';
		if($simple == true) {
			$query_select = 'drid, title';
		}

		$query = $db->query("SELECT {$query_select} FROM ".Tprefix."development_requirements WHERE parent=".$db->escape_string($id).' ORDER BY refWord ASC, refKey ASC');
		if($db->num_rows($query) > 0) {
			while($requirement = $db->fetch_assoc($query)) {
				$requirements[$requirement['drid']] = $requirement;
				$requirements[$requirement['drid']]['children'] = $this->read_requirement_children($requirement['drid']);
			}
			return $requirements;
		}

		return false;
	}

	public function get_changes($id = '') {
		global $db, $core;

		if(empty($id)) {
			$id = $this->requirement['drid'];
		}

		$query = $db->query("SELECT drc.*, u.displayName AS createdByName, dr.title AS outcomeReqTitle, dr.refKey AS drRefKey, dr.refWord AS drRefWord
							FROM ".Tprefix."development_requirements_changes  drc
							LEFT JOIN ".Tprefix."development_requirements dr ON (drc.outcomeReq=dr.drid)
							JOIN ".Tprefix."users u ON (drc.createdBy=u.uid)
							WHERE drc.drid=".$db->escape_string($id)."
							ORDER BY dateCreated ASC");
		if($db->num_rows($query) > 0) {
			while($change = $db->fetch_assoc($query)) {
				$change['dateCreated_output'] = date($core->settings['dateformat'], $change['dateCreated']);
				$changes[$change['drcid']] = $change;
			}
		}

		return $changes;
	}

	public function read_user_requirements($simple = false) {
		global $db, $core;

		$query_select = '*';
		if($simple == true) {
			$query_select = 'drid, title';
		}

		$query = $db->query("SELECT {$query_select} FROM ".Tprefix."development_requirements WHERE (assignedTo=0 OR assignedTo=".$core->user['uid'].' OR createdBy='.$core->user['uid'].') AND parent=0 ORDER BY refWord ASC, refKey ASC');
		if($db->num_rows($query)) {
			while($requirement = $db->fetch_assoc($query)) {
				$level = 'parent';
				if($requirement['parent'] != 0) {
					$level = 'children';
				}

				$requirements[$requirement['drid']] = $requirement;
				$requirements[$requirement['drid']]['children'] = $this->read_requirement_children($requirement['drid'], $simple);
			}

			return $requirements;
		}
		return false;
	}

	public function get() {
		return $this->requirement;
	}

	public function get_parent() {
		if(!isset($this->requirement['parent'])) {
			return false;
		}
		return new Requirements($this->requirement['parent']);
	}

	public function parse_requirements_list(array $requirements = array(), $highlevel = true, $ref = '', $parsetype = 'list') {
		if(empty($requirements)) {
			if(!isset($this->requirement)) {
				return false;
			}

			if($highlevel == true) {
				$requirements = $this->requirement;
			}
			else {
				return false;
			}
		}

		if($highlevel == true) {
			if($parsetype == 'list') {
				$requirements_list = '<ul>';
			}
			else {
				//$requirements_list .= '<select  name="development[parent] >';
			}
		}

		$ref_param = $ref;

		foreach($requirements as $id => $values) {
			if(empty($ref)) {
				$ref = $values['refWord'].' '.$values['refKey'];
			}
			else {
				$ref = $ref_param.'.'.$values['refKey'];
			}
			if($parsetype == 'list') {
				$requirements_list .= '<li><a href="index.php?module=development/viewrequirement&id='.$values['drid'].'" target="_blank">'.$ref.' '.$values['title'].'</a>';

				if(!empty($values['isCompleted']) && !is_array($values['children'])) {
					$requirements_list .= ' &#10004;';
				}
				elseif(!empty($values['isCompleted']) && is_array($values['children'])) {
					$requirements_list .= ' &#10003;';
				}
				if(is_array($values['children']) && !empty($values['children'])) {
					$requirements_list .= ' <a href="#requirement_'.$values['drid'].'" id="showmore_requirementchildren_'.$values['drid'].'">&raquo;</a>';
				}
				$requirements_list .= '</li>';
			}
			else {
				$requirements_list .= '<option value="'.$values['drid'].'">'.$ref.' '.$values['title'].'</option>';
			}


			if(is_array($values['children']) && !empty($values['children'])) {
				if($parsetype == 'list') {
					$requirements_list .= '<ul id="requirementchildren_'.$values['drid'].'" style="display:none;">';
					$requirements_list .= $this->parse_requirements_list($values['children'], false, $ref);
					$requirements_list .= '</ul>';
				}
				else {
					$requirements_list .= '.';
					$requirements_list .= $this->parse_requirements_list($values['children'], false, $ref, 'select');
				}
			}


			if($highlevel == true) {
				$ref = '';
			}
		}
		if($parsetype == 'list') {
			if($highlevel == true) {
				$requirements_list .= '</ul>';
			}
		}

		return $requirements_list;
	}

}
?>