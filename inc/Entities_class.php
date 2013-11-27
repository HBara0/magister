<?php

class Entities {
	protected $eid = 0;
	protected $status = false;
	protected $data = array();

	public function __construct($data, $action = '', $simple = true) {
		if(is_array($data)) {
			$this->data = $data;
			switch($action) {
				case 'edit':
					$this->modify();
					break;
				case 'add_representative':
					$this->create_representative();
					break;
				case 'set_entitylogo':
					$this->upload_logo();
					break;
				default:
					$this->create();
					break;
			}
		}
		else {
			$this->data = $this->read($data, $simple);
		}
	}

	protected function create() {
		global $db, $core, $lang;

		if(empty($this->data['companyName'])) {
			output_xml("<status>false</status><message>{$lang->specifycompanyname}</message>");
			$this->status = false;
			exit;
		}

		if(!$this->entity_exists($this->data['companyName'])) {
			if(empty($this->data['affid'])) {
				output_xml("<status>false</status><message>{$lang->specifyanaffiliate}</message>");
				$this->status = false;
				exit;
			}

			if(array_key_exists('repName', $this->data) || array_key_exists('repEmail', $this->data)) {
				if($this->data['repName'] == 'na' || $this->data['repName'] == 'n/a') {
					$representatives[$i]['rpid'] = $db->fetch_field($db->query("SELECT rpid FROM ".Tprefix."representatives WHERE name='n/a'"), 'rpid');
				}
				else {
					$this->create_representative();
					$representatives[$i]['rpid'] = $db->last_id();
				}
				unset($this->data['repName'], $this->data['repEmail']);
			}
			else {
				$representatives = $this->data['representative']; //;$this->workout_representatives();
				unset($this->data['representative'], $this->data['rep_numrows']);
			}

			$affiliates = $this->data['affid'];
			unset($this->data['affid']);

			if(isset($this->data['users']) && !empty($this->data['users'])) {
				$employees = $this->data['users'];
				unset($this->data['users'], $this->data['users_numrows']);
			}
			/* else
			  {
			  output_xml("<status>false</status><message>{$lang->specifyauser} Select a user</message>");
			  $this->status = false;
			  exit;
			  } */

			if(isset($this->data['psid']) && !empty($this->data['psid'])) {
				$segments = $this->data['psid'];
				unset($this->data['psid']);
			}
			else {
				output_xml("<status>false</status><message>{$lang->specifyasegment}</message>");
				$this->status = false;
				exit;
			}

			if(!is_empty($this->data['telephone_intcode'], $this->data['telephone_areacode'], $this->data['telephone_number'])) {
				$this->data['phone1'] = $this->data['telephone_intcode'].'-'.$this->data['telephone_areacode'].'-'.$this->data['telephone_number'];
			}

			if(!is_empty($this->data['telephone2_intcode'], $this->data['telephone2_areacode'], $this->data['telephone2_number'])) {
				$this->data['phone2'] = $this->data['telephone2_intcode'].'-'.$this->data['telephone2_areacode'].'-'.$this->data['telephone2_number'];
			}

			if(!is_empty($this->data['fax_intcode'], $this->data['fax_areacode'], $this->data['fax_number'])) {
				$this->data['fax1'] = $this->data['fax_intcode'].'-'.$this->data['fax_areacode'].'-'.$this->data['fax_number'];
			}

			if(!is_empty($this->data['fax2_intcode'], $this->data['fax2_areacode'], $this->data['fax2_number'])) {
				$this->data['fax2'] = $this->data['fax2_intcode'].'-'.$this->data['fax2_areacode'].'-'.$this->data['fax2_number'];
			}

			$geolocation = $this->data['geoLocation']; //Temp solution

			unset($this->data['telephone_intcode'], $this->data['telephone_areacode'], $this->data['telephone_number']);
			unset($this->data['telephone2_intcode'], $this->data['telephone2_areacode'], $this->data['telephone2_number']);
			unset($this->data['fax_intcode'], $this->data['fax_areacode'], $this->data['fax_number']);
			unset($this->data['fax2_intcode'], $this->data['fax2_areacode'], $this->data['fax2_number'], $this->data['geoLocation']);

			if(isset($this->data['mainEmail']) && !empty($this->data['mainEmail'])) {
				if($core->validate_email($this->data['mainEmail'])) {
					$this->data['mainEmail'] = $core->sanitize_email($this->data['mainEmail']);
				}
				else {
					output_xml("<status>false</status><message>{$lang->invalidentityemail}</message>");
					exit;
				}
			}

			$this->data['website'] = $core->sanitize_URL($this->data['website']);

			$this->data['contractFirstSigDate'] = $this->checkgenerate_date($this->data['contractFirstSigDate']);
			$this->data['contractExpiryDate'] = $this->checkgenerate_date($this->data['contractExpiryDate']);

			$this->data['dateAdded'] = TIME_NOW;
			if(!isset($this->data['noQReportReq'])) {
				$this->data['noQReportReq'] = 1; //By default no QR is required
			}

			$query = $db->insert_query('entities', $this->data);
			if($query) {
				$this->eid = $db->last_id();
				/* Temp Solution */
				if(!empty($geolocation)) {
					if(strstr($geolocation, ',')) {
						$geolocation = str_replace(', ', ' ', $geolocation);
					}
					$db->query('UPDATE entities SET geoLocation=geomFromText("POINT('.$db->escape_string($geolocation).')") WHERE eid='.$this->eid);
				}
				$this->insert_affiliatedentities($affiliates);
				$this->insert_representatives($representatives);
				if(is_array($segments)) {
					$this->insert_entitysegments($segments);
				}
				//if($this->data['type'] == 'c') {
				if(IN_AREA == 'user') {
					$this->insert_assignedemployee();
				}
				else {
					$this->insert_assignedemployee($employees);
				}
				//}

				$lang->entitycreated = $lang->sprint($lang->entitycreated, htmlspecialchars($this->data['companyName']));
				output_xml("<status>true</status><message>{$lang->entitycreated}</message>");
				$this->status = true;
			}
			else {
				output_xml("<status>false</status><message>{$lang->errorcreatingentity}</message>");
				$this->status = false;
			}
		}
		else {
			if($this->entity_type($this->data['companyName']) == 'c') {
				$existing_eid = $this->existing_eid($this->data['companyName']);
				$exists = $db->fetch_field($db->query("SELECT COUNT(*) AS counter FROM ".Tprefix."assignedemployees WHERE uid='".$db->escape_string($core->user['uid'])."' AND eid='{$existing_eid}'"), 'counter');
				if($exists == 0) {
					$query = $db->insert_query('assignedemployees', array('eid' => $existing_eid, 'uid' => $core->user['uid']));
					if($query) {
						output_xml("<status>true</status><message>{$lang->joinedsuccessfully}</message>");
						$this->status = true;
					}
					else {
						output_xml("<status>false</status><message>{$lang->errorcreatingentity}</message>");
						$this->status = false;
					}
				}
				else {
					output_xml("<status>false</status><message>{$lang->entityalreadyexists}</message>");
					$this->status = false;
				}
			}
			else {
				output_xml("<status>false</status><message>{$lang->entityalreadyexists}</message>");
				$this->status = false;
			}
		}
	}

