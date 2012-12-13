<?php
/*
 * Orkila Central Online System (OCOS)
 * Copyright © 2009 Orkila International Offshore, All Rights Reserved
 *
 * Maps Class
 * $id: Maps_class.php
 * Created:		@Alain.Paulikevitch		May 10, 2012 | 06:03 PM
 * Last Update: @Alain.Paulikevitch 	November  29, 2012 | 03:32 PM
 */

// ignore all users with gid 7 - done
// fix image button into input type image - done
// in asset management page add icon that opens the map with the asset
// list of assets in asset positions list
// put asset management functions into the class - done
// put text in html into lang (forms in all pages) - done
// calendar set of colors for polylines
// add class databatable to all tables - done by setting width:100%

class Asset {
	private $cache = array();
	private $my_asid;

	public function __construct($asset_id = null) {
		if(isset($asset_id)) {
			$this->my_asid = $asset_id;
		}
	}

	public function set_asid($asid) {
		$this->my_asid = $asid;
	}

	public function get_asid() {
		return $this->my_asid;
	}

	public function edit_asset($id, $asset) {
		global $db;
		$db->update_query('assets', $asset, 'asid='.$id);
	}

	public function add_asset($asset) {
		global $db;
		$db->insert_query('assets', $asset);
	}

	public function delete_asset($id) {
		global $db,$core;
		if($core->usergroup['assets_canDeleteAsset'] == 1) {
			echo 'deleted asset '.$id.'<br>';
			//$db->query('delete from '.Tprefix.'assets where asid='.$id);
		}
	}

	public function edit_tracker($id, $tracker) {
		global $db;
		$db->update_query('assets_trackingdevices', $tracker, 'atdid='.$id);
	}

	public function add_tracker($tracker) {
		global $db;
		$db->insert_query('assets_trackingdevices', $tracker);
	}

	public function delete_tracker($id) {
		global $db, $core;
		if($core->usergroup['assets_canDeleteTracker'] == 1) {
			$result = $db->query('select atdid,asid,fromDate,toDate from '.Tprefix.'assets_trackingdevices where atdid='.$id);
			if($db->num_rows($result) > 0) {
				if($row = $db->fetch_assoc($result)) {
					if (TIME_NOW>$row['toDate']) {
						echo 'deleted tracker '.$id.'<br>';
						//$db->query('delete from '.Tprefix.'assets_trackingdevices where atdid='.$id);
					}
				}
			}
		}
	}

	public function edit_assignedasset($id, $assigned) {
		global $db;
		$db->update_query('assets_users', $assigned, 'auid='.$id);
	}

	public function add_assignedasset($assigned) {
		global $db;
		$db->insert_query('assets_users', $assigned);
	}

	public function delete_assignedasset($id) {
		global $db,$core;
		if($core->usergroup['assets_canDeleteAssignement'] == 1) {
			echo 'deleted assignement '.$id.'<br>';
			//$db->query('delete from '.Tprefix.'assets_users where auid='.$id);
		}
	}

	public function getAllAssets() {
		global $db, $core;
		$result = array();
		$affiliates = array();
		foreach(getAffiliateList() as $key => $value) {
			$affiliates[] = $key;
		}
		$query = 'SELECT asid,title FROM '.Tprefix.'assets WHERE affid IN ('.implode(',', $affiliates).')';
		$query = $db->query($query);
		if($db->num_rows($query) > 0) {
			while($row = $db->fetch_assoc($query)) {
				$result[$row['asid']] = $row['title'];
			}
		}
		return $result;
	}

