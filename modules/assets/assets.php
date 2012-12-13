<?php
/*
 * Copyright © 2012 Orkila International Offshore, All Rights Reserved
 *
 * [Provide Short Descption Here]
 * $id: assets.php
 * Created:        @alain.paulikevitch    Nov 26, 2012 | 10:21:56 AM
 * Last Update:    @alain.paulikevitch    Nov 26, 2012 | 10:21:56 AM
 */

if(!defined("DIRECT_ACCESS")) {
	die('Direct initialization of this file is not allowed.');
}

if($core->usergroup['assets_canTrack'] == 0) {
	error($lang->sectionnopermission);
	exit;
}

// init
if(!isset($core->input["action"])) {
	$core->input["action"] = "map";
}

$assetslist = Asset::getAllAssets();
$resolve = array('asid' => array('table' => 'assets', 'id' => 'asid', 'name' => 'title'));
if(isset($core->input['asid'])) {
	if (is_array($core->input['asid'])) {
		$selected_options = $core->input['asid'];
	} else {
		$selected_options = array($core->input['asid']);
	}

}
else {
	$selectedasset = get_first($assetslist);
	$selected_options = array($selectedasset['key']);
}
$selectedasset = get_first($selected_options);

if (!isset($core->input['hourTo'])) {
	$core->input['hourTo']=0;
}
if (!isset($core->input['hourFrom'])) {
	$core->input['hourFrom']=0;
}


if (isset($core->input['fromDate']) && $core->input['fromDate']!="") {
	$from = strtotime($core->input['fromDate'].' '.str_pad($core->input['hourFrom'], 2, '0', STR_PAD_LEFT).':'.str_pad($core->input['minuteFrom'], 2, '0', STR_PAD_LEFT));
} else {
	unset($from);
}
if (isset($core->input['toDate']) && $core->input['toDate']!="") {
	$to = strtotime($core->input['toDate'].' '.str_pad($core->input['hourTo'], 2, '0', STR_PAD_LEFT).':'.str_pad($core->input['minuteTo'], 2, '0', STR_PAD_LEFT));
} else {
	$to = strtotime(date('Y-m-d',TIME_NOW).' '.str_pad($core->input['hourTo'], 2, '0', STR_PAD_LEFT).':'.str_pad($core->input['minuteTo'], 2, '0', STR_PAD_LEFT));
}



//filter
$pagecontents = '<form name="assets_filter" enctype="multipart/form-data" method="post" action="'.DOMAIN.'/index.php?module=assets/assets&action='.$core->input['action'].'">
				 <table cellspacing=5 cellpadding=5 border=0>
				 '.(($core->input['action'] == 'map') ?
				 '<tr><td valign=top align=right>'.$lang->asid.'</td><td colspan=3>'.parse_selectlist('asid[]', 1, $assetslist, $selected_options, 1,null,array('id' => 'asid')).'</td></tr>'
				 :'<tr><td valign=top align=right>'.$lang->asid.'</td><td colspan=3>'.parse_selectlist('asid', 1, $assetslist, $selected_options, 0, 'this.form.submit()',array('id' => 'asid')).'&nbsp;&nbsp;<a href="'.DOMAIN.'/index.php?module=assets/assets&action=map&asid='.$selectedasset['val'].'">Show on map</a></td></tr>' ).'<tr><td valign = top align = right></td><td>'.$lang->date.'</td><td>'.$lang->hours.'</td><td>'.$lang->minutes.'</td></tr>
				 <tr><td valign = top align = right>'.$lang->from.'</td><td><input type = "text" name = "fromDate" id = "pickDateFrom" tabindex = "2" value="'.$core->input['fromDate'].'"/></td><td>'.parse_selectlist('hourFrom', 3, range(0, 23), array($core->input['hourFrom'])).'</td><td>'.parse_selectlist('minuteFrom', 4, range(0, 59), array($core->input['minuteFrom'])).'</td></tr>
				 <tr><td valign = top align = right>'.$lang->to.'</td><td><input type = "text" name = "toDate" id = "pickDateTo" tabindex = "5"  value="'.$core->input['toDate'].'"/></td><td>'.parse_selectlist('hourTo', 6, range(0, 23), array($core->input['hourTo'])).'</td><td>'.parse_selectlist('minuteTo', 7, range(0, 59), array($core->input['minuteTo'])).'</td></tr>
				 <tr><td colspan = 4><input type = "submit" name = "filter_assets" value = "Filter" tabindex = "3"/></tr></tr></table></form>';
//echo '<pre>'.print_r($core->input,true)."\n\n\n".print_r(array('from'=>date('Y-m-d h:i',$from),'to'=>date('Y-m-d h:i',$to),'fromstamp'=>$from,'tostamp'=>$to),true).'</pre>';
if($core->input['action'] == 'map') {
	$core->input['asid'];
	$core->input['fromDate'];
	$core->input['toDate'];
	$pagetitle = $lang->assetstrackmap;
	$asset = new Asset();
	$data = $asset->get_data_for_assets($selected_options,$from,$to);
	$pagecontents .= $asset->get_map($data, array('infowindow' => 1, 'zoom' => 15,'mapcenter' => '33.89, 35.51'));
}
elseif($core->input['action'] == 'list') {
	$asset = new Asset($selectedasset['val']);
	$data = $asset->get_data_for_assets(array($selectedasset['val']), $from, $to, 5);
	$pagetitle = $lang->assetstrackpage;
	$pagecontents .= '<table cellspacing = 0 cellpadding = 5 border = 1>';
	if(isset($data)) {
		$pagecontents .= '<tr>';
		foreach($data as $key => $trackedasset) {
			foreach($trackedasset as $key => $value) {
				foreach($value as $columnkey => $columnvalue) {
					$pagecontents .= '<th>'.$lang->{$columnkey}.'</th>';
				}
				break;
			}
			break;
		}
		$pagecontents .= '</tr>';
		foreach($data as $key => $trackedasset) {
			foreach($trackedasset as $key => $value) {
				if(!isset($value['displayName'])) {
					$value['displayName'] = Maps::get_streetname($value['latitude'], $value['longitude']);
					Asset::update_location($value['alid'], $value);
				}
				$pagecontents .='<tr>';
				foreach($value as $columnkey => $columnvalue) {
					if($columnkey == 'timeLine') {
						$pagecontents .= '<td>'.date('Y/m/d G:i', $columnvalue).'</td>';
					}
					elseif($columnkey == 'asid') {
						$pagecontents .= '<td>'.get_name_from_id($columnvalue, $resolve['asid']['table'], $resolve['asid']['id'], $resolve['asid']['name']).'</td>';
					}
					else {
						$pagecontents .= '<td>'.$columnvalue.'</td>';
					}
				}
				$pagecontents .='</tr>';
			}
		}
	}
	$pagecontents .= '</table>';
}
eval("\$assetslist = \"".$template->get('assets_assets')."\";");
echo $assetslist;

function get_first($foo) {
	if(is_array($foo)) {
		foreach($foo as $k => $v) {
			return array('key' => $k, 'val' => $v);
		}
	}
}

function turn_to_keys($foo) {
	$new=array();
	foreach ($foo as $key=>$value) {
		$new[$key]=$key;
	}
	return $new;
}
?>