	protected function modify() {
		global $core, $db, $lang;

		if(array_key_exists('eid', $this->data)) {
			$this->eid = $this->data['eid'];

			if(!empty($this->data['companyName'])) {
				$check_name = $db->query("SELECT eid FROM ".Tprefix."entities WHERE companyName='".$db->escape_string($this->data['companyName'])."'");
				if($db->num_rows($check_name) > 0) {
					$existing = $db->fetch_array($check_name);
					if($existing['eid'] != $this->data['eid']) {
						output_xml("<status>false</status><message>{$lang->entityalreadyexists}</message>");
						$this->status = false;
						exit;
					}
				}

				if(empty($this->data['affid'])) {
					output_xml("<status>false</status><message>{$lang->specifyanaffiliate}</message>");
					$this->status = false;
					exit;
				}

				if(!is_empty($this->data['telephone_intcode'], $this->data['telephone_areacode'], $this->data['telephone_number'])) {
					$this->data['phone1'] = $this->data['telephone_intcode'].'-'.$this->data['telephone_areacode'].'-'.$this->data['telephone_number'];
				}

				if(!is_empty($this->data['telephone2_intcode'], $this->data['telephone2_areacode'], $this->data['telephone2_number'])) {
					$this->data['phone2'] = $this->data['telephone2_intcode'].'-'.$this->data['telephone2_areacode'].'-'.$this->data['telephone2_number'];
				}

				if(!is_empty($this->data['fax_intcode'], $this->data['fax_areacode'], $this->data['fax_number'])) {
					$this->data['fax1'] = $this->data['fax_intcode'].'-'.$this->data['fax_areacode'].'-'.$this->data['fax_number'];
				}

				if(!is_empty($this->data['fax2_intcode'], $this->data['fax2_areacode'], $this->data['fax2_number'])) {
					$this->data['fax2'] = $this->data['fax2_intcode'].'-'.$this->data['fax2_areacode'].'-'.$this->data['fax2_number'];
				}

				unset($this->data['telephone_intcode'], $this->data['telephone_areacode'], $this->data['telephone_number']);
				unset($this->data['telephone2_intcode'], $this->data['telephone2_areacode'], $this->data['telephone2_number']);
				unset($this->data['fax_intcode'], $this->data['fax_areacode'], $this->data['fax_number']);
				unset($this->data['fax2_intcode'], $this->data['fax2_areacode'], $this->data['fax2_number']);

				if(isset($this->data['mainEmail']) && !empty($this->data['mainEmail'])) {
					if($core->validate_email($this->data['mainEmail'])) {
						$this->data['mainEmail'] = $core->sanitize_email($this->data['mainEmail']);
					}
					else {
						output_xml("<status>false</status><message>{$lang->invalidentityemail}</message>");
						exit;
					}
				}

				$representatives = $this->data['representative']; //;$this->workout_representatives();
				unset($this->data['representative'], $this->data['rep_numrows']);

				$affiliates = $this->data['affid'];
				unset($this->data['affid']);

				if(isset($this->data['users']) && !empty($this->data['users'])) {
					$employees = $this->data['users'];
					unset($this->data['users'], $this->data['users_numrows']);
				}
				else {
					output_xml("<status>false</status><message>{$lang->specifyauser} Select a user</message>");
					$this->status = false;
					exit;
				}

				if(isset($this->data['psid']) && !empty($this->data['psid'])) {
					$segments = $this->data['psid'];
					unset($this->data['psid']);
				}
				else {
					output_xml("<status>false</status><message>{$lang->specifyasegment} Select a segment</message>");
					$this->status = false;
					exit;
				}

				if(isset($this->data['logo']) && !empty($this->data['logo'])) {
					$old_logo = $db->fetch_field($db->query("SELECT logo FROM ".Tprefix."entities WHERE eid=".$this->eid), 'logo');
					if(!empty($old_logo)) {
						if($old_logo != $this->data['logo']) {
							unlink(ROOT.'/uploads/entitieslogos/'.$old_logo);
						}
					}
				}

				if(isset($this->data['contractFirstSigDate'])) {
					$this->data['contractFirstSigDate'] = $this->checkgenerate_date($this->data['contractFirstSigDate']);
				}
				if(isset($this->data['contractExpiryDate'])) {
					$this->data['contractExpiryDate'] = $this->checkgenerate_date($this->data['contractExpiryDate']);
				}

				/* Set value for unchecked checkboxes - START  */
				$checkboxes_tocheck = array('noQReportSend', 'noQReportReq');
				foreach($checkboxes_tocheck as $checkid) {
					if(!isset($this->data[$checkid])) {
						$this->data[$checkid] = 0;
					}
				}
				/* Set value for unchecked checkboxes - END */

				$query = $db->update_query('entities', $this->data, "eid='".$this->eid."'");
				if($query) {
					$db->delete_query('affiliatedentities', "eid='".$this->eid."'");
					$this->insert_affiliatedentities($affiliates);
					$db->delete_query('entitiesrepresentatives', "eid='".$this->eid."'");
					$this->insert_representatives($representatives);
					if(is_array($segments)) {
						$db->delete_query('entitiessegments', "eid='".$this->eid."'");
						$this->insert_entitysegments($segments);
					}

					if(IN_AREA == 'admin') {
						/* $query = $db->query("SELECT uid FROM ".Tprefix."assignedemployees WHERE isValidator='1' AND eid='".$this->eid."'");
						  $validators = array();
						  while($validator = $db->fetch_assoc($query)) {
						  $validators[] = $validator['uid'];
						  }
						 */
						$db->delete_query('assignedemployees', "eid='".$this->eid."'");
						$db->delete_query('suppliersaudits', "eid='".$this->eid."'");
						$this->insert_assignedemployee($employees);
					}

					$lang->entitymodified = $lang->sprint($lang->entitymodified, htmlspecialchars($this->data['companyName']));
					output_xml("<status>true</status><message>{$lang->entitymodified}</message>");
					$this->status = true;
				}
			}
			else {
				output_xml("<status>false</status><message>{$lang->specifyanaffiliate}</message>");
				$this->status = false;
				exit;
			}
		}
	}