	public function get_asset_data($from = null, $to = null, $asset_id = null) {
		global $db;
		if(!isset($asset_id)) {
			$asset_id = $this->my_asid;
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

	public function get_data_for_assets($asset_ids, $from = null, $to = null, $limit = 'All') {
		global $db;
		if(is_array($asset_ids)) {
			$query = 'SELECT alid,asid,X(location) as latitude, Y(location) as longitude,timeLine,deviceId,speed,direction,antenna,fuel,vehiclestate,otherstate,displayName FROM '.Tprefix.'assets_locations WHERE asid IN ('.implode(',', $asset_ids).')';
		}
		else {
			return null;
		}


		if(isset($from)) {
			$query .= ' AND timeLine>'.$from;
		}
		if(isset($to)) {
			$query .= ' AND timeLine<'.$to;
		}
		if($limit != 'All') {
			$query .= ' LIMIT 0,'.$limit;
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

	public function get_data($limit = 'All') {
		global $db;
		if($count == 'All') {
			$query = 'SELECT alid,asid,X(location) as latitude, Y(location) as longitude,timeLine,deviceId,speed,direction,antenna,fuel,vehiclestate,otherstate,displayName FROM '.Tprefix.'assets_locations WHERE asid='.$this->my_asid.' ORDER BY timeLine DESC';
		}
		else {
			$query = 'SELECT alid,asid,X(location) as latitude, Y(location) as longitude,timeLine,deviceId,speed,direction,antenna,fuel,vehiclestate,otherstate,displayName FROM '.Tprefix.'assets_locations WHERE asid='.$this->my_asid.' ORDER BY timeLine DESC Limit 0,'.$count;
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

	public function get_data_for_users($users_ids, $from = 0, $to = TIME_NOW, $limit = 'All') {
		global $db;
		$tgt = array();
		$query = 'SELECT * FROM '.Tprefix.'assets_users WHERE uid IN ('.implode($users_ids, ',').')';
		if(isset($from) AND isset($to)) {
			$query .= ' AND (toDate>'.$from.' AND fromDate<'.$to.')';
		}
		if($limit != 'All') {
			$query .= ' LIMIT 0,'.$limit;
		}
		$query2 = $db->query($query);
		if($db->num_rows($query2) > 0) {
			while($row = $db->fetch_assoc($query2)) {
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
			$subquery = 'SELECT alid,asid,X(location) as latitude, Y(location) as longitude,timeLine,deviceId,speed,direction,antenna,fuel,vehiclestate,otherstate FROM '.Tprefix.'assets_locations WHERE asid = '.$asid.' AND (';
			$postquery = '';
			foreach($ranges as $key => $range) {
				$postquery.='(timeLine>'.$range["from"].' AND timeLine<'.$range["to"].') OR';
			}
			$subquery.= substr($postquery, 0, strlen($postquery) - 3).')';
			$subquery2 = $db->query($subquery);
			if($db->num_rows($subquery2) > 0) {
				while($row = $db->fetch_assoc($subquery2)) {
					$loc[$row['asid']][$row['alid']] = $row;
				}
			}
		}
		return $loc;
	}

	public function update_location($id, $data) {
		global $db;
		$data['location'] = $data['latitude'].' '.$data['longitude'];
		unset($data['latitude']);
		unset($data['longitude']);
		unset($data['alid']);

		$options['geoLocation'] = array('location');

		$db->update_query('assets_locations', $data, 'alid='.$id, $options);
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

	public function get_map($data, $options) { // ($uids,$asids,$from,$to) {
		global $db;
		if(isset($data)) {
			foreach($data as $key => $trackedasset) {
				foreach($trackedasset as $key2 => $value) {
					$markers[] = array('title' => $value['latitude'].'|'.$value['longitude'].' -> asset('.$key.')', 'otherinfo' => 'some other info', 'geoLocation' => (number_format($value['latitude'], 6).','.number_format($value['longitude'], 6)));
				}
			}
			$options['overlaytype'] = 'parsePolylines';
			//echo '<pre>'.print_r($markers).'</pre>';
			$map = new Maps($markers, $options);
			$map_view = $map->get_map(500, 400);
			return $map_view;
		}
		else {
			return "";
		}
	}

}
?>