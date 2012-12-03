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

class Asset {
	private $cache = array();
	private $my_asid;

	public function __construct() {

	}

	public function set_asid($asid) {
		$my_asid = $asid;
	}

	public function get_asid() {
		return $my_asid;
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

	public function get_data_for_assets($asset_ids, $from = null, $to = null) {
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

	public function record_location($data) {
		$data["deviceId"] = $data["pin"];
		$data["timeLine"] = TIME_NOW;
		$data["fuel"] = 0;
		$data["antenna"] = 1;
		$data["direction"] = $data["heading"];
		$data["vehiclestate"] = 1;
		$data["otherstate"] = (double)$data["altitude"];
		$data['location'] = $data['lat'].' '.$data['long'];
		$options['geoLocation']=array('location');
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
		$db->insert_query('assets_locations', $data,$options);
	}

	public function get_map($data) { // ($uids,$asids,$from,$to) {
		global $db;

		foreach($data as $key => $trackedasset) {
			foreach($trackedasset as $key2 => $value) {
				$markers[] = array('title' => $value['latitude'].'|'.$value['longitude'].' ->'. $key.':'.Maps::get_streetname($value['latitude'], $value['longitude']), 'otherinfo' => 'some other info', 'geoLocation' => (number_format($value['latitude'], 6).','.number_format($value['longitude'], 6)));
			}
		}

		$options=array('overlaytype'=>'parsePolylines');
		$map = new Maps($markers, array('infowindow' => 1, 'mapcenter' => '32.887078, 34.195312'),$options);
		$map_view = $map->get_map(300, 200);
		return $map_view.'<hr><pre>'.$map->get_streetname($lat, $long).'</pre>';
	}

	public function assign_assetuser($asid, $uid, $from, $to) {
		global $db;
		$db->insert_query('assets_users', array('uid' => $uid, 'asid' => $asid, 'fromDate' => $from, 'toDate' => $to));
	}

	public function assign_tracker_to_asset($devid, $asid, $from, $to) {
		global $db;
		$db->insert_query('assets_users', array('deviceId' => $devid, 'asid' => $asid, 'fromDate' => $from, 'toDate' => $to));
	}

}
?>