	private function create_representative() {
		global $core, $db, $lang;

		if(!isset($this->data['repName'], $this->data['repEmail']) || (empty($this->data['repName']) || empty($this->data['repEmail']))) {
			output_xml("<status>false</status><message>{$lang->fillrequiredfields}</message>");
			exit;
		}

		$count = $db->fetch_field($db->query("SELECT COUNT(*) AS existing FROM ".Tprefix."representatives WHERE name='".$db->escape_string($this->data['repName'])."' AND email ='".$db->escape_string($this->data['repEmail'])."' "), "existing");
		if($count > 0) {
			output_xml("<status>false</status><message>{$lang->representativeexists}</message>");
			exit;
		}

		if($core->validate_email($this->data['repEmail'])) {
			$core->input['repEmail'] = $core->sanitize_email($this->data['repEmail']);
		}
		else {
			output_xml("<status>false</status><message>{$lang->invalidentityemail}</message>");
			exit;
		}

		if(isset($this->data['repTelephone']) && !empty($this->data['repTelephone'])) {
			if(!is_empty($this->data['repTelephone']['intcode'], $this->data['repTelephone']['areacode'], $this->data['repTelephone']['number'])) {
				$this->data['repTelephone'] = implode('-', $this->data['repTelephone']);
			}
			else {
				unset($this->data['repTelephone']);
			}
		}

		$query = $db->insert_query('representatives', array('name' => ucwords(strtolower($this->data['repName'])), 'email' => $this->data['repEmail'], 'phone' => $this->data['repTelephone']));
		if($query) {
			$rpid = $db->last_id();
			if(isset($this->data['repcid'])) {
				$db->insert_query('entitiesrepresentatives', array('eid' => $this->data['repcid'], 'rpid' => $rpid));
			}

			if(isset($this->data['repspid'])) {
				$db->insert_query('entitiesrepresentatives', array('eid' => $this->data['repspid'], 'rpid' => $rpid));
			}

			$this->status = true;
		}
		else {
			$this->status = false;
		}
	}

