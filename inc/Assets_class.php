<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Assets Class
 * $id: Assets_class.php
 * Created:		@Alain.Paulikevitch		May 10, 2012 | 06:03 PM
 * Last Update: @tony.assaad	        June 25, 2013 | 04:32 PM
 */

class Assets {
	private $cache = array();
	private $my_asid;
	private $asset = array();

	public function __construct($id = '', $simple = false) {
		if(!empty($id)) {
			$this->asset = $this->read($id, $simple);
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
			$this->asset = $data;
		}

		/* Santize inputs - START */
		$sanitize_fields = array('title', 'affid', 'type', 'description');
		foreach($sanitize_fields as $val) {
			$this->asset[$val] = $core->sanitize_inputs($this->asset[$val], array('removetags' => true));
		}

		/* If action is edit, don't check if supplier already exists */
		if($options['operationtype'] != 'update') {
			if(value_exists('assets', 'title', $this->asset['title'])) {
				$this->errorcode = 2;
				return false;
			}
		}

		if($options['operationtype'] == 'update') {
			if(is_empty($data['title'], $data['tag'], $data['status'])) {
				$this->errorcode = 1;
				return false;
			}
			$this->asset = $data;
			$this->asset['editedOn'] = TIME_NOW;
			$this->asset['editedBy'] = $core->user['uid'];
			$assetid = intval($this->asset['asid']);
			unset($this->asset['asid']);
			$query = $db->update_query('assets', $this->asset, 'asid='.$assetid.'');
		}
		else {
			$this->asset['isActive'] = 1;
			$this->asset['createdOn'] = TIME_NOW;
			$this->asset['createdBy'] = $core->user['uid'];
			$query = $db->insert_query('assets', $this->asset);
			$this->asset['asid'] = $db->last_id();
		}

		if($query) {
			$this->errorcode = 0;
		}
	}

	public function manage_trackers($trackersdata, array $options = array()) {
		global $db, $log, $core, $errorhandler, $lang;
		if(is_empty($trackersdata['IMEI'])) {
			$this->errorcode = 1;
			return false;
		}
		if($options['operationtype'] != 'update') {
			if(value_exists('assets_trackers', 'deviceId', $trackersdata['deviceId'])) {
				$this->errorcode = 2;
				return false;
			}
		}
		unset($trackersdata['mobileintcode'], $trackersdata['mobileareacode']);
		if(is_array($trackersdata)) {
			$this->tracker = $trackersdata;
		}
		/* Santize inputs - START */
		$sanitize_fields = array('IMEI', 'PUK', 'PIN', 'Phonenumber');
		foreach($sanitize_fields as $val) {
			$this->tracker[$val] = $core->sanitize_inputs($this->tracker[$val], array('removetags' => true));
		}
		if($options['operationtype'] == 'update') {
			//$this->tracker = $trackersdata;
			$this->tracker['editedOn'] = TIME_NOW;
			$this->tracker['editedBy'] = $core->user['uid'];
			$trackerid = intval($this->tracker['trackerid']);
			unset($this->tracker['trackerid']);
			$query = $db->update_query('assets_trackers', $this->tracker, 'trackerid='.$trackerid.'');
		}
		else {
			$this->tracker['createdOn'] = TIME_NOW;
			$this->tracker['createdBy'] = $core->user['uid'];
			$this->tracker['password'] = base64_encode($this->tracker['password']);
			$query = $db->insert_query('assets_trackers', $this->tracker);
		}

		if($query) {
			$this->errorcode = 0;
		}
	}

	public function set_asid($asid) {
		$my_asid = $asid;
	}

