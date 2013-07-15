<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Maps Class
 * $id: Maps_class.php
 * Created:		@Alain.Paulikevitch		May 10, 2012 | 06:03 PM
 * Last Update: @tony.assaad	        june 25, 2013 | 04:32 PM
 */

class Asset {
	private $cache = array();
	private $my_asid;

	public function __construct($id = '', $simple = false) {
		if(!empty($id)) {
			$this->assets = $this->read($id, $simple);
		}
	}

	/* Setter Functions ----START */
	public function add($data, array $options = array()) {
		global $db, $log, $core, $errorhandler, $lang;

		if(is_empty($data['title'], $data['type'], $data['status'])) {
			$this->errorcode = 1;
			return false;
		}

		if(is_array($data)) {
			$this->assets = $data;
		}

		/* Santize inputs - START */
		$sanitize_fields = array('title', 'affid', 'type', 'description');
		foreach($sanitize_fields as $val) {
			$this->assets[$val] = $core->sanitize_inputs($this->assets[$val], array('removetags' => true));
		}

		/* If action is edit, don't check if supplier already exists */
		if($options['operationtype'] != 'update') {
			if(value_exists('assets', 'title', $this->assets['title'])) {
				$this->errorcode = 2;
				return false;
			}
		}

		if($options['operationtype'] == 'update') {
			if(is_empty($data['title'], $data['tag'], $data['status'])) {
				$this->errorcode = 1;
				return false;
			}
			$this->assets = $data;
			$this->assets['editedon'] = TIME_NOW;
			$this->assets['editedby'] = $core->user['uid'];
			$assetid = intval($this->assets['asid']);
			unset($this->assets['asid']);
			$query = $db->update_query('assets', $this->assets, 'asid='.$assetid.'');
		}
		else {
			$this->assets['isActive'] = 1;
			$this->assets['createdon'] = TIME_NOW;
			$this->assets['createdby'] = $core->user['uid'];
			$query = $db->insert_query('assets', $this->assets);
			$this->assets['asid'] = $db->last_id();
		}

		if($query) {
			$this->errorcode = 0;
		}
	}

	public function manage_tracker($trackerdata, array $options = array()) {
		global $db, $log, $core, $errorhandler, $lang;
		if(is_empty($trackerdata['deviceId'], $trackerdata['fromDate'], $trackerdata['toDate'])) {
			$this->errorcode = 1;
			return false;
		}

		$trackerdata['fromDate'] = strtotime($trackerdata['fromDate']);
		$trackerdata['toDate'] = strtotime($trackerdata['toDate']);

		if(value_exists('assets_trackingdevices', 'deviceId', $trackerdata['deviceId'], 'asid= '.$trackerdata['asid'].' AND (('.$trackerdata['fromDate'].' BETWEEN fromDate AND toDate) OR ('.$trackerdata['toDate'].' BETWEEN fromDate AND toDate))')) {
			$this->errorcode = 2;
			return false;
		}
		if(is_array($trackerdata)) {
			$this->tracker = $trackerdata;
		}
		/* Santize inputs - START */
		$sanitize_fields = array('deviceId', 'fromDate', 'toDate', 'asid');
		foreach($sanitize_fields as $val) {
			$this->tracker[$val] = $core->sanitize_inputs($this->tracker[$val], array('removetags' => true));
		}

		if($options['operationtype'] == 'update') {
			$this->tracker = $trackerdata;
			$trackerid = intval($this->tracker['atdid']);
			unset($this->tracker['atdid']);
			$query = $db->update_query('assets_trackingdevices', $this->tracker, 'atdid='.$trackerid.'');
		}
		else {
			$query = $db->insert_query('assets_trackingdevices', $this->tracker);
		}

		if($query) {
			$this->errorcode = 0;
		}
	}

	public function set_asid($asid) {
		$my_asid = $asid;
	}