	private function workout_representatives() {
		global $core, $lang;

		for($i = 1; $i <= $this->data['rep_numrows']; $i++) {
			if(empty($this->data['representative_'.$i])) {
				unset($this->data['representative_'.$i]);
				continue;
			}
			else {
				$found_once = true;
				$representatives[$i]['rpid'] = $this->data['representative_'.$i];
			}
			unset($this->data['representative_'.$i]);
		}

		unset($this->data['rep_numrows']);

		if($found_once !== true) {
			output_xml("<status>false</status><message>{$lang->specifyrepresentative}</message>");
			$this->status = false;
			exit;
		}
		return $representatives;
	}

	private function insert_representatives(array $representatives) {
		global $db;

		foreach($representatives as $key => $val) {
			if(empty($val['rpid'])) {
				continue;
			}
			$representative = array(
					'rpid' => $val['rpid'],
					'eid' => $this->eid
			);
			$db->insert_query('entitiesrepresentatives', $representative);
		}
	}

	private function insert_affiliatedentities(array $affiliates) {
		global $db;

		foreach($affiliates as $key => $val) {
			$affentity = array(
					'affid' => $val,
					'eid' => $this->eid,
			);
			$db->insert_query('affiliatedentities', $affentity);
		}
	}

	private function insert_assignedemployee($employees = '', array $validators = array()) {
		global $db, $core;

		if(isset($employees) && !empty($employees)) {
			foreach($employees as $key => $val) {
				if(empty($val['uid']) || !isset($val['uid'])) {
					continue;
				}
				if(isset($val['isValidator']) && $val['isValidator'] == 'on') {
					$db->insert_query('suppliersaudits', array('eid' => $this->eid, 'uid' => $val['uid']));
				}
				foreach($val['affiliates'] as $value) {
					$db->insert_query('assignedemployees', array('eid' => $this->eid, 'uid' => $val['uid'], 'affid' => $value));
				}
			}
		}
		else {
			$main_affiliate = $db->fetch_field($db->query("SELECT affid FROM ".Tprefix."affiliatedemployees WHERE isMain='1' AND uid='".$core->user['uid']."'"), 'affid');
			$db->insert_query('assignedemployees', array('eid' => $this->eid, 'uid' => $core->user['uid'], 'affid' => $main_affiliate));
		}
		/* if(empty($employees)) {
		  $db->insert_query('assignedemployees', array('eid'=> $this->eid, 'uid'=> $core->user['uid']));
		  }
		  else
		  {
		  if(is_array($employees)) {
		  foreach($employees as $key => $val) {
		  $assignemployee = array(
		  'uid'	  => $val,
		  'eid'	  => $this->eid,
		  );
		  if(in_array($val, $validators)) {
		  $assignemployee['isValidator'] = 1;
		  }
		  $db->insert_query('assignedemployees', $assignemployee);
		  }
		  }
		  } */
	}