	public function record_location($data) {
		global $db;
		$data['timeLine'] = strtotime($data['timeLine']);
		$options['geoLocation'] = array('location');

		$data['location'] = $data['lat'].' '.$data['long'];
		unset($data['lat'], $data['long']);
		$query = 'SELECT asid 
				  FROM '.Tprefix.'assets_trackingdevices astd
				  JOIN assets_trackers ast  ON (ast.trackerid=astd.trackerid)
				  WHERE ast.deviceId='.$data['deviceId'].' AND astd.fromDate<'.$data['timeLine'].' AND astd.toDate>'.$data['timeLine'].'
				  ORDER BY fromDate DESC ';
		$query = $db->query($query);
		if($db->num_rows($query) > 0) {
			$data['asid'] = $db->fetch_field($query, 'asid');
		}

		$map = new Maps();
		$data['parsedLocation'] = Maps::reverse_geocoding($value['latitude'], $value['longitude']); /* record location as json coming directly from google */
		$query_insert = $db->insert_query('assets_locations', $data, $options);
		if($query_insert) {
			return true;
		}
		return false;
	}

	public function assign_assetuser($userdata, $options = array()) {
		global $db, $core;
		if(is_empty($userdata['uid'], $userdata['fromDate'], $userdata['toDate'], $userdata['fromTime'], $userdata['toTime'])) {
			$this->errorcode = 1;
			return false;
		}

		if(!$this->isValidDate($userdata['fromDate'])) {
			$this->errorcode = 6;
			return false;
		}
		if(!$this->isValidDate($userdata['toDate'])) {
			$this->errorcode = 6;
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
					'asid' => $this->asset['asid'],
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

	public function isValidDate($date) {
		global $core;
		$datetime = explode('-', $date);
		return checkdate($datetime[1], $datetime[0], $datetime[2]);
	}

	public function update_assetuser($userdata) {
		global $db, $core;
		$auid = intval($userdata['auid']);
		if(isset($userdata['uid'], $userdata['fromDate'], $userdata['toDate'], $userdata['fromTime'], $userdata['toTime'])) {
			if(is_empty($userdata['buid'], $userdata['fromDate'], $userdata['toDate'], $userdata['fromTime'], $userdata['toTime'])) {
				$this->errorcode = 1;
				return false;
			}
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
		if(!empty($this->asset['asid'])) {
			$db->update_query('assets', array('isActive' => 0), 'asid='.$db->escape_string($this->asset['asid']));
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

	public function get_allassignee($filter_where) {
		global $db, $core;
		if(!empty($filter_where) && isset($filter_where)) {
			$filter_where = ' AND '.$filter_where;
		}
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

		$assigne_query = $db->query("SELECT asu.* FROM ".Tprefix."assets_users asu 
									JOIN ".Tprefix."assets a ON(a.asid=asu.asid) WHERE a.isActive='1'
									{$filter_where}	
									{$sort_query}
									LIMIT {$limit_start},{$core->settings[itemsperlist]}");

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

	public function get_asset_data($from = null, $to = null, $asset_id = null, $options = '') {
		global $db;
		if(!isset($asset_id)) {
			$asset_id = $my_asid;
		}
		if($options == 'topnew') {
			$latestrecord_where = ' WHERE timeLine > '.strtotime('-24 HOURS').'';
		}
		if(!empty($from) && !empty($to)) {
			$from_to = ' WHERE (timeLine BETWEEN '.$from.' AND '.$to.') ';
		}

		$query = 'SELECT alid,asid,parsedLocation,X(location) as latitude, Y(location) as longitude,timeLine,deviceId,speed,direction,antenna,fuel,vehiclestate,otherstate 
				 FROM '.Tprefix.'assets_locations '.$from_to.' '.$latestrecord_where.' AND asid='.$asset_id.' LIMIT 0,10';
		$query = $db->query($query);
		if($db->num_rows($query) > 0) {
			while($row = $db->fetch_assoc($query)) {
				$loc[$row['alid']] = $row;
			}
		}

		return $loc;
	}

	public function get_assets_data($id = '', $from = null, $to = null) {
		global $db,$core;
		$query = 'SELECT asl.alid,asl.asid,asl.parsedLocation,X(asl.location) as latitude, Y(asl.location) as longitude,asl.timeLine,asl.deviceId,asl.speed,asl.direction,asl.antenna,asl.fuel,asl.vehiclestate,asl.otherstate 
				  FROM '.Tprefix.'assets_locations asl JOIN '.Tprefix.'assets ast ON(ast.asid=asl.asid) WHERE ast.isActive=1   and ast.affid IN('.$db->escape_string(implode(',', $core->user['affiliates'])).')  ORDER BY asl.alid DESC';
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
					$asset_location[$row['alid']] = $row;
				}
				$cache[$query] = $asset_location;
			}
		}
		return $asset_location;
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
		if(is_array($data)) {
			foreach($data as $key => $trackedasset) {
				$markers[$trackedasset['asid']][$trackedasset['alid']] = array('title' => Maps::get_streetname($trackedasset['latitude'], $trackedasset['longitude']), 'otherinfo' => 'some other info', 'geoLocation' => $trackedasset['longitude'].','.$trackedasset['latitude']);
			}
			$map = new Maps($markers, array('infowindow' => 1, 'mapcenter' => '32.887078, 34.195312', 'overlaytype' => 'parsePolylines'));
			$map_view = $map->get_map(400, 300);
			return $map_view.'<hr>';
		}
	}

	public function delete_asset() {
		global $db, $core;
		if($core->usergroup['assets_canDeleteAsset'] == 1) {
			$db->delete_query('assets', 'asid='.$db->escape_string($this->asset['asid']));
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
						JOIN ".Tprefix."affiliatedemployees ae ON (u.uid=ae.uid)
						WHERE (ae.isMain=1 AND ae.affid='{$core->user[mainaffiliate]}' OR u.reportsTo='{$core->user[uid]}')
						AND u.gid!=7 AND u.uid!={$core->user[uid]} ORDER BY displayName ASC");
		while($user = $db->fetch_assoc($query)) {
			$employees[$user['uid']] = $user['displayName'];
		}
		return $employees;
	}

	public function get_affiliateassets($option = '', $filter_where = '') {
		global $db, $core;

		if(!empty($filter_where) && isset($filter_where)) {
			$filter_where = ' AND '.$filter_where;
		}
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
		/* Get asset for the affiliates that are for the user affilliates */
		$allassets = $db->query("SELECT a.*, ast.title AS type, ast.name 
								FROM ".Tprefix."assets a		
								JOIN ".Tprefix."assets_types ast ON (a.type=ast.astid) 
								WHERE a.affid IN (".$db->escape_string(implode(',', $core->user['affiliates'])).")
								{$filter_where} 
								{$sort_query}
								LIMIT {$limit_start}, {$core->settings[itemsperlist]}");
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
		return $this->asset;
	}

	public function get_tracker($id) {
		global $db;
		if(!empty($id)) {
			$this->tracker = $db->fetch_assoc($db->query("SELECT * FROM ".Tprefix."assets_trackers WHERE trackerid=".$db->escape_string($id)));
		}
		return $this->tracker;
	}

	public function get_trackers($filter_where) {
		global $db, $core;

		if(!empty($filter_where) && isset($filter_where)) {
			$filter_where = ' WHERE ast.'.$filter_where;
		}
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


		$alltrackers = $db->query("SELECT ast.*,astd.asid,a.title FROM ".Tprefix."asssets_trackers ast	
								  JOIN ".Tprefix."assets_trackingdevices astd ON (astd.trackerid=ast.trackerid)
								  JOIN ".Tprefix."assets a ON (a.asid=astd.asid) 
								{$filter_where} 
								{$sort_query}
								LIMIT {$limit_start}, {$core->settings[itemsperlist]}");
		while($trackers = $db->fetch_assoc($alltrackers)) {
			$tracker[$trackers['trackerid']] = $trackers;
		}


		return $tracker;
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

}
?>