	public function record_location($data) {
		$data["deviceId"] = $data["pin"];
		$data["timeLine"] = TIME_NOW;
		$data["fuel"] = 0;
		$data["antenna"] = 1;
		$data["direction"] = $data["heading"];
		$data["vehiclestate"] = 1;
		$data["otherstate"] = (double)$data["altitude"];
		$data['location'] = $data['lat'].' '.$data['long'];
		$options['geoLocation'] = array('location');
		unset($data["pin"]);
		unset($data['lat']);
		unset($data['long']);
		unset($data["altitude"]);
		unset($data["heading"]);

		global $db;
		$query = 'SELECT asid FROM '.Tprefix.'assets_trackingdevices WHERE deviceId='.$data["deviceId"].' AND fromDate<'.$data['timeLine'].' AND toDate>'.$data['timeLine'].' ORDER BY fromDate DESC';
		$query = $db->query($query);
		if($db->num_rows($query) > 0) {
			if($row = $db->fetch_assoc($query)) {
				$data["asid"] = $row['asid'];
			}
		}
		$db->insert_query('assets_locations', $data, $options);
	}

	public function assign_assetuser($userdata, $options = array()) {
		global $db, $core;
		if(is_empty($userdata['uid'], $userdata['fromDate'], $userdata['toDate'], $userdata['fromTime'], $userdata['toTime'])) {
			$this->errorcode = 1;
			return false;
		}

		$userdata['fromDate'] = strtotime($userdata['fromDate'].' '.$userdata['fromTime']);
		$userdata['toDate'] = strtotime($userdata['toDate'].' '.$userdata['toTime']);

		if($userdata['toDate'] < $userdata['fromDate']) {
			$this->errorcode = 5;
			return false;
		}
		if(value_exists('assets_users', 'asid', $userdata['asid'], '(('.$userdata['fromDate'].' BETWEEN fromDate AND toDate) OR ('.$userdata['toDate'].' BETWEEN fromDate AND toDate))')) {
			$this->errorcode = 2;
			return false;
		}
		if(is_array($userdata)) {
			$userassets_data = array('uid' => $userdata['uid'],
					'asid' => $userdata['asid'],
					'fromDate' => $userdata['fromDate'],
					'toDate' => $userdata['toDate'],
					'conditionOnHandover' => $userdata['conditionOnHandover'],
					'conditionOnReturn' => $userdata['conditionOnReturn'],
					'assignedon' => TIME_NOW,
					'assignedby' => $core->user['uid']
			);
		}
		$query = $db->insert_query('assets_users', $userassets_data);
		if($query) {
			$this->errorcode = 0;
		}
	}

	public function isValidDate($dateTime) {
		global $core;
		if(date($core->settings['dateformat'], strtotime($dateTime)) == $dateTime) {
			return true;
		}
		else {
			return false;
		}
	}

	public function update_assetuser($userdata) {
		global $db, $core;
		$auid = intval($userdata['auid']);
		if(is_empty($userdata['uid'], $userdata['fromDate'], $userdata['toDate'], $userdata['fromTime'], $userdata['toTime'])) {
			$this->errorcode = 1;
			return false;
		}
		if(is_array($userdata)) {
			$userassets_data = array('uid' => $userdata['uid'],
					'asid' => $userdata['asid'],
					'fromDate' => strtotime($userdata['fromDate'].' '.$userdata['fromTime']),
					'toDate' => strtotime($userdata['toDate'].' '.$userdata['toTime']),
					'conditionOnHandover' => $userdata['conditionOnHandover'],
					'conditionOnReturn' => $userdata['conditionOnReturn'],
					'editedon' => TIME_NOW,
					'editedby' => $core->user['uid']
			);
		}
		$db->update_query('assets_users', $userassets_data, 'auid='.$auid.'');
	}

	public function delete_userassets($id = '') {
		global $db;
		if(!empty($id)) {
			$db->delete_query('assets_users', 'auid='.$db->escape_string($id));
			$this->errorcode = 3;
		}
	}

	public function deactivate_asset($id = '') {
		global $db;
		if(!empty($this->assets['asid'])) {
			$db->update_query('assets', array('isActive' => 0), 'asid='.$db->escape_string($this->assets['asid']));
			$this->errorcode = 3;
		}
	}

	public function assign_tracker_to_asset($devid, $asid, $from, $to) {
		global $db;
		$db->insert_query('assets_users', array('deviceId' => $devid, 'asid' => $asid, 'fromDate' => $from, 'toDate' => $to));
	}

