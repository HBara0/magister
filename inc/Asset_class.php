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
		$query = 'SELECT * FROM '.Tprefix.'assets_locations WHERE asid='.$asset_id;
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
	}

	public function get_data_for_assets($asset_ids, $from = null, $to = null) {
		global $db;
		$query = 'SELECT * FROM '.Tprefix.'assets_locations WHERE asid IN ('.implode($asset_id, ',').')';
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
					$loc[$row['asid']][$row['alid']] = $row;
				}
				$cache[$query] = $loc;
			}
		}
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
			$subquery = 'SELECT * FROM '.Tprefix.'assets_locations WHERE asid IN ('.implode($asset_id, ',').') AND (';
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
		global $db;
		$query = 'SELECT asid FROM '.Tprefix.'assets_trackingdevices WHERE deviceId='.$data["devId"].' AND fromDate<'.$data['timeLine'].' AND toDate>'.$data['timeLine'].' ORDER BY fromDate DSC';
		$query = $db->query($query);
		if($db->num_rows($query) > 0) {
			if($row = $db->fetch_assoc($query)) {
				$data["asid"] = $row['asid'];
			}
		}
		$db->insert_query('assets_locations', $data);
	}

	public function get_map() { // ($uids,$asids,$from,$to) {
		global $db;
		$lat=(float)33.869797;
		$long=(float)35.522593;
		$markers[]=array('title'=>'random1','otherinfo'=>'some other info','geoLocation'=>(number_format($lat,6).','.number_format($long,6)));
		$map = new Maps($markers, array('infowindow' => 1, 'mapcenter' => '32.887078, 34.195312'));
		$map_view = $map->get_map(300, 200);
		return $map_view.'<hr><pre>'.$map->get_streetname($lat,$long).'</pre>';
	}

	public function assign_assetuser($asid,$uid,$from,$to) {
		global $db;
		$db->insert_query('assets_users', array('uid'=>$uid,'asid'=>$asid,'fromDate'=>$from,'toDate'=>$to));
	}
	public function assign_tracker_to_asset($devid,$asid,$from,$to) {
		global $db;
		$db->insert_query('assets_users', array('deviceId'=>$devid,'asid'=>$asid,'fromDate'=>$from,'toDate'=>$to));
	}
}
?>