	private function insert_entitysegments(array $segments) {
		global $db;
		if(is_array($segments)) {
			foreach($segments as $key => $val) {
				$db->insert_query('entitiessegments', array('psid' => $val, 'eid' => $this->eid));
			}
		}
	}

	private function checkgenerate_date($date) {
		if(!empty($date)) {
			$date_details = explode('-', $date);
			if(checkdate($date_details[1], $date_details[0], $date_details[2])) {
				return mktime(0, 0, 0, $date_details[1], $date_details[0], $date_details[2]);
			}
			else {
				output_xml("<status>false</status><message>{$lang->invalidfromdate}</message>");
				exit;
			}
		}
	}

	private function upload_logo() {
		global $core;
		$core->settings['logosdir'] = ROOT.'/uploads/entitieslogos';
		$upload = new Uploader($this->data['fieldname'], $this->data['file'], array('image/jpeg', 'image/gif'), 'putfile', 300000, 0, 1);
		$upload->set_upload_path($core->settings['logosdir']);
		$upload->process_file();
		$upload->resize();

		$this->logofilename = $upload->get_filename();
	}

	public function get_uploaded_logo() {
		return $this->logofilename;
	}

	public function get_eid() {
		return $this->data['eid'];
	}

	public function get_status() {
		return $this->status;
	}

	public function entity_exists($name) {
		global $db;

		if(function_exists('value_exists')) {
			return value_exists('entities', 'companyName', $name);
		}
		else {
			$query = $db->query("SELECT companyName FROM ".Tprefix."entities WHERE companyName='".$db->escape_string($name)."'");
			if($db->num_rows($query) > 0) {
				return true;
			}
			else {
				return false;
			}
		}
	}

	public function get() {
		return $this->data;
	}

	private function read($id, $simple) {
		global $db;
		if(!empty($id)) {
			$query_select = '*';
			if($simple == true) {
				$query_select = 'eid, companyName, companyNameAbbr, companyNameShort, logo';
			}
			return $db->fetch_assoc($db->query("SELECT ".$query_select." FROM ".Tprefix."entities WHERE eid='".$db->escape_string($id)."'"));
		}
		return false;
	}

	protected function existing_eid($name) {
		global $db;
		return $db->fetch_field($db->query("SELECT eid FROM ".Tprefix."entities WHERE companyName='".$db->escape_string($name)."'"), 'eid');
	}

	public function entity_type($name) {
		global $db;
		return $db->fetch_field($db->query("SELECT type FROM ".Tprefix."entities WHERE companyName='".$db->escape_string($name)."'"), 'type');
	}

	public function auto_assignsegment($gpid) {
		global $db;
		/* Get segment of generic product */
		$psid = $db->fetch_field($db->query("SELECT psid FROM ".Tprefix."genericproducts WHERE gpid='".$db->escape_string($gpid)."'"), 'psid');

		if(!value_exists('entitiessegments', 'psid', $psid, 'eid='.$this->data['eid'].'')) {
			$db->insert_query('entitiessegments', array('psid' => $psid, 'eid' => $this->data['eid']));
		}
	}

	public static function get_entity_byname($name) {
		global $db;

		if(!empty($name)) {
			$id = $db->fetch_field($db->query('SELECT eid FROM '.Tprefix.'entities WHERE companyName="'.$db->escape_string($name).'"'), 'eid');
			if(!empty($id)) {
				return new Entities($id);
			}
		}
		return false;
	}

	public function get_assignedusers(array $affiliates = array()) {
		global $db;

		if(!empty($affiliates)) {
			$query_extrawhere .= ' AND affid IN ('.implode(', ', $affiliates).')';
		}

		$query = $db->query('SELECT * 
						FROM '.Tprefix.'assignedemployees 
						WHERE eid='.$this->data['eid'].' AND uid NOT IN (SELECT uid FROM '.Tprefix.'users WHERE gid=7)'.$query_extrawhere);
		if($db->num_rows($query) > 0) {
			while($assigned = $db->fetch_assoc($query)) {
				$assigns[] = new Users($assigned['uid']);
			}
			return $assigns;
		}
		return false;
	}

	public function has_assignedusers(array $affiliates = array()) {
		global $db;

		if(!empty($affiliates)) {
			$query_extrawhere .= ' AND affid IN ('.implode(', ', $affiliates).')';
		}

		$query = $db->query('SELECT * 
					FROM '.Tprefix.'assignedemployees 
					WHERE eid='.$this->data['eid'].' AND uid NOT IN (SELECT uid FROM '.Tprefix.'users WHERE gid=7)'.$query_extrawhere);
		if($db->num_rows($query) > 0) {
			return true;
		}
		return false;
	}

}
?>