	/* Setter Functions ----END */

	/* Getter Functions ----START */
	public function get_asid() {
		return $my_asid;
	}

	public function get_allassignee() {
		global $db;
		$assigne_query = $db->query("SELECT asu.* FROM ".Tprefix."assets_users asu JOIN ".Tprefix."assets a ON(a.asid=asu.asid) WHERE a.isActive='1'");
		while($assignee = $db->fetch_assoc($assigne_query)) {
			$assignees[$assignee['auid']] = $assignee;
		}
		return $assignees;
	}

	public function get_assigneduser($id) {
		global $db, $core;
		if(!empty($id)) {
			$assignee = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."assets_users WHERE auid=".$db->escape_string($id)));
			$assignee['fromDate_output'] = date($core->settings['dateformat'], $assignee['fromDate']);
			$assignee['fromTime_output'] = preg_replace('[AM]', '', date($core->settings['timeformat'], $assignee['fromDate']));
			$assignee['toDate_output'] = date($core->settings['dateformat'], $assignee['toDate']);
			$assignee['toTime_output'] = preg_replace('[AM]', '', date($core->settings['timeformat'], $assignee['toDate']));
		}
		return $assignee;
	}

	public function get_asset_data($from = null, $to = null, $asset_id = null) {
		global $db;
		if(!isset($asset_id)) {
			$asset_id = $my_asid;
		}
		$query = 'SELECT alid,asid,X(location) as latitude, Y(location) as longitude,timeLine,deviceId,speed,direction,antenna,fuel,vehiclestate,otherstate FROM '.Tprefix.'assets_locations WHERE asid='.$asset_id;
		if(isset($from)) {
			$query .= ' AND timeLine>'.$from;
		}
		if(isset($to)) {
			$query .= ' AND timeLine<'.$to;
		}
		if(isset($cache[$query])) {
			return $cache[$query];
		}
		else {
			$query = $db->query($query);
			if($db->num_rows($query) > 0) {
				while($row = $db->fetch_assoc($query)) {
					$loc[$row['alid']] = $row;
				}
				$cache[$query] = $loc;
			}
		}
		return $loc;
	}

	public function get_assets_data($asset_ids, $from = null, $to = null) {
		global $db;
		$query = 'SELECT alid,asid,X(location) as latitude, Y(location) as longitude,timeLine,deviceId,speed,direction,antenna,fuel,vehiclestate,otherstate FROM '.Tprefix.'assets_locations WHERE asid IN ('.implode(',', $asset_ids).')';
		if(isset($from)) {
			$query .= ' AND timeLine>'.$from;
		}
		if(isset($to)) {
			$query .= ' AND timeLine<'.$to;
		}
		if(isset($cache[$query])) {
			return $cache[$query];
		}
		else {
			$queryobj = $db->query($query);
			if($db->num_rows($queryobj) > 0) {
				while($row = $db->fetch_assoc($queryobj)) {
					$loc[$row['asid']][$row['alid']] = $row;
				}
				$cache[$query] = $loc;
			}
		}
		return $loc;
	}

	public function get_data_for_users($users_ids, $from, $to) {
		global $db;
		$query = 'SELECT * FROM '.Tprefix.'assets_users WHERE uid IN ('.implode($asset_id, ',').')';
		if(isset($from) AND isset($to)) {
			$query .= ' AND (toDate>'.$from.' AND fromDate<'.$to.')';
		}
		$query = $db->query($query);
		if($db->num_rows($query) > 0) {
			while($row = $db->fetch_assoc($query)) {
				if($row['toDate'] > $to) {
					$tmpTo = $row['toDate'];
				}
				else {
					$tmpTo = $to;
				}
				if($row['fromDate'] > $from) {
					$tmpFrom = $row['fromDate'];
				}
				else {
					$tmpFrom = $from;
				}
				$tgt[$row['asid']][] = array("from" => $tmpFrom, "to" => $tmpTo);
			}
		}
		foreach($tgt as $asid => $ranges) {
			$subquery = 'SELECT alid,asid,X(location) as latitude, Y(location) as longitude,timeLine,deviceId,speed,direction,antenna,fuel,vehiclestate,otherstate FROM '.Tprefix.'assets_locations WHERE asid IN ('.implode($asset_id, ',').') AND (';
			$postquery = '';
			foreach($ranges as $key => $range) {
				$postquery.='(timeLine>'.$range["from"].' AND timeLine<'.$range["to"].') OR';
			}
			$subquery.= substr($postquery, 0, strlen($postquery) - 3).')';
			$subquery = $db->query($subquery);
			if($db->num_rows($subquery) > 0) {
				while($row = $db->fetch_assoc($subquery)) {
					$loc[$row['asid']][$row['alid']] = $row;
				}
			}
		}
		return $loc;
	}

	public function get_map($data) {
		global $db;

		foreach($data as $key => $trackedasset) {
			foreach($trackedasset as $key2 => $value) {
				$markers[] = array('title' => $value['latitude'].'|'.$value['longitude'].' ->'.$key.':'.Maps::get_streetname($value['latitude'], $value['longitude']), 'otherinfo' => 'some other info', 'geoLocation' => (number_format($value['latitude'], 6).','.number_format($value['longitude'], 6)));
			}
		}

		$options = array('overlaytype' => 'parsePolylines');
		$map = new Maps($markers, array('infowindow' => 1, 'mapcenter' => '32.887078, 34.195312'), $options);
		$map_view = $map->get_map(400, 300);
		return $map_view.'<hr><pre >'.$map->get_streetname($lat, $long).'</pre>';
	}

	public function delete_asset() {
		global $db, $core;
		if($core->usergroup['assets_canDeleteAsset'] == 1) {
			$db->delete_query('assets', 'asid='.$db->escape_string($this->assets['asid']));
			$this->errorcode = 4;
		}
	}

	private function read($id, $simple = false) {
		global $db;

		$query_select = '*';
		if($simple == true) {
			$query_select = 'asid, title, type';
		}
		if(!empty($id)) {
			$query_where = 'WHERE asid='.$db->escape_string($id);
		}
		return $db->fetch_assoc($db->query("SELECT {$query_select} FROM ".Tprefix."assets {$query_where}"));
	}

	public function get_assignto() {
		global $db, $core;
		$query = $db->query("SELECT u.uid, u.displayName FROM ".Tprefix."users u 
						JOIN ".Tprefix."affiliatedemployees ae ON (u.uid=ae.uid) WHERE (ae.isMain=1 AND ae.affid='{$core->user[mainaffiliate]}' OR u.reportsTo='{$core->user[uid]}')
						AND u.gid!=7 AND u.uid!={$core->user[uid]} ORDER BY displayName ASC");
		while($user = $db->fetch_assoc($query)) {
			$employees[$user['uid']] = $user['displayName'];
		}
		return $employees;
	}

	public function get_affiliateassets($option = '') {
		global $db, $core;
		$allassets = $db->query("SELECT a.*,ast.title AS type,ast.name FROM ".Tprefix."assets a JOIN ".Tprefix."assets_types ast ON (a.type=ast.astid) 
								WHERE a.affid in(".implode(',', $core->user['affiliates']).") Order BY isActive DESC");
		while($assets = $db->fetch_assoc($allassets)) {
			if($option == 'titleonly') {
				$asset[$assets['asid']] = $assets['title'];
			}
			else {
				$asset[$assets['asid']] = $assets;
			}
		}
		return $asset;
	}

	public function get() {
		return $this->assets;
	}

	public function get_trackingdevices($id) {
		global $db, $core;
		if(!empty($id)) {
			$tracker_devices = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."assets_trackingdevices WHERE atdid=".$db->escape_string($id)));
			$tracker_devices['fromDate_output'] = date($core->settings['dateformat'], $tracker_devices['fromDate']);
			$tracker_devices['toDate_output'] = date($core->settings['dateformat'], $tracker_devices['toDate']);
		}
		return $tracker_devices;
	}

	public function get_errorcode() {
		return $this->errorcode;
	}

	/* Getter Functions ----END */
}
?>